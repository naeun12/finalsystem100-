<?php

namespace App\Http\Controllers\tenant\auth\bookingprocess;

use App\Http\Controllers\Controller;
use App\Models\landlord\landlordRoomModel;
use App\Models\landlord\reservationModel;

use Illuminate\Http\Request;

class selectionRoomController extends Controller
{
    public function SelectionRoom($dormitoryID,$tenantID)
    {
        return view('tenant.auth.bookingProcess.roomSelection',['title'=>'Room Selection','cssPath'=>asset('css/tenantpage/auth/roomselect.css'),'dormitory_id' =>$dormitoryID,'tenant_id'=>$tenantID]);
    }
    public function availableRooms($dormitoryID)
    {
        $rooms = landlordRoomModel::where('dormitory_id',$dormitoryID)
        ->where('availability','Available')
        ->get();
        return response()->json(['rooms' => $rooms]);
    }
    public function occupiedRooms($dormitoryID)
    {
        $rooms = landlordRoomModel::where('dormitory_id',$dormitoryID)
        ->where('availability','Occupied')
        ->get();
        return response()->json(['rooms' => $rooms]);
    }
    public function maintenanceRooms($dormitoryID)
    {
        $rooms = landlordRoomModel::where('dormitory_id',$dormitoryID)
        ->where('availability','Under Maintenance')
        ->get();
        return response()->json(['rooms' => $rooms]);
    }
    public function selectedPriceRange(Request $request, $dormitoryID)
{
    $min = $request->query('min');
    $max = $request->query('max');
    $chooseStatus = $request->query('chooseStatus');

    $rooms = landlordRoomModel::where('dormitory_id', $dormitoryID)
        ->where('availability', $chooseStatus)
        ->whereBetween('price', [$min, $max])
        ->get();

    return response()->json(['rooms' => $rooms]);
}
public function filterGender(Request $request, $dormitoryID)
{
    $gender = $request->query('gender');
    $chooseStatus = $request->query('chooseStatus');

    $rooms = landlordRoomModel::where('dormitory_id', $dormitoryID)
        ->where('availability', $chooseStatus)
        ->where('gender_preference', $gender)
        ->get();

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
    public function reservation(Request $request)
    {
        try {
            // 1. VALIDATE FIRST
            $validatedData = $request->validate([
                'dormitory_id'        => 'required|integer',
                'room_id'             => 'required|integer',
                'tenant_id'           => 'required|string',
                'firstname'           => 'required|string|max:255',
                'lastname'            => 'required|string|max:255',
                'contact_number'      => 'required|string|max:255',
                'email'               => 'required|email|max:255',
                'age'                 => 'required|integer|min:15|max:60',
                'gender'              => 'required|in:Male,Female',
                'studentpicture_id'   => 'required|string',
            ]);
    
            
            // 3. SAVE TO DATABASE
            $reservation = reservationModel::create([
                'fkdormitoryID'      => $request->dormitory_id,
                'fkroomID'           => $request->room_id,
                'fktenantID'         => $request->tenant_id,
                'firstname'          => $request->firstname,
                'lastname'           => $request->lastname,
                'contact_number'     => $request->contact_number,
                'contact_email'      => $request->email,
                'age'                => $request->age,
                'gender'             => $request->gender,
                'studentpicture_id'  => $request->studentpicture_id,
                'status'             => 'pending',
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Reservation created successfully.',
                'data' => $reservation
            ]);
        } catch (\Exception $e) {
            \Log::error('Reservation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Reservation failed.',
                'error' => $e->getMessage()]);
            }
        }
        
}
