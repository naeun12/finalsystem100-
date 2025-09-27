<?php

namespace App\Http\Controllers\landlord\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordModel;
use App\Models\landlord\roomModel;
use App\Models\landlord\bookingModel;
use App\Models\tenant\approvetenantsModel;
use App\Models\tenant\reservationModel;
use App\Models\notificationModel;
use Carbon\Carbon;

use App\Models\landlord\dormModel;
use Barryvdh\DomPDF\Facade\Pdf;



class dashboardController extends Controller
{
     public function landlordDashboard($landlord_id)
    {
          $sessionLandlordId = session('landlord_id');
            $notifications = notificationModel::where('receiverID', $sessionLandlordId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        if (!$sessionLandlordId) {
            return redirect()->route('loginLandlord')->with('error', 'Please log in as a landlord.');
        }
    
        if ($landlord_id !== $sessionLandlordId) {
            return redirect()->route('loginLandlord')->with('error', 'Unauthorized access.');
        }
    
        $landlord = landlordModel::find($landlord_id);
        if (!$landlord) {
            return redirect()->route('loginLandlord')->with('error', 'Landlord not found.');
        }
        $unreadCount = notificationModel::where('receiverID', $landlord_id)
            ->where('isRead', false)
            ->count();
    
        return view ('landlord.auth.dashboard', ['title' => 'Landlord - Dashboard',
        'headerName' => 'Dashboard',
        'color' =>'primary'
        ,'landlord_id'=> $landlord_id,
        'notifications' => $notifications,
        'unread_count' => $unreadCount
    ]);
}
public function getLandlord(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
$date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();
    $landlord = landlordModel::find($landlord_id);

    if (!$landlord) {
        return response()->json([
            'status' => 'error',
            'message' => 'Landlord not found.'
        ], 404);
    }

    return response()->json([
        'status' => 'success',
        'landlord' => $landlord
    ]);
}
public function getTotalTenants(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
    $date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();

    $totalTenants = approvetenantsModel::whereHas('room', function ($query) use ($landlord_id) {
        $query->where('fklandlordID', $landlord_id);
    })
    ->where('status','<>','moved_out')
    ->where('status','<>','pending')
    ->whereDate('created_at', '<=', $date)
    ->count();

    return response()->json([
        'status' => 'success',
        'total_tenants' => $totalTenants
    ]);
}
public function availableBeds(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
    $date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();
    
    // If you want to filter by month instead of specific date
    $startOfMonth = $date->copy()->startOfMonth();
    $endOfMonth = $date->copy()->endOfMonth();

    // All rooms of the landlord marked as Available
    $roomsQuery = roomModel::where('fklandlordID', $landlord_id)
        ->where('availability', 'Available');

    // Booked rooms during that month (overlapping with the month at all)
    $bookedRoomIds = approvetenantsModel::whereHas('room', function ($query) use ($landlord_id) {
        $query->where('fklandlordID', $landlord_id);
    })
    ->where(function($query) use ($startOfMonth, $endOfMonth) {
        $query->where(function($q) use ($startOfMonth, $endOfMonth) {
            // Booking starts before or during month and ends after or during month
            $q->where('moveInDate', '<=', $endOfMonth)
              ->where('moveOutDate', '>=', $startOfMonth);
        });
    })
    ->pluck('fkroomID')
    ->toArray();

    // Count available rooms not booked during that month
    $availableRoomsCount = $roomsQuery
        ->whereNotIn('roomID', $bookedRoomIds)
        ->count();

    return response()->json([
        'status' => 'success',
        'available_beds' => $availableRoomsCount,
        'debug' => [
            'date' => $date,
            'month_start' => $startOfMonth,
            'month_end' => $endOfMonth,
            'bookedRoomIds' => $bookedRoomIds,
            'allAvailableRooms' => $roomsQuery->pluck('roomID')->toArray(),
        ]
    ]);
}



public function getReservationList(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
$date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();
    $reservations = reservationModel::with(['room']) // eager load room data
        ->whereHas('room', function ($query) use ($landlord_id) {
            $query->where('fklandlordID', $landlord_id);
        })
        ->whereDate('created_at', '<=', $date)
        ->orderBy('created_at', 'desc') // optional: show latest first
        ->get();

    return response()->json([
        'status' => 'success',
        'reservations' => $reservations
    ]);
}
public function getBookingList(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
    $date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();

    $bookings = bookingModel::with('room') // eager load room info
        ->whereHas('room', function ($query) use ($landlord_id) {
            $query->where('fklandlordID', $landlord_id);
        })
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'status' => 'success',
        'bookings' => $bookings
    ]);
}

