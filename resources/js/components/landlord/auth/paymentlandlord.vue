<template>
    <Loader ref="loader" />
    <NotificationList ref="toastRef" />
    <div class="d-flex bg-light" style="min-height: 90vh;">
        <!-- Left Side: Benefits -->
        <div class="p-4 text-white bg-info shadow-lg rounded-4" style="width: 320px;">
            <!-- Title -->
            <h4 class="mb-4 text-center fw-bold">
                <i class="bi bi-star-fill me-2 text-warning"></i> Benefits
            </h4>

            <!-- Benefits List -->
            <ul class="list-unstyled">
                <li class="mb-3 d-flex align-items-start">
                    <i class="bi bi-house-door-fill me-2 text-warning fs-5"></i>
                    <span>Can upload your dorm listings</span>
                </li>
                <li class="mb-3 d-flex align-items-start">
                    <i class="bi bi-people-fill me-2 text-warning fs-5"></i>
                    <span>Reach more tenants faster</span>
                </li>
                <li class="mb-3 d-flex align-items-start">
                    <i class="bi bi-shield-check me-2 text-warning fs-5"></i>
                    <span>Verified landlord badge</span>
                </li>
                <li class="mb-3 d-flex align-items-start">
                    <i class="bi bi-graph-up-arrow me-2 text-warning fs-5"></i>
                    <span>Better visibility on Dormhub search</span>
                </li>
            </ul>

            <!-- Divider -->
            <hr class="border-light opacity-75" />

            <!-- Footer -->
            <div class="mt-3 text-center">
                <small class="text-light fst-italic">Powered by Dormhub Secure Payments</small>
            </div>
        </div>

        <!-- Right Side: Payment Form -->
        <div class="flex-fill d-flex align-items-center justify-content-center">
            <div class="card shadow-lg border-0 rounded-4 p-4" style="width: 100%; max-width: 420px;">
                <!-- Title -->
                <h4 class="text-center fw-bold mb-4 text-primary">Upgrade Your Account</h4>

                <!-- Payment Method -->
                <div class="mb-4 d-flex justify-content-center">
                    <div class="fw-bold border rounded-pill px-4 py-2 shadow-sm bg-light">
                        Payment Method:
                        <span class="badge bg-primary ms-2 px-3 py-2">GCash</span>
                    </div>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" class="form-control form-control-lg" placeholder="you@email.com"
                        v-model="email" />
                        <p class="text-danger mt-1" v-if="error.email">{{ error.email[0] }}</p>
                </div>

                <!-- Pay Button -->
                <button class="btn btn-primary btn-lg w-100 py-2 shadow-sm" @click="payWithGCash" :disabled="loading">
                    <i class="bi bi-wallet2 me-2"></i>
                    <span v-if="loading">Processing...</span>
                    <span v-else>Pay ₱{{ amount }}</span>
                </button>
            </div>
        </div>
    </div>
    <Toastcomponents ref="toast" />
</template>

<script>
import axios from "axios";
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
import NotificationList from '@/components/notifications.vue';
export default {
     components: {
        Loader,
        Modalconfirmation,
        NotificationList
    },
    data() {
        return {
            email: "",
            amount: 500, 
            loading: false,
            error: {},
        };
    },
    methods: {
        async payWithGCash() {

            if (!this.email) {
                this.error.email = ['Email is required.'];
                return;
            }
            
            try {
                this.$refs.loader.loading = true;

                const res = await axios.post(
                    `/landlord/verify-payment`,
                    {
                        paymentEmail: this.email,
                        amount: this.amount,

                    }
                );
                window.location.href = res.data.checkout_url;
                this.error = {};

            } catch (err) {
                console.error(err.response?.data || err);
                alert("Something went wrong. Please try again.");
            } finally {
                this.$refs.loader.loading = false;
            }
        },
        async cancelAnimationFrame() {
            const response = await axios.get('/landlord/{landlord_id}/payment-cancel');
            if(response.data.status === 'cancelled') {
                alert('Payment was cancelled. Please try again.');
            }
        }
    },
    mounted() { 
        const params = new URLSearchParams(window.location.search);
        const status = params.get('status');

        if (status === 'success') {
            this.$refs.toast.show('✅ Payment Success! Your account is now verified.');
            // Optional: call backend to save payment info
        } else if (status === 'cancelled') {
            this.$refs.toast.show('⚠️ Payment was cancelled.');
        }
    }
};
</script>
