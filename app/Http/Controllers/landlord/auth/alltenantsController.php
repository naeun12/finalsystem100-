<?php

namespace App\Http\Controllers\landlord\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordModel;
use App\Models\tenant\approvetenantsModel;
use App\Models\landlord\roomModel;
use App\Models\landlord\dormModel;
use App\Models\notificationModel;

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

    // Load dorms with all rooms
$dorms = dormModel::with('rooms')->where('fklandlordID', $landlordId)->get();
$uniqueRooms = collect();

    if ($dorms->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'No dorms found for this landlord.'
        ], 404);
    }

   foreach ($dorms as $dorm) {
    foreach ($dorm->rooms as $room) {
        if (!$uniqueRooms->contains('roomNumber', $room->roomNumber)) {
            $uniqueRooms->push($room);
        }
    }
            $dorm->setRelation('rooms', $uniqueRooms);

}


    return response()->json([
        'status' => 'success',
        'dorms' => $dorms
    ]);
}

}
