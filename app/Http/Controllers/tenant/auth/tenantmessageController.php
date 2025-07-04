<?php

namespace App\Http\Controllers\tenant\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use  App\Models\tenant\messageModel;

class tenantmessageController extends Controller
{
    public function tenantmessageIndex($tenant_id)
    {
        $sessionTenant_id = session('tenant_id');

        return view('tenant.auth.tenantmessage',['title'=>'Message','cssPath'=>'']);
    }
    public function landlordtenantmessageIndex($tenant_id, $landlord_id)
{
    return view('tenant.auth.tenantmessage', [
        'title' => 'Message',
        'cssPath' => '',
        'tenant_id' => $tenant_id,
        'landlord_id' => $landlord_id,
    ]);
}

    public function sendMessage(Request $request)
    {
        $tenantId = $request->tenant_id ?? session('tenant_id'); // from Vue or session fallback
    
        $request->validate([
            'receiver_id' => 'required|uuid',
            'message' => 'required|string',
            'conversation_id' => 'nullable|integer',
            'reply_to_id' => 'nullable|integer',
        ]);
        $message = messageModel::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => $tenantId,
            'sender_role' => 'tenant',
            'receiver_id' => $request->receiver_id,
            'receiver_role' => 'landlord',
            'message' => $request->message,
            'reply_to_id' => $request->reply_to_id,
            'is_read' => false,
            'sent_at' => now(),
        ]);
    
        return response()->json(['status' => 'success', 'message' => 'Message sent.']);
    }
    public function getMessages($tenant_id, $receiver_id)
    {
        $messages = messageModel::where(function ($query) use ($tenant_id, $receiver_id) {
            $query->where('sender_id', $tenant_id)
                  ->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($tenant_id, $receiver_id) {
            $query->where('sender_id', $receiver_id)
                  ->where('receiver_id', $tenant_id);
        })
        ->orderBy('sent_at', 'asc')
        ->get();
        return response()->json($messages);
    }
    public function getLandlordInformation(Request $request)
{
    $landlordId = $request->input('landlord');

    $landlord = \App\Models\landlord\landlordAccountModel::where('landlord_id', $landlordId)->first();

    if (!$landlord) {
        return response()->json(['message' => 'Landlord not found'], 404);
    }

    return response()->json([
        'landlord_id' => $landlord->landlord_id,
        'firstname' => $landlord->firstname,
        'lastname' => $landlord->lastname,
        'profile_pic_url' => asset('storage/' . str_replace('storage/', '', $landlord->profile_pic_url)),
    ]);
    
}

}

