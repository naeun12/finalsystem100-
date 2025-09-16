<?php

namespace App\Http\Controllers\tenant\auth\nav\myroom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\roomModel;
use App\Models\tenant\tenantModel;
use App\Models\tenant\approvetenantsModel;
use App\Models\tenant\approvepaymentModel;
use Illuminate\Support\Facades\Validator;
use App\Models\notificationModel;
use App\Models\reviewandratingModel;
use App\Models\conversationModel;
use App\Models\messageModel;
use App\Events\MessageSent;

use Illuminate\Support\Facades\DB;


use PDF; // DOMPDF facade


class myroomsController extends Controller
{
    public function myroomsIndex($tenant_id)
        {
            $sessionTenant_id = session('tenant_id');
        
            if (!$sessionTenant_id) {
                return redirect()->route('tenant-login')->with('error', 'Please log in as a landlord.');
            }
        
            if ($tenant_id !== $sessionTenant_id) {
                return redirect()->route('tenant-login')->with('error', 'Unauthorized access.');
            }
            $notifications = notificationModel::where('receiverID', $sessionTenant_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            $unreadCount = notificationModel::where('receiverID', $tenant_id)
            ->where('isRead', false)
            ->count();
        
            $tenant = tenantModel::find($tenant_id);
            if (!$tenant) {
                return redirect()->route('tenant-login')->with('error', 'Landlord not found.');
            }
            $title = 'Tenant My Rooms - Dormhub';
            return view('tenant.auth.nav.rooms.myrooms',['title' => 'My Rooms',
            'tenant_id' => $tenant_id,
            'cssPath' => asset('css/tenantpage/auth/roomdetails.css'),
            'notifications' => $notifications,
             'unread_count' => $unreadCount,]);
        }

   public function myroomsList($tenant_id)
{
    $approvedTenants = approvetenantsModel::with([
        'room.dorm',
        'room.dorm.landlord',
        'room.features',
        'payments'
    ])
    ->where('fktenantID', $tenant_id)
    ->orderBy('created_at', 'desc')
    ->get();

    return response()->json([
        'rooms' => $approvedTenants
    ]);
}

public function extendRent(Request $request)
{
        $paymentOption = $request->input('paymentOption');
        $approveID = $request->input('approveID');
       $approve = approvetenantsModel::with('room.landlord','payments')->find($request->approveID);
        $payment = $approve->payments()->where('fkapprovedID',$request->approveID);
$landlord = $approve->room->landlord;
     $tenant = approvetenantsModel::with('room.landlord','room.dorm')->find($request->approveID);
        if ($paymentOption === 'online') {

    $validator = Validator::make($request->all(), [
        'paymentOption' => 'required|string|in:online,onsite',
        'approveID'    => 'required|integer|exists:approved_tenants,approvedID',
        'paymentType'  => 'required|string|max:50',
        'paymentImage' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'amount'       => 'required|numeric|min:1'
    ], [
        'approveID.required'    => 'Approve ID is required.',
        'paymentOption.required'    => 'Choose payment option',
        'approveID.exists'      => 'The selected approve ID does not exist.',
        'paymentType.required'  => 'Payment type is required.',
        'paymentImage.required' => 'Payment image is required.',
        'paymentImage.image'    => 'Payment image must be an image file.',
        'amount.required'       => 'Amount is required.',
        'amount.numeric'        => 'Amount must be a valid number.',
        'amount.min'            => 'Amount must be greater than 0.'
    ]);

    
    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    // 3️⃣ Save image
    $mainImageUrl = null;
    if ($request->hasFile('paymentImage')) {
        $image1 = $request->file('paymentImage');
        $image1Name = time() . '_1.' . $image1->getClientOriginalExtension();
        $image1->storeAs('public/uploads/roomImages', $image1Name);
        $mainImageUrl = asset('storage/uploads/roomImages/' . $image1Name);
    }
   
    approvepaymentModel::create([
        'fkapprovedID' => $request->approveID,
        'paymentType'  => $request->paymentOption,
        'amount'       => $request->amount,
        'status'       => 'pending',
        'paymentImage' => $mainImageUrl,
    ]);
    if (!$tenant) {
        return response()->json(['status' => 'error', 'message' => 'Tenant not found']);
    }

        $tenant->paymentOption = $paymentOption;
        $payment->update(['status' => 'pending']);
        $tenant->extension_decision = 'pending';
        $tenant->notifyRent = 1; 
        $tenant->extension_payment_status = 'pending';
        $tenant->save();

$notifications = notificationModel::create([
    'senderID'     => $approve->fktenantID,
    'senderType'   => 'tenant',
    'receiverID'   => $landlord->landlordID,
    'receiverType' => 'landlord',
    'title'        => 'Checked Tenant Extension Payment',
    'message'      => "A tenant has submitted payment for the rent extension of Room #{$approve->room->roomNumber}. Please review and confirm.",
    'isRead'       => false,
    'readAt'       => null,
]);
    broadcast(new \App\Events\NewNotificationEvent($notifications));
    // 5️⃣ Return success
    return response()->json([
        'status'  => 'success',
        'message' => 'Successfully paid extension, wait for landlord approval'
    ]);
}
else if($paymentOption === 'onsite')
{
    $tenant->paymentOption = $paymentOption;
    
        $tenant->extension_decision = 'pending';
        $tenant->notifyRent = 1; 
        $tenant->extension_payment_status = 'pending';
        $payment->update(['status' => 'pending']);
        $tenant->save();
   $notifications = notificationModel::create([
    'senderID'     => $approve->fktenantID,
    'senderType'   => 'tenant',
    'receiverID'   => $landlord->landlordID,
    'receiverType' => 'landlord',
    'title'        => 'Onsite Payment Chosen',
    'message'      => "The tenant has chosen onsite payment for the rent extension of Room #{$approve->room->roomNumber}. Please coordinate with the tenant directly for the payment.",
    'isRead'       => false,
    'readAt'       => null,
]);

    broadcast(new \App\Events\NewNotificationEvent($notifications));
     return response()->json([
        'status'  => 'success',
        'message' => 'You selected On-site Payment. Please proceed to your landlord to complete the payment.'
    ]);
}

}
public function generateReceipt($id)
    {
$tenant = approvetenantsModel::with(['room.dorm','room.landlord', 'payments'])->findOrFail($id);
    $totalPaid = $tenant->payments->sum('amount');
        $latestPayment = $tenant->payments->sortByDesc('created_at')->first();


        $data = [
            'tenant' => $tenant,
            'balance' => $tenant->room->price - ($tenant->amount ?? 0),
             'totalPaid' => $totalPaid,
             'paymentType'  => $latestPayment ? $latestPayment->paymentType : null

             

        ];

        $pdf = PDF::loadView('tenant.receipt.receipt', $data);

        return $pdf->stream('receipt.pdf'); 
    }
  public function ReviewAndRating(Request $request)
{
    $request->validate([
        'roomID' => 'required',
        'approvedID' => 'required',
        'rating' => 'required|integer|min:1|max:5',
        'review' => 'nullable|string|max:2000',
    ]);

    $room = \App\Models\landlord\roomModel::find($request->roomID);

    if (!$room) {
        return response()->json([
            'success' => false,
            'message' => 'Room not found.'
        ], 404);
    }

    $dormID = $room->fkdormID;
    $approvedID = $request->approvedID;

    // Check if this approved tenant already reviewed this dorm
    $existing = reviewandratingModel::where('fkdormID', $dormID)
                ->where('fkapprovedID', $approvedID)
                ->first();

    if ($existing) {
        return response()->json([
            'success' => false,
            'message' => 'You have already reviewed this dorm.'
        ], 409);
    }

    // Save new review
    reviewandratingModel::create([
        'fkdormID'   => $dormID,
        'fkapprovedID' => $approvedID,
        'rating'     => $request->rating,
        'review'     => $request->review,
    ]);
    $tenant = \App\Models\tenant\approvetenantsModel::find($request->approvedID);
    if ($tenant) {
        $tenant->has_rated = true; // i-set nga naka-rate na
        $tenant->save();
    }

    // Get updated stats
    $reviews = reviewandratingModel::where('fkdormID', $dormID)->get();
    $average = $reviews->avg('rating');
    $percentage = ($average / 5) * 100;

    return response()->json([
        'success'    => true,
        'message'    => 'Review submitted successfully.',
        'reviews'    => $reviews,
        'average'    => round($average, 2),
        'percentage' => $percentage
    ]);
}
 public function sendIssue(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'senderID' => 'required|string',
            'senderRole' => 'required|string|in:tenant,landlord',
             'receiverID' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $topic = "tenant_{$request->senderID}_landlord_{$request->receiverID}";
            $conversation = conversationModel::where('topic', $topic)->first();

            // ✅ Check if a conversation already exists for this tenant and landlord
            if (!$conversation) {
                $conversation = conversationModel::create([
                    'initiatorID'   => $request->senderID,
                    'initiatorRole' => $request->senderRole,
                    'topic'         => $topic,
                ]);
            }

            // ✅ Create the message
            $message = messageModel::create([
                'conversationID' => $conversation->id,
                'senderID' => $request->senderID,
                'senderRole' => $request->senderRole,
                'receiverID' => $request->receiverID,
                'receiverRole' => 'landlord',
                'message' => $request->message,
                'replyToId' => null,
                'isRead' => false,
                'sentAt' => now(),
            ]);
            broadcast(new MessageSent($message))->toOthers();
            $notifications = notificationModel::create([
            'senderID'     => $message->senderID,
            'senderType'   => 'tenant',
            'receiverID'   => $message->receiverID,
            'receiverType' => 'landlord',
            'title'        => 'Tenant sent report',
            'message'      => "A tenant has sent you a report",
            'isRead'       => false,
            'readAt'       => null,
            ]);
                    broadcast(new \App\Events\NewNotificationEvent($notifications));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Issue sent successfully!',
                'conversationID' => $conversation->id,
                'messageID' => $message->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to send issue: ' . $e->getMessage(),
            ], 500);
        }
    }
