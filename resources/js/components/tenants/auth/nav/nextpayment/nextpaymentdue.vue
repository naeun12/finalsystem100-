<template>
    <NotificationList ref="toastRef" />

    <div class="container-fluid py-4">
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Payment History -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden">

                    <!-- Header -->
                    <div class="card-header text-center text-white fw-bold"
                        style="background: linear-gradient(90deg, #4edce2, #2cb5b8);">
                        <h5 class="mb-0">📜 Payment History</h5>
                    </div>

                    <div class="card-body p-4" style="background: #f9fdfd;">
                        <!-- Scrollable Wrapper -->
                        <div style="max-height: 600px; overflow-y: auto;">

                            <!-- Payment Item -->
                            <div class="border rounded-3 p-3 mb-3 shadow-sm bg-white d-flex align-items-center justify-content-between hover-effect"
                                v-for="(history, index) in allHistory" :key="index"
                                style="transition: all 0.2s ease; border: 1px solid #d9f3f4;">

                                <!-- Left Side (Tenant & Room Info) -->
                                <div class="flex-grow-1">
                                    <p class="mb-1 fw-semibold text-dark">
                                        {{ history.firstname }}
                                        <span v-if="history.type === 'Booking'">Booking Payment</span>
                                        <span v-else-if="history.type === 'Reservation'">Reservation Payment</span>
                                        <span v-else-if="history.type === 'Extension Payment'">Extension Payment</span>
                                        <span v-else>{{ history.type }}</span>
                                        #{{ history.reservation?.room?.roomID || history.booking?.room?.roomID ||
                                        history.approved_tenant?.room?.roomID || 'N/A' }}
                                    </p>

                                    <p class="mb-0 text-muted small">
                                        Payment Date: <strong>{{ formatDate(history.created_at) }}</strong>
                                    </p>
                                </div>

                                <!-- Amount -->
                                <div class="text-success fw-bold mx-3" v-if="history.amount">
                                    ₱{{ formatAmount(history.amount) }}
                                </div>

                                <!-- Payment Method -->
                                <div class="text-end">
                                    <span class="badge rounded-pill px-3 py-2" :class="{
                                        'bg-success text-white': history.paymentType === 'online',
                                        'bg-primary text-white': history.paymentType === 'onsite',
                                        'bg-secondary text-white': !history.paymentType
                                    }">
                                        {{ history.paymentType || 'N/A' }}
                                    </span>
                                </div>
                               
                            </div>
                            <!-- End Payment Item -->

                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                <!-- Payment Summary -->
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden"
                    style="background: linear-gradient(135deg, #e6fafa, #ffffff);">

                    <!-- Header -->
                    <div class="card-header text-center text-white fw-bold"
                        style="background: linear-gradient(90deg, #4edce2, #2cb5b8);">
                        <h6 class="mb-0">💰 Payment Summary</h6>
                    </div>

                    <!-- Body -->
                    <div class="card-body p-4">

                        <!-- Date Picker -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Choose Date:</label>
                            <input type="date" v-model="chooseDate" @change="paymentHistory()"
                                class="form-control rounded-3 shadow-sm">
                        </div>

                        <!-- Current Bill -->
                        <p class="fw-semibold text-secondary mb-2">📌 Current Bill:</p>
                        <div class="progress mb-3 rounded-pill" style="height: 20px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated fw-bold"
                                role="progressbar" :style="{ width: percent + '%' }">
                                {{ Math.round(percent) }}%
                            </div>
                        </div>

                        <!-- Total Amount -->
                        <p class="fw-semibold text-dark mb-4">
                            Total Amount This Month:
                            <span class="text-success fs-5">₱{{ formatAmount(totalAmount) }}</span>
                        </p>

                        <!-- Payment Methods -->
                        <p class="fw-semibold text-secondary mb-2">💳 Payment Method:</p>
                        <div class="form-check border rounded-3 px-3 py-2 shadow-sm mb-2" style="cursor:pointer;">
                            <input class="form-check-input" type="radio" v-model="paymentMethod" value="online"
                                id="gcash" @change="paymentHistory()">
                            <label class="form-check-label fw-semibold ms-2" for="gcash">online</label>

                        </div>
                        <div class="form-check border rounded-3 px-3 py-2 shadow-sm mb-2" style="cursor:pointer;">
                            <input class="form-check-input" type="radio" v-model="paymentMethod" value="onsite"
                                id="onsite" @change="paymentHistory()">
                            <label class="form-check-label fw-semibold ms-2" for="onsite">Onsite</label>

                        </div>

                        <!-- Optional Future Add-ons -->
                        <!-- 
            <div class="form-check border rounded-3 px-3 py-2 shadow-sm">
                <input class="form-check-input" type="radio" v-model="paymentMethod" value="paypal" id="paypal">
                <label class="form-check-label fw-semibold ms-2" for="paypal">PayPal</label>
            </div>
            -->
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
<script>
import axios from 'axios';
import Loader from '@/components/loader.vue';
import NotificationList from '@/components/notifications.vue';
export default {
    components: {
        Loader,
        NotificationList,
    },
    data() {
        return {
            tenant_id: '',
            reservationHistory: [],
            bookingHistory: [],
            paymentModal: false,
            approveHistory: [],
            roomHistory: [],
            allHistory: [],
            totalAmount: 0.0,
            bookingpaid: 0,
            reservationpaid: 0,
            approvepaid: 0,
            percent: 0,
            paymentMethod: '',
            chooseDate: this.todayDate,
            todayDate: new Date().toISOString().split('T')[0],
            notifications: [],
            receiverID: '',
        }
    },
    methods: {
        subscribeToNotifications() {
            if (this.hasSubscribed) return;
            this.hasSubscribed = true;

            this.receiverID = this.tenant_id;
            Echo.private(`notifications.${this.tenant_id}`)
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

        async paymentHistory() {
            try {
                console.log(this.paymentMethod);
                const response = await axios.get(`/tenant/payment/history/list/${this.tenant_id}`, {
                    params: {
                        chooseDate: this.chooseDate,
                        paymentMethod: this.paymentMethod
                    }
                });
                // Tag each with its source type
                this.approveHistory = (response.data.approve_bill || []).map(item => ({
                    ...item,
                    type: 'Extension Payment'
                }));
                this.reservationHistory = (response.data.reservation_bills || []).map(item => ({
                    ...item,
                    type: 'Reservation'
                }));

                this.bookingHistory = (response.data.booking_bills || []).map(item => ({
                    ...item,
                    type: 'Booking'
                }));
                // Combine all and sort by date
                this.allHistory = [
                    ...this.approveHistory,
                    ...this.reservationHistory,
                    ...this.bookingHistory,
                ].sort((a, b) => {
                    const dateA = new Date(a.payment?.created_at || a.created_at || 0);
                    const dateB = new Date(b.payment?.created_at || b.created_at || 0);
                    return dateB - dateA;
                });
                this.getTotalAmount();


            } catch (error) {
                console.error("Error fetching payment history:", error);

            } finally {

            }
        },
        getTotalAmount() {
            axios.get(`/api/total-amount/${this.tenant_id}`, {
                params: {
                    chooseDate: this.chooseDate,
                    method: this.paymentMethod
                }
            }).then(response => {
                this.bookingpaid = response.data.bookingTotal;
                this.reservationpaid = response.data.reservationTotal;
                this.approvepaid = response.data.reservationTotal;
                this.totalAmount = response.data.totalAmount;
                this.percent = Math.min((response.data.totalAmount / 10000) * 100, 100);
            });
        },
        formatDate(date) {
            if (!date) return 'N/A';
            const d = new Date(date);
            return d.toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
        },
        formatAmount(amount) {
            if (!amount) return '0.00';
            return parseFloat(amount).toFixed(2);
        },

    },
    mounted() {
        this.tenant_id = window.tenant_id;
        this.paymentHistory();
        this.subscribeToNotifications();
    },
    watch: {
        paymentMethod(newVal) {
            console.log("Selected method:", newVal);
        }
    }
}
</script>
