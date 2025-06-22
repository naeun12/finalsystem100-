<?php

namespace App\Http\Controllers\landloard\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class dashboard extends Controller
{
    public function landlordDashboard()
    {
        return view ('landlord.auth.dashboard', ['title' => 'Landlord - DormManagement',
        'headerName' => 'Dashboard',
        'color' =>'primary']);
    }
}
