<?php

namespace App\Http\Controllers\landlord\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordModel;
use App\Models\landlord\bookingModel;
use App\Models\landlord\roomModel;
use App\Models\landlord\dormModel;
use App\Models\tenant\bookingpaymentModel;
use App\Models\notificationModel;
use App\Mail\TenantLandlordReminder;
use Illuminate\Support\Facades\Mail;

use App\Models\tenant\approvetenantsModel;

class bookingpageController extends Controller
{
    public function bookingpageIndex($landlord_id)
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
    
        return view('landlord.auth.bookingpage',[
        "title" => "Landlord - Tenants", 
        'headerName' => 'Tenants Booking',           
        'landlord' => $landlord,
        'landlord_id' => $landlord_id,
        'notifications' => $notifications,
        'unread_count' => $unreadCount,
    ]);   
    }
    public function searchBooking(Request $request)
{
    $landlordId = session('landlord_id');

    if (!$landlordId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized action. Please log in as a landlord.'
        ], 403);
    }

    $searchTerm = $request->input('search');

    $query = bookingModel::with(['room.dorm', 'room.landlord', 'tenant'])
        ->whereHas('room', function ($q) use ($landlordId) {
            $q->where('fklandlordID', $landlordId);
        });

    if ($searchTerm) {
        $query->where(function ($q) use ($searchTerm) {
            $q->where('firstname', 'like', '%' . $searchTerm . '%')
              ->orWhere('lastname', 'like', '%' . $searchTerm . '%')
              ->orWhere('contactEmail', 'like', '%' . $searchTerm . '%');
        });
    }

    $bookings = $query->orderBy('updated_at', 'asc')->paginate(5);

    return response()->json([
        'status' => 'success',
        'booking' => $bookings
    ]);
}

    public function bookingList()
    {
        $landlordId = session('landlord_id');
        if (!$landlordId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action. Please log in as a landlord.'
            ], 403);
        }
        $booking = bookingModel::with(['room.dorm', 'room.landlord', 'tenant','payment'])
        ->whereHas('room', function ($query) use ($landlordId) {
            $query->where('fklandlordID', $landlordId);
        })
        ->orderBy('updated_at', 'desc')
        ->paginate(6);
    
        return response()->json([
            'status' => 'success',
            'booking' => $booking,
            'landlord_id' => $landlordId
        ]);
    }
    public function getDormName()
{
    $dorms = dormModel::all();

    return response()->json($dorms); 
}
public function getTenantsByDorm($dormId)
{
    $tenants = bookingModel::whereHas('room.dorm', function ($query) use ($dormId) {
        $query->where('dormID', $dormId);
    })->with(['room.dorm'])
      ->paginate(6);

    return response()->json($tenants);
}
public function getRooms(Request $request)
{
    $landlordId = session('landlord_id');
    $searchTerm = $request->input('roomsNumber');

    $query = bookingModel::with(['room.dorm', 'room.landlord', 'tenant','payment'])
        ->whereHas('room', function ($q) use ($landlordId) {
            $q->where('fklandlordID', $landlordId);
        });

        if ($searchTerm) {
            $query->whereHas('room', function ($q) use ($searchTerm) {
                $q->where('roomNumber', '=', $searchTerm); // use exact match
            });
        }
        

    return response()->json($query->paginate(5));
}


public function getApplications(Request $request)
{
    $landlordId = session('landlord_id');
    $searchStatus = $request->input('selectedapplicationStatus');

    $query = bookingModel::with(['room.dorm', 'room.landlord', 'tenant',
    'payment'])
        ->whereHas('room', function ($q) use ($landlordId) {
            $q->where('fklandlordID', $landlordId);
        });

    if ($searchStatus && strtolower($searchStatus) !== 'all') {
    $query->where('status', '=', strtolower($searchStatus));
}


    return response()->json($query->paginate(5));
}
    public function ViewTenant($id)
{
    $landlordId = session('landlord_id');
    if (!$landlordId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized action. Please log in as a landlord.'
        ], 403);
    }

    $tenant = bookingModel::with([
        'room.dorm','payment'
    ])
    ->where('bookingID', $id)
    ->whereHas('room', function ($query) use ($landlordId) {
        $query->where('fklandlordID', $landlordId);
    })
    ->first();

    if (!$tenant) {
        return response()->json([
            'status' => 'error',
            'message' => 'Tenant screening record not found.'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'tenant' => $tenant
    ]);
}

public function handletenantBooking(Request $request)
{
    $request->validate([
        'bookingID' => 'required|integer|exists:bookings,bookingID',
        'status' => 'required|string|in:approved,rejected,confirmed,paid,pending,cancelled'
    ]);
    try {
        $booking = bookingModel::findOrFail($request->bookingID);
        $room = roomModel::findOrFail($booking->fkroomID);
         if ($request->status === 'approved' && $room->availability === 'Occupied') {
            return response()->json([
                'status' => 'error',
                'message' => 'This room is already occupied. You cannot approve another tenant.',
            ], 403);
        }
         if($request->status === 'rejected')
        {
              $tenantName = $booking->firstname . ' ' . $booking->lastname;
            $message = "Hi! 👋 We regret to inform you that your Booking for Room {$room->roomNumber} at {$room->dorm} has been declined by the landlord. If you have any questions or wish to explore other available rooms, feel free to contact us. Thank you!";
            $type = 'tenant';
                     Mail::to($booking->contactEmail)->send(new TenantLandlordReminder($tenantName, $message, $type));

        }
        if($request->status === 'confirmed') {
            $tenantName = $booking->firstname . ' ' . $booking->lastname;
$message = "Hi {$tenantName}, 👋 Your booking for Room {$room->roomNumber} at {$room->dorm} has been *approved*. Please proceed with the payment to confirm your reservation.";
            $type = 'tenant';
                     Mail::to($booking->contactEmail)->send(new TenantLandlordReminder($tenantName, $message, $type));

        }
        if($request->status === 'paid') {
             $approve = approvetenantsModel::create([
            'fkroomID'          => $booking->fkroomID,
            'firstname'         => $booking->firstname,
            'lastname'          => $booking->lastname,
            'contactNumber'     => $booking->contactNumber,
            'moveInDate'      => $booking->moveInDate,     // Example value
            'moveOutDate'     => $booking->moveOutDate,
            'contactEmail'      => $booking->contactEmail,
            'age'               => $booking->age,
            'gender'            => $booking->gender,
            'paymentType'       => $booking->paymentType,
            'paymentImage'      => $booking->paymentImage,
            'studentpictureId'  => $booking->studentpictureID,
        ]);
         $tenantName = $booking->firstname . ' ' . $booking->lastname;
            $message = "Hi {$tenantName}, 👋 Your Booking for Room {$room->roomNumber} at {$room->dorm} has been *confirmed*. Thank you!";
            $type = 'tenant';
             Mail::to($booking->contactEmail)->send(new TenantLandlordReminder($tenantName, $message, $type));
            $room->availability = 'Occupied'; 
            $room->save();
        } 


        $booking->status = $request->status; // Use the validated status
        $booking->updated_at = now();
        $booking->save();

            return response()->json([
            'status' => 'success',
            'message' => 'Tenant has been approved successfully.',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to approve tenant.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function deleteBooking($id)
{
    try {
        $booking = bookingModel::where('bookingID', $id)->first();

        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tenant screening record not found.'
            ], 404);
        }
        $booking->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tenant screening record deleted successfully.'
        ]);

    } catch (\Exception $e) {
        \Log::error('Error deleting screening: ' . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while deleting the tenant screening record.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


}