public function notifyrentUpdate(Request $request)
{
    $request->validate([
        'approveID' => 'required|integer',
        'decision'    => 'required|string',
    ]);

    $tenant = approvetenantsModel::with('room.landlord','room.dorm','payments')->find($request->approveID);
    $payment = $tenant->payments()->where('fkapprovedID',$request->approveID)->first();
    if (!$tenant) {
        return response()->json(['status' => 'error', 'message' => 'Tenant not found']);
    }

    // Update status
    $tenant->notifyRent = false; // or 0
        $tenant->extension_decision = $request->decision; 
        $tenant->save();

    // Send notification to landlord
    $notification = notificationModel::create([
        'senderID'     => $tenant->fktenantID,   // tenant ID as sender
        'senderType'   => 'tenant',
        'receiverID'   => $tenant->room->landlord->landlordID, // landlord as receiver
        'receiverType' => 'landlord',
        'title'        => 'Extension Decision',
        'message'      => 'Tenant ' . $tenant->firstname . ' ' . $tenant->lastname . 
                          ' has ' . $request->desicion . ' the rent extension request for room ' .
                          $tenant->room->roomNumber . ' at ' . $tenant->room->dorm->dormName . '.',
        'isRead'       => false,
        'readAt'       => null,
    ]);

    // Broadcast the new notification
    broadcast(new \App\Events\NewNotificationEvent($notification));

    return response()->json([
        'status'  => 'success',
        'message' => 'Rent status updated and landlord notified'
    ]);
}


}
