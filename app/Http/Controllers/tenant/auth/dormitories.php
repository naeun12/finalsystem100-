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
use App\Models\notificationModel;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


use Illuminate\Support\Facades\Http;


class dormitories extends Controller
{
    public function dormitoriesListing($tenant_id)
    {
        $sessionTenant_id = session('tenant_id');
         $notifications = notificationModel::where('receiverID', $sessionTenant_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            $unreadCount = notificationModel::where('receiverID', $tenant_id)
            ->where('isRead', false)
            ->count();
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
        'tenant_id',$tenant,'cssPath' => asset('css/tenantpage/auth/dormitorymap.css')
        ,'notifications' => $notifications,
             'unread_count' => $unreadCount,
    ]);

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
    public function mostWatchedDorm($id)
    {
        $dorm = dormModel::with('images')->orderBy('views', 'desc')->get();
            return response()->json([
            'message' => 'Most watched dorm retrieved successfully.',
            'dorm' => $dorm
    ]);
    }
    public function searchDormsWithFilters(array $filters)
{
    $query = dormModel::query();

    if (!empty($filters['location'])) {
        $query->where('address', 'like', '%' . $filters['location'] . '%');
    }

    // You can join rooms table and filter by room type or price
    if (!empty($filters['type']) || !empty($filters['max_price'])) {
        $query->whereHas('rooms', function ($q) use ($filters) {
            if (!empty($filters['type'])) {
                $q->where('roomType', $filters['type']);
            }
            if (!empty($filters['max_price'])) {
                $q->where('price', '<=', $filters['max_price']);
            }
        });
    }

    $dorms = $query->with('rooms')->get();

    return $dorms;
}
public function getQuestionRecommendations(Request $request)
{
    $question = $request->input('question');

    // Call Flask API to get filters & AI message
    $response = Http::post('http://127.0.0.1:5000/api/ask', [
        'question' => $question,
    ]);

    \Log::info('Raw Flask response: ' . $response->body());

    $data = $response->json()['answer'] ?? null;

    if (!$data) {
        return response()->json([
            'message' => 'Walay tubag nakuha',
            'result' => []
        ]);
    }

    // Extract AI message & filters (assuming filters are parsed in AI or sent separately)
    $aiMessage = $data['message'] ?? 'Walay tubag nakuha';

    // Example: parse filters from AI message or use separate endpoint to extract filters
    // For demonstration, let's assume the AI sends filters in the response (you'll need to adapt Flask code)
    $filters = $data['filters'] ?? [];  // <-- implement sa Flask nga mo-send ni siya

    $dorms = $this->searchDormsWithFilters($filters);

    return response()->json([
        'message' => $aiMessage,
        'result' => $dorms
    ]);
}





 



   
}