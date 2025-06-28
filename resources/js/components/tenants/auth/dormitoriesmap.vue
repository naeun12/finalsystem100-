<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
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
            <div class="col-md-4">
                <div class="bg-white border rounded-4 shadow p-4 d-flex flex-column" style="height: 760px;">
                    <!-- Section Header -->
                    <h5
                        class="text-dark fw-bold text-center mb-4 d-flex align-items-center justify-content-center gap-2">
                        <img :src="logoImage" alt="Pin Logo" width="50" height="50" class="rounded" />
                        Nearby Dormitories
                    </h5>
                    <div class="row mb-4">
                        <!-- Price Range Dropdown -->
                        <div class="col-md-6 col-lg-4 mb-2 w-50">
                            <select class="form-select shadow-sm" v-model="selectedPriceRange"
                                @change="dropdownPriceRange">
                                <option disabled value="">Select Price Range (based on rooms)</option>
                                <option value="all">All Prices</option>
                                <option value="0-100">₱0 - ₱100</option>
                                <option value="101-200">₱101 - ₱200</option>
                                <option value="201-300">₱201 - ₱300</option>
                                <option value="301+">₱301 and above</option>
                            </select>
                        </div>

                        <!-- Occupancy Type Dropdown -->
                        <div class="col-md-6 col-lg-4 mb-2 w-50">
                            <select class="form-select shadow-sm" v-model="selectedGenderType"
                                @change="dropdownGenderType">
                                <option disabled value="">Select Occupancy Type</option>
                                <option value="all">All Types</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Mixed">Mixed</option>
                            </select>
                        </div>
                    </div>


                    <!-- Scrollable List -->
                    <!-- Scrollable List -->
                    <div class="overflow-auto" style="flex-grow: 1; max-height: 640px;">
                        <!-- No Results -->
                        <div v-if="nearbyDorms.length === 0" class="text-center text-muted mt-5">
                            <em>No dormitories found near this location.</em>
                        </div>

                        <!-- Dorm Cards -->
                        <div v-else class="row gy-3">
                            <div class="col-12" v-for="dorm in nearbyDorms" :key="dorm.id">
                                <div class="d-flex align-items-center p-3 rounded-4 shadow-sm bg-white border gap-3 hover-shadow"
                                    style="transition: all 0.3s ease; cursor: pointer;"
                                    @click="viewDormsDetails(dorm.dorm_id)">
                                    <!-- Dorm Image -->
                                    <img :src="dorm.images?.main_image || dorm.main_image || '/images/default-dorm.webp'"
                                        alt="Dorm Image" class="rounded-3 border"
                                        style="width: 90px; height: 90px; object-fit: cover;" />

                                    <!-- Dorm Info -->
                                    <div class="flex-grow-1">
                                        <h6 class="fw-semibold text-primary mb-1">
                                            {{ dorm.dorm_name }}
                                        </h6>
                                        <p class="text-secondary small mb-1 d-flex align-items-center gap-1">
                                            <img :src="logoImage" alt="Pin Logo" width="18" height="18"
                                                class="rounded" />
                                            <span>{{ dorm.address }}</span>
                                        </p>
                                        <p class="mb-2 text-secondary small d-flex align-items-center gap-2">
                                            <i class="fas fa-user-group text-muted"></i>
                                            {{ dorm.occupancy_type }}
                                        </p>


                                        <div v-if="isPrice" class="mb-1">
                                            <p class="mb-0 text-dark ">
                                                ₱{{ dorm.price ? dorm.price.toLocaleString() : '0' }}
                                            </p>
                                        </div>

                                        <span class="badge bg-success bg-opacity-75">
                                            📍 {{ parseFloat(dorm.distance_km).toFixed(2) }} km away
                                        </span>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="ms-auto">
                                        <button class="btn btn-sm rounded-pill">
                                            View
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
            logoImage: 'http://127.0.0.1:8000/images/Logo/logo.png',
            map: null,
            draggableMarker: null,
            nearbyDorms: [],
            markers: [],
            allDorms: '',
            selectedPriceRange: '',
            selectedGenderType: '',
            isPrice: false,



        }
    },
    name: "DormMap",
    mounted() {

        // Load Google Maps script dynamically
        if (!window.google || !window.google.maps) {
            const script = document.createElement("script");
            script.src =
                "https://maps.googleapis.com/maps/api/js?key=AIzaSyCyQYH_O-3v9vW6ba_V653qgVECSxII0GU&callback=initMap";
            script.async = true;
            window.initMap = () => this.initMap(); // 👈 Fix here

            document.head.appendChild(script);

        } else {
            this.initMap();

        }
    },
    methods: {
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
            this.addRadarOverlay(centerBetween.lat, centerBetween.lng);
            this.fetchNearbyDorms(centerBetween.lat, centerBetween.lng);
            this.draggableMarker = new google.maps.Marker({
                position: centerBetween,
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
                        if (!bounds.contains(latLng)) return;

                        // Check if the dorm is within the map bounds
                        const marker = new google.maps.Marker({
                            position: { lat, lng },
                            map: this.map,
                            icon: {
                                url: '/images/tenant/allimagesResouces/dormmap.webp',
                                scaledSize: new google.maps.Size(40, 40)
                            },
                            title: `${dorm.dorm_name} (${dorm.distance_km} km)`
                        });

                        const infoWindow = new google.maps.InfoWindow({
                            content: `
                            <div style="max-width: 250px;">
                                <h6>${dorm.dorm_name}</h6>
                                <p style="margin:0;">${dorm.address}</p>
                                <small><b>Distance:</b> ${dorm.distance_km} km</small>
                            </div>`
                        });

                        marker.addListener("click", () => {
                            infoWindow.open(this.map, marker);
                        });

                        this.markers.push(marker);


                    });
                    this.addRadarOverlay(lat, lng);
                    this.$refs.loader.loading = false;
                    console.log("Nearby Dorms Count:", this.nearbyDorms.length);
                    console.log("Clearing markers:", this.markers.length);

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

        async dropdownPriceRange() {
            try {
                const lat = this.draggableMarker.getPosition().lat();
                const lng = this.draggableMarker.getPosition().lng();

                const response = await axios.post('/price-range',
                    {
                        price_range: this.selectedPriceRange,
                        lat: lat,
                        lng: lng

                    });
                if (this.combinePriceAndGender && this.selectedGenderType !== 'all') {
                    await this.combinePriceAndGender();
                    this.isPrice = true;

                    return;
                }
                if (response.status === 200 && response.data.status === 'success') {
                    this.nearbyDorms = response.data.data;
                    this.isPrice = true;

                }
            } catch (error) {
                this.nearbyDorms = [];

            }
        },
        async dropdownGenderType() {
            try {
                const lat = this.draggableMarker.getPosition().lat();
                const lng = this.draggableMarker.getPosition().lng();
                const response = await axios.post('/selected-gender-type', {
                    gender_type: this.selectedGenderType,
                    lat: lat,
                    lng: lng
                });
                if (this.combinePriceAndGender && this.selectedPriceRange !== 'all') {
                    await this.combinePriceAndGender();

                    return;
                }

                if (response.data.status === 'success') {
                    this.nearbyDorms = response.data.data;
                    this.isPrice = false;
                }
            } catch (error) {
                console.error('Gender filter error:', error);
                this.nearbyDorms = [];
            }
        },
        async combinePriceAndGender() {
            try {
                const lat = this.draggableMarker.getPosition().lat();
                const lng = this.draggableMarker.getPosition().lng();
                const response = await axios.post('/filter-price-gender', {
                    price_range: this.selectedPriceRange,
                    gender_type: this.selectedGenderType,
                    lat: lat,
                    lng: lng
                });
                if (response.status === 200 && response.data.status === "success") {
                    this.nearbyDorms = response.data.data;
                    this.isPrice = true;

                }

            }
            catch (error) {
                console.error('Combined filter error:', error);
                this.nearbyDorms = [];
            }
        }




    }
}
</script>
<style></style>