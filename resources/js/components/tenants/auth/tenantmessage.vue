<template>
    <div class="container-fluid vh-100 d-flex flex-column">
        <div class="row flex-grow-1 h-100">
            <!-- Sidebar -->
            <div class="col-md-3 bg-white border-end shadow-sm d-flex flex-column">
                <div class="p-3">
                    <input type="text" class="form-control form-control-sm mb-3" placeholder="Search Conversation" />
                    <div class="list-group bg-transparent">
                        <a v-for="convo in conversations" :key="convo.conversation_id" href="#"
                            class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-2 px-3 shadow-sm rounded mb-2"
                            :class="isActiveConversation(convo.conversation_id) ? 'bg-primary text-white' : 'bg-light text-dark'"
                            @click.prevent="selectConversation(convo)" style="border: none;">

                            <img :src="convo.receiver_profile || 'default-profile.png'" alt="Profile"
                                class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover;" />

                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-semibold">
                                    {{ convo.receiver_name }}
                                </h6>
                                <small
                                    :class="isActiveConversation(convo.conversation_id) ? 'text-white-50' : 'text-muted'">
                                    {{ convo.last_message }}
                                </small>
                            </div>
                        </a>
                    </div>

                </div>
            </div>

            <!-- Chat Area -->
            <div class="col-md-9 d-flex flex-column p-3 bg-light">
                <!-- Header -->
                <div class="d-flex align-items-center bg-white shadow-sm rounded p-2 mb-3">
                    <img :src="activeLandlord.profile_pic_url || 'default-profile.png'" alt="Landlord"
                        class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;" />
                    <h6 class="mb-0 fw-bold text-black">
                        {{ activeLandlord.firstname ? activeLandlord.firstname + ' ' + activeLandlord.lastname :
                            'Loading...' }}
                    </h6>
                </div>

                <!-- Chat Messages -->
                <div class="p-3 card flex-grow-1 overflow-auto mb-3">
                    <div v-for="msg in messages" :key="msg.id" class="d-flex mb-3"
                        :class="msg.senderID === tenantID ? 'justify-content-end text-end' : 'justify-content-start text-start'">
                        <div class="d-flex align-items-end w-100"
                            :class="msg.senderID === tenantID ? 'flex-row-reverse' : ''">
                            <div :class="msg.senderID === tenantID ? 'bg-primary text-white' : 'bg-white text-dark'"
                                class="rounded p-2 shadow-sm" style="max-width: 60%;">
                                <p class="mb-1">{{ msg.message }}</p>
                                <small class="text-white-50">
                                    {{ formatRole(msg.senderRole) }} • {{ formatTime(msg.sentAt) }}
                                </small>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="input-group">
                    <input type="text" v-model="message" class="form-control" placeholder="Type a message..." />
                    <button type="button" class="btn btn-primary" @click="pushMessage">Send</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            tenantID: '',
            receiver_id: '',
            conversation_id: '',
            conversations: [],
            messages: [],
            message: '',
            activeLandlord: {
                firstname: '',
                lastname: '',
                profile_pic_url: ''
            },
        };
    },
    methods: {
        async fetchMessages() {
            try {
                const res = await axios.get(`/get-messages/${this.tenantID}/${this.receiver_id}`);
                this.messages = res.data;
            } catch (err) {
                console.error("Fetch Messages Error:", err);
            }
        },
        async getTenantConversation() {
            try {
                const res = await axios.get(`/tenant-conversation/${this.tenantID}`);
                this.conversations = res.data.history || [];

                if (this.conversations.length > 0) {
                    this.selectConversation(this.conversations[0]);
                }
            } catch (err) {
                console.error("Conversation Fetch Error:", err);
            }
        },
        selectConversation(convo) {
            this.conversation_id = convo.conversation_id;
            this.receiver_id = convo.receiver_id;

            const [first, last] = convo.receiver_name?.split(' ') || [];
            this.activeLandlord = {
                firstname: first || '',
                lastname: last || '',
                profile_pic_url: convo.receiver_profile
            };

            this.fetchMessages();
            this.listenToConversation();
        },
        listenToConversation() {
            if (!window.Echo || !this.conversation_id) {
                console.warn("Echo not ready or missing conversation ID");
                return;
            }

            window.Echo.channel(`conversation.${this.conversation_id}`)
                .listen('.message.sent', (e) => {
                    this.messages.push(e.message);
                });
        },
        async pushMessage() {
            if (!this.message.trim()) return;
            try {
                const res = await axios.post('/tenant/send-message', {
                    tenant_id: this.tenantID,
                    receiver_id: this.receiver_id,
                    message: this.message
                });
                this.message = '';
                this.fetchMessages();

            } catch (err) {
                console.error("Send Message Error:", err);
                alert("Failed to send message.");
            }
        },
        isActiveConversation(id) {
            return this.conversation_id === id;
        },
        formatRole(role) {
            if (!role) return 'Unknown';
            return role.charAt(0).toUpperCase() + role.slice(1);
        },
        formatTime(datetime) {
            const date = new Date(datetime);
            return date.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true,
            });
        },
    },

    mounted() {
        const el = document.getElementById('tenantmessage');
        this.tenantID = el.getAttribute('data-tenant-id')?.trim();
        this.receiver_id = el.getAttribute('data-landlord-id')?.trim();

        this.getTenantConversation();
    }
};
</script>

<style scoped>
.list-group-item.active {
    background-color: #e7f1ff;
    border-left: 4px solid #0d6efd;
    font-weight: 500;
}
</style>
