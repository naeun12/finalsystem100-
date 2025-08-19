@include('tenant.auth.partials.navigation')
<!-- Hero Section with Full-Width Carousel -->
<section class="hero-section py-0">
    <div id="reviewandrating" data-dorm-id="{{ $dormitory_id }}" data-tenant-id="{{ $tenant_id }}">
        <tenants-auth-reviewandrating></tenants-auth-reviewandrating>
    </div>
</section>
<!-- HTML -->


@include('tenant.auth.partials.footer')
