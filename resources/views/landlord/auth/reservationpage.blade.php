<div class="app container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar -->
        @include('landlord.auth.partials.sidebar')


        <div class="col-md-10 main-content">
            @include('landlord.auth.partials.navigation')
            <div id="reservationPage" data-landlord-id="{{ $landlord_id }}" class="p-4 mt-4">
                <landlord-auth-reservation></landlord-auth-reservation>

            </div>


        </div>

        <script>
            // Get references to the sidebar and toggle button
        </script>


        @include('landlord.auth.partials.footer')
