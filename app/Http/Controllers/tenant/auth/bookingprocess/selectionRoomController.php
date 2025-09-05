<?php

namespace App\Http\Controllers\tenant\auth\bookingprocess;

use App\Http\Controllers\Controller;
use App\Models\landlord\roomModel;
use App\Models\landlord\dormModel;
use App\Models\tenant\reservationModel;
use App\Models\notificationModel;
use App\Mail\TenantLandlordReminder;
use App\Events\NewNotificationEvent;

use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;

class selectionRoomController extends Controller
{
    public function SelectionRoom($dormitoryID,$tenantID)
    {
                $sessionTenant_id = session('tenant_id');
         $notifications = notificationModel::where('receiverID', $sessionTenant_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            $unreadCount = notificationModel::where('receiverID', $tenantID)
            ->where('isRead', false)
            ->count();
return view('tenant.auth.bookingProcess.roomSelection', [
    'title' => 'Room Selection',
    'cssPath' => asset('css/tenantpage/auth/roomselect.css'),
    'dormitory_id' => $dormitoryID,
    'tenant_id' => $tenantID,
    'notifications' => $notifications,
             'unread_count' => $unreadCount,
]);
    }
    public function availableRooms($dormitoryID)
    {
        $rooms = roomModel::where('fkdormID',$dormitoryID)
        ->where('availability','Available')
        ->orWhere('created_at','asc')
        ->get();
        return response()->json(['rooms' => $rooms]);
    }
    public function occupiedRooms($dormitoryID)
    {
         $reservedRoomIDs = reservationModel::where('fkdormitoryID', $dormitoryID)
        ->whereIn('status', ['approved', 'paid']) 
        ->pluck('fkroomID');
       $rooms = roomModel::with('currentTenant','dorm')
        ->where('fkdormID', $dormitoryID)
        ->where(function ($query) use ($reservedRoomIDs) {
            $query->where('availability', 'Occupied')
                  ->orWhereIn('roomID', $reservedRoomIDs);
        })
        ->get();


        return response()->json(['rooms' => $rooms]);
    }
   
    public function selectedPriceRange(Request $request, $dormitoryID)
{
    $min = $request->query('min');
    $max = $request->query('max');
    $chooseStatus = $request->query('chooseStatus');

    $rooms = roomModel::where('fkdormID', $dormitoryID)
        ->where('availability', $chooseStatus)
        ->whereBetween('price', [$min, $max])
        ->get();

    return response()->json(['rooms' => $rooms]);
}
public function filterGender(Request $request, $dormitoryID)
{
    $gender = $request->query('gender');
    $chooseStatus = $request->query('chooseStatus');

    $rooms = roomModel::where('fkdormID', $dormitoryID)
        ->where('availability', $chooseStatus)
        ->where('genderPreference', $gender)
        ->get();

    return response()->json(['rooms' => $rooms]);
}
    public function viewRoomDetails($id)
    {
        $room = roomModel::with('approvedTenant','dorm')->where('roomID',$id)->first();
        return response()->json([
            'status' => 'success',
            'room' => $room,
        ]);
    }
   public function reservation(Request $request)
{
    try {
          $validatedData = $request->validate([
            'dormitory_id'      => 'required|integer|exists:dorms,dormID',
            'room_id'           => 'required|integer|exists:rooms,roomID',
            'tenant_id'         => 'required|string|exists:tenants,tenantID',
            'firstname'         => 'required|string|max:255',
            'lastname'          => 'required|string|max:255',
            'contact_number'    => 'required|string|max:20',
            'email'             => 'required|email|max:255',
            'age'               => 'required|integer|min:15|max:60',
            'gender'            => 'required|in:Male,Female',
            'studentpicture_id' => 'required|string',
        ]);
        // 1. Get the room with landlord and tenant
        $room = roomModel::with('landlord','currentTenant')->find($request->room_id);

        if (!$room) {
            return response()->json([
                'status' => 'error',
                'message' => 'Room not found.'
            ], 404);
        }

        $landlord = $room->landlord;
        $currentTenant = $room->currentTenant;

        // 2. Check tenant extension decision
       if ($currentTenant && $currentTenant->extension_decision === 'extending') {
        return response()->json([
            'status' => 'error',
            'message' => 'The current tenant has decided to extend their stay. Reservation is not allowed at the moment.',
        ]);
    } elseif ($currentTenant && $currentTenant->extension_decision === 'pending') {
        return response()->json([
            'status' => 'error',
             'message' => 'The current tenant’s extension request is still pending. Reservation is not allowed at the moment. Please message the landlord for request.'
        ]);
    }


        // 3. Validate input
      

        // 4. Save reservation
        $reservation = reservationModel::create([
            'fkdormitoryID' => $request->dormitory_id,
            'fkroomID'      => $request->room_id,
            'fktenantID'    => $request->tenant_id,
            'firstname'     => $request->firstname,
            'lastname'      => $request->lastname,
            'contactNumber' => $request->contact_number,
            'contactEmail'  => $request->email,
            'age'           => $request->age,
            'gender'        => $request->gender,
            'pictureID'     => $request->studentpicture_id,
            'status'        => 'pending',
        ]);

        // 5. Send email to tenant (optional wrap in try/catch to avoid breaking flow if mail fails)
        try {
            $tenantName = $request->firstname . ' ' . $request->lastname;
            $tenantMessage = "Hi $tenantName, your room reservation has been submitted successfully and is currently pending confirmation.";
            Mail::to($request->email)->send(new TenantLandlordReminder($tenantName, $tenantMessage, 'tenant'));
        } catch (\Exception $mailException) {
            \Log::warning('Failed to send reservation email: ' . $mailException->getMessage());
        }

        // 6. Create landlord notification
        $notifications = notificationModel::create([
            'senderID'     => $request->tenant_id,
            'senderType'   => 'tenant',
            'receiverID'   => $landlord->landlordID ?? null,
            'receiverType' => 'landlord',
            'title'        => 'New Reservation Request',
            'message'      => "A tenant has reserved Room #{$room->roomNumber}. Please review the request.",
            'isRead'       => false,
            'readAt'       => null,
        ]);

        broadcast(new \App\Events\NewNotificationEvent($notifications));

        // 7. Success response
        return response()->json([
            'status'  => 'success',
            'message' => 'Room reserved successfully. Waiting for landlord confirmation.',
            'data'    => $reservation
        ]);

    } catch (\Exception $e) {
        \Log::error('Reservation failed: ' . $e->getMessage());
        return response()->json([
            'status'  => 'error',
            'message' => 'Reservation failed.',
            'error'   => $e->getMessage()
        ], 500);
    }
}

}
