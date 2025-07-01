@include('tenant.auth.partials.navigation')
<div id="roomBook" data-room-id="{{ $roomId }}" data-tenant-id="{{ $tenant_id }}">
    <tenants-auth-bookingProcess-bookRoom></tenants-auth-bookingProcess-bookRoom>
</div>

@include('tenant.auth.partials.footer')
