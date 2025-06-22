<div class="app container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar -->
        @include('landlord.auth.partials.sidebar')


        <div class="col-md-10 main-content">
            @include('landlord.auth.partials.navigation')
            <div id="landlorddormManagement">
                <landlord-auth-dormManagement></landlord-auth-dormManagement>

            </div>


        </div>

        <script>
            window.landlordId = @json(session('landlord_id'));
        </script>


        @include('landlord.auth.partials.footer')
