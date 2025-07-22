<?php

namespace App\Http\Controllers\tenant\auth\nav\nextpayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\bookingModel;
use App\Models\landlord\roomModel;
use App\Models\tenant\reservationModel;
use App\Models\tenant\tenantModel;
use App\Models\tenant\bookingpaymentModel;
use App\Models\tenant\reservationpaymentModel;
use Carbon\Carbon;


class nextpaymentController extends Controller
{
    public function nextpaymentIndex($tenant_id)
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
            return view('tenant.auth.nav.nextpayment.nextpaymentdue',['title' => 'next Payment Due',
            'tenant_id' => $tenant_id,
            'cssPath' => asset('css/tenantpage/auth/roomdetails.css')]);
        }
        public function paymentHistory($tenant_id)
    {
        $bookingBills = bookingModel::with(['payment', 'room'])
            ->where('fktenantID', $tenant_id)
            ->get();

        $reservationBills = reservationModel::with(['payment', 'room', 'dormitory'])
            ->where('fktenantID', $tenant_id)
            ->get();

        return response()->json([
            'booking_bills' => $bookingBills,
            'reservation_bills' => $reservationBills,
        ]);
    }
    public function getTotalAmount($tenantID)
{
    // Get booking payment total for this tenant
    $bookingTotal = bookingpaymentModel::whereHas('booking', function ($q) use ($tenantID) {
        $q->where('fktenantID', $tenantID);
    })->sum('paymentAmount');

    // Get reservation payment total for this tenant
    $reservationTotal = reservationpaymentModel::whereHas('reservation', function ($q) use ($tenantID) {
        $q->where('fktenantID', $tenantID);
    })->sum('paymentAmount');

    $total = $bookingTotal + $reservationTotal;

    return response()->json([
        'bookingTotal' => $bookingTotal,
        'reservationTotal' => $reservationTotal,
        'totalAmount' => $total,
        'totalDue' => 10000 
    ]);
}

}
