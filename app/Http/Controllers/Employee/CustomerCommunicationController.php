<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CustomerCommunicationChat;
// use App\Models\CustomerCommunicationMessage;
use App\Models\PotentialCustomer;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class CustomerCommunicationController extends Controller
{
    // 1- قائمة الشاتات
    public function index(Request $request)
    {
        $employee = Auth::guard('employee')->user();

        $chats = CustomerCommunicationChat::where('employee_id', $employee->id)
            ->with([
                'potentialCustomer',
                'admin',
                'messages' => function ($q) {
                    $q->latest()->limit(1);
                }
            ])
            ->withCount(['messages as unread_count' => function ($q) {
                $q->where('sender_type', 'admin')->where('is_read', false);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        // احصائيات (يمكنك إضافة مزيد حسب الحاجة)
        $stats = [
            'total' => PotentialCustomer::count(),
        ];

        return view('employee.customer_communication.index', compact('chats', 'stats'));
    }

    // 2- شاشة الشات
    public function show(CustomerCommunicationChat $chat)
    {
       $employee = Auth::guard('employee')->user();

        if ($chat->employee_id !== $employee->id) {
            abort(403);
        }

        $chat->messages()
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('employee.customer_communication.show', compact('chat', 'messages'));
    }

    // 3- ارسال رسالة جديدة نص/ملف/صوت
    public function sendMessage(Request $request, CustomerCommunicationChat $chat)
    {
       $employee = Auth::guard('employee')->user();

        if ($chat->employee_id !== $employee->id) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }

        $request->validate([
            'message_type' => 'required|in:text,file,voice',
            'content' => 'required_if:message_type,text',
            'file' => 'required_if:message_type,file|file|max:10240',
            'voice' => 'required_if:message_type,voice',
            'duration' => 'nullable|integer',
        ]);

        $message = null;
        if ($request->message_type === 'text') {
            $message = CustomerCommunicationMessage::createTextMessage(
                $chat->id, 'employee', $employee->id, $request->content
            );
        } elseif ($request->message_type === 'file') {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('customer-communication/files', $fileName, 'public');
            $message = CustomerCommunicationMessage::createFileMessage(
                $chat->id, 'employee', $employee->id, [
                    'path' => 'files/' . $fileName,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(), 'type' => $file->getMimeType()
                ]
            );
        } elseif ($request->message_type === 'voice') {
            $voiceData = $request->voice;
            if (is_string($voiceData) && str_starts_with($voiceData, 'data:audio')) {
                $audio = base64_decode(explode(',', $voiceData)[1]);
                $fileName = time() . '_' . Str::random(10) . '.webm';
                Storage::disk('public')->put('customer-communication/voice/' . $fileName, $audio);

                $duration = is_numeric($request->duration) ? (int)$request->duration : 0;
                if ($duration > 1000) $duration = round($duration / 1000);
                if ($duration < 1) $duration = 1;
                elseif ($duration > 600) $duration = 600;

                $message = CustomerCommunicationMessage::createVoiceMessage(
                    $chat->id, 'employee', $employee->id, [
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
            $chat->last_message_at = now();
            $chat->save();
            return response()->json(['success' => true, 'message' => $message->toArray()]);
        }

        return response()->json(['error' => 'فشل في إرسال الرسالة'], 500);
    }

    // 4- جلب أحدث الرسائل (AJAX)
    public function getMessages(CustomerCommunicationChat $chat)
    {
      $employee = Auth::guard('employee')->user();
        if ($chat->employee_id !== $employee->id) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }

        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit(50)->get()->reverse()->values();

        $chat->messages()
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'messages' => $messages->map(function ($message) {
                $data = [
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
                ];
                if ($message->message_type === 'voice') {
                    $voiceDuration = $message->voice_duration;
                    $data['duration'] = $voiceDuration['total_seconds'];
                    $data['duration_formatted'] = $voiceDuration['formatted'];
                }
                return $data;
            }),
        ]);
    }

    // 5- حذف شات بالكامل وكل ملفاته
    public function destroy(CustomerCommunicationChat $chat)
    {
       $employee = Auth::guard('employee')->user();
        if ($chat->employee_id !== $employee->id) abort(403);

        $chat->messages->each(function($message) {
            $message->deleteFile();
        });
        $chat->delete();

        return redirect()->route('employee.customer-communication.index')
            ->with('success', 'تم حذف الشات بنجاح');
    }

    // 6- حذف الملفات (File+Voice) فقط لجميع الرسائل في شات
    public function clearFiles(CustomerCommunicationChat $chat)
    {
     $employee = Auth::guard('employee')->user();
        if ($chat->employee_id !== $employee->id) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }

        $fileMessages = $chat->messages()
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

    // 7- إحصائيات مساحة وتخزين الملفات المرتبطة بالشاتات
    public function getStorageInfo()
    {
       $employee = Auth::guard('employee')->user();
        $totalSize = CustomerCommunicationMessage::whereNotNull('file_size')
            ->whereHas('chat', function($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })->sum('file_size');

        $fileCount = CustomerCommunicationMessage::whereIn('message_type', ['file', 'voice'])
            ->whereHas('chat', function($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })->count();

        return response()->json([
            'total_size' => $this->formatBytes($totalSize),
            'file_count' => $fileCount
        ]);
    }

    // 8- صفحة إدارة المساحة (شبيهة بـwork_chat storage)
    public function storageManagement()
    {
      $employee = Auth::guard('employee')->user();
        $totalSize = CustomerCommunicationMessage::whereNotNull('file_size')
            ->whereHas('chat', function($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })->sum('file_size');

        $fileCount = CustomerCommunicationMessage::whereIn('message_type', ['file', 'voice'])
            ->whereHas('chat', function($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })->count();

        $fileStats = CustomerCommunicationMessage::selectRaw('
                message_type,
                COUNT(*) as count,
                SUM(file_size) as total_size
            ')
            ->whereIn('message_type', ['file', 'voice'])
            ->whereHas('chat', function($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })
            ->groupBy('message_type')
            ->get();

        $oldFiles = CustomerCommunicationMessage::whereIn('message_type', ['file', 'voice'])
            ->whereHas('chat', function($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })
            ->where('created_at', '<', now()->subMonths(3))
            ->selectRaw('COUNT(*) as count, SUM(file_size) as size')
            ->first();

        // شاتات للموظف فيها ملفات
        $userChats = CustomerCommunicationChat::where('employee_id', $employee->id)
            ->with(['potentialCustomer'])
            ->withCount(['messages as file_count' => function($q) {
                $q->whereIn('message_type', ['file', 'voice']);
            }])
            ->having('file_count', '>', 0)
            ->get();

        return view('employee.customer_communication.storage', compact(
            'totalSize', 'fileCount', 'fileStats', 'oldFiles', 'userChats'
        ));
    }

    // 9- حذف الملفات القديمة (+3 شهور)
    public function clearOldFiles()
    {
        $employee = Auth::guard('employee')->user();
        $oldMessages = CustomerCommunicationMessage::whereIn('message_type', ['file', 'voice'])
            ->whereHas('chat', function($q) use ($employee) {
                $q->where('employee_id', $employee->id);
            })->where('created_at', '<', now()->subMonths(3))
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

    // 10- تحميل كل ملفات شاتات الموظف كـ ZIP منظمة
    public function backupFiles()
    {
        return $this->createBackup();
    }

    // 11- تحميل ملفات شات محدد
    public function backupChatFiles($chatId)
    {
        $chat = CustomerCommunicationChat::where('id', $chatId)
            ->where('employee_id', Auth::id())->first();
        if (!$chat) {
            return response()->json(['error' => 'الشات غير موجود'], 404);
        }
        return $this->createBackup($chatId);
    }

    // دالة النسخ الإحتياطي البرمجية لكلا الحالتين
    private function createBackup($specificChatId = null)
    {
       $employee = Auth::guard('employee')->user();
        $zip = new ZipArchive();

        if ($specificChatId) {
            $chat = CustomerCommunicationChat::find($specificChatId);
            $backupName = 'chat_backup_' . $chat->id . '_' . Str::slug($chat->title) . '_' . date('Y_m_d_H_i_s') . '.zip';
        } else {
            $backupName = 'all_chats_backup_' . date('Y_m_d_H_i_s') . '.zip';
        }

        $backupPath = storage_path('app/backups/' . $backupName);
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        if ($zip->open($backupPath, ZipArchive::CREATE) === TRUE) {
            $query = CustomerCommunicationMessage::whereIn('message_type', ['file', 'voice'])
                ->whereNotNull('file_path')
                ->with(['chat.potentialCustomer', 'chat.admin']);

            if ($specificChatId) {
                $query->where('chat_id', $specificChatId);
            } else {
                $query->whereHas('chat', function($q) use ($employee) {
                    $q->where('employee_id', $employee->id);
                });
            }
            $messages = $query->get();

            $chatInfo = [];
            $processedChats = [];
            foreach ($messages as $message) {
                $chat = $message->chat;
                $filePath = storage_path('app/public/customer-communication/' . $message->file_path);

                if (file_exists($filePath)) {
                    $chatFolder = "Chat_{$chat->id}_" . Str::slug($chat->title);
                    $typeFolder = $message->message_type === 'voice' ? 'voices' : 'files';
                    $zipFilePath = $chatFolder . '/' . $typeFolder . '/' . $message->file_name;
                    $zip->addFile($filePath, $zipFilePath);

                    if (!isset($processedChats[$chat->id])) {
                        $chatInfo[] = [
                            'chat_id' => $chat->id,
                            'title' => $chat->title,
                            'admin' => $chat->admin->name ?? 'غير محدد',
                            'customer' => $chat->potentialCustomer->customer_name ?? 'غير محدد',
                            'created_at' => $chat->created_at->format('Y-m-d H:i:s'),
                            'messages_count' => $chat->messages()->count(),
                            'files_count' => $chat->messages()->whereIn('message_type', ['file', 'voice'])->count(),
                        ];
                        $processedChats[$chat->id] = true;
                    }
                }
            }

            $readmeContent = $this->generateReadmeContent($chatInfo, $specificChatId);
            $zip->addFromString('README.txt', $readmeContent);

            $detailsContent = json_encode([
                'backup_date' => date('Y-m-d H:i:s'),
                'backup_type' => $specificChatId ? 'single_chat' : 'all_chats',
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'chats' => $chatInfo,
                'total_files' => $messages->count()
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $zip->addFromString('backup_details.json', $detailsContent);
            $zip->close();

            return response()->download($backupPath)->deleteFileAfterSend();
        }
        return response()->json(['error' => 'فشل في إنشاء النسخة الاحتياطية'], 500);
    }

    private function generateReadmeContent($chatInfo, $specificChatId = null)
    {
       $employee = Auth::guard('employee')->user();
        $content = "=== نسخة احتياطية من ملفات شاتات التواصل مع العملاء ===\n\n";
        $content .= "تاريخ النسخة الاحتياطية: " . date('Y-m-d H:i:s') . "\n";
        $content .= "نوع النسخة: " . ($specificChatId ? 'شات واحد' : 'جميع الشاتات') . "\n";
        $content .= "الموظف: " . $employee->name . "\n\n";
        $content .= "=== هيكل المجلدات ===\n";
        $content .= "Chat_{ID}_{TITLE}/\n";
        $content .= " ├── files/ (الملفات المرفقة)\n";
        $content .= " └── voices/ (التسجيلات الصوتية)\n\n";
        $content .= "=== معلومات الشاتات ===\n\n";

        foreach ($chatInfo as $chat) {
            $content .= "شات رقم: {$chat['chat_id']}\n";
            $content .= "العنوان: {$chat['title']}\n";
            $content .= "المدير: {$chat['admin']}\n";
            $content .= "العميل: {$chat['customer']}\n";
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
