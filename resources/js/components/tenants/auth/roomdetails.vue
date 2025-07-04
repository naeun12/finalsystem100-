<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />

    <div class=" m-5 my-4" v-if="dorm">
        <!-- Header -->
        <div class="row mb-4 align-items-center border-bottom pb-3 shadow-sm rounded bg-light px-3 py-3">
            <div class="col-12 col-md-8">
                <h2 class="fw-bold text-primary mb-1">{{ this.landlordname }}</h2>
                <p class="text-muted mb-0"><i class="bi bi-clock"></i> Posted {{ formatDate(dorm.dorm.created_at) }}</p>
            </div>
            <div class="col-12 col-md-4 text-md-end text-start mt-3 mt-md-0">
                <button class="btn  px-4 rounded-pill shadow-sm" @click="messagePage">
                    <i class="bi bi-chat-dots me-2"></i> Message
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
                        <li v-for="(aminity, index) in displayedAmenities" :key="aminity.aminities_id">
                            {{ aminity.name }}
                        </li>
                    </ul>
                    <div class="text-center mt-2" v-if="amenities.length > 3">
                        <a href="#" class="text-decoration-none text-primary fw-semibold"
                            @click.prevent="amenitiesShowMore = !amenitiesShowMore">
                            {{ amenitiesShowMore ? '-- Show Less --' : '-- More --' }}
                        </a>
                    </div>
                </div>

                <div class="bg-light rounded p-3 shadow-sm">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-door-open me-2 text-primary"></i>Feedback and Review
                    </h5>
                    <div class="text-muted" style="white-space: pre-line;">
                        {{ dorm.dorm.room_features }}
                    </div>
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
                                {{ rule.rules_name }}
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
                            <i class="bi bi-telephone me-2 text-muted"></i>{{ dorm.dorm.contact_phone }}
                        </li>
                        <li>
                            <i class="bi bi-envelope me-2 text-muted"></i>{{ dorm.dorm.contact_email }}
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
                                <i class="bi bi-house-door-fill me-2 text-primary"></i>{{ dorm.dorm.dorm_name }}
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
                                {{ dorm.dorm.occupancy_type }}
                            </p>

                            <p>
                                <i class="bi bi-building me-2 text-primary"></i>
                                <strong>Building Type:</strong>
                                {{ dorm.dorm.building_type }}
                            </p>

                            <p>
                                <i class="bi bi-door-open-fill me-2 text-primary"></i>
                                <strong>Rooms Available:</strong>
                                {{ dorm.dorm.total_rooms > 0 ? dorm.dorm.total_rooms + ' room(s)' : 'No rooms available'
                                }}
                            </p>
                            <p class="mb-4">
                                <i class="bi bi-person-fill-check me-2 text-primary"></i>
                                <strong>Total Capacity:</strong> {{ totalCapacity }} tenant(s)
                            </p>
                        </div>

                        <div class="col-md-4">

                            <div class="ratio ratio-4x3">
                                <div id="map" style="width: 100%; height: 300px;"></div>
                            </div>
                        </div>

                    </div>


                    <h5 class="fw-bold mt-4 mb-3"><i class="bi bi-cash-coin me-2"></i>Room Pricing</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Room Type</th>
                                    <th>Monthly Rate (PHP)</th>
                                    <th>Inclusions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="rooms.length === 0">
                                    <td colspan="3">No rooms available</td>
                                </tr>
                                <tr v-for="room in rooms" :key="room.room_id">
                                    <td>{{ room.room_type }}</td>
                                    <td>₱{{ room.price.toLocaleString() }}</td>
                                    <td><!-- Add room inclusions here --></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="col-12 col-md-4">
                <form @submit.prevent="bookaroom" class="border rounded p-4 shadow bg-white">

                    <h5 class="fw-bold mb-4 text-center text-primary">
                        <i class="bi bi-calendar-check-fill me-2"></i>Dormitory Booking
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
                            <i class="bi bi-gender-ambiguous me-2 text-primary"></i>Gender
                        </label>
                        <select id="sex" v-model="sex" class="form-select shadow-sm" style="border: 2px solid #4edce2;">
                            <option value="" disabled>Select</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                        <span v-if="errors.sex" class="error text-danger small mt-1 d-block">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.sex[0] }}
                        </span>
                    </div>

                    <!-- Move-in/out Dates -->
                    <div class="d-flex gap-3 px-4">
                        <!-- Move In -->
                        <div class="mb-3 flex-fill">
                            <label for="move_in_date" class="form-label fw-semibold">
                                <i class="bi bi-calendar-event me-2 text-primary"></i>Move-in Date
                            </label>
                            <input type="date" class="form-control shadow-sm" v-model="moveInDate"
                                @change="setMoveOutDate" id="move_in_date" />
                        </div>

                        <!-- Move Out -->
                        <div class="mb-3 flex-fill">
                            <label for="move_out_date" class="form-label fw-semibold">
                                <i class="bi bi-calendar-check me-2 text-primary"></i>Move-out Date
                            </label>
                            <input type="date" class="form-control shadow-sm" v-model="moveOutDate" id="move_out_date"
                                disabled />
                        </div>
                    </div>
                    <div class="px-4">
                        <button type="button" @click="submitTenantInformation" class="btn w-100 fw-bold">Submit</button>
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
                                <div class="container border border-secondary rounded-3  p-4 mb-3 text-center"
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
                                    <button type="button" class="btn mb-3 w-50" @click="tenantIdpicture">
                                        Select a room
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>

        <!--image process-->

    </div>
    <Modalconfirmation ref="modal" />


