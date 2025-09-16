<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\tenant\tenantModel;
use App\Models\landlord\landlordModel;
use App\Models\landlord\paymentVerifiedModel;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'totalTenants'     => tenantModel::count(),
            'totalLandlords'   => landlordModel::count(),
            'totalCollection'  => paymentVerifiedModel::where('status', 'success')->sum('amount'),
        ];

        return response()->json($totals);
    }

    public function getSubscribersPerMonth()
    {
        $data = DB::table('landlordVerifiedPayment')
            ->selectRaw("DATE_FORMAT(created_at, '%b') as month, COUNT(*) as count")
            ->where('status', 'success') // make consistent with getTotals()
            ->whereBetween('created_at', [now()->subMonths(6), now()])
            ->groupByRaw("DATE_FORMAT(created_at, '%b')")
            ->orderByRaw("MIN(created_at)")
            ->get();

        return response()->json($data);
    }
    public function generateSubscriptionReport()
{
    // Query subscriptions per month
    $subscriptions = DB::table('landlordVerifiedPayment')
        ->selectRaw("DATE_FORMAT(created_at, '%M %Y') as month, COUNT(*) as count")
        ->where('status', 'success')
        ->groupByRaw("DATE_FORMAT(created_at, '%M %Y')")
        ->orderByRaw("MIN(created_at)")
        ->get();
    $logoPath = public_path('images/Logo/logo.png');

    // Pass to view
    $pdf = Pdf::loadView('admin.auth.reports.subscription-report', compact('subscriptions','logoPath'));

    // Download as PDF
    return $pdf->download('subscription_report.pdf');
}
}
