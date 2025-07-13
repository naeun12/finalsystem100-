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
        'firstname'     => 'required|string|max:255',
        'lastname'      => 'required|string|max:255',
        'contactInfo'   => [
            'required',
            'string',
            'max:255',
            'regex:/^(09|\+639)\d{9}$/'
        ],
        'age'           => 'required|integer|min:15|max:60',
        'sex'           => 'required|in:Male,Female',
        'email'         => 'required|email|max:255',
    ], [
        'firstname.required'     => 'Please enter your first name.',
        'lastname.required'      => 'Please enter your last name.',
        'contactInfo.required'   => 'We need your contact information.',
        'contactInfo.regex'      => 'Please enter a valid Philippine mobile number (e.g., 09XXXXXXXXX or +639XXXXXXXXX).',
        'age.required'           => 'Please enter your age.',
        'age.integer'            => 'Age should be a valid number.',
        'age.min'                => 'You must be at least 15 years old to register.',
        'age.max'                => 'You must be 60 years old or younger to register.',
        'sex.required'           => 'Please select your gender.',
        'sex.in'                 => 'Gender must be either Male or Female.',
        'email.required'         => 'Please enter your email address.',
        'email.email'            => 'That doesn’t look like a valid email address.',
    ]);

    try {
        return response()->json(['status' => 'success']);
    } catch(\Illuminate\Validation\ValidationException $e) {
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
       
    
    }