<template>
    <Loader ref="loader" />
    <Toastcomponents ref="toast" />
    <NotificationList ref="toastRef" />

    <div class="container-fluid py-4">
        <div class="container-fluid mt-3">
            <h2
                class="mb-4 text-primary fw-semibold text-center d-flex justify-content-center align-items-center gap-2">
                <i class="bi bi-calendar-check-fill fs-2 text-primary"></i>
                My Reservations
            </h2>
        </div>
        <div v-if="visibleReservation.length === 0"
            class="text-center p-5 bg-light rounded-4 shadow-lg position-relative overflow-hidden">
         

            <h4 class="text-muted fw-bold mb-3">No Reservation found</h4>
            <p class="text-muted fs-6 mb-4">You haven't made any Reservation yet. Explore dormitories and find your perfect
                room!</p>
        </div>
        <div class="card mb-3 shadow-sm border-0 rounded-4" v-for="(reservation, index) in visibleReservation"
            :key="index" style="transition: transform 0.2s ease-in-out;">
            <div class="row g-0" style="border:1px solid #4edce2;">
                <!-- Image Column -->
                <div class="col-md-2 d-flex align-items-center justify-content-center  rounded-start-4">
                    <img :src="reservation.pictureID" alt="Dorm Image" class="img-fluid rounded-start-4"
                        style="height: 100px; object-fit:  cover; width: 100%; border-right:1px solid #4edce2;" />
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
                                Reservation Status
                                <StatusBadge :status="reservation.status" />
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-center justify-content-center bg-white rounded-end">
                    <button @click="showReservationModal(reservation)"
                        class="custom-btn  btn-sm rounded-3 w-100 m-2 p-1 fw-semibold d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-eye"></i> View
                    </button>


                </div>
            </div>
        </div>
        <div class="text-center mt-3" v-if="reservations.length > 3">
            <button class="custom-btn" @click="toggleView">
                {{ showAll ? 'Show Less' : 'See More' }}
            </button>
        </div>
    </div>
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.5); ">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content rounded-4" style="border:2px solid #4edce2;">
                <div class="modal-header">
                    <h5 class="modal-title">Reservation Details</h5>
                    <button type="button" class="btn-close" @click="showModal = false"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-4  text-black p-3 rounded-start" style="background-color: #4edce2;">
                                <img :src="reservationDetails.pictureID" class="img-fluid rounded mb-3"
                                    alt="ERD Image" />

                                <p><strong>📌 Reservation Status: </strong>
                                    <StatusBadge :status="reservationDetails.status" />

                                </p>



                                <p><strong>👤 Name:</strong> {{ reservationDetails.firstname }} {{
                                    reservationDetails.lastname }}</p>
                                <p><strong>🎂 Age:</strong> {{ reservationDetails.age }}</p>
                                <p><strong>🚻 Gender:</strong> {{ reservationDetails.gender }}</p>
                                <p><strong>📞 Contact No#:</strong> {{ reservationDetails.contactNumber }}</p>
                                <p><strong>📧 Email:</strong> {{ reservationDetails.contactEmail }}</p>

                                <div class="d-flex justify-content-center align-items-center"
                                    v-if="reservationDetails.status === 'confirmed' || reservationDetails.status === 'pending'">
                                    <button @click="cancelReservation(reservationDetails.reservationID)"
                                        class="btn btn-danger ">
                                        Cancel Reservation
                                    </button>
                                </div>

                            </div>

                            <!-- Right Column -->
                            <div class="col-md-8 p-3">
                                <img :src="reservationDetails.room?.roomImages" class="img-fluid rounded mb-3 w-100"
                                    alt="ERD Image" />

                                <div class="row">
                                    <div class="col-md-6">
                                        <p style="border: 1px solid #4edce2; padding: 8px; border-radius: 6px;">
                                            <strong>🏘️ Dormitory Name:</strong>
                                            {{ reservationDetails.room?.dorm.dormName }}
                                        </p>

                                        <p style="border:1px solid #4edce2; padding: 8px; border-radius: 6px;">
                                            <strong>🛏️ Occupancy Type:</strong> {{
                                            reservationDetails.room?.genderPreference
                                            }}
                                        </p>
                                        <p style="border:1px solid #4edce2; padding: 8px; border-radius: 6px;">
                                            <strong>🔢 Room No#:</strong> {{
                                            reservationDetails.room?.roomNumber
                                            }}
                                        </p>
                                        <p style="border:1px solid #4edce2; padding: 8px; border-radius: 6px;">
                                            <strong>🛋️ Area Sqm:</strong> {{
                                            reservationDetails.room?.areaSqm
                                            }}
                                        </p>

                                    </div>
                                    <div class="col-md-6">
                                        <p style="border:1px solid #4edce2; padding: 8px; border-radius: 6px;">
                                            <strong>📍 Address:</strong> {{
                                            reservationDetails.room?.dorm.address
                                            }}
                                        </p>
                                        <p style="border:1px solid #4edce2; padding: 8px; border-radius: 6px;">
                                            <strong>💵 Monthly Rate:</strong> ₱{{
                                            reservationDetails.room?.price
                                            }}
                                        </p>
                                        <p style="border:1px solid #4edce2; padding: 8px; border-radius: 6px;">
                                            <strong>🛋️ Room Type:</strong> {{
                                            reservationDetails.room?.roomType
                                            }}
                                        </p>
                                        <p style="border:1px solid #4edce2; padding: 8px; border-radius: 6px;">
                                            <strong>🛋️ Tenant Date remaining:</strong> {{ tenantDateRemaining }}
                                        </p>



                                    </div>
                                </div>

                                <ReservationStatusAlert :status="reservationDetails.status" role="tenant"
                                    class="mt-4" />

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <div v-if="reservationDetails.status === 'confirmed'">
                            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

                                <!-- Card Header -->
                                <div class="card-header bg-primary text-white fw-bold text-center py-3">
                                    <i class="bi bi-wallet2 me-2"></i> Payment Details
                                </div>

                                <div class="card-body p-4">

                                    <!-- Move In Date -->
                                    <div class="mb-4">
                                        <label for="moveInDate" class="form-label fw-semibold text-dark">
                                            <i class="bi bi-calendar2-check-fill text-primary me-2"></i> Move In Date
                                        </label>
                                        <input type="date" class="form-control form-control-lg shadow-sm rounded-3"
                                            id="moveInDate" v-model="moveInDate" :min="moveInDate" />

                                    </div>

                                    <!-- Gcash Number -->
                                    <div class="mb-4">
                                        <label for="paymentType" class="form-label fw-semibold text-dark">
                                            <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i> Landlord
                                            GCash Number
                                        </label>
                                        <div class="mb-4">


                                            <div class="p-3 border rounded-3 shadow-sm bg-light fw-semibold text-dark">
                                                {{ reservationDetails.room?.dorm.gcashNumber }}
                                            </div>

                                            <div class="form-text text-muted mt-1">
                                                <i class="bi bi-info-circle me-2"></i> Use this number when sending your
                                                payment
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Upload Proof -->
                                    <div class="border border-secondary rounded-4 p-4 mb-4 text-center upload-box"
                                        v-if="isPaymentImage" @click="triggerPaymentImage" style="cursor: pointer;">
                                        <input ref="PaymentPicturesInput" class="d-none" type="file" accept="image/*"
                                            @change="handlePaymentPicture" />
                                        <div class="d-flex flex-column align-items-center">
                                            <img :src="paymentIcon" alt="Payment Icon"
                                                style="max-width: 60px; height: auto;" class="mb-3" />
                                            <h6 class="fw-bold text-secondary">Upload Payment Proof</h6>
                                            <small class="text-muted">Click to browse and select an image file</small>
                                        </div>
                                    </div>

                                    <!-- Preview -->
                                    <div v-if="PaymentPicturePreview" class="text-center mb-2">
                                        <img :src="PaymentPicturePreview" alt="Uploaded Payment Image"
                                            class="img-fluid rounded-3 shadow-sm mb-3" style="max-height: 250px;" />

                                    </div>
                                    <button type="button" @click="removePaymentPicture"
                                        class="btn btn-outline-danger btn-sm text-center mb-3 align-items-center d-flex justify-content-center mx-auto">
                                        <i class="bi bi-x-circle me-1"></i> Remove Uploaded Image
                                    </button>
                                    <div class="justify-content-center d-flex mb-3">
                                        <span v-if="errors.payment_image" class="text-danger small">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i>{{ errors.payment_image[0]
                                            }}
                                        </span>
                                    </div>

                                    <!-- Pay Button -->
                                    <button type="submit"
                                        class="btn btn-success w-100 py-3 fw-semibold shadow-sm rounded-3"
                                        @click="submitPayment(reservationDetails)">
                                        <i class="bi bi-cash-coin me-2"></i> Pay for Room
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
import StatusBadge from '@/components/statusmap.vue';
import ReservationStatusAlert from '@/components/ReservationStatusAlert.vue';
import NotificationList from '@/components/notifications.vue';

