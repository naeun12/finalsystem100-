<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <NotificationList ref="toastRef" />

    <div class=" mt-5 mb-2">
        <h1 class="text-center fs-3 fw-semibold text-primary">Explore Dorm Locations in Lapu-Lapu and Mandaue</h1>
        <p class="text-center text-muted">Find the best places to stay near your location</p>
    </div>



    <!-- Right Column: Instructions -->
    <div class="d-flex align-items-start gap-3">
        <img :src="logoImage" alt="Pin Logo" width="50" height="50" class="rounded" />
        <p class="mb-0">
            <strong>Use this pin to locate your position.</strong><br>
            Drag the pin on the left and drop it on your desired location. This helps us suggest nearby
            dormitories more accurately.
        </p>
    </div>




    <!-- Two-column layout -->
    <div class="m-2 mt-4 mb-2">
        <div class="row g-4">
            <!-- Left section - Map -->
            <div class="col-md-8">
                <div class="bg-light border rounded shadow-sm" style="height: 760px; overflow: hidden;">
                    <div id="map" style="width: 100%; height: 100%;"></div>
                </div>
            </div>

            <!-- Right section - Info -->
            <div class="col-md-4">
                <div class="bg-white border rounded-4 shadow p-4 d-flex flex-column" style="height: 760px;">
                    <!-- Section Header -->
                    <h5
                        class="text-dark fw-bold text-center mb-4 d-flex align-items-center justify-content-center gap-2">
                        <img :src="logoImage" alt="Pin Logo" width="50" height="50" class="rounded" />
                        Nearby Dormitories
                    </h5>

                    <!-- Scrollable List -->
                    <div class="overflow-auto" style="flex-grow: 1; max-height: 640px;">
                        <!-- No Results -->
                        <div v-if="nearbyDorms.length === 0" class="text-center text-muted mt-5">
                            <em>No dormitories found near this location.</em>
                        </div>

                        <!-- Dorm Cards -->
                        <div v-else class="d-flex flex-column align-items-center gap-3">
                            <div v-for="dorm in nearbyDorms" :key="dorm.id" class="w-100 d-flex justify-content-center">
                                <div class="card shadow-sm rounded-4 p-3 w-100"
                                    style="max-width: 500px; cursor: pointer;" @click="viewDormsDetails(dorm.dormID)">

                                    <!-- Card Content: stacked vertically -->
                                    <img :src="dorm.images?.mainImage || dorm.mainImage || '/images/default-dorm.webp'"
                                        alt="Dorm Image" class="rounded-3 border mb-3"
                                        style="width: 100%; height: 150px; object-fit: cover;" />

                                    <div class="text-center mb-3">
                                        <h6 class="fw-semibold text-primary mb-1">{{ dorm.dormName }}</h6>
                                        <p
                                            class="text-secondary small mb-1 d-flex align-items-center justify-content-center gap-1">
                                            <img :src="logoImage" alt="Pin Logo" width="18" height="18"
                                                class="rounded" />
                                            <span>{{ dorm.address }}</span>
                                        </p>
                                        <p class="mb-2 text-secondary small">{{ dorm.occupancyType }}</p>
                                        <div v-if="isPrice" class="mb-1">
                                            <p class="mb-0 text-dark">₱{{ dorm.price ? dorm.price.toLocaleString() : '0'
                                                }}</p>
                                        </div>
                                        <span class="badge bg-success bg-opacity-75">
                                            📍 {{ parseFloat(dorm.distance_km).toFixed(2) }} km away
                                        </span>
                                    </div>

                                    <!-- Action Button at bottom -->
                                    <div class="d-flex justify-content-center">
                                        <button class="btn btn-sm btn-primary rounded-pill w-75">View</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</div>

    </div> <!-- End Right Column -->
  

</template>
<script>
import axios from 'axios'
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';
import NotificationList from '@/components/notifications.vue';

