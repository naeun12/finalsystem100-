<template>
    <Loader ref="loader" />
    <NotificationList ref="toastRef" />

    <div class="container-fluid py-4">
        <div class="container-fluid mt-3">
            <h2 class="mb-4 text-primary fw-semibold text-center">My Bookings</h2>
        </div>
        <div class="d-flex justify-content-end">
            <button @click="viewPayments" class="custom-btn  px-4 py-2 mb-3 rounded-pill fw-semibold">
                💸 View Payments
            </button>
        </div>

        <div v-if="bookings.length === 0"
            class="text-center p-5 bg-light rounded-4 shadow-lg position-relative overflow-hidden">
            <!-- Decorative Circle -->
            

            <h4 class="text-muted fw-bold mb-3">No bookings found</h4>
            <p class="text-muted fs-6 mb-4">You haven't made any bookings yet. Explore dormitories and find your perfect
                room!</p>
        </div>

        <div class="card mb-3 shadow-lg border-0 rounded-4 hover-card"
            v-for="(booking, index) in bookings.slice(0, showCount)" :key="index" style="background: #ffffff;">

            <!-- Header -->
            <div class="card-header text-white fw-bold rounded-top-4"
                style="background: linear-gradient(90deg, #4edce2, #2cb5b8);">
                📝 Booking Information
            </div>

            <div class="row g-0 rounded-bottom-4 overflow-hidden" style="border: 1.5px solid #d9f3f4;">

                <!-- Image Column -->
                <div class="col-md-2 d-flex align-items-center justify-content-center bg-light">
                    <img :src="booking.pictureID" alt="Dorm Image" class="img-fluid rounded-start-4"
                        style="height: 120px; object-fit: cover; width: 100%;" />
                </div>

                <!-- Booking Info -->
                <div class="col-md-9 p-3">
                    <div class="row">
                        <!-- Personal Info -->
                        <div class="col-md-4 mb-2">
                            <p class="mb-1 fw-semibold text-dark">
                                <i class="bi bi-person-fill text-primary"></i> Fullname:
                                <span class="text-muted">{{ booking.firstname }} {{ booking.lastname }}</span>
                            </p>
                            <p class="mb-1 fw-semibold text-dark">
                                <i class="bi bi-calendar2-week-fill text-success"></i> Age:
                                <span class="text-muted">{{ booking.age }}</span>
                            </p>
                            <p class="mb-0 fw-semibold text-dark">
                                <i class="bi bi-gender-ambiguous text-info"></i> Gender:
                                <span class="text-muted">{{ booking.gender }}</span>
                            </p>
                        </div>

                        <!-- Dorm Info -->
                        <div class="col-md-4 mb-2">
                            <p class="mb-1 fw-semibold text-dark">
                                🏠 Dormitory:
                                <span class="text-muted">{{ booking.room?.dorm.dormName }}</span>
                            </p>
                            <p class="mb-0 fw-semibold text-dark">
                                📍 Location:
                                <span class="text-muted">{{ booking.room?.dorm.address }}</span>
                            </p>
                        </div>

                        <!-- Room Info -->
                        <div class="col-md-4 mb-2">
                            <p class="mb-1 fw-semibold text-dark">
                                🛏 Room:
                                <span class="text-muted">{{ booking.room?.roomNumber }}</span>
                            </p>
                            <p class="mb-1 fw-semibold text-dark">
                                📅 Check-in:
                                <span class="text-muted">{{ booking.moveOutDate }}</span>
                            </p>
                            <p class="mb-0 fw-semibold text-dark d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                                Status:
                                <statusMap :status="booking.status" role="tenant" />
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Button Column -->
                <div class="col-md-1 d-flex align-items-center justify-content-center bg-white">
                    <button @click="viewBooking(booking)"
                        class="btn btn-outline-info rounded-pill px-3 py-1 fw-semibold shadow-sm">
                        View
                    </button>
                </div>
            </div>
        </div>


        <div class="text-center mt-3">
            <button class="custom-btn rounded-pill px-4" v-if="bookings.length > 3" @click="toggleShow">
                {{ showAll ? 'Show Less' : 'Show More' }}
            </button>
        </div>

    </div>
</template>

<script>
import axios from 'axios';
import Loader from '@/components/loader.vue';
import BookingStatus from '@/components/BookingStatusAlert.vue';
import statusMap from '@/components/statusmap.vue';
import NotificationList from '@/components/notifications.vue';

export default {
    components: {
        Loader,
        BookingStatus,
        statusMap,
        NotificationList,

    },
    data() {
        return {
            bookings: [],
            tenantid: '',
            showAll: false, // default is false
            showCount: 3,   // show 3 items by default
            viewBookingModal: false,
            notifications: [],
            receiverID: '',
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
        viewPayments() {
            window.location.href = `/view/payment/${this.tenantid}`;
        },
        viewBooking(booking) {
            window.location.href = `/view/booking/details/${this.tenantid}/${booking.bookingID}`;
        },
        async myBookingList() {
            try {
                this.$refs.loader.loading = true;

                const response = await axios.get(`/tenant/my-bookings/${this.tenantid}`);
                this.bookings = response.data;
            } catch (error) {
                console.error("Error fetching booking list:", error);
            }
            finally {
                this.$refs.loader.loading = false;

            }
        },
        toggleShow() {
            if (this.showAll) {
                this.showCount = 3;
            } else {
                this.showCount = this.bookings.length;
            }
            this.showAll = !this.showAll;
        }
    },
    mounted() {
        const el = document.getElementById('viewBooking');
        this.tenantid = el.getAttribute('tenant_id')?.trim();
        this.myBookingList();
        this.subscribeToNotifications();




    },
};
</script>

<style scoped>
.card {
    border-radius: 12px;
}
</style>
