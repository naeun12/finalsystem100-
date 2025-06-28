<?php

namespace App\Http\Controllers\tenant\auth;
use App\Models\landlord\landlordAccountModel;
use App\Models\landlord\landlordRoomModel;
use App\Models\landlord\landlordDormManagement; 
use App\Models\tenant\tenantaccountModel; 
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
    
        $tenant = tenantaccountModel::find($tenant_id);
        if (!$tenant) {
            return redirect()->route('tenant-login')->with('error', 'Landlord not found.');
        }
        return view('tenant.auth.dormitories',['title' => 'Dormitories  - Dormhub',
        'tenant_id',$tenant,'cssPath' => asset('css/tenantpage/auth/dormitorymap.css')]);

    }
    public function Listdorms()
    {
        $dorms = landlordDormManagement::with(['amenities','images'])->get();
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

            Log::info('Search location input:', $validated);

            $keyword = strtolower(trim($validated['location']));
            $keywordNormalized = $this->normalize($keyword);

            $dorms = DB::table('dorms as d')
                ->leftJoin('dorm_images as i', 'i.dormitory_id', '=', 'd.dorm_id')
                ->select('d.*', 'i.main_image')
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
            Log::error('Error from searchLocations(): ' . $e->getMessage());

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
                ->join('dorms as d', 'r.dormitory_id', '=', 'd.dorm_id')
                ->leftJoin('dorm_images as i', 'i.dormitory_id', '=', 'd.dorm_id')
                ->select(
                    'd.dorm_id',
                    'd.dorm_name',
                    'd.address',
                    'd.latitude',
                    'd.longitude',
                    'r.price',
                    'r.room_type',
                    'r.furnishing_status',
                    'r.capacity',
                    'r.availability',
                    'i.main_image'
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
                ->leftJoin('dorm_images as i', 'i.dormitory_id', '=', 'd.dorm_id')
                ->select('d.*', 'i.main_image')
                ->get();

            $recommendations = $dorms->filter(function ($dorm) use ($inputType) {
                $type = strtolower(trim($dorm->occupancy_type));
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
            Log::error("Laravel Gender API Error: " . $e->getMessage());
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

            $results = DB::table('rooms as r')
                ->join('dorms as d', 'r.dormitory_id', '=', 'd.dorm_id')
                ->leftJoin('dorm_images as i', 'i.dormitory_id', '=', 'd.dorm_id')
                ->select('d.dorm_name', 'd.address', 'r.price', 'r.room_type','i.main_image')
                ->whereNotNull('r.price')
                ->whereRaw('LOWER(r.availability) = ?', ['available'])
                ->get();

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
                ->leftJoin('dorm_images as i', 'i.dormitory_id', '=', 'd.dorm_id')
                ->select('d.*', 'i.main_image')
                ->get();

            $results = $dorms->filter(function ($dorm) use ($gender, $keywordNormalized) {
                $dormGender = strtolower(trim($dorm->occupancy_type));
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
        ->join('rooms as r', 'd.dorm_id', '=', 'r.dormitory_id') // Gamiton nato ang INNER JOIN para ang dorms nga walay rooms dili maapil
        ->leftJoin('dorm_images as i', 'i.dormitory_id', '=', 'd.dorm_id')
            ->select('d.*', 'i.main_image', 'r.price', 'r.room_type')
            ->distinct();

        // Apply occupancy type filter
        if ($occupancyType && $occupancyType !== 'all') {
            $query->where(function($q) use ($occupancyType) {
                if ($occupancyType === 'male') {
                    $q->whereRaw("LOWER(d.occupancy_type) = ?", ['male only'])
                      ->orWhereRaw("LOWER(d.occupancy_type) = ?", ['male']);
                } elseif ($occupancyType === 'female') {
                    $q->whereRaw("LOWER(d.occupancy_type) = ?", ['female only'])
                      ->orWhereRaw("LOWER(d.occupancy_type) = ?", ['female']);
                } elseif ($occupancyType === 'mixed (male & female – separate floors)') {
                    $q->whereRaw("LOWER(d.occupancy_type) = ?", ['mixed (male & female – separate floors)']);
                } else {
                    $q->whereRaw("LOWER(d.occupancy_type) = ?", [$occupancyType]);
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
            ->join('rooms as r', 'd.dorm_id', '=', 'r.dormitory_id') // Only dorms with rooms
            ->leftJoin('dorm_images as i', 'i.dormitory_id', '=', 'd.dorm_id')
            ->select('d.*', 'i.main_image', 'r.price', 'r.room_type')
            ->distinct();

        // Filter by occupancy type
        if ($occupancyType && $occupancyType !== 'all') {
            $query->where(function($q) use ($occupancyType) {
                if ($occupancyType === 'male') {
                    $q->whereRaw("LOWER(d.occupancy_type) = ?", ['male only'])
                      ->orWhereRaw("LOWER(d.occupancy_type) = ?", ['male']);
                } elseif ($occupancyType === 'female') {
                    $q->whereRaw("LOWER(d.occupancy_type) = ?", ['female only'])
                      ->orWhereRaw("LOWER(d.occupancy_type) = ?", ['female']);
                } elseif ($occupancyType === 'mixed') {
                    $q->whereRaw("LOWER(d.occupancy_type) LIKE ?", ['mixed%']);
                } else {
                    $q->whereRaw("LOWER(d.occupancy_type) = ?", [$occupancyType]);
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