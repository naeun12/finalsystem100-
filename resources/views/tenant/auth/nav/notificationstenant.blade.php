@include('tenant.auth.partials.navigation')

<!-- HTML -->
<div id="notificationsTenant" data-tenant-id="{{ $tenant_id }}">
    <tenants-auth-nav-notificationstenant></tenants-auth-nav-notificationstenant>
</div>

@include('tenant.auth.partials.footer')
