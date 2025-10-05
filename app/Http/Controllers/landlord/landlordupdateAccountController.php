<?php

namespace App\Http\Controllers\landlord;

use App\Http\Controllers\Controller;
use App\Models\landlord\landlordModel;
use Illuminate\Http\Request;
use App\Models\notificationModel;

class landlordupdateAccountController extends Controller
{
    public function updateAccountIndex(Request $request, $landlord_id)
    {
        $landlord = landlordModel::find($landlord_id);
        if (!$landlord) {
            return redirect()->back()->with('error', 'Landlord not found.');
        }
    
        $sessionLandlordId = session('landlord_id');
     $notifications = notificationModel::where('receiverID', $sessionLandlordId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    $unreadCount = notificationModel::where('receiverID', $landlord_id)
            ->where('isRead', false)
            ->count();
        return view('landlord.auth.landlordaccountupdate',[
            "title" => "Landlord - Update Account",
            'headerName' => 'Update Account',
            'landlord' => $landlord,
            'landlord_id' => $sessionLandlordId,
            'id' =>$sessionLandlordId,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
    ]);   
    }
  public function getlandlordData($landlord_id)
{
    if (!$landlord_id) {
        return response()->json([
            'status' => 'error',
            'message' => 'Landlord ID not found.'
        ], 400);
    }

$landlord = landlordModel::find($landlord_id);

 
        return response()->json([
            'status' => 'success',
            'landlord' => $landlord,
            'landlordProfile' => $landlord->profilePicUrl
        ]);
    
}
public function updatelandlordAccount(Request $request, $landlord_id)
{
    $landlord = landlordModel::find($landlord_id);

    if (!$landlord) {
        return response()->json([
            'status' => 'error',
            'message' => 'Landlord not found.'
        ], 404);
    }

    $request->validate([
        'firstname'     => 'required|string|max:255',
        'lastname'      => 'required|string|max:255',
        'gender'        => 'nullable|string',
        'profilePicUrl' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ], [
        'firstname.required' => 'Please enter the firstname.',
        'lastname.required'  => 'Please enter the lastname.',
    ]);

    // Update basic info
    $landlord->firstname = $request->firstname;
    $landlord->lastname  = $request->lastname;
    $landlord->gender    = $request->gender;

    // Update profile picture only if a new file is uploaded
    if ($request->hasFile('profilePicUrl')) {
        $image = $request->file('profilePicUrl');
        $imageName = time() . '_profile.' . $image->getClientOriginalExtension();
        $image->storeAs('public/uploads/profilePic', $imageName);
        $landlord->profilePicUrl = 'storage/uploads/profilePic/' . $imageName;
    }

    $landlord->save();

    return response()->json([
        'status'   => 'success',
        'message'  => 'Landlord account updated successfully.',
        'landlord' => $landlord
    ]);
}


public function updateDocuments(Request $request, $landlord_id)
{
    $landlord = landlordModel::findOrFail($landlord_id);

    $request->validate([
        'businessPermit' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'governmentID'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Business Permit
    if ($request->hasFile('businessPermit')) {
        $file = $request->file('businessPermit');
        $filename = time() . '_business_permit.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/landlords'), $filename);
        $landlord->businessPermit = 'uploads/landlords/' . $filename;
    }

    // Government ID
    if ($request->hasFile('governmentID')) {
        $file = $request->file('governmentID');
        $filename = time() . '_government_id.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/landlords'), $filename);
        $landlord->govermentID = 'uploads/landlords/' . $filename;
    }

    $landlord->save();

    return response()->json([
        'status'   => 'success',
        'message'  => 'Documents updated successfully.',
        'landlord' => $landlord
    ]);
}



}
