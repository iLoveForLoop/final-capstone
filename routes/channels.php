<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;

// Channel for specific conversations
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    return $conversation && $conversation->hasParticipant($user->id) ? $user : null;
});

// Channel for user-specific notifications
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId ? $user : null;
});

Broadcast::channel('users.{id}', fn ($user, $id) => (int) $user->id === (int) $id);

// Channel for online presence (optional)
Broadcast::channel('messaging', function ($user) {
    return ['id' => $user->id, 'name' => $user->name];
});
