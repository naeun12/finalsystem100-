@include('tenant.auth.partials.navigation')
<p>{{ $tenant_id }}</p>
<!-- Hero Section with Full-Width Carousel -->
<section class="hero-section py-0">
    <div id="RoomDetails" data-dorm-id="{{ $dormitory_id }}" data-tenant-id="{{ $tenant_id }}">
        <tenants-auth-RoomDetails></tenants-auth-RoomDetails>
    </div>
</section>
<!-- HTML -->


@include('tenant.auth.partials.footer')
