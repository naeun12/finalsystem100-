<?php

namespace App\Http\Controllers\tenant\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use App\Models\messageModel;
use App\Models\landlord\roomModel;
use App\Models\landlord\landlordModel;
use App\Models\tenant\tenantModel;
use App\Models\conversationModel;

class tenantmessageController extends Controller
{
    public function tenantMessageIndex($tenant_id)
{
    $conversations = conversationModel::where('initiatorID', $tenant_id)
        ->where('initiatorRole', 'tenant')
        ->get();

    $history = $conversations->map(function ($convo) {
        $landlord_id = $this->extractLandlordId($convo->topic);

        $landlord = $landlord_id ? landlordModel::find($landlord_id) : null;

        $lastMessage = messageModel::where('conversationID', $convo->id)
            ->latest('sentAt')
            ->first();

        return [
            'conversation_id' => $convo->id,
            'receiver_name' => $landlord ? $landlord->firstname . ' ' . $landlord->lastname : 'Unknown',
            'receiver_profile' => $landlord->profilePicUrl ?? 'default-profile.png',
            'last_message' => $lastMessage->message ?? '',
            'sent_at' => $lastMessage->sentAt ?? null,
        ];
    });

    return view('tenant.auth.tenantmessage', [
        'title' => 'My Conversations',
        'cssPath' => '',
        'demo' => '',
        'tenant_id' => $tenant_id,
        'history' => $history,
    ]);
}

    

    public function displayConversation($tenant_id)
    {
        $conversations = conversationModel::where('initiatorID', $tenant_id)->get();

        $history = $conversations->map(function ($convo) {
            $landlord_id = $this->extractLandlordId($convo->topic);

            $landlord = landlordModel::where('landlordID', $landlord_id)->first();

            return [
                'conversation_id' => $convo->id,
                'receiver_id' => $landlord_id,
                'receiver_name' => $landlord ? $landlord->firstname . ' ' . $landlord->lastname : 'Unknown',
'receiver_profile' => $landlord ? asset($landlord->profilePicUrl) : null,
                'last_message' => messageModel::where('conversationID', $convo->id)->latest()->first()->message ?? '',
            ];
        });

        return response()->json([
            'history' => $history,
        ]);
    }

    private function extractLandlordId($topic)
    {
        if (preg_match('/_landlord_([a-zA-Z0-9\-]+)/', $topic, $matches)) {
            return $matches[1];
        }
        return null;
    }
    public function landlordtenantmessageIndex(Request $request, $tenant_id)
    {
        $landlord_id = $request->input('landlord_id');
    
        if (!$landlord_id) {
            return back()->with('error', 'Landlord ID is missing.');
        }
    
        $tenant = tenantModel::find($tenant_id);
        $landlord = landlordModel::find($landlord_id);
    
        if (!$tenant || !$landlord) {
            return back()->with('error', 'Invalid tenant or landlord.');
        }
    
        $topic = "tenant_{$tenant_id}_landlord_{$landlord_id}";
    
        $conversation = conversationModel::where('topic', $topic)->first();
    
        if (!$conversation) {
            $conversation = conversationModel::create([
                'topic' => $topic,
                'initiatorID' => $tenant_id,
                'initiatorRole' => 'tenant',
            ]);
        }
    
        $messages = messageModel::where('conversationID', $conversation->id)
            ->orderBy('sentAt', 'asc')
            ->get();
    
        return view('tenant.auth.tenantmessage', [
            'title' => 'Message',
            'cssPath' => '',
            'landlord' => $landlord,
            'tenant_id' => $tenant_id,
            'messages' => $messages,
            'demo' => $landlord_id,
            'conversation_id' => $conversation->id,
        ]);
    }
    

    public function sendMessage(Request $request)
    {
        $tenant_id = $request->tenant_id ?? session('tenant_id');

        $request->validate([
            'receiver_id' => 'required',
            'message' => 'required|string',
            'conversation_id' => 'nullable|integer',
            'reply_to_id' => 'nullable|integer',
        ]);

        $landlord_id = $request->receiver_id;

        $conversation = conversationModel::firstOrCreate([
            'initiatorID' => $tenant_id,
            'initiatorRole' => 'tenant',
            'topic' => "tenant_{$tenant_id}_landlord_{$landlord_id}",
        ]);

        $message = messageModel::create([
            'conversationID' => $conversation->id,
            'senderID' => $tenant_id,
            'senderRole' => 'tenant',
            'receiverID' => $landlord_id,
            'receiverRole' => 'landlord',
            'message' => $request->input('message'),
            'replyToId' => $request->input('reply_to_id'),
            'isRead' => false,
            'sentAt' => now(),
        ]);

        event(new MessageSent($message));

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent.',
            'newMessage' => $message,
        ]);
    }

    public function getMessages($tenant_id, $landlord_id)
    {
        $conversation = conversationModel::where([
            ['initiatorID', '=', $tenant_id],
            ['initiatorRole', '=', 'tenant'],
            ['topic', '=', "tenant_{$tenant_id}_landlord_{$landlord_id}"],
        ])->first();

        if (!$conversation) {
            return response()->json([]);
        }

        $messages = messageModel::where('conversationID', $conversation->id)
            ->orderBy('sentAt', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function getLandlordInformation(Request $request)
    {
        $tenant_id = $request->input('tenant_id');

        $historyConvo = conversationModel::where('initiatorID', $tenant_id)
            ->with(['messages' => function ($query) {
                $query->orderBy('sentAt', 'asc');
            }])
            ->get();

        if ($historyConvo->isEmpty()) {
            return response()->json(['message' => 'Landlord not found'], 404);
        }

        return response()->json([
            'historyConvo' => $historyConvo,
        ]);
    }
}
