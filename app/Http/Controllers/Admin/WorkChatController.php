<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkChat;
use App\Models\WorkChatMessage;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

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
        $adminId = auth('admin')->id();
        
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

        // قائمة الشاتات للمدير الحالي
        $userChats = WorkChat::where('admin_id', $adminId)
            ->with(['employee'])
            ->withCount(['messages as file_count' => function($q) {
                $q->whereIn('message_type', ['file', 'voice']);
            }])
            ->having('file_count', '>', 0)
            ->get();

        return view('admin.work-chat.storage', compact('totalSize', 'fileCount', 'fileStats', 'oldFiles', 'userChats'));
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

    // تحميل جميع الملفات
    public function backupFiles()
    {
        return $this->createBackup();
    }

    // تحميل ملفات شات محدد
    public function backupChatFiles($chatId)
    {
        $chat = WorkChat::where('id', $chatId)
            ->where('admin_id', auth('admin')->id())
            ->with('employee')
            ->first();

        if (!$chat) {
            return response()->json(['error' => 'الشات غير موجود'], 404);
        }

        return $this->createBackup($chatId);
    }

    // دالة إنشاء النسخة الاحتياطية المحسنة
    private function createBackup($specificChatId = null)
    {
        $zip = new ZipArchive();
        
        // تحديد اسم الملف
        if ($specificChatId) {
            $chat = WorkChat::find($specificChatId);
            $backupName = 'chat_backup_' . $chat->id . '_' . Str::slug($chat->title) . '_' . date('Y_m_d_H_i_s') . '.zip';
        } else {
            $backupName = 'all_chats_backup_' . date('Y_m_d_H_i_s') . '.zip';
        }
        
        $backupPath = storage_path('app/backups/' . $backupName);
        
        // إنشاء مجلد البكاب إذا لم يكن موجود
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        if ($zip->open($backupPath, ZipArchive::CREATE) === TRUE) {
            
            // استعلام الرسائل
            $query = WorkChatMessage::whereIn('message_type', ['file', 'voice'])
                ->whereNotNull('file_path')
                ->with(['chat.employee', 'chat.admin']);

            // إضافة فلتر للشات المحدد إذا لزم الأمر
            if ($specificChatId) {
                $query->where('chat_id', $specificChatId);
            } else {
                // فقط الشاتات التي يملكها المدير الحالي
                $query->whereHas('chat', function($q) {
                    $q->where('admin_id', auth('admin')->id());
                });
            }

            $messages = $query->get();

            // إنشاء ملف معلومات الشاتات
            $chatInfo = [];
            $processedChats = [];

            foreach ($messages as $message) {
                $chat = $message->chat;
                $filePath = storage_path('app/public/work-chat/' . $message->file_path);
                
                if (file_exists($filePath)) {
                    // تنظيم الملفات حسب الشات
                    $chatFolder = "Chat_{$chat->id}_{$chat->type}_" . Str::slug($chat->title);
                    
                    // تحديد نوع المجلد (files أو voices)
                    $typeFolder = $message->message_type === 'voice' ? 'voices' : 'files';
                    
                    // مسار الملف في الـ ZIP
                    $zipFilePath = $chatFolder . '/' . $typeFolder . '/' . $message->file_name;
                    
                    // إضافة الملف للـ ZIP
                    $zip->addFile($filePath, $zipFilePath);
                    
                    // جمع معلومات الشات
                    if (!isset($processedChats[$chat->id])) {
                        $chatInfo[] = [
                            'chat_id' => $chat->id,
                            'title' => $chat->title,
                            'type' => $chat->type,
                            'admin' => $chat->admin->name ?? 'غير محدد',
                            'employee' => $chat->employee->name ?? 'غير محدد',
                            'created_at' => $chat->created_at->format('Y-m-d H:i:s'),
                            'messages_count' => $chat->messages()->count(),
                            'files_count' => $chat->messages()->whereIn('message_type', ['file', 'voice'])->count()
                        ];
                        $processedChats[$chat->id] = true;
                    }
                }
            }

            // إنشاء ملف README مع معلومات الشاتات
            $readmeContent = $this->generateReadmeContent($chatInfo, $specificChatId);
            $zip->addFromString('README.txt', $readmeContent);

            // إنشاء ملف JSON مع التفاصيل الكاملة
            $detailsContent = json_encode([
                'backup_date' => date('Y-m-d H:i:s'),
                'backup_type' => $specificChatId ? 'single_chat' : 'all_chats',
                'admin_id' => auth('admin')->id(),
                'admin_name' => auth('admin')->user()->name,
                'chats' => $chatInfo,
                'total_files' => $messages->count()
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            $zip->addFromString('backup_details.json', $detailsContent);

            $zip->close();

            return response()->download($backupPath)->deleteFileAfterSend();
        }

        return response()->json(['error' => 'فشل في إنشاء النسخة الاحتياطية'], 500);
    }

    // إنشاء محتوى ملف README
    private function generateReadmeContent($chatInfo, $specificChatId = null)
    {
        $content = "=== نسخة احتياطية من ملفات شاتات العمل ===\n\n";
        $content .= "تاريخ النسخة الاحتياطية: " . date('Y-m-d H:i:s') . "\n";
        $content .= "نوع النسخة: " . ($specificChatId ? 'شات واحد' : 'جميع الشاتات') . "\n";
        $content .= "المدير: " . auth('admin')->user()->name . "\n\n";

        $content .= "=== هيكل المجلدات ===\n";
        $content .= "Chat_{ID}_{TYPE}_{TITLE}/\n";
        $content .= "  ├── files/          (الملفات المرفقة)\n";
        $content .= "  └── voices/         (التسجيلات الصوتية)\n\n";

        $content .= "=== معلومات الشاتات ===\n\n";

        foreach ($chatInfo as $chat) {
            $content .= "شات رقم: {$chat['chat_id']}\n";
            $content .= "العنوان: {$chat['title']}\n";
            $content .= "النوع: " . ($chat['type'] === 'design' ? 'تصميم' : 'مونتاج') . "\n";
            $content .= "المدير: {$chat['admin']}\n";
            $content .= "الموظف: {$chat['employee']}\n";
            $content .= "تاريخ الإنشاء: {$chat['created_at']}\n";
            $content .= "عدد الرسائل: {$chat['messages_count']}\n";
            $content .= "عدد الملفات: {$chat['files_count']}\n";
            $content .= str_repeat('-', 50) . "\n\n";
        }

        $content .= "=== ملاحظات ===\n";
        $content .= "- جميع الملفات محفوظة بأسمائها الأصلية\n";
        $content .= "- التسجيلات الصوتية بصيغة WebM\n";
        $content .= "- تفاصيل إضافية متوفرة في ملف backup_details.json\n";

        return $content;
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