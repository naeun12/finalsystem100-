<?php

namespace App\Http\Controllers\tenant\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\landlord\dormModel; 
use App\Models\landlord\tenantscreeningModel; 
use Illuminate\Support\Facades\Http;
use App\Models\landlord\roomModel;
use App\Models\tenant\tenantModel;

class dormdetailscontroller extends Controller
{
    public function roomDetails($dormitory_id,$tenant_id)
        {
            $sessionTenant_id = session('tenant_id');
        
            if (!$sessionTenant_id) {
                return redirect()->route('tenant-login')->with('error', 'Please log in as a landlord.');
            }
        
            if ($tenant_id !== $sessionTenant_id) {
                return redirect()->route('tenant-login')->with('error', 'Unauthorized access.');
            }
        
            $tenant = tenantModel::find($tenant_id);
            if (!$tenant) {
                return redirect()->route('tenant-login')->with('error', 'Landlord not found.');
            }
            $title = 'Tenant room Details - Dormhub';
            return view('tenant.auth.roomdetails',['title' => 'Room Details',
            'dormitory_id' => $dormitory_id,
            'tenant_id' => $tenant_id,
            'tenant' => $tenant,
            'cssPath' => asset('css/tenantpage/auth/roomdetails.css')]);
        }
        public function ViewDorms(Request $request)
        {
            $dormitory_id = $request->query('dormitory_id');
            $dorm = dormModel::with([
                'amenities',
                'images',
                'rulesAndPolicy',
                'landlord',
                'rooms' => function ($query) {
                    $query->where('availability', 'Available');
                }
            ])->where('dormID', $dormitory_id)->first(); 
    
            if (!$dorm) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dorm not found',
                ], 404);
            }
            return response()->Json([
                'status' => 'success',
                'dorm' => $dorm,
                'landlord' => $dorm->landlord,
                
    
            ]);
        }
        public function tenantInformation(Request $request)
        {
            $validatedData = $request->validate([
                'firstname'   => 'required|string|max:255',
                'lastname'    => 'required|string|max:255',
                'contactInfo' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^(09|\+639)\d{9}$/'
                ],    
                'age'         => 'required|integer|min:15|max:60',
                'sex'         => 'required|in:Male,Female',
                'email'       => 'required|email|max:255',
            ], [
               'firstname.required'   => 'Please enter your first name.',
                'lastname.required'    => 'Please enter your last name.',
                'contactInfo.required' => 'We need your contact information.',
                'age.required'         => 'Please enter your age.',
                'age.integer'          => 'Age should be a valid number.',
                'age.min'              => 'You must be at least 15 years old to register.',
                'contactInfo.regex' => 'Please enter a valid Philippine mobile number (e.g., 09XXXXXXXXX or +639XXXXXXXXX).',
                'age.max'              => 'You must be 60 years old or younger to register.',
                'sex.required'         => 'Please select your gender.',
                'sex.in'               => 'Gender must be either Male or Female.',
                'email.required'       => 'Please enter your email address.',
                'email.email'          => 'That doesn’t look like a valid email address.',
            ]);
            try
            {
                return response()->json(['status' => 'success']);
            }
            catch(\Illuminate\Validation\ValidationException $e)
            {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors(),
                ], 422);
        
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 500);
            }
            
        }
        public function uploadTenantId(Request $request)
        {
            $validatedData = $request->validate([  
                 'tenant_picture' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ], [
                'tenant_picture.required' => 'Please upload a tenant picture.',
                'tenant_picture.image'    => 'The uploaded file must be an image.',
                'tenant_picture.mimes'    => 'The image must be a file of type: jpg, jpeg, png.',
                'tenant_picture.max'      => 'The image must not be larger than 2MB.',
               
            ]);
            if ($request->hasFile('tenant_picture')) {
                $image1 = $request->file('tenant_picture');
                $image1Name = time() . '_1.' . $image1->getClientOriginalExtension();
                $image1Path = $image1->storeAs('public/uploads/roomImages', $image1Name);
                $mainImageUrl = asset('storage/uploads/roomImages/' . $image1Name);
            }
            try
            {
                return response()->json(['status' => 'success',       
                'image_url' => $mainImageUrl,
            ]);
            }
            catch(\Illuminate\Validation\ValidationException $e)
            {
                return response()->json([
                    'status' => 'error',
                    
                    'message' => $e->errors(),
                ], 422);
        
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 500);
            }
            
        }
        public function bookaroom(Request $request)
        {
            $validatedData = $request->validate([
                'dormitory_id'     => 'integer|required',
                'landlord_id'       => 'string|required',
                'firstname'        => 'required|string|max:255',
                'lastname'         => 'required|string|max:255',
                'contactInfo'      => 'required|string|max:255',
                'age'              => 'required|integer|min:15|max:60',
                'sex'              => 'required|in:Male,Female',
                'email'            => 'required|email|max:255',
                'room_id'          => 'required|integer',
                'tenant_picture' => 'required|image|mimes:jpg,jpeg,png|max:2048',
                'payment_type'    => 'required|string|max:255',
                'PaymentPictureFile' => 'required|image|mimes:jpg,jpeg,png|max:2048'
    
                
            ]);
            $tenant_id = session('tenant_id');
            if (!$tenant_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tenant ID not found in session.'
                ], 400);
            }
        
            try {
                $book = new tenantscreeningModel();
                
                $book->fkdormitory_id = $validatedData['dormitory_id'];
                $book->landlord_id = $validatedData['landlord_id'];
                $book->fkroom_id = $validatedData['room_id'];
                $book->fktenant_id = $tenant_id;
                $book->firstname = $validatedData['firstname'];
                $book->lastname = $validatedData['lastname'];
                $book->contact_number = $validatedData['contactInfo'];
                $book->age = $validatedData['age'];
                $book->gender = $validatedData['sex'];
                $book->contact_email = $validatedData['email'];
                if ($request->hasFile('tenant_picture')) {
                    $image1 = $request->file('tenant_picture');
                    $image1Name = time() . '_1.' . $image1->getClientOriginalExtension();
                    $image1Path = $image1->storeAs('public/uploads/roomImages', $image1Name);
                    $mainImageUrl = asset('storage/uploads/roomImages/' . $image1Name);
                } else {
                    $mainImageUrl = null;
                }
                $book->studentpicture_id = $mainImageUrl;
                $book->payment_type = $validatedData['payment_type'];
                if ($request->hasFile('PaymentPictureFile')) {
                    $image1 = $request->file('PaymentPictureFile');
                    $image1Name = time() . '_1.' . $image1->getClientOriginalExtension();
                    $image1Path = $image1->storeAs('public/uploads/roomImages', $image1Name);
                    $mainImageUrl = asset('storage/uploads/roomImages/' . $image1Name);
                } else {
                    $mainImageUrl = null;
                }
                $book->payment_image = $mainImageUrl;
    
        
                // $book->studentpicture_id;         
                $book->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Successfully submitted your booking request.',
                    'book' => $book
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        }
        public function viewRoomDetails($id)
        {
            $room = roomModel::where('room_id',$id)->first();
            if(!$room)
            {
                return response()->json(['message'=>'room not found']
                ,404);
                
            }
            return response()->json([
                'status' => 'success',
                'room' => $room,
            ]);
        }
    //     public function PaymentPaymongo()
    // {
    //     try {
    //         $response = Http::withBasicAuth(env('PAYMONGO_SECRET_KEY'), '')
    //             ->post('https://api.paymongo.com/v1/payment_intents', [
    //                 'data' => [
    //                     'attributes' => [
    //                         'amount' => 10000,
    //                         'payment_method_allowed' => ['card'],
    //                         'payment_method_options' => ['card' => ['request_three_d_secure' => 'automatic']],
    //                         'currency' => 'PHP',
    //                     ],
    //                 ],
    //             ]);
    
    //         if ($response->failed()) {
    //             return response()->json([
    //                 'error' => 'Failed to create payment intent',
    //                 'details' => $response->body()
    //             ], 500);
    //         }
    
    //         return response()->json($response->json());
    //     } catch (\Exception $e) {
    //         // This will give you the error message as a JSON response for frontend to see
    //         return response()->json([
    //             'error' => 'Server error',
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    
    // }
    
        
    
    }
    

