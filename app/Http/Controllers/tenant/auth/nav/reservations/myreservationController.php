<?php

namespace App\Http\Controllers\tenant\auth\nav\reservations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tenant\reservationModel;
use App\Models\tenant\tenantModel;

class myreservationController extends Controller
{
    public function viewReservation($tenant_id)
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
            $title = 'Tenant room Details - Dormhub';
            return view('tenant.auth.nav.reservations.reservation',['title' => 'Reservations',
            'tenant_id' => $tenant_id,
            'cssPath' => asset('css/tenantpage/auth/roomdetails.css')]);
        }
        public function myReservationList($tenant_id)
        {
            $bookings = reservationModel::with(['tenant','room.dorm' // nested eager load
])
                ->where('fktenantID', $tenant_id)
                ->get();

            return response()->json($bookings);
        }
}
