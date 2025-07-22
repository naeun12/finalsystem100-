<template>
    <Loader ref="loader" />

    <div class="container-fluid py-4">
        <div class="container-fluid mt-3">
            <h2 class="mb-4 text-primary fw-semibold text-center">My Bookings</h2>
        </div>


        <div class="input-group mb-4 w-100 shadow-sm rounded-pill overflow-hidden">
            <span class="input-group-text bg-white border-0">
                <i class="bi bi-search text-primary"></i>
            </span>
            <input type="text" class="form-control border-0 shadow-none" placeholder="Search Locations"
                aria-label="Search Tenant name" v-model="searchQuery" @input="debouncedSearch" />
        </div>
        <div class="d-flex justify-content-end">
            <button class="btn  px-4 py-2 mb-3 rounded-pill fw-semibold">
                💸 View Payment
            </button>
        </div>


        <div class="card mb-3 shadow-sm border-0 rounded-4" v-for="(booking, index) in bookings" :key="index"
            style="transition: transform 0.2s ease-in-out;">
            <div class="row g-0">
                <!-- Image Column -->
                <div class="col-md-2 d-flex align-items-center justify-content-center  rounded-start-4">
                    <img :src="booking.studentpictureID" alt="Dorm Image" class="img-fluid rounded-start-4"
                        style="height: 100px; object-fit: cover; width: 100%;" />
                </div>
                <div class="col-md-9 p-3  rounded-end-4">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <p class="mb-1 fw-semibold text-dark">
                                <i class="bi bi-person-fill"></i> Fullname:
                                <span class="text-muted">{{ booking.firstname }} {{ booking.lastname }}</span>
                            </p>
                            <p class="mb-1 fw-semibold text-dark">
                                <i class="bi bi-calendar2-week-fill"></i> Age:
                                <span class="text-muted">{{ booking.age }}</span>
                            </p>
                            <p class="mb-0 fw-semibold text-dark">
                                <i class="bi bi-gender-ambiguous"></i> Gender:
                                <span class="text-muted">{{ booking.gender }}</span>
                            </p>
                        </div>

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

                        <div class="col-md-4 mb-2">
                            <p class="mb-1 fw-semibold text-dark">
                                🛏 Room:
                                <span class="text-muted">{{ booking.room?.roomNumber }}</span>
                            </p>
                            <p class="mb-0 fw-semibold text-dark mb-1">
                                📅 Check-in:
                                <span class="text-muted">{{ booking.moveOutDate }}</span>
                            </p>
                            <p class="mb-0 fw-semibold text-dark d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                                Booking Status:

                                <span :class="['ms-1', getStatusClass(booking.status)]">{{ booking.status }}</span>
                            </p>

                        </div>
                    </div>
                </div>

                <!-- Button Column -->
                <div class="col-md-1 d-flex align-items-center justify-content-center bg-white rounded-end">
                    <button @click="viewBooking(booking)" class="btn  btn-sm rounded-3 w-100 m-2 p-1 fw-semibold">
                        View
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Loader from '@/components/loader.vue';
export default {
    components: {
        Loader,
    },
    data() {
        return {
            bookings: [],
            tenantid: '',
            searchQuery: '',
            viewBookingModal: false,
        };
    },
    methods: {
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
        getStatusClass(status) {
            switch (status.toLowerCase()) {
                case 'approved':
                    return 'text-success';
                case 'declined':
                case 'rejected':
                    return 'text-danger';
                case 'pending':
                    return 'text-warning';
                default:
                    return 'text-secondary';
            }
        }
    },
    mounted() {
        const el = document.getElementById('viewBooking');
        this.tenantid = el.getAttribute('tenant_id')?.trim();
        this.myBookingList();



    },
};
</script>

<style scoped>
.card {
    border-radius: 12px;
}
</style>
