<template>
    <Loader ref="loader" />
    <NotificationList ref="toastRef" />

    <div class="container-fluid py-4 bg-light min-vh-100 d-flex flex-column flex-lg-row gap-4">
        <!-- AI Question Sidebar -->
        <aside class="ai-aside bg-white p-4 shadow-lg rounded-4 mx-auto d-flex flex-column"
            style="max-width: 500px; max-height: 150vh; overflow-y: auto;">
            <div class="container text-center flex-shrink-0">
                <h4 class="mb-4 fw-bold text-primary">💬 Looking for Rooms or Dorms? Ask AI!</h4>

                <!-- Input -->
                <div class="input-group input-group-lg mb-4 shadow-sm rounded-pill overflow-hidden border">
                    <input v-model="question" type="text" class="form-control border-0"
                        placeholder="Type your question here..." @keyup.enter="aiQuestion" />
                    <button @click="aiQuestion" class="btn btn-primary px-4 rounded-pill">
                        Ask
                    </button>
                </div>
            </div>

            <!-- Dorm Recommendations -->
            <div class="flex-grow-1" style="overflow-y: auto; padding-right: 5px;">
                <div class="card border-0 shadow-sm mb-4 ai-response">
                    <div class="card-body p-4">
                        💬 AI Response
                        <p class="mb-0 text-muted" style="white-space: pre-wrap; line-height: 1.5;">
                            {{ chatresponse || 'No response yet. Type your question above and hit Ask!' }}
                        </p>
                    </div>
                </div>
                <h5 class="card-title text-start mb-3 fw-semibold">AI Suggestions: Rooms & Dorms</h5>

                <div v-if="dormReccomend.length > 0" class="row g-3 justify-content-center">
                    <div v-for="(dorm, idx) in dormReccomend" :key="idx" class="col-12">
                        <div class="card dorm-card mb-4 shadow-sm rounded-4 overflow-hidden">
                            <div class="card-body p-3">
                                <h5 class="card-title fw-bold mb-2">{{ dorm.dormName }}</h5>
                                <p class="mb-1 text-muted"><i class="fas fa-user text-primary me-2"></i>{{
                                    dorm.occupancyType }}</p>
                                <p class="mb-2 text-muted"><i class="fas fa-map-marker-alt text-primary me-2"></i>{{
                                    dorm.address }}</p>

                                <!-- Rooms -->
                                <div v-if="dorm.rooms && dorm.rooms.length" class="rooms-scroll mb-2 border-top pt-2">
                                    <div v-for="(room, rIdx) in dorm.rooms" :key="rIdx" class="mb-2 pb-2 border-bottom">
                                        <p class="text-muted mb-1">
                                            Room #: {{ room.roomNumber }} | Type: {{ room.type }}
                                        </p>
                                        <p class="text-muted mb-1">
                                            Price: ₱{{ room.price }} | Availability: {{ room.availability }}
                                        </p>
                                        <div v-if="room.features && room.features.length" class="mb-2">
                                            <strong class="d-block mb-1">Features:</strong>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span v-for="(feature, index) in room.features" :key="index"
                                                    class="badge bg-info text-dark rounded-pill">
                                                    {{ feature }}
                                                </span>
                                            </div>
                                        </div>



                                    </div>
                                </div>

                                <a class="btn btn-primary w-100 rounded-pill mt-3"
                                    @click="viewDormsDetails(dorm?.dormID)">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <p v-else class="text-muted mb-0">No dorms or rooms available.</p>
            </div>
        </aside>


        <!-- Main Content -->
        <div class="flex-grow-1" style="overflow-x: hidden;">
            <div class="container-fluid mt-3">
                <h2 class="mb-4 text-primary fw-semibold text-center">
                    Find Your Ideal Dorm in Mandaue and Lapu-Lapu City
                </h2>
            </div>

            <!-- Most Watched Dorms Horizontal Scroll -->
            <section class="text-primary mb-4">
                <h5 class="mb-3 fw-bold">Most Watched Dormitories</h5>
                <div style="overflow-x: auto; white-space: nowrap; padding-bottom: 1rem;"
                    class="shadow-sm rounded-4 bg-light p-3">

                    <div class="d-flex flex-row gap-3" style="width: max-content;">
                        <div class="card dorm-card shadow-sm border-0 rounded-4 overflow-hidden"
                            v-for="(dorm, index) in mostwatchdorm" :key="index"
                            style="width: 20rem; flex-shrink: 0; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                            <div class="position-relative">
                                <img :src="dorm?.images?.mainImage || dorm?.mainImage || 'https://via.placeholder.com/320x200'"
                                    class="card-img-top" :alt="dorm.dormName"
                                    style="height: 200px; object-fit: cover;" />
                                <span
                                    class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark rounded-pill shadow-sm px-3 py-2">
                                    <i class="bi bi-eye-fill me-1"></i>{{ dorm.views || 0 }}
                                </span>
                            </div>
                            <div class="card-body d-flex flex-column" style="height: 150px;">
                                <h5 class="card-title text-primary fw-bold text-truncate">{{ dorm.dormName }}</h5>
                                <p class="card-text text-muted small mb-2 text-truncate">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    {{ dorm.address || 'No description available.' }}
                                </p>
                                <div class="mt-auto">
                                    <button class="btn rounded-pill w-100" @click="viewDormsDetails(dorm.dormID)">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Search Bar -->
            <div class="input-group mb-4 w-100 shadow-sm rounded-pill overflow-hidden border">
                <span class="input-group-text bg-white border-0">
                    <i class="bi bi-search text-primary"></i>
                </span>
                <input type="text" class="form-control border-0 shadow-none" placeholder="Search Locations"
                    aria-label="Search Locations" v-model="searchQuery" @input="debouncedSearch" />
            </div>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-6 col-lg-4 mb-2">
                    <select class="form-select shadow-sm" v-model="selectedPriceRange"
                        @change="dropdownPriceRecommendations">
                        <option disabled value="">Select Price Range (based on rooms)</option>
                        <option value="all">All Prices</option>
                        <option value="0-100">₱0 - ₱100</option>
                        <option value="101-200">₱101 - ₱200</option>
                        <option value="201-300">₱201 - ₱300</option>
                        <option value="301+">₱301 and above</option>
                    </select>
                </div>

                <div class="col-md-6 col-lg-4 mb-2">
                    <select class="form-select shadow-sm" v-model="selectedOccupancyType"
                        @change="dropdownGenderRecommdations">
                        <option disabled value="">Select Occupancy Type</option>
                        <option value="all">All Types</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Mixed">Mixed</option>
                    </select>
                </div>
            </div>

            <!-- Dorm Listings Grid -->
            <div class="row g-4">
                <div class="col-12 col-sm-6 col-md-4 col-lg-3" v-for="(dorm, dormID) in dormitories" :key="dormID">
                    <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden d-flex flex-column">
                        <div class="image-wrapper">
                            <img :src="dorm?.images?.mainImage || dorm?.mainImage" class="card-img-top"
                                :alt="dorm.dormName" style="object-fit: cover; height: 180px;" />
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between flex-grow-1">
                            <div>
                                <h5 class="card-title text-dark fw-bold">{{ dorm.dormName }}</h5>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-person-fill"></i> {{ dorm.occupancyType }}
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-geo-alt-fill"></i> {{ dorm.address }}
                                </p>
                            </div>
                            <div class="mt-4 d-flex justify-content-center">
                                <button class="btn rounded-pill px-4 w-100" @click="viewDormsDetails(dorm.dormID)">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import _ from 'lodash';
