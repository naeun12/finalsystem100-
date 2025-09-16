<template>
    <Loader ref="loader" />
    <NotificationList ref="toastRef" />

    <div class="p-4 mt-2">
        <div style="border:2px solid #4edce2;" class="input-group mb-2 w-100 shadow-sm rounded-pill overflow-hidden">
            <span class="input-group-text bg-white border-0">
                <i class="bi bi-search text-primary"></i>
            </span>
            <input type="text" class="form-control border-0 shadow-none" placeholder="Search Tenants name"
                aria-label="Search Tenant " v-model="searchTerm" />
        </div>
        <div class="py-3 d-flex gap-3 align-items-center">
            <!-- Dorm No Dropdown -->
            <div class="mb-2 d-flex align-items-center gap-2">
                <div class="w-100">
                    <select id="dormSelect" style="border:2px solid #4edce2;"
                        class="form-select form-select-sm rounded-3  shadow-sm" @change="filterDorms"
                        v-model="selectedDormId">
                        <option value="" disabled> Select Dorm</option>
                        <option value="all"> All dorms</option>
                        <option v-for="dorm in dorms" :key="dorm.dormID" :value="dorm.dormID">
                            {{ dorm.dormName }} (ID: {{ dorm.dormID }})
                        </option>
                    </select>
                </div>
            </div>
            <!-- Room No Dropdown -->
            <div class="mb-2 d-flex align-items-center gap-2">
                <div class="w-100">

                    <select id="dormSelect" style="border:2px solid #4edce2;"
                        class="form-select form-select-sm rounded-3 shadow-sm" v-model="selectedroomNumber"
                        @change="filterroomNumber">
                        <option value="" disabled>Select Room Number</option>
                        <option value="all">All rooms</option>
                        <option v-for="room in uniqueRooms" :key="room.fkroomID" :value="room.room?.roomNumber">
                            Room {{ room.room?.roomNumber }}
                        </option>
                    </select>

                </div>
            </div>
            <div class="mb-2 d-flex align-items-center gap-2">
                <div class="w-100">

                    <select id="dormSelect" style="border:2px solid #4edce2;"
                        class="form-select w-100 form-select-sm rounded-3 shadow-sm" @change="filterApplicationStatus"
                        v-model="selectedapplicationStatus">
                        <option value="" disabled> Select Application Status</option>
                        <option value="all"> All Application Status</option>
                        <option value="pending"> Pending</option>
                        <option value="confirmed"> Confirmed</option>
                        <option value="rejected"> Rejected</option>
                        <option value="paid"> Paid</option>
                        <option value="approved"> Approved</option>
                        <option value="cancelled"> Cancelled</option>
                        <option value="expired"> Expired</option>

                    </select>


                </div>
            </div>
        </div>
        <div v-if="!reservations.length" class="d-flex flex-column justify-content-center align-items-center"
            style="height: 200px;">
            <i class="bi bi-emoji-frown mb-2" style="font-size: 2rem; color: #6c757d;"></i>
            <p class="text-muted fw-bold">No Reservation found.</p>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 rounded-3 g-4" style="">

            <div class="col" v-for="reservation in reservations" :key="reservation.reservationID">
                <div class="card shadow-sm rounded-4 border-2 h-100" style="border:2px solid #4edce2;">
                    <div class="d-flex justify-content-between align-items-start card-header rounded-3 bg-info">
                        <h5 class="fw-bold text-dark mb-0">Reservation #{{ reservation.reservationID }}</h5>
                        <button class="btn btn-outline-dark btn-sm px-3"
                            @click="deleteReservation(reservation.reservationID)" title="Delete Reservation">
                            <i class="bi bi-x me-1"></i>
                        </button>
                    </div>
                    <div class="card-body  rounded-4">
                        <!-- Header -->

                        <!-- Body Content -->
                        <ul class="list-unstyled text-secondary small">

                            <li class="mb-2">
                                <i class="bi bi-person-fill me-2 text-dark"></i>
                                <strong>Name:</strong> {{ reservation.firstname }} {{ reservation.lastname }}
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-envelope-fill me-2 text-dark"></i>
                                <strong>Email:</strong> {{ reservation.contactEmail }}
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-building-fill me-2 text-dark"></i>
                                <strong>Dorm:</strong> {{ reservation.room?.dorm?.dormName ?? 'N/A' }}
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-info-circle-fill me-2 text-dark"></i>
                                <strong>Status: </strong>
                                <StatusBadge :status="reservation.status" />

                            </li>
                            <li class="mb-2">
                                <i class="bi bi-door-open-fill me-2 text-dark"></i>
                                <strong>Room:</strong> {{ reservation.room?.roomNumber ?? 'N/A' }}
                            </li>
                        </ul>

                        <!-- Actions -->
                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button class="btn btn-outline-primary btn-sm w-100 px-3"
                                @click="displayReservationInformation(reservation.reservationID)"
                                title="View Reservation">
                                <i class="bi bi-eye-fill me-1"></i>
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="lastPage > 1" class="d-flex justify-content-center align-items-center my-4">
            <nav aria-label="Pagination">
                <ul class="pagination shadow-sm rounded-pill overflow-hidden mb-0">
                    <!-- Previous Button -->
                    <li :class="['page-item', { disabled: currentPage === 1 }]">
                        <button class="page-link border-0 px-4 py-2 text-primary fw-semibold bg-white"
                            :disabled="currentPage === 1" @click="handlePagination(currentPage - 1)"
                            aria-label="Previous" style="transition: 0.2s;">
                            &laquo; Prev
                        </button>
                    </li>

                    <!-- Page Status Display -->
                    <li class="page-item disabled d-flex align-items-center px-3 bg-light">
                        <span class="text-dark small">
                            Page <strong>{{ currentPage }}</strong> of <strong>{{ lastPage }}</strong>
                        </span>
                    </li>

                    <!-- Next Button -->
                    <li :class="['page-item', { disabled: currentPage === lastPage }]">
                        <button class="page-link border-0 px-4 py-2 text-primary fw-semibold bg-white"
                            :disabled="currentPage === lastPage" @click="handlePagination(currentPage + 1)"
                            aria-label="Next" style="transition: 0.2s;">
                            Next &raquo;
                        </button>
                    </li>
                </ul>
            </nav>
        </div>

        <div v-if="VisibleModalApproval" class="modal fade show d-block radius-3"
            style="background: rgba(0, 0, 0, 0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered ">
                <div class="modal-content shadow-lg rounded-4 border-0 overflow-hidden">
                    <!-- Header -->
                    <div class="modal-header bg-info  text-white border-bottom-0 py-3">
                        <h5 class="modal-title fw-bold">
                            🧾 Reservation Information
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                            @click="VisibleModalApproval = false"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body px-5">
                        <div v-if="selectedReservation.status === 'expired'"
                            class="overlay-message d-flex justify-content-center align-items-center">
                            <span>This reservation is expired</span>
                        </div>

                        <!-- Profile Picture and Status -->
                        <div class="text-center mb-4">
                            <img :src="selectedReservation.pictureID"
                                class="rounded-circle border border-3 border-light shadow-sm"
                                style="width: 130px; height: 130px; object-fit: cover;" />
                            <p class="mt-3">
                                <StatusBadge :status="selectedReservation.status" />

                            </p>
                        </div>

                        <!-- Reservation Information -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <p><strong>👤 First Name:</strong> {{ selectedReservation.firstname }}</p>
                                <p><strong>🎂 Age:</strong> {{ selectedReservation.age }}</p>
                                <p><strong>📧 Email:</strong> {{ selectedReservation.contactEmail }}</p>
                                <p><strong>📍 Address:</strong> {{ selectedReservation.room?.dorm?.address }}</p>
                                <p><strong>🚪 Room #:</strong> {{ selectedReservation.room?.roomNumber }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>👤 Last Name:</strong> {{ selectedReservation.lastname }}</p>
                                <p><strong>🚻 Gender:</strong> {{ selectedReservation.gender }}</p>
                                <p><strong>📱 Contact:</strong> {{ selectedReservation.contactNumber }}</p>
                                <p><strong>🏠 Dorm:</strong> {{ selectedReservation.room?.dorm?.dormName || 'N/A' }}</p>
                                <p><strong>💰 Price:</strong> ₱{{
                                    Number(selectedReservation.room?.price).toLocaleString(undefined, {
                                    minimumFractionDigits: 2
                                    })
                                    }}</p>
                            </div>
                        </div>
                        <div class="mb-2 mt-2">
                            <select class="form-select" v-model="status">
                                <option value="" disabled selected>Select Action</option>
                                <option value="pending"
                                    v-if="['cancelled', 'rejected'].includes(selectedReservation.status)">
                                    Pending - Awaiting Confirmation
                                </option>

                                <option value="confirmed"
                                    v-if="['cancelled', 'rejected', 'pending'].includes(selectedReservation.status)">
                                    Confirmed - Tenant To Pay</option>
                                <option value="approved"
                                    v-if="['cancelled', 'rejected', 'paid'].includes(selectedReservation.status)">
                                    Approved
                                    - Reservation Approved</option>
                                <option value="rejected">Rejected - User Initiated</option>
                            </select>
                        </div>
                        <!-- Additional Notes -->
                        <!-- Current Occupant Info -->
                        <div class="mt-2">
                            <h6 class="text-center fw-bold mb-3">👥 Current Occupant Details</h6>
                            <div class="row g-3">
                                <!-- First Column: Tenant Info -->
                                <div class="col-md-6">
                                    <label><strong>👤 Firstname:</strong>
                                        {{ selectedReservation.room?.current_tenant?.firstname }}
                                    </label>

                                </div>

                                <!-- Second Column: Booking Dates -->
                                <div class="col-md-6">
                                    <label><strong>👤 Lastname:</strong>
                                        {{ selectedReservation.room?.current_tenant?.lastname }}
                                    </label>
                                </div>
                                <div class="d-flex">
                                    <div class="col-md-6">
                                        <label><strong>👤 Email:</strong>
                                            {{ selectedReservation.room?.current_tenant?.contactEmail }}
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="ms-2"><strong>📱 Contact:</strong>
                                            {{ selectedReservation.room?.current_tenant?.contactNumber }}
                                        </label>
                                    </div>


                                </div>

                                <label>
                                    <strong>📅 Current Tenant End of Lease: </strong>
                                    {{ formatDate(selectedReservation.room?.current_tenant?.moveOutDate) }}
                                </label>


                            </div>

                        </div>
                        <ReservationStatusAlert :status="selectedReservation.status" role="landlord" class="mt-4" />
                        <div v-if="selectedReservation.status === 'paid' || selectedReservation.status === 'approved'">
                            <label class="mb-2"> <strong>📅 Reservation Start of Lease: </strong>{{
                                formatDate(selectedReservation.moveInDate) }}</label>

                            <p class="text-success fw-bold mt-3" v-if="selectedReservation.status === 'paid'">
                                ✅ Reservation has been paid. You can now accept or reject this reservation.
                            </p>
                            <div class="d-flex justify-content-center mt-3">
                                <img :src="selectedReservation.payment.paymentImage" alt="Payment Receipt"
                                    class="img-fluid rounded border shadow-sm"
                                    style="max-width: 400px; max-height: 400px; object-fit: contain; cursor: pointer;"
                                    @click="viewPaymentImage" />
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer justify-content-between bg-light border-top-0 px-4 py-3">
                        <button class="btn btn-outline-primary px-4" @click="messagePage(selectedReservation)">
                            💬 Message Tenant
                        </button>

                        <div class="d-flex gap-2">
                            <div>
                                <button class="btn btn-outline-success" @click="acceptReservation(selectedReservation)">
                                    ✅ Update Reservation
                                </button>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div v-if="paymentImageModal" class="modal d-block" style="background-color: rgba(0, 0, 0, 0.8);">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header border-0">
                        <h5 class="modal-title">Payment Receipt</h5>
                        <button type="button" class="btn-close btn-close-white" @click="paymentImageModal = false"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex justify-content-center align-items-center p-0">
                        <img :src="selectedReservation.payment?.paymentImage" class="img-fluid"
                            style="max-height: 100vh; max-width: 100vw; object-fit: contain; transition: transform 0.3s ease;"
                            @mouseover="$event.target.style.transform = 'scale(2)'"
                            @mouseleave="$event.target.style.transform = 'scale(1)'" />
                    </div>
                </div>
            </div>
        </div>


    </div>
    <Modalconfirmation ref="modal" />
    <Toastcomponents ref="toast" />

