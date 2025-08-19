<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use App\Models\conversationModel;

Broadcast::channel('notifications.{receiverID}', function ($user, $receiverID) {
    return (string) ($user->landlordID ?? $user->tenantID) === (string) $receiverID;
}, ['guards' => ['landlord', 'tenant']]);
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = \App\Models\conversationModel::with('messages')->find($conversationId);

    if (!$conversation) {
        return false;
    }

    // Get the user ID (tenant or landlord)
    $userId = $user->tenantID ?? $user->landlordID ?? null;

    if (!$userId) {
        return false;
    }

    // Check if user is the initiator of the conversation
    if ($conversation->initiatorID === $userId) {
        return true;
    }

    // Check if the user is involved in any message (as receiver or sender)
    return $conversation->messages->contains(function ($message) use ($userId) {
        return $message->receiverID === $userId || $message->senderID === $userId;
    });
}, ['guards' => ['tenant', 'landlord']]);





















