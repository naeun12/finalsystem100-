@include('tenant.auth.partials.navigation')
<!-- Sidebar -->


<div id="tenantmessage" data-landlord-id="{{ $demo }}" data-tenant-id="{{ $tenant_id }}">
    <tenants-auth-tenantmessage></tenants-auth-tenantmessage>
</div>


<script>
    console.log("From Blade:", "{{ $landlord_id }}");
</script>

@include('tenant.auth.partials.footer')
