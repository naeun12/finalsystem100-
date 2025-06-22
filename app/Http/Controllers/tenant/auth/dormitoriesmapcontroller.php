<?php

namespace App\Http\Controllers\tenant\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordAccountModel;
use App\Models\landlord\landlordRoomModel;
use App\Models\landlord\landlordDormManagement; 
use App\Models\tenant\tenantaccountModel; 

class dormitoriesmapcontroller extends Controller
{
    public function dormitoriesMap($tenant_id)
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
        return view('tenant.auth.dormitorieslocation',['title' => 'Dormitories Map  - Dormhub',
        'tenant_id',$tenant,'cssPath' => asset('css/tenantpage/auth/dormitorymap.css')]);

    }
    public function getNearbyByCoordinates(Request $request)
{
    $lat = $request->input('lat');
    $lng = $request->input('lng');
    $radius = 2; // Radius in KM

    $nearbyDorms = landlordDormManagement::with('images')
    ->select('dorm_id', 'dorm_name', 'address', 'latitude', 'longitude')
    ->selectRaw("(
        6371 * acos(
            cos(radians(?)) * cos(radians(latitude)) *
            cos(radians(longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(latitude))
        )
    ) AS distance", [$lat, $lng, $lat])
    ->having('distance', '<', $radius)
    ->orderBy('distance')
    ->get()
    ->map(function ($dorm)
    {
       $dorm->distance_km = round($dorm->distance, 2);
       return $dorm; 
    });

return response()->json([
    'status' => 'success',
    'count' => $nearbyDorms->count(),
    'data' => $nearbyDorms
]);
}

    public function locationSearch()
    {
        
    }
}
