<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\tenant\tenantModel;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminTenantEmail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class admintenantManagementController extends Controller
{
     public function admintenantManagemnentIndex($admin_id)
    {
        return view('admin.auth.tenantmanagement', [
            'title' => 'Admin - Tenant Management',
            'admin_id' => $admin_id,
        ]);
    }
    public function getTenants()
    {
        $tenants = tenantModel::orderBy('created_at','desc')->paginate(10); // 10 tenants per page
        return response()->json($tenants);
    }
    public function viewTenantProfile($tenant_id)
    {
        $tenant = tenantModel::where('tenantID',$tenant_id)->first();
        return response()->json($tenant);

    }
    public function deactivatetenantAccount($tenant_id)
    {
         $tenant = tenantModel::where('tenantID',$tenant_id)->first();
         $tenant->is_deactivated = 1;
         $tenant->save();
          $tenantName = $tenant->firstname . ' ' . $tenant->lastname;
          $messageBody = "Your tenant account has been deactivated. Please contact DormHub support if you believe this is an error.";

    // Send email
    Mail::to($tenant->email)->send(new AdminTenantEmail($tenantName, $messageBody));
        return response()->json([
            'status'  => 'success',
            'message' => 'Tenant account successfully deactivated',
            'tenant'  => $tenant
        ]);


    }
    public function reactivetenantAccount($tenant_id)
    {
         $tenant = tenantModel::where('tenantID',$tenant_id)->first();
         $tenant->is_deactivated = 0;
         $tenant->save();
           $tenantName = $tenant->firstname . ' ' . $tenant->lastname;
    $messageBody = "Your tenant account has been reactivated successfully. You can now log in to DormHub.";

    // Send the email
    Mail::to($tenant->email)->send(new AdminTenantEmail($tenantName, $messageBody));
        return response()->json([
            'status'  => 'success',
            'message' => 'Tenant account successfully reactivated',
            'tenant'  => $tenant
        ]);
    }
public function sendTenantEmail(Request $request)
{
    $request->validate([
        'to' => 'required|email',
        'subject' => 'required|string',
        'message' => 'required|string',
    ]);

    $emailData = $request->only('to','subject','message');

    // Pass two arguments to the Mailable
    Mail::to($emailData['to'])->send(new AdminTenantEmail(
        $emailData['subject'],  // this will be used as tenantName or title
        $emailData['message']   // this will be messageBody
    ));

    return response()->json([
        'status' => 'success',
        'message' => 'Email sent successfully'
    ]);
}
public function downloadTenantReport()
{
    // Query subscriptions per month
    $tenants = tenantModel::orderBy('created_at','asc')->get();   
    $logoPath = public_path('images/Logo/logo.png');

    // Pass to view
    $pdf = Pdf::loadView('admin.auth.reports.tenant-report', compact('tenants','logoPath'));

    // Download as PDF
    return $pdf->download('tenant_report.pdf');
}
public function searchTenants(Request $request)
{
    $searchTerm = $request->query('q'); // get ?q= from URL

    $tenants = tenantModel::query()
        ->where('firstname', 'like', "%{$searchTerm}%")
        ->orWhere('lastname', 'like', "%{$searchTerm}%")
        ->orderBy('firstname', 'asc')
        ->paginate(10);

    return response()->json([
        'status' => 'success',
        'data' => $tenants
    ]);
}


}
