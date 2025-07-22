@include('tenant.accountprocess.partials.navigation')


<main class="d-flex justify-content-center  align-items-center mb-5 py-5 mt-5">

    <div class="card border-primary shadow-lg mt-5 py-3" style="width: 700px; height:auto;">
        <div class="card-body ">
            <h1 class="text-center mb-2 mt-4 fw-bold text-primary display-5 animate__animated animate__fadeInDown ">
                👋 Hello, Tenant!
            </h1>
            <div class="bg-white  rounded-3 p-3 ">
                <p class="text-center text-muted fs-5 mb-0">
                    🏠 Welcome back! Please log in to explore available boarding houses and manage your bookings and
                    reservations.
                </p>
            </div>


        </div>
        <div id="tenant">
            <tenants-login></tenants-login>
        </div>

        <p class="text-center mt-5">
            Hello Tenant! Don't have an account?
            <a href="{{ route('register-tenant') }}" class="text-primary">Sign up here.</a>
        </p>


    </div>


</main>
@include('tenant.accountprocess.partials.footer')
