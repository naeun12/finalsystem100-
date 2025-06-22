@include('tenant.auth.partials.navigation')

<!-- Hero Section with Full-Width Carousel -->
<section class="hero-section py-0">
    <div id="carouselExampleControls" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="0" class="active"
                aria-current="true"></button>
            <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#carouselExampleControls" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner rounded-0 shadow">
            <div class="carousel-item active">
                <img src="{{ asset('images/tenant/homepage/caruasel/img2.jpg') }}" class="d-block w-100" alt="Slide 1">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                    <h1 class="display-5 text-light">Welcome to DormHub</h1>
                    <p class="lead text-light">Find your ideal dormitory with ease search, compare, and connect all in
                        one place.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/tenant/homepage/caruasel/image1.jpg') }}" class="d-block w-100"
                    alt="Slide 2">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                    <h1 class="display-5 text-light">Comfortable & Affordable</h1>
                    <p class="lead text-light">Browse verified listings tailored for students and young professionals.
                    </p>
                </div>

            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/tenant/homepage/caruasel/image3.jpg') }}" class="d-block w-100"
                    alt="Slide 3">
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                    <h1 class="display-5 text-light">Connect with Landlords</h1>
                    <p class="lead text-light">Message property owners directly and secure your next home hassle-free.
                    </p>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
<!-- HTML -->
<div id="homepage">
    <tenants-auth-homepage></tenants-auth-homepage>
</div>

@include('tenant.auth.partials.footer')
