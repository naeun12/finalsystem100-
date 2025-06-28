@include('tenant.auth.partials.header')
@if (session('tenant_logged_in'))
    <nav class="navbar navbar-expand-lg navbar-light bg-light d-flex justify-items-end custom-navbar-border mb-1">
        <div class="container-fluid  d-flex justify-content-between align-items-center">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center text-black" href="#">
                <img src="{{ asset('images/Logo/logo.png') }}" alt="Company Logo" width="100" class="me-2">
                <span class="logo-text">DormHub</span>
            </a>

            <!-- Hamburger Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
                aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible Content -->
            <div class="collapse navbar-collapse position-relative" id="navbarMain">
                <!-- Left: Nav Links -->
                <ul class="navbar-nav align-items-center gap-4 mx-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-custom-link {{ request()->routeIs('homepage') ? 'active' : '' }}"
                            href="{{ route('homepage', ['tenant_id' => session('tenant_id')]) }}">
                            <i class="bi bi-house-door-fill me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-custom-link {{ request()->routeIs('dorm.map') ? 'active' : '' }}"
                            href="{{ route('dorm.map', ['tenant_id' => session('tenant_id')]) }}">
                            <i class="bi bi-geo-alt-fill me-1"></i> Dorm Location
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-custom-link {{ request()->routeIs('dormitories') ? 'active' : '' }}"
                            href="{{ route('dormitories', ['tenant_id' => session('tenant_id')]) }}"><i
                                class="bi bi-building me-1"></i>
                            Dormitories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-custom-link" href="#"><i class="bi bi-chat-left-text me-1"></i>
                            Message</a>
                    </li>
                </ul>

                <!-- Right: Notifications + Avatar -->
                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <!-- Notifications -->
                    <div class="nav-item dropdown">
                        <button
                            class="btn p-0 border-0 bg-transparent nav-link dropdown-toggle d-flex align-items-center"
                            id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            aria-label="Notifications" style="cursor:pointer;">
                            <i class="bi bi-bell-fill fs-5"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2"
                            aria-labelledby="notificationDropdown">
                            <!-- Notification items -->
                            <li class="dropdown-item">No new notifications</li>
                        </ul>
                    </div>

                    <!-- User Avatar -->
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-black text-decoration-none dropdown-toggle"
                            href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset(session('profile_pic_url')) }}" alt="User Avatar" width="40"
                                height="40" class="rounded-circle me-2">
                            <span class="fw-semibold d-none d-md-inline">
                                {{ session('tenant_firstname') }} {{ session('tenant_lastname') }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#contactModal">View
                                    Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('tenant.logout') }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
@endif


<style>

</style>
