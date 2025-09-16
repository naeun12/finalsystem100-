<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\landlord\landlordModel;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminTenantEmail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class adminlandlordManagementController extends Controller
{
    public function landlordManagementIndex($admin_id)
    {
        return view('admin.auth.landlordmanagement', [
            'title' => 'Admin - Landlord Management',
            'admin_id' => $admin_id,
        ]);
    }
     public function getLandlords()
    {
        $landlords = landlordModel::orderBy('created_at','desc')->paginate(10); // 10 tenants per page
        return response()->json($landlords);
    }
    public function viewLandlordProfile($lanlord_id)
    {
        $landlord = landlordModel::where('landlordID',$lanlord_id)->first();
        return response()->json($landlord);

    }
    
    public function deactivateLandlordAccount($landlord_id)
    {
         $landlord = landlordModel::where('landlordID',$landlord_id)->first();
         $landlord->is_deactivated = 1;
         $landlord->save();
          $landlordName = $landlord->firstname . ' ' . $landlord->lastname;
          $messageBody = "Your Landlord account has been deactivated. Please contact DormHub support if you believe this is an error.";

    // Send email
    Mail::to($landlord->email)->send(new AdminTenantEmail($landlordName, $messageBody));
        return response()->json([
            'status'  => 'success',
            'message' => 'Landlord account successfully deactivated',
            'landlord'  => $landlord
        ]);


    }
    public function reactivelandlordAccount($landlord_id)
    {
         $landlord = landlordModel::where('landlordID',$landlord_id)->first();
         $landlord->is_deactivated = 0;
         $landlord->save();
           $landlordName = $landlord->firstname . ' ' . $landlord->lastname;
    $messageBody = "Your landlord account has been reactivated successfully. You can now log in to DormHub.";

    // Send the email
    Mail::to($landlord->email)->send(new AdminTenantEmail($landlordName, $messageBody));
        return response()->json([
            'status'  => 'success',
            'message' => 'Landlord account successfully reactivated',
            'landlord'  => $landlord
        ]);
    }
public function sendLandlordEmail(Request $request)
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
public function downloadLandlordReport()
{
    // Query subscriptions per month
    $landlords = landlordModel::orderBy('created_at','asc')->get();   
    $logoPath = public_path('images/Logo/logo.png');

    // Pass to view
    $pdf = Pdf::loadView('admin.auth.reports.landlord-report', compact('landlords','logoPath'));

    // Download as PDF
    return $pdf->download('landlord_report.pdf');
}
public function searchLandlords(Request $request)
{
    $searchTerm = $request->query('q'); // get ?q= from URL

    $landlords = landlordModel::query()
        ->where('firstname', 'like', "%{$searchTerm}%")
        ->orWhere('lastname', 'like', "%{$searchTerm}%")
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return response()->json([
        'status' => 'success',
        'data' => $landlords
    ]);
}



}
