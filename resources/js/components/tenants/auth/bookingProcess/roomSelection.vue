<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <Modalconfirmation ref="modal" />
    <NotificationList ref="toastRef" />

    <div class="container-fluid py-3 m-0">

        <div class="d-flex align-items-center justify-content-center gap-3 my-4">
            <button :class="['btn', isAvailable, 'fw-bold']" @click="ClickavailableRooms()">
                <i class="bi bi-door-open-fill me-2"></i>Available Rooms
            </button>

            <button :class="['btn', isOccupied, 'fw-bold']" @click="ClickoccupiedRooms()">
                <i class="bi bi-door-open-fill me-2"></i>Occupied Rooms
            </button>
        </div>
        <div class="d-flex justify-content-start my-3 p-2 gap-3">
            <select class="form-select w-auto" id="roomFilter" style="border:1px solid #4edce2 "
                v-model="selectPriceRange" @change="filterByPriceRange($event)">
                <option value="" disabled>Select Price Range</option>
                <option value="all">All Price</option>
                <option value="0-100">0-100</option>
                <option value="101-300">101-300</option>
                <option value="301-99999">300+</option>
            </select>
            <select class="form-select w-auto" id="genderFilter" style="border:1px solid #4edce2 "
                v-model="selectedGender" @change="filterByGender($event)">
                <option value="" disabled>Select Gender Preferences</option>
                <option value="all">All Gender</option>
                <option value="Male Only">Male</option>
                <option value="Female Only">Female</option>
            </select>


        </div>
        <div v-if="!rooms.length" class="text-center p-5 bg-light rounded shadow-sm">
            <i class="bi bi-door-closed text-secondary" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-dark">No Rooms Available</h5>
            <p class="text-muted">Please check back later or explore other dormitories.</p>
        </div>
        <div class="card shadow-lg rounded-4 overflow-hidden p-3 m-2" v-for="(room, index) in visibleRooms"
            :key="room.room_id">
            <div class="row g-3 align-items-center">

                <!-- Image Section -->
                <div class="col-12 col-md-4" style="min-width: 200px;">
                    <img :src="room.roomImages" :alt="`Image of ${room.dorm?.dorm_name || 'Dorm'}`"
                        class="img-fluid rounded" style="object-fit: cover; width: 100%; height: 180px;" />
                </div>

                <!-- Details Section -->
                <div class="col-12 col-md-8 d-flex flex-column justify-content-between">

                    <!-- Title -->
                    <div class="text-black px-2 py-1">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-house-door-fill me-2 text-primary"></i>
                            {{ room.listingType || 'Available Dorm' }}
                        </h5>
                    </div>

                    <!-- Tags -->
                    <div class="d-flex flex-wrap gap-2 mt-2 px-2">
                        <span class="badge bg-light border text-dark">
                            <i class="bi bi-aspect-ratio me-1 text-secondary"></i>
                            {{ room.areaSqm || 'N/A' }} sqm
                        </span>
                        <span class="badge bg-light border text-dark">
                            <i class="bi bi-gender-ambiguous me-1 text-secondary"></i>
                            {{ room.genderPreference || 'N/A' }}
                        </span>
                        <span class="badge bg-light border text-dark">
                            <i class="bi bi-lamp-fill me-1 text-secondary"></i>
                            {{ room.furnishing_status || 'N/A' }}
                        </span>
                    </div>

                    <!-- Price -->
                    <div class="d-flex justify-content-between align-items-center mt-3 px-2 flex-wrap">
                        <span class="fw-bold text-success fs-5">
                            <i class="bi bi-cash-coin me-1"></i> ₱{{ Number(room.price).toLocaleString() || 'N/A' }} /
                            Head
                        </span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-2 flex-wrap">
                        <a @click="openRoomDetails(room.roomID)"
                            class="text-primary text-decoration-none px-3 py-2 fw-semibold" style="cursor: pointer;">
                            <i class="bi bi-eye me-1"></i>View Details
                        </a>
                        <div class="d-flex gap-2 mt-2 mt-md-0">
                            <button v-if="chooseStatus === 'Occupied'" type="button"
                                class="btn btn-outline-primary btn-sm px-3" @click="openReservationModal(room)">
                                <i class="bi bi-calendar-check me-1"></i>Reserve this room
                            </button>
                            <button v-if="chooseStatus === 'Available'" type="button"
                                class="btn btn-primary btn-sm px-3" @click="bookRoom(room)">
                                <i class="bi bi-calendar-check me-1"></i>Book
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3 mt-3" v-if="rooms.length > 3">
            <a href="#" @click.prevent="toggleShowMore">
                {{ showAll ? 'Show Less' : 'Show More' }}
            </a>
        </div>
        <div v-if="openRoomDetailsModal" class="modal fade show d-block w-100" tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4 overflow-hidden">

                    <!-- Modal Header -->
                    <div class="modal-header bg-info  text-white">
                        <h5 class="modal-title">Room Details</h5>
                        <button type="button" class="btn-close" @click="CloseRoomDetails()"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Image Preview -->
                        <div class="text-center mb-4">
                            <img :src="roomsDetail?.roomImages" class="img-fluid rounded-3 shadow w-100"
                                style="max-height: 300px; object-fit: cover;"
                                :alt="`Image of ${roomsDetail?.room_type || 'Room'}`" />
                        </div>

                        <!-- Room Info -->
                        <div class="row g-4">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <div class="bg-light border rounded-3 p-3 shadow-sm mb-3">
                                    <label class="form-label fw-semibold text-secondary">
                                        <i class="bi bi-hash me-2 text-primary"></i> Room Number
                                    </label>
                                    <div class="text-dark fw-medium">{{ roomsDetail?.roomNumber || 'N/A' }}</div>
                                </div>

                                <div class="bg-light border rounded-3 p-3 shadow-sm mb-3">
                                    <label class="form-label fw-semibold text-secondary">
                                        <i class="bi bi-door-open-fill me-2 text-primary"></i> Room Type
                                    </label>
                                    <div class="text-dark fw-medium">{{ roomsDetail?.roomType || 'N/A' }}</div>
                                </div>

                                <div class="bg-light border rounded-3 p-3 shadow-sm mb-3">
                                    <label class="form-label fw-semibold text-secondary">
                                        <i class="bi bi-cash-coin me-2 text-primary"></i> Monthly Rate
                                    </label>
                                    <div class="text-dark fw-medium">₱{{ Number(roomsDetail?.price).toLocaleString() ||
                                        '0' }}</div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <div class="bg-light border rounded-3 p-3 shadow-sm mb-3">
                                    <label class="form-label fw-semibold text-secondary">
                                        <i class="bi bi-clipboard-check me-2 text-primary"></i> Availability
                                    </label>
                                    <div>
                                        <span class="badge px-3 py-2 fs-6" :class="roomsDetail?.availability === 'Available' ? 'bg-success' :
                                            roomsDetail?.availability === 'Occupied' ? 'bg-danger' :
                                                'bg-warning text-dark'">
                                            {{ roomsDetail?.availability || 'Unknown' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="bg-light border rounded-3 p-3 shadow-sm mb-3">
                                    <label class="form-label fw-semibold text-secondary">
                                        <i class="bi bi-people-fill me-2 text-primary"></i>Current Tenant Name
                                    </label>
                                    <div class="text-dark fw-medium">
                                        <template v-if="roomsDetail.latest_approved_tenant">
                                            {{ roomsDetail.latest_approved_tenant.firstname }} {{
                                            roomsDetail.latest_approved_tenant.lastname }}
                                        </template>
                                        <template v-else>
                                            <span class="text-muted fst-italic">No tenant yet</span>
                                        </template>
                                    </div>
                                </div>


                                <div class="bg-light border rounded-3 p-3 shadow-sm mb-3">
                                    <label class="form-label fw-semibold text-secondary">
                                        <i class="bi bi-calendar-event-fill me-2 text-primary"></i> Lease Expiration
                                        Date
                                    </label>
                                    <div class="text-dark fw-medium">
                                        {{ formatDate(roomsDetail.latest_approved_tenant?.moveOutDate) }}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="button-costumize  px-4" @click="CloseRoomDetails()">OK</button>
                    </div>
                </div>
                <!-- Modal Footer -->

            </div>
        </div>
    </div>
    <div v-if="reservationDetailsModal" class="modal fade show d-block w-100" tabindex="-1"
        style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-4 overflow-hidden">

                <!-- Modal Header -->
                <div class="modal-header bg-info  text-white">
                    <h5 class="modal-title">Room Details</h5>
                    <button type="button" class="btn-close" @click="reservationDetailsModal = false"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4">
                    <!-- Image Preview -->
                    <div class="text-center mb-4">
                        <img :src="this.imageUrl" class="img-fluid rounded-3 shadow w-50"
                            style="max-height: 300px; "
                            :alt="`Image of ${roomsDetail?.room_type || 'Room'}`" />
                    </div>

                    <!-- Personal Information -->
                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <div class="bg-light border rounded-3 p-3 h-100 shadow-sm">
                                <label class="form-label fw-semibold text-secondary">
                                    <i class="bi bi-person-circle me-2 text-primary"></i> Firstname
                                </label>
                                <div class="text-dark fw-medium">{{ this.firstname || 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light border rounded-3 p-3 h-100 shadow-sm">
                                <label class="form-label fw-semibold text-secondary">
                                    <i class="bi bi-person-circle me-2 text-primary"></i> Lastname
                                </label>
                                <div class="text-dark fw-medium">{{ this.lastname || 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <div class="bg-light border rounded-3 p-3 h-100 shadow-sm">
                                <label class="form-label fw-semibold text-secondary">
                                    <i class="bi bi-envelope me-2 text-primary"></i> Email
                                </label>
                                <div class="text-dark fw-medium text-break">{{ this.email || 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light border rounded-3 p-3 h-100 shadow-sm">
                                <label class="form-label fw-semibold text-secondary">
                                    <i class="bi bi-telephone me-2 text-primary"></i> Contact Number
                                </label>
                                <div class="text-dark fw-medium text-break">{{ this.contactInfo || 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="bg-light border rounded-3 p-3 h-100 shadow-sm">
                                <label class="form-label fw-semibold text-secondary">
                                    <i class="bi bi-calendar-check me-2 text-primary"></i> Age
                                </label>
                                <div class="text-dark fw-medium">{{ this.age || 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light border rounded-3 p-3 h-100 shadow-sm">
                                <label class="form-label fw-semibold text-secondary">
                                    <i class="bi bi-gender-ambiguous me-2 text-primary"></i> Gender
                                </label>
                                <div class="text-dark fw-medium">{{ this.sex || 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer justify-content-center">
                    <button type="button" class="button-costumize  px-4" @click="reserveRoom()"> <i
                            class="bi bi-check-circle-fill"></i>
                        Reserved this room</button>
                </div>
            </div>

        </div>
        <Modalconfirmation ref="modal" />

    </div>

</template>
<script>
import axios from 'axios'
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
import NotificationList from '@/components/notifications.vue';

import { toHandlers } from 'vue';
import { parse } from 'vue/compiler-sfc';
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation,
        NotificationList,
    },
    data() {
        return {
            isAvailable: 'btn-outline-primary',
            chooseStatus: '',
            isOccupied: 'btn-outline-danger',
            selectPriceRange: '',
            selectedGender: '',
            isGender: '',
            dormitory_id: '',
            rooms: [],
            showAll: false,
            roomsToShow: 3,
            room_id: '',
            tenant_id: '',
            openRoomDetailsModal: false,
            reservationDetailsModal: false,
            roomsDetail: '',
            selectedRoomId: '',
            firstname: '',
            lastname: '',
            email: '',
            contactInfo: '',
            age: '',
            sex: '',
            roomNu: '',
            roomType: '',
            price: '',
            idPicturePreview: '',
            imageUrl: null,
            selectedRoomId: '',
            notifications: [],
            receiverID: '',
        }
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
        getInformationData() {
            const data = {
                firstname: this.firstname,
                lastname: this.lastname,
                email: this.email,
                contactInfo: this.contactInfo,
                age: this.age,
                sex: this.sex,
                idPicturePreview: this.idPicturePreview,
                imageUrl: this.imageUrl,
                selectedRoomId: this.selectedRoomId,
            };
            localStorage.setItem('tenantInfo', JSON.stringify(data));

        },
        async availableRooms() {
            try {
                this.$refs.loader.loading = true;
                this.selectPriceRange = '';
                const response = await axios.get(`/available-room/${this.dormitory_id}`);
                this.rooms = response.data.rooms;
                this.isAvailable = 'btn-primary';
                this.isOccupied = 'btn-outline-danger';
                this.chooseStatus = 'Available';
                this.selectedGender = '';

            }
            catch (error) {

            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async ClickavailableRooms() {
            this.availableRooms();
        },

        async ClickoccupiedRooms() {
            try {
                this.$refs.loader.loading = true;

                this.selectPriceRange = '';
                const response = await axios.get(`/occupied-room/${this.dormitory_id}`);
                this.rooms = response.data.rooms;
                this.isAvailable = 'btn-outline-primary';
                this.isOccupied = 'btn-danger';
                this.chooseStatus = 'Occupied';
            }
            catch (error) {
                this.$refs.loader.loading = false;

            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async filterByPriceRange(event) {
            this.$refs.loader.loading = true;

            const range = event.target.value;
            if (range === 'all') {
                if (this.chooseStatus === 'Available') {
                    this.availableRooms();

                }
                else if (this.chooseStatus === 'Occupied') {
                    this.ClickoccupiedRooms();

                }
                return;
            }

            const [min, max] = range.split('-');

            try {
                const response = await axios.get(`/filter-price-range/${this.dormitory_id}?min=${min}&max=${max}&chooseStatus=${this.chooseStatus}`);
                this.rooms = response.data.rooms;
                this.selectedGender = '';

            } catch (error) {
                console.error('Error fetching price-filtered rooms', error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async filterByGender(event) {
            this.$refs.loader.loading = true;

            const gender = event.target.value;

            // If "Any Gender" is selected, reload all rooms by availability
            if (gender === 'all') {
                if (this.chooseStatus === 'Available') {
                    await this.availableRooms();
                } else if (this.chooseStatus === 'Occupied') {
                    await this.ClickoccupiedRooms();
                }
                return;
            }

            // Else fetch rooms filtered by gender and status
            try {
                const response = await axios.get(
                    `/filter-gender/${this.dormitory_id}?gender=${gender}&chooseStatus=${this.chooseStatus}`
                );
                this.rooms = response.data.rooms;
                this.selectPriceRange = '';

            } catch (error) {
                console.error('Error filtering by gender:', error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },

        async openRoomDetails(roomId) {
            this.selectedRoomId = roomId;
            try {
                const response = await axios.get(`/view-room-details/${this.selectedRoomId}`);
                if (response.data.status === 'success') {
                    this.roomsDetail = response.data.room;
                    this.openRoomDetailsModal = true;
                    console.log(this.roomsDetail);

                }
            }
            catch (error) {

            }
        },
        openReservationModal(room) {
            this.room_id = room.roomID;
            this.roomNu = room.roomNumber;
            this.roomsex = room.genderPreference;
            this.dormsec = room.dorm?.occupancyType;
            this.reservationDetailsModal = true;
        },
        async reserveRoom() {
            try {

                const tenantSex = this.sex.toLowerCase(); // "male" / "female"

                // Normalize room preference
                let roomPref = this.roomsex?.toLowerCase();
                if (roomPref === "male only") roomPref = "male";
                if (roomPref === "female only") roomPref = "female";
                if (roomPref === "any gender") roomPref = "any";

                // Normalize occupancy type
                let occupancyType = this.dormsec?.toLowerCase();
                if (occupancyType === "male only") occupancyType = "male";
                if (occupancyType === "female only") occupancyType = "female";
                if (occupancyType === "any gender") occupancyType = "any";

                // ✅ Gender restriction check
                const genderAllowed =
                    roomPref === "any" || tenantSex === roomPref;

                // ✅ Occupancy restriction check
                const occupancyAllowed =
                    !occupancyType || occupancyType === "any" || tenantSex === occupancyType;

                if (!genderAllowed || !occupancyAllowed) {
                    this.$refs.toast.showToast('You are not eligible to reserve this room.', 'warning');
                    this.reservationDetails = false;
                    return;
                }

                // Confirm reservation
                const confirmed = await this.$refs.modal.show({
                    title: `Are you sure you want to reserve Room #${this.roomNu || this.room_id}?`,
                    message: `This will reserve Room #${this.roomNu || this.room_id} for you.`,
                    functionName: 'Confirm Reservation'
                });

                if (!confirmed) return;

                this.$refs.loader.loading = true;

                const formdata = new FormData();
                formdata.append('dormitory_id', this.dormitory_id);
                formdata.append('room_id', this.room_id);
                formdata.append('tenant_id', this.tenant_id);
                formdata.append('firstname', this.firstname);
                formdata.append('lastname', this.lastname);
                formdata.append('contact_number', this.contactInfo);
                formdata.append('email', this.email);
                formdata.append('age', this.age);
                formdata.append('gender', this.sex);
                formdata.append('studentpicture_id', this.imageUrl);
                const response = await axios.post('/reserved-room', formdata);

                if (response.data.status === 'success') {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.reservationDetailsModal = false;
                } else if (response.data.status === 'error') {
                    this.$refs.toast.showToast(response.data.message, 'danger');
                    this.reservationDetailsModal = false;
                }

            } catch (error) {
                console.error('Reservation error:', error);

                this.$refs.toast.showToast(
                    error.response?.data?.message || 'Reservation failed. Please try again later.',
                    'error'
                );
            } finally {
                this.$refs.loader.loading = false;
                this.openRoomDetailsModal = false;

            }
        },


        CloseRoomDetails() {
            this.selectedRoomId = '';
            this.openRoomDetailsModal = false;

        },
        async bookRoom(room) {

            const tenantSex = this.sex.toLowerCase(); // "male" / "female"
            // Convert room values to simpler form
            let roomPref = room.genderPreference?.toLowerCase(); // "male only", "female only", "any gender"
            if (roomPref === "male only") roomPref = "male";
            if (roomPref === "female only") roomPref = "female";
            if (roomPref === "any gender") roomPref = "any";

            let occupancyType = room.dorm?.occupancyType?.toLowerCase();
            if (occupancyType === "male only") occupancyType = "male";
            if (occupancyType === "female only") occupancyType = "female";
            if (occupancyType === "any gender") occupancyType = "any";

            // ✅ Gender eligibility
            const genderAllowed =
                roomPref === "any" || tenantSex === roomPref;

            // ✅ Occupancy eligibility
            const occupancyAllowed =
                !occupancyType || occupancyType === "any" || tenantSex === occupancyType;

            if (genderAllowed && occupancyAllowed) {
                const confirmed = await this.$refs.modal.show({
                    title: `Are you sure you want to choose Room #${room.roomNumber || room.roomID}?`,
                    message: `This will book Room #${room.roomNumber || room.roomID} for you.`,
                    functionName: 'Confirm Room Selection'
                });

                if (!confirmed) return;

                this.room_id = room.roomID;
                this.getInformationData();
                window.location.href = `/booking-process/${this.room_id}/${this.tenant_id}`;
            } else {
                this.$refs.toast.showToast('You are not eligible to book this room.', 'warning');
            }
        },
        toggleShowMore() {
            this.showAll = !this.showAll;
        },
        formatDate(date) {
            if (!date) return "N/A";
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },


    },
    mounted() {
        const data = localStorage.getItem('tenantInfo');
        if (data) {
            const parsed = JSON.parse(data);
            this.firstname = parsed.firstname;
            this.lastname = parsed.lastname;
            this.email = parsed.email;
            this.contactInfo = parsed.contactInfo;
            this.age = parsed.age;
            this.sex = parsed.sex;
            this.idPicturePreview = parsed.idPicturePreview;
            this.imageUrl = parsed.imageUrl;
        }
        const element = document.getElementById('roomSelection');
        this.dormitory_id = element.dataset.dormId;
        this.tenant_id = element.dataset.tenantId;
        this.availableRooms();
        this.subscribeToNotifications();

    },
    computed: {
        visibleRooms() {
            return this.showAll ? this.rooms : this.rooms.slice(0, this.roomsToShow);
        },
    }
}
</script>