export default {
    components: {
        Toastcomponents,
        Loader,
        NotificationList,
    },
    data() {
        return {
            logoImage: 'http://127.0.0.1:8000/images/Logo/logo.png',
            map: null,
            draggableMarker: null,
            nearbyDorms: [],
            markers: [],
            allDorms: '',
            selectedPriceRange: '',
            selectedGenderType: '',
            isPrice: false,
            notifications: [],
            receiverID: '',
            tenant_id: '',
        }
    },
    name: "DormMap",
    mounted() {

        // Load Google Maps script dynamically
        if (!window.google || !window.google.maps) {
            const script = document.createElement("script");
            script.src =
                "https://maps.googleapis.com/maps/api/js?key=AIzaSyBZgqadX1d4wnviOKzUMNStd0DG2X7GA6s&callback=initMap";
            script.async = true;
            window.initMap = () => this.initMap(); // 👈 Fix here

            document.head.appendChild(script);

        } else {
            this.initMap();

        }
        this.tenant_id = window.tenant_id;

        this.subscribeToNotifications();

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

        viewDormsDetails(dormitoryId) {
            this.tenant_id = window.tenant_id;
            window.location.href = `/room-details/${dormitoryId}/${this.tenant_id}`;
        },

        initMap() {
            const mandaue = { lat: 10.3283, lng: 123.9385 };   // Approximate center of Mandaue
            const lapuLapu = { lat: 10.3090, lng: 123.9494 };  // Lapu-Lapu center
            const centerBetween = {
                lat: (mandaue.lat + lapuLapu.lat) / 2,
                lng: (mandaue.lng + lapuLapu.lng) / 2
            };
            this.map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: centerBetween,
                draggable: true,
                zoomControl: true,
                scrollwheel: true,
                disableDoubleClickZoom: false
            });
            this.fetchNearbyDorms(centerBetween.lat, centerBetween.lng);
            this.draggableMarker = new google.maps.Marker({
                position: centerBetween,
                map: this.map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                title: "Drag me!",
                icon: {
                    url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
                    scaledSize: new google.maps.Size(50, 50)
                },
            });

            this.draggableMarker.addListener('dragstart', () => {
                this.draggableMarker.setAnimation(google.maps.Animation.BOUNCE);
            });

            this.draggableMarker.addListener('dragend', (e) => {
                const lat = e.latLng.lat();
                const lng = e.latLng.lng();
                this.fetchNearbyDorms(lat, lng);
            });
        },
        async fetchNearbyDorms(lat, lng) {
            try {
                this.$refs.loader.loading = true;
                const response = await axios.get(`nearby-dorms`, {
                    params: { lat, lng }
                });
                this.clearMarkers();

                if (response.data.status === "success") {
                    this.allDorms = response.data.data;
                    this.selectedPriceRange = '';
                    this.selectedGenderType = '';
                    this.isPrice = false;
                    this.nearbyDorms = this.allDorms.filter(dorm => parseFloat(dorm.distance_km) <= 2);

                    const bounds = this.map.getBounds(); // Get current visible map area
                    this.nearbyDorms.forEach(dorm => {
                        const lat = parseFloat(dorm.latitude);
                        const lng = parseFloat(dorm.longitude);
                        const km = parseFloat(dorm.distance_km);
                        if (km > 2) return;

                        const latLng = new google.maps.LatLng(lat, lng);
                        if (bounds && !bounds.contains(latLng)) return;

                        // Check if the dorm is within the map bounds
                        const marker = new google.maps.Marker({
                            position: { lat, lng },
                            map: this.map,
                            icon: {
                                url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png', // Default red pin
                                scaledSize: new google.maps.Size(40, 40)
                            },
                            title: `${dorm.dormName} (${dorm.distance_km} km)`
                        });


                        const infoWindow = new google.maps.InfoWindow({
                            content: `
                            <div style="max-width: 250px;">
                                <h6>${dorm.dormName}</h6>
                                <p style="margin:0;">${dorm.address}</p>
                                <small><b>Distance:</b> ${dorm.distance_km} km</small>
                            </div>`
                        });

                        marker.addListener("click", () => {
                            infoWindow.open(this.map, marker);
                        });

                        this.markers.push(marker);


                    });
                    this.$refs.loader.loading = false;
                }
            } catch (error) {
                console.error('Error fetching dorms:', error);
                this.nearbyDorms = [];
                this.allDorms = null;
                this.clearMarkers();
                this.$refs.loader.loading = false;

            }
        },
        clearMarkers() {
            if (this.markers && this.markers.length > 0) {
                this.markers.forEach(marker => {
                    if (marker && typeof marker.setMap === 'function') {
                        marker.setMap(null); // removes marker from map
                    }
                });
            }
            this.markers = []; // Clear tracking

        },

        onDragStart(event) {
            this.offsetX = event.offsetX;
            this.offsetY = event.offsetY;
        },
        onDragEnd(event) {
            const containerRect = event.target.closest('.row').getBoundingClientRect();
            this.pinX = event.pageX - containerRect.left - this.offsetX;
            this.pinY = event.pageY - containerRect.top - this.offsetY;
        },
    }
}
</script>
<style></style>