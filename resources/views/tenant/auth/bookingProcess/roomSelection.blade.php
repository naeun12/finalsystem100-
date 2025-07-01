@include('tenant.auth.partials.navigation')
<div id="roomSelection" data-dorm-id="{{ $dormitory_id }}" data-tenant-id="{{ $tenant_id }}">
    <tenants-auth-bookingProcess-roomSelection></tenants-auth-bookingProcess-roomSelection>
</div>

@include('tenant.auth.partials.footer')
