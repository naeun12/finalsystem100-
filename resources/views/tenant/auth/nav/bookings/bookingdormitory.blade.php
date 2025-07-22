@include('tenant.auth.partials.navigation')
<!-- HTML -->
<div id="viewBooking" tenant_id="{{ $tenant_id }}">
    <tenants-auth-nav-bookingdormitory></tenants-auth-nav-bookingdormitory>
</div>

@include('tenant.auth.partials.footer')
