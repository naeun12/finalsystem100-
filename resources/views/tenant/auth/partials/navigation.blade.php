@include('tenant.auth.partials.header')
@auth('tenant')
    <nav class="navbar navbar-expand-lg navbar-light bg-light d-flex justify-content-end custom-navbar-border mb-1">
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
                        <a class="nav-link nav-custom-link {{ request()->routeIs('tenant.message') ? 'active' : '' }}"
                            href="{{ route('tenant.message', ['tenant_id' => session('tenant_id')]) }}">
                            <i class="bi bi-chat-left-text me-1"></i> Message
                        </a>
                    </li>



                </ul>

                <!-- Right: Notifications + Avatar -->
                <div class="d-flex align-items-center p-2 gap-3 mt-3 mt-lg-0">
                    <!-- Notifications -->
                    <div class="dropdown">
                        <button class="btn position-relative p-0 border-0 bg-transparent" type="button"
                            id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            aria-label="Notifications">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"
                                class="bi bi-bell-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901" />
                            </svg>
                            <!-- Notification badge -->
                            @if ($unread_count > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{ $unread_count }}
                                    <span class="visually-hidden">unread notifications</span>
                                </span>
                            @endif


                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2" aria-labelledby="notificationDropdown"
                            style="min-width: 300px;">
                            <li class="dropdown-header fw-bold text-center text-primary">Notifications</li>

                            @forelse($notifications as $notif)
                                <li>
                                    <a href="#" class="dropdown-item d-flex align-items-start">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0d6efd"
                                            class="me-3 bi bi-bell-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901" />
                                        </svg>
                                        <div>
                                            <div class="fw-semibold">{{ $notif->title }}</div>
                                            <small class="text-muted">{{ $notif->message }}</small>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="text-center text-muted px-3 py-2">
                                    No notifications yet.
                                </li>
                            @endforelse

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li class="text-center">
                                <a href="{{ route('view.notifications.tenant', ['tenant_id' => session('tenant_id')]) }}"
                                    class="dropdown-item text-primary fw-bold">
                                    See All Notifications
                                </a>
                            </li>


                        </ul>
                    </div>

                </div>

                <!-- User Avatar -->
                <div class="dropdown">
                    <a class="d-flex align-items-center text-black text-decoration-none dropdown-toggle" href="#"
                        id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ asset(session('profilePicUrl')) }}" alt="User Avatar" width="40" height="40"
                            class="rounded-circle me-2">
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
