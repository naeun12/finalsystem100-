<?php

namespace App\Http\Controllers\landlord\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordModel;
use App\Models\tenant\approvetenantsModel;
use App\Models\landlord\roomModel;
use App\Models\landlord\dormModel;
use App\Models\notificationModel;
use Illuminate\Support\Facades\Validator;

class alltenantsController extends Controller
{
     public function alltenantIndex($landlord_id)
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
    
        return view('landlord.auth.tenantpage',[
        "title" => "Landlord - Tenants List", 
        'headerName' => 'Tenants List',           
        'landlord' => $landlord,
        'landlord_id' => $landlord_id,
        'notifications' => $notifications,
        'unread_count' => $unreadCount,
    ]); 
}
 public function tenantsList(Request $request)
    {
        $landlordId = session('landlord_id');
        if (!$landlordId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action. Please log in as a landlord.'
            ], 403);
        }
        $tenant = approvetenantsModel::with(['room.dorm', 'room.landlord'])
        ->whereHas('room', function ($query) use ($landlordId) {
            $query->where('fklandlordID', $landlordId);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(11);
    
        return response()->json([
            'status' => 'success',
            'tenant' => $tenant,
            'landlord_id' => $landlordId
        ]);
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

    $tenant = approvetenantsModel::with([
        'room.dorm'
    ])
    ->where('approvedID', $id)
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
public function updateTenantInformation(Request $request, $id)
{
    $landlordId = session('landlord_id');
    if (!$landlordId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized action. Please log in as a landlord.'
        ], 403);
    }

    $tenant = approvetenantsModel::find($id);
    if (!$tenant || $tenant->room->fklandlordID !== $landlordId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Tenant not found or unauthorized access.'
        ], 404);
    }
$validator = Validator::make($request->all(), [
    'firstname'     => ['required', 'string', 'max:255', 'regex:/^[A-Za-z]+$/'],
    'lastname'      => ['required', 'string', 'max:255', 'regex:/^[A-Za-z]+$/'],
    'gender'        => 'required|in:Male,Female,Other',
    'age'           => 'required|integer|min:15|max:120', // ✅ Minimum age 15
    'contactEmail'  => 'required|email|max:255', // ✅ Valid email
    'contactNumber' => 'required|digits_between:10,15', // ✅ Digits only
    'status'        => 'required|string|max:50'
], [
    'firstname.regex' => 'The first name may only contain letters.',
    'lastname.regex'  => 'The last name may only contain letters.',
    'age.min'         => 'The tenant must be at least 15 years old.',
    'contactNumber.digits_between' => 'The contact number must be between 10 and 15 digits and contain numbers only.'
]);



    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422);
    }
      $tenant->firstname = $request->input('firstname');
        $tenant->lastname = $request->input('lastname');
        $tenant->gender = $request->input('gender');
        $tenant->age = $request->input('age');
        $tenant->contactEmail = $request->input('contactEmail');
        $tenant->contactNumber = $request->input('contactNumber');
        $tenant->status = $request->input('status');

        $tenant->save();
    


    return response()->json([
        'status' => 'success',
        'message' => 'Tenant information updated successfully.',
        'tenant' => $tenant
    ]);
}
public function getDorms()
{
    $landlordId = session('landlord_id');

    if (!$landlordId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized action. Please log in as a landlord.'
        ], 403);
    }
    $dorms = dormModel::with('rooms')->where('fklandlordID', $landlordId)->get();
    if ($dorms->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No dorms found for this landlord.'
        ], 404);
    }

  
    return response()->json([
        'status' => 'success',
        'dorms' => $dorms
    ]);
}
public function getRoomTypes(Request $request)
{
    $roomNumber = $request->input('roomNumber');
    $dormId = $request->input('dormID'); // optional

    $query = roomModel::query()
        ->where('roomNumber', $roomNumber);

    if ($dormId) {
        $query->where('fkdormID', $dormId);
    }
    $roomTypes = $query->pluck('roomType');


    return response()->json([
        'status' => 'success',
        'roomTypes' => $roomTypes
    ]);
}
public function getRooms(Request $request)
{
    $dormId = $request->input('dormID');

$rooms = roomModel::where('fkdormID', $dormId)
    ->get();

    return response()->json([
        'status' => 'success',
        'data' => $rooms
    ]);
}
public function getRoomsDetails(Request $request)
{
    $roomID = $request->input('roomID');

    $rooms = roomModel::where('roomID', $roomID)->get();

    return response()->json([
        'status' => 'success',
        'data' => $rooms
    ]);
}
public function roomUpdate(Request $request,$roomID)
{
    $request->validate([
        'current_room_id' => 'required|integer|exists:rooms,roomID',
        'tenant_id' => 'required|integer|exists:approved_tenants,approvedID'
    ]);
     $currentRoom = roomModel::findOrFail($request->current_room_id);
    $currentRoom->availability = 'Available';
    $currentRoom->save();

    $newRoom = roomModel::findOrFail($roomID);
    $newRoom->availability = 'Occupied';
    $newRoom->save();
    if ($newRoom->availability === 'Occupied') {
        return response()->json([
            'status'  => 'error',
            'message' => 'The selected room is already occupied.'
        ]);
    }

    $updateTenant = approvetenantsModel::findOrFail($request->tenant_id);
    $updateTenant->fkroomID = $roomID;
    $updateTenant->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Room updated successfully',
        'current_room' => $currentRoom,
        'new_room' => $newRoom
    ]);
}
public function searchTenants(Request $request)
{
    $landlordId = session('landlord_id');
    if (!$landlordId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized action.'
        ], 403);
    }

    $searchTerm = trim($request->input('query', ''));

    $tenants = approvetenantsModel::with('room.dorm')
        ->whereHas('room', function ($query) use ($landlordId) {
            $query->where('fklandlordID', $landlordId);
        })
        ->when($searchTerm !== '', function ($query) use ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('firstname', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('lastname', 'LIKE', "%{$searchTerm}%");
            });
        })
        ->orderBy('updated_at', 'desc')
        ->get();

    return response()->json([
        'status' => 'success',
        'tenants' => $tenants
    ]);
}

public function filterByDorm(Request $request)
{
    $landlordId = session('landlord_id');
    $dormId = $request->input('dorm_id');

    if (!$landlordId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized action.'
        ], 403);
    }

    $query = approvetenantsModel::with('room.dorm')->whereHas('room', function ($q) use ($landlordId, $dormId) {
        $q->where('fklandlordID', $landlordId);
        if ($dormId) {
            $q->where('fkdormID', $dormId);
        }
    });

    $tenants = $query->get();

    return response()->json([
        'status' => 'success',
        'tenants' => $tenants
    ]);
}





}
