@include('tenant.auth.partials.navigation')

<!-- HTML -->
<div id="viewBookingDetails" booking_id={{ $booking_id }}>
    <tenants-auth-nav-bookings-mybookingdetails></tenants-auth-nav-bookings-mybookingdetails>
</div>

@include('tenant.auth.partials.footer')
