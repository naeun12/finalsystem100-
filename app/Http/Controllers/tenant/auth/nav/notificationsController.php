<?php

namespace App\Http\Controllers\tenant\auth\nav;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\notificationModel;
use App\Models\tenant\tenantModel;
use App\Models\landlord\landlordModel;

class notificationsController extends Controller
{
       public function notificationstenant($tenant_id)
    {
        $sessiontenantID = session('tenant_id');
        $notifications = notificationModel::where('receiverID', $sessiontenantID)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            $unreadCount = notificationModel::where('receiverID', $tenant_id)
            ->where('isRead', false)
            ->count();
        $tenant = tenantModel::find($tenant_id);
        return view('tenant.auth.nav.notificationstenant',[
        "title" => "Tenant - Notifications", 
        "cssPath" => "",
        'headerName' => 'Notifications',           
        'tenant' => $tenant,
        'tenant_id' => $tenant_id,
        'notifications' => $notifications,
        'unread_count' => $unreadCount,
    ]); 
}
public function gettenantotificationsList($tenant_id)
{
    $notifications = notificationModel::with('sender', 'receiver') // load both sender & receiver info
        ->where('receiverID', $tenant_id)
        ->orderBy('created_at', 'desc')
        ->where('receiverType', 'tenant') // string ra kay naka morphMap na
        ->get();

    return response()->json([
        'status' => 'success',
        'notifications' => $notifications
    ]);
}
public function markAsRead($notificationId)
{
    $notification = notificationModel::findOrFail($notificationId);

    // Update read status ug time
    $notification->update([
        'isRead' => true,
        'readAt' => now(),
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Notification marked as read',
        'notification' => $notification
    ]);
}

public function markAllAsRead(Request $request)
{
    $tenantId = $request->tenant_id;

    notificationModel::where('receiverID', $tenantId)
        ->where('isRead', false) // optional: aron dili mag-update og mga nabasa na
        ->update([
            'isRead' => true,
            'readAt' => now(),
        ]);

    return response()->json([
        'status' => 'success',
        'message' => 'All notifications marked as read.'
    ]);
}
public function clearAll(Request $request)
    {
        $tenantId = $request->tenant_id;

        // delete all notifications for this tenant
        notificationModel::where('receiverID', $tenantId)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications cleared successfully.'
        ]);
    }



}
