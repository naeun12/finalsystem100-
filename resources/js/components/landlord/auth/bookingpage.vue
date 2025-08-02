<template>
    <Loader ref="loader" />
    <NotificationList ref="toastRef" />

    <div class="p-4 mt-4">
        <div class="input-group mb-4 w-100 shadow-sm rounded-pill overflow-hidden">
            <span class="input-group-text bg-white border-0">
                <i class="bi bi-search text-primary"></i>
            </span>
            <input type="text" class="form-control border-0 shadow-none" placeholder="Search Tenants name"
                aria-label="Search Tenant " v-model="searchTerm" />
        </div>
        <div class="row g-3 py-3">
            <!-- Dorm Dropdown -->
            <div class="col-12 col-md-4">
                <select class="form-select form-select-lg shadow-sm rounded-3" v-model="selectedDormId"
                    @change="filterDorms">
                    <option disabled value="">Select Dorm</option>
                    <option value="all">All Dorms</option>
                    <option v-for="dorm in dorms" :key="dorm.dormID" :value="dorm.dormID">
                        {{ dorm.dormName }} (ID: {{ dorm.dormID }})
                    </option>
                </select>
            </div>

            <!-- Room Dropdown -->
            <div class="col-12 col-md-4">
                <select class="form-select form-select-lg shadow-sm rounded-3" v-model="selectedroomNumber"
                    @change="filterroomNumber">
                    <option disabled value="">Select Room Number</option>
                    <option value="all">All Rooms</option>
                    <option v-for="room in uniqueRooms" :key="room.fkroomID" :value="room.room?.roomNumber">
                        Room {{ room.room?.roomNumber }}
                    </option>
                </select>
            </div>

            <!-- Application Status Dropdown -->
            <div class="col-12 col-md-4">
                <select class="form-select form-select-lg shadow-sm rounded-3" v-model="selectedapplicationStatus"
                    @change="filterApplicationStatus">
                    <option disabled value="">Select Application Status</option>
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="not approved">Not Approved</option>
                    <option value="approved">Approved</option>
                </select>
            </div>
        </div>


        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-3">
            <div class="col" v-for="booking in tenants" :key="booking.bookingID">
                <div class="card shadow-sm rounded-4 h-100 w-100 border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="fw-bold text-primary mb-0">Booking #{{ booking.bookingID }}</h5>
                            <span class="badge text-wrap text-center fw-semibold fs-6" :class="{
                                'bg-success text-white': booking.status === 'Fully Approved',
                                'bg-warning text-dark': booking.status === 'pending',
                                'bg-danger text-white': booking.status === 'Not Approved',
                                'bg-info text-dark': booking.status === 'Accepted by Landlord',
                                'bg-secondary text-white': booking.status === 'Pending Payment Confirmation'
                            }" style="min-width: 150px; max-width: 100%; white-space: normal;">
                                {{ booking.status }}
                            </span>

                        </div>

                        <ul class="list-unstyled text-secondary">
                            <li class="mb-2"><i class="bi bi-person-fill me-2 text-dark"></i><strong>Name:</strong> {{
                                booking.firstname }} {{ booking.lastname }}</li>
                            <li class="mb-2"><i class="bi bi-envelope-fill me-2 text-dark"></i><strong>Email:</strong>
                                {{ booking.contactEmail }}</li>
                            <li class="mb-2"><i class="bi bi-building me-2 text-dark"></i><strong>Dorm:</strong> {{
                                booking.room?.dorm?.dormName ?? 'N/A' }}</li>
                            <li class="mb-2"><i class="bi bi-door-open-fill me-2 text-dark"></i><strong>Room:</strong>
                                {{ booking.room?.roomNumber ?? 'N/A' }}</li>
                            <li class="mb-2"><i class="bi bi-calendar-check-fill me-2 text-dark"></i><strong>Move-In
                                    Date:</strong> {{ booking.moveInDate }}</li>
                            <li class="mb-2">
                                <i class="bi bi-credit-card-fill me-2 text-dark"></i>
                                <strong>Payments:</strong>
                                <span v-if="booking.payment.length">
                                    <span v-for="(pay, index) in booking.payment" :key="index">
                                        {{ pay.paymentType }}<span v-if="index !== booking.payment.length - 1">, </span>
                                    </span>
                                </span>
                                <span v-else>No payment yet</span>
                            </li>

                        </ul>

                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button class="btn btn-outline-primary btn-sm px-3" @click="openTenant(booking.bookingID)">
                                <i class="bi bi-eye-fill me-1"></i> View
                            </button>
                            <button class="btn btn-outline-danger btn-sm px-3"
                                @click="deleteBooking(booking.bookingID)">
                                <i class="bi bi-trash-fill me-1"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="lastPage > 1" class="d-flex justify-content-center my-4">
            <nav>
                <ul class="pagination pagination-sm shadow-sm">
                    <li :class="['page-item', { disabled: currentPage === 1 }]">
                        <button class="page-link" @click="handlePagination(currentPage - 1)"
                            :disabled="currentPage === 1">
                            &laquo; Prev
                        </button>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">Page {{ currentPage }} of {{ lastPage }}</span>
                    </li>
                    <li :class="['page-item', { disabled: currentPage === lastPage }]">
                        <button class="page-link" @click="handlePagination(currentPage + 1)"
                            :disabled="currentPage === lastPage">
                            Next &raquo;
                        </button>
                    </li>
                </ul>
            </nav>
        </div>

        <!--Modal Tenant Appoval-->
        <!-- Use v-if to render the modal only if needed -->
        <div v-if="VisibleModalApproval" class="modal fade show d-block" style="background: rgba(0, 0, 0, 0.5);"
            tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg rounded-4 border-0">
                    <!-- Header -->
                    <div class="modal-header bg-white border-bottom-0">
                        <h5 class="modal-title text-primary fw-bold">
                            🧾 Tenant Profile
                        </h5>
                        <button type="button" class="btn-close" @click="VisibleModalApproval = false"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body px-5">
                        <!-- Profile Picture and Status -->
                        <div class="text-center mb-4">
                            <img :src="selectedtenant.studentpictureID"
                                class="rounded-circle border border-3 border-light shadow-sm"
                                style="width: 130px; height: 130px; object-fit: cover;" />
                            <p class="mt-3">
                                <span class="badge rounded-pill px-3 py-2 fs-6" :class="{
                                    'bg-success text-white': selectedtenant.status === 'Approved',
                                    'bg-danger text-white': selectedtenant.status === 'Not Approved',
                                    'bg-warning text-dark': selectedtenant.status !== 'Approved' && selectedtenant.status !== 'Not Approved'
                                }">{{ selectedtenant.status }}</span>
                            </p>
                        </div>

                        <!-- Information Grid -->
                        <div class="row g-4">
                            <div class="col-md-6">
                                <p><strong>👤 First Name:</strong> {{ selectedtenant.firstname }}</p>
                                <p><strong>🎂 Age:</strong> {{ selectedtenant.age }}</p>
                                <p><strong>📧 Email:</strong> {{ selectedtenant.contactEmail }}</p>
                                <p><strong>📍 Address:</strong> {{ selectedtenant.room?.dorm?.address }}</p>
                                <p><strong>🚪 Room #:</strong> {{ selectedtenant.room?.roomNumber }}</p>
                                <p><strong>🚪 Move in date #:</strong> {{ selectedtenant.moveInDate }}</p>

                            </div>
                            <div class="col-md-6">
                                <p><strong>👤 Last Name:</strong> {{ selectedtenant.lastname }}</p>
                                <p><strong>🚻 Gender:</strong> {{ selectedtenant.gender }}</p>
                                <p><strong>📱 Contact:</strong> {{ selectedtenant.contactNumber }}</p>
                                <p><strong>🏠 Dorm:</strong> {{ selectedtenant.room?.dorm?.dormName || 'N/A' }}</p>
                                <p><strong>💰 Price:</strong> ₱{{
                                    Number(selectedtenant.room?.price).toLocaleString(undefined, {
                                        minimumFractionDigits: 2
                                    }) }}</p>
                                <p><strong>🚪 Move out date #:</strong> {{ selectedtenant?.moveOutDate }}</p>

                            </div>
                        </div>

                        <!-- Payment Image -->
                        <div class="text-center mt-4" v-if="selectedtenant.payment.length">
                            <p><strong>💳 Payment Type:</strong> {{ selectedtenant.payment[0].paymentType }}</p>
                            <img :src="selectedtenant.payment[0].paymentImage"
                                class="img-thumbnail rounded shadow-sm mt-2"
                                style="width: 220px; height: 220px; object-fit: cover; cursor: pointer;"
                                @click="showFullImage = true" />
                        </div>
                        <div v-else class="text-center mt-4">
                            <p><strong>💳 Payment Type:</strong> No payment yet</p>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer justify-content-between bg-light border-top-0 px-4 py-3">
                        <button class="btn btn-outline-primary px-4" @click="messagePage(selectedtenant.fktenantID)">
                            💬 Message Tenant
                        </button>
                        <div v-if="selectedtenant.status === 'Pending Payment Confirmation'">
                            <button class="btn btn-success px-4" @click="confirmApplication(selectedtenant.bookingID)"
                                :disabled="selectedtenant.status === 'Approved'">
                                ✅ Confirm Application
                            </button>
                        </div>

                        <div class="d-flex gap-2"
                            v-if="selectedtenant.status === 'Approved' || selectedtenant.status === 'Not Approved' || selectedtenant.status === 'pending'">
                            <button class="btn btn-success px-4" @click="approveTenant(selectedtenant.bookingID)">
                                ✅ Approve
                            </button>
                            <button class="btn btn-danger px-4" @click="notapproveTenant(selectedtenant.bookingID)">
                                ❌ Not Approve
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade show" tabindex="-1" style="display: block;" v-if="showFullImage"
        @click.self="showFullImage = false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content shadow-lg rounded-4 overflow-hidden">
                <div class="modal-body p-0 position-relative bg-dark">
                    <img :src="selectedtenant.paymentImage" class="img-fluid w-100"
                        style="object-fit: contain; max-height: 90vh;" />
                    <button type="button"
                        class="btn-close position-absolute top-0 end-0 m-3 bg-white p-2 rounded-circle"
                        aria-label="Close" @click="showFullImage = false"></button>
                </div>
                <div class="modal-footer justify-content-center bg-light border-top">
                    <button type="button" class="btn btn-outline-dark px-4" @click="showFullImage = false">
                        Close
                    </button>
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

