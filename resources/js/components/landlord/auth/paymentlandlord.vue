<template>
    <div class="d-flex bg-light" style="min-height: 90vh;">
        <!-- Left Side: Benefits -->
        <div class="p-4 text-white" style="background-color: #0d6efd; width: 300px;">
            <h4 class="mb-4">
                <i class="bi bi-star-fill me-2"></i> Benefits
            </h4>

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

            <hr class="border-light" />
            <div class="mt-4 text-center">
                <small class="text-white-50">Powered by Dormhub Secure Payments</small>
            </div>
        </div>

        <!-- Right Side: Payment Form -->
        <div class="flex-fill d-flex align-items-center">
            <div class="p-4 rounded" style="width: 100%; max-width: 100%;  overflow-y: auto;">

                <!-- Title -->
                <h5 class="mb-4 text-center fw-bold">Upgrade Your Account</h5>

                <!-- Payment Method -->
                <div class="mb-4 d-flex justify-content-center" style="font-size: 1rem;">
                    <div class="fw-bold">
                        Payment Method: <span class="text-primary">GCash</span>
                    </div>
                </div>


                <!-- Email -->
                <div class="mb-4">
                    <input type="email" class="form-control form-control-lg" placeholder="you@email.com" />
                </div>
                <div v-if="selectedMethod === 'gcash'" class="mb-4">
                    <input type="text" class="form-control form-control-lg" placeholder="GCash Number" />
                </div>
                <button class="btn btn-primary btn-lg w-100 py-2">
                    Pay ₱500
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            selectedMethod: 'gcash',
        };
    },
};
</script>
