<?php

use Illuminate\Support\Facades\Broadcast;
use Musonza\Chat\Models\Conversation;

/*
|--------------------------------------------------------------------------
| Broadcast Channels (real-time chat when CHAT_REALTIME_ENABLED=true)
|--------------------------------------------------------------------------
|
| Only participants of a conversation may subscribe to mc-chat-conversation.{id}.
| Supports both admin and employee guards.
|
*/

Broadcast::channel('mc-chat-conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    if (! $conversation) {
        return false;
    }
    $participants = $conversation->getParticipants();

    return $participants->contains(fn ($p) => $p->getKey() === $user->getKey()
        && $p->getMorphClass() === $user->getMorphClass());
}, ['guards' => ['admin', 'employee']]);
