<?php

namespace App\Http\Controllers\landlord\auth;
use App\Mail\TenantLandlordReminder;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordModel;
use App\Models\tenant\reservationModel;
use App\Models\landlord\roomModel;
use App\Models\landlord\dormModel;

class reservationController extends Controller
{
    public function reservationIndex($landlord_id)
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
    
        return view('landlord.auth.reservationpage',[
        "title" => "Landlord - Reservation", 
        'headerName' => 'Tenants Reservation',           
        'landlord' => $landlord,
    ]);   
    }
        public function searchReservation(Request $request)
{
    $landlordId = session('landlord_id');

    if (!$landlordId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized action. Please log in as a landlord.'
        ], 403);
    }

    $searchTerm = $request->input('search');

    $query = reservationModel::with(['room.dorm', 'room.landlord', 'tenant'])
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
    $reservations = $query->orderBy('created_at', 'asc')->paginate(5);

    return response()->json([
        'status' => 'success',
        'reservation' => $reservations
    ]);
}
    public function reservationList(Request $request)
    {
        $landlordId = session('landlord_id');
        if (!$landlordId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action. Please log in as a landlord.'
            ], 403);
        }
        $reservation = reservationModel::with(['room.dorm', 'room.landlord','tenant'])
        ->whereHas('room', function ($query) use ($landlordId) {
            $query->where('fklandlordID', $landlordId);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(5);
    
        return response()->json([
            'status' => 'success',
            'reservation' => $reservation,
            'landlord_id' => $landlordId
        ]);
    }
    public function viewReservation($id)
    {
        $landlordId = session('landlord_id');
        if (!$landlordId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action. Please log in as a landlord.'
            ], 403);
        }
    
        $reservation = reservationModel::with([
            'room.dorm','room.currentTenant'
        ])
        ->where('reservationID', $id)
        ->whereHas('room', function ($query) use ($landlordId) {
        $query->where('fklandlordID', $landlordId);
        })
        ->first();
    
        if (!$reservation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tenant screening record not found.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'reservation' => $reservation
        ]);
    }
    public function reservationDelete($id)
{
    try {
        $reservation = reservationModel::where('reservationID', $id)->first();

        if (!$reservation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tenant reservation record not found.'
            ], 404);
        }
        $reservation->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Tenant reservation record deleted successfully.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while deleting the tenant reservation record.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function getDormReservations()
{
    $dorms = dormModel::all();

    return response()->json($dorms); 
}
public function getTenantsByDorm($dormId)
{
    $tenants = reservationModel::whereHas('room.dorm', function ($query) use ($dormId) {
        $query->where('dormID', $dormId);
    })->with(['room.dorm'])
     ->orderBy('created_at', 'desc')
      ->paginate(5);

    return response()->json($tenants);
}
public function getRoomsNumber(Request $request)
{
    $landlordId = session('landlord_id');
    $searchTerm = $request->input('roomsNumber'); // match 'roomsNumber'

    $query = reservationModel::with(['room.dorm', 'room.landlord', 'tenant'])
        ->whereHas('room', function ($q) use ($landlordId) {
            $q->where('fklandlordID', $landlordId);
        });

        if ($searchTerm) {
            $query->whereHas('room', function ($q) use ($searchTerm) {
                $q->where('roomNumber', '=', $searchTerm); // use exact match
            });
        }
        

    return response()->json($query->orderBy('created_at', 'desc')->paginate(5));
}

public function getApplications(Request $request)
{
    $landlordId = session('landlord_id');
    $searchStatus = $request->input('selectedapplicationStatus');

    $query = reservationModel::with(['room.dorm', 'room.landlord', 'tenant'])
        ->whereHas('room', function ($q) use ($landlordId) {
            $q->where('fklandlordID', $landlordId);
        });

    if ($searchStatus) {
        $query->where('status', '=', $searchStatus);
    }

    return response()->json($query->orderBy('created_at', 'desc')->paginate(5));
}
 public function getAllReservations()
    {
        $reservations = reservationModel::with('room.dorm')->get();

        return response()->json($reservations);
    }
public function acceptReservation(Request $request)
{
    $request->validate([
        'reservationID' => 'required|integer|exists:reservation,reservationID',
        'email'         => 'required|email',
        'roomNumber'    => 'required|string',
        'dorm' => 'required|string',
        'firstname' => 'required|string',
        'lastname' => 'required|string',
    ]);


    try {
$tenantName = $request->firstname . ' ' . $request->lastname;
$message = "Hi! 👋 Just a friendly reminder to complete your reservation for Room {$request->roomNumber} at {$request->dorm}. Please confirm your move-in details soon!";
        $type = 'tenant';

    Mail::to($request->email)->send(new TenantLandlordReminder($tenantName, $message, $type));
        $reservation = reservationModel::findOrFail($request->reservationID);
        $reservation->status = 'for-confirmation';
        $reservation->updated_at = now();
        $reservation->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Reservation status updated to for-confirmation.'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update reservation.',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function EjectReservation(Request $request)
{
    $request->validate([
        'reservationID' => 'required|integer|exists:reservation,reservationID',
        'email'         => 'required|email',
        'roomNumber'    => 'required|string',
        'dorm' => 'required|string',
        'firstname' => 'required|string',
        'lastname' => 'required|string',
    ]);


    try {
            $tenantName = $request->firstname . ' ' . $request->lastname;
            $message = "Hi! 👋 We regret to inform you that your reservation for Room {$request->roomNumber} at {$request->dorm} has been declined by the landlord. If you have any questions or wish to explore other available rooms, feel free to contact us. Thank you!";
            $type = 'tenant';

    Mail::to($request->email)->send(new TenantLandlordReminder($tenantName, $message, $type));
        $reservation = reservationModel::findOrFail($request->reservationID);
        $reservation->status = 'rejected';
        $reservation->updated_at = now();
        $reservation->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Reservation has been rejected by the landlord.',
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update reservation.',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
