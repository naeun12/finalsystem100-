<?php

namespace App\Http\Controllers\tenant\auth\bookingprocess;

use App\Http\Controllers\Controller;
use App\Models\landlord\landlordRoomModel;

use Illuminate\Http\Request;

class bookroomController extends Controller
{
    public function bookRoom($roomId,$tenantID)
    {
        return view('tenant.auth.bookingProcess.roomBooking',['title'=>'Room Selection','cssPath'=>'','roomId' =>$roomId,'tenant_id'=>$tenantID]);
    }
    public function getRoom($roomID)
    {
        $room = landlordRoomModel::where('room_id',$roomID)->first();
        return response()->json([
            'status' => 'success',
            'room' => $room,
        ]);
    }
}
