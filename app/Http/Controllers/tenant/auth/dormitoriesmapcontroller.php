<?php

namespace App\Http\Controllers\tenant\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordAccountModel;
use App\Models\landlord\roomModel;
use App\Models\landlord\dormModel; 
use App\Models\tenant\tenantModel; 
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
    
        $tenant = tenantModel::find($tenant_id);
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

    $nearbyDorms = dormModel::with('images')
    ->select('dormID', 'dormName', 'address', 'latitude', 'longitude','occupancyType')
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

    $dorms = dormModel::with(['rooms', 'images'])
        ->select('dorms.*')
        ->selectRaw("(
            6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + 
                sin(radians(?)) * sin(radians(latitude))
            )
        ) AS distance_km", [$lat, $lng, $lat])
        ->having('distance_km', '<=', 2)
        ->whereHas('rooms', function ($query) use ($range) {
            if ($range !== 'all') {
                if ($range === '301+') {
                    $query->where('price', '>=', 301);
                } elseif (str_contains($range, '-')) {
                    [$min, $max] = explode('-', $range);
                    $query->whereBetween('price', [(int)$min, (int)$max]);
                }
            }
        })
        ->get()
        ->map(function ($dorm) {
            // Add computed field to each dorm
            $dorm->price = optional($dorm->rooms->first())->price;
            $dorm->main_image = optional($dorm->images)->mainImage;
            return $dorm;
        });

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

    $dorms = dormModel::with('images')
        ->selectRaw("dorms.*, (
            6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )
        ) AS distance_km", [$lat, $lng, $lat])
        ->having('distance_km', '<=', 2)
        ->when($gender_type !== 'all', function ($query) use ($gender_type) {
            if ($gender_type === 'male') {
                $query->whereRaw("LOWER(occupancyType) LIKE 'male only'");
            } elseif ($gender_type === 'female') {
                $query->whereRaw("LOWER(occupancyType) LIKE 'female only'");
            } elseif ($gender_type === 'mixed') {
                $query->whereRaw("LOWER(occupancyType) LIKE '%mixed%'");
            }
        })
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $dorms
    ]);
}

public function filterPriceGender(Request $request)
{
    try {
        $range = $request->input('price_range');
        $gender_type = strtolower(trim($request->input('gender_type')));
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        $dorms = dormModel::with(['rooms', 'images'])
            ->selectRaw("dorms.*, (
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )
            ) AS distance_km", [$lat, $lng, $lat])
            ->having('distance_km', '<=', 2)
            ->when($range !== 'all', function ($query) use ($range) {
                if ($range === '301+') {
                    $query->whereHas('rooms', function ($q) {
                        $q->where('price', '>=', 301);
                    });
                } elseif (str_contains($range, '-')) {
                    [$min, $max] = explode('-', $range);
                    $query->whereHas('rooms', function ($q) use ($min, $max) {
                        $q->whereBetween('price', [(int)$min, (int)$max]);
                    });
                }
            })
            ->when($gender_type !== 'all', function ($query) use ($gender_type) {
                $query->whereRaw("LOWER(occupancyType) LIKE ?", [$gender_type === 'mixed' ? '%mixed%' : "$gender_type only"]);
            })
            ->get();

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
