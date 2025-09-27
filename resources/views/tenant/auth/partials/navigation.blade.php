@include('tenant.auth.partials.header')
@auth('tenant')
    <nav class="navbar navbar-expand-lg navbar-light bg-light d-flex justify-content-end custom-navbar-border mb-1">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center text-black" href="#">
                <img src="{{ asset('images/Logo/logo.png') }}" alt="Company Logo" width="100" class="me-2">
                <span class="logo-text">DormHub</span>
            </a>

            <!-- Offcanvas Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#tenantNav"
                aria-controls="tenantNav" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Desktop Navbar -->
            <div class="collapse navbar-collapse d-none d-lg-flex justify-content-between" id="navbarMain">
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
                            href="{{ route('dormitories', ['tenant_id' => session('tenant_id')]) }}">
                            <i class="bi bi-building me-1"></i> Dormitories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-custom-link {{ request()->routeIs('tenant.message') ? 'active' : '' }}"
                            href="{{ route('tenant.message', ['tenant_id' => session('tenant_id')]) }}">
                            <i class="bi bi-chat-left-text me-1"></i> Message
                        </a>
                    </li>
                </ul>

                <!-- Right: Notifications + Avatar -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="btn position-relative p-0 border-0 bg-transparent" type="button"
                            id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell-fill fs-5"></i>
                            @if ($unread_count > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $unread_count }}
                                </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2"
                            aria-labelledby="notificationDropdown" style="min-width: 300px;">
                            <li class="dropdown-header fw-bold text-center text-primary">Notifications</li>
                            @forelse($notifications as $notif)
                                <li>
                                    <a href="#" class="dropdown-item d-flex align-items-start">
                                        <i class="bi bi-bell-fill text-primary me-2"></i>
                                        <div>
                                            <div class="fw-semibold">{{ $notif->title }}</div>
                                            <small class="text-muted">{{ $notif->message }}</small>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="text-center text-muted px-3 py-2">No notifications yet.</li>
                            @endforelse
                            <li><hr class="dropdown-divider"></li>
                            <li class="text-center">
                                <a href="{{ route('view.notifications.tenant', ['tenant_id' => session('tenant_id')]) }}"
                                    class="dropdown-item text-primary fw-bold">
                                    See All Notifications
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- User Avatar -->
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-black text-decoration-none dropdown-toggle"
                            href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                           <img src="{{ asset('storage/' . session('profilePicUrl')) }}" 
                             alt="User Avatar" width="40" height="40" class="rounded-circle me-2">

                            <span class="fw-semibold d-none d-md-inline">
                                {{ session('tenant_firstname') }} {{ session('tenant_lastname') }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="userDropdown">
                            <li>
                                <a href="{{ route('tenant.update', ['tenant_id' => session('tenant_id')]) }}" class="dropdown-item">
                                    View Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
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

    <!-- Offcanvas Sidebar for Mobile -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="tenantNav" aria-labelledby="tenantNavLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="tenantNavLabel">DormHub</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav gap-3">
                <li><a class="nav-link {{ request()->routeIs('homepage') ? 'active' : '' }}"
                        href="{{ route('homepage', ['tenant_id' => session('tenant_id')]) }}">
                        <i class="bi bi-house-door-fill me-1"></i> Home</a>
                </li>
                <li><a class="nav-link {{ request()->routeIs('dorm.map') ? 'active' : '' }}"
                        href="{{ route('dorm.map', ['tenant_id' => session('tenant_id')]) }}">
                        <i class="bi bi-geo-alt-fill me-1"></i> Dorm Location</a>
                </li>
                <li><a class="nav-link {{ request()->routeIs('dormitories') ? 'active' : '' }}"
                        href="{{ route('dormitories', ['tenant_id' => session('tenant_id')]) }}">
                        <i class="bi bi-building me-1"></i> Dormitories</a>
                </li>
                <li><a class="nav-link {{ request()->routeIs('tenant.message') ? 'active' : '' }}"
                        href="{{ route('tenant.message', ['tenant_id' => session('tenant_id')]) }}">
                        <i class="bi bi-chat-left-text me-1"></i> Message</a>
                </li>
            </ul>
        </div>
    </div>
@endauth
