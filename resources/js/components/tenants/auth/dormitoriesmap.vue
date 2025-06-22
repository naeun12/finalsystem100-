<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <div class=" mt-5 mb-2">
        <h1 class="text-center fs-3 fw-semibold text-primary">Explore Dorm Locations in Lapu-Lapu and Mandaue</h1>
        <p class="text-center text-muted">Find the best places to stay near your location</p>
    </div>

    <!-- Two-column layout -->
    <div class="m-2 mt-4 mb-2">
        <div class="row g-4">
            <!-- Left section - Map -->
            <div class="col-md-8">
                <div class="bg-light border rounded shadow-sm" style="height: 760px; overflow: hidden;">
                    <div id="map" style="width: 100%; height: 100%;"></div>
                    <div id="radarPulse" style="
                        position: absolute;
                        width: 160px; 
                        height: 160px; 
                        border: 5px solid #007bff;
                        border-radius: 50%;
                        animation: radarPulse 2s infinite;
                        pointer-events: none;
                        transform: translate(-50%, -50%);
                        z-index: 999;
                    "></div>


                </div>
            </div>

            <!-- Right section - Info -->
            <div class=" col-md-4">
                <div class="bg-white border rounded shadow-sm p-4 d-flex flex-column" style="height: 760px;">
                    <!-- Search Input -->
                    <div class="mb-3">
                        <input type="text" class="form-control form-control-lg rounded-pill shadow-sm"
                            id="searchLocation" placeholder="🔍 Search gender..." aria-label="Search Location">
                    </div>

                    <!-- Recommended Dorms -->
                    <h5 class="text-dark fw-bold text-center mb-3">🏠 Recommended Dormitories</h5>

                    <div class="overflow-auto" style="flex-grow: 1; max-height: 640px;">
                        <div v-if="nearbyDorms.length === 0" class="text-center text-muted mt-5">
                            <em>No dormitories found near this location.</em>
                        </div>

                        <div v-else class="row">
                            <div class="col-12 mb-3" v-for="dorm in nearbyDorms" :key="dorm.id">
                                <div class="border rounded shadow-sm p-3 bg-white d-flex gap-3 align-items-center dorm-card hover-shadow transition"
                                    style="transition: all 0.3s ease-in-out; cursor: pointer;">
                                    <!-- Dorm Image -->
                                    <img :src="dorm.images.main_image" alt="Dorm Image" class="rounded border"
                                        style="width: 100px; height: 100px; object-fit: cover; flex-shrink: 0;" />

                                    <!-- Dorm Info -->
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-primary mb-1 mb-md-2">{{ dorm.dorm_name }}</h6>
                                        <p class="text-secondary small mb-2">
                                            <i class="bi bi-geo-alt-fill me-1"></i> {{ dorm.address }}
                                        </p>
                                        <span class="badge bg-success">📍 {{ dorm.distance_km }} km away</span>
                                    </div>

                                    <!-- Button -->
                                    <div class="ms-auto">
                                        <button class="btn 
                                         btn-sm rounded-pill" @click="viewDormsDetails(dorm.dorm_id)">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
