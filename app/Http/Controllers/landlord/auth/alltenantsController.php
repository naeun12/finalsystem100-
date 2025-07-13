<?php

namespace App\Http\Controllers\landlord\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordModel;
use App\Models\tenant\approvetenantsModel;
use App\Models\landlord\roomModel;
use App\Models\landlord\dormModel;

class alltenantsController extends Controller
{
     public function alltenantIndex($landlord_id)
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
    
        return view('landlord.auth.tenantpage',[
        "title" => "Landlord - Tenants", 
        'headerName' => 'Tenants List',           
        'landlord' => $landlord,
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
        ->orderBy('created_at', 'asc')
        ->paginate(5);
    
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
}
