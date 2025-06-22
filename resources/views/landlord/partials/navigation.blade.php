@include('landlord.partials.header')

<nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
    <div class="container-fluid">
        <!-- Logo and Brand -->
        <a class="navbar-brand d-flex align-items-center text-black" href="{{ route('landingpage') }}">
            <img src="{{ asset('images/Logo/logo.png') }}" alt="Company Logo" width="50" class="me-2">
            <span class="logo-text fw-bold">DormHub</span>
        </a>

        <!-- Toggler for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav gap-lg-3 gap-2 px-3 py-2 text-center text-lg-start">
                <li class="nav-item">
                    <a class="nav-link nav-btn" href="{{ route('landingpage') }}">Home</a>
                </li>

                <!-- About Us Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle nav-btn" href="#" id="aboutUsDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        About Us
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="aboutUsDropdown">
                        <li>
                            <a class="dropdown-item text-center text-black" href="{{ route('landingpage') }}">About
                                Us</a>
                        </li>
                        <li>
                            <a class="dropdown-item text-center text-black" href="{{ route('landingpage') }}"
                                onclick="showOurTeam()">Our Team</a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-btn" href="{{ route('landingpage') }}">Contact Us</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-btn" href="{{ route('landingpage') }}">FAQs</a>
                </li>
            </ul>

            <!-- Tenant Button -->
            <div class="d-flex justify-content-center mt-2 mt-lg-0">
                <a href="{{ route('login-tenant') }}" class="btn btn-outline-primary px-4">Tenant</a>
            </div>
        </div>
    </div>
</nav>
