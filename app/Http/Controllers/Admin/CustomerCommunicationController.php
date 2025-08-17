<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerCommunicationChat;
use App\Models\CustomerCommunicationMessage;
use App\Models\PotentialCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class CustomerCommunicationController extends Controller
{
    // 1- عرض قائمة الشاتات الخاصة بالادمن مع احصائيات وعدد الرسائل غير المقروءة
 public function index(Request $request)
{
    $admin = Auth::guard('admin')->user();
    if (!$admin) abort(403);

    $type = $request->get('type', null);
    $query = CustomerCommunicationChat::where('admin_id', $admin->id);
    if ($type) {
        $query->where('type', $type);
    }
    $chats = $query->with([
            'potentialCustomer',
            'employee',
            'messages' => function ($q) {
                $q->latest()->limit(1);
            }
        ])
        ->withCount(['messages as unread_count' => function ($q) {
            $q->where('sender_type', 'employee')->where('is_read', false);
        }])
        ->orderBy('last_message_at', 'desc')
        ->get();

    $stats = [
        'total' => PotentialCustomer::count(),
    ];

    // إضافة الإحصائيات المطلوبة في فلترة العملاء
    $callPendingCount = PotentialCustomer::whereJsonContains('customer_classifications', 'requested_call')->count();
    $visitPendingCount = PotentialCustomer::whereJsonContains('customer_classifications', 'requested_visit')->count();

// جلب العملاء المنتظرين مكالمة هاتفية أو زيارة
$customers = PotentialCustomer::where(function ($q) {
    $q->whereJsonContains('customer_classifications', 'requested_call')
      ->orWhereJsonContains('customer_classifications', 'requested_visit');
})
->with(['employee']) // لو أردت اسم الموظف... حسب الاحتياج
->get();

// اختياري: تجهيز كل عميل ليحتوي مصفوفة display_name
foreach ($customers as $customer) {
    $classifications = $customer->customer_classifications; // مصفوفة Json
    // تحويلها لنماذج عرض (يمكن تحسين الكود لو عندك Model خاص)
    $customer->customer_classifications_array = \App\Models\PotentialCustomerClassification::whereIn('name', $classifications)->get();
    
    // إذا كنت تريد ربط الشات بالعميل
    $customer->chat = \App\Models\CustomerCommunicationChat::where('potential_customer_id', $customer->id)->first();
    // يمكنك عمل count لغير المقروء لكل شات مباشرة
    $customer->chat_unread_messages_count = $customer->chat ? $customer->chat->messages()->where('is_read', false)->count() : 0;
    // اسم الموظف لو موجود
    $customer->employee_name = $customer->employee->name ?? null;
}

 return view('admin.customer_communication.index', compact(
    'chats', 'type', 'stats',
    'callPendingCount', 'visitPendingCount',
    'customers'
));

}

    // 2- عرض شاشة تفاصيل الشات
    public function show(CustomerCommunicationChat $chat)
    {
        $admin = Auth::guard('admin')->user();

        if ($chat->admin_id !== $admin->id) {
            abort(403);
        }

        $chat->messages()
            ->where('sender_type', 'employee')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.customer_communication.show', compact('chat', 'messages'));
    }

    // 3- إرسال رسالة من الادمن داخل الشات
    public function sendMessage(Request $request, CustomerCommunicationChat $chat)
    {
        $admin = Auth::guard('admin')->user();

        if ($chat->admin_id !== $admin->id) {
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
                $chat->id, 'admin', $admin->id, $request->content
            );
        } elseif ($request->message_type === 'file') {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('customer-communication/files', $fileName, 'public');
            $message = CustomerCommunicationMessage::createFileMessage(
                $chat->id, 'admin', $admin->id, [
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
                    $chat->id, 'admin', $admin->id, [
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
        $admin = Auth::guard('admin')->user();
        if ($chat->admin_id !== $admin->id) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }

        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit(50)->get()->reverse()->values();

        $chat->messages()
            ->where('sender_type', 'employee')
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
        $admin = Auth::guard('admin')->user();
        if ($chat->admin_id !== $admin->id) abort(403);

        $chat->messages->each(function($message) {
            $message->deleteFile();
        });
        $chat->delete();

        return redirect()->route('admin.customer-communication.index')
            ->with('success', 'تم حذف الشات بنجاح');
    }

    // 6- حذف الملفات (File+Voice) فقط لجميع الرسائل في شات
    public function clearFiles(CustomerCommunicationChat $chat)
    {
        $admin = Auth::guard('admin')->user();
        if ($chat->admin_id !== $admin->id) {
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
        $admin = Auth::guard('admin')->user();
        $totalSize = CustomerCommunicationMessage::whereNotNull('file_size')
            ->whereHas('chat', function($q) use ($admin) {
                $q->where('admin_id', $admin->id);
            })->sum('file_size');

        $fileCount = CustomerCommunicationMessage::whereIn('message_type', ['file', 'voice'])
            ->whereHas('chat', function($q) use ($admin) {
                $q->where('admin_id', $admin->id);
            })->count();

        return response()->json([
            'total_size' => $this->formatBytes($totalSize),
            'file_count' => $fileCount
        ]);
    }
public function createChat(Request $request)
{
    $admin = Auth::guard('admin')->user();
    if (!$admin) abort(403);

    $customerId = $request->input('customer_id');
    $employeeId = $request->input('employee_id', null);

    // تحقق أن العميل موجود
    $customer = \App\Models\PotentialCustomer::findOrFail($customerId);

    // تحقق ألا يوجد شات سابق لهذه العلاقة
    $existingChat = \App\Models\CustomerCommunicationChat::where('potential_customer_id', $customerId)
        ->where('admin_id', $admin->id)
        ->first();

    if ($existingChat) {
        // إذا فيه شات بالفعل: redirect للشات
        return redirect()->route('admin.customer-communication.show', $existingChat->id)
            ->with('info', 'يوجد شات بالفعل لهذا العميل.');
    }

    // أنشئ الشات الجديد
    $chat = \App\Models\CustomerCommunicationChat::create([
        'potential_customer_id' => $customerId,
        'admin_id' => $admin->id,
        'employee_id' => $employeeId,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    return redirect()->route('admin.customer-communication.show', $chat->id)
        ->with('success', 'تم إنشاء الشات بنجاح.');
}

    // 8- صفحة إدارة المساحة
    public function storageManagement()
    {
        $admin = Auth::guard('admin')->user();
        $totalSize = CustomerCommunicationMessage::whereNotNull('file_size')
            ->whereHas('chat', function($q) use ($admin) {
                $q->where('admin_id', $admin->id);
            })->sum('file_size');

        $fileCount = CustomerCommunicationMessage::whereIn('message_type', ['file', 'voice'])
            ->whereHas('chat', function($q) use ($admin) {
                $q->where('admin_id', $admin->id);
            })->count();

        $fileStats = CustomerCommunicationMessage::selectRaw('
                message_type,
                COUNT(*) as count,
                SUM(file_size) as total_size
            ')
            ->whereIn('message_type', ['file', 'voice'])
            ->whereHas('chat', function($q) use ($admin) {
                $q->where('admin_id', $admin->id);
            })
            ->groupBy('message_type')
            ->get();

        $oldFiles = CustomerCommunicationMessage::whereIn('message_type', ['file', 'voice'])
            ->whereHas('chat', function($q) use ($admin) {
                $q->where('admin_id', $admin->id);
            })
            ->where('created_at', '<', now()->subMonths(3))
            ->selectRaw('COUNT(*) as count, SUM(file_size) as size')
            ->first();

        $userChats = CustomerCommunicationChat::where('admin_id', $admin->id)
            ->with(['employee'])
            ->withCount(['messages as file_count' => function($q) {
                $q->whereIn('message_type', ['file', 'voice']);
            }])
            ->having('file_count', '>', 0)
            ->get();

        return view('admin.customer_communication.storage', compact(
            'totalSize', 'fileCount', 'fileStats', 'oldFiles', 'userChats'
        ));
    }

    // 9- حذف الملفات القديمة (+3 شهور)
    public function clearOldFiles()
    {
        $admin = Auth::guard('admin')->user();
        $oldMessages = CustomerCommunicationMessage::whereIn('message_type', ['file', 'voice'])
            ->whereHas('chat', function($q) use ($admin) {
                $q->where('admin_id', $admin->id);
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

    // 10- تحميل جميع ملفات شاتات الادمن كـ ZIP
    public function backupFiles()
    {
        return $this->createBackup();
    }

    // 11- تحميل ملفات شات محدد
    public function backupChatFiles($chatId)
    {
        $chat = CustomerCommunicationChat::where('id', $chatId)
            ->where('admin_id', Auth::id())->first();
        if (!$chat) {
            return response()->json(['error' => 'الشات غير موجود'], 404);
        }
        return $this->createBackup($chatId);
    }

    // إنشاء النسخة الاحتياطية
    private function createBackup($specificChatId = null)
    {
        $admin = Auth::guard('admin')->user();
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
                ->with(['chat.employee', 'chat.potentialCustomer']);

            if ($specificChatId) {
                $query->where('chat_id', $specificChatId);
            } else {
                $query->whereHas('chat', function($q) use ($admin) {
                    $q->where('admin_id', $admin->id);
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
                            'employee' => $chat->employee->name ?? 'غير محدد',
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
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
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
        $admin = Auth::guard('admin')->user();
        $content = "=== نسخة احتياطية من ملفات شاتات التواصل مع العملاء ===\n\n";
        $content .= "تاريخ النسخة الاحتياطية: " . date('Y-m-d H:i:s') . "\n";
        $content .= "نوع النسخة: " . ($specificChatId ? 'شات واحد' : 'جميع الشاتات') . "\n";
        $content .= "المدير: " . $admin->name . "\n\n";
        $content .= "=== هيكل المجلدات ===\n";
        $content .= "Chat_{ID}_{TITLE}/\n";
        $content .= " ├── files/ (الملفات المرفقة)\n";
        $content .= " └── voices/ (التسجيلات الصوتية)\n\n";
        $content .= "=== معلومات الشاتات ===\n\n";

        foreach ($chatInfo as $chat) {
            $content .= "شات رقم: {$chat['chat_id']}\n";
            $content .= "العنوان: {$chat['title']}\n";
            $content .= "الموظف: {$chat['employee']}\n";
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
