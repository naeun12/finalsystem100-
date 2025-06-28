<?php

namespace App\Http\Controllers\tenant\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordAccountModel;
use App\Models\landlord\landlordRoomModel;
use App\Models\landlord\landlordDormManagement; 
use App\Models\tenant\tenantaccountModel; 
use Illuminate\Support\Facades\DB;


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
    ->select('dorm_id', 'dorm_name', 'address', 'latitude', 'longitude','occupancy_type')
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
public function priceRange(Request $request)
{
    $range = $request->input('price_range');
    $lat = $request->input('lat');
    $lng = $request->input('lng');
    $query = DB::table('dorms')
    ->join('rooms','dorms.dorm_id', '=', 'rooms.dormitory_id')
    ->leftjoin('dorm_images','dorms.dorm_id','=','dorm_images.dormitory_id')
    ->select('dorms.*','rooms.price','dorm_images.main_image',
    DB::raw("(
        6371 * acos(
            cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) +
            sin(radians($lat)) * sin(radians(latitude))
        )
    ) AS distance_km")
)
    ->having('distance_km', '<=', 2)  // Only within 2km
    ->distinct();
    if($range !== 'all')
    {
        if($range === '301+')
        {
            $query->where('rooms.price', '>=', 301);
           
        }
        else {
            [$min, $max] = explode('-', $range);
            $query->whereBetween('rooms.price', [(int)$min, (int)$max]);
        }
    }
    $dorms = $query->get();
  
    return response()->json([
        'status' => 'success',
        'data' => $dorms
    ]);

}

public function SelectedGenderType(Request $request)
{
    $gender_type = strtolower(trim($request->input('gender_type')));
    $lat = $request->input('lat');
    $lng = $request->input('lng');

    $query = DB::table('dorms')
        ->leftJoin('dorm_images', 'dorms.dorm_id', '=', 'dorm_images.dormitory_id')
        ->select(
            'dorms.*',
            'dorm_images.main_image',
            DB::raw("(
                6371 * acos(
                    cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) +
                    sin(radians($lat)) * sin(radians(latitude))
                )
            ) AS distance_km")
        )
        ->having('distance_km', '<=', 2)
        ->distinct();

    if ($gender_type === 'male') {
        $query->whereRaw("LOWER(occupancy_type) LIKE 'male only'");
    } elseif ($gender_type === 'female') {
        $query->whereRaw("LOWER(occupancy_type) LIKE 'female only'");
    } elseif ($gender_type === 'mixed') {
        $query->whereRaw("LOWER(occupancy_type) LIKE '%mixed%'");
    }

    $dorms = $query->get();

    return response()->json([
        'status' => 'success',
        'data' => $dorms
    ]);
}public function filterPriceGender(Request $request)
{
    try {
        $range = $request->input('price_range');
        $gender_type = strtolower(trim($request->input('gender_type')));
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        $query = DB::table('dorms')
            ->join('rooms', 'dorms.dorm_id', '=', 'rooms.dormitory_id')
            ->leftJoin('dorm_images', 'dorms.dorm_id', '=', 'dorm_images.dormitory_id')
            ->select(
                'dorms.*',
                'rooms.price',
                'dorm_images.main_image',
                DB::raw("(
                    6371 * acos(
                        cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) +
                        sin(radians($lat)) * sin(radians(latitude))
                    )
                ) AS distance_km")
            )
            ->having('distance_km', '<=', 2)
            ->distinct();

        // ✅ Price filtering
        if ($range !== 'all') {
            if ($range === '301+') {
                $query->where('rooms.price', '>=', 301);
            } elseif (str_contains($range, '-')) {
                [$min, $max] = explode('-', $range);
                $query->whereBetween('rooms.price', [(int)$min, (int)$max]);
            }
        }

        // ✅ Gender filtering
        if ($gender_type !== 'all') {
            if ($gender_type === 'male') {
                $query->whereRaw("LOWER(occupancy_type) LIKE 'male only'");
            } elseif ($gender_type === 'female') {
                $query->whereRaw("LOWER(occupancy_type) LIKE 'female only'");
            } elseif ($gender_type === 'mixed') {
                $query->whereRaw("LOWER(occupancy_type) LIKE '%mixed%'");
            }
        }

        $dorms = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $dorms
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
        ], 500);
    }
}





}
