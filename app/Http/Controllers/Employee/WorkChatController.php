<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\WorkChat;
use App\Models\WorkChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkChatController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth('employee')->user();
        $type = $request->get('type');

        // التحقق من الصلاحية
        if ($type === 'design' && !$employee->hasPermission('design_follow_up')) {
            abort(403, 'ليس لديك صلاحية للوصول لشاتات التصميم');
        }
        
        if ($type === 'montage' && !$employee->hasPermission('montage_follow_up')) {
            abort(403, 'ليس لديك صلاحية للوصول لشاتات المونتاج');
        }

        $query = WorkChat::where('employee_id', $employee->id);
        
        if ($type) {
            $query->where('type', $type);
        }

        $chats = $query->with(['admin', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->withCount(['messages as unread_count' => function($q) use ($employee) {
                $q->where('sender_type', 'admin')
                  ->where('is_read', false);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('employee.work-chat.index', compact('chats', 'type'));
    }

    public function show(WorkChat $workChat)
    {
        $employee = auth('employee')->user();
        
        // تأكد أن الموظف مشارك في الشات
        if ((int)$workChat->employee_id !== (int)$employee->id) {
            abort(403);
        }

        // التحقق من الصلاحية حسب نوع الشات
        if ($workChat->type === 'design' && !$employee->hasPermission('design_follow_up')) {
            abort(403, 'ليس لديك صلاحية للوصول لهذا الشات');
        }
        
        if ($workChat->type === 'montage' && !$employee->hasPermission('montage_follow_up')) {
            abort(403, 'ليس لديك صلاحية للوصول لهذا الشات');
        }

        // تحديد الرسائل كمقروءة للموظف
        $workChat->messages()
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $messages = $workChat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('employee.work-chat.show', compact('workChat', 'messages'));
    }

    public function sendMessage(Request $request, WorkChat $workChat)
    {
        $employee = auth('employee')->user();
        
        if ((int)$workChat->employee_id !== (int)$employee->id) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }

        // التحقق من الصلاحية
        if ($workChat->type === 'design' && !$employee->hasPermission('design_follow_up')) {
            return response()->json(['error' => 'ليس لديك صلاحية'], 403);
        }
        
        if ($workChat->type === 'montage' && !$employee->hasPermission('montage_follow_up')) {
            return response()->json(['error' => 'ليس لديك صلاحية'], 403);
        }

        $request->validate([
            'message_type' => 'required|in:text,file,voice',
            'content' => 'required_if:message_type,text',
            'file' => 'required_if:message_type,file|file|max:10240', // 10MB
            'voice' => 'required_if:message_type,voice'
        ]);

        $message = null;

        if ($request->message_type === 'text') {
            $message = WorkChatMessage::createTextMessage(
                $workChat->id,
                'employee',
                $employee->id,
                $request->content
            );
        } 
        elseif ($request->message_type === 'file') {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('work-chat/files', $fileName, 'public');
            
            $message = WorkChatMessage::createFileMessage(
                $workChat->id,
                'employee',
                $employee->id,
                [
                    'path' => 'files/' . $fileName,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ]
            );
        }
        elseif ($request->message_type === 'voice') {
            // التعامل مع الصوت (base64 أو ملف)
            $voiceData = $request->voice;
            if (is_string($voiceData) && str_starts_with($voiceData, 'data:audio')) {
                // Base64 audio
                $audio = base64_decode(explode(',', $voiceData)[1]);
                $fileName = time() . '_' . Str::random(10) . '.webm';
                Storage::disk('public')->put('work-chat/voice/' . $fileName, $audio);
                
                // تحسين معالجة المدة الزمنية
                $duration = $request->duration;
                
                // التأكد من أن المدة رقم صحيح
                $duration = is_numeric($duration) ? (int) $duration : 0;
                
                // إذا كانت المدة كبيرة جداً، فهي على الأرجح بالميلي ثانية
                if ($duration > 1000) {
                    $duration = round($duration / 1000);
                }
                
                // تأكد من أن المدة منطقية (بين 1 ثانية و 10 دقائق)
                if ($duration < 1) {
                    $duration = 1; // أقل مدة ثانية واحدة
                } elseif ($duration > 600) {
                    $duration = 600; // أقصى مدة 10 دقائق
                }
                
                $message = WorkChatMessage::createVoiceMessage(
                    $workChat->id,
                    'employee',
                    $employee->id,
                    [
                        'path' => 'voice/' . $fileName,
                        'name' => $fileName,
                        'size' => strlen($audio),
                        'duration' => $duration
                    ]
                );
            }
        }

        if ($message) {
            $message->load('sender');
            
            // إرجاع البيانات مع المدة المصححة
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
                'is_read' => $message->is_read
            ];
            
            // إضافة معلومات المدة للرسائل الصوتية
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

        return response()->json(['error' => 'فشل في إرسال الرسالة'], 500);
    }

    public function getMessages(WorkChat $workChat)
    {
        $employee = auth('employee')->user();
        
        if ((int)$workChat->employee_id !== (int)$employee->id) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }

        // التحقق من الصلاحية
        if ($workChat->type === 'design' && !$employee->hasPermission('design_follow_up')) {
            return response()->json(['error' => 'ليس لديك صلاحية'], 403);
        }
        
        if ($workChat->type === 'montage' && !$employee->hasPermission('montage_follow_up')) {
            return response()->json(['error' => 'ليس لديك صلاحية'], 403);
        }

        $messages = $workChat->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        // تحديد الرسائل كمقروءة
        $workChat->messages()
            ->where('sender_type', 'admin')
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
                
                // إضافة معلومات المدة للرسائل الصوتية
                if ($message->message_type === 'voice') {
                    $voiceDuration = $message->voice_duration;
                    $messageData['duration'] = $voiceDuration['total_seconds'];
                    $messageData['duration_formatted'] = $voiceDuration['formatted'];
                }
                
                return $messageData;
            })
        ]);
    }
}