import debounce from 'lodash/debounce';
import Loader from '@/components/loader.vue';
import NotificationList from '@/components/notifications.vue';

export default {
    components: {
        Loader,
        NotificationList,

    },
    name: 'ProductGrid',
    data() {
        return {
            dormitories: [],
            filteredDorms: [],
            searchQuery: '',
            selectedPriceRange: '',
            selectedOccupancyType: '',
            recommendations: [],
            recommendloading: false,
            isGenderBased: false,
            recommend: '',
            dorms: [],
            dormReccomend: [],
            question: '',
            chatresponse: '',
            mostwatchdorm: [],
            tenant_id: '',
            notifications: [],
            receiverID: '',


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
        async dormListingfetch() {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get('/list-dorms');
                this.dormitories = response.data.dorms;
                this.$refs.loader.loading = false;


            } catch (error) {
                console.error("Error fetching dorms:", error);
                this.$refs.loader.loading = false;

            }
        },
        viewDormsDetails(dormitoryId) {
            this.tenant_id = window.tenant_id;
            window.location.href = `/room-details/${dormitoryId}/${this.tenant_id}`;
        },
        async searchLocations() {
            try {
                this.$refs.loader.loading = true;

                if (!this.searchQuery.trim()) {

                    await this.dormListingfetch();  // re-fetch all dorms
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('/search-locations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        location: this.searchQuery,
                    }),
                });

                const result = await response.json();
                if (result.status === "success") {
                    this.dormitories = result.recommendations;
                    this.$refs.loader.loading = false;


                } else {
                    console.error('Server responded with error:', result.message);
                    this.dormitories = [];
                    this.$refs.loader.loading = false;


                }
            } catch (err) {
                console.error('Search failed:', err);
                this.filteredDorms = [];
                this.$refs.loader.loading = false;

            }
        },
        async dropdownPriceRecommendations() {
            this.recommendloading = true;
            const [min, max] = this.getPriceRange(this.selectedPriceRange);
            try {
                const response = await fetch('/pricerecommendations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        min_price: Number(min), // ensure numeric
                        max_price: Number(max)
                    })
                });

                const result = await response.json();
                if (result.status === 'success') {
                    this.recommendations = result.data;
                    this.dormitories = this.recommendations;
                } else {
                    this.recommendloading = false;

                }

            } catch (error) {
                this.recommendloading = false;
                this.$refs.loader.loading = false;


            }
        },
        getPriceRange(range) {
            switch (range) {
                case '0-100': return [0, 100];
                case '101-200': return [101, 200];
                case '201-300': return [201, 300];
                case '301+': return [301, 999999];
                default: return [0, 999999]; // fallback
            }
        },
        async dropdownGenderRecommdations() {
            try {

                const response = await fetch('gender-recommendations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        occupancy_type: this.selectedOccupancyType.toLowerCase()
                    }),
                });
                const result = await response.json();

                if (result.status === 'success') {
                    this.dormitories = result.recommendations;
                    this.genderSelectedPriceRange = '';

                }
            }
            catch (error) {
                this.$refs.loader.loading = false;
            }
        }, async aiQuestion() {
            try {
                this.$refs.loader.loading = true;

                // Send the user question to Laravel AI route
                const response = await axios.post('ai/question/reccomendations', { question: this.question });

                // AI text response
                this.chatresponse = response.data.message || 'Walay tubag gikan sa AI';

                // AI recommendations (dorms or rooms)
                const recs = response.data.recommendations || [];

                this.dormReccomend = (recs || []).map(d => ({
                    dormID: d.dormID || 0,
                    dormName: d.dormName || 'Unnamed Dorm',
                    address: d.address || 'No address provided',
                    occupancyType: d.occupancyType || 'Mixed',
                    amenities: d.amenities || '',
                    rules: d.rules || [],
                    rooms: (d.rooms || []).map(r => ({
                        roomNumber: r.roomNumber || 'N/A',
                        type: r.type || r.roomType || 'Standard',
                        price: r.price || 'Contact landlord',
                        availability: r.availability || 'Unknown',
                        features: r.features
                            ? (Array.isArray(r.features)
                                ? r.features
                                : r.features.split(',').map(f => f.trim()))
                            : []
                    }))
                }));

                console.log('AI Recommendations:', this.dormReccomend);
        console.log('AI Response:', this.chatresponse);

            } catch (error) {
                console.error('Error sending AI question:', error);
                this.chatresponse = 'Naa’y error sa pagkuha og recommendations.';
                this.dormReccomend = [];
            } finally {
                this.$refs.loader.loading = false;
            }
        },




        
        async mostWatchDorm() {
            try {
                this.tenant_id = window.tenant_id;  // siguro naa ni sa global js
                const response = await axios.get(`/most/watched/dorm/${this.tenant_id}`);
                this.mostwatchdorm = response.data.dorm;
                // I-update imong UI with this.mostwatchdorm
                console.log('Most watched dorm:', this.mostwatchdorm);
            } catch (error) {
                console.error('Error fetching most watched dorm:', error);
            }
        }


    },
    created() {
        this.debouncedSearch = debounce(this.searchLocations, 500);
    },
    mounted() {
        this.dormListingfetch();
        this.mostWatchDorm();
        this.tenant_id = window.tenant_id;
        this.subscribeToNotifications();
    },
};


