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
    
        $rooms = roomModel::with('latestApprovedTenant')
        ->where('fkdormID', $dormitoryID)
        ->where('availability', 'Occupied')
        ->where('isReservable', 1)
        ->get()
        ->filter(function($room) {
            if($room->latestApprovedTenant && $room->latestApprovedTenant->status === 'pending'){
                return false; 
            }
            return true; // show only rooms occupied but not reserved
        })
        ->values();
       
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
            $room = roomModel::with(['latestApprovedTenant','dorm'])
                 ->where('roomID',$id)
                 ->first();        
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

        // 1. Get the room with landlord and all tenants
        $room = roomModel::with(['landlord', 'currentTenant'])->find($request->room_id);

        if (!$room) {
            return response()->json([
                'status' => 'error',
                'message' => 'Room not found.'
            ], 404);
        }

        $landlord = $room->landlord;

        // 2. Check ALL tenants’ extension decision
        $tenants = $room->currentTenant()->where('status', 'active')->get();

        foreach ($tenants as $tenant) {
            if ($tenant->extension_decision === 'extend') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'One of the tenants has decided to extend their stay. Reservation is not allowed at the moment.',
                ]);
            }

            if ($tenant->extension_decision === 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'A tenant’s extension request is still pending. Reservation is not allowed at the moment. Please message the landlord for request.'
                ]);
            }
        }

        // 3. If no blocking tenants → create reservation
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

        // 4. Send email to tenant
        try {
            $tenantName = $request->firstname . ' ' . $request->lastname;
            $tenantMessage = "Hi $tenantName, your room reservation has been submitted successfully and is currently pending confirmation.";
            Mail::to($request->email)->send(new TenantLandlordReminder($tenantName, $tenantMessage, 'tenant'));
        } catch (\Exception $mailException) {
            \Log::warning('Failed to send reservation email: ' . $mailException->getMessage());
        }

        // 5. Create landlord notification
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
