<?php

namespace App\Http\Controllers\tenant\auth\bookingprocess;

use App\Http\Controllers\Controller;
use App\Models\landlord\roomModel;
use App\Models\tenant\bookingModel;

use Illuminate\Http\Request;

class bookroomController extends Controller
{
    public function bookRoomPage($roomId,$tenantID)
    {
        return view('tenant.auth.bookingProcess.roomBooking',['title'=>'Room Selection','cssPath'=>'','roomId' =>$roomId,'tenant_id'=>$tenantID]);
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
                'payment_type'      => 'required|string|max:255',
                'moveInDate'       => 'required',
                'moveOutDate'      => 'required',
                'payment_image' => 'required|image|mimes:jpeg,png,jpg|max:1024',
                'studentpicture_id'   => 'required|string',
            ],
            [ 
                'payment_type.required'    => 'Please select a payment method.',
                'moveInDate.required'      => 'Please select a move-in date.',
                'moveInDate.date'          => 'Move-in date must be a valid date.',
                'moveOutDate.required'     => 'Please select a move-out date.',
                'moveOutDate.date'         => 'Move-out date must be a valid date.',
                'moveOutDate.after'        => 'Move-out date must be after the move-in date.',
                'payment_image.required'   => 'Please upload a payment screenshot.',
                'payment_image.mimes'      => 'Payment image must be in JPEG, PNG, or JPG format.',
                'payment_image.max'        => 'Payment image must not be larger than 1MB.',
                'studentpicture_id.required' => 'Student picture ID is required.'
            ]);
            if ($request->hasFile('payment_image')) {
                $image1 = $request->file('payment_image');
                $image1Name = time() . '_1.' . $image1->getClientOriginalExtension();
                $image1Path = $image1->storeAs('public/uploads/roomImages', $image1Name);
                $mainImageUrl = asset('storage/uploads/roomImages/' . $image1Name);
            }
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
                'paymentType'       => $request->payment_type,
                'paymentImage'      => $mainImageUrl,
                'studentpictureId'  => $request->studentpicture_id,
                'status'             => 'pending',
            ]);
    
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
                'message' => 'Reservation failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        
    
}
