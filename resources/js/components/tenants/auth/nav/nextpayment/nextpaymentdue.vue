<template>
    <NotificationList ref="toastRef" />

    <div class="container-fluid py-4">
        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Current Bills -->

                <!-- Payment History -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0">
                        <h5 class="mb-0 fw-bold text-dark">📜 Payment History</h5>
                    </div>

                    <div class="card-body" style="border:2px solid #4edce2;">
                        <!-- Wrapper with scroll and fixed height -->
                        <div style="max-height: 600px; overflow-y: auto;">
                            <!-- Payment Item -->
                            <div class="border rounded p-3 mb-3 shadow-sm" style="border:2px solid #4edce2;"
                                v-for="(history, index) in allHistory" :key="index">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <p class="mb-1 fw-semibold text-dark">
                                            {{ history.firstname }}
                                            <span v-if="history.type === 'Booking'">Booking Payment</span>
                                            <span v-else-if="history.type === 'Reservation'">Reservation Payment</span>
                                            <span v-else-if="history.type === 'Extension Payment'">Extension
                                                Payment</span>
                                            #{{ history.reservation?.room?.roomID || history.booking?.room?.roomID ||
                                                history.approved_tenant?.room?.roomID ||
                                                'N/A' }}
                                        </p>

                                        <p class="mb-0 text-muted">
                                            Payment Date: <strong>{{ formatDate(history.created_at) }}</strong>
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="mb-0 fw-bold text-dark" v-if="history.paymentAmount">
                                            ₱{{ formatAmount(history.paymentAmount) }}

                                        </p>
                                        <p class="mb-0 fw-bold  text-dark" v-if="history.amount">
                                            ₱{{ formatAmount(history.amount)
                                            }}
                                        </p>

                                    </div>
                                    <div class="col-md-2 text-md-end">
                                        <strong>{{ history.paymentType || 'N/A' }}</strong>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- End of Scrollable Area -->
                    </div>
                </div>

            </div>
            <!-- Right Column -->
            <div class="col-md-4">
                <!-- Payment Summary -->
                <div class="card mb-4 shadow-sm border-0 rounded-4" style="border:2px solid #4edce2;">
                    <div class="card-header bg-white border-bottom-0" style="border:2px solid #4edce2;">
                        <h6 class="mb-0 fw-bold text-dark">💰 Payment Summary</h6>
                    </div>

                    <div class="card-body" style="border:2px solid #4edce2;">
                        <input type="date" v-model="chooseDate" @change="paymentHistory()" class=" form-control mb-3">

                        <!-- Current Bill -->
                        <p class="fw-semibold mb-2">Current Bill:</p>
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar bg-success fw-bold" :style="{ width: percent + '%' }">
                                {{ Math.round(percent) }}%
                            </div>
                        </div>

                        <p class="mb-0 fw-semibold text-dark">
                            Total Amount This Month: <span class="text-success mb-2">₱{{ formatAmount(totalAmount)
                            }}</span>
                        </p>
                        <!-- Payment Methods -->
                        <p class="fw-semibold mb-2">Payment Methods:</p>
                        <ul class="list-unstyled">
                            <li class="form-check mb-2">
                                <input class="form-check-input" type="radio" v-model="paymentMethod" value="gcash"
                                    id="gcash" @change="paymentHistory()">

                                <label class="form-check-label" for="gcash">GCash</label>
                            </li>
                            <li class="form-check mb-2">
                                <input class="form-check-input" type="radio" v-model="paymentMethod"
                                    value="Bank Transfer" id="bdo" @change="paymentHistory()">
                                <label class="form-check-label" for="bdo">Bank Transfer</label>
                            </li>
                            <li class="form-check mb-2">
                                <input class="form-check-input" type="radio" v-model="paymentMethod" value="maya"
                                    id="maya" @change="paymentHistory()">
                                <label class="form-check-label" for="maya">Maya</label>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- Simple Modal using v-if -->

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
