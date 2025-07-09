<?php

namespace App\Http\Controllers\tenant\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordAccountModel;
use App\Models\landlord\roomModel;

use App\Models\landlord\dormModel; 
use App\Models\tenant\tenantModel; 



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
    
        $tenant = tenantModel::find($tenant_id);
        if (!$tenant) {
            return redirect()->route('tenant-login')->with('error', 'Landlord not found.');
        }
        return view('tenant.auth.homepage',['title' => 'Room Details  - Dormhub',
        'tenant_id',$tenant,'cssPath' => asset('css/tenantpage/auth/home.css')]);

    }
    public function dormLapuLapu()
{
    return response()->json(
        dormModel::where('address', 'LIKE', '%Lapu-Lapu%')
            ->select('dormID', 'dormName', 'address', 'latitude', 'longitude')
            ->with(['images' => function ($query) {
                $query->select('fkdormID', 'mainImage');
            }])
            ->get()
    );
}

public function dormMandaeu()
{
    return response()->json(
        dormModel::where('address', 'LIKE', '%Mandaue%')
            ->select('dormID', 'dormName', 'address', 'latitude', 'longitude')
            ->with(['images' => function ($query) {
                $query->select('fkdormID', 'mainImage');
            }])
            ->get()
    );
}

}
