<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <div class="container-fluid py-4">
        <div class="container-fluid mt-3">
            <h2
                class="mb-4 text-primary fw-semibold text-center d-flex justify-content-center align-items-center gap-2">
                <i class="bi bi-calendar-check-fill fs-2 text-primary"></i>
                My Reservations
            </h2>
        </div>
        <div class="d-flex justify-content-end">
        </div>
        <div class="card mb-3 shadow-sm border-0 rounded-4" v-for="(reservation, index) in visibleReservation"
            :key="index" style="transition: transform 0.2s ease-in-out;">
            <div class="row g-0">
                <!-- Image Column -->
                <div class="col-md-2 d-flex align-items-center justify-content-center  rounded-start-4">
                    <img :src="reservation.studentpictureID" alt="Dorm Image" class="img-fluid rounded-start-4"
                        style="height: 100px; object-fit: cover; width: 100%;" />
                </div>
                <div class="col-md-9 p-3  rounded-end-4">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <p class="mb-1 fw-semibold text-dark">
                                <i class="bi bi-person-fill"></i> Fullname:
                                <span class="text-muted">{{ reservation.firstname }} {{ reservation.lastname }}</span>
                            </p>
                            <p class="mb-1 fw-semibold text-dark">
                                <i class="bi bi-calendar2-week-fill"></i> Age:
                                <span class="text-muted">{{ reservation.age }}</span>
                            </p>
                            <p class="mb-0 fw-semibold text-dark">
                                <i class="bi bi-gender-ambiguous"></i> Gender:
                                <span class="text-muted">{{ reservation.gender }}</span>
                            </p>
                        </div>

                        <div class="col-md-4 mb-2">
                            <p class="mb-1 fw-semibold text-dark">
                                🏠 Dormitory:
                                <span class="text-muted">{{ reservation.room?.dorm.dormName }}</span>
                            </p>
                            <p class="mb-0 fw-semibold text-dark">
                                📍 Location:
                                <span class="text-muted">{{ reservation.room?.dorm.address }}</span>
                            </p>
                        </div>

                        <div class="col-md-4 mb-2">
                            <p class="mb-1 fw-semibold text-dark">
                                🛏 Room:
                                <span class="text-muted">{{ reservation.room?.roomNumber }}</span>
                            </p>
                            <p class="mb-0 fw-semibold text-dark mb-1">
                                📅 Request Date:
                                <span class="text-muted">{{ formatDate(reservation.created_at) }}</span>
                            </p>


                            <p class="mb-0 fw-semibold text-dark d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                                Reservation Status:

                                <span :class="['ms-1', getStatusClass(reservation.status)]">{{ reservation.status
                                    }}</span>
                            </p>

                        </div>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-center justify-content-center bg-white rounded-end">
                    <button @click="showReservationModal(reservation)"
                        class="btn  btn-sm rounded-3 w-100 m-2 p-1 fw-semibold d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-eye"></i> View
                    </button>


                </div>
            </div>
        </div>
        <div class="text-center mt-3" v-if="reservations.length > 3">
            <button class="btn" @click="toggleView">
                {{ showAll ? 'Show Less' : 'See More' }}
            </button>
        </div>
    </div>
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title">Reservation Details</h5>
                    <button type="button" class="btn-close" @click="showModal = false"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-4 bg-dark text-white p-3 rounded-start">
                                <img :src="reservationDetails.studentpictureID" class="img-fluid rounded mb-3"
                                    alt="ERD Image" />

                                <p><strong>📌 Reservation Status: </strong>
                                    <span :class="{
                                        'text-warning': reservationDetails.status === 'pending' || reservationDetails.status === 'awaiting_payment',
                                        'text-success': reservationDetails.status === 'confirmed' || reservationDetails.status === 'approved',
                                        'text-danger': reservationDetails.status === 'rejected' || reservationDetails.status === 'cancelled',
                                        'text-muted': reservationDetails.status === 'expired',
                                        'text-primary': reservationDetails.status === 'completed',
                                    }" class="fw-bold">
                                        {{ reservationDetails.status.replace('_', ' ').toUpperCase() }}
                                    </span>
                                </p>

                                <p><strong>👤 Name:</strong> {{ reservationDetails.firstname }} {{
                                    reservationDetails.lastname }}</p>
                                <p><strong>🎂 Age:</strong> {{ reservationDetails.age }}</p>
                                <p><strong>🚻 Gender:</strong> {{ reservationDetails.gender }}</p>
                                <p><strong>📞 Contact No#:</strong> {{ reservationDetails.contactNumber }}</p>
                                <p><strong>📧 Email:</strong> {{ reservationDetails.contactEmail }}</p>

                                <div class="d-flex justify-content-center align-items-center"
                                    v-if="reservationDetails.status === 'confirmed' || reservationDetails.status === 'pending'">
                                    <button @click="cancelReservation(reservationDetails.reservationID)" class="btn ">
                                        Cancel Reservation
                                    </button>
                                </div>

                            </div>

                            <!-- Right Column -->
                            <div class="col-md-8 p-3">
                                <img :src="reservationDetails.room?.roomImages" class="img-fluid rounded mb-3"
                                    alt="ERD Image" />

                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>🏘️ Dormitory Name:</strong> {{
                                            reservationDetails.room?.dorm.dormName
                                            }}</p>
                                        <p><strong>🛏️ Occupancy Type:</strong> {{
                                            reservationDetails.room?.genderPreference
                                            }}</p>
                                        <p><strong>🔢 Room No#:</strong> {{
                                            reservationDetails.room?.roomNumber
                                            }}</p>
                                        <p><strong>🛋️ Area Sqm:</strong> {{
                                            reservationDetails.room?.areaSqm
                                            }}</p>

                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>📍 Address:</strong> {{
                                            reservationDetails.room?.dorm.address
                                            }}</p>
                                        <p><strong>💵 Monthly Rate:</strong> ₱{{
                                            reservationDetails.room?.price
                                            }}</p>
                                        <p><strong>🛋️ Room Type:</strong> {{
                                            reservationDetails.room?.roomType
                                            }}</p>

                                    </div>
                                </div>

                                <div v-if="statusAlert" :class="['alert', statusAlert.class, 'mt-3']" role="alert">
                                    <strong>{{ statusAlert.icon }} {{ statusAlert.title }}</strong><br>
                                    {{ statusAlert.message }}
                                </div>
                                <div v-if="reservationDetails.status === 'confirmed'">
                                    <!-- Payment method selection -->
                                    <div class="container py-4 mb-4">
                                        <div class="mb-3">
                                            <label for="paymentType" class="form-label fw-semibold text-dark">
                                                <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>Payment
                                                Method
                                            </label>
                                            <input type="text" class="form-control form-control-lg shadow-sm"
                                                id="paymentType" v-model="payment_type" readonly />
                                            <div id="paymentTypeHelp" class="form-text text-muted">
                                                Specify your preferred method of payment.
                                            </div>
                                        </div>

                                        <!-- Payment Options -->
                                        <div
                                            class="d-flex justify-content-center align-items-center gap-3 flex-wrap mt-3">
                                            <div v-for="(src, name) in payment" :key="name"
                                                class="text-center p-3 border rounded shadow-sm d-flex flex-column align-items-center justify-content-between"
                                                :class="{ 'border-primary bg-light': payment_type === name }"
                                                role="button" style="cursor: pointer; width: 120px; height: 130px;"
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
                                                <i class="bi bi-exclamation-circle-fill me-1"></i>{{
                                                    errors.paymentType[0] }}
                                            </span>
                                        </div>



                                    </div>

                                    <!-- Payment Upload -->
                                    <div class="border border-secondary rounded-3 p-4 mb-3 text-center"
                                        style="cursor: pointer;" v-if="isPaymentImage" @click="triggerPaymentImage">
                                        <input ref="PaymentPicturesInput" class="d-none" type="file" accept="image/*"
                                            @change="handlePaymentPicture" />
                                        <div class="d-flex flex-column align-items-center text-center mb-3">
                                            <img :src="paymentIcon" alt="Payment Icon"
                                                style="max-width: 60px; height: auto;" class="mb-2" />
                                            <h5 class="text-secondary mt-2">Upload Payment Image</h5>
                                            <small class="text-muted">Click to browse and select an image file</small>
                                        </div>
                                    </div>

                                    <!-- Preview -->
                                    <div v-if="PaymentPicturePreview" class="text-center mb-3">
                                        <img :src="PaymentPicturePreview" alt="Uploaded Payment Image"
                                            class="img-fluid rounded mb-2" style="max-height: 250px;" />
                                        <div>
                                            <button type="button" @click="removePaymentPicture" class="btn btn-sm">
                                                Remove Uploaded Image
                                            </button>
                                        </div>
                                    </div>
                                    <div class="justify-content-center d-flex mb-2">
                                        <span v-if="errors.payment_image" class="text-danger small mt-1 d-block">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.payment_image[0]
                                            }}
                                        </span>
                                    </div>

                                    <!-- Pay Button -->
                                    <button type="submit" class="btn btn-success w-100 py-2 fw-semibold shadow-sm"
                                        @click="submitPayment(reservationDetails.reservationID)">
                                        <i class="bi bi-calendar-check-fill me-2"></i>Pay for Room
                                    </button>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>
    <Modalconfirmation ref="modal" />

