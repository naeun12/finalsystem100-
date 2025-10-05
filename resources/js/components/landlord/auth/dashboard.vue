<template>


    <div class="main-content w-100">

        <div class="dashboard-content px-4 py-3">
            <NotificationList ref="toastRef" />
            <Loader ref="loader" />
            <div class="py-3 px-4 mb-3 bg-light border-start border-primary border-4 rounded shadow-sm">
                <h3 class="mb-1 text-primary">
                    <i class="bi bi-person-circle me-2"></i>
                    {{ landlord.firstname }} {{ landlord.lastname }}
                </h3>


                <!-- Today's Date and Generate Reports in one row -->
                <div class="d-flex align-items-center mb-3">
                    <!-- Left side: Date -->
                    <div class="d-flex align-items-center gap-2">
                        <label class=" form-label fw-bold m-0">📅 Today's Date:</label>
                        <input type="date" class="form-control w-auto" style="border: 1px solid #4edce2; min-width: 200px;"
                            v-model="newDate" :max="today">
                    </div>

                    <!-- Right side: Generate Reports -->
                    <!-- Right side: Generate Reports -->
                    <div class="ms-auto d-flex align-items-center gap-2">
                       
                        <!-- Button with icon -->
                        <a :href="`/generate-full-report/${landlord_id}?date=${newDate}`" target="_blank"
                            class="btn btn-outline-success btn-lg" :class="{ 'disabled': !newDate }">
                            📄 Download Full Report
                        </a>

                    </div>

                </div>
            </div>



            <div class="row">
                <!-- Total Tenants -->
                <div class="col-md-6 mb-3">
                    <a :href="`/all-tenants-index/${landlord_id}`" class="text-decoration-none">
                        <div class="card shadow-sm border-start border-primary border-4 h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-1 text-primary">Total Tenants</h5>
                                    <p class="card-text display-6 fw-bold mb-0 text-primary">{{ totalTenants }}</p>
                                </div>
                                <div class="ms-3">
                                    <i class="bi bi-people-fill fs-1 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>



                <!-- Vacant Rooms -->
                <div class="col-md-6 mb-3">
                    <a :href="`/landlordRoomManagement/${landlord_id}`" class="text-decoration-none">
                        <div class="card shadow-sm border-start border-success border-4 h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-1 text-success">Vacant Beds</h5>
                                    <p class="card-text display-6 fw-bold mb-0 text-success">{{ availableBeds }}</p>
                                </div>
                                <div class="ms-3">
                                    <i class="bi bi-door-open-fill fs-1 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>


                <div class="charts d-flex flex-wrap gap-3 justify-content-between mb-4">
                    <!-- Left Chart -->
                    <div class="chart-container p-3 border rounded shadow-sm"
                        style="flex: 1 1 45%; max-width: 45%; min-width: 250px;">
                        <h6 class="fw-bold mb-2">📈 Highest Dorm Profits</h6>
                        <p class="fs-6 text-success mb-3">₱{{ totalDormProfit }}</p>
                        <LineChart v-if="chartData" :chart-data="chartData" :chart-options="chartOptions" />
                    </div>

                    <!-- Right Chart -->
                    <!-- Right Chart Container -->
                    <!-- Right Chart Container -->
                    <div class="chart-container p-3 border rounded shadow-sm"
                        style="flex: 1 1 45%; max-width: 45%; min-width: 250px;">
                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-cash-stack me-2"></i> Profits Per Dorm
                        </h6>
                        <DoughnutChart v-if="bookingChartData" :chart-data="bookingChartData"
                            :chart-options="bookingChartOptions" />



                        <!-- Legend -->
                        <div class="legend mt-3"
                            v-if="bookingChartData && bookingChartData.labels && bookingChartData.datasets && bookingChartData.datasets.length">
                            <div class="legend-item d-flex justify-content-between align-items-center mb-1"
                                v-for="(label, index) in bookingChartData.labels" :key="index">
                                <span class="dot me-2" :style="{
                                    width: '10px',
                                    height: '10px',
                                    backgroundColor: bookingChartData.datasets[0].backgroundColor[index],
                                    borderRadius: '50%',
                                    display: 'inline-block'
                                }"></span>
                                <span class="flex-grow-1 small">{{ label }}</span>
                                <span class="small">
                                    {{
                                    calculatePercentage(
                                    bookingChartData.datasets[0].data[index],
                                    bookingChartData.datasets[0].data
                                    )
                                    }}%
                                </span>
                            </div>
                        </div>
                    </div>


                </div>

                <!-- Recent Bookings -->
                <div class="col-md-6 mb-3">
                    <a :href="`/booking-index/${landlord_id}`" class="text-decoration-none">
                        <div class="card shadow-sm border-start border-info border-4 h-100">
                            <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                                <h5 class="mb-0 text-info">
                                    <i class="bi bi-calendar-check-fill me-2"></i>
                                    Recent Bookings
                                </h5>
                                <span class="badge bg-info text-white">Updated</span>
                            </div>
                            <div class="card-body">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Move-In</th>
                                            <th>Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="booking in bookings.slice(0, 3)" :key="booking.bookingID">
                                            <td><strong>{{ booking.firstname }} {{ booking.lastname }}</strong></td>
                                            <td><span class="text-muted">{{ booking.moveInDate }}</span></td>
                                            <td>
                                                <span class="badge bg-primary px-3 py-2">
                                                    Room {{ booking.room?.roomNumber ?? 'N/A' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 mb-3">
                    <a :href="`/reservation-index/${landlord_id}`" class="text-decoration-none">
                        <div class="card shadow-sm border-start border-warning border-4 h-100">
                            <div class="card-header bg-transparent d-flex align-items-center">
                                <h5 class="mb-0 text-warning">
                                    <i class="bi bi-person-plus-fill me-2"></i>
                                    Recent Reservations
                                </h5>
                            </div>

                            <div class="card-body">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="tenant in reservations.slice(0, 3)" :key="tenant.reservationID">
                                            <td><strong>{{ tenant.firstname }} {{ tenant.lastname }}</strong></td>
                                            <td>
                                                <span class="badge bg-primary px-3 py-2">
                                                    Room {{ tenant.room?.roomNumber ?? 'N/A' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </a>
                </div>




            </div>

        </div>


    </div>



</template>
<script>
import axios from 'axios';
import LineChart from './chart/LineChart.vue';
import DoughnutChart from './chart/DoughnuChart.vue';
import Loader from '@/components/loader.vue';
import NotificationList from '@/components/notifications.vue';
import { Title } from 'chart.js';



export default
    {
        components: {
            LineChart,
            DoughnutChart,
            Loader,
            NotificationList

        },
        data() {
            return {
                receiverID: '',
                hasSubscribed: false,
                landlord_id: '',
                landlord: [],
                reservations: [],
                notifications: [],
                rooms: [],
                bookings: [],
                newDate: '',
                today: '',
                totalTenants: 0,
                availableBeds: 0,
                totalDormProfit: 0,
                chartData: null,
                chartOptions: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
                bookingChartData: null,
                bookingChartOptions: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            }
        },

        mounted() {
            const element = document.getElementById('dashboard');
            this.landlord_id = element.dataset.landlordId;
            this.receiverID = this.landlord_id;  // set receiverID here, early
            this.subscribeToNotifications();
            this.today = this.getTodayDate();
            this.newDate = this.today; // default value to today
            this.getLandlord();

        },
        methods:
        {
            subscribeToNotifications() {
                if (this.hasSubscribed) return; // prevent multiple subscriptions
                this.hasSubscribed = true;

                this.receiverID = this.landlord_id;
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
            async getLandlord() {
                try {
                    this.$refs.loader.loading = true;

                    const response = await axios.get(`/get/landlord/${this.landlord_id}`);

                    this.landlord = response.data.landlord;
                    await Promise.all([
                        this.getTotalTenants(),
                        this.getAvailableBeds(),
                        this.getTenantsList(),
                        this.getBookingList(),
                        this.getDormProfits(),
                        this.getAllprofits()
                    ]);

                }
                catch (error) {
                    console.log(error);
                }
                finally {
                    this.$refs.loader.loading = false;

                }

            },
            async getTotalTenants() {
                try {
                    const response = await axios.get(`/get/total-tenants/${this.landlord_id}`, {
                        params: { date: this.newDate }
                    });
                    this.totalTenants = response.data.total_tenants;
                } catch (error) {
                    console.error('Failed to fetch total tenants:', error);
                }
            },
            async getAvailableBeds() {
                try {
                    const response = await axios.get(`/get/available-beds/${this.landlord_id}`, {
                        params: { date: this.newDate }
                    });
                    this.availableBeds = response.data.available_beds;
                } catch (error) {
                    console.error('Failed to fetch available beds:', error);
                }
            },
            async getTenantsList() {
                try {
                    const response = await axios.get(`/get/reservation-list/${this.landlord_id}`, {
                        params: { date: this.newDate }
                    });
                    this.reservations = response.data.reservations;
                } catch (error) {
                    console.error('Failed to fetch tenant list:', error);
                }
            },
            async getBookingList() {
                try {
                    const response = await axios.get(`/get/booking-list/${this.landlord_id}`, {
                        params: { date: this.newDate }
                    });
                    this.bookings = response.data.bookings;
                } catch (error) {
                    console.error('Failed to fetch booking list:', error);
                }
            },
            async getDormProfits() {
                try {
                    const response = await axios.get(`/get/dorm-profits/${this.landlord_id}`, {
                        params: { date: this.newDate }
                    });
                    const dorms = response.data.data;

                    this.chartData = {
                        labels: dorms.map(d => d.dormName),
                        datasets: [
                            {
                                label: 'Dorm Profits',
                                data: dorms.map(d => d.profit),
                                borderColor: '#2196f3',
                                tension: 0.4,
                                fill: false
                            }
                        ]
                    };

                    this.totalDormProfit = response.data.total_profit; // ← set this

                } catch (error) {
                    console.error('Error fetching dorm profits:', error);
                }
            },
            async getAllprofits() {
                // Optional: show loading state
                try {
                    this.$refs.loader.loading = true;

                    const response = await axios.get(`/get/all-profits/${this.landlord_id}`, {
                        params: { date: this.newDate }
                    });

                    // ✅ Safe guard — make sure we always have an array
                    const bookings = Array.isArray(response.data?.data) ? response.data.data : [];

                    if (bookings.length === 0) {
                        // Fallback data to show an empty chart
                        this.bookingChartData = {
                            labels: ["No Data"],
                            datasets: [
                                {
                                    label: "Profits",
                                    data: [1], // a dummy value so the chart renders
                                    backgroundColor: ["#e0e0e0"],
                                    hoverOffset: 4
                                }
                            ]
                        };
                        this.totalDormProfit = 0;
                        return;
                    }


                    const labels = bookings.map(item => item.dormName ?? "Unknown Dorm");
                    const data = bookings.map(item => Number(item.totalProfit) || 0);

                    const backgroundColors = [
                        "#2196f3", "#9c27b0", "#ff9800", "#4caf50", "#e91e63", "#00bcd4",
                        "#795548", "#607d8b", "#ffc107", "#8bc34a"
                    ];

                    this.bookingChartData = {
                        labels,
                        datasets: [
                            {
                                label: "Profits",
                                data,
                                backgroundColor: backgroundColors.slice(0, data.length),
                                hoverOffset: 4
                            }
                        ]
                    };

                    // ✅ Always calculate safely
                    this.totalDormProfit = data.reduce((acc, val) => acc + val, 0);

                } catch (error) {
                    console.error("❌ Failed to fetch booking profits:", error);
                    // Reset state on error
                    this.bookingChartData = { labels: [], datasets: [] };
                    this.data = 0;
                } finally {
                    // Optional: hide loading state
                    this.$refs.loader.loading = false;
                }
            },

            calculatePercentage(value, dataArray) {
                const total = dataArray.reduce((sum, val) => sum + val, 0);
                if (total === 0) return 0;
                return ((value / total) * 100).toFixed(1);
            },
            getTodayDate() {
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}`;
            },
            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleString();
            }


        },
        watch: {
            newDate(newVal) {
                if (newVal) {
                    this.getLandlord();
                }
            },
            landlord_id(newVal) {
                if (newVal) {
                    this.subscribeToNotifications();
                }
            }
        }

    }

</script>
