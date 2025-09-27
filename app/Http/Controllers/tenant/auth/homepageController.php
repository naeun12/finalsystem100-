<?php

namespace App\Http\Controllers\tenant\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordAccountModel;
use App\Models\landlord\roomModel;
use App\Models\notificationModel;
use App\Models\reviewandratingModel;
use App\Models\landlord\dormModel; 
use App\Models\tenant\tenantModel; 



class homepageController extends Controller
{
    public function homepage($tenant_id)
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
        return view('tenant.auth.homepage',['title' => 'Homepage  - Dormhub',
        'tenant_id',$tenant,'cssPath' => asset('css/tenantpage/auth/home.css')
        ,'notifications' => $notifications,
             'unread_count' => $unreadCount,]);

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
public function topRatedDorms()
{
  $dorms = dormModel::with('images')       // eager load images
    ->withAvg('reviews', 'rating')          // calculate average rating
    ->withCount('reviews')                  // count reviews
    ->orderByDesc('reviews_avg_rating')     // top-rated first
    ->take(5)                               // get top 5
    ->get()
    ->map(function($dorm) {
        return [
            'fkdormID' => $dorm->dormID,
            'dorm'     => $dorm,
            'avg_rating'=> $dorm->reviews_avg_rating ? floatval($dorm->reviews_avg_rating) : 0,
        ];
    });

    return response()->json($dorms);
}


}
