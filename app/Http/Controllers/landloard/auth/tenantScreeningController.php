<?php

namespace App\Http\Controllers\landloard\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\landlord\landlordAccountModel;
use App\Models\landlord\tenantscreeningModel;
use App\Models\landlord\landlordRoomModel;




class tenantScreeningController extends Controller
{
    public function tenantScreening($landlordId)
    {
        $sessionLandlordId = session('landlord_id');
    
        if (!$sessionLandlordId) {
            return redirect()->route('loginLandlord')->with('error', 'Please log in as a landlord.');
        }
    
        if ($landlordId !== $sessionLandlordId) {
            return redirect()->route('loginLandlord')->with('error', 'Unauthorized access.');
        }
    
        $landlord = landlordAccountModel::find($landlordId);
        if (!$landlord) {
            return redirect()->route('loginLandlord')->with('error', 'Landlord not found.');
        }
    
        return view('landlord.auth.tenantScreening',[
        "title" => "Landlord - Tenants", 
        'headerName' => 'Tenants Screening',           
        'landlord' => $landlord,
    ]);
        
    }
    public function tenantScreeningList()
    {
        $landlordId = session('landlord_id');

        if (!$landlordId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action. Please log in as a landlord.'
            ], 403);
        }
        $screening = tenantscreeningModel::with('room','dorm')->where('landlord_id', $landlordId)->get();

        return response()->json([
            'status' => 'success',
            'screening' => $screening,
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
    
        // Fetch the room by ID with its dorm info
        $tenant = DB::table('tenant_screening as tenant_screening')
            ->join('dorms', 'tenant_screening.fkdormitory_id', '=', 'dorms.dorm_id')
            ->join('rooms', 'tenant_screening.fkroom_id', '=', 'rooms.room_id')
            ->where('tenant_screening.tenantscreening_id', $id)
            ->where('rooms.landlord_id', $landlordId)
            ->select(
                'tenant_screening.*',
                'dorms.dorm_name',
                'dorms.address',
                'dorms.contact_email',
                'dorms.contact_phone',
                'dorms.rules',
                'rooms.room_number',
                'rooms.price',
            )
            ->first();

    
        // Check if the room exists
        if (!$tenant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Room not found.'
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
            'tenant_screening_id' => 'required|integer|exists:tenant_screening,tenantscreening_id',
        ]);
    
        try {
            // Get screening record
            $screening = tenantscreeningModel::findOrFail($request->tenant_screening_id);
    
            // Get the associated room
            $room = landlordRoomModel::findOrFail($screening->fkroom_id);
    
            // Check if room has capacity left
            if ($room->capacity <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Room is full. Cannot approve more tenants.',
                ], 400);
            }
    
            // Approve tenant
            $screening->status = 'Approved';
            $screening->updated_at = now();
            $screening->save();
    
            // Reduce capacity by 1
            $room->capacity -= 1;
    
            // If capacity is now 0, mark room as not available
            if ($room->capacity <= 0) {
                $room->availability = 'Occupied';
            }
    
            $room->save();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Tenant has been approved successfully.',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error approving tenant: ' . $e->getMessage());
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
            'tenant_screening_id' => 'required|integer|exists:tenant_screening,tenantscreening_id',
        ]);
    
        try {
            // Get the screening record
            $screening = tenantscreeningModel::findOrFail($request->tenant_screening_id);
    
            // If the tenant was previously approved, we should increase capacity
            if ($screening->status === 'Approved') {
                $room = landlordRoomModel::findOrFail($screening->fkroom_id);
    
                // Increase capacity by 1
                $room->capacity += 1;
    
                // If room now has space, mark as Available
                if ($room->capacity > 0) {
                    $room->availability = 'Available';
                }
    
                $room->save();
            }
    
            // Update tenant status to "Not Approved"
            $screening->status = 'Not Approved';
            $screening->updated_at = now();
            $screening->save();
    
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
    public function DeleteScreening($id)
{
    $landlordId = session('landlord_id');

    if (!$landlordId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized action. Please log in as a landlord.'
        ], 403);
    }

    $screening = tenantscreeningModel::where('tenantscreening_id', $id)
        ->where('landlord_id', $landlordId)
        ->first();

    if (!$screening) {
        return response()->json([
            'status' => 'error',
            'message' => 'Tenant screening record not found.'
        ], 404);
    }

    try {
        // If the tenant was previously approved, increase room capacity
        if ($screening->status === 'Approved') {
            $room = landlordRoomModel::find($screening->fkroom_id);

            if ($room) {
                $room->capacity += 1;

                // Mark as Available if it now has space
                if ($room->capacity > 0) {
                    $room->availability = 'Available';
                }

                $room->save();
            }
        }

        $screening->delete();

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
