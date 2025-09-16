<template>
    <div class="fixed bottom-6 left-6 z-50">
        <transition-group name="fade" tag="div">
            <div v-for="(notif, index) in toasts" :key="notif.id"
                :class="['toast-child mb-3', `bg-${notif.color || 'success'}`]">
                <div class="toast-body d-flex flex-column text-white fw-bold py-3 px-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="me-auto">{{ notif.title }}</span>
                        <button type="button" class="btn-close btn-close-white ms-3" aria-label="Close"
                            @click="removeToast(notif.id)"></button>
                    </div>
                    <span class="text-wrap small mt-1">{{ notif.message }}</span>
                </div>
            </div>
        </transition-group>
    </div>
</template>
<script>
export default {
    name: 'NotificationPopup',
    data() {
        return {
            toasts: [],
        };
    },
    methods: {
        pushNotification(notification) {
            const toast = {
                id: Date.now(),
                title: notification.title || 'Notification',
                message: notification.message || 'You have a new notification!',
                color: notification.color || 'success',
            };

            this.toasts.unshift(toast);

            setTimeout(() => {
                this.removeToast(toast.id);
            }, 3000);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter((t) => t.id !== id);
        },
    },
};
</script>

<style scoped>
.toast-child {
    width: 95%;
    max-width: 28rem;
    border-left: 5px solid white;
    border-radius: 0.5rem;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    opacity: 0;
    pointer-events: none;
    animation: slideInLeft 0.4s ease forwards;
    /* NEW animation */
}

/* Slide-in from left bottom */
@keyframes slideInLeft {
    0% {
        transform: translateX(-30px) translateY(20px);
        opacity: 0;
    }

    100% {
        transform: translateX(0) translateY(0);
        opacity: 1;
        pointer-events: auto;
    }
}

/* Fade for transition-group */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>