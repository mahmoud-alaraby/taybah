<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkChat;
use App\Models\WorkChatMessage;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkChatController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'design'); // design or montage
        $adminId = auth('admin')->id();

        $chats = WorkChat::where('admin_id', $adminId)
            ->where('type', $type)
            ->with(['employee', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->withCount(['messages as unread_count' => function($q) use ($adminId) {
                $q->where('sender_type', 'employee')
                  ->where('is_read', false);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('admin.work-chat.index', compact('chats', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'design');
        
        // قائمة الموظفين حسب النوع
        $permission = $type === 'design' ? 'design_follow_up' : 'montage_follow_up';
        
        $employees = Employee::whereHas('roles.permissions', function($q) use ($permission) {
            $q->where('name', $permission);
        })->where('status', 'active')->get();

        return view('admin.work-chat.create', compact('employees', 'type'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:design,montage',
            'employee_id' => 'required|exists:employees,id'
        ]);

        $chat = WorkChat::create([
            'title' => $request->title,
            'type' => $request->type,
            'admin_id' => auth('admin')->id(),
            'employee_id' => $request->employee_id
        ]);

        return redirect()->route('admin.work-chat.show', $chat);
    }

    public function show(WorkChat $workChat)
    {
        // تأكد أن المدير مالك الشات
        if ($workChat->admin_id !== auth('admin')->id()) {
            abort(403);
        }

        // تحديد الرسائل كمقروءة للمدير
        $workChat->messages()
            ->where('sender_type', 'employee')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $messages = $workChat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.work-chat.show', compact('workChat', 'messages'));
    }

    public function sendMessage(Request $request, WorkChat $workChat)
    {
        if ($workChat->admin_id !== auth('admin')->id()) {
            return response()->json(['error' => 'غير مسموح'], 403);
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
                'admin',
                auth('admin')->id(),
                $request->content
            );
        } 
        elseif ($request->message_type === 'file') {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('work-chat/files', $fileName, 'public');
            
            $message = WorkChatMessage::createFileMessage(
                $workChat->id,
                'admin',
                auth('admin')->id(),
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
                
                // تحويل المدة من milliseconds إلى seconds إذا لزم الأمر
                $duration = $request->duration;
                if ($duration > 1000) {
                    $duration = round($duration / 1000); // تحويل من milliseconds
                }
                
                $message = WorkChatMessage::createVoiceMessage(
                    $workChat->id,
                    'admin',
                    auth('admin')->id(),
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
            return response()->json([
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
                    'created_at' => $message->created_at->format('H:i')
                ]
            ]);
        }

        return response()->json(['error' => 'فشل في إرسال الرسالة'], 500);
    }

    public function getMessages(WorkChat $workChat)
    {
        if ($workChat->admin_id !== auth('admin')->id()) {
            return response()->json(['error' => 'غير مسموح'], 403);
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
            ->where('sender_type', 'employee')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'messages' => $messages->map(function($message) {
                return [
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
            })
        ]);
    }

    public function destroy(WorkChat $workChat)
    {
        if ($workChat->admin_id !== auth('admin')->id()) {
            abort(403);
        }

        // حذف جميع الملفات المرتبطة
        $workChat->messages()->each(function($message) {
            $message->deleteFile();
        });

        $workChat->delete();

        return redirect()->route('admin.work-chat.index')
            ->with('success', 'تم حذف الشات بنجاح');
    }

    public function clearFiles(WorkChat $workChat)
    {
        if ($workChat->admin_id !== auth('admin')->id()) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }

        $fileMessages = $workChat->messages()
            ->whereIn('message_type', ['file', 'voice'])
            ->get();

        $deletedCount = 0;
        $freedSpace = 0;

        foreach ($fileMessages as $message) {
            $freedSpace += $message->file_size ?? 0;
            $message->deleteFile();
            $message->delete();
            $deletedCount++;
        }

        return response()->json([
            'success' => true,
            'deleted_count' => $deletedCount,
            'freed_space' => $this->formatBytes($freedSpace)
        ]);
    }

    public function getStorageInfo()
    {
        $totalSize = WorkChatMessage::whereNotNull('file_size')->sum('file_size');
        $fileCount = WorkChatMessage::whereIn('message_type', ['file', 'voice'])->count();

        return response()->json([
            'total_size' => $this->formatBytes($totalSize),
            'file_count' => $fileCount
        ]);
    }

    public function storageManagement()
    {
        $totalSize = WorkChatMessage::whereNotNull('file_size')->sum('file_size');
        $fileCount = WorkChatMessage::whereIn('message_type', ['file', 'voice'])->count();
        
        // إحصائيات تفصيلية
        $fileStats = WorkChatMessage::selectRaw('
            message_type,
            COUNT(*) as count,
            SUM(file_size) as total_size
        ')
        ->whereIn('message_type', ['file', 'voice'])
        ->groupBy('message_type')
        ->get();

        $oldFiles = WorkChatMessage::whereIn('message_type', ['file', 'voice'])
            ->where('created_at', '<', now()->subMonths(3))
            ->selectRaw('COUNT(*) as count, SUM(file_size) as size')
            ->first();

        return view('admin.work-chat.storage', compact('totalSize', 'fileCount', 'fileStats', 'oldFiles'));
    }

    public function clearOldFiles()
    {
        $oldMessages = WorkChatMessage::whereIn('message_type', ['file', 'voice'])
            ->where('created_at', '<', now()->subMonths(3))
            ->get();

        $deletedCount = 0;
        $freedSpace = 0;

        foreach ($oldMessages as $message) {
            $freedSpace += $message->file_size ?? 0;
            $message->deleteFile();
            $message->delete();
            $deletedCount++;
        }

        return response()->json([
            'success' => true,
            'deleted_count' => $deletedCount,
            'freed_space' => $this->formatBytes($freedSpace)
        ]);
    }

    public function backupFiles()
    {
        $zip = new \ZipArchive();
        $backupName = 'work_chat_backup_' . date('Y_m_d_H_i_s') . '.zip';
        $backupPath = storage_path('app/backups/' . $backupName);
        
        // إنشاء مجلد البكاب إذا لم يكن موجود
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        if ($zip->open($backupPath, \ZipArchive::CREATE) === TRUE) {
            $messages = WorkChatMessage::whereIn('message_type', ['file', 'voice'])
                ->whereNotNull('file_path')
                ->get();

            foreach ($messages as $message) {
                $filePath = storage_path('app/public/work-chat/' . $message->file_path);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, 'chat_files/' . $message->file_path);
                }
            }

            $zip->close();

            return response()->download($backupPath)->deleteFileAfterSend();
        }

        return response()->json(['error' => 'فشل في إنشاء النسخة الاحتياطية'], 500);
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