</template>

<script>
import axios from 'axios';
import Loader from '@/components/loader.vue';
import Toastcomponents from '@/components/Toastcomponents.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation
    },
    data() {
        return {
            reservations: [],
            tenantid: '',
            showModal: false,
            searchQuery: '',
            reservationDetails: [],
            viewReservationModal: false,
            showAll: '',
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
            payment_type: '',

        };
    },
    methods: {
        viewReservation(reservation) {
            window.location.href = `/view/reservation/details/${this.tenantid}/${reservation.reservationID}`;
        },
        async myreservation() {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get(`/tenant/my-reservation/${this.tenantid}`);
                this.reservations = response.data;
            } catch (error) {
                console.error("Error fetching reservation list:", error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },

        formatDate(date) {
            if (!date) return '';
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString('en-US', options);
        },
        getStatusClass(status) {
            if (!status) return 'text-secondary';

            const map = {
                approved: 'text-success',
                pending: 'text-warning',
                confirmed: 'text-warning',
                rejected: 'text-danger',
                cancelled: 'text-danger',
                expired: 'text-secondary',
                paid: 'text-info',
            };

            return map[status.toLowerCase()] || 'text-secondary';
        },

        toggleView() {
            this.showAll = !this.showAll;
        },
        closeModal() {
            this.PaymentPicturePreview = '';
            this.PaymentPictureFile = null;
            this.payment_type = '';
            this.showModal = false;
        },
        async showReservationModal(reservation) {
            try {
                const response = await axios.get(`/tenant/my-reservation/details/${reservation.reservationID}`);
                if (response.data.status === 'success') {
                    this.showModal = true;
                    this.reservationDetails = response.data.data;
                }

            } catch (error) {
                console.error(error);
            }


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
        async submitPayment(reservationID) {
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
            formData.append('paymentType', this.payment_type);
            if (this.PaymentPictureFile) {
                formData.append('paymentImage', this.PaymentPictureFile);
            }
            try {
                const response = await axios.post(`/tenant/pay/reservation/${reservationID}`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });


                this.$refs.loader.loading = false;
                this.isPaymentImage = true;
                this.myreservation();
                this.closeModal()
                this.$refs.toast.showToast(response.data.message, 'success');
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
        async cancelReservation(reservation_id) {
            try {
                const response = await axios.get(`/tenant/cancel/reservation/${reservation_id}`);
                this.myreservation();
            } catch (error) {
                console.error(error);
                alert('Failed to cancel reservation.');
            }
        },

    },
    computed: {
        visibleReservation() {
            return this.showAll ? this.reservations : this.reservations.slice(0, 3);
        },
        statusAlert() {
            const status = this.reservationDetails.status;

            const map = {
                approved: {
                    class: 'alert-success',
                    icon: '✔️',
                    title: 'Reserve Approved Successfully!',
                    message: 'You can now view your approved room under My Room.',
                },
                pending: {
                    class: 'alert-warning',
                    icon: '⏳',
                    title: 'Your Reservation is Pending.',
                    message: 'Please wait for the landlord to review your request.',
                },
                confirmed: {
                    class: 'alert-warning',
                    icon: '💸',
                    title: 'Awaiting Payment.',
                    message: 'Please upload your payment to confirm the reservation.',
                },
                rejected: {
                    class: 'alert-danger',
                    icon: '❌',
                    title: 'Reservation Rejected.',
                    message: 'Unfortunately, your reservation was not approved.',
                },
                cancelled: {
                    class: 'alert-danger',
                    icon: '🚫',
                    title: 'Reservation Cancelled.',
                    message: 'You have cancelled this reservation.',
                },
                expired: {
                    class: 'alert-secondary',
                    icon: '⌛',
                    title: 'Reservation Expired.',
                    message: 'The reservation was not completed in time.',
                },
                paid: {
                    class: 'alert-info',
                    icon: '💳',
                    title: 'Payment Submitted',
                    message: 'Your payment was received. Please wait for landlord verification.',
                },


            };

            return map[status] || null;
        }
    },
    mounted() {
        const el = document.getElementById('myReservation');
        this.tenantid = el.getAttribute('tenant_id')?.trim();
        this.myreservation();



    },
};
</script>

<style scoped>
.card {
    border-radius: 12px;
}
</style>