</template>

<script>
import axios from 'axios';
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
import { nextTick } from 'vue';
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation
    },
    data() {
        return {
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
            moveInDate: '',
            moveOutDate: '',
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
        };
    },
    mounted() {
        const element = document.getElementById('RoomDetails');
        this.dormitory_id = element.dataset.dormId;
        this.tenant_id = element.dataset.tenantId;

        this.displayDorms();
        this.dormLocation();

    },
    methods: {
        changeMainImage(imgSrc) {
            this.mainImage = imgSrc;
        },
        loadImagesFromData(data) {
            // Collect images from data.dorm.images object, ignoring null or empty strings
            const imgs = [];
            let dormImages = data.dorm.images;

            if (dormImages.main_image) imgs.push(dormImages.main_image);
            if (dormImages.secondary_image) imgs.push(dormImages.secondary_image);
            if (dormImages.third_image) imgs.push(dormImages.third_image);

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
                this.landlord_id = res.data.landlord?.landlord_id;
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
                idPictureFile: this.idPictureFile
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
            formData.append('payment_type', this.payment_type);
            formData.append('payment_image', this.payment_image);

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
                        imageUrl: imageUrl
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

        },

        handleidPictre(event) {
            const file = event.target.files[0];
            if (file) {
                if (this.idPicturePreview) {
                    URL.revokeObjectURL(this.idPicturePreview); // Clear old preview
                }

                this.idPictureFile = file;
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
            // Add null check for safety
            if (this.$refs.idPicturePreview) {
                this.$refs.idPicturePreview.value = ''; // Reset file input
            }
        },
        setMoveOutDate() {
            if (this.moveInDate) {
                const date = new Date(this.moveInDate);
                date.setMonth(date.getMonth() + 1);

                // Adjust if date goes beyond valid month days (e.g., Feb 30)
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');

                this.moveOutDate = `${year}-${month}-${day}`;
            } else {
                this.moveOutDate = '';
            }
        },
        messagePage() {
            window.location.href = `/landlord-tenant-message/${this.tenant_id}/${this.landlord_id}`;

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
                    url: '/images/tenant/allimagesResouces/dormmap.webp',
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
                    "https://maps.googleapis.com/maps/api/js?key=AIzaSyCyQYH_O-3v9vW6ba_V653qgVECSxII0GU&callback=initMap";
                script.async = true;
                window.initMap = () => this.initMap(); // 👈 Fix here

                document.head.appendChild(script);

            } else {
                this.initMap();

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
