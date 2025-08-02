<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

Broadcast::channel('notifications.{receiverID}', function ($user, $receiverID) {
    return (string) ($user->landlordID ?? $user->tenantID) === (string) $receiverID;
}, ['guards' => ['landlord', 'tenant']]);









