<template>
    <div :class="['container-toast mt-5', { show: toaster }]">
        <!-- Toast Container -->
        <div :class="['toast-child', `bg-${toastColor}`]">
            <div class="toast-body d-flex justify-content-between align-items-center text-white fw-bold py-3 px-4">
                <span class="text-wrap">{{ messageToaster }}</span>
                <button type="button" class="btn-close btn-close-white ms-3" @click="ExitToaster"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            toaster: false,
            toastColor: 'success', // Default color
            messageToaster: '',
        };
    },
    methods: {
        showToast(message, color = 'success') {
            this.messageToaster = message;
            this.toastColor = color;
            this.toaster = true;

            // Auto-hide after 3 seconds
            setTimeout(() => {
                this.ExitToaster();
            }, 3000);
        },
        ExitToaster() {
            this.toaster = false;
        }
    }
};
</script>
<style>
.container-toast {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    width: 95%;
    max-width: 30rem;
    z-index: 1000;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

/* Slide up just a bit from the bottom-right */
@keyframes slideUpBounce {
    0% {
        transform: translateY(20px);
        opacity: 0;
    }

    100% {
        transform: translateY(0);
        opacity: 1;
    }
}

.container-toast.show {
    pointer-events: auto;
    animation: slideUpBounce 0.4s ease forwards;
}
</style>