<template>
    <div class="notifications-container">
        <!-- Header -->
        <div class="header d-flex align-items-center mb-3">
            <h2 class="me-2">Notifications</h2>
            <i class="bi bi-bell fs-4"></i>
        </div>

        <!-- Notification List -->
        <div class="notification-list">
            <div v-for="notification in notifications" :key="notification.id"
                class="notification-item card border-light mb-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- User Profile -->
                        <div class="user-profile d-flex align-items-center">
                            <img :src="notification.avatar" alt="User Avatar" class="avatar me-3" />
                            <div class="user-info">
                                <strong>{{ notification.user }}</strong>
                                <span>{{ notification.action }}</span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <button class="btn btn-sm btn-primary rounded-pill">
                            {{ notification.buttonText }}
                        </button>
                    </div>

                    <!-- Notification Content -->
                    <p class="mt-2">{{ notification.message }}</p>

                    <!-- Timestamp -->
                    <small class="text-muted mt-2">{{ notification.timestamp }}</small>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "NotificationsComponent",
    data() {
        return {
            // Notification Data
            notifications: [
                {
                    id: 1,
                    avatar: "https://via.placeholder.com/50 ",
                    user: "John Doe",
                    action: "reacted to your post",
                    message:
                        "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Unde, dolorem.",
                    timestamp: "10 mins ago",
                    buttonText: "View",
                },
                {
                    id: 2,
                    avatar: "https://via.placeholder.com/50 ",
                    user: "Richard Miles",
                    action: "liked your post",
                    message:
                        "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Unde, dolorem.",
                    timestamp: "10 mins ago",
                    buttonText: "View",
                },
                {
                    id: 3,
                    avatar: "https://via.placeholder.com/50 ",
                    user: "Brian Cumin",
                    action: "reacted to your post",
                    message:
                        "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Unde, dolorem.",
                    timestamp: "10 mins ago",
                    buttonText: "View",
                },
                {
                    id: 4,
                    avatar: "https://via.placeholder.com/50 ",
                    user: "Lance Bogrol",
                    action: "reacted to your post",
                    message:
                        "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Unde, dolorem.",
                    timestamp: "10 mins ago",
                    buttonText: "View",
                },
                {
                    id: 5,
                    avatar: "https://via.placeholder.com/50 ",
                    user: "Parsley Montana",
                    action: "reacted to your post",
                    message:
                        "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Unde, dolorem.",
                    timestamp: "10 mins ago",
                    buttonText: "View",
                },
            ],
        };
    },
};
</script>

<style scoped>
.notifications-container {
    font-family: Arial, sans-serif;
    max-width: 800px;
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
</style>