<?php

namespace App\Http\Controllers\landloard\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function tenant()
    {
        return view('landlord.auth.tenant',["title" => "Landlord - Tenants", 'headerName' => 'Tenants']);
    }
}
