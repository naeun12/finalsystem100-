<template>
    <Loader ref="loader" />

    <div class="container-fluid py-4">
        <div class="container-fluid mt-3">
            <h2
                class="mb-4 text-primary fw-semibold text-center d-flex justify-content-center align-items-center gap-2">
                <i class="bi bi-calendar-check-fill fs-2 text-primary"></i>
                My Reservations
            </h2>
        </div>



        <div class="input-group mb-4 w-100 shadow-sm rounded-pill overflow-hidden">
            <span class="input-group-text bg-white border-0">
                <i class="bi bi-search text-primary"></i>
            </span>
            <input type="text" class="form-control border-0 shadow-none" placeholder="Search Locations"
                aria-label="Search Tenant name" v-model="searchQuery" @input="debouncedSearch" />
        </div>
        <div class="d-flex justify-content-end">

        </div>


        <div class="card mb-3 shadow-sm border-0 rounded-4" v-for="(reservation, index) in reservations" :key="index"
            style="transition: transform 0.2s ease-in-out;">
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

                <!-- Button Column -->
                <!-- View Button -->
                <div class="col-md-1 d-flex align-items-center justify-content-center bg-white rounded-end">
                    <button @click="viewReservation(reservation)"
                        class="btn btn-outline-primary btn-sm rounded-3 w-100 m-2 p-1 fw-semibold d-flex align-items-center justify-content-center gap-1">
                        <i class="bi bi-eye"></i> View
                    </button>

                </div>

                <!-- Cancel Button -->


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
            reservations: [],
            tenantid: '',
            searchQuery: '',
            viewReservationModal: false,
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
