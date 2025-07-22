<template>
    <!-- Top Navigation -->
    <div class="bg-white m-3 py-3 px-2 text-center shadow-sm border-custom rounded-4">
        <ul class="nav justify-content-center gap-3 flex-wrap">
            <li class="nav-item">
                <a href="#" @click="viewBooking" class="nav-link nav-feature-link d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-check fs-5"></i>
                    <span>View Bookings</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" @click="viewPayment" class="nav-link nav-feature-link d-flex align-items-center gap-2">
                    <i class="bi bi-cash-coin fs-5"></i>
                    <span>Next Payment</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" @click="viewMyrooms" class="nav-link nav-feature-link d-flex align-items-center gap-2">
                    <i class="bi bi-house-door fs-5"></i>
                    <span>My Rooms</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" @click="viewReservation" class="nav-link nav-feature-link d-flex align-items-center gap-2">
                    <i class="bi bi-journal-text fs-5"></i>
                    <span>My Reservations</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link nav-feature-link d-flex align-items-center gap-2">
                    <i class="bi bi-bell fs-5"></i>
                    <span>Notifications</span>
                </a>
            </li>

        </ul>

    </div>



    <!-- Content Section -->
    <div class="container-fluid m-2 py-5">
        <div class="row g-4">
            <!-- Mandaue Map -->
            <div class="col-md-6">
                <div class="p-4 rounded-4 shadow-sm map-card">
                    <h2 class="h5 fw-bold mb-3 text-center text-info">Dormitories in Mandaue City</h2>
                    <div id="map-mandaue" class="rounded-3" style="height: 400px;"></div>
                </div>
            </div>

            <!-- Lapu-Lapu Map -->
            <div class="col-md-6">
                <div class="p-4 rounded-4 shadow-sm map-card">
                    <h2 class="h5 fw-bold mb-3 text-center text-info">Dormitories in Lapu-Lapu City</h2>
                    <div id="map" class="rounded-3" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcement Header -->


    <!-- Custom Masonry Layout -->
    <!-- Section Header -->
    <div class="p-4 rounded shadow-sm text-center mb-4" style="border: 1px solid #4edce2; border-radius: 20px;">
        <h2 class="h5 fw-bold text-info">Top Rated Dormitories</h2>
    </div>

    <!-- Custom Masonry Grid Layout -->
    <div class="m-2 py-4">
        <div class="row g-4">
            <!-- Large Left Card -->
            <div class="col-md-6">
                <div class="card h-100 dorm-card text-white bg-dark border-0">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title">Sunshine Dormitory</h5>
                            <p class="card-text">Mandaue City</p>
                            <p class="card-text">⭐ 4.8</p>
                        </div>
                        <div class="text-end">
                            <a href="#" class="btn btn-outline-light">View Details</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
                <!-- Top Right Wide Card -->
                <div class="card mb-4 dorm-card text-white bg-dark border-0">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title">Palm Grove Dorm</h5>
                            <p class="card-text">Lapu-Lapu City</p>
                            <p class="card-text">⭐ 4.6</p>
                        </div>
                        <div class="text-end">
                            <a href="#" class="btn btn-outline-light">View Details</a>
                        </div>
                    </div>
                </div>

                <!-- Two Smaller Cards -->
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card dorm-card text-white bg-dark border-0">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title">Cityside Stay</h5>
                                    <p class="card-text">Mandaue</p>
                                    <p class="card-text">⭐ 4.5</p>
                                </div>
                                <div class="text-end">
                                    <a href="#" class="btn btn-outline-light">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card dorm-card text-white bg-dark border-0">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title">Lapu Cozy Dorm</h5>
                                    <p class="card-text">Lapu-Lapu</p>
                                    <p class="card-text">⭐ 4.7</p>
                                </div>
                                <div class="text-end">
                                    <a href="#" class="btn btn-outline-light">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- End Smaller Cards -->
            </div>
        </div>
    </div>


