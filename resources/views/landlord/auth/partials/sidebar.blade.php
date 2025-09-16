@include('landlord.auth.partials.header')
<div class="col-md-2 bg-white sidebar py-3" id="sidebarMenu">
    <a class="navbar-brand d-flex align-items-center text-black">
        <img src="{{ asset('images/Logo/logo.png') }}" alt="Company Logo" width="100" class="me-2">
        <span class="logo-text fw-bold">DormHub</span>
    </a>
    <ul class="list-group list-group-flush py-3">
        <li class="list-group-item">
            <a href="{{ route('landlord.dashboard', ['landlordId' => session('landlord_id')]) }}"
                class="text-decoration-none d-flex align-items-center gap-2 px-2 py-1 rounded hover-effect
                    {{ request()->routeIs('landlord.dashboard') ? 'active-item' : 'text-dark' }}">
                <i class="bi bi-speedometer2 fs-5 text-primary"></i>
                Dashboard
            </a>
        </li>

        <li class="list-group-item">
            <a href="{{ route('landlordDormManagement', ['landlordId' => session('landlord_id')]) }}"
                class="text-decoration-none d-flex align-items-center gap-2 px-2 py-1 rounded hover-effect
                    {{ request()->routeIs('landlordDormManagement') ? 'active-item' : 'text-dark' }}">
                <i class="bi bi-house-add fs-5 text-primary"></i>
                <span class="fw-semibold">Add or Manage Dorm Listing</span>
            </a>
        </li>

        <li class="list-group-item">
            <a href="{{ route('landlordRoomManagement', ['landlordId' => session('landlord_id')]) }}"
                class="text-decoration-none d-flex align-items-center gap-2 px-2 py-1 rounded hover-effect
                    {{ request()->routeIs('landlordRoomManagement') ? 'active-item' : 'text-dark' }}">
                <i class="bi bi-door-open fs-5 text-primary"></i>
                <span class="fw-semibold">Manage Rooms</span>
            </a>
        </li>

        {{-- Booking & Reservation --}}
        <li class="list-group-item border-0 px-0">
            <a class="d-flex justify-content-between align-items-center text-decoration-none fw-semibold py-2 px-3 bg-light rounded shadow-sm"
                data-bs-toggle="collapse" href="#tenantManagementMenu" role="button"
                aria-expanded="{{ request()->routeIs('booking.index') || request()->routeIs('reservation.index') ? 'true' : 'false' }}"
                aria-controls="tenantManagementMenu">
                <div class="text-dark">
                    <i class="bi bi-calendar2-fill me-2 text-primary fs-5"></i>
                    Booking & Reservation Approval
                </div>
                <i class="bi bi-caret-down-fill text-secondary"></i>
            </a>

            <ul class="collapse list-unstyled ms-4 mt-2 {{ request()->routeIs('booking.index') || request()->routeIs('reservation.index') ? 'show' : '' }}"
                id="tenantManagementMenu">
                <li class="mb-2">
                    <a href="{{ route('booking.index', ['landlord_id' => session('landlord_id')]) }}"
                        class="text-decoration-none d-flex align-items-center gap-2 px-2 py-1 rounded hover-effect
                            {{ request()->routeIs('booking.index') ? 'active-item' : 'text-dark' }}">
                        <i class="bi bi-calendar-check-fill text-success fs-5"></i>
                        <span class="fw-medium">Booking Approval</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('reservation.index', ['landlord_id' => session('landlord_id')]) }}"
                        class="text-decoration-none d-flex align-items-center gap-2 px-2 py-1 rounded hover-effect
                            {{ request()->routeIs('reservation.index') ? 'active-item' : 'text-dark' }}">
                        <i class="bi bi-calendar-plus-fill text-info fs-5"></i>
                        <span class="fw-medium">Reservation Approval</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="list-group-item">
            <a href="{{ route('all.tenants.index', ['landlord_id' => session('landlord_id')]) }}"
                class="text-decoration-none d-flex align-items-center gap-2 px-2 py-1 rounded hover-effect
                    {{ request()->routeIs('all.tenants.index') ? 'active-item' : 'text-dark' }}">
                <i class="bi bi-person-lines-fill text-info fs-5"></i>
                All Tenants
            </a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('message.landlord', ['landlord_id' => session('landlord_id')]) }}"
                class="text-decoration-none d-flex align-items-center gap-2 px-2 py-1 rounded hover-effect
                    {{ request()->routeIs('message.landlord') ? 'active-item' : 'text-dark' }}">
                <i class="bi bi-chat-dots-fill text-success fs-5"></i>
                Messages
            </a>
        </li>

        <li class="list-group-item">
            <a href="{{ route('notifications.landlord', ['landlord_id' => session('landlord_id')]) }}"
                class="text-decoration-none d-flex align-items-center gap-2 px-2 py-1 rounded hover-effect
            {{ request()->routeIs('notifications.landlord') ? 'active-item' : 'text-dark' }}">
                <i class="bi bi-bell-fill text-warning fs-5"></i>
                Notifications
            </a>
        </li>

        <li class="list-group-item">
            <a href="{{ route('payment.landlord', ['landlord_id' => session('landlord_id')]) }}"
                class="text-decoration-none d-flex align-items-center gap-2 px-2 py-1 rounded hover-effect
        {{ request()->routeIs('payment.landlord') ? 'active-item' : 'text-dark' }}">
                <i class="bi bi-credit-card-fill text-primary fs-5"></i>
                Payment Account Verification
            </a>
        </li>

    </ul>
</div>

{{-- Highlight CSS --}}
<style>
    .active-item {
        background-color: #0d6efd !important;
        /* Bootstrap primary color */
        color: white !important;
        font-weight: bold;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
    }

    .active-item i {
        color: white !important;
    }

    .hover-effect:hover {
        background-color: #f1f1f1;
    }
</style>
