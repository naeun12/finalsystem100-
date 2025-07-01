<template>
    <div class="container-fluid py-3 m-0">
        <h2 class="text-center mb-4 fw-bold text-primary">
            <i class="bi bi-door-open-fill me-2"></i>Available Rooms
        </h2>
        <div class="card flex-md-row shadow-lg rounded-4 overflow-hidden p-3 m-2" style="height: 220px;"
            v-for="(room, index) in rooms" :key="room.room_id">
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
                        <button type="button" class="btn btn-sm px-3" @click="bookRoom(room.room_id)">
                            <i class="bi bi-calendar-check me-1"></i>Book
                        </button>
                    </div>
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
                                        <i class="bi bi-people-fill me-2 text-primary"></i>Capacity:
                                    </label>
                                    <div class="p-2 border rounded bg-light text-break">
                                        {{ roomsDetail?.capacity || 'N/A' }} tenant(s)
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
    </div>
</template>
<script>
import axios from 'axios'
export default {
    data() {
        return {
            dormitory_id: '',
            rooms: [],
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
                selectedRoomId: this.selectedRoomId
            };
            localStorage.setItem('tenantInfo', JSON.stringify(data));

        },
        async availableRooms() {
            try {
                const response = await axios.get(`/available-room/${this.dormitory_id}`);
                this.rooms = response.data.rooms;
            }
            catch (error) {

            }
        },
        async openRoomDetails(roomId) {
            this.selectedRoomId = roomId;
            console.log('sd', this.selectedRoomId);
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
        CloseRoomDetails() {
            this.selectedRoomId = '';
            this.openRoomDetailsModal = false;

        },
        bookRoom(roomId) {
            this.room_id = roomId;

            this.getInformationData(); // ✅ Call before redirect
            window.location.href = `/booking-process/${this.room_id}/${this.tenant_id}`;
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
        }
        const element = document.getElementById('roomSelection');
        this.dormitory_id = element.dataset.dormId;
        this.tenant_id = element.dataset.tenantId;
        this.availableRooms();
    },
}
</script>