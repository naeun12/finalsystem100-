<?php

namespace App\Http\Controllers\tenant;

use App\Models\tenant\tenantaccountModel;
use App\Models\tenant\OtpModels;
use App\Jobs\DeleteExpiredOtpsJob;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\tenantEmailOtp;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

   use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class accountprocessController extends Controller
{
    public function login()
    {
        $title = 'Login - Dormhub';

    
        return view('tenant.accountprocess.login', compact('title'));

    }

    public function register()
    {
        $title = 'Register - Dormhub';
        return view('tenant.accountprocess.register', compact('title'));
    }
    public function registerTenant(Request $request)
    {
     
        try {
            $validated = $request->validate([
                'firstname' => 'required|string|max:55',
                'lastname' => 'required|string|max:55',
                'password' => 'required|string|min:8|confirmed',
                'email' => 'required|email|unique:tenants,email',
                'phonenumber' => 'required|string|unique:tenants,phonenumber|regex:/^\+?[0-9]{10,15}$/',
                'gender' => 'required|in:Male,Female',
                'city' => 'required|string|max:255',
                'province' => 'required|string|max:255',
                'region' => 'required|string|max:255',
                'postalcode' => 'required|string|max:4|regex:/^[a-zA-Z0-9\s\-]+$/',
                'currentaddress' => 'required|string|max:255',
                'codeotp' => 'required|digits:6',
                'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            ]);

            // Handle the profile picture upload
            if ($request->hasFile('profile_pic')) {
                $image = $request->file('profile_pic');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('public/uploads/profilePic', $imageName);
                $profilePicUrl = asset('storage/uploads/profilePic/' . $imageName);
            } else {
                $profilePicUrl = null; // No image uploaded
            }

            $passwordHash = Hash::make($request->input('password'));

            $tenantId = Str::uuid();
            $verifyOtp = OtpModels::where('email',$validated['email'])
            ->where('otpCode',$validated['codeotp'])
            ->where('otpExpires_at', '>', now())
            ->first();
            // Store the tenant data
            if(!$verifyOtp)
            {
                  return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP Submission. Please try again.',
                'data' => null
            ],400);
               
            }
             $tenant = tenantaccountModel::create([
                'tenant_id' => $tenantId,
                'firstname' => $validated['firstname'],
                'lastname' => $validated['lastname'],
                'email' => $validated['email'],
                'password_hash' => $passwordHash,
                'phonenumber' => $validated['phonenumber'],
                'gender' => $validated['gender'],
                'region' => $validated['region'],
                'city' => $validated['city'],
                'province' => $validated['province'],
                'postalcode' => $validated['postalcode'],
                'currentaddress' => $validated['currentaddress'],
                'profile_pic_url' => $profilePicUrl,
            ]);
            $verifyOtp->where('email',$validated['email'])->forceDelete();
            return response()->json([
                'status' => 'success',
                'message' => 'Registered Successfully!',
                'data' => [
                  'tenant' => $tenant
                ]
            ],200);
            
            
        } catch (\Illuminate\Validation\ValidationException $e) {
       
    } catch (\Exception $e) {
        return response()->json([
            'errors' => 'An error occurred during registration.',
            'message' => $e->getMessage()
        ], 500);
    }
    }


       public function SendOtp(Request $request)
{
    // Validate the request
    $validated = $request->validate([
       

        'email' => 'required|email|unique:tenants,email',
        'phonenumber' => 'required|string|unique:tenants,phonenumber|regex:/^\+?[0-9]{10,15}$/|max:11|min:11',
        'postalcode' => 'required|string|max:4|regex:/^[a-zA-Z0-9\s\-]+$/',
        'password' => 'required|string|min:8|confirmed',
    ], [
        
         'email.required' => 'The email field is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email is already registered.',

        'phonenumber.required' => 'The phone number field is required.',
        'phonenumber.regex' => 'Please enter a valid phone number (e.g., +1234567890).',
        'phonenumber.max' => 'Please enter a valid phone number (e.g., +1234567890).',
        'phonenumber.min' => 'Please enter a valid phone number (e.g., +1234567890).',
        'phonenumber.unique' => 'This phone number is already registered.',

        'postalcode.required' => 'The postal code field is required.',
        'postalcode.max' => 'The postal code cannot exceed 4 characters.',
        'postalcode.regex' => 'Please enter a valid postal code (e.g., alphanumeric with spaces or hyphens).',

        'password.required' => 'The password field is required.',
        'password.min' => 'The password must be at least 8 characters.',
        'password.confirmed' => 'The password confirmation does not match.',
        

    ]);

    try {
        // Generate OTP
        $otpCode = rand(100000, 999999);

        // Send OTP via email
        Mail::to($validated['email'])->send(new tenantEmailOtp($otpCode));

        // Store OTP in the database
        $expiresAt = now()->addMinutes(1);
        OtpModels::create([
            'email' => $validated['email'],
            'otpCode' => $otpCode,
            'otpExpires_at' => $expiresAt,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Check Your Gmail To Verify Your Account',
            'timer' => $expiresAt,
        ], 200);
                $delay = $expiresAt->diffInSeconds(now());
        DeleteExpiredOtpsJob::dispatch()->delay($delay);


    } catch (\Exception $e) {
        \Log::error('Unexpected Error in SendOtp:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'message' => 'An unexpected error occurred. Please try again.',
        ], 500);
    }
}
public function resendOtp(Request $request)
{
    $validated = $request->validate([
         'email' => 'required|email',
    ],
    [   
        'email.exists' => 'No OTP record found for this email.',
    ]);
    try{
         $otpRecord = OtpModels::where('email',$validated['email']);
    if(!$otpRecord)
    {
        return response()->json([
            'message' => 'No OTP record found for this email',
        ]);
    }
                $newOtp = rand(100000, 999999);
                $newOtpExpiresAt = now()->addMinutes(1);
                   $otpRecord->update([
            'otpCode' => $newOtp,
            'otpExpires_at' => $newOtpExpiresAt, 
        ]);
                Mail::to($validated['email'])->send(new tenantEmailOtp($newOtp));
                 return response()->json([
            'status' => 'success',
            'message' => 'A new OTP has been sent to your email.',
            'timer' => $newOtpExpiresAt
        ], 200);



    }
    catch(Exception $ex)
{   
    \Log::error('Unexpected Error in ResendOtp:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'message' => 'An unexpected error occurred. Please try again.',
        ], 500);
   
}
}
public function loginTenant(Request $request)
{
    try
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Please enter email address',
            'email.email' => 'Please enter a valid email address',
            'password.required' => 'Please enter your password',
        ]);
    
        $user = tenantaccountModel::where('email', $request->email)->first();
    
        if (!$user || !\Hash::check($request->password, $user->password_hash)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }
    
    session([
        'tenant_logged_in' => true,
        'tenant_id' => $user->tenant_id,
        'tenant_firstname' => $user->firstname,
        'tenant_lastname' => $user->lastname,
        'profile_pic_url' => $user->profile_pic_url
    ]);
    
        return response()->json([
            'message' => 'Login successful',
            'status' => 'success',
            'tenant' => [
                'id' => $user->tenant_id,
                'firstname' =>$user->firstname,
                'lastname' =>$user->lastname,
            ],
    
        ]);
    }
    catch(\Illuminate\Validation\ValidationException $e)
    {

    }
   
}
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/tenantLogin');
}


    

}



