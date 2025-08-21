<?php

namespace App\Http\Controllers\tenant\auth\nav\booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\bookingModel;
use App\Models\landlord\roomModel;
use App\Models\tenant\tenantModel;
use App\Models\notificationModel;

class mybookingController extends Controller
{
    public function viewBooking($tenant_id)
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
            $title = 'Tenant Booking List - Dormhub';
            return view('tenant.auth.nav.bookings.bookingdormitory',['title' => $title,
            'tenant_id' => $tenant_id,
            'cssPath' => asset('css/tenantpage/auth/roomdetails.css'),
               'notifications' => $notifications,
             'unread_count' => $unreadCount,]);
        }
       public function mybookingList($tenant_id)
        {
            $bookings = bookingModel::with(['tenant','room.dorm' // nested eager load
])
                ->where('fktenantID', $tenant_id)
                ->orderBy('updated_at','desc')
                ->get();

            return response()->json($bookings);
        }
}
