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
                ->leftJoin('dormimages as i', 'i.fkdormID', '=', 'd.dormID')
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
                ->leftJoin('dormimages as i', 'i.fkdormID', '=', 'd.dormID')
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

    try {
        // Call Flask/OpenAI API
//  $response = Http::post('http://127.0.0.1:5000/ask-ai/dormitories', [
//             'question' => $question,
//         ]);
// Use your Render app URL here
// $flaskApiUrl = "https://pythonai-rkae.onrender.com/ask-ai/dormitories";

// Send POST request to Flask API
$apiUrl = env('AI_API_URL') . '/'.'dormitories';

$response = Http::post($apiUrl, [
    'question' => $question,
]);

// Optional: handle errors
if ($response->failed()) {
    return [
        'message' => 'Naa’y error sa pagkuha og AI recommendations.',
        'result' => [],
        'recommendations' => [],
    ];
}

// Get JSON response
$data = $response->json();

        \Log::info('Raw Flask response: ' . $response->body());

        $data = $response->json() ?? [];

        // Raw AI message
        $aiRaw = $data['message'] ?? 'Walay tubag nakuha gikan sa AI';

        // Extract AI message text (remove JSON block if any)
        $aiMessage = preg_replace('/```json.*```/s', '', $aiRaw);
        $aiMessage = trim($aiMessage);

        // Extract AI JSON recommendations if present
        preg_match('/```json(.*?)```/s', $aiRaw, $matches);
        $aiRecommendations = [];
        if (isset($matches[1])) {
            $aiRecommendations = json_decode(trim($matches[1]), true) ?? [];
        }

        // Fallback if Flask didn't return JSON
        if (empty($aiRecommendations)) {
            $aiRecommendations = $data['recommendations'] ?? [];
        }

        // Ensure each dorm has proper structure and rooms are included
        $recommendations = array_map(function ($dorm) {
            // Handle amenities safely
            $amenities = '';
            if (isset($dorm['amenities'])) {
                if (is_array($dorm['amenities'])) {
                    $amenities = implode(',', array_column($dorm['amenities'], 'aminityName'));
                } elseif (is_string($dorm['amenities'])) {
                    $amenities = $dorm['amenities'];
                }
            }

            // Handle rooms safely
            $rooms = [];
            if (!empty($dorm['rooms']) && is_array($dorm['rooms'])) {
                $rooms = array_map(function ($room) {
                    $features = $room['features'] ?? [];
                    if (is_string($features)) {
                        $features = explode(',', $features);
                    }
                    return [
                        'roomNumber' => $room['roomNumber'] ?? null,
                        'type' => $room['type'] ?? $room['roomType'] ?? "Standard",
                        'price' => $room['price'] ?? null,
                        'availability' => $room['availability'] ?? null,
                        'features' => $features,
                    ];
                }, $dorm['rooms']);
            }

            return [
                'dormID' => $dorm['dormID'] ?? null,
                'dormName' => $dorm['dormName'] ?? 'Unnamed Dorm',
                'address' => $dorm['address'] ?? 'No address provided',
                'occupancyType' => $dorm['occupancyType'] ?? 'Mixed',
                'price' => $dorm['price'] ?? 'Contact landlord',
                'dormimages' => [
                    'mainImage' => $dorm['dormimages']['mainImage'] ?? $dorm['mainImage'] ?? null,
                    'secondaryImage' => $dorm['dormimages']['secondaryImage'] ?? null,
                    'thirdImage' => $dorm['dormimages']['thirdImage'] ?? null
                ],
                'amenities' => $amenities,
                'rules' => $dorm['rules'] ?? [],
                'rooms' => $rooms,
                'fklandlordID' => $dorm['fklandlordID'] ?? null,
                'landlord' => $dorm['landlord'] ?? [],
            ];
        }, $aiRecommendations);

        // Fallback entirely to DB result if recommendations still empty
        if (empty($recommendations) && isset($data['result']) && is_array($data['result'])) {
            $recommendations = array_map(function($room) {
                $features = $room['features'] ?? [];
                if (is_string($features)) {
                    $features = explode(',', $features);
                }

                return [
                    'dormID' => $room['dormID'] ?? null,
                    'dormName' => $room['dormName'] ?? 'Unnamed Dorm',
                    'address' => $room['address'] ?? 'No address provided',
                    'occupancyType' => $room['occupancyType'] ?? 'Mixed',
                    'price' => $room['price'] ?? 'Contact landlord',
                    'rooms' => [
                        [
                            'roomNumber' => $room['roomNumber'] ?? null,
                            'type' => $room['type'] ?? $room['roomType'] ?? "Standard",
                            'price' => $room['price'] ?? null,
                            'availability' => $room['availability'] ?? null,
                            'features' => $features,
                        ]
                    ],
                    'fklandlordID' => $room['fklandlordID'] ?? null,
                    'landlord' => $room['landlord'] ?? [],
                ];
            }, $data['result']);
        }

        return response()->json([
            'message' => $aiMessage,
            'dorms' => $data['result'] ?? [],
            'recommendations' => $recommendations,
        ]);

    } catch (\Exception $e) {
        \Log::error('AI Dorm Recommendation Error: ' . $e->getMessage());

        return response()->json([
            'message' => 'Naa’y error sa pagkuha og AI recommendations.',
            'dorms' => [],
            'recommendations' => [],
        ], 500);
    }
}

}