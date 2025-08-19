<div class="app container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar -->
        @include('landlord.auth.partials.sidebar')


        <div class="col-md-10 main-content">
            @include('landlord.auth.partials.navigation')
            <div id="paymentLandlord" data-landlord-id="{{ $landlord_id }}">
                <landlord-auth-paymentlandlord></landlord-auth-paymentlandlord>

            </div>


        </div>


        @include('landlord.auth.partials.footer')
