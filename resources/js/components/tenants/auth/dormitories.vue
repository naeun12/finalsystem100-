<template>
    <Loader ref="loader" />

    <div class="container-fluid py-4 bg-light min-vh-100 d-flex flex-column flex-lg-row gap-4">
        <!-- Main dorm listing area -->
        <div class="flex-grow-1">
            <div class="container-fluid mt-3">
                <h2 class="mb-4 text-primary fw-semibold text-center">Find Your Ideal Dorm in Mandaue and Lapu-Lapu City
                </h2>
            </div>
            <div class="text-primary">
                Top Rated Dormitories
            </div>

            <div class="input-group mb-4 w-100 shadow-sm rounded-pill overflow-hidden">
                <span class="input-group-text bg-white border-0">
                    <i class="bi bi-search text-primary"></i>
                </span>
                <input type="text" class="form-control border-0 shadow-none" placeholder="Search Locations"
                    aria-label="Search Locations" v-model="searchQuery" @input="debouncedSearch" />
            </div>

            <div class="row mb-4">
                <!-- Price Range Dropdown -->
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

                <!-- Occupancy Type Dropdown -->
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


            <div class="row g-4">
                <div class="col-12 col-sm-6 col-md-4 col-lg-3" v-for="(dorm, dorm_id) in dormitories" :key="dorm_id">
                    <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden d-flex flex-column">
                        <div class="image-wrapper">
                            <img :src="dorm?.images?.main_image ||
                                dorm?.main_image ||
                                'https://placehold.co/300x200?text=No+Image'
                                " class="card-img-top" :alt="dorm.dorm_name" />
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between flex-grow-1">
                            <div>
                                <h5 class="card-title text-dark fw-bold">{{ dorm.dorm_name }}</h5>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-person-fill"></i> {{ dorm.occupancy_type }}
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-geo-alt-fill"></i> {{ dorm.address }}
                                </p>
                            </div>
                            <div class="mt-4 d-flex justify-content-center">
                                <button class="btn rounded-pill px-4 w-100" @click="viewDormsDetails(dorm.dorm_id)">View
                                    Details</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar: recommendations only show if there are any -->
        <aside class="sidebar bg-white shadow-sm p-3 rounded position-relative w-100 w-lg-25 mt-4 mt-lg-0"
            v-if="showRecommendations" style="max-width: 350px;">
            <!-- Close button -->
            <div class="mb-5">
                <button @click="hiderecommendations" class="btn btn-sm btn-light mb-3 position-absolute top-0 end-0 m-2"
                    aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div v-if="recommendloading"
                class="d-flex flex-column justify-content-center align-items-center text-center"
                style="position: absolute; inset: 0; background-color: rgba(255,255,255,0.8); z-index: 10; border-radius: 0.5rem;">

                <section class="dots-container ">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </section>
                <p class="text-black">Fetching recommendations, please wait...</p>
            </div>

            <h5 class="text-primary fw-semibold mb-4 text-center">
                <i class="bi bi-star-fill me-2"></i>
                {{ recommend }}
            </h5>
            <div class="input-group mb-2 w-100 shadow-sm rounded-pill overflow-hidden" v-if="!isGenderBased">
                <span class="input-group-text bg-white border-0">
                    <i class="bi bi-search text-primary"></i>
                </span>
                <input type="text" class="form-control border-0 shadow-none" placeholder="Search Locations"
                    aria-label="Search Locations" v-model="searchPriceLocations" @input="debouncedpriceSearch" />
            </div>
            <div class="input-group mb-2 w-100 shadow-sm rounded-pill overflow-hidden" v-if="isGenderBased">
                <span class="input-group-text bg-white border-0">
                    <i class="bi bi-search text-primary"></i>
                </span>
                <input type="text" class="form-control border-0 shadow-none" placeholder="Search Location"
                    aria-label="Search Locations" v-model="searchGenderLocations" @input="debouncedgenderSearch" />
            </div>

            <div class="col-md-6 col-lg-4 mb-2 w-75">

                <select class="form-select shadow-sm " v-if="!isGenderBased" v-model="priceFilterOccupancyType"
                    @change="priceDropdownOccupancyType">
                    <option disabled value="">Select Occupancy Type</option>
                    <option value="all">All Types</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="mixed (male & female – separate floors)">Mixed</option>

                </select>
            </div>
            <div class="col-md-6 col-lg-4 mb-2 w-100" v-if="isGenderBased">
                <select class="form-select shadow-sm" v-model="genderSelectedPriceRange"
                    @change="genderDropdownpriceRange">
                    <option disabled value="">Select Price Range (based on rooms)</option>
                    <option value="all">All Prices</option>
                    <option value="0-100">₱0 - ₱100</option>
                    <option value="101-200">₱101 - ₱200</option>
                    <option value="201-300">₱201 - ₱300</option>
                    <option value="301+">₱301 and above</option>
                </select>
            </div>
            <div v-if="!recommendloading && recommendations.length === 0" class="text-center text-muted my-4">
                <i class="bi bi-emoji-frown fs-4"></i>
                <p class="mt-2 mb-0">No results found for your search.</p>
                <small>Try another location or price range.</small>
            </div>
            <div v-for="(dorm, index) in recommendations" :key="'ai-' + index" class="mb-3">
                <div class="card shadow-sm">
                    <div class="card-body p-3">
                        <div class="image-wrapper">
                            <img :src="dorm?.images?.main_image ||
                                dorm?.main_image ||
                                'https://placehold.co/300x200?text=No+Image'
                                " class="card-img-top" :alt="dorm.dorm_name" />
                        </div>
                        <h6 class="mb-2">
                            <i class="bi bi-building me-2 text-primary"></i>{{ dorm.dorm_name }}
                        </h6>
                        <p class="mb-1 small text-muted">
                            <i class="bi bi-geo-alt-fill me-1"></i>{{ dorm.address }}
                        </p>
                        <p v-if="!isGenderBased || visiblecard" class="mb-1 small">
                            <strong>₱{{ dorm.price }}</strong>
                            <span class="mx-2">|</span>
                            <i class="bi bi-door-open me-1"></i>{{ dorm.room_type }}
                        </p>
                        <p v-if="isGenderBased || visiblecard" class="mb-1 small">
                            <i class="bi bi-people-fill me-1"></i>Occupancy Type: {{ dorm.occupancy_type }}
                        </p>
                        <button class="btn mt-3 w-75 mx-auto d-block" @click="viewDormsDetails(dorm.dorm_id)" style="
                            height: 50px;">View Details</button>
                    </div>
                    <div v-if="!recommendloading && recommendations.length === 0" class="text-center text-muted my-4">
                        <i class="bi bi-emoji-frown fs-4"></i>
                        <p class="mt-2 mb-0">No results found for your search.</p>
                        <small>Try another location or price range.</small>
                    </div>

                </div>
            </div>
        </aside>


    </div>
