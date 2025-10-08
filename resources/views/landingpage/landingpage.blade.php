@include('landingpage.partials.navigation')
<main>

    <!-- Home Section -->
    <section id="home" class="py-5">

        <div class="container-fluid px-0 position-relative" style="height: 80vh; overflow: hidden;">
            <!-- Background Image -->
            <img src="{{ asset('images/landingpage/giphy.gif') }}" alt="Person sitting on a bunk bed"
                class="w-100 h-100 position-absolute top-0 start-0" style="object-fit: cover; filter: brightness(0.4);" />

            <!-- Optional dark gradient overlay -->
            <div class="position-absolute top-0 start-0 w-100 h-100"
                style="
                background: linear-gradient(to right, rgba(0, 0, 0, 0.6), rgba(0,0,0,0.2));
                z-index: 1;">
            </div>

            <!-- Content Overlay -->
            <div class="position-relative z-2 h-75 d-flex align-items-center justify-content-center px-3 px-md-5">
                <div class="bg-white bg-opacity-10 text-white p-5 rounded-4 shadow-lg backdrop-blur"
                    style="max-width: 700px; width: 100%; border: 1px solid rgba(255,255,255,0.15);">

                    <h1 class="fw-bold mb-3 text-white" style="font-size: 2.8rem;">
                        Welcome to <span style="color: #4edce2;">DormHub</span>
                    </h1>
                    <p class="lead" style="color: rgba(255, 255, 255, 0.85);">
                        Your Ultimate Solution for Dormitory House Management in Lapu-Lapu and Mandaue City
                    </p>
                    <p class="mb-4" style="color: rgba(255, 255, 255, 0.75);">
                        DormHub connects students, professionals, and travelers to top-notch dorms while making
                        management easier for landlords and tenants.
                    </p>

                    <p class="mb-2">Sign up as:</p>
                    <div class="d-flex gap-3 flex-column flex-sm-row">
                        <a href="{{ route('landlord-Login') }}"
                            class="btn  text-white rounded-pill px-4 fw-semibold shadow">
                            Landlord
                        </a>
                        <a href="{{ route('login-tenant') }}" class="btn  rounded-pill px-4 fw-semibold">
                            Tenant
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cards Section (Overlap sa Background Image) -->
        <div class="container position-relative" style="margin-top: -120px; z-index: 5;">
            <h2 class="text-center fw-bold mb-5 text-white">✨ Benefits of DormHub ✨</h2>
            <div class="row g-4">

                <!-- Card 1 -->
                <div class="col-md-4">
                    <div class="card shadow-lg border-0 rounded-4 h-100 hover-card text-center p-4">
                        <div class="icon-wrapper mb-3">
                            📍
                        </div>
                        <h5 class="fw-bold text-gradient">Easy Search</h5>
                        <p class="text-muted">
                            Quickly find dormitories around Lapu-Lapu and Mandaue City with filters for price, location, and amenities.
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-md-4">
                    <div class="card shadow-lg border-0 rounded-4 h-100 hover-card text-center p-4">
                        <div class="icon-wrapper mb-3">
                            💸
                        </div>
                        <h5 class="fw-bold text-gradient">Affordable Options</h5>
                        <p class="text-muted">
                            Access budget-friendly dorms suited for students and workers with transparent pricing.
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-md-4">
                    <div class="card shadow-lg border-0 rounded-4 h-100 hover-card text-center p-4">
                        <div class="icon-wrapper mb-3">
                            🔒
                        </div>
                        <h5 class="fw-bold text-gradient">Safe & Verified</h5>
                        <p class="text-muted">
                            All listings are verified to ensure secure and trustworthy accommodations.
                        </p>
                    </div>
                </div>

                <!-- Card 4 -->
              <div class="col-md-4">
            <div class="card shadow-lg border-0 rounded-4 h-100 hover-card text-center p-4">
                <div class="icon-wrapper mb-3">
                    🏠
                </div>
                <h5 class="fw-bold text-gradient">Booking & Reservation</h5>
                <p class="text-muted">
                    Easily reserve your preferred dorm room or instantly book and confirm your stay 
                    with our hassle-free system.
                </p>
            </div>
        </div>


                <!-- Card 5 -->
                        <div class="col-md-4">
                <div class="card shadow-lg border-0 rounded-4 h-100 hover-card text-center p-4">
                    <div class="icon-wrapper mb-3">
                        🌐
                    </div>
                    <h5 class="fw-bold text-gradient">User-Friendly</h5>
                    <p class="text-muted">
                        Experience a clean and easy-to-use web platform accessible anytime on your browser.
                    </p>
                </div>
            </div>


                <!-- Card 6 -->
                <div class="col-md-4">
                    <div class="card shadow-lg border-0 rounded-4 h-100 hover-card text-center p-4">
                        <div class="icon-wrapper mb-3">
                            🤝
                        </div>
                        <h5 class="fw-bold text-gradient">Community Support</h5>
                        <p class="text-muted">
                            Connect with landlords and co-tenants, fostering a safe and supportive community.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mt-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-uppercase border-bottom pb-2 d-inline-block" style="color: #4edce2;">
                    <i class="bi bi-building me-2"></i>Available Dorms
                </h2>
                <p class="text-muted mt-2">Explore top dormitory options in Lapu-Lapu and Mandaue City.</p>
            </div>
        </div>
        <div class="m-2 py-4">
            <div class="row g-4">
                <!-- Large Left Card -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 dorm-card border-0 shadow-sm position-relative overflow-hidden">
                        <!-- Full-size image -->
                        <img src="{{ asset('images/landingpage/dorm1.jpg') }}" class="img-fluid w-100 h-100"
                            alt="Sunshine Dormitory Image" style="object-fit: cover; height: 300px;">

                        <!-- Overlay content -->
                        <div class="card-img-overlay d-flex flex-column justify-content-end"
                            style="background: linear-gradient(to top, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.1));">
                            <div class="text-white">
                                <h5 class="card-title">Sunshine Dormitory</h5>
                                <p class="card-text mb-1">📍 Mandaue City</p>
                                <p class="card-text">⭐ 4.8</p>
                                <div class="text-end">
                                    <button type="button" class="btn mt-2" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




                <!-- Right Column -->
                <div class="col-md-6">
                    <!-- Top Right Wide Card -->
                    <div class="card mb-4 border-0 shadow-sm position-relative overflow-hidden">
                        <!-- Full-width Image -->
                        <img src="{{ asset('images/landingpage/dorm2.webp') }}" class="img-fluid w-100"
                            alt="Palm Grove Dorm" style="object-fit: cover; height: 300px;">

                        <!-- Text Overlay -->
                        <div class="card-img-overlay d-flex flex-column justify-content-end"
                            style="background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);">
                            <div class="text-white">
                                <h5 class="card-title">Palm Grove Dorm</h5>
                                <p class="card-text mb-1">📍 Lapu-Lapu City</p>
                                <p class="card-text">⭐ 4.6</p>
                                <div class="text-end">
                                    <button type="button" class="btn  mt-2" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Two Smaller Cards -->
                    <div class="row g-4">
                        <!-- Lapu Cozy Dorm -->
                        <div class="col-md-6">
                            <div class="card dorm-card border-0 shadow-sm position-relative overflow-hidden">
                                <img src="{{ asset('images/landingpage/dorm4.jpg') }}" class="img-fluid w-100"
                                    alt="Lapu Cozy Dormitory Image" style="object-fit: cover; height: 250px;">
                                <div class="card-img-overlay d-flex flex-column justify-content-end"
                                    style="background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);">
                                    <div class="text-white">
                                        <h5 class="card-title">Lapu Cozy Dorm</h5>
                                        <p class="card-text mb-1">📍 Lapu-Lapu</p>
                                        <p class="card-text">⭐ 4.7</p>
                                        <div class="text-end">
                                            <button type="button" class="btn mt-2" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal">
                                                View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cityside Stay Dorm -->
                        <div class="col-md-6">
                            <div class="card dorm-card border-0 shadow-sm position-relative overflow-hidden">
                                <img src="{{ asset('images/landingpage/dorm3.jpg') }}" class="img-fluid w-100"
                                    alt="Cityside Stay Dormitory Image" style="object-fit: cover; height: 250px;">
                                <div class="card-img-overlay d-flex flex-column justify-content-end"
                                    style="background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);">
                                    <div class="text-white">
                                        <h5 class="card-title">Cityside Stay</h5>
                                        <p class="card-text mb-1">📍 Mandaue</p>
                                        <p class="card-text">⭐ 4.5</p>
                                        <div class="text-end">
                                            <button type="button" class="btn  mt-2" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal">
                                                View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dorm Listings -->


            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-lg border-0 rounded-4">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold text-primary" id="exampleModalLabel">Authentication
                                Required
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center py-4 px-4">
                            <i class="bi bi-person-circle text-primary fs-1 mb-3"></i>
                            <p class="mb-0 fs-5">To access the details, please log in to your account or sign up if
                                you're new here. It only takes a moment.</p>
                        </div>
                        <div class="modal-footer border-0 d-flex justify-content-center">
                            <a href="{{ route('login-tenant') }}" class="btn  px-4 py-2 rounded-pill fw-semibold">
                                Sign Up / Log In
                            </a>
                        </div>
                    </div>
                </div>
            </div>

    </section>

    <!-- About Us Section -->
    <section class="about-us section py-5" data-aos="zoom-out-left" id="about-us"
        style="background-color: #f9f9f9;">
        <div class="container">
            <div class="row align-items-center g-5">
                <!-- Left: Text Content -->
                <div class="col-md-6">
                    <h2 class="fw-bold text-uppercase mb-4 text-primary">
                        <i class="bi bi-info-circle-fill me-2"></i> About Us
                    </h2>
                    <p class="mb-3" style="font-size: 16px;">
                        Welcome to <strong>DormHub</strong>, the premier platform tailored for students in search of
                        dormitory accommodations across Mandaue and Lapu-Lapu City. We understand how crucial it is to
                        find safe, affordable, and convenient housing.
                    </p>
                    <p style="font-size: 14px;">
                        Our mission is to help you discover the perfect dorm — whether you're looking for proximity to
                        your school, essential amenities, or value-for-money pricing. DormHub provides detailed
                        listings, location maps, and school-distance info to make your decision easy and informed.
                    </p>
                    <div class="mt-4">
                        <button class="btn  rounded-pill px-4" onclick="showOurTeam()">
                            <i class="bi bi-people-fill me-2"></i> Meet Our Team
                        </button>
                    </div>
                </div>

                <!-- Right: Image -->
                <div class="col-md-6 text-center">
                    <img src="{{ asset('images/landingpage/aboutmap.png') }}" class="img-fluid rounded shadow-sm"
                        alt="Map of Cebu showing dormitory locations" />
                </div>
            </div>
        </div>
    </section>
    <section class="py-5 bg-light" id="ourteam" style="display: none;">
        <div class="container-lg">
            <h2 class="text-center mb-5 fw-bold text-primary">
                <i class="bi bi-people-fill me-2"></i> Our Team
            </h2>

            <!-- Project Manager -->
            <div class="text-center mb-5">
                <div class="mb-3">
                    <img src="{{ asset('images/ourteam/chloe.png') }}" alt="Project Manager"
                        class="rounded-circle shadow" style="width: 160px; height: 160px; object-fit: cover;">
                </div>
                <h5 class="fw-semibold mb-1">Project Manager</h5>
                <p class="mb-1 text-muted">AVERY CHLOE P. HOMOC</p>
                <p>
                    <i class="bi bi-envelope-fill me-1"></i>
                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=homocavery@gmail.com" target="_blank">
                        homocavery@gmail.com
                    </a>
                </p>
                <p><i class="bi bi-telephone-fill me-1"></i> 09165688223</p>
            </div>

            <!-- Team Members Grid -->
            <div class="row justify-content-center gy-5">
                <!-- Member Card -->
                @php
                    $members = [
                        [
                            'role' => 'Hacker',
                            'name' => 'LANCE  S. MONSANTO',
                            'email' => 'niiinaeun@gmail.com',
                            'phone' => '09326754339',
                            'img' => 'gwaps.png',
                        ],
                        [
                            'role' => 'Hipster',
                            'name' => 'Alyssa Sumile',
                            'email' => 'alyssa.sumile18@gmail.com',
                            'phone' => '09686114788',
                            'img' => 'alyssa.jpg',
                        ],
                        [
                            'role' => 'Additional Member 1',
                            'name' => 'Leande May Soronio',
                            'email' => 'leandemays@gmail.com',
                            'phone' => '09974672756',
                            'img' => 'lss-removebg-preview.png',
                        ],
                        [
                            'role' => 'Additional Member 2',
                            'name' => 'Giannne Isabelle Augusto',
                            'email' => 'Gianneaisabelle@gmail.com',
                            'phone' => '095670364107',
                            'img' => 'gianne-removebg-preview.png',
                        ],
                    ];
                @endphp

                @foreach ($members as $member)
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm text-center p-3 h-100">
                            <img src="{{ asset('images/ourteam/' . $member['img']) }}" alt="{{ $member['role'] }}"
                                class="rounded-circle mx-auto mb-3"
                                style="width: 130px; height: 130px; object-fit: cover;">
                            <h6 class="fw-bold">{{ $member['role'] }}</h6>
                            <p class="mb-1 text-muted">{{ $member['name'] }}</p>
                           <p class="mb-1">
    <i class="bi bi-envelope-fill me-1"></i> 
    <a href="https://mail.google.com/mail/?view=cm&fs=1&to={{ $member['email'] }}" target="_blank">
        {{ $member['email'] }}
    </a>
