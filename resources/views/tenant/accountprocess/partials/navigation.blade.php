@include('tenant.accountprocess.partials.header')
<header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
        <div class="container-fluid navigation-style">
            <a class="navbar-brand text-black px-2" href="#">
                <img src="{{ asset('images/Logo/logo.png') }}" alt="Company Logo" width="75">
                <span class="ml-2 logo-text"> DormHub </span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Bootstrap Icons -->

            <!-- Navbar (Inside your main layout) -->
            <div class="collapse navbar-collapse justify-content-end px-5" id="navbarNav">
                <ul class="navbar-nav align-items-center gap-3">
                    <!-- Home -->
                    <li class="nav-item w-25">
                        <a class="nav-link fw-semibold text-black px-3 py-2" href="#home">
                            <i class="bi bi-house-door-fill me-2 text-black"></i> Home
                        </a>
                    </li>

                    <!-- About Us Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold text-black px-3 py-2" href="#about-us"
                            id="aboutUsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-info-circle-fill me-2 text-black"></i> About Us
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end bg-dark border-0 shadow-sm"
                            aria-labelledby="aboutUsDropdown">
                            <li>
                                <a class="dropdown-item text-black text-center" href="#about-us">
                                    <i class="bi bi-building-fill me-2"></i> About Us
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-black text-center" href="#ourteam" onclick="showOurTeam()">
                                    <i class="bi bi-people-fill me-2"></i> Our Team
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Contact -->
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-black px-3 py-2" href="#contact-us">
                            <i class="bi bi-telephone-fill me-2 text-black"></i> Contact Us
                        </a>
                    </li>

                    <!-- FAQs -->
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-dark px-3 py-2" href="#faqs">
                            <i class="bi bi-question-circle-fill me-2 text-black"></i> FAQs
                        </a>
                    </li>
                </ul>
            </div>


        </div>
    </nav>
    <button class="btn py-3 "> <a href="{{ route('landlord-Login') }}"
            class="text-decoration-none">Landlord</a></button>

</header>

<!-- Include the Vite-compiled CSS and JS files -->
