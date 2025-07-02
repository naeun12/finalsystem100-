<template>
    <Loader ref="loader" />
    <Modalconfirmation ref="modal" />

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
            <select class="form-select w-auto" id="roomFilter" v-model="selectPriceRange"
                @change="filterByPriceRange($event)">
                <option value="" disabled>Select Price Range</option>
                <option value="all">All Price</option>
                <option value="0-100">0-100</option>
                <option value="101-300">101-300</option>
                <option value="301-99999">300+</option>
            </select>
            <select class="form-select w-auto" id="genderFilter" v-model="selectedGender"
                @change="filterByGender($event)">
                <option value="" disabled>Select Gender Preferences</option>
                <option value="all">All Gender</option>
                <option value="Male Only">Male</option>
                <option value="Female Only">Female</option>
                <option value="Any Gender">Any Gender</option>
            </select>


        </div>
        <div class="card flex-md-row shadow-lg rounded-4 overflow-hidden p-3 m-2" style="height: 220px;"
            v-for="(room, index) in visibleRooms" :key="room.room_id">
            <!-- Image Section -->
            <div class="dorm-image" style="width: 35%; min-width: 200px;">
                <img :src="room.room_images" :alt="`Image of ${room.dorm?.dorm_name || 'Dorm'}`"
                    style="width: 100%; height: 100%; object-fit: cover;" />
            </div>

            <!-- Details Section -->
            <div class="p-3 d-flex flex-column justify-content-between w-100">
                <!-- Title -->
                <div class="text-black px-3 py-1">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-house-door-fill me-2 text-primary"></i>{{
                            room.listing_type || 'Available Dorm' }}
                    </h5>
                </div>

                <!-- Tags -->
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="badge bg-light border text-dark">
                        <i class="bi bi-aspect-ratio me-1 text-secondary"></i>
                        {{ room.area_sqm || 'N/A' }} sqm
                    </span>
                    <span class="badge bg-light border text-dark">
                        <i class="bi bi-gender-ambiguous me-1 text-secondary"></i>
                        {{ room.gender_preference || 'N/A' }}
                    </span>
                    <span class="badge bg-light border text-dark">
                        <i class="bi bi-lamp-fill me-1 text-secondary"></i> {{
                            room.furnishing_status || 'N/A' }}
                    </span>
                </div>

                <!-- Price -->
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                    <span class="fw-bold text-success fs-5">
                        <i class="bi bi-cash-coin me-1"></i> ₱{{
                            Number(room.price).toLocaleString() || 'N/A' }} / Head
                    </span>
                </div>


                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-3 mb-2 flex-wrap">
                    <a @click="openRoomDetails(room.room_id)"
                        class="text-primary text-decoration-none px-4 py-2 fw-semibold" style="cursor: pointer;">
                        <i class="bi bi-eye me-1"></i>View Details
                    </a>
                    <div class="d-flex gap-2 mt-2 mt-md-0">
                        <div v-if="chooseStatus === 'Occupied'">
                            <button type="button" class="button-costumize btn-sm px-3" @click="reserveRoom(room)">
                                <i class="bi bi-calendar-check me-1"></i>Reserve this room
                            </button>
                        </div>
                        <div v-if="chooseStatus === 'Available'">
                            <button type="button" class="button-costumize btn-sm px-3" @click="bookRoom(room)">
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
                    <div class="modal-header  text-white">
                        <h5 class="modal-title">Room Details</h5>
                        <button type="button" class="btn-close" @click="CloseRoomDetails()"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-center mb-3">
                            <img :src="roomsDetail?.room_images" class="img-fluid rounded shadow-sm w-100"
                                style="max-height: 300px; object-fit: cover;"
                                :alt="`Image of ${roomsDetail?.room_type || 'Room'}`" />
                        </div>



                        <div class="row g-4 ">

                            <div class="col-md-6">
                                <!-- Room Number -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-hash me-2 text-primary"></i>Room Number:
                                    </label>
                                    <div class="p-2 border rounded bg-light text-break">
                                        {{ roomsDetail?.room_number || 'N/A' }}
                                    </div>
                                </div>

                                <!-- Room Type -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-door-open-fill me-2 text-primary"></i>Room Type:
                                    </label>
                                    <div class="p-2 border rounded bg-light text-break">
                                        {{ roomsDetail?.room_type || 'N/A' }}
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-cash-coin me-2 text-primary"></i>Price:
                                    </label>
                                    <div class="p-2 border rounded bg-light text-break">
                                        ₱{{ Number(roomsDetail?.price).toLocaleString() || '0' }}
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <!-- Availability -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-clipboard-check me-2 text-primary"></i>Availability:
                                    </label>
                                    <div class="p-2 border rounded bg-light">
                                        <span class="badge px-3 py-2 fs-6" :class="roomsDetail?.availability === 'Available' ? 'bg-success' :
                                            roomsDetail?.availability === 'Occupied' ? 'bg-danger' :
                                                'bg-warning text-dark'">
                                            {{ roomsDetail?.availability || 'Unknown' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Capacity -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-people-fill me-2 text-primary"></i>Tenant Name:
                                    </label>
                                    <div class="p-2 border rounded bg-light text-break">
                                        Lance Monsanto
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-calendar-event-fill me-2 text-primary"></i>Lease Expiration
                                        Date:
                                    </label>
                                    <div class="p-2 border rounded bg-light text-break">
                                        June 23, 2025
                                    </div>
                                </div>

                            </div>



                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="button-costumize  px-4" @click="CloseRoomDetails()">OK</button>
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
import Modalconfirmation from '@/components/modalconfirmation.vue';
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation
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
            selectedRoomId: ''
        }
    },
    methods: {
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
                selectedRoomId: this.selectedRoomId
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

                }
            }
            catch (error) {

            }
        },
        async reserveRoom(room) {

            alert('sadas');
            console.log('sadas', this.imageUrl);
            this.room_id = room.room_id;
            try {
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
                    alert('success');
                }

            } catch (error) {
                console.error('Reservation error:', error);

            }
        },
        CloseRoomDetails() {
            this.selectedRoomId = '';
            this.openRoomDetailsModal = false;

        },
        async bookRoom(room) {
            const confirmed = await this.$refs.modal.show({
                title: `Are you sure you want to choose Room #${room.room_number || room.room_id}?`,
                message: `This will reserve Room #${room.room_number || room.room_id} for you.`,
                functionName: 'Confirm Room Selection'
            });

            if (!confirmed) {
                return;
            }
            this.room_id = room.room_id;

            this.getInformationData(); // ✅ Call before redirect
            window.location.href = `/booking-process/${this.room_id}/${this.tenant_id}`;
        },
        toggleShowMore() {
            this.showAll = !this.showAll;
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
    },
    computed: {
        visibleRooms() {
            return this.showAll ? this.rooms : this.rooms.slice(0, this.roomsToShow);
        },
    }
}
</script>