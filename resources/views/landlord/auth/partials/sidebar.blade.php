@include('landlord.auth.partials.header')
<div class="col-md-2 bg-white sidebar py-3" id="sidebarMenu">
    <a class="navbar-brand d-flex align-items-center text-black">
        <img src="{{ asset('images/Logo/logo.png') }}" alt="Company Logo" width="100" class="me-2">
        <span class="logo-text fw-bold">DormHub</span>
    </a>
    <ul class="list-group list-group-flush py-3 ">
        <li class="list-group-item">
            <a href="{{ route('landlordDashboard') }}"
                class="text-decoration-none text-dark d-flex align-items-center gap-2">
                <i class="bi bi-speedometer2 text-primary"></i>
                Dashboard
            </a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('landlordDormManagement', ['landlordId' => session('landlord_id')]) }}"
                class="text-decoration-none d-flex align-items-center gap-2 text-dark d-block py-2 px-3 rounded hover-bg">
                <i class="bi bi-house-add fs-5 text-primary"></i>
                <span class="fw-semibold">Add or Manage Dorm Listing</span>
            </a>
        </li>

        <li class="list-group-item">
            <a href="{{ route('landlordRoomManagement', ['landlordId' => session('landlord_id')]) }}"
                class="text-decoration-none d-flex align-items-center gap-2 text-dark py-2 px-3 rounded hover-bg">
                <i class="bi bi-door-open fs-5 text-primary"></i>
                <span class="fw-semibold">Manage Rooms</span>
            </a>
        </li>
        <li class="list-group-item border-0 px-0">
            <a class="d-flex justify-content-between align-items-center text-decoration-none fw-semibold py-2 px-3 bg-light rounded shadow-sm"
                data-bs-toggle="collapse" href="#tenantManagementMenu" role="button" aria-expanded="false"
                aria-controls="tenantManagementMenu">
                <div class="text-dark">
                    <i class="bi bi-calendar2-fill me-2 text-primary fs-5"></i>
                    Booking and Approval
                </div>
                <i class="bi bi-caret-down-fill text-secondary"></i>
            </a>

            <ul class="collapse list-unstyled ms-4 mt-2" id="tenantManagementMenu">
                <li class="mb-2">
                    <a href="{{ route('booking.index', ['landlord_id' => session('landlord_id')]) }}"
                        class="text-decoration-none text-dark d-flex align-items-center gap-2 px-2 py-1 rounded hover-effect">
                        <i class="bi bi-calendar-check-fill text-success fs-5"></i>
                        <span class="fw-medium">Booking Approval</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('reservation.index', ['landlord_id' => session('landlord_id')]) }}"
                        class="text-decoration-none text-dark d-flex align-items-center gap-2 px-2 py-1 rounded hover-effect">
                        <i class="bi bi-calendar-plus-fill text-info fs-5"></i>
                        <span class="fw-medium">Reservation Approval</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="list-group-item">
            <a class="text-decoration-none text-dark d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill text-info fs-5"></i> <!-- 👈 Bootstrap icon for group -->
                All Tenants
            </a>
        </li>


        <li class="list-group-item">
            <a href="{{ route('AnalyticsPage') }}"
                class="text-decoration-none text-dark d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart-line-fill text-warning"></i>
                Analytics Dashboard
            </a>
        </li>

        <li class="list-group-item">
            <a href="{{ route('MessagingPage') }}"
                class="text-decoration-none text-dark d-flex align-items-center gap-2">
                <i class="bi bi-chat-dots-fill text-success"></i>
                Messaging Center
            </a>
        </li>

        <li class="list-group-item">
            <a href="{{ route('ReviewandFeedback') }}"
                class="text-decoration-none text-dark d-flex align-items-center gap-2">
                <i class="bi bi-star-fill text-info"></i>
                Review & Feedback
            </a>
        </li>

        <li class="list-group-item">
            <a href="{{ route('NotificationPage') }}"
                class="text-decoration-none text-dark d-flex align-items-center gap-2">
                <i class="bi bi-bell-fill text-danger"></i>
                Notifications
            </a>
        </li>

    </ul>
</div>
<style>

</style>
