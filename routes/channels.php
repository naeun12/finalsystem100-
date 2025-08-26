<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use App\Models\conversationModel;

Broadcast::channel('notifications.{receiverID}', function ($user, $receiverID) {
    return (string) ($user->landlordID ?? $user->tenantID) === (string) $receiverID;
}, ['guards' => ['landlord', 'tenant']]);
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = \App\Models\ConversationModel::find($conversationId);

    if (!$conversation) {
        return false;
    }

    // Both Tenant ug Landlord pwede mu-sub
    return (
        $conversation->tenant_id === $user->id ||
        $conversation->landlord_id === $user->id
    );
}, ['guards' => ['tenant', 'landlord']]);





















