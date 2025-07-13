<template>
    <Loader ref="loader" />

    <div class="p-4 mt-4">
        <div class="input-group mb-2 w-100 shadow-sm rounded-pill overflow-hidden">
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

                    <select id="dormSelect" class="form-select form-select-lg shadow-sm" @change="filterDorms"
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

                    <select id="dormSelect" class="form-select form-select-lg shadow-sm" v-model="selectedroomNumber"
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

                    <select id="dormSelect" class="form-select form-select-lg shadow-sm"
                        @change="filterApplicationStatus" v-model="selectedapplicationStatus">
                        <option value="" disabled> Select Application Status</option>
                        <option value="all"> All Application Status</option>
                        <option value="pending"> Pending</option>
                        <option value="for-confirmation"> For confirmation</option>
                        <option value="rejected"> Ejected application</option>

                    </select>


                </div>
            </div>
        </div>

        <div class="table-responsive shadow-sm rounded p-3 bg-white">
            <!-- Table -->
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-primary bg-primary text-center">
                    <tr>
                        <th>Reservation ID</th>
                        <th>Tenant Name</th>
                        <th>Email</th>
                        <th>Dorm Name</th>
                        <th>Room No</th>
                        <th>Reservation Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr v-for="reservation in reservations" :key="reservation.reservationID">
                        <td>{{ reservation.reservationID }}</td>
                        <td>{{ reservation.firstname }} {{ reservation.lastname }}</td>
                        <td>{{ reservation.contactEmail }}</td>
                        <td>{{ reservation.room?.dorm?.dormName ?? 'N/A' }}</td>
                        <td>{{ reservation.room?.roomNumber ?? 'N/A' }}</td>
                        <td>
                            <span class="badge px-2 py-1" :class="{
                                'bg-success text-white': reservation.status === 'for-confirmation',
                                'bg-warning text-white': reservation.status === 'pending',
                                'bg-danger text-white': reservation.status === 'rejected',
                                'bg-secondawary text-white': !['Approved', 'Pending', 'rejected'].includes(reservation.status)
                            }">
                                {{ reservation.status }}
                            </span>
                        </td>

                        <td>
                            <button class="btn btn-sm btn-primary"
                                @click="displayReservationInformation(reservation.reservationID)">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-danger ms-2"
                                @click="deleteReservation(reservation.reservationID)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="lastPage > 1" class="d-flex justify-content-center align-items-center my-3">
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0">
                    <li :class="['page-item', { disabled: currentPage === 1 }]">
                        <button class="page-link" :disabled="currentPage === 1"
                            @click="handlePagination(currentPage - 1)" aria-label="Previous">
                            <span aria-hidden="true">&laquo; Prev</span>
                        </button>
                    </li>

                    <li class="page-item disabled">
                        <span class="page-link">
                            Page {{ currentPage }} of {{ lastPage }}
                        </span>
                    </li>

                    <li :class="['page-item', { disabled: currentPage === lastPage }]">
                        <button class="page-link" :disabled="currentPage === lastPage"
                            @click="handlePagination(currentPage + 1)">
                            <span aria-hidden="true">Next &raquo;</span>
                        </button>
                    </li>

                </ul>
            </nav>
        </div>
        <div v-if="VisibleModalApproval" class="modal fade show d-block" style="background: rgba(0, 0, 0, 0.5);"
            tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4 border-0">
                    <!-- Header -->
                    <div class="modal-header bg-white border-bottom-0">
                        <h5 class="modal-title text-primary fw-bold">
                            🧾 Reservation Information
                        </h5>
                        <button type="button" class="btn-close" @click="VisibleModalApproval = false"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body px-5">
                        <!-- Profile Picture and Status -->
                        <div class="text-center mb-4">
                            <img :src="selectedReservation.studentpictureID"
                                class="rounded-circle border border-3 border-light shadow-sm"
                                style="width: 130px; height: 130px; object-fit: cover;" />
                            <p class="mt-3">
                                <span class="badge rounded-pill px-3 py-2 fs-6" :class="{
                                    'bg-success text-white': selectedReservation.status === 'for-confirmation',
                                    'bg-danger text-white': selectedReservation.status === 'rejected',
                                    'bg-warning text-dark': selectedReservation.status !== 'for-confirmation' && selectedReservation.status !== 'Not Approved'
                                }">{{ selectedReservation.status }}</span>
                            </p>
                        </div>

                        <!-- Information Grid -->
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
                                <p><strong>🔑 Room ID:</strong> {{ selectedReservation.room?.roomID }}</p>

                                <p><strong>💰 Price:</strong> ₱{{
                                    Number(selectedReservation.room?.price).toLocaleString(undefined, {
                                        minimumFractionDigits: 2
                                    }) }}</p>

                            </div>
                        </div>
                        <div class="mt-4 text-center" v-if="selectedReservation.status === 'for-confirmation'">
                            <p>
                                <strong>💳⏳🗓️ The tenant is currently finalizing their payment and selecting a move-in
                                    date to begin their stay.</strong>
                            </p>
                        </div>
                        <!-- Current Occupant Information -->
                        <div class="mt-4 text-center">
                            <p><strong>👥 Current Occupant Details:</strong></p>
                        </div>
                        <div class="row mt-4 g-3">
                            <div class="col-md-6">
                                <p><strong>👥 Current Occupant:</strong>
                                    {{ selectedReservation.room?.current_tenant?.firstname }} {{
                                        selectedReservation.room?.current_tenant?.lastname }}
                                </p>
                                <p><strong>📅 Start of Occupancy:</strong>
                                    {{ selectedReservation.room?.current_tenant?.moveInDate || 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>🔄 Will Extend Stay?:</strong>
                                    <!-- This assumes you will handle this value in your backend later -->
                                    Yes
                                </p>
                                <p><strong>📅 End of Occupancy:</strong>
                                    {{ selectedReservation.room?.current_tenant?.moveOutDate || 'N/A' }}
                                </p>
                            </div>
                        </div>





                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between bg-light border-top-0 px-4 py-3">
                        <button class="btn btn-outline-primary px-4">
                            💬 Message Tenant
                        </button>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success px-4" @click="acceptReservation(selectedReservation)"
                                :disabled="selectedReservation.status === 'for-confirmation'">
                                ✅ Accept Reservation
                            </button>
                            <button class="btn btn-danger px-4" @click="rejectReservation(selectedReservation)"
                                :disabled="selectedReservation.status === 'rejected'">
                                ❌ Reject Reservation
                            </button>
                        </div>
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
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation
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
            lastPage: 1
        }
    },
    methods: {
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

            this.$refs.loader.loading = true;

            try {
                const response = await axios.post('/accept-reservation', formData);
                if (response.data.status === 'success') {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.reservationList(); // Refresh table
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
        async rejectReservation(selectedReservation) {
            const formData = new FormData();
            formData.append('reservationID', selectedReservation.reservationID);
            formData.append('email', selectedReservation.contactEmail);
            formData.append('roomNumber', selectedReservation.room?.roomNumber);
            formData.append('firstname', selectedReservation.firstname);
            formData.append('lastname', selectedReservation.lastname);
            formData.append('dorm', selectedReservation.room?.dorm?.dormName);

            this.$refs.loader.loading = true;

            try {
                const response = await axios.post('/eject-reservation', formData);
                if (response.data.status === 'success') {
                    this.$refs.toast.showToast(response.data.message, 'success');
                    this.reservationList(); // Refresh table
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
        }

    },
    mounted() {
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
        }

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
        }
    }
}
</script>