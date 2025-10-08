<template>
    <NotificationList ref="toastRef" />

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
                <a href="#" @click="viewnotifications"
                    class="nav-link nav-feature-link d-flex align-items-center gap-2">
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
    <div class="p-4 rounded shadow-sm text-center mb-4 bg-light border-info">
        <h2 class="h4 fw-bold text-info">Top Rated Dormitories</h2>
        <p class="text-muted">Check out the best dorms in your area</p>
    </div>

    <div class="m-2 py-4">
        <div class="row g-4">
            <!-- Large Left Card -->
            <div class="col-md-6" v-if="topDorms.length > 0">
                <div class="card h-100 dorm-card text-white border-0 overflow-hidden shadow-lg"
                    style="border-radius: 20px; cursor: pointer; position: relative; height: 400px;">
                    <!-- Full background image -->
                    <div :style="{
                        backgroundImage: `url(${topDorms[0].dorm.images?.mainImage || '/default-image.jpg'})`,
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                        height: '100%',
                        width: '100%',
                        borderRadius: '20px',
                        filter: 'brightness(0.7)'
                    }"></div>

                    <!-- Text overlay -->
                    <div style="position: absolute; bottom: 20px; left: 20px; z-index: 10;">
                        <h4 class="fw-bold">{{ topDorms[0].dorm.dormName }}</h4>
                        <p class="mb-1">{{ topDorms[0].dorm.address }}</p>
                        <p class="mb-1">⭐ {{ Number(topDorms[0].avg_rating).toFixed(1) }}</p>
                        <a @click="viewDorms(topDorms[0].dorm.dormID)" class="btn btn-outline-light btn-sm">View
                            Details</a>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
                <!-- Second Card -->
                <div class="card mb-4 dorm-card text-white border-0 overflow-hidden shadow" v-if="topDorms.length > 1"
                    style="border-radius: 20px; height: 190px; position: relative;">
                    <div :style="{
                        backgroundImage: `url(${topDorms[1].dorm.images?.mainImage || '/default-image.jpg'})`,
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                        height: '100%',
                        width: '100%',
                        borderRadius: '20px',
                        filter: 'brightness(0.7)'
                    }"></div>
                    <div style="position: absolute; bottom: 10px; left: 15px; z-index: 10;">
                        <h5 class="fw-bold mb-1">{{ topDorms[1].dorm.dormName }}</h5>
                        <p class="mb-1">{{ topDorms[1].dorm.address }}</p>
                        <p class="mb-0">⭐ {{ Number(topDorms[1].avg_rating).toFixed(1) }}</p>
                        <!-- FIXED: now correctly points to second dorm -->
                        <a @click="viewDorms(topDorms[1].dorm.dormID)"
                            class="btn btn-outline-light btn-sm mt-1">View</a>
                    </div>
                </div>

                <!-- Two Smaller Cards -->
                <div class="row g-3">
                    <div class="col-md-6" v-for="(dorm, index) in topDorms.slice(2, 4)" :key="dorm.fkdormID">
                        <div class="card dorm-card text-white border-0 overflow-hidden shadow-sm"
                            style="border-radius: 15px; height: 140px; position: relative;">
                            <div :style="{
                                backgroundImage: `url(${dorm.dorm.images?.mainImage || '/default-image.jpg'})`,
                                backgroundSize: 'cover',
                                backgroundPosition: 'center',
                                height: '100%',
                                width: '100%',
                                borderRadius: '15px',
                                filter: 'brightness(0.7)'
                            }"></div>
                            <div style="position: absolute; bottom: 10px; left: 10px; z-index: 10;">
                                <h6 class="fw-bold mb-0">{{ dorm.dorm.dormName }}</h6>
                                <p class="mb-0 text-truncate">{{ dorm.dorm.address }}</p>
                                <p class="mb-0">⭐ {{ Number(dorm.avg_rating).toFixed(1) }}</p>
                                <!-- FIXED: correctly uses the loop dorm -->
                                <a @click="viewDorms(dorm.dorm.dormID)"
                                    class="btn btn-outline-light btn-sm mt-1">View</a>
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
import Loader from '@/components/loader.vue';
import NotificationList from '@/components/notifications.vue';

export default {
    components: {
        Loader,
        NotificationList,
    },
    data() {
        return {
            rooms: [],
            tenant_id: '',
            notifications: [],
            receiverID: '',
            topDorms: []


        };
    },
    methods: {
        subscribeToNotifications() {
            if (this.hasSubscribed) return;
            this.hasSubscribed = true;

            this.receiverID = this.tenant_id;
            Echo.private(`notifications.${this.tenant_id}`)
                .subscribed(() => {
                    console.log('✔ Subscribed!');
                })
                .listen('.NewNotificationEvent', (e) => {
                    this.notifications.unshift(e); // save for list
                    this.$refs.toastRef.pushNotification({
                        title: e.title || 'New Notification',
                        message: e.message,
                        color: 'success',
                    });
                });
        },

        viewDorms(dormID) {
            this.tenant_id = window.tenant_id;            
            window.location.href = `/room-details/${dormID}/${this.tenant_id}`;
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
        viewnotifications() {
            this.tenant_id = window.tenant_id;
            window.location.href = `/view/notifications/${this.tenant_id}`;
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
        
        <img src="${dorm.images.mainImage}" alt="Dorm Image"
            style="width: 100%; height: 150px; object-fit: cover; border-bottom: 1px solid #ddd;">
        
        <div class="mb-3" style=" flex: 1;">
            <div style="font-size: 17px; font-weight: 600; color: #2c3e50;">
                🏠 ${dorm.dormName}
            </div>
            <div class="mt-2">
                <a href="/room-details/${dorm.dormID}/${this.tenant_id}" class="btn btn-primary w-100" style="font-size: 14px;">View Details</a>
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

        <img src="${dorm.images.mainImage}" alt="Dorm Image"
            style="width: 100%; height: 150px; object-fit: cover; border-bottom: 1px solid #ddd;">
        
        <div class="mb-3" style=" flex: 1;">
            <div style="font-size: 17px; font-weight: 600; color: #2c3e50;">
                🏠 ${dorm.dormName}
            </div>
            <div class="mt-2">
                <a href="/room-details/${dorm.dormID}/${this.tenant_id}" class="btn btn-primary w-100" style="font-size: 14px;">View Details</a>
            </div>
        </div>
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
        },
        async fetchTopRatedDorms() {
            try {
                const response = await axios.get('/api/top-rated-dorms');
                // Convert avg_rating to number
                this.topDorms = response.data.map(dorm => ({
                    ...dorm,
                    avg_rating: Number(dorm.avg_rating)
                }));
            } catch (error) {
                console.error(error);
            }
        }



    },
    mounted() {
        // Load Google Maps script dynamically
        const script = document.createElement("script");
        script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBZgqadX1d4wnviOKzUMNStd0DG2X7GA6s&callback=initMap";
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);

        // Attach initMap function globally
        window.initMap = this.initMap;
        this.tenant_id = window.tenant_id;
        this.subscribeToNotifications();
        this.fetchTopRatedDorms();

    },
}
</script>
