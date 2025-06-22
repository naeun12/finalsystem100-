<?php

namespace App\Http\Controllers\landloard\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function AnalyticsPage()
    {
        return view ('landlord.auth.analytics', ['title' => 'Landlord - Analytics',
        'headerName' => 'Analytics'
    ]);
    }
}
