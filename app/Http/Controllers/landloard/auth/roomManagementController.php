<?php

namespace App\Http\Controllers\landloard\auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\Controller;
use App\Models\landlord\landlordAccountModel;
use App\Models\landlord\landlordRoomModel;
use App\Models\landlord\landlordDormManagement;
use App\Models\landlord\landlordRoomFeaturesModel;
use App\Models\landlord\landlordRoomFeaturesRoomModel;

use Illuminate\Support\Facades\Auth;

class roomManagementController extends Controller
{
    public function RoomManagement($landlordId)
    {
        $sessionLandlordId = session('landlord_id');
    
        if (!$sessionLandlordId) {
            return redirect()->route('loginLandlord')->with('error', 'Please log in as a landlord.');
        }
    
        if ($landlordId !== $sessionLandlordId) {
            // Optional: you can log this attempt or show a 403 page
            return redirect()->route('loginLandlord')->with('error', 'Unauthorized access.');
        }
    
        $landlord = landlordAccountModel::find($landlordId);
        if (!$landlord) {
            return redirect()->route('loginLandlord')->with('error', 'Landlord not found.');
        }
        $dorms = landlordDormManagement::where('landlord_id', $landlordId)->get();
        return view ('landlord.auth.roomManagement', ['title' => 'Landlord - Room Management',
        'headerName' => 'Room Management',
        'color' =>'primary',
        'landlord' => $landlord,
        'dorms' => $dorms,
    ]);
    }
    public function getRoomsByDorm($dormId)
    {
        $landlordId = session('landlord_id');
        $rooms = landlordRoomModel::where('landlord_id', $landlordId)
            ->where('dormitory_id', $dormId)
            ->with('dorm')
            ->paginate(5);
            return response()->json([
                'rooms' => $rooms // ✅ this is important!
            ]);   
    }
    public function getRoomsByGender($gender)
    {
        $landlordId = session('landlord_id');
        $rooms = landlordRoomModel::where('landlord_id', $landlordId)
            ->where('gender_preference', $gender)
            ->paginate(5);
        return response()->json([
            'rooms' => $rooms 
        ]);
    }
    public function getRoomsByAvailability($availability)
    {
        $landlordId = session('landlord_id');
        $rooms = landlordRoomModel::where('landlord_id', $landlordId)
            ->where('availability', $availability)
            ->paginate(5);
        return response()->json([
            'rooms' => $rooms 
        ]);
    }
    public function getRoomsByRoomType(Request $request)
    {
        $roomType = $request->query('roomType'); // or $request->input('type')

        $landlordId = session('landlord_id');
        $rooms = landlordRoomModel::where('landlord_id', $landlordId)
            ->where('room_type', $roomType)
            ->paginate(5);
        return response()->json([
            'rooms' => $rooms 
        ]);
    }

