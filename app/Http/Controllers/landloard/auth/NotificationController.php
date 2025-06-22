<?php

namespace App\Http\Controllers\landloard\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function NotificationPage()
    {
        return view ('landlord.auth.Notification', ['title' => 'Landlord - Notification',
        'headerName' => 'Notification'
    ]);
    }
}
