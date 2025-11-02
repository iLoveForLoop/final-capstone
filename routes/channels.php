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



//FOR NOTIFICATIONS
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Add this channel for vendor notifications
Broadcast::channel('vendor.{vendorId}', function ($user, $vendorId) {
    // Check if the authenticated user owns the vendor account
    return $user->vendor && (int) $user->vendor->id === (int) $vendorId;
});

Broadcast::channel('client.{clientId}', function ($user, $clientId) {
    // Check if the authenticated user owns the client account
    return $user->client && (int) $user->client->id === (int) $clientId;
});