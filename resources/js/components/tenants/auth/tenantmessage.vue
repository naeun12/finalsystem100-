<template>

    <div class="container-fluid vh-100 d-flex flex-column">
        <div class="row flex-grow-1 h-100">
            <!-- Sidebar -->
            <div class="col-md-3 bg-white border-end shadow-sm d-flex flex-column">
                <div class="p-3">
                    <input type="text" class="form-control form-control-sm mb-3" placeholder="Search Conversation" />
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center active">
                            <img :src="landlord.profile_pic_url" alt="Tenant" class="rounded-circle me-2"
                                style="width: 40px; height: 40px; object-fit: cover;" />
                            <span class="fw-semibold text-dark">Lance
                                Monsanto</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="col-md-9 d-flex flex-column p-3 bg-light">
                <!-- Header -->
                <div class="d-flex align-items-center bg-white shadow-sm rounded p-2 mb-3">
                    <img :src="landlord.profile_pic_url" alt="Tenant" class="rounded-circle me-2"
                        style="width: 40px; height: 40px; object-fit: cover;" />
                    <h6 class="mb-0 fw-bold">
                        {{ landlord.firstname ? landlord.firstname + ' ' + landlord.lastname : 'Loading...' }}
                    </h6>
                </div>

                <!-- Chat Messages -->
                <!-- Chat Messages -->
                <div class="p-3 card flex-grow-1 overflow-auto mb-3">
                    <div v-for="msg in messages" :key="msg.id" class="d-flex mb-3"
                        :class="msg.sender_id === tenant_id ? 'justify-content-end text-end' : 'justify-content-start text-start'">

                        <!-- Message wrapper -->
                        <div class="d-flex align-items-end w-100"
                            :class="msg.sender_id === tenant_id ? 'flex-row-reverse' : ''">
                            <!-- Profile Pic -->

                            <!-- Message Bubble -->
                            <div :class="msg.sender_id === tenant_id ? 'bg-primary text-white' : 'bg-white text-dark'"
                                class="rounded p-2 shadow-sm" style="max-width: 60%;">
                                <p class="mb-1">{{ msg.message }}</p>
                                <small class="text-muted">
                                    {{ msg.sender_role }} • {{ new Date(msg.sent_at).toLocaleTimeString() }}
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
    props: {
        tenantId: String,
        receiverId: String,
    },
    data() {
        return {
            message: '',
            successMessage: '',
            receiver_id: this.receiverId,
            tenant_id: this.tenantId,
            messages: [],
            landlord: {},

        };
    },
    methods: {
        async fetchMessages() {
            this.tenant_id = '68bdbf38-7bad-4887-9c15-c6d537df0345'
            this.receiver_id = '32e2e460-e3c5-4518-9da4-237bdff34e1c';
            try {

                const response = await axios.get(`/tenant/messages/${this.tenant_id}/${this.receiver_id}`);
                this.messages = response.data;
                console.log("Fetched Messages:", response.data); // 👈 log the response

            } catch (error) {
                console.error("Failed to load messages", error);
            }
        },
        async pushMessage() {
            if (!this.message.trim()) return;
            this.receiver_id = '32e2e460-e3c5-4518-9da4-237bdff34e1c';
            try {
                const response = await axios.post('/tenant/send-message', {
                    tenant_id: this.tenantId,
                    receiver_id: this.receiver_id,
                    message: this.message,
                });

                this.successMessage = response.data.message;
                this.message = '';
            } catch (error) {
                console.error(error);
                alert('Failed to send message.');
            }
        },
        async getLandlordinfo() {
            try {

                const response = await axios.get('/landlord-information', {
                    params: {
                        landlord: this.receiver_id // Send as query param: ?landlord=xxx
                    }
                });
                this.landlord = response.data;
                console.log("Landlord image URL:", this.landlord.profile_pic_url);

                console.log(response.data); // Debug response
            } catch (error) {
                console.error('Error fetching landlord info:', error);
            }
        }


    },
    mounted() {
        this.fetchMessages();
        this.getLandlordinfo();
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
