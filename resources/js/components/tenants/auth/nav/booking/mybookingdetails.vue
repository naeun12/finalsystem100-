<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <Modalconfirmation ref="modal" />
    <NotificationList ref="toastRef" />

    <div class="container-fluid  p-3">
        <div class="row shadow rounded overflow-hidden " style="border: 3px solid #4edce2;">
            <!-- Left Section -->
            <div class="col-md-4 text-black text-center py-4" style="background-color: #4edce2;">
                <!-- Image Container -->
                <div class="card-img-top d-flex align-items-center justify-content-center"
                    style="height: 200px; overflow: hidden;">
                    <img v-if="booking.pictureID" :src="booking.pictureID" alt="Profile Image" class="rounded"
                        style="max-height: 100%; max-width: 100%; object-fit: cover;">
                    <p v-else class="text-muted">No Image</p>
                </div>
                <statusMap :status="this.ispayment" role="tenant" />


                <!-- Booking Details -->
                <div class="text-start px-3">
                    <p><strong><i class="bi bi-person-fill"></i>Full name:</strong> {{ booking.firstname }} {{
                        booking.lastname }}</p>
                    <p><strong><i class="bi bi-calendar-fill"></i> Age:</strong> {{ booking.age }}</p>
                    <p><strong><i class="bi bi-gender-ambiguous"></i> Gender:</strong> {{ booking.gender }}</p>
                    <p><strong><i class="bi bi-telephone-fill"></i> Contact No#:</strong> {{ booking.contactNumber }}
                    </p>
                    <p><strong><i class="bi bi-envelope-fill"></i> Email:</strong> {{ booking.contactEmail }}</p>
                    <p><strong><i class="bi bi-house-door-fill"></i> Move-in Date:</strong> {{ booking.moveInDate }}</p>
                    <p><strong><i class="bi bi-flag-fill"></i> Move-out Date:</strong> {{ booking.moveOutDate }}</p>

                </div>

                <!-- Action Button -->
                <div v-if="booking.status === 'confirmed' || booking.status === 'pending'" class="mt-3">
                    <button class="btn btn-danger" @click="cancelBooking(booking.bookingID)">
                        <i class="bi bi-x-circle-fill me-2"></i>Cancel Booking
                    </button>
                </div>
            </div>



            <!-- Right Section -->
            <div class="col-md-8 bg-light p-4">
                <div class="border mb-4 rounded bg-white d-flex align-items-center justify-content-center overflow-hidden"
                    style="height: 300px;">
                    <img :src="booking.room?.roomImages" alt="Dormitory Image"
                        class="w-100 h-100 object-fit-cover rounded" v-if="booking.room?.roomImages" />
                    <p class="mb-0 text-muted" v-else>No Image</p>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="bg-white p-3 border rounded shadow-sm h-100">
                            <strong>🏢 Dormitory Name:</strong> {{ booking.room?.dorm.dormName }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-white p-3 border rounded shadow-sm h-100">
                            <strong>📍 Address:</strong> {{ booking.room?.dorm.address }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-white p-3 border rounded shadow-sm h-100">
                            <strong>🛏️ Occupancy Type:</strong> {{ booking.room?.dorm.occupancyType }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-white p-3 border rounded shadow-sm h-100">
                            <strong>✅ Availability:</strong> {{ booking.room?.dorm.availability }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-white p-3 border rounded shadow-sm h-100">
                            <strong>🔢 Room No#:</strong> {{ booking.room?.roomNumber }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-white p-3 border rounded shadow-sm h-100">
                            <strong>💵 Monthly Rate:</strong> ₱{{ booking.room?.price }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-white p-3 border rounded shadow-sm h-100">
                            <strong>🛏️ Room Type:</strong> {{ booking.room?.roomType }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-white p-3 border rounded shadow-sm h-100">
                            <strong>🚻 Gender Preference:</strong> {{ booking.room?.genderPreference }}
                        </div>
                    </div>
                </div>
                <div class="mb-2 mt-4">
                    <BookingStatus :status="this.ispayment" role="tenant" />
                </div>



                <div v-if="ispayment === 'confirmed'">
                    <!-- Payment method (fixed to GCash) -->
                    <div class="container py-4 mb-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">
                                <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>Payment Method
                            </label>
                            <input type="text" class="form-control form-control-lg shadow-sm" v-model="payment_type"
                                value="GCash" readonly />
                            <div class="form-text text-muted">
                                Payments are only available via GCash.
                            </div>
                        </div>

                        <!-- GCash Card -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body rounded-4 p-4"
                                style="border:2px solid #4edce2; background: linear-gradient(135deg,#e8fafa,#ffffff);">

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold text-primary mb-0">
                                        <i class="bi bi-wallet2 me-2"></i>GCash Number
                                    </h6>

                                </div>

                                <p class="fs-5 fw-bold text-dark mb-1">{{ booking.room?.dorm.gcashNumber }}</p>
                                <small class="text-muted">Send payment to this number and upload your receipt
                                    below.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Proof of Payment -->
                    <div class="border border-secondary rounded-3 p-4 mb-3 text-center" style="cursor: pointer;"
                        v-if="isPaymentImage" @click="triggerPaymentImage">
                        <input ref="PaymentPicturesInput" class="d-none" type="file" accept="image/*"
                            @change="handlePaymentPicture" />
                        <div class="d-flex flex-column align-items-center text-center mb-3">
                            <img :src="paymentIcon" alt="Payment Icon" style="max-width: 60px; height: auto;"
                                class="mb-2" />
                            <h5 class="text-secondary mt-2">Upload GCash Payment Receipt</h5>
                            <small class="text-muted">Click to browse and select an image file</small>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div v-if="PaymentPicturePreview" class="text-center mb-3">
                        <img :src="PaymentPicturePreview" alt="Uploaded Payment Image" class="img-fluid rounded mb-2"
                            style="max-height: 250px;" />
                        <div>
                            <button type="button" @click="removePaymentPicture" class="btn btn-danger shadow-sm">
                                Remove Uploaded Image
                            </button>
                        </div>
                    </div>
                    <div class="justify-content-center d-flex mb-2">
                        <span v-if="errors.payment_image" class="text-danger small mt-1 d-block">
                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.payment_image[0] }}
                        </span>
                    </div>

                    <!-- Pay Button -->
                    <button type="submit" class="btn btn-success w-100 py-2 fw-semibold shadow-sm"
                        @click="submitPayment">
                        <i class="bi bi-check-circle-fill me-2"></i>Confirm GCash Payment
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import axios from 'axios';
import Toastcomponents from '@/components/Toastcomponents.vue';
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
import BookingStatus from '@/components/BookingStatusAlert.vue';
import statusMap from '@/components/statusmap.vue';
import NotificationList from '@/components/notifications.vue';

export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation,
        BookingStatus,
        statusMap,
        NotificationList,
    },
    data() {
        return {
            booking: [],
            ispayment: '',
            paymentIcon: '/images/tenant/allimagesResouces/paymentIcon.jpg',
            id_picture: '/images/tenant/allimagesResouces/vector-id-card-icon.jpg',
            errors: [],
            paymentIcon: '/images/tenant/allimagesResouces/paymentIcon.jpg',
            PaymentPicturePreview: '',
            PaymentPictureFile: null,
            isPaymentImage: true,
            bookingID: '',
            notifications: [],
            receiverID: '',
            payment_type: 'online',
            tenantid: '',

        };
    },
    methods: {
        getBookingDetails() {
            this.$refs.loader.loading = true;

            axios.get(`/my-bookings/details/${this.bookingID}`)
                .then(response => {
                    if (response.data.length > 0) {
                        this.booking = response.data[0];
                        this.ispayment = this.booking.status;
                        this.$refs.loader.loading = false;

                    }
                })
                .catch(error => {
                    console.error('Error fetching booking details:', error);
                    this.$refs.loader.loading = false;

                });
        },
        async cancelBooking(bookingID) {
            try {
                const confirmed = await this.$refs.modal.show({
                    title: `Cancel Booking`,
                    message: `Are you sure you want to Cancelled this booking?`,
                    functionName: 'Confirm Cancelled Booking'
                });

                if (!confirmed) {
                    return;
                }
                const response = await axios.get(`/cancel/booking/${bookingID}`);
                this.$refs.toast.showToast(response.data.message, 'success');

                this.getBookingDetails();
            } catch (error) {
                console.error(error);
                alert('Failed to cancel reservation.');
            }
        },

        handlePaymentPicture(event) {
            const file = event.target.files[0];
            if (file) {
                // Create object URL and revoke previous one if exists
                if (this.PaymentPicturePreview) {
                    URL.revokeObjectURL(this.PaymentPicturePreview);
                }
                this.PaymentPictureFile = file;
                this.isPaymentImage = false;

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
            this.isPaymentImage = true;
        },
        subscribeToNotifications() {
            if (this.hasSubscribed) return;
            this.hasSubscribed = true;

            this.receiverID = this.tenantid;
            Echo.private(`notifications.${this.tenantid}`)
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
        async submitPayment() {
            this.errors = {};
            const confirmed = await this.$refs.modal.show({
                title: `Confirm Payment`,
                message: `Are you sure you want to proceed with the payment for this room?`,
                functionName: 'Confirm Payment'
            });

            if (!confirmed) {
                return;
            }

            this.$refs.loader.loading = true;
            const formData = new FormData();
            formData.append('fkbookingID', this.bookingID);
            formData.append('paymentType', this.payment_type);
            formData.append('amount', this.booking.room?.price || 0);
            if (this.PaymentPictureFile) {
                formData.append('paymentImage', this.PaymentPictureFile);
            }
            try {
                const response = await axios.post('/tenant/pay-room', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                this.PaymentPicturePreview = '';
                this.PaymentPictureFile = null;
                this.payment_type = '';
                this.$refs.loader.loading = false;
                this.isPaymentImage = true;
                this.$refs.toast.showToast(response.data.message, 'success');
                this.getBookingDetails();

            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors || {};

                } else if (error.response && error.response.data.message) {
                    alert(error.response.data.message);
                } else {
                    alert('Something went wrong. Please try again.');
                }
            }
            finally {
                this.$refs.loader.loading = false;

            }

        },

    },

    mounted() {
        const el = document.getElementById('viewBookingDetails');
        this.bookingID = el.getAttribute('booking_id')?.trim();
        this.tenantid = el.getAttribute('tenant_id')?.trim();

        this.getBookingDetails();
        this.subscribeToNotifications();

    },
};
</script>

<style scoped>
.border {
    border: 1px solid #dee2e6 !important;
}
</style>
