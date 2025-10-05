<template>
    <div v-if="statusData" :class="['alert', statusData.class]" role="alert">
        <h5 class="mb-2 fw-bold">
            {{ statusData.icon }} {{ statusData.title }}
        </h5>
        <p class="mb-0">{{ statusData.message }}</p>
        <p v-if="statusData.note" class="mt-2 text-muted small">{{ statusData.note }}</p>
    </div>
</template>

<script>
export default {
    props: {
        status: {
            type: String,
            required: true
        },
        role: {
            type: String,
            required: true,
            validator: (value) => ['tenant', 'landlord'].includes(value)
        }
    },
    computed: {
        statusData() {
            const common = {
                approved: {
                    class: 'alert-success',
                    icon: '✔️',
                    title: 'Booking Approved',
                    message: this.role === 'tenant'
                        ? 'You can now view your approved room under My Room.'
                        : 'You have successfully approved this booking request.',
                },
                pending: {
                    class: 'alert-warning',
                    icon: '⏳',
                    title: 'Booking is Pending',
                    message: this.role === 'tenant'
                        ? 'Please wait for the landlord to review your booking.'
                        : 'You have a pending booking request to review.',
                },
                confirmed: {
                    class: 'alert-secondary',
                    icon: '💸',
                    title: 'Awaiting Payment',
                    message: this.role === 'tenant'
                        ? 'Please upload your payment to confirm the booking.'
                        : 'Waiting for tenant to upload payment.',
                    note: this.role === 'tenant'
                        ? '💳⏳🗓️ Please finalize your payment and select a move-in date to begin your stay.'
                        : null
                },
                rejected: {
                    class: 'alert-danger',
                    icon: '❌',
                    title: 'Booking Rejected',
                    message: this.role === 'tenant'
                        ? 'Unfortunately, your booking was not approved.'
                        : 'You have rejected the tenant’s booking request.',
                },
                cancelled: {
                    class: 'alert-danger',
                    icon: '🚫',
                    title: 'Booking Cancelled',
                    message: this.role === 'tenant'
                        ? 'You have cancelled this booking.'
                        : 'The tenant has cancelled their booking.',
                },
                expired: {
                    class: 'alert-secondary',
                    icon: '⌛',
                    title: 'Booking Expired',
                    message: this.role === 'tenant'
                        ? 'The booking was not completed in time.'
                        : 'Tenant did not complete the booking in time.',
                },
                paid: {
                    class: 'alert-info',
                    icon: '💳',
                    title: 'Payment Submitted',
                    message: this.role === 'tenant'
                        ? 'Your payment was received. Please wait for landlord verification.'
                        : 'The tenant has submitted a payment. Please verify it.',
                }
            };

            return common[this.status] || null;
        }
    }
};
</script>

<style scoped>
.alert {
    border-radius: 0.5rem;
    padding: 1rem;
}
</style>
