<?php

namespace App\Http\Controllers\landlord\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordModel;
use App\Models\landlord\bookingModel;
use App\Models\landlord\roomModel;
use App\Models\landlord\dormModel;

use App\Models\tenant\approvetenantsModel;

class bookingpageController extends Controller
{
    public function bookingpageIndex($landlord_id)
    {
        $sessionLandlordId = session('landlord_id');
    
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

    $bookings = $query->orderBy('created_at', 'asc')->paginate(5);

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
        $booking = bookingModel::with(['room.dorm', 'room.landlord', 'tenant'])
        ->whereHas('room', function ($query) use ($landlordId) {
            $query->where('fklandlordID', $landlordId);
        })
        ->orderBy('created_at', 'asc')
        ->paginate(5);
    
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
      ->paginate(5);

    return response()->json($tenants);
}
public function getRooms(Request $request)
{
    $landlordId = session('landlord_id');
    $searchTerm = $request->input('roomsNumber'); // match 'roomsNumber'

    $query = bookingModel::with(['room.dorm', 'room.landlord', 'tenant'])
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

    $query = bookingModel::with(['room.dorm', 'room.landlord', 'tenant'])
        ->whereHas('room', function ($q) use ($landlordId) {
            $q->where('fklandlordID', $landlordId);
        });

    if ($searchStatus) {
        $query->where('status', '=', $searchStatus);
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
        'room.dorm'
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
public function approveTenant(Request $request)
{
    $request->validate([
        'bookingID' => 'required|integer|exists:bookings,bookingID',
    ]);

    try {
        $booking = bookingModel::findOrFail($request->bookingID);
        $room = roomModel::findOrFail($booking->fkroomID);

        // ✅ Check if the room is already occupied
        if ($room->availability === 'Occupied') {
            return response()->json([
                'status' => 'error',
                'message' => 'This room is already occupied. You cannot approve another tenant.',
            ], 403);
        }

        // ✅ Proceed with approval
        $booking->status = 'Approved';
        $booking->updated_at = now();
        $booking->save();

        $room->availability = 'Occupied';
        $room->save();

        $approve = approvetenantsModel::create([
            'fkroomID'          => $booking->fkroomID,
            'firstname'         => $booking->firstname,
            'lastname'          => $booking->lastname,
            'contactNumber'     => $booking->contactNumber,
            'move-in-date'      => now()->format('Y-m-d'),     // Example value
            'move-out-date'     => now()->addMonths(6)->format('Y-m-d'), // Example 6-month stay
            'contactEmail'      => $booking->contactEmail,
            'age'               => $booking->age,
            'gender'            => $booking->gender,
            'paymentType'       => $booking->paymentType,
            'paymentImage'      => $booking->paymentImage,
            'studentpictureId'  => $booking->studentpictureID,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tenant has been approved successfully.',
            'approveID' => $approve->approveID
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to approve tenant.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function notapproveTenant(Request $request)
{
    $request->validate([
        'bookingID' => 'required|integer|exists:bookings,bookingID',
    ]);

    try {
        // Get the screening record
        $booking = bookingModel::findOrFail($request->bookingID);
        if ($booking->status === 'Approved') {
            $room = roomModel::findOrFail($booking->fkroomID);
            $room->availability = 'Available';
            $room->save();
        }
        $booking->status = 'Not Approved';
        $booking->updated_at = now();
        $booking->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Tenant application has been declined.',
        ]);

    } catch (\Exception $e) {
        \Log::error('Error declining tenant: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to decline tenant.',
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
