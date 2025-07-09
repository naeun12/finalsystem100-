<?php

namespace App\Http\Controllers\tenant\auth;
use App\Models\landlord\landlordModel;
use App\Models\landlord\roomModel;
use App\Models\landlord\dormModel; 
use App\Models\tenant\tenantModel; 
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;



use Illuminate\Support\Facades\Http;


class dormitories extends Controller
{
    public function dormitoriesListing($tenant_id)
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
        return view('tenant.auth.dormitories',['title' => 'Dormitories  - Dormhub',
        'tenant_id',$tenant,'cssPath' => asset('css/tenantpage/auth/dormitorymap.css')]);

    }
    public function Listdorms()
    {
        $dorms = dormModel::with(['amenities','images'])->get();
        return response()->Json([
            'status' => 'success',
            'dorms' => $dorms,
        ]);
    }
    protected $locationSynonyms = [
        "lapulapu" => ["lapu-lapu", "lapu lapu", "lapulapu", "lapulapu city"],
        "airport" => ["airport", "opon", "mcac", "mactan"]
    ];

    protected function normalize($text)
    {
        return preg_replace('/[^a-z0-9]+/', '', strtolower($text));
    }

    protected function matchesSynonym($keywordNormalized, $addressNormalized)
    {
        foreach ($this->locationSynonyms as $synonyms) {
            foreach ($synonyms as $syn) {
                $normalizedSyn = $this->normalize($syn);
                if (strpos($addressNormalized, $normalizedSyn) !== false && strpos($keywordNormalized, $normalizedSyn) !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    public function searchLocations(Request $request)
    {
        try {
            $validated = $request->validate([
                'location' => 'required|string',
            ]);
            $keyword = strtolower(trim($validated['location']));
            $keywordNormalized = $this->normalize($keyword);

            $dorms = DB::table('dorms as d')
                ->leftJoin('dormImages as i', 'i.fkdormID', '=', 'd.dormID')
                ->select('d.*', 'i.mainImage')
                ->whereNotNull('d.latitude')
                ->whereNotNull('d.longitude')
                ->get();

            $recommendations = $dorms->filter(function ($dorm) use ($keywordNormalized) {
                $addressNormalized = preg_replace('/[^a-z0-9]+/', '', strtolower($dorm->address));

                return strpos($addressNormalized, $keywordNormalized) !== false ||
                       $this->matchesSynonym($keywordNormalized, $addressNormalized);
            });

            return response()->json([
                'status' => 'success',
                'recommendations' => $recommendations->values()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Laravel controller error',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function priceRecommendations(Request $request)
    {
        try {
            $minPrice = floatval($request->input('min_price', 0));
            $maxPrice = floatval($request->input('max_price', 999999));
    
            $adjustedMin = $minPrice * 0.8;
            $adjustedMax = $maxPrice * 1.2;
    
            $results = DB::table('rooms as r')
                ->join('dorms as d', 'r.fkdormID', '=', 'd.dormID')
                ->leftJoin('dormImages as i', 'i.fkdormID', '=', 'd.dormID')
                ->select(
                    'd.dormID',
                    'd.dormName',
                    'd.address',
                    'd.latitude',
                    'd.longitude',
                    'r.price',
                    'r.roomType',
                    'r.furnishing_status',
                    'r.availability',
                    'i.mainImage'
                )
                ->whereBetween('r.price', [$adjustedMin, $adjustedMax])
                ->whereRaw('LOWER(r.availability) = ?', ['available'])
                ->orderBy('r.price', 'asc')
                ->limit(10)
                ->get();
    
            return response()->json([
                'status' => 'success',
                'data' => $results,
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function genderRecommendations(Request $request)
    {
        try {
            $validated = $request->validate([
                'occupancy_type' => 'required|string',
            ]);

            $inputType = strtolower(trim($validated['occupancy_type']));

            $dorms = DB::table('dorms as d')
                ->leftJoin('dormimages as i', 'i.fkdormID', '=', 'd.dormID')
                ->select('d.*', 'i.mainImage')
                ->get();

            $recommendations = $dorms->filter(function ($dorm) use ($inputType) {
                $type = strtolower(trim($dorm->occupancyType));
                return (
                    ($inputType == 'male' && $type == 'male only') ||
                    ($inputType == 'female' && $type == 'female only') ||
                    ($inputType == 'mixed' && strpos($type, 'mixed') !== false) ||
                    ($inputType == 'all')
                );
            });

            return response()->json([
                'status' => 'success',
                'recommendations' => $recommendations->values(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Laravel error',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function searchWithPrice(Request $request)
{
    try {
        $keyword = strtolower(trim($request->input('keyword', '')));
        $keywordNormalized = $this->normalize($keyword);
        $minPrice = floatval($request->input('min_price', 0));
        $maxPrice = floatval($request->input('max_price', 999999));

        // Query using Eloquent relationships
        $results = roomModel::with(['dorm', 'dorm.images']) // eager load
            ->whereNotNull('price')
            ->whereRaw('LOWER(availability) = ?', ['available'])
            ->get()
            ->map(function ($room) {
                return (object) [
                    'dormName'   => $room->dorm->dormName ?? null,
                    'address'    => $room->dorm->address ?? null,
                    'price'      => $room->price,
                    'roomType'   => $room->roomType,
                    'mainImage'  => optional($room->dorm->images)->mainImage,
                ];
            });

        // Filter by price range and keyword match
        $filtered = $results->filter(function ($item) use ($keywordNormalized, $minPrice, $maxPrice) {
            $price = floatval($item->price);
            $addressNormalized = $this->normalize($item->address);

            return $price >= $minPrice * 0.8 && $price <= $maxPrice * 1.2 &&
                (strpos($addressNormalized, $keywordNormalized) !== false ||
                 $this->matchesSynonym($keywordNormalized, $addressNormalized));
        });

        return response()->json([
            'status' => 'success',
            'recommendations' => $filtered->values()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Laravel error: ' . $e->getMessage()
        ], 500);
    }
}


    public function genderLocationRecommendations(Request $request)
    {
        try {
            $validated = $request->validate([
                'occupancy_type' => 'required|string',
                'location' => 'required|string',
            ]);

            $gender = strtolower(trim($validated['occupancy_type']));
            $keyword = strtolower(trim($validated['location']));
            $keywordNormalized = $this->normalize($keyword);

            $dorms = DB::table('dorms as d')
                ->leftJoin('dormimages as i', 'i.fkdormID', '=', 'd.dormID')
                ->select('d.*', 'i.mainImage')
                ->get();

            $results = $dorms->filter(function ($dorm) use ($gender, $keywordNormalized) {
                $dormGender = strtolower(trim($dorm->occupancyType));
                $addressNormalized = $this->normalize($dorm->address);
                $locationMatch = strpos($addressNormalized, $keywordNormalized) !== false ||
                                 $this->matchesSynonym($keywordNormalized, $addressNormalized);

                return $locationMatch && (
                    ($gender == 'male' && $dormGender == 'male only') ||
                    ($gender == 'female' && $dormGender == 'female only') ||
                    ($gender == 'mixed' && strpos($dormGender, 'mixed') !== false) ||
                    ($gender == 'all')
                );
            });

            return response()->json([
                'status' => 'success',
                'recommendations' => $results->values()
            ]);

        } catch (\Exception $e) {
            Log::error("Laravel Gender+Location API Error: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Laravel error',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function filtergenderpriceDormitories(Request $request)
{
    try {
        $priceRange = urldecode($request->input('price_range'));
        $occupancyType = strtolower(trim($request->input('occupancy_type')));

        Log::info('Price Range: ' . $priceRange);
        Log::info('Occupancy Type: ' . $occupancyType);

        $query = DB::table('dorms as d')
        ->join('rooms as r', 'd.dormID', '=', 'r.fkdormID') // Gamiton nato ang INNER JOIN para ang dorms nga walay rooms dili maapil
        ->leftJoin('dormimages as i', 'i.fkdormID', '=', 'd.dormID')
            ->select('d.*', 'i.mainImage', 'r.price', 'r.roomType')
            ->distinct();

        // Apply occupancy type filter
        if ($occupancyType && $occupancyType !== 'all') {
            $query->where(function($q) use ($occupancyType) {
                if ($occupancyType === 'male') {
                    $q->whereRaw("LOWER(d.occupancyType) = ?", ['male only'])
                      ->orWhereRaw("LOWER(d.occupancyType) = ?", ['male']);
                } elseif ($occupancyType === 'female') {
                    $q->whereRaw("LOWER(d.occupancyType) = ?", ['female only'])
                      ->orWhereRaw("LOWER(d.occupancyType) = ?", ['female']);
                } elseif ($occupancyType === 'mixed (male & female – separate floors)') {
                    $q->whereRaw("LOWER(d.occupancyType) = ?", ['mixed (male & female – separate floors)']);
                } else {
                    $q->whereRaw("LOWER(d.occupancyType) = ?", [$occupancyType]);
                }
            });
        }

        // Apply price range filter but allow NULLs (so dorms with no room still show)
        if ($priceRange && $priceRange !== 'all') {
            $query->where(function($q) use ($priceRange) {
                if ($priceRange === '301+') {
                    $q->where('r.price', '>=', 301)
                      ->orWhereNull('r.price');
                } elseif (strpos($priceRange, '-') !== false) {
                    [$min, $max] = explode('-', $priceRange);
                    $q->whereBetween('r.price', [(int)$min, (int)$max])
                      ->orWhereNull('r.price');
                }
            });
        }

        $dormitories = $query->get();

        return response()->json([
            'status' => 'success',
            'recommendations' => $dormitories,
        ]);

    } catch (\Exception $e) {
        Log::error('Error in filtergenderpriceDormitories: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Internal server error',
            'error' => $e->getMessage(),
        ], 500);
    }
}
public function filterpriceGenderDormitories(Request $request)
{
    try {
        $occupancyType = strtolower(trim($request->input('occupancy_type')));
        $priceRange = urldecode($request->input('price_range'));

        Log::info('Occupancy Type: ' . $occupancyType);
        Log::info('Price Range: ' . $priceRange);

        $query = DB::table('dorms as d')
            ->join('rooms as r', 'd.dormID', '=', 'r.fkdormID') // Only dorms with rooms
            ->leftJoin('dormimages as i', 'i.fkdormID', '=', 'd.dormID')
            ->select('d.*', 'i.mainImage', 'r.price', 'r.roomType')
            ->distinct();

        // Filter by occupancy type
        if ($occupancyType && $occupancyType !== 'all') {
            $query->where(function($q) use ($occupancyType) {
                if ($occupancyType === 'male') {
                    $q->whereRaw("LOWER(d.occupancyType) = ?", ['male only'])
                      ->orWhereRaw("LOWER(d.occupancyType) = ?", ['male']);
                } elseif ($occupancyType === 'female') {
                    $q->whereRaw("LOWER(d.occupancyType) = ?", ['female only'])
                      ->orWhereRaw("LOWER(d.occupancyType) = ?", ['female']);
                } elseif ($occupancyType === 'mixed') {
                    $q->whereRaw("LOWER(d.occupancyType) LIKE ?", ['mixed%']);
                } else {
                    $q->whereRaw("LOWER(d.occupancyType) = ?", [$occupancyType]);
                }
            });
        }

        // Filter by price
        if ($priceRange && $priceRange !== 'all') {
            $query->where(function($q) use ($priceRange) {
                if ($priceRange === '301+') {
                    $q->where('r.price', '>=', 301)
                      ->orWhereNull('r.price');
                } elseif (strpos($priceRange, '-') !== false) {
                    [$min, $max] = explode('-', $priceRange);
                    $q->whereBetween('r.price', [(int)$min, (int)$max])
                      ->orWhereNull('r.price');
                }
            });
        }

        $dormitories = $query->get();

        return response()->json([
            'status' => 'success',
            'recommendations' => $dormitories,
        ]);
    } catch (\Exception $e) {
        Log::error('Error in filterpriceGenderDormitories: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Internal server error',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    
    


}