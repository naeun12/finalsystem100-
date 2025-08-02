<?php

namespace App\Http\Controllers\tenant\auth\nav\reservations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\tenant\reservationModel;
use App\Models\tenant\reservationpaymentModel;
use App\Models\tenant\tenantModel;

class myreservationController extends Controller
{
    public function viewReservation($tenant_id)
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
            return view('tenant.auth.nav.reservations.reservation',['title' => 'Reservations',
            'tenant_id' => $tenant_id,
            'cssPath' => asset('css/tenantpage/auth/roomdetails.css')]);
        }
        public function myReservationList($tenant_id)
        {
            $bookings = reservationModel::with(['tenant','room.dorm' ])
                ->where('fktenantID', $tenant_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($bookings);
        }
        public function reservationDetails($reservationID)
        {
            $reservation = reservationModel::with(['tenant','room.dorm'])->find($reservationID);
            return response()->json(['status' => 'success'
            ,'data' => $reservation]);
        }
       public function cancelReservation($reservationID)
{
    $reservation = reservationModel::with(['tenant', 'room.dorm'])->where('reservationID', $reservationID)->first();

    if ($reservation) {
        $reservation->status = 'cancelled';
        $reservation->save();

        return response()->json(['message' => 'Reservation cancelled successfully', 'reservation' => $reservation]);
    }

    return response()->json(['message' => 'Reservation not found'], 404);
    }
    public function paymentReservation(Request $request, $reservationID)
{
    try {
        $validatedData = $request->validate([
            'paymentType' => 'required|string|max:255',
            'paymentImage' => 'required|image|mimes:jpeg,png,jpg|max:1024',
        ],
        [
            'paymentType.required' => 'Please select a payment method.',
            'paymentImage.required' => 'Please upload a payment screenshot.',
            'paymentImage.mimes' => 'Payment image must be in JPEG, PNG, or JPG format.',
            'paymentImage.max' => 'Payment image must not be larger than 1MB.',
        ]);

        // Handle image upload
        $mainImageUrl = null;
        if ($request->hasFile('paymentImage')) {
            $image1 = $request->file('paymentImage');
            $image1Name = time() . '_1.' . $image1->getClientOriginalExtension();
            $image1->storeAs('public/uploads/roomImages', $image1Name);
            $mainImageUrl = asset('storage/uploads/roomImages/' . $image1Name);
        }

        // Save to payment table
        $reservationPayment = reservationpaymentModel::create([
            'reservationID' => $reservationID,
            'paymentType' => $request->paymentType,
            'paymentImage' => $mainImageUrl,
        ]);
        $reservation = reservationModel::with(['tenant', 'room.dorm'])->where('reservationID', $reservationID)->first();

        if ($reservation) {
            $reservation->status = 'paid';
            $reservation->save();
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Payment successfully. Waiting for landlord confirmation.',
            'data' => $reservationPayment
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => 'validation_error',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Reservation failed.',
            'error' => $e->getMessage()
        ], 500);
    }
}

        
    }
