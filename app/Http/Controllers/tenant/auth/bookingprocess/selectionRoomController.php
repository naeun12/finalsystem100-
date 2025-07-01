<?php

namespace App\Http\Controllers\tenant\auth\bookingprocess;

use App\Http\Controllers\Controller;
use App\Models\landlord\landlordRoomModel;
use Illuminate\Http\Request;

class selectionRoomController extends Controller
{
    public function SelectionRoom($dormitoryID,$tenantID)
    {
        return view('tenant.auth.bookingProcess.roomSelection',['title'=>'Room Selection','cssPath'=>'','dormitory_id' =>$dormitoryID,'tenant_id'=>$tenantID]);
    }
    public function availableRooms($dormitoryID)
    {
        $rooms = landlordRoomModel::where('dormitory_id',$dormitoryID)->get();
        return response()->json(['rooms' => $rooms]);
    }
    public function viewRoomDetails($id)
    {
        $room = landlordRoomModel::where('room_id',$id)->first();
        return response()->json([
            'status' => 'success',
            'room' => $room,
        ]);
    }
}