<script>
import axios from 'axios'
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';
export default {
    components: {
        Toastcomponents,
        Loader,


    },
    data() {
        return {
            map: null,
            draggableMarker: null,
            nearbyDorms: [],
            markers: [],
            allDorms: '',


        }
    },
    name: "DormMap",
    mounted() {
        this.$refs.loader.loading = true;

        // Load Google Maps script dynamically
        if (!window.google || !window.google.maps) {
            const script = document.createElement("script");
            script.src =
                "https://maps.googleapis.com/maps/api/js?key=AIzaSyCyQYH_O-3v9vW6ba_V653qgVECSxII0GU&callback=initMap";
            script.async = true;
            window.initMap = () => this.initMap(); // 👈 Fix here

            document.head.appendChild(script);
            this.$refs.loader.loading = false;

        } else {
            this.initMap();
            this.$refs.loader.loading = false;

        }
    },
    methods: {
        viewDormsDetails(dormitoryId) {
            this.tenant_id = window.tenant_id;

            if (!this.tenant_id) {
                alert("tenant_id id not found");
                return;
            }

            window.location.href = `/room-details/${dormitoryId}/${this.tenant_id}`;
        },

        initMap() {
            const lapuLapu = { lat: 10.3090, lng: 123.9494 };

            this.map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: lapuLapu,
                draggable: true,
                zoomControl: true,
                scrollwheel: true,
                disableDoubleClickZoom: false
            });
            this.addRadarOverlay(lapuLapu.lat, lapuLapu.lng);
            this.fetchNearbyDorms(lapuLapu.lat, lapuLapu.lng);
            this.draggableMarker = new google.maps.Marker({
                position: lapuLapu,
                map: this.map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                title: "Drag me!",
                icon: {
                    url: "http://127.0.0.1:8000/images/Logo/logo.png",
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
                this.addRadarOverlay(lat, lng); // Move radar to new position
            });

            // Click to fetch dorms
            this.map.addListener('click', (e) => {
                const lat = e.latLng.lat();
                const lng = e.latLng.lng();
                this.fetchNearbyDorms(lat, lng);
                this.addRadarOverlay(lat, lng); // ➕ Move radar on map click

            });
        },


        async fetchNearbyDorms(lat, lng) {
            try {
                this.$refs.loader.loading = true;
                const response = await axios.get(`nearby-dorms`, {
                    params: { lat, lng }
                });
                if (response.data.status === "success") {
                    this.allDorms = response.data.data;

                    // Filter dormitories within 2 km
                    this.nearbyDorms = this.allDorms.filter(dorm => parseFloat(dorm.distance_km) <= 2);

                    // Clear all existing markers first
                    this.clearMarkers();

                    if (!this.nearbyDorms.length) {
                        this.allDorms = null;
                        this.nearbyDorms = [];
                        this.$refs.loader.loading = false;
                        return;
                    }

                    // Add markers only for dorms within 2 km
                    const bounds = this.map.getBounds(); // Get current visible map area
                    this.clearMarkers();
                    this.nearbyDorms.forEach(dorm => {
                        const lat = parseFloat(dorm.latitude);
                        const lng = parseFloat(dorm.longitude);
                        const latLng = new google.maps.LatLng(lat, lng);

                        // Check if the dorm is within the map bounds
                        if (bounds.contains(latLng)) {
                            const marker = new google.maps.Marker({
                                position: { lat, lng },
                                map: this.map,
                                icon: {
                                    url: '/images/tenant/allimagesResouces/dormmap.webp',
                                    scaledSize: new google.maps.Size(40, 40)
                                },
                                title: `${dorm.dorm_name} (${dorm.distance_km} km)`
                            });

                            // Add InfoWindow for the marker
                            const infoWindow = new google.maps.InfoWindow({
                                content: `
                            <div style="max-width: 250px;">
                                <h6>${dorm.dorm_name}</h6>
                                <p style="margin:0;">${dorm.address}</p>
                                <small><b>Distance:</b> ${dorm.distance_km} km</small>
                            </div>
                        `
                            });

                            marker.addListener("click", () => {
                                infoWindow.open(this.map, marker);
                            });

                            this.markers.push(marker); // Store marker to clear later
                        }
                    });

                    // Update radar overlay to the new position
                    this.addRadarOverlay(lat, lng);
                    this.$refs.loader.loading = false;
                }
            } catch (error) {
                console.error('Error fetching dorms:', error);
                this.nearbyDorms = [];
                this.allDorms = null;
            }
        },
        clearMarkers() {
            if (this.markers.length) {
                this.markers.forEach(marker => marker.setMap(null));
                this.markers = [];
            }
        },


        addRadarOverlay(lat, lng) {
            const radar = document.getElementById("radarPulse");
            // Remove radar from old parent
            if (radar.parentNode) {
                radar.parentNode.removeChild(radar);
            }
            // Remove old overlay
            if (this.radarOverlay && this.radarOverlay.setMap) {
                this.radarOverlay.setMap(null);
            }
            const overlayView = new google.maps.OverlayView();
            overlayView.onAdd = function () {
                const panes = this.getPanes();
                panes.overlayMouseTarget.appendChild(radar); // use mouseTarget for better positioning
            };
            overlayView.draw = function () {
                const projection = this.getProjection();
                const position = projection.fromLatLngToDivPixel(new google.maps.LatLng(lat, lng));
                // Set radar center correctly
                radar.style.left = `${position.x}px`;
                radar.style.top = `${position.y}px`;
            };
            overlayView.onRemove = function () {
                if (radar.parentNode) {
                    radar.parentNode.removeChild(radar);
                }
            };
            overlayView.setMap(this.map);
            this.radarOverlay = overlayView;
        }


    }
}
</script>
<style></style>