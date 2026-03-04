<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Musonza\Chat\Facades\ChatFacade as Chat;

/**
 * Shared conversation controller for both admins and employees.
 * One conversation system for all users (Musonza Chat).
 */
class ConversationController extends Controller
{
    /**
     * Display conversations (Messenger-style: sidebar + empty state or chat).
     */
    public function index(Request $request)
    {
        $user = auth('admin')->user() ?? auth('employee')->user();
        $conversations = $user->conversations();
        foreach ($conversations as $conv) {
            $conv->load('last_message', 'participants.messageable');
        }
        $conversationList = $this->buildConversationList($conversations, $user);
        $usersForNewChat = $this->getOtherMessageableUsers($user);

        $layout = auth('admin')->check() ? 'admin.layouts.app' : 'employee.layouts.app';
        $storeRoute = auth('admin')->check() ? 'admin.conversations.store' : 'employee.conversations.store';
        $showRoute = auth('admin')->check() ? 'admin.conversations.show' : 'employee.conversations.show';
        $destroyRoute = auth('admin')->check() ? 'admin.conversations.destroy' : 'employee.conversations.destroy';

        return view('conversations.messenger', [
            'layout'             => $layout,
            'conversationList'   => $conversationList,
            'usersForNewChat'    => $usersForNewChat,
            'storeRoute'         => $storeRoute,
            'showRoute'          => $showRoute,
            'destroyRoute'       => $destroyRoute,
            'selectedConversation' => null,
            'conversation'       => null,
            'messages'           => null,
            'currentUser'        => $user,
            'otherNames'         => '',
            'sendRoute'          => auth('admin')->check() ? 'admin.conversations.send' : 'employee.conversations.send',
        ]);
    }

    /**
     * Create a new conversation with the selected user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'participant' => ['required', 'string', 'regex:#^(admin|employee):\d+$#'],
        ]);

        [$type, $id] = explode(':', $request->participant, 2);

        $currentUser = auth('admin')->user() ?? auth('employee')->user();
        $otherUser = $this->findMessageableUser($type, (int) $id);

        if (!$otherUser) {
            return back()->with('error', 'المستخدم المحدد غير موجود.');
        }

        if ($otherUser->getKey() === $currentUser->getKey() && get_class($otherUser) === get_class($currentUser)) {
            return back()->with('error', 'لا يمكن بدء محادثة مع نفسك.');
        }

        $existing = $this->getExistingConversationBetween($currentUser, $otherUser);
        if ($existing) {
            return $this->redirectToConversation($existing->id);
        }

        $conversation = Chat::makeDirect()
            ->createConversation([$currentUser, $otherUser], []);

        return $this->redirectToConversation($conversation->id);
    }

    /**
     * Show a single conversation (Messenger-style: sidebar + chat).
     */
    public function show(int $id)
    {
        $currentUser = auth('admin')->user() ?? auth('employee')->user();
        $conversation = Chat::conversations()->getById($id);

        if (!$conversation) {
            return $this->conversationNotFound();
        }

        $participants = $conversation->getParticipants();
        $isParticipant = $participants->contains(fn ($p) => $p->getKey() === $currentUser->getKey() && $p->getMorphClass() === $currentUser->getMorphClass());
        if (!$isParticipant) {
            abort(403, 'غير مصرح لك بعرض هذه المحادثة.');
        }

        $messages = Chat::conversation($conversation)
            ->setParticipant($currentUser)
            ->getMessages();

        if (method_exists($messages, 'getCollection')) {
            $messages->getCollection()->load('participation.messageable');
        }

        Chat::conversation($conversation)->setParticipant($currentUser)->readAll();

        $otherParticipants = $participants->filter(fn ($p) => $p->getKey() !== $currentUser->getKey() || $p->getMorphClass() !== $currentUser->getMorphClass());
        $otherNames = $otherParticipants->map(fn ($p) => $p->name ?? 'مستخدم')->implode(', ') ?: 'محادثة';

        $conversations = $currentUser->conversations();
        foreach ($conversations as $conv) {
            $conv->load('last_message', 'participants.messageable');
        }
        $conversationList = $this->buildConversationList($conversations, $currentUser);
        $usersForNewChat = $this->getOtherMessageableUsers($currentUser);

        $layout = auth('admin')->check() ? 'admin.layouts.app' : 'employee.layouts.app';
        $storeRoute = auth('admin')->check() ? 'admin.conversations.store' : 'employee.conversations.store';
        $showRoute = auth('admin')->check() ? 'admin.conversations.show' : 'employee.conversations.show';
        $sendRoute = auth('admin')->check() ? 'admin.conversations.send' : 'employee.conversations.send';
        $destroyRoute = auth('admin')->check() ? 'admin.conversations.destroy' : 'employee.conversations.destroy';

        return view('conversations.messenger', [
            'layout'              => $layout,
            'conversationList'    => $conversationList,
            'usersForNewChat'     => $usersForNewChat,
            'storeRoute'          => $storeRoute,
            'showRoute'           => $showRoute,
            'sendRoute'           => $sendRoute,
            'destroyRoute'        => $destroyRoute,
            'selectedConversation' => $conversation,
            'conversation'        => $conversation,
            'messages'            => $messages,
            'currentUser'         => $currentUser,
            'otherNames'          => $otherNames,
        ]);
    }

