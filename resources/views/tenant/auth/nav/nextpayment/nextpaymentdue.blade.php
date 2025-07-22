@include('tenant.auth.partials.navigation')
<!-- HTML -->
<div id="nextPayment" tenant_id="{{ $tenant_id }}">
    <tenants-auth-nav-nextPayment></tenants-auth-nav-nextPayment>
</div>

@include('tenant.auth.partials.footer')
