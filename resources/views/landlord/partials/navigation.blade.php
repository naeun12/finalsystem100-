@include('landlord.partials.header')
<header>
    <nav class="navbar navbar-expand-lg bg-light shadow-sm fixed-top">
        <div class="container-fluid px-4">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center text-dark fw-bold" href="{{ route('landingpage') }}">
                <img src="{{ asset('images/Logo/logo.png') }}" alt="Company Logo" width="55" class="me-2">
                <span class="logo-text">DormHub</span>
            </a>

            <!-- Toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-3 me-3">
                    <!-- Home -->
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark" href="#home">
                            <i class="bi bi-house-door-fill me-2"></i> Home
                        </a>
                    </li>

                    <!-- About Us Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold text-dark" href="#about-us" id="aboutUsDropdown"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-info-circle-fill me-2"></i> About Us
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm" aria-labelledby="aboutUsDropdown">
                            <li>
                                <a class="dropdown-item text-dark" href="#about-us">
                                    <i class="bi bi-building-fill me-2"></i> About Us
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-dark" href="#ourteam" onclick="showOurTeam()">
                                    <i class="bi bi-people-fill me-2"></i> Our Team
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Contact -->
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark" href="#contact-us">
                            <i class="bi bi-telephone-fill me-2"></i> Contact Us
                        </a>
                    </li>

                    <!-- FAQs -->
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark" href="#faqs">
                            <i class="bi bi-question-circle-fill me-2"></i> FAQs
                        </a>
                    </li>
                </ul>

                <!-- Auth Buttons -->
                <div class="d-flex gap-2">
                    <a href="{{ route('login-tenant') }}" class="btn  fw-semibold">Tenant</a>
                    <a class="btn" disabled>Landlord</a>
                </div>
            </div>
        </div>
    </nav>


</header>

<!-- Include the Vite-compiled CSS and JS files -->
