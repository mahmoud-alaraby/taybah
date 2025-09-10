<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CustomerChat;
use App\Models\CustomerChatMessage;
use App\Models\PotentialCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CustomerCommunicationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');
        $employee = auth('employee')->user();

        // الحصول على العملاء المطلوب التواصل معهم (مكالمات أو زيارات)
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
        } elseif ($filter === 'my_chats') {
            // العملاء الذين لديهم شاتات مع هذا الموظف
            $customerIds = CustomerChat::where('employee_id', $employee->id)
                ->pluck('potential_customer_id')
                ->toArray();
            $query->whereIn('id', $customerIds);
        }

        $customers = $query->with(['customerChat' => function ($q) use ($employee) {
            $q->where('employee_id', $employee->id)
                ->orWhereNull('employee_id');
        }])
            ->withCount(['customerChat as unread_count' => function ($q) use ($employee) {
                $q->whereHas('messages', function ($msgQuery) {
                    $msgQuery->where('sender_type', 'admin')
                        ->where('is_read', false);
                });
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // الإحصائيات
        $stats = $this->getEmployeeStats();

        return view('employee.customer_communication.index', compact('customers', 'filter', 'stats'));
    }

    public function show($potentialCustomerId)
    {
        $employee = auth('employee')->user();

        // البحث عن العميل
        $customer = PotentialCustomer::findOrFail($potentialCustomerId);

        // البحث عن الشات بين الموظف والإدارة بخصوص هذا العميل
        $chat = CustomerChat::where('potential_customer_id', $potentialCustomerId)
            ->where('employee_id', $employee->id)
            ->first();

        // إذا لم يوجد شات، ننشئ واحد جديد
        if (!$chat) {
            $chat = CustomerChat::create([
                'potential_customer_id' => $potentialCustomerId,
                'employee_id' => $employee->id,
                'status' => 'pending',
                'priority' => $this->determineCustomerPriority($customer),
                'customer_type' => $this->determineCustomerType($customer),
                'last_message_at' => now()
            ]);
        }

        // تحميل الرسائل بين الموظف والإدارة
        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // تحديد الرسائل كمقروءة للموظف
        $chat->markAsRead('employee');

        // تحديث حالة الشات إلى نشط
        if ($chat->status === 'pending') {
            $chat->update(['status' => 'active']);
        }

        return view('employee.customer_communication.show', compact('customer', 'chat', 'messages'));
    }

    public function sendMessage(Request $request, $chatId)
    {
        $employee = auth('employee')->user();
        $chat = CustomerChat::findOrFail($chatId);

        // التحقق من أن هذا الشات خاص بهذا الموظف
        if ((int) $chat->employee_id !== (int) $employee->id) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }


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
                    'sender_type' => 'employee',
                    'sender_id' => $employee->id,
                    'message_type' => 'text',
                    'content' => $request->content
                ]);
            } elseif ($request->message_type === 'file') {
                $file = $request->file('file');
                $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('customer-chat/files', $fileName, 'public');

                $message = CustomerChatMessage::create([
                    'chat_id' => $chat->id,
                    'sender_type' => 'employee',
                    'sender_id' => $employee->id,
                    'message_type' => 'file',
                    'file_path' => 'files/' . $fileName,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType()
                ]);
            } elseif ($request->message_type === 'voice') {
                $voiceData = $request->voice;
                if (is_string($voiceData) && str_starts_with($voiceData, 'data:audio')) {
                    // فك تشفير الصوت من base64
                    $audio = base64_decode(explode(',', $voiceData)[1]);
                    $fileName = time() . '_' . Str::random(10) . '.webm';

                    // حفظ الملف
                    $filePath = 'customer-chat/voice/' . $fileName;
                    Storage::disk('public')->put($filePath, $audio);

                    // التأكد من حفظ الملف
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
                        'sender_type' => 'employee',
                        'sender_id' => $employee->id,
                        'message_type' => 'voice',
                        'file_path' => 'voice/' . $fileName,
                        'file_name' => $fileName,
                        'file_size' => strlen($audio),
                        'file_type' => 'audio/webm',
                        'duration' => $duration
                    ]);

                    // تسجيل المسار للتأكد
                    \Log::info('Voice file saved:', [
                        'path' => $filePath,
                        'url' => asset('storage/' . $filePath),
                        'exists' => Storage::disk('public')->exists($filePath)
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
                        'content' => $message->content,
                        'message_type' => $message->message_type,
                        'file_url' => $message->file_url,
                        'file_name' => $message->file_name,
                        'file_size_formatted' => $message->file_size_formatted,
                        'sender_name' => $message->sender_name,
                        'sender_type' => $message->sender_type,
                        'created_at' => $message->created_at->format('H:i'),
                        'created_at_full' => $message->created_at->format('Y-m-d H:i:s'),
                        'is_read' => $message->is_read,
                        'file_path' => $message->file_path,
                        'file_type' => $message->file_type,
                        'file_size' => $message->file_size
                    ]
                ];

                if ($message->message_type === 'voice') {
                    $voiceDuration = $message->voice_duration;
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

    // دالة محسنة لجلب الرسائل الجديدة فقط
    public function getNewMessages($chatId, Request $request)
    {
        $employee = auth('employee')->user();
        $chat = CustomerChat::findOrFail($chatId);

        if ((int) $chat->employee_id !== (int) $employee->id) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }


        $lastMessageId = $request->get('last_message_id', 0);

        $newMessages = $chat->messages()
            ->where('id', '>', $lastMessageId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // تحديد الرسائل من الإدارة كمقروءة
        $chat->messages()
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->where('id', '>', $lastMessageId)
            ->update(['is_read' => true, 'read_at' => now()]);

        $formattedMessages = $newMessages->map(function ($message) {
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
                'created_at_full' => $message->created_at->format('Y-m-d H:i:s'),
                'is_read' => $message->is_read,
                'file_path' => $message->file_path,
                'file_type' => $message->file_type,
                'file_size' => $message->file_size,
                'can_delete' => $message->sender_type === 'employee' &&
                    $message->created_at->diffInMinutes(now()) <= 5
            ];

            if ($message->message_type === 'voice') {
                $messageData['duration'] = $message->duration;
                $messageData['duration_formatted'] = $message->duration_formatted;
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
        $employee = auth('employee')->user();
        $chat = CustomerChat::findOrFail($chatId);

        if ((int) $chat->employee_id !== (int) $employee->id) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }


        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        // تحديد الرسائل كمقروءة للموظف
        $chat->messages()
            ->where('sender_type', 'admin')
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
                    'is_read' => $message->is_read
                ];

                if ($message->message_type === 'voice') {
                    $messageData['duration'] = $message->duration;
                    $messageData['duration_formatted'] = $message->duration_formatted;
                }

                return $messageData;
            })
        ]);
    }

    public function markAsCompleted($chatId)
    {
        $employee = auth('employee')->user();
        $chat = CustomerChat::findOrFail($chatId);

        if ((int) $chat->employee_id !== (int) $employee->id) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }


        $chat->update(['status' => 'completed']);

        return response()->json(['success' => true, 'message' => 'تم تحديث حالة التواصل مع العميل']);
    }

    public function deleteMessage($messageId)
    {
        $employee = auth('employee')->user();
        $message = CustomerChatMessage::findOrFail($messageId);

        // التحقق من أن الرسالة من هذا الموظف وضمن 5 دقائق
        if (
            $message->sender_type !== 'employee' ||
            $message->sender_id !== $employee->id ||
            $message->created_at->diffInMinutes(now()) > 5
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

    private function getEmployeeStats()
    {
        $employee = auth('employee')->user();

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

        // شاتات هذا الموظف
        $myChats = CustomerChat::where('employee_id', $employee->id)->count();

        // الرسائل غير المقروءة من الإدارة
        $unreadFromAdmin = CustomerChatMessage::whereHas('chat', function ($query) use ($employee) {
            $query->where('employee_id', $employee->id);
        })->where('sender_type', 'admin')
            ->where('is_read', false)
            ->count();

        return [
            'total_customers' => $totalCustomers,
            'calls_requests' => $callsRequests,
            'visits_requests' => $visitsRequests,
            'high_priority' => $highPriority,
            'my_chats' => $myChats,
            'unread_from_admin' => $unreadFromAdmin
        ];
    }

    private function determineCustomerPriority($customer)
    {
        $classifications = $customer->customer_classifications ?? [];
        if (is_string($classifications)) {
            $classifications = json_decode($classifications, true) ?? [];
        }

        if (in_array('difficult_customer', $classifications)) {
            return 'high';
        } elseif (in_array('requested_call', $classifications) || in_array('requested_visit', $classifications)) {
            return 'medium';
        }

        return 'normal';
    }

    private function determineCustomerType($customer)
    {
        $classifications = $customer->customer_classifications ?? [];
        if (is_string($classifications)) {
            $classifications = json_decode($classifications, true) ?? [];
        }

        if (in_array('requested_call', $classifications)) {
            return 'call_request';
        } elseif (in_array('requested_visit', $classifications)) {
            return 'visit_request';
        }

        return 'general';
    }
}
