@include('tenant.auth.partials.navigation')
<!-- HTML -->
<div id="myRooms" tenant_id="{{ $tenant_id }}">
    <tenants-auth-nav-myrooms></tenants-auth-nav-myrooms>
</div>

@include('tenant.auth.partials.footer')
