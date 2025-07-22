@include('tenant.auth.partials.navigation')
<!-- HTML -->
<div id="myReservation" tenant_id="{{ $tenant_id }}">
    <tenants-auth-reservation-reservation></tenants-auth-reservation-reservation>
</div>

@include('tenant.auth.partials.footer')
