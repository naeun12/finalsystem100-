<?php

namespace App\Http\Controllers\tenant\auth\nav\myroom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\roomModel;
use App\Models\tenant\tenantModel;
use App\Models\tenant\approvetenantsModel;


class myroomsController extends Controller
{
    public function myroomsIndex($tenant_id)
        {
            $sessionTenant_id = session('tenant_id');
        
            if (!$sessionTenant_id) {
                return redirect()->route('tenant-login')->with('error', 'Please log in as a landlord.');
            }
        
            if ($tenant_id !== $sessionTenant_id) {
                return redirect()->route('tenant-login')->with('error', 'Unauthorized access.');
            }
        
            $tenant = tenantModel::find($tenant_id);
            if (!$tenant) {
                return redirect()->route('tenant-login')->with('error', 'Landlord not found.');
            }
            $title = 'Tenant My Rooms - Dormhub';
            return view('tenant.auth.nav.rooms.myrooms',['title' => 'My Rooms',
            'tenant_id' => $tenant_id,
            'cssPath' => asset('css/tenantpage/auth/roomdetails.css')]);
        }
        public function myroomsList($tenant_id)
        {
    $rooms = roomModel::with('dorm', 'features', 'approvedTenant')
    ->whereHas('approvedTenant', function ($query) use ($tenant_id) {
        $query->where('fktenantID', $tenant_id);
    })->get();


return response()->json([
        'rooms' => $rooms // 👈 importante ni para `response.data.rooms` sa Vue
    ]);
    }

}
