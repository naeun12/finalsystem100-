<?php

namespace App\Http\Controllers\landloard\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessagingCenterController extends Controller
{
    public function MessagingPage()
    {
        return view ('landlord.auth.messagingCenter', ['title' => 'Landlord - Messaging Center',
        'headerName' => 'Messaging Center'
    ]);
    }
}
