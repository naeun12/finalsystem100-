<?php

namespace App\Http\Controllers\landloard\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function BookingPage()
    {
        return view ('landlord.auth.bookingManagement', ['title' => 'Landlord - Booking Management',
        'headerName' => 'Booking Management'
    ]);
    }
}