</template>
<script>
import axios from 'axios';
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
import { debounce } from 'lodash';
import NotificationList from '@/components/notifications.vue';
import StatusBadge from '@/components/statusmap.vue';
import ReservationStatusAlert from '@/components/ReservationStatusAlert.vue';


export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation,
        NotificationList,
        StatusBadge,
        ReservationStatusAlert,


    },
    props: {
        reservation: Object,
    },
    data() {
        return {
            allReservations: [],
            searchTerm: '',
            filterMode: '',
            dorms: [],
            reservations: [],
            selectedReservation: '',
            selectedDormId: '',
            selectedapplicationStatus: '',
            selectedroomNumber: '',
            VisibleModalApproval: false,
            currentPage: 1,
            lastPage: 1,
            notifications: [],
            receiverID: '',
            landlord_id: '',
            paymentImageModal: false,
            hasSubscribed: false,
            status: '',
        }
    },
    methods: {
        subscribeToNotifications() {
            if (this.hasSubscribed) return;
            this.hasSubscribed = true;

            this.receiverID = this.landlord_id;
            Echo.private(`notifications.${this.receiverID}`)
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
        async searcReservation(page = 1) {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get('/search-reservation', {
                    params: {
                        search: this.searchTerm,
                        page: page
                    }
                });

                if (response.data.status === 'success') {
                    this.$refs.loader.loading = false;
                    this.selectedDormId = '';
                    this.selectedapplicationStatus = '';
                    this.selectedroomNumber = '';
                    this.reservations = response.data.reservation.data;
                    this.lastPage = response.data.reservation.last_page;
                } else {
                    this.$refs.toast.showToast(response.data.message, 'danger');
                }
            } catch (error) {
                console.error('Error searching bookings:', error);
                this.$refs.toast.showToast('Search failed.', 'danger');
            }
        },
        async reservationList(page = 1) {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get(`/reservation-list?page=${this.currentPage}`);

                if (response.data.status === 'success') {
                    this.$refs.loader.loading = false;
                    this.reservations = response.data.reservation.data;
                    this.lastPage = response.data.reservation.last_page;

                } else {
                    console.warn('Error:', response.data.message);
                }
            } catch (error) {
                console.error('Fetch failed:', error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        fetchallRooms() {
            axios.get('/get/allroomNumbers')
                .then(response => {
                    this.allReservations = response.data;
                })
                .catch(err => console.error('Error fetching all rooms:', err));
        },
        filterroomNumber() {
            this.$refs.loader.loading = true;
            if (this.selectedroomNumber === 'all') {
                this.reservationList();
                this.$refs.loader.loading = false;
                return;
            }

            axios.get(`/api/roomnumber/reservation`, {
                params: {
                    roomsNumber: this.selectedroomNumber,
                }
            })
                .then(response => {
                    this.$refs.loader.loading = false;
                    this.searchTerm = '';
                    this.selectedDormId = '';
                    this.selectedapplicationStatus = '';
                    if (response.data.data) {
                        this.reservations = response.data.data;
                        this.lastPage = response.data.last_page;
                    } else {
                        this.reservations = response.data;
                    }

                })
                .catch(error => {
                    this.$refs.loader.loading = false;
                    console.error("Error fetching room data:", error);
                });
        },
        displaydorms() {
            axios.get('/api/dorms')
                .then(response => {
                    this.dorms = response.data;
                });
        },
        dormId(dorm) {
            this.selectedDorm = dorm;
        },
        filterDorms(page = 1) {
            this.$refs.loader.loading = true;

            if (this.selectedDormId === 'all') {
                this.reservationList();
                this.$refs.loader.loading = false;
                return;
            }
            axios.get(`/api/dorms/${this.selectedDormId}/reservation`, {
                params: { page }
            })
                .then(response => {
                    this.$refs.loader.loading = false;
                    this.searchTerm = '';
                    this.selectedapplicationStatus = '';
                    this.selectedroomNumber = '';
                    this.reservations = response.data.data;
                    this.lastPage = response.data.last_page ?? 1;
                })
                .catch(error => {
                    this.$refs.loader.loading = false;
                    console.error('Error fetching dorm reservations:', error);
                });
        },
        filterApplicationStatus() {
            this.$refs.loader.loading = true;

            if (this.selectedapplicationStatus === 'all') {
                this.reservationList();
                this.$refs.loader.loading = false;
                return;
            }
            axios.get('/api/applications/reservation', {
                params: {
                    selectedapplicationStatus: this.selectedapplicationStatus,
                }
            })
                .then(response => {
                    this.$refs.loader.loading = false;
                    this.searchTerm = '';
                    this.selectedDormId = '';
                    this.selectedroomNumber = '';
                    this.reservations = response.data.data ?? response.data;
                    this.lastPage = response.data.last_page ?? 1;

                })
                .catch(error => {
                    this.$refs.loader.loading = false;
                    console.error("Error filtering applications:", error);
                });
        },
        async displayReservationInformation(reservationID) {
            this.$refs.loader.loading = true;
            try {

                const response = await axios.get(`/view-reservation/reservation/${reservationID}`);
                if (response.data.status === 'success') {
                    this.selectedReservation = response.data.reservation;
                    this.VisibleModalApproval = true;
                }

            } catch (error) {
                console.error('Error fetching reservation details:', error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },

        async acceptReservation(selectedReservation) {
            const formData = new FormData();
            formData.append('reservationID', selectedReservation.reservationID);
            formData.append('email', selectedReservation.contactEmail);
            formData.append('roomNumber', selectedReservation.room?.roomNumber);
            formData.append('firstname', selectedReservation.firstname);
            formData.append('lastname', selectedReservation.lastname);
            formData.append('dorm', selectedReservation.room?.dorm?.dormName);
            formData.append('landlordID', this.landlord_id);
            formData.append('status', this.status);
            console.log(this.status);
            this.$refs.loader.loading = true;

            try {
                const response = await axios.post('/accept-reservation', formData);
                if (response.data.status === 'success') {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.VisibleModalApproval = false; // Close modal

                    this.reservationList();
                    this.status = '';
                } else {
                    this.$refs.toast.showToast(response.data.message || 'Failed to accept.', 'danger');
                }
            } catch (error) {
                console.error('Accept reservation error:', error);
                this.$refs.toast.showToast('An error occurred.', 'danger');
            } finally {
                this.$refs.loader.loading = false;

            }
        },

        async deleteReservation(reservationID) {

            const confirmed = await this.$refs.modal.show({
                title: 'Delete reservation',
                message: `Confirm delete to this reservation information?`,
                functionName: 'Delete reservation',
            });
            if (!confirmed) {
                return;
            }
            this.$refs.loader.loading = true;

            try {
                const response = await axios.delete(`/delete-reservation/reservation/${reservationID}`);

                if (response.data.status === 'success') {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.reservationList();
                } else {
                    alert('Error: ' + response.data.message);
                }
            } catch (error) {
                console.error('Delete error:', error);
                alert('Something went wrong while deleting.');
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        viewPaymentImage() {
            this.paymentImageModal = true;
        },

        handlePagination(page) {

            if (page < 1 || page > this.lastPage) return;
            this.currentPage = page;
            if (this.selectedapplicationStatus && this.selectedapplicationStatus !== 'all') {
                this.filterApplicationStatus(page);
            } else if (this.selectedDormId && this.selectedDormId !== 'all') {
                this.filterDorms(page);
            } else if (this.selectedroomNumber && this.selectedroomNumber !== 'all') {
                this.filterroomNumber(page);
            } else if (this.searchTerm && this.searchTerm.trim() !== '') {
                this.searcReservation(page);
            } else {
                this.reservationList(page); // Default
            }
        },
        messagePage(selectedReservation) {
            const tenantID = selectedReservation.tenant?.tenantID;

            if (!tenantID) {
                alert('No tenant assigned to this reservation.');
                return;
            }

            const url = `/api/select/landlord/conversations/${this.landlord_id}?tenant_id=${tenantID}`;
            window.location.href = url;
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
        const el = document.getElementById('reservationPage');
        if (el) {
            this.landlord_id = el.getAttribute('data-landlord-id');
        }


        this.subscribeToNotifications();
        this.reservationList();
        this.displaydorms();
        this.fetchallRooms();      // ADD THIS to get all room numbers


    },
    computed:
    {
        uniqueRooms() {
            if (!Array.isArray(this.allReservations)) return [];

            const seen = new Set();
            return this.allReservations.filter(reservation => {
                const roomNumber = reservation.room?.roomNumber;
                if (!roomNumber || seen.has(roomNumber)) return false;
                seen.add(roomNumber);
                return true;
            });
        },


    },
    watch: {
        searchTerm: {
            handler: debounce(function (newVal) {
                if (newVal.trim() !== '') {
                    this.searcReservation();
                } else {
                    this.reservationList();
                }
            }, 300),
            immediate: false
        },
        selectedDormId(newVal) {
            if (newVal !== '') {
                this.selectedapplicationStatus = '';
                this.selectedroomNumber = '';
                this.searchTerm = '';
                this.filterMode = 'dorm';
                this.handlePagination(1);
            }
        },
        selectedapplicationStatus(newVal) {
            if (newVal !== '') {
                this.selectedDormId = '';
                this.selectedroomNumber = '';
                this.searchTerm = '';
                this.filterMode = 'status';
                this.handlePagination(1);
            }
        },
        selectedroomNumber(newVal) {
            if (newVal !== '') {
                this.selectedDormId = '';
                this.selectedapplicationStatus = '';
                this.searchTerm = '';
                this.filterMode = 'room';
                this.handlePagination(1);
            }
        },
      
    }
}
</script>
<style>
.overlay-message {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    /* dark semi-transparent overlay */
    color: #fff;
    font-size: 1.5rem;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    z-index: 10;
    border-radius: 12px;
    /* kung gusto rounded ang modal body */
    text-align: center;
    pointer-events: none;
    /* para dili clickable ang ubos */
}

.overlay-message span {
    background: rgba(255, 0, 0, 0.8);
    padding: 10px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}
</style>