</template>

<script>
import axios from 'axios';
import _ from 'lodash';
import debounce from 'lodash/debounce';
import Loader from '@/components/loader.vue';

export default {
    components: {
        Loader,
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
            showRecommendations: false,
            isGenderBased: false,
            searchlocations: '',
            searchPriceLocations: '',
            searchGenderLocations: '',
            priceFilterOccupancyType: '',
            genderSelectedPriceRange: '',
            visiblecard: false,
            recommend: '',

        };
    },
    methods: {
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
            if (this.$refs.loader) {
                this.showRecommendations = true;
            }
            if (!this.showRecommendations) {
                // First time loading
                if (this.$refs.loader) {
                }
            } else {
                // Already showing recos – refresh/reload only
                this.recommendloading = true;
            }
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
                    this.showRecommendations = true;
                    this.recommendloading = false;
                    this.recommendations = result.data;
                    this.filteredDorms = this.recommendations;
                    this.isGenderBased = false;
                    this.recommend = 'Recommended dorms based on your selected room price range';
                    this.selectedOccupancyType = '';
                    console.log(this.recommendations);
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
                this.recommendloading = true;
                if (this.$refs.loader) {
                    this.showRecommendations = true;
                }
                if (!this.showRecommendations) {
                    // First time loading
                    if (this.$refs.loader) {
                    }
                } else {
                    // Already showing recos – refresh/reload only
                    this.recommendloading = true;
                }

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
                    this.recommendations = result.recommendations;
                    this.showRecommendations = true;
                    this.isGenderBased = true; // set this to true for gender
                    this.recommend = 'Recommended dorms based on your selected occupancy preference';
                    this.selectedPriceRange = '';
                    this.recommendloading = false;
                    this.genderSelectedPriceRange = '';

                }
            }
            catch (error) {
                this.recommendloading = false;
                this.$refs.loader.loading = false;
            }
        },
        async recommendationsPriceSearchLocations() {
            try {
                this.recommendloading = true;
                if (!this.searchPriceLocations.trim()) {

                    await this.dropdownPriceRecommendations();  // re-fetch all dorms
                    return;
                }

                const priceRange = this.selectedPriceRange || 'all';
                let min_price = 0, max_price = 999999;

                if (priceRange !== 'all') {
                    if (priceRange.includes('+')) {
                        min_price = parseInt(priceRange.replace('+', '')) || 0;
                        max_price = 999999;
                    } else {
                        const [min, max] = priceRange.split('-');
                        min_price = parseInt(min) || 0;
                        max_price = parseInt(max) || 999999;
                    }
                }

                const response = await fetch('/api/search', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        keyword: this.searchPriceLocations,
                        min_price,
                        max_price
                    }),
                });

                const result = await response.json();

                if (result.status === 'success') {
                    this.recommendations = result.recommendations;

                }
            } catch (error) {
                console.error('Search error:', error);
            } finally {
                this.recommendloading = false;
            }
        },
        async recommendationsGenderLocations() {
            try {
                this.recommendloading = true;
                if (!this.searchPriceLocations.trim()) {

                    await this.dropdownPriceRecommendations();  // re-fetch all dorms
                    return;
                }

                const priceRange = this.selectedPriceRange || 'all';
                let min_price = 0, max_price = 999999;

                if (priceRange !== 'all') {
                    if (priceRange.includes('+')) {
                        min_price = parseInt(priceRange.replace('+', '')) || 0;
                        max_price = 999999;
                    } else {
                        const [min, max] = priceRange.split('-');
                        min_price = parseInt(min) || 0;
                        max_price = parseInt(max) || 999999;
                    }
                }

                const response = await fetch('/api/search', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        keyword: this.searchPriceLocations,
                        min_price,
                        max_price
                    }),
                });

                const result = await response.json();

                if (result.status === 'success') {
                    this.recommendations = result.recommendations;



                }
            } catch (error) {
                console.error('Search error:', error);
            } finally {
                this.recommendloading = false;
            }
        },
        async genderLocationSearch() {
            if (!this.searchGenderLocations.trim()) {

                await this.genderDropdownpriceRange();  // re-fetch all dorms
                return;
            }

            try {
                const response = await fetch('/recommendations/gender-location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        occupancy_type: this.selectedOccupancyType,
                        location: this.searchGenderLocations
                    })
                });

                const result = await response.json();
                if (result.status === 'success') {
                    this.recommendations = result.recommendations;


                } else {
                    this.recommendations = [];
                }
            } catch (error) {
                console.error('Gender + Location search failed:', error);
            }
        },
        priceDropdownOccupancyType() {
            this.recommendloading = true;
            if (this.$refs.loader) {
                this.showRecommendations = true;
            }
            if (!this.showRecommendations) {
                // First time loading
                if (this.$refs.loader) {
                }
            } else {
                // Already showing recos – refresh/reload only
                this.recommendloading = true;
            }
            axios.get('/dormitories/filter', {
                params: {
                    price_range: this.selectedPriceRange,
                    occupancy_type: this.priceFilterOccupancyType.toLowerCase()
                }
            })
                .then(response => {
                    // Assuming backend returns: { status: 'success', recommendations: [...] }
                    this.recommendations = response.data.recommendations || [];
                    this.recommendloading = false;
                    this.searchPriceLocations = '';
                })
                .catch(error => {
                    console.error('Error fetching filtered dormitories:', error);
                    this.recommendloading = false; // Add this line

                });
        },
        async genderDropdownpriceRange() {
            this.recommendloading = true;
            if (this.$refs.loader) {
                this.showRecommendations = true;
            }
            if (!this.showRecommendations) {
                // First time loading
                if (this.$refs.loader) {
                }
            } else {
                // Already showing recos – refresh/reload only
                this.recommendloading = true;
            }
            axios.post('/filterpricegender-dormitories', {
                occupancy_type: this.selectedOccupancyType,
                price_range: this.genderSelectedPriceRange,
            })
                .then(response => {
                    this.recommendations = response.data.recommendations;
                    this.visiblecard = true;
                    this.recommendloading = false; // Add this line
                    this.searchGenderLocations = '';

                })
                .catch(error => {
                    console.error("Error fetching filtered dorms:", error);
                    this.recommendloading = false; // Add this line

                });
        },
        hiderecommendations() {
            this.showRecommendations = false;
            this.genderSelectedPriceRange = '';
            this.filteredDorms = [];
            this.isGenderBased = false;
            this.selectedOccupancyType = '';
            this.searchGenderLocations = '';
            this.searchPriceLocations = '';


        }
    },
    created() {
        this.debouncedSearch = debounce(this.searchLocations, 500);
        this.debouncedpriceSearch = debounce(this.recommendationsPriceSearchLocations, 500);
        this.debouncedgenderSearch = debounce(this.genderLocationSearch, 500);
    },
    mounted() {
        this.dormListingfetch();
    },
};


</script>

<style scoped>
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

/* Default desktop styles */


/* Responsive styles for tablets and below */
</style>