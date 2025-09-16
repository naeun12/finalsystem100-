@if (session('landlord_logged_in'))
    <header class="bg-white nav-header py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <button class="btn btn-primary d-md-none" type="button" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <h4 class="text-black fs-5">{{ $headerName }}</h4>

        <div class="user-profile text-black d-flex align-items-center gap-3">

            <!-- Notification Bell with Dropdown -->

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
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
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
                        <a href="{{ route('notifications.landlord', ['landlord_id' => session('landlord_id')]) }}" class="dropdown-item text-primary fw-bold">
                            See All Notifications
                        </a>
                    </li>
                </ul>
            </div>



            <!-- User profile dropdown -->
           <div class="dropdown">
    <a class="d-flex align-items-center text-black text-decoration-none dropdown-toggle" 
       href="#" 
       role="button" 
       id="userDropdown" 
       data-bs-toggle="dropdown" 
       aria-expanded="false">
        <img src="{{ asset(session('landlord_avatar')) }}" 
             alt="User Avatar" 
             width="45" 
             height="45"
             class="rounded-circle me-2">

        <span class="username fw-semibold">
            {{ session('landlord_firstname') }} {{ session('landlord_lastname') }}
        </span>
    </a>

    <ul class="dropdown-menu dropdown-menu-end mt-2 shadow" aria-labelledby="userDropdown">
        <li>
            <a class="dropdown-item" href="{{ route('landlord.account.update', ['landlordId' => session('landlord_id')]) }}">
                View Profile
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                @csrf
                <button type="submit" class="dropdown-item" style="cursor: pointer;">Logout</button>
            </form>
        </li>
    </ul>
</div>

        </div>
        <!--End Modal-->
    </header>
@endif
