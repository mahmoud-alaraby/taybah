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

class CustomerCommunicationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');
        $admin = auth('admin')->user();

        // الحصول على الموظفين الذين لديهم صلاحية customer_communication
        $employeesWithPermission = Employee::whereHas('roles.permissions', function($query) {
            $query->where('name', 'customer_communication');
        })->pluck('id');

        // الحصول على العملاء المطلوب التواصل معهم
        $query = PotentialCustomer::where(function($q) {
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
            // العملاء الذين لديهم رسائل غير مقروءة من الموظفين
            $customerIds = CustomerChat::whereHas('messages', function($msgQuery) {
                $msgQuery->where('sender_type', 'employee')
                         ->where('is_read', false);
            })->pluck('potential_customer_id')->toArray();
            $query->whereIn('id', $customerIds);
        }

        $customers = $query->with(['customerChat' => function($q) use ($employeesWithPermission) {
                        $q->whereIn('employee_id', $employeesWithPermission)
                          ->with('employee');
                    }])
                    ->withCount(['customerChat as unread_count' => function($q) {
                        $q->whereHas('messages', function($msgQuery) {
                            $msgQuery->where('sender_type', 'employee')
                                     ->where('is_read', false);
                        });
                    }])
                    ->orderBy('created_at', 'desc')
                    ->paginate(12);

        // الإحصائيات
        $stats = $this->getAdminStats();

        return view('admin.customer_communication.index', compact('customers', 'filter', 'stats'));
    }

    public function show($potentialCustomerId)
    {
        $admin = auth('admin')->user();
        
        // البحث عن العميل
        $customer = PotentialCustomer::findOrFail($potentialCustomerId);
        
        // البحث عن الشات الخاص بهذا العميل مع أي موظف
        $chat = CustomerChat::where('potential_customer_id', $potentialCustomerId)
                           ->with('employee')
                           ->first();
        
        // إذا لم يوجد شات، لا يمكن للإدارة بدء شات جديد
        if (!$chat) {
            return redirect()->route('admin.customer-communication.index')
                           ->with('error', 'لا يوجد تواصل مبدأ مع هذا العميل من قبل الموظفين');
        }

        // تحميل الرسائل بين الموظف والإدارة
        $messages = $chat->messages()
                        ->with('sender')
                        ->orderBy('created_at', 'asc')
                        ->get();

        // تحديد الرسائل كمقروءة للإدارة
        $chat->markAsRead('admin');

        return view('admin.customer_communication.show', compact('customer', 'chat', 'messages'));
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
            } 
            elseif ($request->message_type === 'file') {
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
            }
            elseif ($request->message_type === 'voice') {
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
                        'sender_type' => 'admin',
                        'sender_id' => $admin->id,
                        'message_type' => 'voice',
                        'file_path' => 'voice/' . $fileName,
                        'file_name' => $fileName,
                        'file_size' => strlen($audio),
                        'file_type' => 'audio/webm',
                        'duration' => $duration
                    ]);
                    
                    // تسجيل المسار للتأكد
                    \Log::info('Admin Voice file saved:', [
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
                    'file_path' => $message->file_path,
                    'file_type' => $message->file_type,
                    'file_size' => $message->file_size
                ];
                
                if ($message->message_type === 'voice') {
                    $voiceDuration = $message->voice_duration;
                    $responseData['duration'] = $voiceDuration['total_seconds'];
                    $responseData['duration_formatted'] = $voiceDuration['formatted'];
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $responseData
                ]);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'فشل في إرسال الرسالة: ' . $e->getMessage()], 500);
        }

        return response()->json(['error' => 'فشل في إرسال الرسالة'], 500);
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
            'messages' => $messages->map(function($message) {
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

    public function deleteMessage($messageId)
    {
        $admin = auth('admin')->user();
        $message = CustomerChatMessage::findOrFail($messageId);

        // التحقق من أن الرسالة من هذا الإدارة وضمن 5 دقائق
        if ($message->sender_type !== 'admin' || 
            $message->sender_id !== $admin->id ||
            $message->created_at->diffInMinutes(now()) > 5) {
            return response()->json(['error' => 'لا يمكن حذف هذه الرسالة'], 403);
        }

        // حذف الملف إذا وجد
        if ($message->file_path && Storage::disk('public')->exists('customer-chat/' . $message->file_path)) {
            Storage::disk('public')->delete('customer-chat/' . $message->file_path);
        }

        $message->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الرسالة']);
    }

    private function getAdminStats()
    {
        // الموظفين المؤهلين للتواصل مع العملاء
        $qualifiedEmployees = Employee::whereHas('roles.permissions', function($query) {
            $query->where('name', 'customer_communication');
        })->count();
        
        // العملاء المطلوب التواصل معهم
        $totalCustomers = PotentialCustomer::where(function($query) {
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

        return [
            'qualified_employees' => $qualifiedEmployees,
            'total_customers' => $totalCustomers,
            'calls_requests' => $callsRequests,
            'visits_requests' => $visitsRequests,
            'high_priority' => $highPriority,
            'active_chats' => $activeChats,
            'unread_from_employees' => $unreadFromEmployees
        ];
    }
}