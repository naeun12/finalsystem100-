<template>

    <div class="container-fluid py-4" :class="{ 'card-slide': animate }"
        v-for="tenant in rooms.slice(currentIndex, currentIndex + 1)" :key="tenant.roomID">
        <div class="container-fluid">

            <div class="row row-cols-1 row-cols-md-3 g-4">
                <!-- Left Card -->
                <div class="col d-flex">
                    <div class="card shadow-sm border-0 w-100">
                        <div class="card-body text-center">
                            <img :src="tenant.approved_tenant?.studentpictureId"
                                class="rounded-circle mb-3 border border-2 border-primary" width="100" height="100"
                                alt="User Image" />
                            <h5 class="fw-bold mb-1">{{ tenant.approved_tenant?.firstname }} {{
                                tenant.approved_tenant?.lastname }}</h5>
                            <span class="badge bg-success mb-3">Tenant</span>

                            <ul class="list-group list-group-flush text-start small">
                                <li class="list-group-item">
                                    <i class="bi bi-gender-male me-2 text-primary"></i>
                                    <strong>Gender:</strong> {{ tenant.approved_tenant?.gender }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-person-fill me-2 text-secondary"></i>
                                    <strong>Age:</strong> {{ tenant.approved_tenant?.age }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-envelope-fill me-2 text-danger"></i>
                                    <strong>Email:</strong> {{ tenant.approved_tenant?.contactEmail }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-telephone-fill me-2 text-success"></i>
                                    <strong>Contact #:</strong> {{ tenant.approved_tenant?.contactNumber }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Middle Card -->
                <div class="col d-flex">
                    <div class="card shadow-sm border-0 w-100">
                        <img :src="tenant.roomImages" class="card-img-top" style="height: 200px; object-fit: cover;"
                            alt="Room Image" />

                        <div class="card-body">
                            <h5 class="card-title mb-1">Room #{{ tenant.roomNumber }}</h5>

                            <div class="mb-2">
                                <span class="badge bg-primary me-1">{{ tenant.roomType }}</span>
                            </div>

                            <ul class="list-group list-group-flush small">
                                <li class="list-group-item">
                                    <i class="bi bi-currency-peso text-success me-2"></i>
                                    <strong>Price:</strong> ₱{{ tenant.price }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-house-door-fill text-info me-2"></i>
                                    <strong>Furnishing:</strong> {{ tenant.furnishing_status }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-box-fill text-warning me-2"></i>
                                    <strong>Listing Type:</strong> {{ tenant.listingType }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-rulers text-secondary me-2"></i>
                                    <strong>Area:</strong> {{ tenant.areaSqm }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-gender-male text-purple me-2"></i>
                                    <strong>For:</strong> {{ tenant.genderPreference }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Card -->
                <div class="col d-flex">
                    <div class="card shadow-sm border-0 w-100">
                        <div class="card-body text-center">
                            <h5 class="fw-bold mb-2">🏠 Tenant Lease Summary

                            </h5>

                            <ul class="list-group list-group-flush text-start small">
                                <li class="list-group-item">
                                    <i class="bi bi-calendar-event text-primary me-2"></i>
                                    <strong>Lease Start:</strong> {{ tenant.approved_tenant?.moveInDate }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-calendar2-check-fill text-success me-2"></i>
                                    <strong>Lease End:</strong> {{ tenant.approved_tenant?.moveOutDate }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-currency-peso text-info me-2"></i>
                                    <strong class="">💸 Monthly Payment:</strong>
                                    ₱{{ tenant.approved_tenant?.monthlyPayment || 'Not available' }}
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-clock-history text-warning me-2"></i>
                                    <strong>Days Remaining: </strong>
                                    <span class="text-success">
                                        {{ getRemainingLeaseDays(tenant.approved_tenant?.moveInDate,
                                            tenant.approved_tenant?.moveOutDate) }}
                                    </span>
                                </li>


                            </ul>
                            <button class="btn" @click="viewPayment">View Payment</button>

                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="mt-4 d-flex justify-content-center gap-3">
            <button class="btn " @click="prevCard" :disabled="currentIndex === 0">⬅️
                Prev</button>
            <button class="btn " @click="nextCard" :disabled="currentIndex >= rooms.length - 1">Next
                ➡️</button>
        </div>


    </div>



</template>
<script>
import axios from 'axios';
import { nextTick } from 'vue';

export default {
    data() {
        return {
            tenantid: '',
            rooms: [],
            currentIndex: 0,
            animate: false,

        }
    },
    methods: {
        async roomsList() {

            try {
                const response = await axios.get(`/tenant/room-list/${this.tenantid}`);
                this.rooms = response.data.rooms; // dapat consistent sa backend
            } catch (error) {
                console.error('Failed to fetch room list:', error);
            }
        },
        getRemainingLeaseDays(moveIn, moveOut) {
            if (!moveIn || !moveOut) return 'Incomplete dates';

            // Parse properly
            const today = new Date();
            const moveOutDate = new Date(moveOut);

            // Zero out time to avoid timezone issues
            today.setHours(0, 0, 0, 0);
            moveOutDate.setHours(0, 0, 0, 0);

            // Calculate the difference in milliseconds then convert to days
            const diffInMs = moveOutDate - today;
            const remainingDays = Math.ceil(diffInMs / (1000 * 60 * 60 * 24));

            if (remainingDays < 0) return 'Lease ended';
            return `${remainingDays} day(s) remaining`;
        },

        nextCard() {
            if (this.currentIndex < this.rooms.length - 1) {
                this.currentIndex++;
                this.triggerAnimation();
            }
        },
        prevCard() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                this.triggerAnimation();
            }
        },
        triggerAnimation() {
            this.animate = false;
            this.$nextTick(() => {
                this.animate = true;
            });
        },
        viewPayment() {
            window.location.href = `/view/payment/${this.tenantid}`;

        },

    },
    mounted() {
        nextTick(() => {
            const el = document.getElementById('myRooms');
            if (el) {
                this.tenantid = el.getAttribute('tenant_id')?.trim();
                this.roomsList();
            } else {
                console.warn('nextPayment div not found');
            }
        });
    }

}
</script>
<style>
.card-slide {
    animation: slideIn 0.3s ease-in-out;
}

@keyframes slideIn {
    0% {
        transform: translateX(30px);
        opacity: 0;
    }

    100% {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
