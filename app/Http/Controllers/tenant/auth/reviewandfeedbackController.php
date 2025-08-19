<?php

namespace App\Http\Controllers\tenant\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\notificationModel;
use App\Models\landlord\dormModel;
use App\Models\tenant\tenantModel;
use App\Models\reviewandratingModel;

class reviewandfeedbackController extends Controller
{
    public function reviewandrating($dormitory_id,$tenant_id)
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
                $dorm = dormModel::find($dormitory_id);

                $dorm->increment('views');

        
            $tenant = tenantModel::find($tenant_id);
            if (!$tenant) {
                return redirect()->route('tenant-login')->with('error', 'Landlord not found.');
            }
            return view('tenant.auth.ratingandreview',['title' => 'Review and Rating - Dormhub ',
            'dormitory_id' => $dormitory_id,
            'tenant_id' => $tenant_id,
            'tenant' => $tenant,
            'cssPath' => asset('css/tenantpage/auth/roomdetails.css'),
            'notifications' => $notifications,
             'unread_count' => $unreadCount,]);
        }
        
      public function fetchreviewandrating($dormitory_id)
{
    // Get all reviews for the dormitory with tenant relationship
    $reviews = reviewandratingModel::with('tenant')
        ->where('fkdormID', $dormitory_id)
          ->where('review', '!=', '')
        ->latest()
        ->get();

    // Calculate average rating
    $averageRating = round($reviews->avg('rating'), 1);

    // Total reviewers
    $totalReviewers = $reviews->count();

    // Star distribution
    $starRatings = [];
    for ($i = 5; $i >= 1; $i--) {
        $count = $reviews->where('rating', $i)->count();
        $percentage = $totalReviewers > 0 ? round(($count / $totalReviewers) * 100, 2) : 0;

        $starRatings[] = [
            'stars' => $i,
            'count' => $count,
            'percentage' => $percentage
        ];
    }

    return response()->json([
        'averageRating'   => $averageRating,
        'totalReviewers'  => $totalReviewers,
        'starRatings'     => $starRatings,
        'reviews'         => $reviews
    ]);
}

}
