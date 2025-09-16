@include('landingpage.partials.header')
<header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
        <div class="container-fluid navigation-style">
            <a class="navbar-brand text-black px-2" href="#">
                <img src="{{ asset('images/Logo/logo.png') }}" alt="Company Logo" width="75">
                <span class="ml-2 logo-text"> DormHub </span>
            </a>

            <!-- Offcanvas Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav"
                aria-controls="mobileNav" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Normal Navbar (desktop view) -->
            <div class="collapse navbar-collapse justify-content-end px-5 d-none d-lg-flex" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-3">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-black px-3 py-2" href="#home">
                            <i class="bi bi-house-door-fill me-2 text-black"></i> Home
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold text-black px-3 py-2" href="#about-us"
                            id="aboutUsDropdownDesktop" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-info-circle-fill me-2 text-black"></i> About Us
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm"
                            aria-labelledby="aboutUsDropdownDesktop">
                            <li>
                                <a class="dropdown-item text-black text-center" href="#about-us">
                                    <i class="bi bi-building-fill me-2"></i> About Us
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-black text-center" href="#ourteam"
                                    onclick="showOurTeam()">
                                    <i class="bi bi-people-fill me-2"></i> Our Team
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-black px-3 py-2" href="#contact-us">
                            <i class="bi bi-telephone-fill me-2 text-black"></i> Contact Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark px-3 py-2" href="#faqs">
                            <i class="bi bi-question-circle-fill me-2 text-black"></i> FAQs
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Offcanvas (Mobile Sidebar) -->
    <div class="offcanvas offcanvas-start bg-light" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
        <div class="offcanvas-header">
        <a class="navbar-brand text-black px-2" href="#">
                <img src="{{ asset('images/Logo/logo.png') }}" alt="Company Logo" width="75">
                <span class="ml-2 logo-text"> DormHub </span>
            </a>            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav gap-3">
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-black" href="#home">
                        <i class="bi bi-house-door-fill me-2 text-black"></i> Home
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle fw-semibold text-black" href="#about-us"
                        id="aboutUsDropdownMobile" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-info-circle-fill me-2 text-black"></i> About Us
                    </a>
                    <ul class="dropdown-menu border-0 shadow-sm" aria-labelledby="aboutUsDropdownMobile">
                        <li>
                            <a class="dropdown-item text-black" href="#about-us">
                                <i class="bi bi-building-fill me-2"></i> About Us
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-black" href="#ourteam" onclick="showOurTeam()">
                                <i class="bi bi-people-fill me-2"></i> Our Team
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-black" href="#contact-us">
                        <i class="bi bi-telephone-fill me-2 text-black"></i> Contact Us
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-dark" href="#faqs">
                        <i class="bi bi-question-circle-fill me-2 text-black"></i> FAQs
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
