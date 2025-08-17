<?php
namespace App\Http\Controllers\Employee;

use App\Models\WorkChat;
use App\Models\WorkChatMessage;
use App\Models\PotentialCustomer;
use Illuminate\Http\Request;

class EmployeeCustomerCommunicationChatController extends Controller
{
    public function index()
    {
        $employeeId = auth('employee')->id();
        $chats = WorkChat::where('employee_id', $employeeId)
            ->where('type', 'customer_communication')
            ->with('potentialCustomer')
            ->get();
        return view('employee.customer_communication.index', compact('chats'));
    }

    public function show($customerId)
    {
        $employeeId = auth('employee')->id();
        $chat = WorkChat::firstOrCreate([
            'employee_id' => $employeeId,
            'admin_id'    => 1, // استبدله بمعرف الأدمن الفعلي
            'potential_customer_id' => $customerId,
            'type'        => 'customer_communication'
        ]);
        $messages = $chat->messages()->orderBy('created_at')->get();
        // علم الرسائل كمقروءة للموظف
        $chat->messages()->where('sender_type', 'admin')->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        return view('employee.customer_communication.show', compact('chat', 'messages'));
    }

    public function sendMessage(Request $request, $customerId)
    {
        $employeeId = auth('employee')->id();
        $chat = WorkChat::where('employee_id', $employeeId)
            ->where('potential_customer_id', $customerId)->firstOrFail();

        $request->validate([
            'message_type' => 'required|in:text,file,voice',
            'content' => 'required_if:message_type,text',
            'file' => 'required_if:message_type,file|file|max:10240',
            'voice' => 'required_if:message_type,voice'
        ]);

        // اعتماد نفس منطق WorkChatMessage الحالي
        if ($request->message_type === 'text') {
            WorkChatMessage::createTextMessage($chat->id, 'employee', $employeeId, $request->content);
        } elseif ($request->message_type === 'file') {
            $file = $request->file('file');
            $fileName = time() . '_' . \Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('work-chat/files', $fileName, 'public');
            WorkChatMessage::createFileMessage($chat->id, 'employee', $employeeId, [
                'path' => 'files/' . $fileName,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
            ]);
        } elseif ($request->message_type === 'voice') {
            // صوت بنفس صيغة WorkChat الحالي
        }

        return redirect()->route('employee.customer-communication.show', $customerId)
            ->with('success', 'تم إرسال الرسالة');
    }

    public function getMessages($customerId)
    {
        $employeeId = auth('employee')->id();
        $chat = WorkChat::where('employee_id', $employeeId)
            ->where('potential_customer_id', $customerId)->firstOrFail();
        $messages = $chat->messages()->with('sender')->orderBy('created_at', 'desc')->limit(50)->get()->reverse()->values();
        // علم الرسائل كمقروءة للموظف
        $chat->messages()->where('sender_type', 'admin')->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['messages' => $messages]);
    }
}
