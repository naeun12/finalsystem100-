@if (session('landlord_logged_in'))
    <div class="app container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            @include('landlord.auth.partials.sidebar')


            <div class="col-md-10 main-content">
                @include('landlord.auth.partials.navigation')


                <div class="dashboard-content px-4 py-3">
                    <div class="py-2">
                        <h3>Hello {{ session('landlord_firstname') }}
                            {{ session('landlord_lastname') }}</h3>

                        <h4 class="text-secondary">Today's Date: <span id="todayDate"></span></h4>
                    </div>


                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Total Tenants</h5>
                                    <p class="card-text display-4">120</p>
                                </div>
                            </div>
                        </div>

                        <!-- Vacant Rooms Card -->
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Vacant Rooms</h5>
                                    <p class="card-text display-4">15</p>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Bookings Table -->
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-header">Recent Bookings</div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Room</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>John Doe</td>
                                                <td>101</td>
                                                <td>2023-10-01</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>Jane Smith</td>
                                                <td>102</td>
                                                <td>2023-10-02</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Occupancy Rate Chart -->
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm">
                                <div class="card-header">Occupancy Rate</div>
                                <div class="card-body">
                                    <canvas id="occupancyChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- New Tenants Card -->
                        <div class="col-md-12">
                            <div class="card shadow-sm">
                                <div class="card-header">New Tenants</div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">John Doe - Room 101</li>
                                        <li class="list-group-item">Jane Smith - Room 102</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const today = new Date();
        const formattedDate = today.toLocaleDateString('en-US'); // e.g. 5/24/2025
        document.getElementById('todayDate').textContent = formattedDate;
    </script>
@endif
@include('landlord.auth.partials.footer')
