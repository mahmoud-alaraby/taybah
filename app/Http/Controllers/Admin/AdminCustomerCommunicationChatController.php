<?php
namespace App\Http\Controllers\Admin;

use App\Models\WorkChat;
use App\Models\WorkChatMessage;
use Illuminate\Http\Request;

class AdminCustomerCommunicationChatController extends Controller
{
    public function index()
    {
        $adminId = auth('admin')->id();
        $chats = WorkChat::where('admin_id', $adminId)
            ->where('type', 'customer_communication')
            ->with('potentialCustomer', 'employee')
            ->get();
        return view('admin.customer_communication.index', compact('chats'));
    }

    public function show($customerId)
    {
        $adminId = auth('admin')->id();
        $chat = WorkChat::firstOrCreate([
            'admin_id' => $adminId,
            'potential_customer_id' => $customerId,
            'type' => 'customer_communication'
        ]);
        $messages = $chat->messages()->orderBy('created_at')->get();
        $chat->messages()->where('sender_type', 'employee')->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        return view('admin.customer_communication.show', compact('chat', 'messages'));
    }

    public function sendMessage(Request $request, $customerId)
    {
        $adminId = auth('admin')->id();
        $chat = WorkChat::where('admin_id', $adminId)
            ->where('potential_customer_id', $customerId)->firstOrFail();

        $request->validate([
            'message_type' => 'required|in:text,file,voice',
            'content' => 'required_if:message_type,text',
            'file' => 'required_if:message_type,file|file|max:10240',
            'voice' => 'required_if:message_type,voice'
        ]);

        if ($request->message_type === 'text') {
            WorkChatMessage::createTextMessage($chat->id, 'admin', $adminId, $request->content);
        } elseif ($request->message_type === 'file') {
            $file = $request->file('file');
            $fileName = time() . '_' . \Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('work-chat/files', $fileName, 'public');
            WorkChatMessage::createFileMessage($chat->id, 'admin', $adminId, [
                'path' => 'files/' . $fileName,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
            ]);
        } elseif ($request->message_type === 'voice') {
            // نفس منطق WorkChatMessage للصوت
        }

        return redirect()->route('admin.customer-communication.show', $customerId)
            ->with('success', 'تم إرسال الرسالة');
    }

    public function getMessages($customerId)
    {
        $adminId = auth('admin')->id();
        $chat = WorkChat::where('admin_id', $adminId)
            ->where('potential_customer_id', $customerId)->firstOrFail();
        $messages = $chat->messages()->with('sender')->orderBy('created_at', 'desc')->limit(50)->get()->reverse()->values();
        $chat->messages()->where('sender_type', 'employee')->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['messages' => $messages]);
    }
}
