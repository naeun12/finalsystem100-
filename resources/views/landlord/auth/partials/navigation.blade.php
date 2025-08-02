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
                        <a href="{{ route('NotificationPage') }}" class="dropdown-item text-primary fw-bold">
                            See All Notifications
                        </a>
                    </li>
                </ul>
            </div>



            <!-- User profile dropdown -->
            <div class="dropdown">
                <a class="d-flex align-items-center text-black text-decoration-none dropdown-toggle" href="#"
                    role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ asset(session('landlord_avatar')) }}" alt="User Avatar" width="45" height="45"
                        class="rounded-circle me-2">




                    <span class="username fw-semibold"> {{ session('landlord_firstname') }}
                        {{ session('landlord_lastname') }}</span>
                </a>
                <span class="username fw-semibold">
                    <ul class="dropdown-menu dropdown-menu-end mt-2" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#contactModal">View
                                Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                @csrf
                                <button type="submit" class="dropdown-item" style="cursor: pointer;">Logout</button>
                            </form>
                        </li>
                    </ul>

            </div>


        </div>
        <!--Modal Update User-->
        <!-- Contact Us Modal -->
        <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content shadow-lg border-0 rounded-4">
                    <!-- Modal Header -->
                    <div class="modal-header  text-black rounded-top-4">
                        <h5 class="modal-title fw-bold" id="contactModalLabel">
                            🛠️ Update Landlord Account
                        </h5>
                        <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body px-4 py-3">
                        <form id="contactForm" novalidate>
                            <div class="row g-4">
                                <!-- Left Column -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold required">Firstname</label>
                                    <input type="text" class="form-control shadow-sm" id="firstname" name="firstname"
                                        placeholder="Enter your first name" required>

                                    <label class="form-label fw-semibold mt-3 required">Lastname</label>
                                    <input type="text" class="form-control shadow-sm" id="lastname" name="lastname"
                                        placeholder="Enter your last name" required>

                                    <label class="form-label fw-semibold mt-3 required">Email</label>
                                    <input type="email" class="form-control shadow-sm" id="email"
                                        name="email" placeholder="Enter your email" required>

                                    <label class="form-label fw-semibold mt-3 required">Phone Number</label>
                                    <input type="tel" class="form-control shadow-sm" id="phone"
                                        name="phonenumber" placeholder="+63 912 345 6789" pattern="^\+?\d{7,15}$"
                                        required>
                                    <div class="form-text">Include country code, e.g., +63</div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold required">Gender</label>
                                    <select class="form-select shadow-sm" id="gender" name="gender" required>
                                        <option disabled selected value="">Select gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>

                                    <label class="form-label fw-semibold mt-3">Role</label>
                                    <input type="text" class="form-control shadow-sm" id="role"
                                        name="role" readonly value="Landlord">

                                    <label class="form-label fw-semibold mt-3">Profile Picture</label>
                                    <input class="form-control shadow-sm" type="file" id="profilePic"
                                        name="profilePicUrl" accept="image/*" @change="previewImage">

                                    <!-- Image Preview -->
                                    <div class="mt-3 text-center" v-if="previewImageUrl">
                                        <img :src="previewImageUrl" alt="Preview"
                                            class="img-thumbnail rounded-circle"
                                            style="width: 120px; height: 120px; object-fit: cover;">
                                        <p class="small text-muted mt-2">Preview</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer border-top-0 px-4 py-3">
                        <button type="submit" form="contactForm" class="btn btn-primary fw-semibold px-4">
                            <i class="bi bi-check-circle me-1"></i> Update
                        </button>
                        <button type="button" class="btn btn-outline-danger px-4" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!--End Modal-->
    </header>
@endif
