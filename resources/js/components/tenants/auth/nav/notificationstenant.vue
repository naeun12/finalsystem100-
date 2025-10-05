<template>
    <Loader ref="loader" />

    <div class="notifications-container">
        <!-- Header -->
        <div class="header d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center">
                <h2 class="me-2 mb-0">Notifications</h2>
                <i class="bi bi-bell-fill text-warning fs-4"></i>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-success btn-sm" @click="markAllAsRead" :disabled="notifications.length === 0">
                    <i class="bi bi-check2-all me-1"></i> Read All
                </button>

                <button class="btn btn-danger btn-sm" @click="clearAllNotifications"
                    :disabled="notifications.length === 0">
                    <i class="bi bi-trash me-1"></i> Clear All
                </button>
            </div>

        </div>

        <!-- Notification List -->
        <div class="notification-list scrollable-notifications">
            <div v-if="notifications.length === 0" class="text-center text-muted py-5">
                <i class="bi bi-bell-slash display-6 d-block mb-2"></i>
                No notifications
            </div>
            <div v-for="notification in notifications" :key="notification.id"
                class="notification-item card border-0 shadow-sm mb-3 rounded-4 hover-notification">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- User Profile -->
                        <div class="d-flex align-items-center flex-grow-1">
                           
                            <div class="flex-grow-1">
                                <strong class="text-dark">
                                    {{ notification.sender?.firstname }} {{ notification.sender?.lastname }}
                                </strong>
                                <div class="text-muted small">{{ notification.action }}</div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="text-muted small d-flex align-items-center me-3">
                            <i v-if="notification.readAt" class="bi bi-check2-circle text-success me-1"></i>
                            <i v-else class="bi bi-clock text-secondary me-1"></i>
                            {{ notification.readAt ? notification.readAt : "Unread" }}
                        </div>

                        <!-- Action -->
                        <button class="btn btn-primary btn-sm rounded-pill px-3"
                            @click="markNotificationAsRead(notification.id)">
                            View
                        </button>
                    </div>

                    <!-- Message -->
                    <p class="mt-2 mb-1">{{ notification.message }}</p>

                    <!-- Timestamp -->
                    <small class="text-muted">{{ notification.created_at }}</small>
                </div>
            </div>
        </div>

        <!-- Notification Modal -->
        <div v-if="ismodalviewNotifications" class="modal fade show" tabindex="-1"
            style="display: block; background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-3">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">{{ selectedNotification.title || "Notification" }}</h5>
                        <button type="button" class="btn-close" @click="ismodalviewNotifications = false"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <p>{{ selectedNotification.message }}</p>
                        <small class="text-muted">
                            <i v-if="selectedNotification.isRead" class="bi bi-check2-circle text-success"></i>
                            <i v-else class="bi bi-clock text-secondary"></i>
                            {{ selectedNotification.readAt ? selectedNotification.readAt : "Unread" }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <Modalconfirmation ref="modal" />
    </div>
</template>

<script>
import axios from 'axios';
import NotificationList from '@/components/notifications.vue';
import Loader from '@/components/loader.vue';
import Modalconfirmation from '@/components/modalconfirmation.vue';
export default {
    components: {
        Loader,
        Modalconfirmation,
        NotificationList,
    },
    data() {
        return {
            // Notification Data
            notifications: [],
            tenant_id: '',
            ismodalviewNotifications: false,
            selectedNotification: {},
        };
    },
    methods: {
        subscribeToNotifications() {
            if (this.hasSubscribed) return;
            this.hasSubscribed = true;

            this.receiverID = this.tenant_id;
            Echo.private(`notifications.${this.receiverID}`)
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
        async getNotificationsList() {
            try {
                const response = await axios.get(`/get/notifications/tenant/${this.tenant_id}`);
                if (response.data.status === 'success') {
                    this.notifications = response.data.notifications;
                }
            } catch (error) {
                console.error(error);
            }
        },
        async markNotificationAsRead(id) {
            try {
                const res = await axios.post(`/mark/read/tenant/${id}`);
                this.ismodalviewNotifications = true;
                this.selectedNotification = res.data.notification;
                this.getNotificationsList();
            } catch (error) {
                console.error(error);
            }
        },

        async markAllAsRead() {
            const confirmed = await this.$refs.modal.show({
                title: 'Mark Notifications',
                message: `Confirm to mark all notifications as read?`,
                functionName: 'Mark Notifications',
            });
            if (!confirmed) {
                return;
            }
            try {
                this.$refs.loader.loading = true;
                await axios.post(`/notifications/mark-all-as-read/tenant`, { tenant_id: this.tenant_id }, {
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    }

                });
                this.getNotificationsList();

            } catch (error) {

            }
            finally {
                this.$refs.loader.loading = false;

            }


        },
        async clearAllNotifications() {
            const confirmed = await this.$refs.modal.show({
                title: 'Clear Notifications',
                message: `Confirm to clear all notifications?`,
                functionName: 'Clear Notifications',
            });
            if (!confirmed) {
                return;
            }
            try {
                this.$refs.loader.loading = true;
                await axios.post(`/clear/notifications/tenant`, { tenant_id: this.tenant_id }, {
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    }

                });
                this.getNotificationsList();

            } catch (error) {

            }
            finally {
                this.$refs.loader.loading = false;

            }

        }
    },
    mounted() {
        const el = document.getElementById('notificationsTenant');
        if (el) {
            this.tenant_id = el.getAttribute('data-tenant-id');
        }

        this.subscribeToNotifications();
        this.getNotificationsList();
    }
};
</script>

<style scoped>
.notifications-container {
    font-family: Arial, sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

.header h2 {
    display: inline-block;
    position: relative;
    font-size: 24px;
    font-weight: bold;
}

.header h2::after {
    content: "";
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #007bff;
}

.notification-item {
    border-left: 4px solid #007bff;
}

.avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
}

.user-info strong {
    font-size: 16px;
}

.user-info span {
    font-size: 14px;
    color: #666;
}

.card-body p {
    font-size: 14px;
    line-height: 1.6;
}

/* Scrollable notifications container */
.scrollable-notifications {
    max-height: 700px;
    /* Adjust height */
    overflow-y: auto;
    padding-right: 5px;
    /* para di magka-cut scrollbar */
}

.scrollable-notifications::-webkit-scrollbar {
    width: 6px;
}

.scrollable-notifications::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
}

.scrollable-notifications::-webkit-scrollbar-track {
    background: transparent;
}
</style>