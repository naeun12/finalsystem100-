<?php

namespace App\Http\Controllers\tenant\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordAccountModel;
use App\Models\landlord\landlordRoomModel;
use App\Models\landlord\landlordDormManagement; 
use App\Models\tenant\tenantaccountModel; 



class homepageController extends Controller
{
    public function homepage($tenant_id)
    {
        
        $sessionTenant_id = session('tenant_id');
    
        if (!$sessionTenant_id) {
            return redirect()->route('tenant-login')->with('error', 'Please log in as a landlord.');
        }
    
        if ($tenant_id !== $sessionTenant_id) {
            return redirect()->route('tenant-login')->with('error', 'Unauthorized access.');
        }
    
        $tenant = tenantaccountModel::find($tenant_id);
        if (!$tenant) {
            return redirect()->route('tenant-login')->with('error', 'Landlord not found.');
        }
        return view('tenant.auth.homepage',['title' => 'Room Details  - Dormhub',
        'tenant_id',$tenant,'cssPath' => asset('css/tenantpage/auth/home.css')]);

    }
    public function dormLapuLapu()
{
    return response()->json(
        landlordDormManagement::where('address', 'LIKE', '%Lapu-Lapu%')
            ->select('dorm_id', 'dorm_name', 'address', 'latitude', 'longitude')
            ->with(['images' => function ($query) {
                $query->select('dormitory_id', 'main_image');
            }])
            ->get()
    );
}

public function dormMandaeu()
{
    return response()->json(
        landlordDormManagement::where('address', 'LIKE', '%Mandaue%')
            ->select('dorm_id', 'dorm_name', 'address', 'latitude', 'longitude')
            ->with(['images' => function ($query) {
                $query->select('dormitory_id', 'main_image');
            }])
            ->get()
    );
}

}
