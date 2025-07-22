<?php

namespace App\Http\Controllers\landlord\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\messageModel;
use App\Models\conversationModel;
use App\Models\tenant\tenantModel;
use App\Events\MessageSent;
use App\Models\landlord\landlordModel;
 


class messagelandlordController extends Controller
{
   public function landlordmessageIndex($landlord_id)
{
    $conversations = conversationModel::where('initiatorID', $landlord_id)
        ->where('initiatorRole', 'landlord')
        ->get();

    $history = $conversations->map(function ($convo) {
        // Extract tenant ID from topic (e.g., tenant_{id}_landlord_{id})
        preg_match('/tenant_([a-z0-9\-]+)/', $convo->topic, $matches);
        $tenant_id = $matches[1] ?? null;

        $tenant = $tenant_id ? tenantModel::find($tenant_id) : null;

        $lastMessage = messageModel::where('conversationID', $convo->id)
            ->latest('sentAt')
            ->first();

        return [
            'conversation_id'   => $convo->id,
            'receiver_name'     => $tenant ? $tenant->firstname . ' ' . $tenant->lastname : 'Unknown',
            'receiver_profile'  => $tenant->profile_pic_url ?? 'default-profile.png',
            'last_message'      => $lastMessage->message ?? '',
            'sent_at'           => $lastMessage->sentAt ?? null,
        ];
    })->sortByDesc('sent_at')->values();

    return view('landlord.auth.messagingCenter', [
        'title'        => 'My Conversations',
        'headerName'   => 'Message',
        'landlord_id'  => $landlord_id,
        'history'      => $history,
    ]);
}
public function selecttenantToMessage(Request $request, $landlord_id)
{
    $tenant_id = $request->input('tenant_id');

    if (!$landlord_id || !$tenant_id) {
        return back()->with('error', 'Landlord or Tenant ID is missing.');
    }

    $landlord = landlordModel::find($landlord_id);
    $tenant = tenantModel::find($tenant_id);

    if (!$tenant || !$landlord) {
        return back()->with('error', 'Invalid tenant or landlord.');
    }

    // 🔁 Use consistent topic format
    $topic = "tenant_{$tenant_id}_landlord_{$landlord_id}";

    // ✅ Only create if it doesn't exist
    $conversation = conversationModel::firstOrCreate(
        ['topic' => $topic],
        ['initiatorID' => $landlord_id, 'initiatorRole' => 'landlord']
    );

    $lastMessage = messageModel::where('conversationID', $conversation->id)
        ->latest('sentAt')
        ->first();

    $history = collect([
        [
            'conversation_id'   => $conversation->id,
            'receiver_name'     => $tenant->firstname . ' ' . $tenant->lastname,
            'receiver_profile'  => $tenant->profile_pic_url ?? 'default-profile.png',
            'last_message'      => $lastMessage->message ?? '(No messages yet)',
            'sent_at'           => $lastMessage->sentAt ?? null,
        ]
    ]);

    return view('landlord.auth.messagingCenter', [
        'title'        => 'My Conversations',
        'headerName'   => 'Message',
        'landlord_id'  => $landlord_id,
        'history'      => $history,
    ]);
}


    public function getConversations($landlord_id)
{
    $conversations = conversationModel::where('topic', 'like', '%landlord_' . $landlord_id . '%')->get();

    $history = $conversations->map(function ($convo) {
        preg_match('/tenant_([a-z0-9\-]+)/', $convo->topic, $matches);
        $tenant_id = $matches[1] ?? null;
        $tenant = $tenant_id ? tenantModel::find($tenant_id) : null;

        $lastMessage = messageModel::where('conversationID', $convo->id)
            ->orderBy('sentAt', 'desc')
            ->first();

        return [
            'conversation_id'   => $convo->id,
            'receiver_name'     => $tenant ? $tenant->firstname . ' ' . $tenant->lastname : 'Unknown Tenant',
            'receiver_profile'  => $tenant->profile_pic_url ?? 'default-profile.png',
            'last_message'      => $lastMessage->message ?? '(No messages yet)',
            'sent_at'           => $lastMessage->sentAt ?? null,
        ];
    });

    return response()->json($history);
}
public function getMessages($conversation_id)
{
    $messages = \App\Models\messageModel::where('conversationID', $conversation_id)
        ->orderBy('sentAt', 'asc')
        ->get();

    return response()->json($messages);
}
public function sendMessage(Request $request)
{
    $validated = $request->validate([
        'conversationID' => 'required|exists:conversations,id',
        'message' => 'required|string',
        'senderID' => 'required|string',
        'senderRole' => 'required|in:tenant,landlord',
        
    ]);

    // Get the conversation
    $conversation = \App\Models\conversationModel::find($validated['conversationID']);

    // Extract tenant and landlord IDs from the topic
    preg_match('/tenant_([a-z0-9\-]+)_landlord_([a-z0-9\-]+)/', $conversation->topic, $matches);
    $tenantID = $matches[1] ?? null;
    $landlordID = $matches[2] ?? null;

    // Determine receiver ID
    $receiverID = $validated['senderRole'] === 'tenant' ? $landlordID : $tenantID;
    $receiverRole = $validated['senderRole'] === 'tenant' ? 'landlord' : 'tenant';

    // Validate resolved receiver
    if (!$receiverID) {
        return response()->json([
            'success' => false,
            'message' => 'Unable to resolve receiver ID from topic.'
        ], 422);
    }

    // Create message
    $message = new \App\Models\messageModel();
    $message->conversationID = $validated['conversationID'];
    $message->message = $validated['message'];
    $message->senderID = $validated['senderID'];
    $message->senderRole = $validated['senderRole'];
    $message->receiverID = $receiverID;
    $message->receiverRole = $receiverRole;
    $message->sentAt = now();
    $message->isRead = 0;
    $message->save();


    return response()->json([
        'success' => true,
        'message' => 'Message sent successfully.',
        'data' => $message
    ]);
}


}
