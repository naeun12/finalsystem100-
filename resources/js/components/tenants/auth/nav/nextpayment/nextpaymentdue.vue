<template>
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

                    <div class="card-body">
                        <!-- Wrapper with scroll and fixed height -->
                        <div style="max-height: 600px; overflow-y: auto;">
                            <!-- Payment Item -->
                            <div class="border rounded p-3 mb-3 shadow-sm" v-for="(history, index) in allHistory"
                                :key="index">
                                <div class="row align-items-center">
                                    <div class="col-md-5">
                                        <p class="mb-1 fw-semibold text-dark">
                                            {{ history.firstname }}
                                            {{ history.type === 'booking' ? 'Booking Payment' : 'Reservation Payment' }}
                                            #{{ history.room?.roomID || 'N/A' }}
                                        </p>
                                        <p class="mb-0 text-muted">
                                            Payment Date: <strong>{{ formatDate(history.created_at) }}</strong>
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="mb-0 fw-bold text-dark">₱
                                            {{ formatAmount(history.payment?.[0]?.paymentAmount) }}
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <strong>{{ history.payment?.[0]?.paymentType || 'N/A' }}</strong>
                                    </div>
                                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                        <button class="btn btn-sm rounded-pill px-3">📄 View</button>
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
                <div class="card mb-4 shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0">
                        <h6 class="mb-0 fw-bold text-dark">💰 Payment Summary</h6>
                    </div>

                    <div class="card-body">
                        <input type="date" class="form-control mb-3" :value="todayDate">

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
                                <input class="form-check-input" type="radio" name="paymentMethod" id="gcash" checked>
                                <label class="form-check-label" for="gcash">GCash</label>
                            </li>
                            <li class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="bdo">
                                <label class="form-check-label" for="bdo">BDO Bank Transfer</label>
                            </li>
                            <li class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="maya">
                                <label class="form-check-label" for="maya">Maya</label>
                            </li>
                        </ul>
                    </div>
                </div>


                <!-- Upcoming Bills -->
                <div class="card mb-4 shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0">
                        <h6 class="mb-0 fw-bold text-dark">📅 Upcoming Bills</h6>
                    </div>

                    <div class="card-body">
                        <!-- No Bills -->
                        <p class="text-muted mb-0">No upcoming bills</p>

                        <!-- Example if naa kay bills (i-uncomment if dynamic) -->
                        <!--
    <ul class="list-group list-group-flush">
      <li class="list-group-item d-flex justify-content-between align-items-center">
        August Rental - Room #204
        <span class="badge bg-warning text-dark rounded-pill">Due: Aug 25</span>
      </li>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        Water Bill
        <span class="badge bg-warning text-dark rounded-pill">Due: Aug 20</span>
      </li>
    </ul>
    -->
                    </div>
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
            tenantId: 'afbb0302-8210-4ced-8cf0-81df2d7ebb8e',
            reservationHistory: [],
            bookingHistory: [],
            roomHistory: [],
            allHistory: [],
            totalAmount: 0.0,
            percent: 0,
            todayDate: new Date().toISOString().split('T')[0]



        }
    },
    methods: {
        async paymentHistory() {
            try {
                const response = await axios.get(`/tenant/payment/history/list/${this.tenantId}`);

                // Tag each with its source type
                this.reservationHistory = response.data.reservation_bills.map(item => ({
                    ...item,
                    type: 'reservation'
                }));
                this.bookingHistory = response.data.booking_bills.map(item => ({
                    ...item,
                    type: 'booking'
                }));

                // Combine and sort by created_at (latest first)
                this.allHistory = [...this.reservationHistory, ...this.bookingHistory].sort((a, b) => {
                    const dateA = new Date(a.payment?.created_at || 0);
                    const dateB = new Date(b.payment?.created_at || 0);
                    return dateB - dateA;
                });

            } catch (error) {
                console.error("Error fetching payment history:", error);

            } finally {

            }
        },
        paymentSummary() {

        },
        getTotalAmount() {
            axios.get(`/api/total-amount/${this.tenantId}`).then(response => {
                this.bookingPrice = response.data.bookingTotal;
                this.reservationPrice = response.data.reservationTotal;
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
        }
    },
    mounted() {
        this.paymentHistory();
        this.getTotalAmount();
    }
}
</script>
