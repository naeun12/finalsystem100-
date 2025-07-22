<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <Modalconfirmation ref="modal" />
    <div class="container-fluid py-5">
        <div class="row shadow rounded overflow-hidden">
            <!-- Left Section -->
            <div class="col-md-4 bg-dark text-white text-center py-4">
                <div class="border mb-4 rounded bg-white d-flex align-items-center justify-content-center overflow-hidden"
                    style="height: 200px;">
                    <img :src="booking.studentpictureID" alt="Profile Image"
                        class="img-fluid w-100 h-100 object-fit-cover rounded" v-if="booking.studentpictureID">
                    <p class="mb-0 text-muted" v-else>No Image</p>
                </div>

                <div class="text-start px-3">
                    <p><strong>📌 Booking Status:</strong>
                        <span class="badge px-3 py-2 text-wrap" :class="{
                            'text-success bg-light-success rounded': booking.status === 'Fully Approved',
                            'text-primary bg-light-primary rounded': booking.status === 'Accepted by Landlord',
                            'text-warning bg-light-warning rounded': booking.status === 'Pending Payment Confirmation' || booking.status === 'pending',
                            'text-danger bg-light-danger rounded': booking.status === 'Not Approved'
                        }" style="white-space: normal; min-width: 150px;">
                            {{ booking.status }}
                        </span>


                    </p>
                    <p><strong>👤 Name:</strong> {{ booking.firstname }} {{ booking.lastname }}</p>
                    <p><strong>🎂 Age:</strong> {{ booking.age }}</p>
                    <p><strong>🚻 Gender:</strong> {{ booking.gender }}</p>
                    <p><strong>📞 Contact No#:</strong> {{ booking.contactNumber }}</p>
                    <p><strong>✉️ Email:</strong> {{ booking.contactEmail }}</p>
                    <p><strong>🏠 Move-in Date:</strong> {{ booking.moveInDate }}</p>
                    <p><strong>🏁 Move-out Date:</strong> {{ booking.moveOutDate }}</p>
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
                <div v-if="ispayment === 'Pending Payment Confirmation'"
                    class="alert alert-warning d-flex align-items-center mt-3 shadow-sm rounded" role="alert">
                    <i class="bi bi-hourglass-split me-2 fs-5"></i>
                    <div>
                        <strong>Waiting for Landlord Confirmation</strong><br>
                        Your payment has been submitted and is pending verification.
                    </div>
                </div>
                <div v-if="ispayment === 'Fully Approved'"
                    class="alert alert-success d-flex align-items-start gap-3 mt-3 shadow-sm rounded" role="alert">
                    <i class="bi bi-check-circle-fill fs-4 text-success mt-1"></i>
                    <div>
                        <h6 class="mb-1 fw-bold">Booking Approved Successfully!</h6>
                        <p class="mb-0">You can now view your approved room under <a href="#"
                                class="fw-semibold text-decoration-underline">My Room</a>.</p>
                    </div>
                </div>

                <div v-if="ispayment === 'Accepted by Landlord'">
                    <!-- Payment method selection -->
                    <div class="container py-4 mb-4">
                        <div class="mb-3">
                            <label for="paymentType" class="form-label fw-semibold text-dark">
                                <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>Payment Method
                            </label>
                            <input type="text" class="form-control form-control-lg shadow-sm" id="paymentType"
                                v-model="payment_type" readonly />
                            <div id="paymentTypeHelp" class="form-text text-muted">
                                Specify your preferred method of payment.
                            </div>
                        </div>

                        <!-- Payment Options -->
                        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap mt-3">
                            <div v-for="(src, name) in payment" :key="name"
                                class="text-center p-3 border rounded shadow-sm d-flex flex-column align-items-center justify-content-between"
                                :class="{ 'border-primary bg-light': payment_type === name }" role="button"
                                style="cursor: pointer; width: 120px; height: 130px;"
                                @click="paymentTypeSelection(name)">
                                <img :src="src" :alt="name" class="img-fluid mb-2"
                                    style="width: 50px; height: 50px; object-fit: contain;" />
                                <small class="fw-semibold text-capitalize text-center">
                                    {{ name.replace('_', ' ') }}
                                </small>
                            </div>

                        </div>
                        <div class="justify-content-center d-flex mt-2">
                            <span v-if="errors.paymentType" class="text-danger small mt-1 d-block">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.paymentType[0] }}
                            </span>
                        </div>



                    </div>

                    <!-- Payment Upload -->
                    <div class="border border-secondary rounded-3 p-4 mb-3 text-center" style="cursor: pointer;"
                        v-if="isPaymentImage" @click="triggerPaymentImage">
                        <input ref="PaymentPicturesInput" class="d-none" type="file" accept="image/*"
                            @change="handlePaymentPicture" />
                        <div class="d-flex flex-column align-items-center text-center mb-3">
                            <img :src="paymentIcon" alt="Payment Icon" style="max-width: 60px; height: auto;"
                                class="mb-2" />
                            <h5 class="text-secondary mt-2">Upload Payment Image</h5>
                            <small class="text-muted">Click to browse and select an image file</small>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div v-if="PaymentPicturePreview" class="text-center mb-3">
                        <img :src="PaymentPicturePreview" alt="Uploaded Payment Image" class="img-fluid rounded mb-2"
                            style="max-height: 250px;" />
                        <div>
                            <button type="button" @click="removePaymentPicture" class="btn btn-sm">
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
                        <i class="bi bi-calendar-check-fill me-2"></i>Pay for Room
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
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation
    },
    data() {
        return {
            booking: [],
            ispayment: '',
            paymentIcon: '/images/tenant/allimagesResouces/paymentIcon.jpg',
            id_picture: '/images/tenant/allimagesResouces/vector-id-card-icon.jpg',
            payment: {
                gcash: '/images/tenant/allimagesResouces/GCash-Logo.png',
                maya: '/images/tenant/allimagesResouces/maya.png',
                bank_transer: '/images/tenant/allimagesResouces/bank-transfer-logo.png',

            },
            errors: [],
            paymentIcon: '/images/tenant/allimagesResouces/paymentIcon.jpg',
            PaymentPicturePreview: '',
            PaymentPictureFile: null,
            isPaymentImage: true,
            bookingID: '',

            payment_type: '',
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
        paymentTypeSelection(name) {
            this.payment_type = name;
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
        this.getBookingDetails();
    },
};
</script>

<style scoped>
.border {
    border: 1px solid #dee2e6 !important;
}
</style>
