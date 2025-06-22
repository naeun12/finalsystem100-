@include('landlord.partials.navigation')
<main class="d-flex justify-content-center  align-items-center mb-5 py-5 mt-5">

    <div class="row registration-container justify-content-center mt-5 ">
        <div class=" col-md-10 border p-4 rounded w-100 ">
            <div id="LandlordRegisterContainer">
                <landlord-register></landlord-register>
            </div>
            <div class="text-center mt-5 mb-5">
                Already have an account? <a href="{{ route('landlord-Login') }}"
                    class="text-decoration-none text-primary">Please
                    sign in here.</a>

            </div>
        </div>
    </div>
</main>

@include('landlord.partials.footer')
