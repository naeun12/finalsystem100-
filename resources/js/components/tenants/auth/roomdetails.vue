<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <div class=" m-5 my-4" v-if="dorm">
        <!-- Header -->
        <div class="row mb-4 align-items-center border-bottom pb-3">
            <div class="col-12 col-md-8">


                <h2 class="fw-bold text-primary">{{ this.landlordname }}</h2>
                <p class="text-muted">Posted {{ formatDate(dorm.dorm.created_at) }}</p>
            </div>
            <div class="col-12 col-md-4 text-md-end text-start mt-2 mt-md-0">
                <i><button class="btn">Message</button></i>
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
                    <h5 class="fw-bold">Amenities</h5>

                    <ul class="ps-3 mb-0">
                        <li v-for="(aminity, index) in displayedAmenities" :key="aminity.aminities_id">
                            {{ aminity.name }}
                        </li>
                    </ul>

                    <div class="text-center mt-2" v-if="amenities.length > 3">
                        <a href="#" class="text-decoration-none text-primary fw-semibold"
                            @click.prevent="showMore = !showMore">
                            {{ showMore ? '--Show Less--' : '--More--' }}
                        </a>
                    </div>
                </div>


                <div class="bg-light rounded p-3 shadow-sm">
                    <h5 class="fw-bold">Room Features</h5>
                    <div>
                        <span>
                            {{ dorm.dorm.room_features }}
                        </span>
                    </div>


                </div>
            </div>

            <!-- Rules and Contact -->
            <div class="col-12 col-md-4">
                <div class="bg-light rounded p-3 shadow-sm mb-3">
                    <h5 class="fw-bold">Rules & Policies</h5>
                    <div>
                        <span>
                            {{ dorm.dorm.rules }}
                        </span>
                    </div>


                </div>

                <div class="bg-light rounded p-3 shadow-sm">
                    <h5 class="fw-bold">Contact Information</h5>
                    <ul class="list-unstyled mb-0">
                        <li><i class="fas fa-phone me-2 text-muted"></i>{{ dorm.dorm.contact_email }}</li>
                        <li><i class="fas fa-envelope me-2 text-muted"></i>{{ dorm.dorm.contact_phone }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Property Details and Form -->
        <div class="row mt-3 gy-4 ">
            <!-- Property Details -->
            <div class="col-12 col-md-8">
                <div class="border rounded p-4 shadow-sm bg-white">
                    <h4 class="fw-bold text-primary">{{ dorm.dorm.dorm_name }}</h4>
                    <p>{{ dorm.dorm.address.replace('at the back of ', '') }}</p>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge" :class="{
                            'bg-success': dorm.dorm.availability === 'Available',
                            'bg-danger': dorm.dorm.availability === 'Not Available'
                        }">
                            {{ dorm.dorm.availability }}
                        </span>
                    </div>


                    <p><strong>Location:</strong> {{ dorm.dorm.address.replace('at the back of ', '') }}</p>
                    <p><strong>Occupancy Type:</strong> {{ dorm.dorm.occupancy_type }}</p>
                    <p><strong>Building Type:</strong> {{ dorm.dorm.building_type }}</p>

                    <p><strong>Total Capacity:</strong> {{ totalCapacity }} tenants</p>

                    <h5 class="fw-bold mt-4">Room Pricing</h5>
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
                                    <td>₱{{ room.price }}</td>
                                    <td><!-- You can add inclusions here --></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="col-12 col-md-4">
                <form @submit.prevent="bookaroom" class="border rounded p-4 shadow bg-white">

                    <h5 class="fw-bold mb-4 text-center text-primary">Dormitory Booking</h5>

                    <div class="mb-3 px-4">
                        <label for="firstName" class="form-label">First Name</label>
                        <input id="firstName" type="text" v-model="firstname" class="form-control"
                            style="border: 2px solid #4edce2;" placeholder="First Name" />
                        <span v-if="errors.firstname" class="error text-danger">{{ errors.firstname[0] }}</span>
                    </div>

                    <div class="mb-3 px-4">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input id="lastName" type="text" v-model="lastname" class="form-control"
                            style="border: 2px solid #4edce2;" placeholder="Last Name" />
                        <span v-if="errors.lastname" class="error text-danger">{{ errors.lastname[0] }}</span>
                    </div>

                    <div class="mb-3 px-4">
                        <label for="contactNumber" class="form-label">Contact Number</label>
                        <input id="contactNumber" type="text" v-model="contactInfo" class="form-control"
                            style="border: 2px solid #4edce2;" placeholder="Phone or other contact" />
                        <span v-if="errors.contactInfo" class="error text-danger">{{ errors.contactInfo[0] }}</span>
                    </div>

                    <div class="mb-3 px-4">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" v-model="email" class="form-control"
                            style="border: 2px solid #4edce2;" placeholder="email@example.com" />
                        <span v-if="errors.email" class="error text-danger">{{ errors.email[0] }}</span>
                    </div>

                    <div class="mb-3 px-4">
                        <label for="age" class="form-label">Age</label>
                        <input id="age" type="number" v-model.number="age" class="form-control"
                            style="border: 2px solid #4edce2;" min="15" max="60" placeholder="Age" />
                        <span v-if="errors.age" class="error text-danger">{{ errors.age[0] }}</span>
                    </div>

                    <div class="mb-3 px-4">
                        <label for="sex" class="form-label">Sex</label>
                        <select id="sex" v-model="sex" class="form-select" style="border: 2px solid #4edce2;">
                            <option value="" disabled>Select</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                        <span v-if="errors.sex" class="error text-danger">{{ errors.sex[0] }}</span>
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


                    <div v-if="openRoomModal" class="modal fade show" tabindex="-1"
                        style="display: block; background-color: rgba(0, 0, 0, 0.5); position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content shadow-lg rounded-4 overflow-hidden">

                                <!-- Modal Header -->
                                <div class="modal-header text-white">
                                    <h5 class="modal-title">Room Selection</h5>
                                    <button type="button" class="btn-close" @click="openRoomModal = false"></button>
                                </div>

                                <!-- Modal Body -->
                                <div class="modal-body p-4">
                                    <div class="container">
                                        <h2 class="text-center mb-4 fw-bold text-primary">
                                            Available Rooms
                                        </h2>

                                        <div class="row g-3">
                                            <div class="col-12" v-for="room in displayedRooms" :key="room.room_id"
                                                data-aos="fade-right" data-aos-offset="300"
                                                data-aos-easing="ease-in-sine">

                                                <div class="card flex-md-row shadow-lg rounded-4 overflow-hidden"
                                                    style="height: 220px;">
                                                    <!-- Image Left -->
                                                    <div class="dorm-image" style="width: 35%; min-width: 200px;">
                                                        <img :src="room.room_images"
                                                            :alt="`Image of ${room.dorm?.dorm_name || 'Dorm'}`"
                                                            style="width: 100%; height: 100%; object-fit: cover;" />
                                                    </div>

                                                    <!-- Details Right -->
                                                    <div class="p-3 d-flex flex-column justify-content-between w-100">
                                                        <div class=" text-black px-3 py-1 rounded">
                                                            <h5 class="mb-0">{{ room.listing_type || 'Available Dorm' }}
                                                            </h5>
                                                        </div>

                                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                                            <span class="border px-3 py-1 rounded bg-light">
                                                                {{ room.area_sqm || 'N/A' }} sqm
                                                            </span>
                                                            <span class="border px-3 py-1 rounded bg-light">
                                                                {{ room.gender_preference || 'N/A' }}
                                                            </span>
                                                            <span class="border px-3 py-1 rounded bg-light">
                                                                {{ room.furnishing_status || 'N/A' }}
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                                                            <span class="fw-bold text-success fs-5">
                                                                ₱{{ Number(room.price).toLocaleString() || 'N/A' }} /
                                                                Head
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mt-3 mb-2 flex-wrap">
                                                            <a @click="openRoomDetails(room.room_id)"
                                                                class="d-block text-center text-decoration-none px-4 py-2"
                                                                style="cursor: pointer;">
                                                                View Details
                                                            </a>
                                                            <div class="d-flex gap-2 mt-2 mt-md-0 ">
                                                                <button type="button" class="btn btn-primary btn-sm"
                                                                    @click="paymentMethod(room.room_id)">
                                                                    Book
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- View More Logic -->
                                        <div class="mt-5 text-center">
                                            <a v-if="visibleRooms < rooms.length" @click="visibleRooms += 2"
                                                class="text-decoration-none px-4 py-2 d-inline-block rounded-pill border border-primary text-primary fw-semibold shadow-sm"
                                                style="cursor: pointer; transition: 0.3s;">
                                                <i class="bi bi-plus-circle me-2"></i> View More
                                            </a>

                                            <a v-if="visibleRooms > 2" @click="showLessRooms"
                                                class="text-decoration-none px-4 py-2 d-inline-block rounded-pill border border-secondary text-secondary fw-semibold shadow-sm ms-2"
                                                style="cursor: pointer; transition: 0.3s;">
                                                <i class="bi bi-dash-circle me-2"></i> Show Less
                                            </a>
                                        </div>


                                    </div>
                                </div> <!-- end of modal-body -->
                            </div>
                        </div>
                    </div>

                    <div v-if="openRoomDetailsModal" class="modal fade show d-block w-100" tabindex="-1"
                        style="background-color: rgba(0,0,0,0.5);">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content shadow-lg rounded-4 overflow-hidden">

                                <!-- Modal Header -->
                                <div class="modal-header  text-white">
                                    <h5 class="modal-title">Room Details</h5>
                                    <button type="button" class="btn-close" @click="CloseRoomDetails()"></button>
                                </div>

                                <!-- Modal Body -->
                                <div class="modal-body p-4">

                                    <div class="row g-4 ">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="p-2 border rounded bg-light text-break">{{
                                                    roomsDetail?.room_number
                                                }}
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Room Type:</label>
                                                <div class="p-2 border rounded bg-light text-break">{{
                                                    roomsDetail?.room_type
                                                }}
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Price:</label>
                                                <div class="p-2 border rounded bg-light text-break">₱{{
                                                    roomsDetail?.price
                                                }}
                                                </div>
                                            </div>


                                        </div>

                                        <div class="col-md-6 ">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Availability:</label>
                                                <div class="p-2 border rounded bg-light text-break">
                                                    <span class="badge"
                                                        :class="roomsDetail?.availability === 'Available' ? 'bg-success' : roomsDetail?.availability === 'Occupied' ? 'bg-danger' : 'bg-warning'">
                                                        {{ roomsDetail?.availability }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Capacity:</label>
                                                <div class="p-2 border rounded bg-light text-break">{{
                                                    roomsDetail?.capacity }}
                                                </div>
                                            </div>


                                        </div>


                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="modal-footer justify-content-center">
                                    <button type="button" class="btn  px-4" @click="CloseRoomDetails()">OK</button>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div v-if="openPaymentModel" class="modal fade show d-block" tabindex="-1"
                        style="background-color: rgba(0,0,0,0.5);">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content shadow-lg rounded-4 overflow-hidden">
                                <!-- Modal Header -->
                                <div class="modal-header  text-black">
                                    <h5 class="modal-title">Secure Payment</h5>
                                    <button type="button" class="btn-close btn-close-black"
                                        @click="ClosepaymentMethod"></button>
                                </div>

                                <!-- Modal Body -->
                                <div class="modal-body p-4">
                                    <div class="container">
                                        <!-- User Info -->
                                        <div class="row mb-1">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Firstname:</label>
                                                    <div class="p-2 border rounded bg-light text-break">{{
                                                        this.firstname }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Lastname:</label>
                                                    <div class="p-2 border rounded bg-light text-break">{{
                                                        this.lastname }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Contact Number:</label>
                                            <div class="p-2 border rounded bg-light text-break">{{
                                                this.contactInfo }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Contact Email:</label>
                                            <div class="p-2 border rounded bg-light text-break">{{
                                                this.email }}</div>
                                        </div>

                                        <!-- Room Details -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Room Number:</label>
                                            <div class="p-2 border rounded bg-light text-break">{{
                                                roomsDetail?.room_number }}</div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Room Type:</label>
                                            <div class="p-2 border rounded bg-light text-break">{{
                                                roomsDetail?.room_type
                                            }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Price:</label>
                                            <div class="p-2 border rounded bg-light text-break">{{
                                                roomsDetail?.price }}
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Capacity:</label>
                                            <div class="p-2 border rounded bg-light text-break">{{
                                                roomsDetail?.capacity
                                            }}</div>
                                        </div>

                                        <!-- Payment Type Input -->
                                        <div class="container py-4 mb-4">
                                            <label for="paymentType" class="form-label fw-semibold text-dark">
                                                Payment Method
                                            </label>
                                            <input type="text" class="form-control form-control-lg mb-2"
                                                id="paymentType" v-model="payment_type"
                                                placeholder="e.g., GCash, Bank Transfer, Maya" readonly
                                                aria-describedby="paymentTypeHelp" />
                                            <div id="paymentTypeHelp" class="form-text text-muted mb-3">
                                                Specify your preferred method of payment.
                                            </div>

                                            <div
                                                class="d-flex justify-content-center align-items-center gap-3 flex-wrap mt-3">
                                                <div v-for="(src, name) in payment" :key="name"
                                                    class="text-center p-2 border rounded"
                                                    :class="{ 'border-primary bg-light': payment_type === name }"
                                                    role="button" style="cursor: pointer;"
                                                    @click="paymentTypeSelection(name)">
                                                    <img :src="src" :alt="name" class="img-fluid mx-auto d-block"
                                                        style="width: 50px; height: 50px; object-fit: contain;" />
                                                    <small class="text-muted text-capitalize d-block mt-1">
                                                        {{ name.replace('_', ' ') }}
                                                    </small>
                                                </div>
                                            </div>



                                        </div>

                                        <!-- Payment Image Upload -->
                                        <div class="border border-secondary rounded-3 p-4 mb-3 text-center"
                                            style="cursor: pointer;" @click="triggerPaymentImage">

                                            <input ref="PaymentPicturesInput" class="d-none" type="file"
                                                accept="image/*" @change="handlePaymentPictre" />

                                            <!-- Payment Icon -->
                                            <div class="d-flex flex-column align-items-center text-center mb-3">
                                                <img :src="paymentIcon" alt="Payment Icon"
                                                    style="max-width: 60px; height: auto;" class="mb-2" />
                                                <h5 class="text-secondary mt-2">Upload Payment Image</h5>
                                                <small class="text-muted">Click to browse and select an image
                                                    file</small>
                                            </div>



                                        </div>


                                        <!-- Image Preview -->
                                        <div v-if="PaymentPicturePreview" class="text-center mb-3">
                                            <img :src="PaymentPicturePreview" alt="Uploaded Payment Image"
                                                class="img-fluid rounded mb-2" style="max-height: 250px;" />
                                            <div>
                                                <button type="button" @click="removePaymentPicture" class="btn  btn-sm">
                                                    Remove Uploaded Image
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" class="btn  w-100 py-2 fw-semibold">
                                            Make a Reservation
                                        </button>
                                    </div>
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
            roomsDetail: null,
            amenities: [],
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
            idPictureFile: '',
            openPaymentModel: false,
            openRoomModal: false,
            openRoomDetailsModal: false,
            errors: {},
            payment_type: '',
            totalCapacity: 0,
            PaymentPicturePreview: '',
            PaymentPictureFile: '',
            showMore: false,
            paymentIcon: '/images/tenant/allimagesResouces/paymentIcon.jpg',


        };
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
                this.rooms = res.data.dorm.rooms || [];
                this.amenities = res.data.dorm.amenities || [];
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
        validationForm() {

            this.errors = {};
            if (!this.firstname) this.errors.firstname = ['Please enter your firstmane.'];
            if (!this.lastname) this.errors.lastname = ['Please enter your lastname.'];
            if (!this.contactInfo) this.errors.contactInfo = ['Please enter your Contact number.'];
            if (!this.email) this.errors.email = ['Please enter your email.'];
            if (!this.age) this.errors.age = ['Please enter your age.'];
            if (!this.sex) this.errors.sex = ['Please enter your sex.'];


            return Object.keys(this.errors).length === 0;


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
                    this.$refs.loader.loading = false;
                    this.VisibleImagePostModal = false;

                    if (this.VisibleImagePostModal == false) {
                        this.openRoomModal = true;
                    }

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
        openRoomDetails(roomId) {
            console.log("Viewing details for room:", roomId);
            // Insert your own logic here (e.g., open a detailed modal or navigate to another view)
        },
        showLessRooms() {
            this.visibleRooms = Math.max(2, this.visibleRooms - 2);
        },
        // Function to proceed with booking/payment
        paymentMethod(roomId) {
            console.log("Booking room:", roomId);
            // Insert your own logic here (e.g., redirect to payment or booking form)
        },
        async openRoomDetails(roomId) {
            this.selectedRoomId = roomId;
            this.$refs.loader.loading = true;

            try {
                const response = await axios.get(`/view-room-details/${this.selectedRoomId}`);
                if (response.data.status === 'success') {
                    this.roomsDetail = response.data.room;
                    this.$refs.loader.loading = false;
                    this.openRoomDetailsModal = true;

                }


            }
            catch (error) {
                if (error.response) {
                    this.$refs.loader.loading = false;
                } else {
                    this.$refs.loader.loading = false;
                }
            }
        },
        CloseRoomDetails() {
            this.selectedRoomId = '';
            this.openRoomDetailsModal = false;

        },

        async paymentMethod(roomId) {
            this.selectedRoomId = roomId;
            this.$refs.loader.loading = true;

            try {
                const response = await axios.get(`/view-room-details/${this.selectedRoomId}`);
                if (response.data.status === 'success') {
                    this.roomsDetail = response.data.room;
                    this.openPaymentModel = true;
                    this.$refs.loader.loading = false;

                    this.openRoomDetailsModal = false;
                }
            }
            catch (error) {
                if (error.response) {
                    console.error('Error fetching room data:', error.response.data);
                    this.$refs.loader.loading = false;

                } else {
                    console.error('Error fetching room data:', error.message);
                    this.$refs.loader.loading = false;

                }
            }
        },
        async ClosepaymentMethod() {
            this.selectedRoomId = '';
            this.openPaymentModel = false;
        },
        async bookaroom() {
            try {
                const confirmed = await this.$refs.modal.show({
                    title: 'Booking Confirmation',
                    message: 'Confirm Your Booking',
                    functionName: 'Confirm Booking'
                });

                if (!confirmed) {
                    return;
                }
                this.$refs.loader.loading = true;
                const formData = new FormData();
                formData.append('dormitory_id', this.dormitory_id);
                formData.append('landlord_id', this.landlord_id);
                formData.append('firstname', this.firstname);
                formData.append('lastname', this.lastname);
                formData.append('contactInfo', this.contactInfo);
                formData.append('age', this.age);
                formData.append('sex', this.sex);
                formData.append('email', this.email);
                formData.append('room_id', this.selectedRoomId);
                formData.append('tenant_picture', this.idPictureFile);
                formData.append('payment_type', this.payment_type);
                formData.append('PaymentPictureFile', this.PaymentPictureFile);
                console.log([...formData.entries()]);



                const response = await axios.post('/book-room', formData, {
                    withCredentials: true,
                    headers:
                    {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (response.data.status === 'success') {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.$refs.loader.loading = false;
                    this.ClosepaymentMethod();
                    this.fillForm();
                    this.openRoomModal = false;

                }
            }
            catch (error) {
                if (error.response) {
                    if (error.response.status === 400) {
                        // Laravel validation errors are usually in error.response.data.errors
                        console.log("Validation errors:", error.response.data.errors);
                        this.$refs.loader.loading = false;

                    } else if (error.response.status === 500) {
                        console.log("Server error:", error.response.data.message);

                    } else {
                        console.error("Unexpected error:", error);
                        this.$refs.loader.loading = false;

                    }
                } else {
                    console.error("Unexpected error:", error);
                    alert("Something went wrong.");
                    this.$refs.loader.loading = false;
                }
            }

        },
        handleidPictre(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.idPicturePreview) {
                    URL.revokeObjectURL(this.idPicturePreview);
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
        paymentTypeSelection(name) {
            this.payment_type = name;
            console.log('Selected payment type:', name);
        },
        handlePaymentPictre(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.PaymentPicturePreview) {
                    URL.revokeObjectURL(this.PaymentPicturePreview);
                }
                this.PaymentPictureFile = file;

                this.PaymentPicturePreview = URL.createObjectURL(file);
            }
        },

        triggerPaymentImage() {
            if (this.$refs.PaymentPicturesInput) {
                this.$refs.PaymentPicturesInput.click();
            }
        },

        removePaymentPicture() {
            if (this.PaymentPicturePreview) {
                URL.revokeObjectURL(this.PaymentPicturePreview);
            }
            this.PaymentPicturePreview = null;
            // Add null check for safety
            if (this.$refs.PaymentPicturePreview) {
                this.$refs.PaymentPicturePreview.value = ''; // Reset file input
            }
        },
        paymentTypeSelection(name) {
            this.payment_type = name;
            console.log('Selected payment type:', name);
        },




    },
    mounted() {
        this.dormitory_id = document.getElementById('RoomDetails').dataset.dormId;
        this.displayDorms();

    },
    watch: {

    },
    computed: {
        displayedAmenities() {
            return this.showMore ? this.amenities : this.amenities.slice(0, 3);
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
