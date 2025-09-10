<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerChat;
use App\Models\CustomerChatMessage;
use App\Models\PotentialCustomer;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerCommunicationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');
        $admin = auth('admin')->user();

        // الحصول على الموظفين الذين لديهم صلاحية customer_communication
        $employeesWithPermission = Employee::whereHas('roles.permissions', function ($query) {
            $query->where('name', 'customer_communication');
        })->get();

        // الحصول على العملاء المطلوب التواصل معهم
        $query = PotentialCustomer::where(function ($q) {
            $q->whereJsonContains('customer_classifications', 'requested_call')
                ->orWhereJsonContains('customer_classifications', 'requested_visit');
        });

        // تطبيق الفلاتر
        if ($filter === 'calls_pending') {
            $query = PotentialCustomer::whereJsonContains('customer_classifications', 'requested_call');
        } elseif ($filter === 'visits_pending') {
            $query = PotentialCustomer::whereJsonContains('customer_classifications', 'requested_visit');
        } elseif ($filter === 'high_priority') {
            $query->whereJsonContains('customer_classifications', 'difficult_customer');
        } elseif ($filter === 'unread') {
            $customerIds = CustomerChat::whereHas('messages', function ($msgQuery) {
                $msgQuery->where('sender_type', 'employee')
                    ->where('is_read', false);
            })->pluck('potential_customer_id')->toArray();
            $query->whereIn('id', $customerIds);
        } elseif ($filter === 'my_chats') {
            // إظهار الشاتات التي يديرها هذا الأدمن فقط
            $customerIds = CustomerChat::where('admin_id', $admin->id)
                ->pluck('potential_customer_id')
                ->toArray();
            $query->whereIn('id', $customerIds);
        }

        $customers = $query->with(['customerChat' => function ($q) use ($employeesWithPermission) {
            $q->whereIn('employee_id', $employeesWithPermission->pluck('id'))
                ->with(['employee', 'admin']);
        }])
            ->withCount(['customerChat as unread_count' => function ($q) {
                $q->whereHas('messages', function ($msgQuery) {
                    $msgQuery->where('sender_type', 'employee')
                        ->where('is_read', false);
                });
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // الإحصائيات
        $stats = $this->getAdminStats();

        return view('admin.customer_communication.index', compact('customers', 'filter', 'stats', 'employeesWithPermission'));
    }

    public function show($potentialCustomerId)
    {
        $admin = auth('admin')->user();

        // البحث عن العميل
        $customer = PotentialCustomer::findOrFail($potentialCustomerId);

        // البحث عن الشات الخاص بهذا العميل
        $chat = CustomerChat::where('potential_customer_id', $potentialCustomerId)
            ->with(['employee', 'admin'])
            ->first();

        // إذا لم يوجد شات، عرض صفحة اختيار الموظف
        if (!$chat) {
            // الحصول على الموظفين المؤهلين فقط
            $employeesWithPermission = Employee::whereHas('roles.permissions', function ($query) {
                $query->where('name', 'customer_communication');
            })->where('status', 'active')->get();

            if ($employeesWithPermission->isEmpty()) {
                return redirect()->route('admin.customer-communication.index')
                    ->with('error', 'لا يوجد موظفين مؤهلين للتواصل مع العملاء حالياً');
            }

            return view('admin.customer_communication.show', compact('customer', 'employeesWithPermission'));
        }

        // تحميل الرسائل بين الموظف والإدارة
        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // التأكد من أن المتغيرات محددة بشكل صحيح
        $messages = $messages ?? collect(); // تحويل لـ collection فارغة إذا كانت null

        // تحديد الرسائل كمقروءة للإدارة إذا كانت موجودة
        if ($messages->isNotEmpty()) {
            $chat->markAsRead('admin');
        }

        return view('admin.customer_communication.show', compact('customer', 'chat', 'messages'));
    }

    public function assignEmployee(Request $request, $potentialCustomerId)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id'
        ]);

        $admin = auth('admin')->user();
        $customer = PotentialCustomer::findOrFail($potentialCustomerId);

        // التأكد من أن الموظف لديه الصلاحية المطلوبة وأنه نشط
        $employee = Employee::whereHas('roles.permissions', function ($query) {
            $query->where('name', 'customer_communication');
        })->where('status', 'active')->findOrFail($request->employee_id);

        // البحث عن شات موجود أو إنشاء جديد
        $chat = CustomerChat::where('potential_customer_id', $potentialCustomerId)->first();

        if (!$chat) {
            // تحديد نوع العميل ومستوى الأولوية
            $classifications = $customer->customer_classifications ?? [];
            if (is_string($classifications)) {
                $classifications = json_decode($classifications, true) ?? [];
            }

            $customerType = 'general';
            $priority = 'normal';

            if (in_array('requested_call', $classifications)) {
                $customerType = 'call_request';
                $priority = 'medium';
            } elseif (in_array('requested_visit', $classifications)) {
                $customerType = 'visit_request';
                $priority = 'medium';
            }

            if (in_array('difficult_customer', $classifications)) {
                $priority = 'high';
            }

            // إنشاء شات جديد
            $chat = CustomerChat::create([
                'potential_customer_id' => $potentialCustomerId,
                'employee_id' => $request->employee_id,
                'admin_id' => $admin->id,
                'status' => 'active',
                'priority' => $priority,
                'customer_type' => $customerType,
                'last_message_at' => now()
            ]);

            // إرسال رسالة تعريفية تلقائية من الإدارة
            $introMessage = $this->generateIntroMessage($customer, $employee, $classifications);

            CustomerChatMessage::create([
                'chat_id' => $chat->id,
                'sender_type' => 'admin',
                'sender_id' => $admin->id,
                'message_type' => 'text',
                'content' => $introMessage
            ]);

            // تحديث وقت آخر رسالة
            $chat->update(['last_message_at' => now()]);
        } else {
            // تحديث الموظف المعين إذا كان الشات موجود
            $chat->update([
                'employee_id' => $request->employee_id,
                'admin_id' => $admin->id,
                'status' => 'active'
            ]);

            // إرسال رسالة إشعار بالتغيير
            CustomerChatMessage::create([
                'chat_id' => $chat->id,
                'sender_type' => 'admin',
                'sender_id' => $admin->id,
                'message_type' => 'text',
                'content' => "تم تعديل المسؤول عن هذا العميل إلى: {$employee->name}. يرجى متابعة التواصل معه."
            ]);

            $chat->update(['last_message_at' => now()]);
        }

        return redirect()->route('admin.customer-communication.show', $potentialCustomerId)
            ->with('success', 'تم تعيين الموظف ' . $employee->name . ' وإنشاء الشات بنجاح');
    }

    private function generateIntroMessage($customer, $employee, $classifications)
    {
        $customerType = '';
        $urgencyLevel = '';

        if (in_array('requested_call', $classifications)) {
            $customerType = 'يطلب مكالمة هاتفية';
        } elseif (in_array('requested_visit', $classifications)) {
            $customerType = 'يطلب زيارة للمكتب';
        }

        if (in_array('difficult_customer', $classifications)) {
            $urgencyLevel = ' - عميل يتطلب اهتمام خاص (عالي الأولوية)';
        }

        $message = "مرحباً {$employee->name}،\n\n";
        $message .= "تم تكليفك بالتواصل مع العميل: {$customer->customer_name}\n";
        $message .= "رقم الهاتف: {$customer->phone}\n";
        $message .= "وصف العمل: {$customer->work_description}\n\n";

        if ($customerType) {
            $message .= "نوع الطلب: {$customerType}\n";
        }

        if ($urgencyLevel) {
            $message .= "ملاحظة مهمة: {$urgencyLevel}\n";
        }

        $message .= "\nيرجى التواصل مع العميل في أقرب وقت ممكن وتحديثي بنتائج المحادثة.";

        return $message;
    }

    public function sendMessage(Request $request, $chatId)
    {
        $admin = auth('admin')->user();
        $chat = CustomerChat::findOrFail($chatId);

        $request->validate([
            'message_type' => 'required|in:text,file,voice',
            'content' => 'required_if:message_type,text',
            'file' => 'required_if:message_type,file|file|max:10240',
            'voice' => 'required_if:message_type,voice'
        ]);

        $message = null;

        try {
            DB::beginTransaction();

            if ($request->message_type === 'text') {
                $message = CustomerChatMessage::create([
                    'chat_id' => $chat->id,
                    'sender_type' => 'admin',
                    'sender_id' => $admin->id,
                    'message_type' => 'text',
                    'content' => $request->content
                ]);
            } elseif ($request->message_type === 'file') {
                $file = $request->file('file');
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('customer-chat/files', $fileName, 'public');

                $message = CustomerChatMessage::create([
                    'chat_id' => $chat->id,
                    'sender_type' => 'admin',
                    'sender_id' => $admin->id,
                    'message_type' => 'file',
                    'file_path' => 'files/' . $fileName,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType()
                ]);
            } elseif ($request->message_type === 'voice') {
                $voiceData = $request->voice;
                if (is_string($voiceData) && str_starts_with($voiceData, 'data:audio')) {
                    $audio = base64_decode(explode(',', $voiceData)[1]);
                    $fileName = time() . '_' . Str::random(10) . '.webm';

                    $filePath = 'customer-chat/voice/' . $fileName;
                    Storage::disk('public')->put($filePath, $audio);

                    if (!Storage::disk('public')->exists($filePath)) {
                        throw new \Exception('فشل في حفظ الملف الصوتي');
                    }

                    $duration = $request->duration;
                    $duration = is_numeric($duration) ? (int) $duration : 0;

                    if ($duration > 1000) {
                        $duration = round($duration / 1000);
                    }

                    if ($duration < 1) $duration = 1;
                    elseif ($duration > 600) $duration = 600;

                    $message = CustomerChatMessage::create([
                        'chat_id' => $chat->id,
                        'sender_type' => 'admin',
                        'sender_id' => $admin->id,
                        'message_type' => 'voice',
                        'file_path' => 'voice/' . $fileName,
                        'file_name' => $fileName,
                        'file_size' => strlen($audio),
                        'file_type' => 'audio/webm',
                        'duration' => $duration
                    ]);
                }
            }

            // تحديث آخر رسالة في الشات
            $chat->update(['last_message_at' => now()]);

            DB::commit();

            if ($message) {
                $message->load('sender');

                $responseData = [
                    'success' => true,
                    'message' => [
                        'id' => $message->id,
                        'content' => $message->content ?? '',
                        'message_type' => $message->message_type,
                        'file_url' => $message->file_url ?? '',
                        'file_name' => $message->file_name ?? '',
                        'file_size_formatted' => $message->file_size_formatted ?? '0 KB',
                        'sender_name' => $message->sender_name ?? 'مجهول',
                        'sender_type' => $message->sender_type,
                        'created_at' => $message->created_at ? $message->created_at->format('H:i') : 'الآن',
                        'created_at_full' => $message->created_at ? $message->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                        'is_read' => $message->is_read ?? false,
                        'file_path' => $message->file_path ?? '',
                        'file_type' => $message->file_type ?? '',
                        'file_size' => $message->file_size ?? 0,
                        'can_delete' => $message->created_at && $message->created_at->diffInMinutes(now()) <= 30
                    ]
                ];

                if ($message->message_type === 'voice') {
                    $voiceDuration = $message->voice_duration ?? ['total_seconds' => 0, 'formatted' => '0:00'];
                    $responseData['message']['duration'] = $voiceDuration['total_seconds'];
                    $responseData['message']['duration_formatted'] = $voiceDuration['formatted'];
                }

                return response()->json($responseData);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'error' => 'فشل في إرسال الرسالة: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => false, 'error' => 'فشل في إرسال الرسالة'], 500);
    }

    public function getNewMessages($chatId, Request $request)
    {
        $admin = auth('admin')->user();
        $chat = CustomerChat::findOrFail($chatId);

        $lastMessageId = $request->get('last_message_id', 0);

        $newMessages = $chat->messages()
            ->where('id', '>', $lastMessageId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // تحديد الرسائل من الموظف كمقروءة
        $chat->messages()
            ->where('sender_type', 'employee')
            ->where('is_read', false)
            ->where('id', '>', $lastMessageId)
            ->update(['is_read' => true, 'read_at' => now()]);

        $formattedMessages = $newMessages->map(function ($message) {
            $messageData = [
                'id' => $message->id,
                'content' => $message->content ?? '',
                'message_type' => $message->message_type,
                'file_url' => $message->file_url ?? '',
                'file_name' => $message->file_name ?? '',
                'file_size_formatted' => $message->file_size_formatted ?? '0 KB',
                'sender_name' => $message->sender_name ?? 'مجهول',
                'sender_type' => $message->sender_type,
                'created_at' => $message->created_at ? $message->created_at->format('H:i') : 'الآن',
                'created_at_full' => $message->created_at ? $message->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                'is_read' => $message->is_read ?? false,
                'file_path' => $message->file_path ?? '',
                'file_type' => $message->file_type ?? '',
                'file_size' => $message->file_size ?? 0,
                'can_delete' => $message->sender_type === 'admin' &&
                    $message->created_at &&
                    $message->created_at->diffInMinutes(now()) <= 30
            ];

            if ($message->message_type === 'voice') {
                $messageData['duration'] = $message->duration ?? 0;
                $messageData['duration_formatted'] = $message->duration_formatted ?? '0:00';
            }

            return $messageData;
        });

        return response()->json([
            'success' => true,
            'messages' => $formattedMessages,
            'last_message_id' => $newMessages->last()?->id ?? $lastMessageId
        ]);
    }

    public function getMessages($chatId)
    {
        $admin = auth('admin')->user();
        $chat = CustomerChat::findOrFail($chatId);

        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        // تحديد الرسائل كمقروءة للإدارة
        $chat->messages()
            ->where('sender_type', 'employee')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'messages' => $messages->map(function ($message) {
                $messageData = [
                    'id' => $message->id,
                    'content' => $message->content,
                    'message_type' => $message->message_type,
                    'file_url' => $message->file_url,
                    'file_name' => $message->file_name,
                    'file_size_formatted' => $message->file_size_formatted,
                    'sender_name' => $message->sender_name,
                    'sender_type' => $message->sender_type,
                    'created_at' => $message->created_at->format('H:i'),
                    'is_read' => $message->is_read,
                    'can_delete' => $message->sender_type === 'admin' &&
                        $message->created_at->diffInMinutes(now()) <= 30
                ];

                if ($message->message_type === 'voice') {
                    $messageData['duration'] = $message->duration;
                    $messageData['duration_formatted'] = $message->duration_formatted;
                }

                return $messageData;
            })
        ]);
    }

    public function deleteMessage($messageId)
    {
        $admin = auth('admin')->user();
        $message = CustomerChatMessage::findOrFail($messageId);

        // التحقق من أن الرسالة من هذا الأدمن وضمن 30 دقيقة
        if (
            $message->sender_type !== 'admin' ||
            $message->sender_id !== $admin->id ||
            $message->created_at->diffInMinutes(now()) > 30
        ) {
            return response()->json(['error' => 'لا يمكن حذف هذه الرسالة'], 403);
        }

        // حذف الملف إذا وجد
        if ($message->file_path && Storage::disk('public')->exists('customer-chat/' . $message->file_path)) {
            Storage::disk('public')->delete('customer-chat/' . $message->file_path);
        }

        $message->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الرسالة']);
    }

    public function clearChatFiles($chatId)
    {
        $admin = auth('admin')->user();
        $chat = CustomerChat::findOrFail($chatId);

        $fileMessages = $chat->messages()
            ->whereIn('message_type', ['file', 'voice'])
            ->get();

        $deletedCount = 0;
        $freedSpace = 0;

        foreach ($fileMessages as $message) {
            $freedSpace += $message->file_size ?? 0;

            // حذف الملف من التخزين
            if ($message->file_path && Storage::disk('public')->exists('customer-chat/' . $message->file_path)) {
                Storage::disk('public')->delete('customer-chat/' . $message->file_path);
            }

            $message->delete();
            $deletedCount++;
        }

        return response()->json([
            'success' => true,
            'deleted_count' => $deletedCount,
            'freed_space' => $this->formatBytes($freedSpace)
        ]);
    }

    public function clearEntireChat($chatId)
    {
        $admin = auth('admin')->user();
        $chat = CustomerChat::findOrFail($chatId);

        // حذف جميع الرسائل والملفات
        $messages = $chat->messages;

        foreach ($messages as $message) {
            if ($message->file_path && Storage::disk('public')->exists('customer-chat/' . $message->file_path)) {
                Storage::disk('public')->delete('customer-chat/' . $message->file_path);
            }
            $message->delete();
        }

        // حذف الشات نفسه
        $chat->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الشات بالكامل'
        ]);
    }

    public function getStorageInfo()
    {
        $totalSize = CustomerChatMessage::whereNotNull('file_size')->sum('file_size');
        $fileCount = CustomerChatMessage::whereIn('message_type', ['file', 'voice'])->count();

        return response()->json([
            'total_size' => $this->formatBytes($totalSize),
            'file_count' => $fileCount
        ]);
    }

    private function getAdminStats()
    {
        // الموظفين المؤهلين للتواصل مع العملاء
        $qualifiedEmployees = Employee::whereHas('roles.permissions', function ($query) {
            $query->where('name', 'customer_communication');
        })->where('status', 'active')->count();

        // العملاء المطلوب التواصل معهم
        $totalCustomers = PotentialCustomer::where(function ($query) {
            $query->whereJsonContains('customer_classifications', 'requested_call')
                ->orWhereJsonContains('customer_classifications', 'requested_visit');
        })->count();

        // عملاء المكالمات
        $callsRequests = PotentialCustomer::whereJsonContains('customer_classifications', 'requested_call')->count();

        // عملاء الزيارات
        $visitsRequests = PotentialCustomer::whereJsonContains('customer_classifications', 'requested_visit')->count();

        // العملاء عالي الأولوية
        $highPriority = PotentialCustomer::whereJsonContains('customer_classifications', 'difficult_customer')->count();

        // الشاتات النشطة
        $activeChats = CustomerChat::where('status', 'active')->count();

        // الرسائل غير المقروءة من الموظفين
        $unreadFromEmployees = CustomerChatMessage::where('sender_type', 'employee')
            ->where('is_read', false)
            ->count();

        // الشاتات التي يديرها هذا الأدمن
        $adminChats = CustomerChat::where('admin_id', auth('admin')->id())->count();

        return [
            'qualified_employees' => $qualifiedEmployees,
            'total_customers' => $totalCustomers,
            'calls_requests' => $callsRequests,
            'visits_requests' => $visitsRequests,
            'high_priority' => $highPriority,
            'active_chats' => $activeChats,
            'unread_from_employees' => $unreadFromEmployees,
            'admin_chats' => $adminChats
        ];
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