export default {
    components: {
        Toastcomponents,
        Loader,
        Modalconfirmation,
        StatusBadge,
        ReservationStatusAlert,
        NotificationList,
    },
    data() {
        return {
            reservations: [],
            tenantid: '',
            showModal: false,
            searchQuery: '',
            moveInDate: '',
            reservationDetails: [],
            viewReservationModal: false,
            showAll: '',
            paymentIcon: '/images/tenant/allimagesResouces/paymentIcon.jpg',
            id_picture: '/images/tenant/allimagesResouces/vector-id-card-icon.jpg',
            payment: {
                gcash: '/images/tenant/allimagesResouces/GCash-Logo.png',
            },
            errors: [],
            paymentIcon: '/images/tenant/allimagesResouces/paymentIcon.jpg',
            PaymentPicturePreview: '',
            PaymentPictureFile: null,
            isPaymentImage: true,
            payment_type: 'online',
            moveInDateLocal: '',
            moveOutDateLocal: '',
            notifications: [],
            receiverID: '',

        };
    },
    methods: {
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
                this.$refs.loader.loading = true;

                const response = await axios.get(`/tenant/my-reservation/details/${reservation.reservationID}`);
                if (response.data.status === 'success') {
                    this.showModal = true;
                    this.reservationDetails = response.data.data;
                    this.moveInDate = this.reservationDetails.room?.current_tenant?.moveOutDate.split(' ')[0] || '';
                }

            } catch (error) {
                console.error(error);
            }
            finally {
                this.$refs.loader.loading = false;

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
        async submitPayment(reservationDetails) {
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
            formData.append('moveInDate', this.moveInDate);
            formData.append('tenant_id', this.tenantid);
            formData.append('paymentType', this.payment_type);
            formData.append('amount', reservationDetails.room?.price);
            if (this.PaymentPictureFile) {
                formData.append('paymentImage', this.PaymentPictureFile);
            }
            try {
                const response = await axios.post(`/tenant/pay/reservation/${reservationDetails.reservationID}`, formData, {
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

            const confirmed = await this.$refs.modal.show({
                title: `Cancelled Reservation`,
                message: `Are you sure you want to cancel this reservation?`,
                functionName: 'Confirm Cancellation'
            });

            if (!confirmed) {
                return;
            }
            this.$refs.loader.loading = true;

            try {
                const response = await axios.get(`/tenant/cancel/reservation/${reservation_id}`);
                this.myreservation();
                this.closeModal()
                this.$refs.toast.showToast(response.data.message, 'success');
                this.$refs.loader.loading = false;

            } catch (error) {
                console.error(error);
                alert('Failed to cancel reservation.');
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        formatDate(date) {
            if (!date) return "N/A";
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        },
        nextDay(date) {
            if (!date) return null;
            let d = new Date(date);
            d.setDate(d.getDate() + 1);
            return d.toISOString().split('T')[0]; // format YYYY-MM-DD
        }


    },
    computed: {
        tenantDateRemaining() {
            const moveOut = this.reservationDetails.room?.current_tenant?.moveOutDate;
            if (!moveOut) return 'N/A';

            const today = new Date();
            const moveOutDate = new Date(moveOut);

            // Calculate difference in days
            const diffTime = moveOutDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            return diffDays > 0 ? `${diffDays} day(s) remaining` : 'Move-out date passed';
        },
        visibleReservation() {
            return this.showAll ? this.reservations : this.reservations.slice(0, 5);
        },
       
    },
    mounted() {
        const el = document.getElementById('myReservation');
        this.tenantid = el.getAttribute('tenant_id')?.trim();
        this.myreservation();
        this.subscribeToNotifications();




    },
    watch: {
       
    }
};
</script>

<style scoped>
.card {
    border-radius: 12px;
}
</style>
