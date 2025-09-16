<?php

namespace App\Http\Controllers\tenant\auth\bookingprocess;

use App\Http\Controllers\Controller;
use App\Models\landlord\roomModel;
use App\Models\landlord\bookingModel;
use App\Models\notificationModel;

use Illuminate\Http\Request;

class bookroomController extends Controller
{
    public function bookRoomPage($roomId,$tenantID)
    {
                    $sessionTenant_id = session('tenant_id');

         $notifications = notificationModel::where('receiverID', $sessionTenant_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            $unreadCount = notificationModel::where('receiverID', $tenantID)
            ->where('isRead', false)
            ->count();
        return view('tenant.auth.bookingProcess.roomBooking',['title'=>'Book Room','cssPath'=>'','roomId' =>$roomId,'tenant_id'=>$tenantID,'notifications' => $notifications,
             'unread_count' => $unreadCount,]);
    }
    public function getRoom($roomID)
    {
        $room = roomModel::where('roomID',$roomID)->first();
        return response()->json([
            'status' => 'success',
            'room' => $room,
        ]);
    }
    public function bookingaRoom(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'room_id'             => 'required|integer',
                'tenant_id'           => 'required|string',
                'firstname'           => 'required|string|max:255',
                'lastname'            => 'required|string|max:255',
                'contact_number'      => 'required|string|max:255',
                'email'               => 'required|email|max:255',
                'age'                 => 'required|integer|min:15|max:60',
                'gender'              => 'required|in:Male,Female',
                'moveInDate'       => 'required',
                'moveOutDate'      => 'required',
                'studentpicture_id'   => 'required|string',
            ],
            [ 
                'moveInDate.required'      => 'Please select a move-in date.',
                'moveInDate.date'          => 'Move-in date must be a valid date.',
                'moveOutDate.required'     => 'Please select a move-out date.',
                'moveOutDate.date'         => 'Move-out date must be a valid date.',
                'moveOutDate.after'        => 'Move-out date must be after the move-in date.',
                'studentpicture_id.required' => 'Student picture ID is required.'
            ]);
            
            $book = bookingModel::create([
                'fkroomID'           => $request->room_id,
                'fktenantID'         => $request->tenant_id,
                'firstname'          => $request->firstname,
                'lastname'           => $request->lastname,
                'contactNumber'     => $request->contact_number,
                'contactEmail'      => $request->email,
                'age'                => $request->age,
                'gender'             => $request->gender,
                'moveInDate'            => $request->moveInDate,
                'moveOutDate'             => $request->moveOutDate,
                'pictureID'  => $request->studentpicture_id,
                'status'             => 'pending',
            ]);
            $room = roomModel::with('landlord')->find($request->room_id);
            $roomNumber = $room->roomNumber;
            $landlord = $room->landlord;

            $notifications = notificationModel::create([
                'senderID'     => $request->tenant_id,
                'senderType'   => 'tenant',
                'receiverID'   => $landlord->landlordID,
                'receiverType' => 'landlord',
                'title'        => 'New Booking Request',
                'message'      => "A tenant has requested to book Room #{$roomNumber}.",
                'isRead'       => false,
                'readAt'       => null,
            ]);
        broadcast(new \App\Events\NewNotificationEvent($notifications));
    
            return response()->json([
                'status' => 'success',
                'message' => 'Room book successfully. Waiting for landlord confirmation.',
                'data' => $book
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Catch only validation errors and return a proper JSON response
            return response()->json([
                'status' => 'validation_error',
                'errors' => $e->errors(), // returns an array of field-specific messages
            ], 422);
        } catch (\Exception $e) {
            // Handle unexpected errors
        
            return response()->json([
                'status' => 'error',
                'message' => 'Booking failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        
    
}
