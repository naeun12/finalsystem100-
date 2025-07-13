@if (session('landlord_logged_in'))
    <div class="app container-fluid p-0">
        <div class="row g-0">

            <!-- Sidebar (rendered directly, outside col-md-2) -->
            @include('landlord.auth.partials.sidebar')

            <!-- Main Content (full-width beside sidebar) -->
            <div class="col main-content">
                @include('landlord.auth.partials.navigation')

                <div id="dashboard" data-landlord-id="{{ $landlord_id }}">
                    <landlord-auth-dashboard></landlord-auth-dashboard>
                </div>
            </div>

        </div>
    </div>
@endif

@include('landlord.auth.partials.footer')