</template>
<script>
import axios from 'axios';
export default {
    data() {
        return {
            rooms: [],
            tenant_id: '',

        };
    },
    methods: {

        viewDorms(dormitoryId) {
            this.tenant_id = window.tenant_id;

            if (!this.tenant_id) {
                alert("tenant_id id not found");
                return;
            }

            window.location.href = `/room-details/${dormitoryId}/${this.tenant_id}`;
        },
        viewBooking() {
            this.tenant_id = window.tenant_id;
            window.location.href = `/view/booking/${this.tenant_id}`;

        },
        viewPayment() {
            this.tenant_id = window.tenant_id;
            window.location.href = `/view/payment/${this.tenant_id}`;

        },
        viewMyrooms() {
            this.tenant_id = window.tenant_id;
            window.location.href = `/view/myrooms/${this.tenant_id}`;

        },
        viewReservation() {
            this.tenant_id = window.tenant_id;
            window.location.href = `/view/reservation/${this.tenant_id}`;

        },

        initMap() {
            this.tenant_id = window.tenant_id;

            const lapuLapu = { lat: 10.3090, lng: 123.9494 };
            const mandaue = { lat: 10.3339, lng: 123.9222 };

            const customStyle = [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                }
            ];

            const mapLapu = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: lapuLapu,
                draggable: false,
                disableDoubleClickZoom: true,
                mapTypeControl: false,
                fullscreenControl: false,
                mapTypeId: 'terrain',
                styles: customStyle
            });

            const mapMandaue = new google.maps.Map(document.getElementById("map-mandaue"), {
                zoom: 14,
                center: mandaue,
                draggable: false,
                disableDoubleClickZoom: true,
                mapTypeControl: false,
                fullscreenControl: false,
                mapTypeId: 'terrain',
                styles: customStyle
            });

            const infoWindow = new google.maps.InfoWindow();

            // Fetch Lapu-Lapu Dorms
            axios.get('/tenant/dorms/lapu-lapu')
                .then(response => {
                    response.data.forEach(dorm => {
                        const marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(dorm.latitude),
                                lng: parseFloat(dorm.longitude)
                            },
                            map: mapLapu,
                            title: dorm.dorm_name, // Shown on hover
                            icon: {
                                url: '/images/tenant/allimagesResouces/dormmap.webp',
                                scaledSize: new google.maps.Size(40, 40)
                            }
                        });

                        const content = `
    <div style="
        width: 250px;
        height: 250px;
        border-radius: 12px;
        overflow: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
        background: #fefefe;
        display: flex;
        flex-direction: column;">
        
        <img src="${dorm.images.main_image}" alt="Dorm Image"
            style="width: 100%; height: 150px; object-fit: cover; border-bottom: 1px solid #ddd;">
        
        <div class="mb-3" style=" flex: 1;">
            <div style="font-size: 17px; font-weight: 600; color: #2c3e50;">
                🏠 ${dorm.dorm_name}
            </div>
            <div class="mt-2">
                <a href="/room-details/${dorm.dorm_id}/${this.tenant_id}" class="btn btn-primary w-100" style="font-size: 14px;">View Details</a>
            </div>
        </div>
    </div>
`;


                        marker.addListener("click", () => {
                            infoWindow.setContent(content);
                            infoWindow.open(mapLapu, marker);
                        });
                    });
                })
                .catch(error => {
                    console.error('Error fetching Lapu-Lapu dorms:', error);
                });

            // Fetch Mandaue Dorms
            axios.get('/tenant/dorms/mandaue')
                .then(response => {
                    response.data.forEach(dorm => {
                        const marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(dorm.latitude),
                                lng: parseFloat(dorm.longitude)
                            },
                            map: mapMandaue,
                            title: dorm.dorm_name, // Shown on hover
                            icon: {
                                url: '/images/tenant/allimagesResouces/dormmap.webp',
                                scaledSize: new google.maps.Size(40, 40)
                            }
                        });

                        const content = `
                    <div>
                        <strong>Dorm Name:</strong> ${dorm.dorm_name}<br>
                        <strong>ID:</strong> ${dorm.id}
                    </div>
                `;

                        marker.addListener("click", () => {
                            infoWindow.setContent(content);
                            infoWindow.open(mapMandaue, marker);
                        });
                    });
                })
                .catch(error => {
                    console.error('Error fetching Mandaue dorms:', error);
                });
        }


    },
    mounted() {
        // Load Google Maps script dynamically
        const script = document.createElement("script");
        script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyCyQYH_O-3v9vW6ba_V653qgVECSxII0GU&callback=initMap";
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);

        // Attach initMap function globally
        window.initMap = this.initMap;
        this.tenant_id = window.tenant_id;

    },
}
</script>
