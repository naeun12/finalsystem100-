<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\tenant\tenantModel;
use App\Models\landlord\landlordModel;
use App\Models\landlord\paymentVerifiedModel;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class admindashboardController extends Controller
{
    public function adminDashboardIndex($admin_id)
    {
        return view('admin.auth.admindashboard', [
            'title' => 'Admin - Dashboard',
            'admin_id' => $admin_id,
        ]);
    }

    public function getTotals()
{
    $totals = [
        'totalTenants'       => tenantModel::count(),
        'totalLandlords'     => landlordModel::count(),
        'totalCollection'    => paymentVerifiedModel::where('status', 'success')->sum('amount'),
    ];

    return response()->json($totals);
}


   public function getSubscribersTotal()
{
    // Total successful subscriptions
    $totalSubscribers = DB::table('landlordVerifiedPayment')
        ->where('status', 'success')
        ->count();

    // Total collection from successful payments
    $totalCollection = DB::table('landlordVerifiedPayment')
        ->where('status', 'success')
        ->sum('amount');

    return response()->json([
        'totalSubscribers' => $totalSubscribers,
        'totalCollection'  => $totalCollection,
    ]);
}

    public function generateSubscriptionReport()
{
    // Query subscriptions per month
    $subscriptions = DB::table('landlordVerifiedPayment')
        ->selectRaw("DATE_FORMAT(created_at, '%M %Y') as month, COUNT(*) as count, SUM(amount) as total_amount")
        ->where('status', 'success')
        ->groupByRaw("DATE_FORMAT(created_at, '%M %Y')")
        ->orderByRaw("MIN(created_at)")
        ->get();

    // Calculate grand total income
    $totalIncome = $subscriptions->sum('total_amount');

    $logoPath = public_path('images/Logo/logo.png');

    // Pass to view
    $pdf = Pdf::loadView('admin.auth.reports.subscription-report', compact('subscriptions','logoPath','totalIncome'));

    // Stream PDF
    return $pdf->stream("subscription_report.pdf");
}

}