    /**
     * Send a message in a conversation (text and/or file attachments).
     */
    public function sendMessage(Request $request, int $id)
    {
        $request->validate([
            'body'        => 'nullable|string|max:5000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpeg,jpg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,zip',
        ], [
            'attachments.max'    => 'الحد الأقصى 5 ملفات في الرسالة الواحدة.',
            'attachments.*.max'  => 'حجم كل ملف يجب ألا يتجاوز 10 ميجابايت.',
            'attachments.*.mimes'=> 'نوع الملف غير مسموح. المسموح: صور، PDF، Word، Excel، نص، ZIP.',
        ]);

        $body = trim($request->input('body', ''));
        $attachmentsData = [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if (!$file->isValid()) {
                    continue;
                }
                $path = $file->store('chat-attachments', 'public');
                $attachmentsData[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }

        if ($body === '' && empty($attachmentsData)) {
            return back()->with('error', 'أضف نصاً أو مرفقاً واحداً على الأقل.');
        }

        $currentUser = auth('admin')->user() ?? auth('employee')->user();
        $conversation = Chat::conversations()->getById($id);

        if (!$conversation) {
            return $this->conversationNotFound();
        }

        $participants = $conversation->getParticipants();
        $isParticipant = $participants->contains(fn ($p) => $p->getKey() === $currentUser->getKey() && $p->getMorphClass() === $currentUser->getMorphClass());
        if (!$isParticipant) {
            abort(403, 'غير مصرح لك بالإرسال في هذه المحادثة.');
        }

        $messageBody = $body !== '' ? $body : '📎 مرفقات';
        $payload = Chat::message($messageBody)->from($currentUser)->to($conversation);
        if (!empty($attachmentsData)) {
            $payload->data(['attachments' => $attachmentsData]);
        }
        $payload->send();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'تم إرسال الرسالة.');
    }

    /**
     * Leave/delete conversation for the current user (removes from their list).
     */
    public function destroy(int $id)
    {
        $currentUser = auth('admin')->user() ?? auth('employee')->user();
        $conversation = Chat::conversations()->getById($id);

        if (!$conversation) {
            return $this->conversationNotFound();
        }

        $participants = $conversation->getParticipants();
        $isParticipant = $participants->contains(fn ($p) => $p->getKey() === $currentUser->getKey() && $p->getMorphClass() === $currentUser->getMorphClass());
        if (!$isParticipant) {
            abort(403, 'غير مصرح لك بحذف هذه المحادثة.');
        }

        $currentUser->leaveConversation($id);

        if (auth('admin')->check()) {
            return redirect()->route('admin.conversations.index')->with('success', 'تم حذف المحادثة من قائمتك.');
        }
        return redirect()->route('employee.conversations.index')->with('success', 'تم حذف المحادثة من قائمتك.');
    }

    private function conversationNotFound()
    {
        if (auth('admin')->check()) {
            return redirect()->route('admin.conversations.index')->with('error', 'المحادثة غير موجودة.');
        }
        return redirect()->route('employee.conversations.index')->with('error', 'المحادثة غير موجودة.');
    }

    /**
     * Get all other users (admins + employees) that can be messaged, excluding current user.
     *
     * @return Collection<int, array{value: string, label: string}>
     */
    private function getOtherMessageableUsers($currentUser): Collection
    {
        $list = collect();

        $admins = Admin::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        foreach ($admins as $admin) {
            if ($currentUser instanceof Admin && $currentUser->id === $admin->id) {
                continue;
            }
            $list->push(['value' => 'admin:' . $admin->id, 'label' => $admin->name . ' (مدير)']);
        }

        $employees = Employee::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        foreach ($employees as $employee) {
            if ($currentUser instanceof Employee && $currentUser->id === $employee->id) {
                continue;
            }
            $list->push(['value' => 'employee:' . $employee->id, 'label' => $employee->name . ' (موظف)']);
        }

        return $list;
    }

    private function findMessageableUser(string $type, int $id)
    {
        if ($type === 'admin') {
            return Admin::find($id);
        }
        if ($type === 'employee') {
            return Employee::find($id);
        }
        return null;
    }

    private function getExistingConversationBetween($userOne, $userTwo)
    {
        return Chat::conversations()->between($userOne, $userTwo);
    }

    /**
     * Build sidebar list: id, otherNames, lastMessage, unreadCount for each conversation.
     *
     * @return array<int, array{id: int, otherNames: string, lastMessage: string|null, unreadCount: int}>
     */
    private function buildConversationList($conversations, $currentUser): array
    {
        $list = [];
        foreach ($conversations as $conv) {
            $others = $conv->getParticipants()->filter(
                fn ($p) => $p->getKey() !== $currentUser->getKey() || $p->getMorphClass() !== $currentUser->getMorphClass()
            );
            $otherNames = $others->map(fn ($p) => $p->name ?? 'مستخدم')->implode(', ');
            $lastMsg = $conv->last_message;
            $unreadCount = $conv->unReadNotifications($currentUser)->count();
            $list[] = [
                'id'           => $conv->id,
                'otherNames'   => $otherNames ?: 'محادثة #' . $conv->id,
                'lastMessage'  => $lastMsg ? Str::limit($lastMsg->body, 40) : null,
                'unreadCount'  => $unreadCount,
            ];
        }
        return $list;
    }

    private function redirectToConversation(int $conversationId)
    {
        if (auth('admin')->check()) {
            return redirect()->route('admin.conversations.show', $conversationId)->with('success', 'تم إنشاء المحادثة.');
        }
        return redirect()->route('employee.conversations.show', $conversationId)->with('success', 'تم إنشاء المحادثة.');
    }
}
