<div class="app container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar -->
        @include('landlord.auth.partials.sidebar')


        <div class="col-md-10 main-content">
            @include('landlord.auth.partials.navigation')
            <div id="tenantpage" data-landlord-id="{{ $landlord_id }}">
                <landlord-auth-tenantpage></landlord-auth-tenantpage>

            </div>


        </div>

        <script>
            // Get references to the sidebar and toggle button
        </script>


        @include('landlord.auth.partials.footer')
