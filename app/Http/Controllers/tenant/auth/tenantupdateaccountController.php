<?php

namespace App\Http\Controllers\tenant\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\notificationModel;
use App\Models\tenant\tenantModel; 

class tenantupdateaccountController extends Controller
{
    public function tenantaccountUpdate($tenant_id)
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
        return view('tenant.auth.tenantupdateaccount',['title' => 'Tenant update account  - Dormhub',
        'tenant_id',$tenant,'cssPath' => asset('css/tenantpage/auth/dormitorymap.css')
        ,'notifications' => $notifications,
        'unread_count' => $unreadCount,
        'demo' => $sessionTenant_id,
        'tenant_id' => $tenant_id,
    ]);
    }
    public function getTenantData($tenant_id)
{
    if (!$tenant_id) {
        return response()->json([
            'status' => 'error',
            'message' => 'Landlord ID not found.'
        ], 400);
    }

$tenant = tenantModel::find($tenant_id);

 
        return response()->json([
            'status' => 'success',
            'tenant' => $tenant,
            'tenantProfile' => $tenant->profile_pic_url
        ]);
    
}
public function updatetenantAccount(Request $request,$tenant_id)
{
    $tenant = tenantModel::find($tenant_id);
  $request->validate([
    'firstname'   => 'required|string|max:255',
    'lastname'    => 'required|string|max:255',
    'gender'      => 'nullable|string',
    'profilePicUrl' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    
], [
    'firstname.required'   => 'Please enter the firstname.',
    'lastname.required'    => 'Please enter the lastname.',
]);
            $profilePicPath = null;
           if ($request->hasFile('profilePicUrl')) {
            $image = $request->file('profilePicUrl');
            $imageName = time() . '_profile.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('public/uploads/profilePic', $imageName);
            $profilePicPath = 'storage/uploads/profilePic/' . $imageName;
          }

    $tenant->firstname   = $request->firstname;
    $tenant->lastname    = $request->lastname;
    $tenant->gender      = $request->gender;
    $tenant->profilePicUrl = $profilePicPath;
    $tenant->save();

    return response()->json([
        'status'   => 'success',
        'message'  => 'Your account updated successfully.',
        'tenant' => $tenant
    ]);

}



}