    public function addRoom(Request $request)
{
    $validator = Validator::make($request->all(), [
        'dormsId' => 'required|integer',
        'roomNumber' => ['required','string','max:255',],
        'roomType' => 'required|string|max:255',
        'availability' => 'required|string|max:255',
        'listing_type' => 'required|string|max:255',
        'area_sqm' => 'required|string|max:255',
        'gender_preference' => 'required|string|max:255',
        'furnishing_status' => 'required|string|max:255',
        'roomImageFile' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        'price' => 'required|numeric|min:0',
    ], [
        // Custom error messages
        'dormsId.required' => 'Please select a dormitory.',
'dormsId.integer' => 'Invalid dormitory selection.',

'roomNumber.required' => 'Please enter the room number.',
'roomNumber.string' => 'Room number must be text.',
'roomNumber.max' => 'Room number must be 255 characters or fewer.',

'roomType.required' => 'Please select a room type.',
'roomType.string' => 'Room type must be text.',
'roomType.max' => 'Room type must be 255 characters or fewer.',

'availability.required' => 'Please select availability status.',
'availability.string' => 'Availability must be text.',
'availability.max' => 'Availability must be 255 characters or fewer.',

'listing_type.required' => 'Please select a bed type.',
'listing_type.string' => 'Bed type must be text.',
'listing_type.max' => 'Bed type must be 255 characters or fewer.',

'area_sqm.required' => 'Please enter the room area in square meters.',
'area_sqm.string' => 'Room area must be text.',
'area_sqm.max' => 'Room area must be 255 characters or fewer.',

'gender_preference.required' => 'Please select a gender preference.',
'gender_preference.string' => 'Gender preference must be text.',
'gender_preference.max' => 'Gender preference must be 255 characters or fewer.',

'furnishing_status.required' => 'Please specify the furnishing status.',
'furnishing_status.string' => 'Furnishing status must be text.',
'furnishing_status.max' => 'Furnishing status must be 255 characters or fewer.',

'roomImageFile.required' => 'Please upload a room image.',
'roomImageFile.image' => 'The uploaded file must be an image.',
'roomImageFile.mimes' => 'Accepted image formats: jpg, jpeg, png, webp.',
'roomImageFile.max' => 'Image size must not exceed 2MB.',

'price.required' => 'Please enter the room price.',
'price.numeric' => 'Price must be a valid number.',
'price.min' => 'Price cannot be negative.',

    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $landlordId = session('landlord_id');
        if (!$landlordId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action. Please log in as a landlord.'
            ], 403);
        }

        $room = new landlordRoomModel();
        $room->dormitory_id = $request->input('dormsId');
        $room->landlord_id = $landlordId;
        $room->room_number = $request->input('roomNumber');
        $room->availability = $request->input('availability');
        $room->room_type = $request->input('roomType');
        $room->furnishing_status = $request->input('furnishing_status');
        $room->listing_type = $request->input('listing_type');
        $room->area_sqm = $request->input('area_sqm');
        $room->gender_preference = $request->input('gender_preference');
        $room->price = round($request->input('price'), 2);
        if ($request->hasFile('roomImageFile')) {
            $image1 = $request->file('roomImageFile');
            $image1Name = time() . '_1.' . $image1->getClientOriginalExtension();
            $image1Path = $image1->storeAs('public/uploads/roomImages', $image1Name);
            $mainImageUrl = asset('storage/uploads/roomImages/' . $image1Name);
        } else {
            $mainImageUrl = null;
        }
        $room->room_images = $mainImageUrl;

        $room->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Room added successfully.',
            'room' => $room,
            'room_id' => $room->room_id
        ]);

    } catch (\Illuminate\Database\QueryException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage(),
            'input' => $request->all()
        ], 500);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            'input' => $request->all()
        ], 500);
    }
}
    public function ListRooms(Request $request)
    {
        $landlordId = session('landlord_id');
    
        if (!$landlordId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action. Please log in as a landlord.'
            ], 403);
        }
    
        $rooms = landlordRoomModel::where('landlord_id', $landlordId)
        ->orderBy('created_at', 'desc')
        ->paginate(5);    
        return response()->json([
            'status' => 'success',
            'rooms' => $rooms
        ]);
    }
    public function addRoomFeatures(Request $request)
    {
        $landlordId = session('landlord_id');
        if (!$landlordId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action. Please log in as a landlord.'
            ], 403);
        }
    
        $validated = $request->validate([
            'features' => 'required|string|max:255',
            'room_id' => 'required|integer',
        ]);
    
        try {
            $features = trim($validated['features']);
            $roomId = $validated['room_id'];

            // Check if the feature already exists for this room
            $existingFeature = landlordRoomFeaturesModel::where('feature_name', $features)->first();

            // If it exists globally, use it, otherwise create it
            if (!$existingFeature) {
                $existingFeature = landlordRoomFeaturesModel::create([
                    'feature_name' => $features
                ]);
            }

            // Check if the room already has this feature assigned
            $alreadyLinked = landlordRoomFeaturesRoomModel::where('fkroom_id', $roomId)
                ->where('fkfeature_id', $existingFeature->id)
                ->exists();
    
            if ($alreadyLinked) {
                return response()->json([
                'status' => 'error',
                    'message' => 'This feature is already linked to this room.'
                ], 400);
            }

            // Link the feature to the room
            landlordRoomFeaturesRoomModel::create([
                'fkroom_id' => $roomId,
                'fkfeature_id' => $existingFeature->id,
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Features added successfully!',
            ]);
    
        } catch (\Exception $e) {
            \Log::error('Error adding features: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error adding features.',
                'error' => $e->getMessage()
            ], 500);
        }
    }    public function ViewRoom($id)
    {
        $landlordId = session('landlord_id');
        if (!$landlordId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action. Please log in as a landlord.'
            ], 403);
        }
    
        // Fetch the room by ID with its dorm info
        $room = landlordRoomModel::with(['dorm', 'features'])
        ->where('room_id', $id)
        ->where('landlord_id', $landlordId)
        ->first();
    
    
    
    
        // Check if the room exists
        if (!$room) {
            return response()->json([
                'status' => 'error',
                'message' => 'Room not found.'
            ], 404);
        }
    
        return response()->json([
            'status' => 'success',
            'room' => $room
        ]);
    }
    public function UpdateRoom(Request $request, $id)
{
    $landlordId = session('landlord_id');
    if (!$landlordId) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized action. Please log in as a landlord.'
        ], 403);
    }

    $validator = Validator::make($request->all(), [
        'dormitory_id' => 'required|integer',
        'room_number' => ['required','string','max:255'],
        'room_type' => 'required|string|max:255',
        'availability' => 'required|string|max:255',
        'listing_type' => 'required|string|max:255',
        'area_sqm' => 'required|string|max:255',
        'gender_preference' => 'required|string|max:255',
        'furnishing_status' => 'required|string|max:255',
        'roomImageFile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'price' => 'required|numeric|min:0',
        'existingImage'=> 'nullable|string'
    ], [
        'roomNumber.required' => 'Room number is required.',
        'roomNumber.string' => 'Room number must be a string.',
        'roomNumber.max' => 'Room number must not exceed 255 characters.',
    
        'room_type.required' => 'Room type is required.',
        'room_type.string' => 'Room type must be a string.',
        'room_type.max' => 'Room type must not exceed 255 characters.',
    
        'availability.required' => 'Availability status is required.',
        'availability.string' => 'Availability must be a string.',
        'availability.max' => 'Availability must not exceed 255 characters.',
    
       
    
        'listing_type.required' => 'Bed type is required.',
        'listing_type.string' => 'Bed type must be a string.',
        'listing_type.max' => 'Bed type must not exceed 255 characters.',
    
        'area_sqm.required' => 'Room area (sqm) is required.',
        'area_sqm.string' => 'Room area must be a string.',
        'area_sqm.max' => 'Room area must not exceed 255 characters.',
    
        'gender_preference.required' => 'Gender preference is required.',
        'gender_preference.string' => 'Gender preference must be a string.',
        'gender_preference.max' => 'Gender preference must not exceed 255 characters.',
    
        'furnishing_status.required' => 'Furnishing status is required.',
        'furnishing_status.string' => 'Furnishing status must be a string.',
        'furnishing_status.max' => 'Furnishing status must not exceed 255 characters.',
    
        'roomImageFile.image' => 'The uploaded file must be an image.',
        'roomImageFile.mimes' => 'The room image must be a file of type: jpg, jpeg, png, webp.',
        'roomImageFile.max' => 'The room image may not be greater than 2MB.',
    
        'price.required' => 'Price is required.',
        'price.numeric' => 'Price must be a valid number.',
        'price.min' => 'Price must not be negative.',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422);
    }

    $validated = $validator->validated();

    // Fetch the room model properly
    $room = landlordRoomModel::where('room_id', $id)
        ->where('landlord_id', $landlordId)
        ->first();

    if ($room === null) {
        return response()->json([
            'status' => 'error',
            'message' => 'Room not found.'
        ], 404);
    }

    $room->room_number = $validated['room_number'];
    $room->room_type = $validated['room_type'];
    $room->availability = $validated['availability'];
    $room->furnishing_status = $request->input('furnishing_status');
    $room->listing_type = $request->input('listing_type');
    $room->area_sqm = $request->input('area_sqm');
    $room->gender_preference = $request->input('gender_preference');
    $room->price = round($validated['price'], 2);
    if ($request->hasFile('roomImageFile')) {
        $image1 = $request->file('roomImageFile');
        $image1Name = time() . '_1.' . $image1->getClientOriginalExtension();
        $image1Path = $image1->storeAs('public/uploads/roomImages', $image1Name);
        $mainImageUrl = asset('storage/uploads/roomImages/' . $image1Name);
        $room->room_images = $mainImageUrl;

    } else {
        $room->room_images = $request->input('existingImage'); // retain old image
    }
    try {
        $room->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Room updated successfully.',
            'room' => $room
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating the room: ' . $e->getMessage()
        ], 500);
    }
}