import { toHandlers } from 'vue';
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation,
        NotificationList
    },
    data() {
        return {
            landlord_id: null,
            VisibleModalApproval: false,
            tenants: [],
            filterMode: '',
            dorms: [],
            searchTerm: '',
            selectedDormId: '',
            selectedapplicationStatus: '',
            selectedroomNumber: '',
            selectedtenant: [],
            selectedBookingID: '',
            showFullImage: false,
            tenant: {},
            filteredTenants: [],
            currentPage: 1, // 👈 ADD THIS
            lastPage: 1,
            notifications: [],
            receiverID: '',
            hasSubscribed: false,
        };
    },

    methods: {
        subscribeToNotifications() {
            if (this.hasSubscribed) return; // prevent multiple subscriptions
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
        async searchBooking(page = 1) {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get('/search-booking', {
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
                    this.tenants = response.data.booking.data;
                    this.lastPage = response.data.booking.last_page;
                } else {
                    this.$refs.toast.showToast(response.data.message, 'danger');
                }
            } catch (error) {
                console.error('Error searching bookings:', error);
                this.$refs.toast.showToast('Search failed.', 'danger');
            }
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
                this.bookingList();
                this.$refs.loader.loading = false;
                return;
            }

            axios.get(`/api/dorms/${this.selectedDormId}/tenants`, {
                params: { page }
            })
                .then(response => {
                    this.$refs.loader.loading = false;
                    this.searchTerm = '';
                    this.selectedapplicationStatus = '';
                    this.selectedroomNumber = '';

                    this.tenants = response.data.data ?? [];
                    this.lastPage = response.data.last_page ?? 1;
                })
                .catch(error => {
                    this.$refs.loader.loading = false;
                    console.error('Error fetching dorm tenants:', error);
                });
        },

        filterApplicationStatus(page = 1) {
            this.$refs.loader.loading = true;

            if (this.selectedapplicationStatus === 'all') {
                this.bookingList();
                this.$refs.loader.loading = false;
                return;
            }

            axios.get('/api/applications/booking', {
                params: {
                    selectedapplicationStatus: this.selectedapplicationStatus,
                    page: page
                }
            })
                .then(response => {
                    this.$refs.loader.loading = false;
                    this.searchTerm = '';
                    this.selectedDormId = '';
                    this.selectedroomNumber = '';
                    this.tenants = response.data.data;
                    this.lastPage = response.data.last_page;

                })
                .catch(error => {
                    this.$refs.loader.loading = false;
                    console.error("Error filtering applications:", error);
                });
        },


        filterroomNumber(page = 1) {
            this.$refs.loader.loading = true;

            if (this.selectedroomNumber === 'all') {
                this.bookingList();
                this.$refs.loader.loading = false;
                return;
            }

            axios.get(`/api/roomnumber/booking`, {
                params: {
                    roomsNumber: this.selectedroomNumber,
                    page: page,
                }
            })
                .then(response => {
                    this.$refs.loader.loading = false;
                    this.searchTerm = '';
                    this.selectedDormId = '';
                    this.selectedapplicationStatus = '';
                    if (response.data.data) {
                        this.tenants = response.data.data;
                        this.lastPage = response.data.last_page;
                    } else {
                        this.tenants = response.data;
                    }

                })
                .catch(error => {
                    this.$refs.loader.loading = false;
                    console.error("Error fetching room data:", error);
                });
        },

        async bookingList(page = 1) {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get(`/booking-list?page=${this.currentPage}`);

                if (response.data.status === 'success') {
                    this.tenants = response.data.booking.data;
                    this.lastPage = response.data.booking.last_page;

                } else {
                    this.$refs.toast.showToast(response.data.message, 'danger');
                }
            } catch (error) {
                console.error('Error fetching bookings:', error);
                this.$refs.toast.showToast("Failed to fetch bookings", 'danger');
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async openTenant(booking_id) {
            this.selectedBookingID = booking_id;
            this.$refs.loader.loading = true;

            try {
                const response = await axios.get(`/booking-tenant-view/${this.selectedBookingID}`);
                this.selectedtenant = response.data.tenant;
                console.log(this.selectedtenant);
                this.VisibleModalApproval = true;
                this.$refs.loader.loading = false;
            }
            catch (error) {
                console.error('Error fetching tenant data:', error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async approveTenant(booking_id) {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.post('/approve-tenant', {
                    bookingID: booking_id
                });
                // Success: show modal or success message
                this.$refs.toast.showToast(response.data.message, 'success');
                this.selectedtenant = response.data.tenant;
                this.errors = {};
                this.VisibleModalApproval = false;
                this.$refs.loader.loading = false;
                this.bookingList();

            } catch (error) {
                this.$refs.loader.loading = false;

                if (error.response && error.response.status === 403) {

                    this.$refs.toast.showToast(error.response.data.message, 'danger'); // ✅ Show danger toast
                } else {
                    console.error('Error approving tenant:', error);
                    this.$refs.toast.showToast('Something went wrong while approving the tenant.', 'danger');
                }
            } finally {
                this.$refs.loader.loading = false;
                this.VisibleModalApproval = false;

            }
        },
        async confirmApplication(booking_id) {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.post('/accept-tenant', {
                    bookingID: booking_id
                });
                this.$refs.toast.showToast(response.data.message, 'success');
                this.selectedtenant = response.data.tenant;
                this.errors = {};
                this.VisibleModalApproval = false;
                this.$refs.loader.loading = false;
                this.bookingList();

            } catch (error) {
                this.$refs.loader.loading = false;

                if (error.response && error.response.status === 403) {

                    this.$refs.toast.showToast(error.response.data.message, 'danger'); // ✅ Show danger toast
                } else {
                    console.error('Error approving tenant:', error);
                    this.$refs.toast.showToast('Something went wrong while approving the tenant.', 'danger');
                }
            } finally {
                this.$refs.loader.loading = false;
                this.VisibleModalApproval = false;

            }
        },

        async notapproveTenant(booking_id) {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.post('/not-approve-tenant', {
                    bookingID: booking_id  // ✅ CamelCase
                });
                this.selectedtenant = response.data.tenant;
                this.VisibleModalApproval = false;
                this.$refs.loader.loading = false;
                this.bookingList();

            } catch (error) {
                this.$refs.loader.loading = false;
                console.error('Error approving tenant:', error.response?.data || error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        async deleteBooking(booking_id) {
            try {
                // Confirm user action
                const confirmed = await this.$refs.modal.show({
                    title: 'Delete Booking',
                    message: `Confirm delete to this Book’s information?`,
                    functionName: 'Delete Booking',
                });

                if (!confirmed) {
                    return;
                }

                // Show loader
                this.$refs.loader.loading = true;

                const response = await axios.delete(`/delete-booking/${booking_id}`);

                // ✅ Show success toast
                this.$refs.toast.showToast(response.data.message, 'success');

                // Optional: Refresh list or remove from UI
                this.bookingList();

            } catch (error) {
                const errorMsg = error.response?.data?.message || 'An error occurred while deleting.';
                this.$refs.toast.showToast(errorMsg, 'danger');
                console.error('Delete error:', error);
            } finally {
                this.$refs.loader.loading = false;
            }
        },

        handlePagination(page) {

            if (page < 1 || page > this.lastPage) return;
            this.currentPage = page;


            // 👇 Priority check (only one filter is expected at a time)
            if (this.selectedapplicationStatus && this.selectedapplicationStatus !== 'all') {
                this.filterApplicationStatus(page);
            } else if (this.selectedDormId && this.selectedDormId !== 'all') {
                this.filterDorms(page);
            } else if (this.selectedroomNumber && this.selectedroomNumber !== 'all') {
                this.filterroomNumber(page);
            } else if (this.searchTerm && this.searchTerm.trim() !== '') {
                this.searchBooking(page);
            } else {
                this.bookingList(page); // Default
            }
        },
        messagePage(fktenantID) {
            const url = `/api/select/landlord/conversations/${this.landlord_id}?tenant_id=${fktenantID}`;
            window.location.href = url;
        },




    },
    mounted() {
        const el = document.getElementById('BookingManagement');
        if (el) {
            this.landlord_id = el.getAttribute('landlord-id');
        }
        this.subscribeToNotifications();
        this.bookingList();
        this.displaydorms();

    },
    computed:
    {
        uniqueRooms() {
            const seen = new Set();
            return this.tenants.filter(tenant => {
                if (seen.has(tenant.fkroomID)) return false;
                seen.add(tenant.fkroomID);
                return true;
            });
        }
    },

    watch: {
        searchTerm: {
            handler: debounce(function (newVal) {
                if (newVal.trim() !== '') {
                    this.searchBooking();
                } else {
                    this.bookingList();
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



};
</script>