public function getDormProfits(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
    $date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();

    $dormProfits = dormModel::with(['rooms' => function ($query) use ($date) {
        $query->where('availability', 'Occupied')
              ->whereDate('created_at', '<=', $date);
    }])
    ->where('fklandlordID', $landlord_id)
    ->get()
    ->map(function ($dorm) {
        $totalProfit = $dorm->rooms->sum('price');
        return [
            'dormName' => $dorm->dormName,
            'profit' => $totalProfit,
        ];
    });

    $dormProfitsCollection = collect($dormProfits);
    $total = $dormProfitsCollection->sum('profit');

    return response()->json([
        'status' => 'success',
        'total_profit' => $total,
        'data' => $dormProfits
    ]);
}
public function getallprofits(Request $request, $landlord_id)
{
    $dateParam = $request->query('date');
    $date = $dateParam ? \Carbon\Carbon::parse($dateParam) : \Carbon\Carbon::today();

    // --- 1. Approved tenants payments ---
    $approved = \App\Models\tenant\approvepaymentModel::with(['approvedTenant.room.dorm'])
        ->whereHas('approvedTenant.room.dorm', fn($q) => $q->where('fklandlordID', $landlord_id))
        ->whereDate('created_at', '<=', $date)
        ->get()
        ->groupBy(fn($p) => optional(optional(optional($p->approvedTenant)->room)->dorm)->dormName ?? 'Unknown Dorm')
        ->map(fn($g) => [
            'dormName' => optional(optional(optional($g->first()?->approvedTenant)->room)->dorm)->dormName ?? 'Unknown Dorm',
            'totalProfit' => collect($g)->sum(fn($x) => (float) $x->amount) // ✅ wrap in collect()
        ]);

    // --- 2. Reservation payments ---
    $reservations = \App\Models\tenant\reservationpaymentModel::with(['reservation.room.dorm'])
        ->whereHas('reservation.room.dorm', fn($q) => $q->where('fklandlordID', $landlord_id))
        ->whereDate('created_at', '<=', $date)
        ->get()
        ->groupBy(fn($p) => optional(optional(optional($p->reservation)->room)->dorm)->dormName ?? 'Unknown Dorm')
        ->map(fn($g) => [
            'dormName' => optional(optional(optional($g->first()?->reservation)->room)->dorm)->dormName ?? 'Unknown Dorm',
            'totalProfit' => collect($g)->sum(fn($x) => (float) $x->amount) // ✅ wrap in collect()
        ]);

    // --- 3. Booking payments ---
    $bookings = \App\Models\tenant\bookingpaymentModel::with(['booking.room.dorm'])
        ->whereHas('booking.room.dorm', fn($q) => $q->where('fklandlordID', $landlord_id))
        ->whereDate('created_at', '<=', $date)
        ->get()
        ->groupBy(fn($p) => optional(optional(optional($p->booking)->room)->dorm)->dormName ?? 'Unknown Dorm')
        ->map(fn($g) => [
            'dormName' => optional(optional(optional($g->first()?->booking)->room)->dorm)->dormName ?? 'Unknown Dorm',
            'totalProfit' => collect($g)->sum(fn($x) => (float) $x->amount) // ✅ wrap in collect()
        ]);

    // --- Merge all results ---
    $profits = collect()
        ->merge($approved)
        ->merge($reservations)
        ->merge($bookings)
        ->groupBy('dormName')
        ->map(fn($groups, $dormName) => [
            'dormName' => $dormName,
            'totalProfit' => collect($groups)->sum('totalProfit') // ✅ wrap in collect()
        ])
        ->values()
        ->toArray();

    $totalProfit = collect($profits)->sum('totalProfit');

    return response()->json([
        'status' => 'success',
        'total_profit' => $totalProfit,
        'data' => $profits
    ]);
}
public function generateFullReport($landlordID,Request $request)
{
        $selectedDate = $request->query('date'); // gets ?date=YYYY-MM-DD

    // Fetch reservations with related dorm info and payment
    $reservations = reservationModel::with(['room.dorm','payment'])
        ->where('status', 'approved')
        ->whereHas('room', fn($query) => $query->where('fklandlordID', $landlordID))
        ->whereHas('payment')
        ->when($selectedDate, fn($q) => $q->whereDate('created_at', '<=', $selectedDate))
        ->get();

    // Add total amount per reservation
    $reservations->each(function($r) {
        $r->total_amount = $r->payment->sum('amount');
    });

    // Fetch bookings with related dorm info and payment
    $bookings = bookingModel::with(['room.dorm','payment'])
        ->where('status', 'approved')
        ->whereHas('room', fn($query) => $query->where('fklandlordID', $landlordID))
        ->whereHas('payment')
        ->when($selectedDate, fn($q) => $q->whereDate('created_at', '<=', $selectedDate))

        ->get();

    // Add total amount per booking
    $bookings->each(function($b) {
        $b->total_amount = $b->payment->sum('amount');
    });

    // Calculate total income from reservations + bookings
    $totalIncome = $reservations->sum('total_amount') + $bookings->sum('total_amount');
    $logoPath = public_path('images/Logo/logo.png');

    // Prepare report data array
    $reportData = [
        'reservations' => $reservations,
        'bookings'     => $bookings,
        'totalIncome'  => $totalIncome,
        'landlordID'   => $landlordID,
'reportDate' => Carbon::now('Asia/Manila')->format('F d, Y h:i A'),
        'logoPath'     => $logoPath,
    ];

    // Generate and stream PDF
    $pdf = PDF::loadView('landlord.reports.full-report', $reportData);
    return $pdf->stream("landlord-full-report-{$landlordID}.pdf");
}





}