public function deleteRoomFeatures($pivotId)
    {
        $landlordId = session('landlord_id');
        if (!$landlordId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Please log in.'
            ], 403);
        }
        $pivot = landlordRoomFeaturesRoomModel::find($pivotId); 
        if (!$pivot) {
            return response()->json([
                'status' => 'error',
                'message' => 'Feature link not found.'
            ], 404);
        }
    
        // Check if the dorm_id belongs to the landlord
        $ownsRoom = landlordRoomModel::where('room_id', $pivot->fkroom_id)
            ->where('landlord_id', $landlordId)
            ->exists();

        if (!$ownsRoom) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to delete this feature.'
            ], 403);
        }
    
        $pivot->delete(); 
    
        return response()->json([
            'status' => 'success',
            'message' => 'Feature removed from room.'
        ]);
    }
    public function DeleteRoom($id)
    {
        $landlordId = session('landlord_id');
    
        if (!$landlordId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action. Please log in as a landlord.'
            ], 403);
        }
    
        $room = landlordRoomModel::where('room_id', $id)
            ->where('landlord_id', $landlordId)
            ->first();
    
        if (!$room) {
            return response()->json([
                'status' => 'error',
                'message' => 'Room not found.'
            ], 404);
        }
    
        try {
            $room->delete();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Room deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the room: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
}
