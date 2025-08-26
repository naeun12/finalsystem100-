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
use App\Models\tenant\approvetenantsModel;
use App\Models\tenant\approvepaymentModel;
use App\Models\notificationModel;
use Carbon\Carbon;


class nextpaymentController extends Controller
{
    public function nextpaymentIndex($tenant_id)
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
            $title = 'Tenant Payments History - Dormhub';
            return view('tenant.auth.nav.nextpayment.nextpaymentdue',['title' => $title,
            'tenant_id' => $tenant_id,
            'cssPath' => asset('css/tenantpage/auth/roomdetails.css'),
             'notifications' => $notifications,
             'unread_count' => $unreadCount,]);
        }

public function paymentHistory(Request $request, $tenant_id)
{
    $method = $request->query('paymentMethod'); // example: 'gcash'
    $chooseDate = $request->query('chooseDate'); // format: 'YYYY-MM-DD'

    // Booking bills
    $booking = bookingModel::where('fktenantID', $tenant_id)->get();

    $bookingBills = bookingpaymentModel::with('booking.room')
        ->whereIn('fkbookingID', $booking->pluck('bookingID'))
        ->when($method, function ($query) use ($method) {
            return $query->where('paymentType', $method);
        })
        ->when($chooseDate, function ($query) use ($chooseDate) {
            return $query->whereDate('created_at', $chooseDate);
        })
        ->orderBy('created_at', 'desc')
        ->get();

    // Reservation bills
    $reservation = reservationModel::where('fktenantID', $tenant_id)->get();

    $reservationBills = reservationpaymentModel::with('reservation.room')
        ->whereIn('reservationID', $reservation->pluck('reservationID'))
        ->when($method, function ($query) use ($method) {
            return $query->where('paymentType', $method);
        })
        ->when($chooseDate, function ($query) use ($chooseDate) {
            return $query->whereDate('created_at', $chooseDate);
        })
        ->orderBy('created_at', 'desc')
        ->get();

    // Approve bills
    $approve = approvetenantsModel::where('fktenantID', $tenant_id)->get();

    $approvetenantBills = approvepaymentModel::with('approvedTenant.room')
        ->whereIn('fkapprovedID', $approve->pluck('approvedID'))
        ->when($method, function ($query) use ($method) {
            return $query->where('paymentType', $method);
        })
        ->when($chooseDate, function ($query) use ($chooseDate) {
            return $query->whereDate('created_at', $chooseDate);
        })
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'booking_bills'     => $bookingBills,
        'reservation_bills' => $reservationBills,
        'approve_bill'      => $approvetenantBills
    ]);
}


    public function getTotalAmount(Request $request, $tenantID)
{
    $paymentMethod = $request->query('method');
    $chooseDate = $request->query('chooseDate'); // gikan sa axios

    // Booking total
    $bookingTotal = bookingpaymentModel::whereHas('booking', function ($q) use ($tenantID) {
        $q->where('fktenantID', $tenantID);
    })
    ->when($paymentMethod, function ($query) use ($paymentMethod) {
        return $query->where('paymentType', $paymentMethod);
    })
    ->when($chooseDate, function ($query) use ($chooseDate) {
        return $query->whereDate('created_at', $chooseDate);
    })
    ->sum('amount');

    // Reservation total
    $reservationTotal = reservationpaymentModel::whereHas('reservation', function ($q) use ($tenantID) {
        $q->where('fktenantID', $tenantID);
    })
    ->when($paymentMethod, function ($query) use ($paymentMethod) {
        return $query->where('paymentType', $paymentMethod);
    })
    ->when($chooseDate, function ($query) use ($chooseDate) {
        return $query->whereDate('created_at', $chooseDate);
    })
    ->sum('amount');

    // Approved total
    $approve = approvepaymentModel::whereHas('approvedTenant', function ($q) use ($tenantID) {
        $q->where('fktenantID', $tenantID);
    })
    ->when($paymentMethod, function ($query) use ($paymentMethod) {
        return $query->where('paymentType', $paymentMethod);
    })
    ->when($chooseDate, function ($query) use ($chooseDate) {
        return $query->whereDate('created_at', $chooseDate);
    })
    ->sum('amount');

    $total = $bookingTotal + $reservationTotal + $approve;

    return response()->json([
        'bookingTotal' => $bookingTotal,
        'reservationTotal' => $reservationTotal,
        'approveTotal' => $approve,
        'totalAmount' => $total,
        'totalDue' => 10000,
    ]);
}


}
