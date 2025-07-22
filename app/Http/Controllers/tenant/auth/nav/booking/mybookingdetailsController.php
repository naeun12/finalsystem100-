<?php

namespace App\Http\Controllers\tenant\auth\nav\booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\landlord\bookingModel;
use App\Models\landlord\roomModel;
use App\Models\tenant\bookingpaymentModel;
use App\Models\tenant\tenantModel;
class mybookingdetailsController extends Controller
{
    public function viewBookingDetails($tenant_id,$booking_id)
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
            return view('tenant.auth.nav.bookings.mybookingdetails',['title' => 'Tenant - Booking Details',
            'tenant_id' => $tenant_id,
            'booking_id' => $booking_id,
            'cssPath' => asset('css/tenantpage/auth/roomdetails.css')]);
        }
        public function bookingDetails($booking_id)
        {
            $bookings = bookingModel::with(['tenant','room.dorm'])
                ->where('bookingID', $booking_id)
                ->get();

            return response()->json($bookings);
        }
       
public function payRoom(Request $request)
{
    try {
        // 🔍 Step 1: Validation
        $validated = $request->validate([
            'fkbookingID' => 'required|exists:bookings,bookingID',
            'paymentType' => 'required|string',
            'paymentImage' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'fkbookingID.required' => 'Booking ID is required.',
            'fkbookingID.exists' => 'Booking ID does not exist.',
            'paymentType.required' => 'Please select a payment type.',
            'paymentType.string' => 'Payment type must be a valid string.',
            'paymentImage.image' => 'The file must be an image.',
            'paymentImage.mimes' => 'The image must be a jpeg, png, or jpg.',
            'paymentImage.max' => 'The image must not be larger than 2MB.',
        ]);

        // 🧹 Step 2: Delete old payment (and image if exists)
        $existingPayment = bookingpaymentModel::where('fkbookingID', $request->fkbookingID)->first();

        if ($existingPayment) {
            if ($existingPayment->paymentImage) {
                \Storage::disk('public')->delete($existingPayment->paymentImage);
            }
            $existingPayment->delete();
        }

        // 🖼️ Step 3: Image upload
        $imagePath = null;

        if ($request->hasFile('paymentImage')) {
            $file = $request->file('paymentImage');

            if (!$file->isValid()) {
                return response()->json([
                    'message' => 'Invalid image file. Please try again.',
                ], 400);
            }

            $imagePath = $file->store('paymentImages', 'public');
        }

        // 💾 Step 4: Store payment
        $payment = bookingpaymentModel::create([
            'fkbookingID' => $request->fkbookingID,
            'paymentType' => $request->paymentType,
            'paymentImage' => $imagePath,
        ]);
        bookingModel::where('bookingID', $request->fkbookingID)->update([
            'status' => 'Pending Payment Confirmation'
        ]);

        // ✅ Step 5: Success
        return response()->json([
            'message' => 'Payment submitted successfully!',
            'payment' => $payment
        ], 201);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation failed.',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Something went wrong while submitting payment.',
            'error' => $e->getMessage()
        ], 500);
    }
}



}
