<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <NotificationList ref="toastRef" />

    <div class=" m-5 my-4" v-if="dorm">
        <!-- Header -->
        <div class="row mb-4 align-items-center border-bottom pb-3 shadow-sm rounded bg-light px-3 py-3">
            <div class="col-12 col-md-8">
                <h2 class="fw-bold text-primary mb-1">{{ this.landlordname }}</h2>
                <p class="text-muted mb-0"><i class="bi bi-clock"></i> Posted {{ formatDate(dorm.dorm.created_at) }}</p>
            </div>
            <div
                class="col-12 col-md-4 text-md-end text-start mt-3 mt-md-0 d-flex gap-3 justify-content-md-end justify-content-start">
                <!-- Ask AI Button -->
                <button class="btn btn-primary px-4 py-2 rounded-pill shadow-sm d-flex align-items-center"
                    @click="askAI">
                    <i class="bi bi-robot fs-5 me-2"></i> Smart Guide
                </button>

                <!-- Message Button -->
                <button class="btn btn-outline-success px-4 py-2 rounded-pill shadow-sm d-flex align-items-center"
                    @click="messagePage">
                    <i class="bi bi-envelope-fill fs-5 me-2"></i> Message
                </button>
            </div>

        </div>


        <!-- Main Content -->
        <div class="row gy-4">
            <!-- Images Section -->
            <div class="col-12 col-md-4">
                <div class="mb-3 border rounded overflow-hidden shadow-sm">
                    <img :src="mainImage" alt="Main Image" class="rounded border w-100"
                        style="height: 300px; object-fit: cover;" />
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <div v-for="(img, index) in images" :key="index" class="flex-grow-1" style="max-width: 32%;">
                        <img :src="img" :alt="'Thumbnail ' + (index + 1)"
                            class="rounded border clickable-thumbnail w-100"
                            :class="{ 'border-primary': mainImage === img }" @click="changeMainImage(img)"
                            style="height: 100px; object-fit: cover; cursor: pointer;" />
                    </div>
                </div>
            </div>


            <!-- Amenities and Room Features -->
            <div class="col-12 col-md-4">
                <div class="bg-light rounded p-3 shadow-sm mb-3">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-stars me-2 text-primary"></i>Amenities
                    </h5>
                    <ul class="ps-3 mb-0">
                        <li v-for="(aminity, index) in displayedAmenities" :key="aminity.id">
                            {{ aminity.aminityName }}
                        </li>
                    </ul>
                    <div class="text-center mt-2" v-if="amenities.length > 3">
                        <a href="#" class="text-decoration-none text-primary fw-semibold"
                            @click.prevent="amenitiesShowMore = !amenitiesShowMore">
                            {{ amenitiesShowMore ? '-- Show Less --' : '-- More --' }}
                        </a>
                    </div>
                </div>

                <div class=" bg-light rounded p-3 shadow-sm  p-2">
                    <h5 class="fw-bold ">
                        <i class="bi bi-door-open me-2"></i>Rating and Review
                    </h5>

                    <div class="text-muted" style="white-space: pre-line;">
                        <div class="">
                            <!-- Stars -->
                            <div class="text-warning mb-1" style="font-size: 1.8rem;">
                                <i v-for="n in 5" :key="n" :class="getStarClass(n)"></i>
                                <span class="ms-2 fw-bold text-dark" style="font-size: 1.2rem;">
                                    {{ averagePercentage }}%
                                </span>
                            </div>

                            <p class="mb-1" style="font-size: 1.1rem;">
                                <i class="bi bi-people-fill text-secondary me-2"></i>
                                <strong>Total Reviews:</strong> {{ totalReviewers }}
                            </p>
                        </div>
                    </div>
                    <button class="btn btn-warning btn-md" @click="clickRatingandReview()">
                        Rating and Review
                    </button>

                </div>


            </div>

            <!-- Rules and Contact -->
            <div class="col-12 col-md-4">
                <div class="bg-light rounded p-3 shadow-sm mb-3">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-info-circle me-2 text-primary"></i>Rules & Policies
                    </h5>
                    <div class="text-muted" style="white-space: pre-line;">
                        <ul class="ps-3 mb-0">
                            <li v-for="(rule, index) in displayedRulesAndPolicy" :key="rule.id">
                                {{ rule.rulesName }}
                            </li>
                        </ul>
                    </div>
                    <div class="text-center mt-2" v-if="rulesAndPolicy.length > 3">
                        <a href="#" class="text-decoration-none text-primary fw-semibold"
                            @click.prevent="rulesAndPolicyShowMore = !rulesAndPolicyShowMore">
                            {{ rulesAndPolicyShowMore ? '-- Show Less --' : '-- More --' }}
                        </a>
                    </div>
                </div>

                <div class="bg-light rounded p-3 shadow-sm">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-person-lines-fill me-2 text-primary"></i>Contact Information
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-telephone me-2 text-muted"></i>{{ dorm.dorm.contactPhone }}
                        </li>
                        <li>
                            <i class="bi bi-envelope me-2 text-muted"></i>{{ dorm.dorm.contactEmail }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Property Details and Form -->
        <div class="row mt-3 gy-4 ">
            <!-- Property Details -->
            <div class="col-12 col-md-8">
                <div class="border rounded p-4 shadow-sm bg-white">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="fw-bold text-primary mb-1">
                                <i class="bi bi-house-door-fill me-2 text-primary"></i>{{ dorm.dorm.dormName }}
                            </h4>

                            <p class="text-muted mb-3">
                                <i class="bi bi-geo-alt-fill me-2 text-secondary"></i>
                                {{ dorm.dorm.address.replace('at the back of ', '') }}
                            </p>


                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge d-inline-flex align-items-center px-3 py-2 fs-6" :class="{
                                    'bg-success': dorm.dorm.availability === 'Available',
                                    'bg-danger': dorm.dorm.availability === 'Not Available'
                                }">
                                    <i class="bi" :class="{
                                        'bi-check-circle-fill me-2': dorm.dorm.availability === 'Available',
                                        'bi-x-circle-fill me-2': dorm.dorm.availability === 'Not Available'
                                    }"></i>
                                    {{ dorm.dorm.availability }}
                                </span>
                            </div>
                            <p>
                                <i class="bi bi-geo-alt-fill me-2 text-primary"></i>
                                <strong>Location:</strong>
                                {{ dorm.dorm.address.replace('at the back of ', '') }}
                            </p>

                            <p>
                                <i class="bi bi-people-fill me-2 text-primary"></i>
                                <strong>Occupancy Type:</strong>
                                {{ dorm.dorm.occupancyType }}
                            </p>

                            <p>
                                <i class="bi bi-building me-2 text-primary"></i>
                                <strong>Building Type:</strong>
                                {{ dorm.dorm.buildingType }}
                            </p>

                            <p>
                                <i class="bi bi-door-open-fill me-2 text-primary"></i>
                                <strong>Rooms Available:</strong>
                                {{ dorm.dorm.totalRooms > 0 ? dorm.dorm.totalRooms + ' room(s)' : 'No rooms available'
                                }}
                            </p>
                            <p class="mb-4">
                                <i class="bi bi-person-fill-check me-2 text-primary"></i>
                                <strong>Total tenants currently residing:</strong>
                                {{ dorm.dorm.totalCapacity }} tenant(s)
                            </p>
                        </div>

                        <div class="col-md-4">

                            <div class="ratio ratio-4x3">
                                <div id="map" style="width: 100%; height: 300px;"></div>
                            </div>
                        </div>

                    </div>
                    <h5 class="fw-bold mt-4 mb-3"><i class="bi bi-cash-coin me-2"></i>Room Pricing</h5>
                    <div class="card shadow-sm mb-4 p-3" style="max-height: 400px; max-width: 100%; overflow-y: auto;">
                        <div class="d-flex align-items-center mb-3">
                            <h5 class="mb-0 fw-bold">Room Types & Monthly Rates</h5>
                            <i class="bi bi-building ms-2 fs-4 text-primary"></i>
                        </div>

                        <div v-if="rooms.length === 0" class="text-muted fst-italic">
                            No rooms available
                        </div>

                        <div v-else class="row g-3">
                            <div v-for="room in rooms" :key="room.roomID" class="col-md-6 col-lg-4">
                                <div class="card h-100 border-primary shadow-sm" @click="roomDetails(room.roomID)">
                                    <div class="card-body">
                                        <h6 class="card-title fw-semibold">{{ room.roomType }}</h6>
                                        <p class="card-text fs-5 text-success">₱{{ room.price.toLocaleString() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>


            <!-- Booking Form -->
            <div class="col-12 col-md-4">
                <form class="border rounded p-4 shadow bg-white">

                    <h5 class="fw-bold mb-4 text-center text-primary">
                        <i class="bi bi-calendar-check-fill me-2"></i>Fill up your Information
                    </h5>

                    <div class="mb-3 px-4">
                        <label for="firstName" class="form-label fw-semibold">
                            <i class="bi bi-person-fill me-2 text-primary"></i>First Name
                        </label>
                        <input id="firstName" type="text" v-model="firstname" class="form-control shadow-sm"
                            style="border: 2px solid #4edce2;" placeholder="Enter your first name" />
                        <span v-if="errors.firstname" class="error text-danger small mt-1 d-block">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.firstname[0] }}
                        </span>
                    </div>


                    <div class="mb-3 px-4">
                        <label for="lastName" class="form-label fw-semibold">
                            <i class="bi bi-person-badge-fill me-2 text-primary"></i>Last Name
                        </label>
                        <input id="lastName" type="text" v-model="lastname" class="form-control shadow-sm"
                            style="border: 2px solid #4edce2;" placeholder="Enter your last name" />
                        <span v-if="errors.lastname" class="error text-danger small mt-1 d-block">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.lastname[0] }}
                        </span>
                    </div>


                    <div class="mb-3 px-4">
                        <label for="contactNumber" class="form-label fw-semibold">
                            <i class="bi bi-telephone-fill me-2 text-primary"></i>Contact Number
                        </label>
                        <input id="contactNumber" type="text" v-model="contactInfo" class="form-control shadow-sm"
                            style="border: 2px solid #4edce2;" placeholder="Enter your phone number" />
                        <span v-if="errors.contactInfo" class="error text-danger small mt-1 d-block">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.contactInfo[0] }}
                        </span>

                    </div>


                    <!-- Email -->
                    <div class="mb-3 px-4">
                        <label for="email" class="form-label fw-semibold">
                            <i class="bi bi-envelope-fill me-2 text-primary"></i>Email Address
                        </label>
                        <input id="email" type="email" v-model="email" class="form-control shadow-sm"
                            style="border: 2px solid #4edce2;" placeholder="email@example.com" />
                        <span v-if="errors.email" class="error text-danger small mt-1 d-block">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.email[0] }}
                        </span>
                    </div>

                    <!-- Age -->
                    <div class="mb-3 px-4">
                        <label for="age" class="form-label fw-semibold">
                            <i class="bi bi-person-plus-fill me-2 text-primary"></i>Age
                        </label>
                        <input id="age" type="number" v-model.number="age" class="form-control shadow-sm"
                            style="border: 2px solid #4edce2;" min="15" max="60" placeholder="Enter your age" />
                        <span v-if="errors.age" class="error text-danger small mt-1 d-block">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.age[0] }}
                        </span>
                    </div>

                    <!-- Gender -->
                    <div class="mb-3 px-4">
                        <label for="sex" class="form-label fw-semibold">
                            <i class="bi bi-gender-ambiguous me-2 text-primary"></i>Sex
                        </label>
                        <select id="sex" v-model="sex" class="form-select shadow-sm" style="border: 2px solid #4edce2;">
                            <option value="" disabled>Select</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                        <span v-if="errors.sex" class="error text-danger small mt-1 d-block">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.sex[0] }}
                        </span>
                    </div>

                    <!-- Move-in/out Dates -->

                    <div class="px-4">
                        <button type="button" @click="submitTenantInformation"
                            class="custom-btn w-100 fw-bold">Submit</button>
                    </div>
                    <div v-if="VisibleImagePostModal" class="modal fade show d-block w-100" tabindex="-1"
                        style="background-color: rgba(0,0,0,0.5);">
                        <div class="modal-dialog modal-xl modal-dialog-centered ">
                            <div class="modal-content shadow-lg rounded-4 overflow-hidden py-1">
                                <!-- Header -->
                                <div class="modal-header  text-black">
                                    <h5 class="modal-title">Upload ID PICTURE</h5>
                                    <button type="button" class="btn-close" @click="closeImageModal"></button>
                                </div>
                                <!-- Upload Container -->
                                <div v-if="isImage"
                                    class="container border border-secondary rounded-3  p-4 mb-3 text-center"
                                    style="cursor: pointer;" @click="triggeridPictureImage">
                                    <!-- Hidden File Input -->
                                    <input ref="idPicturesInput" class="d-none" type="file" accept="image/*"
                                        @change="handleidPictre" />

                                    <!-- Upload Prompt -->
                                    <div class="d-flex flex-column align-items-center text-center mb-3">
                                        <img :src="id_picture" alt="Payment Icon" style="max-width: 60px; height: auto;"
                                            class="mb-2" />
                                        <h5 class="text-secondary mt-2">Upload ID Image</h5>
                                        <small class="text-muted">Click to browse and select an image file</small>
                                    </div>
                                </div>

                                <!-- Preview Container -->
                                <div v-if="idPicturePreview" class="text-center mb-3">
                                    <img :src="idPicturePreview" alt="Uploaded ID Image"
                                        class="img-fluid rounded mb-2 shadow-sm" style="max-height: 250px;" />
                                    <div>
                                        <button type="button" @click="removeidPicture" class="btn  btn-sm"
                                            style="background-color: red; color: white;">
                                            Remove Uploaded Image
                                        </button>
                                    </div>


                                </div>
                                <div class="d-flex justify-content-center align-items-center">
                                    <button type="button" class="custom-btn mb-3 w-50" @click="tenantIdpicture">
                                        Select a room
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div v-if="askAIModal" class="modal fade show d-block w-100" tabindex="-1"
        style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-4 overflow-hidden">

                <!-- Header -->
                <div class="modal-header bg-gradient text-white">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-robot fs-4 me-2"></i> Smart Guide for {{ selectedDormAI.dormName }}
                    </h5>
                    <button type="button" class="btn-close btn-close-black" @click="askAIModal = false"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="aiQuestion" class="form-label fw-semibold">Your Question</label>
                        <textarea id="aiQuestion" v-model="aiQuestion" class="form-control rounded-3 shadow-sm" rows="4"
                            placeholder="Type your question about dormitory..."></textarea>
                    </div>
                    <div v-if="aiResponse" class="alert alert-light border shadow-sm rounded-3">
                        <h6 class="fw-bold mb-2"><i class="bi bi-cpu me-2 text-primary"></i> AI Response</h6>
                        <p class="mb-0">{{ aiResponse }}</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" @click="askAIModal = false">
                        <i class="bi bi-x-circle me-2"></i> Close
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill shadow-sm" @click="sendToAI">
                        <i class="bi bi-send-fill me-2"></i> Ask AI
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div v-if="roomDetailsModal" class="modal fade show d-block w-100" tabindex="-1"
        style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-4 overflow-hidden py-1">
                <!-- Header -->
                <div class="modal-header text-black border-bottom">
                    <h5 class="modal-title">{{ selectedRoomDetails.roomType || 'Room Details' }}</h5>
                    <button type="button" class="btn-close" @click="roomDetailsModal = false"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Left: Room Images / Carousel -->
                        <div class="col-md-6">
                            <div v-if="selectedRoomDetails.roomImages" class="overflow-hidden rounded shadow-sm">
                                <img :src="selectedRoomDetails.roomImages"
                                    class="img-fluid w-100 h-100 object-fit-cover" alt="Room Image">
                            </div>
                            <div v-else
                                class="border rounded d-flex align-items-center justify-content-center p-5 text-muted">
                                No image available
                            </div>
                        </div>

                        <!-- Right: Room Info -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5 class="fw-bold mb-1">{{ selectedRoomDetails.roomType || 'Room Type' }}</h5>
                                <p class="text-success fs-5 mb-0">₱{{ selectedRoomDetails.price?.toLocaleString() }}</p>
                                <small class="text-muted">{{ selectedRoomDetails.furnishing_status || 'Furnishing info not available' }}</small>
                            </div>

                            <hr class="my-3">

                            <div class="mb-3">
                                <h6 class="fw-semibold">Area</h6>
                                <p>{{ selectedRoomDetails.areaSqm }} sqm</p>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-semibold">Features</h6>
                                <div v-if="selectedRoomDetails.features && selectedRoomDetails.features.length > 0"
                                    class="d-flex flex-wrap gap-2 overflow-auto" style="max-height: 150px;">
                                    <span v-for="feature in selectedRoomDetails.features" :key="feature.id"
                                        class="badge bg-success text-white">
                                        {{ feature.featureName }}
                                    </span>
                                </div>
                                <div v-else class="text-muted fst-italic">
                                    No features
                                </div>
                            </div>


                            <div class="mb-3">
                                <h6 class="fw-semibold">Availability</h6>
                                <span
                                    :class="selectedRoomDetails.availability ? 'badge bg-success' : 'badge bg-danger'">
                                    {{ selectedRoomDetails.availability ? 'Available' : 'Not Available' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" @click="roomDetailsModal = false">Close</button>
                </div>
            </div>
        </div>
    </div>
    <Modalconfirmation ref="modal" />

</template>

<script>
import axios from 'axios';
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
import NotificationList from '@/components/notifications.vue';
import { nextTick } from 'vue';
export default {
    components: {
        Toastcomponents,
        Loader,
        NotificationList,
        Modalconfirmation
    },
    data() {
        return {
            notifications: [],
            receiverID: '',
            landlordname: [],
            landlordImage: null,
            tenant_id: '',
            mainImage: '',
            paymentIcon: '/images/tenant/allimagesResouces/paymentIcon.jpg',
            id_picture: '/images/tenant/allimagesResouces/vector-id-card-icon.jpg',
            payment: {
                gcash: '/images/tenant/allimagesResouces/GCash-Logo.png',
                maya: '/images/tenant/allimagesResouces/maya.png',
                bank_transer: '/images/tenant/allimagesResouces/bank-transfer-logo.png',

            },
            images: [],
            rooms: [],
            lng: '',
            lat: '',
            roomsDetail: null,
            amenities: [],
            rulesAndPolicy: [],
            visibleRooms: 2,
            landlord_id: '',
            room_id: '',
            selectedRoomId: '',
            firstname: '',
            lastname: '',
            contactInfo: '',
            VisibleImagePostModal: false,
            age: null,
            sex: '',
            email: '',
            student_picture: "",
            dormitory_id: '',
            dorm: null,
            idPicturePreview: '',
            idPictureFile: null,
            openPaymentModel: false,
            openRoomModal: false,
            openRoomDetailsModal: false,
            errors: {},
            payment_type: '',
            totalCapacity: 0,
            PaymentPicturePreview: '',
            PaymentPictureFile: '',
            amenitiesShowMore: false,
            rulesAndPolicyShowMore: false,
            isImage: true,
            totalReviewers: 0,
            averagePercentage: 0,
            askAIModal: false,
            aiQuestion: '',
            aiResponse: '',
            selectedDormAI: [],
            roomDetailsModal: false,
            selectedRoomDetails: '',
        };
    },
    mounted() {
        nextTick(() => {
            const element = document.getElementById('RoomDetails');
            if (element) {
                this.dormitory_id = element.dataset.dormId;
                this.tenant_id = element.dataset.tenantId;
                this.displayDorms();
                this.dormLocation();
                this.subscribeToNotifications();
                this.fetchStats();

            } else {
                console.error("RoomDetails element not found!");
            }
        });
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
        changeMainImage(imgSrc) {
            this.mainImage = imgSrc;
        },
        loadImagesFromData(data) {
            // Collect images from data.dorm.images object, ignoring null or empty strings
            const imgs = [];
            let dormImages = data.dorm.images;

            if (dormImages.mainImage) imgs.push(dormImages.mainImage);
            if (dormImages.secondaryImage) imgs.push(dormImages.secondaryImage);
            if (dormImages.thirdImage) imgs.push(dormImages.thirdImage);

            this.images = imgs;
            this.mainImage = imgs.length > 0 ? imgs[0] : '';
        },

        async displayDorms() {
            try {
                const res = await axios.get('/dorm-details', { params: { dormitory_id: this.dormitory_id } });
                this.loadImagesFromData(res.data);
                this.dorm = res.data;
                this.lat = res.data.dorm.latitude || '';
                this.lng = res.data.dorm.longitude || '';
                this.rooms = res.data.dorm.rooms || [];
                this.amenities = res.data.dorm.amenities || [];
                this.rulesAndPolicy = res.data.dorm.rules_and_policy || [];
                this.landlordname = res.data.landlord?.firstname + ' ' + res.data.landlord?.lastname;
                this.landlord_id = res.data.landlord?.landlordID;
                this.totalCapacity = res.data.totalcapacity;

            } catch (error) {
                console.error("Error fetching dormitory:", error);
            }

        },
        formatDate(dateStr) {
            if (!dateStr) return '';
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateStr).toLocaleDateString('en-US', options);
        },

        fillForm() {
            this.firstname = '';
            this.lastname = '';
            this.email = '';
            this.contactInfo = '';
            this.age = '';
            this.sex = '';
            this.idPicturePreview = '';
            this.idPictureFile = '';
            this.selectedRoomId = '';

        },
        getInformationData() {
            const data = {
                firstname: this.firstname,
                lastname: this.lastname,
                email: this.email,
                contactInfo: this.contactInfo,
                age: this.age,
                sex: this.sex,
                idPicturePreview: this.idPicturePreview,
                idPictureFile: this.idPictureFile,
            };
            localStorage.setItem('tenantInfo', JSON.stringify(data)); // ✅ Store it

        },
        async submitTenantInformation() {
            this.$refs.loader.loading = true;
            const formData = new FormData();
            formData.append('firstname', this.firstname);
            formData.append('lastname', this.lastname);
            formData.append('contactInfo', this.contactInfo);
            formData.append('age', this.age);
            formData.append('sex', this.sex);
            formData.append('email', this.email);
            try {
                const response = await axios.post('/tenant-information', formData, {
                    withCredentials: true,
                    headers:
                    {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === 'success') {
                    this.$refs.loader.loading = false;
                    this.errors = {};
                    this.VisibleImagePostModal = true;

                }
            } catch (error) {
                this.$refs.loader.loading = false;
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                }
            }
        },
        async tenantIdpicture() {
            this.$refs.loader.loading = true;

            const formData = new FormData();
            formData.append('tenant_picture', this.idPictureFile);

            try {
                const response = await axios.post('/tenant-idPicture', formData, {
                    withCredentials: true,
                    headers:
                    {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === 'success') {
                    const imageUrl = response.data.image_url;

                    const data = {
                        firstname: this.firstname,
                        lastname: this.lastname,
                        email: this.email,
                        contactInfo: this.contactInfo,
                        age: this.age,
                        sex: this.sex,
                        idPicturePreview: this.idPicturePreview,
                        imageUrl: imageUrl,

                    };
                    localStorage.setItem('tenantInfo', JSON.stringify(data));
                    this.$refs.loader.loading = false;
                    this.VisibleImagePostModal = false;
                    window.location.href = `/room-selection/${this.dormitory_id}/${this.tenant_id}`;

                }
            } catch (error) {
                this.$refs.loader.loading = false;
                if (error.response && error.response.status === 422) {
                    const errorMessages = Object.values(error.response.data.errors).flat().join(' ');
                    this.$refs.toast.showToast(errorMessages, 'danger');
                }


            }

        },
        closeImageModal() {
            this.VisibleImagePostModal = false;
            this.idPicturePreview = '';
            this.idPictureFile = '';
            this.isImage = true;
        },

        handleidPictre(event) {
            const file = event.target.files[0];
            if (file) {
                if (this.idPicturePreview) {
                    URL.revokeObjectURL(this.idPicturePreview); // Clear old preview
                }

                this.idPictureFile = file;
                this.isImage = false;
                this.idPicturePreview = URL.createObjectURL(file);
            }
        },

        triggeridPictureImage() {
            if (this.$refs.idPicturesInput) {
                this.$refs.idPicturesInput.click();
            }

        },

        removeidPicture() {
            if (this.idPicturePreview) {
                URL.revokeObjectURL(this.idPicturePreview);
            }
            this.idPicturePreview = null;
            this.isImage = true;
            // Add null check for safety
            if (this.$refs.idPicturePreview) {
                this.$refs.idPicturePreview.value = ''; // Reset file input
            }
        },

        messagePage() {
            const url = `/tenant-message/${this.tenant_id}?landlord_id=${this.landlord_id}`;
            window.location.href = url;
        },

        initMap() {
            const mapDiv = document.getElementById("map");
            const dormMap = { lat: this.lat, lng: this.lng };
            const customStyle = [
                {
                    featureType: "poi",
                    elementType: "labels",
                    stylers: [{ visibility: "off" }]
                }
            ];

            // Initialize the map first
            const mapLapu = new google.maps.Map(mapDiv, {
                zoom: 15,
                center: dormMap,
                draggable: false,
                styles: customStyle
            });

            // Now place the marker
            new google.maps.Marker({
                position: dormMap,
                map: mapLapu,
                title: this.dorm.dorm.dorm_name || "Dorm Location",
                icon: {
                    url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                    scaledSize: new google.maps.Size(40, 40),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(20, 40)
                }
            });
        },
        dormLocation() {

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

        },
        async fetchStats() {
            try {
                const res = await axios.get(`/dorms/${this.dormitory_id}/review-stats`);
                this.totalReviewers = res.data.total_reviewers;
                this.averagePercentage = res.data.average_percentage;
            } catch (err) {
                console.error(err);
            }
        },
        getStarClass(starNumber) {
            const rating = this.averagePercentage / 20; // convert % to 5-star scale
            if (starNumber <= Math.floor(rating)) {
                return "bi bi-star-fill";
            } else if (starNumber - rating <= 0.5) {
                return "bi bi-star-half";
            } else {
                return "bi bi-star";
            }
        },
        clickRatingandReview() {
            window.location.href = `/rating/reviews/${this.dormitory_id}/${this.tenant_id}`;

        },
        async askAI() {
            try {
                this.$refs.loader.loading = true;

                const res = await axios.get(`/get/dorm/askai/${this.dormitory_id}`)
                if (res.data.status === 'success') {
                    this.askAIModal = true;
                    this.selectedDormAI = res.data.data;
                    this.$refs.loader.loading = false;

                    console.log(this.selectedDormAI);
                }

            } catch (error) {

            }
            finally {

            }
        },
        async sendToAI() {
            this.$refs.loader.loading = true;

            if (this.aiQuestion.trim() === "") {
                this.$refs.loader.loading = false;

                this.aiResponse = "⚠️ Please enter a question before asking AI.";
            } else {
                try {
                    const formdata = new FormData();
                    formdata.append('question', this.aiQuestion);
                    formdata.append('dormID', this.dormitory_id);

                    const res = await axios.post('/send/ai', formdata);

                    if (res.data.success) {   // ✅ 'success' instead of 'status'
                        this.aiResponse = res.data.answer;
                    } else {
                        this.aiResponse = "⚠️ AI could not process your request.";
                    }
                } catch (error) {
                    console.error(error);
                    this.aiResponse = "❌ Something went wrong while contacting AI.";
                }
                finally {
                    this.$refs.loader.loading = false;

                }
            }
        },
        async roomDetails(id) {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get(`/roomDetail/${id}`);
                if (response.data.success) {
                    this.selectedRoomDetails = response.data.roomDetail;
                    this.roomDetailsModal = true;
                }
            } catch (error) {
                console.error(error);
            }
            finally { 
                this.$refs.loader.loading = false;

            }
        }



    },
    watch: {
        dorm(newVal) {
            if (newVal && newVal.dorm && newVal.dorm.latitude && newVal.dorm.longitude) {
                this.lat = parseFloat(newVal.dorm.latitude);
                this.lng = parseFloat(newVal.dorm.longitude);

                this.$nextTick(() => {
                    this.dormLocation(); // or this.initMap() if already loaded
                });
            }
        }
    },
    computed: {
        displayedAmenities() {
            return this.amenitiesShowMore ? this.amenities : this.amenities.slice(0, 3);
        },
        displayedRulesAndPolicy() {
            return this.rulesAndPolicyShowMore ? this.rulesAndPolicy : this.rulesAndPolicy.slice(0, 3);
        },
        displayedRooms() {
            return this.rooms.slice(0, this.visibleRooms);
        },
    }
};
</script>

<style scoped>
/* Header styles */
</style>