</script>

<style scoped>
body {
    width: 100%;
}

.card:hover {
    transform: scale(1.02);
    transition: transform 0.3s ease;
}

.image-wrapper {
    height: 200px;
    /* You can adjust this height */
    overflow: hidden;
}

.card-img-top {
    height: 100%;
    width: 100%;
    object-fit: cover;
}


.sidebar {
    width: 1000px;
    /* fixed sidebar width */
    max-height: 90vh;
    /* max height to scroll */
    overflow-y: auto;
    position: sticky;
    top: 1rem;
}

/* From Uiverse.io by adamgiebl */
.dots-container {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
}

.dot {
    height: 20px;
    width: 20px;
    margin-right: 10px;
    border-radius: 10px;
    background-color: #b3d4fc;
    animation: pulse 1.5s infinite ease-in-out;
}

.dot:last-child {
    margin-right: 0;
}

.dot:nth-child(1) {
    animation-delay: -0.3s;
}

.dot:nth-child(2) {
    animation-delay: -0.1s;
}

.dot:nth-child(3) {
    animation-delay: 0.1s;
}

@keyframes pulse {
    0% {
        transform: scale(0.8);
        background-color: #b3d4fc;
        box-shadow: 0 0 0 0 rgba(178, 212, 252, 0.7);
    }

    50% {
        transform: scale(1.2);
        background-color: #6793fb;
        box-shadow: 0 0 0 10px rgba(178, 212, 252, 0);
    }

    100% {
        transform: scale(0.8);
        background-color: #b3d4fc;
        box-shadow: 0 0 0 0 rgba(178, 212, 252, 0.7);
    }
}

/* General Styles */
.ai-aside {
    background-color: #f8f9fa;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 1rem;
    max-width: 300px;
    width: 100%;
}

.input-group {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    width: 100%;
}

.form-control {
    padding: 0.75rem 1.25rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.btn-primary {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.rooms-scroll {
    max-height: 300px;
    /* scroll only rooms if too many */
    overflow-y: auto;
    padding-right: 5px;
}

/* Make aside scrollable nicely */
.ai-aside::-webkit-scrollbar {
    width: 6px;
}

.ai-aside::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.ai-aside::-webkit-scrollbar-thumb {
    background: #0d6efd;
    border-radius: 3px;
}
</style>