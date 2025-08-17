<?php

namespace App\Http\Controllers\Employee; // بدل Admin للموظف لو Employee

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PotentialCustomer;
use App\Models\WorkChat;
use App\Models\WorkChatMessage;
use App\Models\Admin; // أو Employee حسب الطرف
use Auth;

class PotentialCustomersChatController extends Controller
{
    // عرض العملاء المنتظرين المكالمات/الزيارات
    public function index()
    {
        $callsPending = PotentialCustomer::whereRaw('JSON_CONTAINS(classifications, \'["requested_call"]\')')->get();
        $visitsPending = PotentialCustomer::whereRaw('JSON_CONTAINS(classifications, \'["requested_visit"]\')')->get();

        // جلب الشاتات الحالية لهذا المسؤول
        $userId = Auth::id();
        $userType = Auth::guard('admin')->check() ? 'admin' : 'employee';

        $chats = WorkChat::where($userType . '_id', $userId)
            ->where('type', 'potential_customer')
            ->with(['potentialCustomer', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->get();

        return view($userType . '.potential_customers_chat.index', compact('callsPending', 'visitsPending', 'chats'));
    }

    // صفحة الشات مع عميل محتمل
    public function show($id)
    {
        $potentialCustomer = PotentialCustomer::findOrFail($id);
        $userId = Auth::id();
        $userType = Auth::guard('admin')->check() ? 'admin' : 'employee';

        // جلب أو إنشاء شات
        $chat = WorkChat::firstOrCreate([
            'type' => 'potential_customer',
            $userType . '_id' => $userId,
            'potential_customer_id' => $potentialCustomer->id,
        ], [
            'title' => $potentialCustomer->name,
            'status' => 'active'
        ]);
        $messages = $chat->messages()->with('sender')->orderBy('created_at', 'asc')->get();
        return view($userType . '.potential_customers_chat.show', compact('chat', 'messages', 'potentialCustomer'));
    }

    // إرسال رسالة
    public function sendMessage(Request $request, $chatId)
    {
        $userType = Auth::guard('admin')->check() ? 'admin' : 'employee';
        $userId = Auth::id();
        $chat = WorkChat::findOrFail($chatId);

        if($chat->{$userType . '_id'} !== $userId) {
            return response()->json(['error' => 'غير مسموح'], 403);
        }

        $request->validate([
            'message_type' => 'required|in:text,file,voice',
            'content' => 'required_if:message_type,text',
            'file' => 'required_if:message_type,file|file|max:10240',
            'voice' => 'required_if:message_type,voice'
        ]);

        $message = null;
        if($request->message_type === 'text') {
            $message = WorkChatMessage::createTextMessage(
                $chat->id, $userType, $userId, $request->content
            );
        } elseif($request->message_type === 'file') {
            $file = $request->file('file');
            $fileName = time() . '_' . str_random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('work-chat/files', $fileName, 'public');
            $message = WorkChatMessage::createFileMessage(
                $chat->id, $userType, $userId,
                [
                    'path' => 'files/' . $fileName,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ]
            );
        } elseif($request->message_type === 'voice') {
            $voiceData = $request->voice;
            if(is_string($voiceData) && str_starts_with($voiceData, 'data:audio')) {
                $audio = base64_decode(explode(',', $voiceData)[1]);
                $fileName = time() . '_' . str_random(10) . '.webm';
                \Storage::disk('public')->put('work-chat/voice/' . $fileName, $audio);
                $duration = is_numeric($request->duration) ? (int)$request->duration : 0;
                if($duration > 1000) $duration = round($duration / 1000);
                if($duration < 1) $duration = 1;
                if($duration > 600) $duration = 600;
                $message = WorkChatMessage::createVoiceMessage(
                    $chat->id, $userType, $userId,
                    [
                        'path' => 'voice/' . $fileName,
                        'name' => $fileName,
                        'size' => strlen($audio),
                        'duration' => $duration
                    ]
                );
            }
        }
        return response()->json(['success' => true, 'message' => $message]);
    }

    // جلب الرسائل
    public function getMessages($chatId)
    {
        $userType = Auth::guard('admin')->check() ? 'admin' : 'employee';
        $userId = Auth::id();
        $chat = WorkChat::findOrFail($chatId);
        if($chat->{$userType . '_id'} !== $userId){
            return response()->json(['error'=>'غير مسموح'],403);
        }
        $messages = $chat->messages()->with('sender')->orderBy('created_at', 'desc')->limit(50)->get()->reverse()->values();
        $chat->messages()->where('sender_type','!=', $userType)->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['messages' => $messages->map(function($message){
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
                'is_read' => $message->is_read,
                'duration' => $message->voice_duration['total_seconds'] ?? null,
                'duration_formatted' => $message->voice_duration['formatted'] ?? null,
            ];
        })]);
    }

    // حذف الشات
    public function destroy($chatId)
    {
        $userType = Auth::guard('admin')->check() ? 'admin' : 'employee';
        $userId = Auth::id();
        $chat = WorkChat::findOrFail($chatId);

        if ($chat->{$userType . '_id'} !== $userId) {
            abort(403);
        }

        $chat->messages()->each(function($message){
            $message->deleteFile();
        });
        $chat->delete();

        return redirect()->route($userType . '.potential-customers-chat.index')
            ->with('success', 'تم حذف الشات بنجاح');
    }

    // واجهة الاستوريدج مثل work-chat.storage
    public function storage()
    {
        $userType = Auth::guard('admin')->check() ? 'admin' : 'employee';
        $userId = Auth::id();

        $totalSize = WorkChatMessage::whereHas('chat', function($q) use ($userType, $userId) {
            $q->where($userType . '_id', $userId)
              ->where('type', 'potential_customer');
        })->whereNotNull('file_size')->sum('file_size');

        $fileCount = WorkChatMessage::whereHas('chat', function($q) use ($userType, $userId) {
            $q->where($userType . '_id', $userId)
              ->where('type', 'potential_customer');
        })->whereIn('message_type', ['file', 'voice'])->count();

        $oldFiles = WorkChatMessage::whereHas('chat', function($q) use ($userType, $userId) {
            $q->where($userType . '_id', $userId)
              ->where('type', 'potential_customer');
        })->whereIn('message_type', ['file', 'voice'])->where('created_at', '<', now()->subMonths(3))->get();

        $userChats = WorkChat::where($userType . '_id', $userId)->where('type', 'potential_customer')
            ->with(['potentialCustomer'])
            ->withCount(['messages as file_count'=>function($q){
                $q->whereIn('message_type',['file','voice']);
            }])
            ->having('file_count', '>', 0)
            ->get();

        return view($userType.'.potential_customers_chat.storage', compact('totalSize','fileCount','oldFiles','userChats'));
    }
}
