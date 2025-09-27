<template>
    <Loader ref="loader" />

    <div class="container-fluid vh-150 d-flex flex-column bg-light">
        <div class="row flex-grow-1 h-100 overflow-hidden">
            <!-- Sidebar -->
            <div class="col-md-3 bg-white border-end shadow-sm d-flex flex-column">
                <div class="p-3">
                    <h5 class="fw-bold text-primary mb-3">Inbox</h5>
                    <div class="list-group bg-transparent overflow-auto" style="max-height: 80vh;">
                        <a v-for="convo in conversations" :key="convo.conversation_id" href="#"
                            class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-2 px-3 shadow-sm rounded mb-2 transition"
                            :class="[
                                convo.is_read === 0
                                    ? 'bg-primary text-white'   // 👈 unread
                                    : 'bg-info text-dark'       // 👈 read
                            ]" @click.prevent="selectConversation(convo)" style="border: none; cursor: pointer;">

                            <!-- Profile Picture -->
                            <img :src="convo.receiver_profile || 'default-profile.png'" alt="Profile"
                                class="rounded-circle border" style="width: 48px; height: 48px; object-fit: cover;" />

                            <!-- Conversation Details -->
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-semibold text-truncate">
                                    {{ convo.receiver_name }}
                                </h6>
                                <small :class="convo.is_read === 0 ? 'fw-bold text-white-50' : 'text-dark'">
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
                <div class="d-flex align-items-center bg-white shadow-sm rounded p-3 mb-3 border">
                    <img :src="activeLandlord.profilePicUrl" alt="Landlord" class="rounded-circle me-3 border"
                        style="width: 50px; height: 50px; object-fit: cover;" />


                    <div>
                        <h6 class="mb-0 fw-bold text-dark">
                            {{ activeLandlord.firstname ? activeLandlord.firstname + ' ' + activeLandlord.lastname :
                            'Loading...' }}
                        </h6>
                        <small class="text-muted">Tenant</small>
                    </div>
                </div>

                <!-- Chat Messages -->
                <!-- Chat Messages -->
                <div ref="chatContainer" class="p-3 bg-white shadow-sm rounded border flex-grow-1 mb-3 overflow-auto"
                    style="height: 600px;">
                    <div v-for="msg in messages" :key="msg.id" class="d-flex mb-3"
                        :class="msg.senderID === currentUserID ? 'justify-content-end text-end' : 'justify-content-start text-start'">
                        <div class="d-flex align-items-end w-100"
                            :class="msg.senderID === currentUserID ? 'flex-row-reverse' : ''">
                            <div :class="msg.senderID === currentUserID ? 'bg-primary text-white' : 'bg-light text-dark'"
                                class="rounded p-3 shadow-sm" style="max-width: 60%;">
                                <p class="mb-1">{{ msg.message }}</p>
                                <small class="text-secondary-50">
                                    {{ formatRole(msg.senderRole) }} • {{ formatTime(msg.sentAt) }}
                                </small>
                            </div>
                        </div>
                    </div>

                </div>


                <!-- Message Input -->
                <div class="input-group shadow-sm">
                    <input type="text" v-model="message" class="form-control rounded-start"
                        placeholder="Type a message..." />
                    <button type="button" class="btn btn-primary rounded-end px-4" @click="pushMessage">
                        <i class="bi bi-send-fill"></i> Send
                    </button>
                </div>
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
            landlordID: '',
            conversations: [],
            pollInterval: null,
            activeConversationID: null,
            messages: [],
            message: '',
            newMessage: '',

            tenantID: '',
            currentUserID: '',
            currentUserRole: '',
            echoChannel: null,

            activeConversationID: null,
            activeLandlord: {
                firstname: '',
                lastname: '',
                profilePicUrl: ''
            },
        };
    },

    methods: {
        fetchConversations() {
            this.$refs.loader.loading = true;

            axios.get(`/api/landlord/conversations/${this.landlordID}`)
                .then(res => {
                    this.conversations = res.data;
                    this.subscribeToConversations();
                    if (!this.activeConversationID && this.conversations.length > 0) {
                        this.selectConversation(this.conversations[0]);
                    }

                }).catch(err => {
                    console.error("Failed to fetch conversations:", err);
                }).finally(() => {
                    this.$refs.loader.loading = false;
                });

        },
        selectConversation(convo) {

            this.activeConversationID = convo.conversation_id;
            this.activeLandlord = {
                firstname: convo.receiver_name.split(' ')[0] || '',
                lastname: convo.receiver_name.split(' ')[1] || '',
                profilePicUrl: convo.receiver_profile
            };

            console.log('sds',this.activeLandlord.profilePicUrl)

            this.fetchMessages(convo.conversation_id);
            if (this.messagePollInterval) {
                clearInterval(this.messagePollInterval);
            }

        },
        fetchMessages(conversationID) {
            this.$refs.loader.loading = true;

            axios.get(`/api/get/landlord/messages/${conversationID}`)
                .then(res => {
                    this.messages = res.data;
                    this.scrollToBottom(); // 👈 auto scroll here

                }).catch(err => {
                    console.error("Failed to fetch messages:", err);
                }).finally()
            {
                this.$refs.loader.loading = false;

            };
        },

        pushMessage() {
            const trimmedMessage = this.message.trim();
            this.$refs.loader.loading = true;

            if (!trimmedMessage) return;

            axios.post('/api/landlord/messages', {
                conversationID: this.activeConversationID,
                message: trimmedMessage,
                senderID: this.landlordID,
                senderRole: 'landlord',
            })
                .then(() => {
                    this.fetchMessages(this.activeConversationID);
                    this.message = '';
                })
                .catch(err => {
                    console.error("❌ Failed to send message:", err);
                    alert("Failed to send message. Please try again.");
                }).finally()
            {
                this.$refs.loader.loading = false;

            };
        },
        subscribeToConversations() {
            this.conversations.forEach(convo => {
                const channelName = `chat.${convo.conversation_id}`;
                console.log(channelName);
                window.Echo.private(channelName)
                    .subscribed(() => {
                    })
                    .listen('.message.sent', (e) => {

                        // Check if this message is for the currently active conversation
                        if (this.activeConversationID == e.message.conversationID) {
                            this.messages.push(e.message);
                            this.scrollToBottom();
                        } else {
                            // Optional: update unread badge or notification
                            console.log(`🔔 New message in another conversation: ${e.message.conversationID}`);
                        }
                    })
                    .error((err) => {
                        console.error(`❌ Subscription error for ${channelName}:`, err);
                    });
            });
        },


        isActiveConversation(id) {
            return this.activeConversationID === id;
        },

        formatTime(datetime) {
            return new Date(datetime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        formatRole(role) {
            return role.charAt(0).toUpperCase() + role.slice(1);
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.chatContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        }
    },

    mounted() {
        const container = document.getElementById('MessagingCenter');
        if (container) {
            this.landlordID = container.getAttribute('landlord_id');
            this.fetchConversations();

        } else {
            console.error("MessagingCenter container not found");
        }
        this.tenantID = localStorage.getItem("tenant_id") || 'your-default-id';
        this.currentUserID = this.landlordID;
        this.currentUserRole = 'landlord';
    },


};

</script>

<style scoped>
.list-group-item.active {
    background-color: #e7f1ff;
    border-left: 4px solid #0d6efd;
    font-weight: 500;
}

.transition {
    transition: background-color 0.2s ease-in-out;
}

::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-thumb {
    background-color: #adb5bd;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background-color: #868e96;
}
</style>
