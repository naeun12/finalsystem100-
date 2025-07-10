<?php

namespace App\Http\Controllers\landlord\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\landlordModel;
use App\Models\landlord\bookingModel;
use App\Models\landlord\roomModel;
use App\Models\landlord\dormModel;

class reservationController extends Controller
{
    public function reservationIndex($landlord_id)
    {
        $sessionLandlordId = session('landlord_id');
    
        if (!$sessionLandlordId) {
            return redirect()->route('loginLandlord')->with('error', 'Please log in as a landlord.');
        }
    
        if ($landlord_id !== $sessionLandlordId) {
            return redirect()->route('loginLandlord')->with('error', 'Unauthorized access.');
        }
    
        $landlord = landlordModel::find($landlord_id);
        if (!$landlord) {
            return redirect()->route('loginLandlord')->with('error', 'Landlord not found.');
        }
    
        return view('landlord.auth.reservationpage',[
        "title" => "Landlord - Reservation", 
        'headerName' => 'Tenants Reservation',           
        'landlord' => $landlord,
    ]);   
    }
}