</p>

                            <p><i class="bi bi-telephone-fill me-1"></i> {{ $member['phone'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>



    <!-- Contact Section -->
    <section class="contact py-5 bg-light section" id="contact-us" data-aos="fade-up" data-aos-delay="100">
        <div class="container">
            <!-- Header -->
            <div class="text-center mb-5">
                <h3 class="fs-1 fw-bold text-primary">
                    <i class="bi bi-envelope-paper-fill me-2"></i>Contact Us
                </h3>
                <p class="text-muted">We’d love to hear from you. Reach out to us anytime!</p>
            </div>

            <!-- Contact Details -->
            <div class="row justify-content-center mb-4">
                <div class="col-md-6 text-center">
                    <p class="mb-2"><i class="bi bi-envelope-fill me-2 text-primary"></i><strong>Email:</strong>
                      supportdormhub@gmail.com</p>
                    <p class="mb-2"><i class="bi bi-telephone-fill me-2 text-primary"></i><strong>Phone:</strong>
                        +63 912 345 6789</p>
                    <p><i class="bi bi-clock-fill me-2 text-primary"></i><strong>Hours:</strong> Mon – Fri, 9:00 AM –
                        5:00 PM</p>
                </div>
            </div>

            <!-- Contact Form -->
           <form action="{{ route('send.email') }}" method="GET" class="row justify-content-center">
    @csrf
    <div class="col-md-8">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" name="name" class="form-control rounded-3 shadow-sm"
                    placeholder="Your Name" required />
            </div>
            <div class="col-md-6">
                <input type="email" name="email" class="form-control rounded-3 shadow-sm"
                    placeholder="Email Address" required />
            </div>
            <div class="col-12">
                <textarea name="message" class="form-control rounded-3 shadow-sm" rows="5"
                    placeholder="Your Message..." required></textarea>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn px-5 py-2 rounded-pill shadow">
                    <i class="bi bi-send-fill me-2"></i>Send Message
                </button>
            </div>
        </div>
    </div>
</form>

        </div>
    </section>


    <!-- FAQs Section -->
    <section class="faq-section py-5 bg-white section" id="faqs" data-aos="fade-up" data-aos-offset="200">
        <div class="container">
            <!-- Heading -->
            <div class="text-center mb-5">
                <h2 class="fw-bold text-primary">
                    <i class="bi bi-question-circle-fill me-2"></i>Frequently Asked Questions
                </h2>
                <p class="text-muted">Quick answers to common questions about DormHub.</p>
            </div>

            <!-- FAQ Accordion -->
            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="accordion accordion-flush" id="faqAccordionLeft">
                        <!-- FAQ Item -->
                        <div class="accordion-item border rounded shadow-sm mb-2">
                            <h2 class="accordion-header" id="heading1">
                                <button class="accordion-button collapsed bg-light" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false"
                                    aria-controls="collapse1">
                                    <i class="bi bi-question-circle me-2 text-primary"></i> What is DormHub?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse"
                                data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    DormHub is a platform designed to help students find safe, affordable, and
                                    conveniently located dorms in Mandaue and Lapu-Lapu City.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border rounded shadow-sm mb-2">
                            <h2 class="accordion-header" id="heading2">
                                <button class="accordion-button collapsed bg-light" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false"
                                    aria-controls="collapse2">
                                    <i class="bi bi-search me-2 text-primary"></i> How do I search for a dormitory?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse"
                                data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    Use the search bar on our homepage or apply filters based on location, price, and
                                    amenities.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border rounded shadow-sm mb-2">
                            <h2 class="accordion-header" id="heading3">
                                <button class="accordion-button collapsed bg-light" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false"
                                    aria-controls="collapse3">
                                    <i class="bi bi-person-lines-fill me-2 text-primary"></i> How do I contact a
                                    dormitory owner?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse"
                                data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    Each dorm listing includes contact details—just click to reach the owner directly.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border rounded shadow-sm mb-2">
                            <h2 class="accordion-header" id="heading4">
                                <button class="accordion-button collapsed bg-light" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false"
                                    aria-controls="collapse4">
                                    <i class="bi bi-cash-stack me-2 text-primary"></i> Is DormHub free to use?
                                </button>
                            </h2>
                            <div id="collapse4" class="accordion-collapse collapse"
                                data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    Yes! DormHub is completely free for both students and dorm owners.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border rounded shadow-sm mb-2">
                            <h2 class="accordion-header" id="heading5">
                                <button class="accordion-button collapsed bg-light" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false"
                                    aria-controls="collapse5">
                                    <i class="bi bi-calendar-check-fill me-2 text-primary"></i> Can I book a dormitory
                                    through DormHub?
                                </button>
                            </h2>
                            <div id="collapse5" class="accordion-collapse collapse"
                                data-bs-parent="#faqAccordionLeft">
                                <div class="accordion-body">
                                    While we don't support booking directly, we connect you with dorm owners so you can
                                    arrange a stay easily.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="accordion accordion-flush" id="faqAccordionRight">
                        <div class="accordion-item border rounded shadow-sm mb-2">
                            <h2 class="accordion-header" id="heading6">
                                <button class="accordion-button collapsed bg-light" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false"
                                    aria-controls="collapse6">
                                    <i class="bi bi-exclamation-triangle-fill me-2 text-primary"></i> How can I report
                                    an issue?
                                </button>
                            </h2>
                            <div id="collapse6" class="accordion-collapse collapse"
                                data-bs-parent="#faqAccordionRight">
                                <div class="accordion-body">
                                    Email us at support@dormhub.com or use the contact form to report listing issues.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border rounded shadow-sm mb-2">
                            <h2 class="accordion-header" id="heading7">
                                <button class="accordion-button collapsed bg-light" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false"
                                    aria-controls="collapse7">
                                    <i class="bi bi-geo-alt-fill me-2 text-primary"></i> Is DormHub limited to specific
                                    areas?
                                </button>
                            </h2>
                            <div id="collapse7" class="accordion-collapse collapse"
                                data-bs-parent="#faqAccordionRight">
                                <div class="accordion-body">
                                    Currently, we serve Mandaue and Lapu-Lapu City, with plans to expand soon.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border rounded shadow-sm mb-2">
                            <h2 class="accordion-header" id="heading8">
                                <button class="accordion-button collapsed bg-light" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false"
                                    aria-controls="collapse8">
                                    <i class="bi bi-info-circle-fill me-2 text-primary"></i> I need more help. What do
                                    I do?
                                </button>
                            </h2>
                            <div id="collapse8" class="accordion-collapse collapse"
                                data-bs-parent="#faqAccordionRight">
                                <div class="accordion-body">
                                    Visit our Help Center or email us at support@dormhub.com for further assistance.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- Footer -->
<!-- Footer -->
<footer class="footer text-white py-5" style="background-color: #4edce2;">
    <div class="container">
        <div class="row gy-4">
            <!-- Useful Links -->
            <div class="col-12 col-md-4">
                <h5 class="text-center text-md-start text-dark fw-bold">Useful Links</h5>
                <ul class="list-unstyled text-center text-md-start mb-0">
                    <li><a href="#about-us" class="text-decoration-none text-dark">About Us</a></li>
                    <li><a href="#contact-us" class="text-decoration-none text-dark">Contact Us</a></li>
                    <li><a href="#faqs" class="text-decoration-none text-dark">FAQ</a></li>
                </ul>
            </div>

            <!-- Social Media Links -->
            <div class="col-12 col-md-4 text-center">
                <h5 class="text-dark fw-bold">Follow Us</h5>
                <div class="d-flex justify-content-center gap-4 mt-3">
                    <a href="https://www.facebook.com/profile.php?id=61580194851438" class="text-dark" target="_blank"><i class="fab fa-facebook fa-lg"></i></a>
                    <a href="#" class="text-dark" target="_blank"><i class="fab fa-instagram fa-lg"></i></a>
                </div>
            </div>

            <!-- Contact & Address -->
            <div class="col-12 col-md-4 text-center text-md-start">
                <h5 class="text-dark fw-bold">Contact</h5>
                <p class="mb-1 text-dark">
                    DormHub, Lapu-Lapu & Mandaue City, Philippines
                </p>
                <p class="mb-0 text-dark">Email: <a href="mailto:supportdormhub@gmail.com"
                        class="text-dark text-decoration-none">supportdormhub@gmail.com</a></p>
            </div>
        </div>

        <!-- Divider -->
        <hr class="my-4 border-dark">

        <!-- Copyright -->
        <div class="text-center text-dark small">
            &copy; 2025 <strong>DormHub</strong>. All rights reserved.
        </div>
    </div>
</footer>

<script>
    const ourTeamElement = document.getElementById("ourteam");

    function showOurTeam() {
        if (ourTeamElement.style.display === "none" || ourTeamElement.style.display === "") {
            ourTeamElement.style.display = "block";
        } else {
            ourTeamElement.style.display = "none";
        }
    }
</script>

@include('landingpage.partials.footer')
