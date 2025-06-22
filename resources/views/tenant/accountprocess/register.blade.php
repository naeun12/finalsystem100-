@include('tenant.accountprocess.partials.navigation')
<main class="d-flex justify-content-center  align-items-center mb-5 py-5 mt-5">
    <div class=" mt-5 registration-container">
        <div class="title d-flex justify-content-center mt-5">
            <h3 class="fs-bold fw-bold text-wrap text-create ">Create a Tenant Account</h3>
        </div>
        <div id="TenantRegisterContainer">
            <tenant-register></tenant-register>

        </div>
        <p class="mt-3 text-center">
            Already have an account? <a class="text-primary" href="{{ route('login-tenant') }}">Login here</a>
        </p>
    </div>
</main>
<style>

</style>
@include('tenant.accountprocess.partials.footer')
