<?php

namespace App\Http\Controllers\landlord\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\notificationModel;
use App\Models\landlord\landlordModel;
use App\Models\tenant\tenantModel;


class notificationslandlordController extends Controller
{
      public function notificationsLandlord($landlord_id)
    {
        $sessionLandlordId = session('landlord_id');
        $notifications = notificationModel::where('receiverID', $sessionLandlordId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            $unreadCount = notificationModel::where('receiverID', $landlord_id)
            ->where('isRead', false)
            ->count();
        if (!$sessionLandlordId) {
            return redirect()->route('loginLandlord')->with('error', 'Please log in as a landlord.');
        }
    
        if ($landlord_id !== $sessionLandlordId) {
            return redirect()->route('loginLandlord')->with('error', 'Unauthorized access.');
        }
    
        $landlord = landlordModel::find($landlord_id);
        if (!$landlord) {
            return redirect()->route('loginLandlord')->with('error', 'Landlord not found.');
        }
    
        return view('landlord.auth.notificationsLandlord',[
        "title" => "Landlord - Notifications", 
        'headerName' => 'Notifications',           
        'landlord' => $landlord,
        'landlord_id' => $landlord_id,
        'notifications' => $notifications,
        'unread_count' => $unreadCount,
    ]); 
}
public function getLandlordNotificationsList($landlord_id)
{
    $notifications = notificationModel::with('sender', 'receiver') // load both sender & receiver info
        ->where('receiverID', $landlord_id)
        ->orderBy('created_at', 'desc')
        ->where('receiverType', 'landlord') // string ra kay naka morphMap na
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
    $landlordId = $request->landlord_id;

    notificationModel::where('receiverID', $landlordId)
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
        $landlordId = $request->landlord_id;

        // delete all notifications for this landlord
        notificationModel::where('receiverID', $landlordId)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications cleared successfully.'
        ]);
    }


}
