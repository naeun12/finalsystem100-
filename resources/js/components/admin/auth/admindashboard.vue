<template>
  <Loader ref="loader" />

  <div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-5 p-3 bg-light shadow-sm rounded">
      <h1 class="fw-bold text-dark mb-0" style="font-size: 1.8rem;">
        Admin Dashboard
      </h1>
      <button class="btn btn-primary d-inline-flex align-items-center px-4 py-2" @click="downloadReport"
        style="transition: transform 0.2s, box-shadow 0.2s;" @mouseover="hoverButton($event, true)"
        @mouseout="hoverButton($event, false)">
        <i class="bi bi-file-earmark-arrow-down me-2"></i>
        Download Subscription Report
      </button>
    </div>

    <div class="row g-4">
      <!-- Total Tenants -->
      <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm border-0 text-center py-4 bg-primary text-white">
          <div class="mb-3">
            <i class="bi bi-people-fill display-4"></i>
          </div>
          <h5 class="mb-2">Total Tenants</h5>
          <p class="display-6 fw-bold mb-0">{{ totalTenants }}</p>
        </div>
      </div>

      <!-- Total Landlords -->
      <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm border-0 text-center py-4 bg-success text-white">
          <div class="mb-3">
            <i class="bi bi-building display-4"></i>
          </div>
          <h5 class="mb-2">Total Landlords</h5>
          <p class="display-6 fw-bold mb-0">{{ totalLandlords }}</p>
        </div>
      </div>

      <!-- Total Collection -->
      <div class="col-lg-4 col-md-12">
        <div class="card shadow-sm border-0 text-center py-4 bg-warning text-dark">
          <div class="mb-3">
            <i class="bi bi-cash-coin display-4"></i>
          </div>
          <h5 class="mb-2">Total Collection</h5>
          <p class="display-6 fw-bold mb-0">₱{{ totalCollection }}</p>
        </div>
      </div>
    </div>

    <!-- Subscribers Graph -->
    <div class="card shadow-sm border-0 p-4 mt-5" v-if="chartData.labels.length">
      <div class="d-flex align-items-center mb-4">
        <i class="bi bi-graph-up-arrow text-primary fs-4 me-2"></i>
        <h5 class="fw-semibold mb-0">Subscribers Graph</h5>
      </div>
      <Bar :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>

<script>
import { Bar } from "vue-chartjs";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js';
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

import axios from "axios";
import Loader from '@/components/loader.vue';

export default {
  name: "AdminDashboard",
  components: { Bar, Loader },
  data() {
    return {
      totalTenants: 0,
      totalLandlords: 0,
      totalCollection: 0,
      totalSubscribers: 0,
      chartData: {
        labels: [],
        datasets: [{
          label: "Subscribers",
          data: [],
          backgroundColor: "#0d6efd",
          borderRadius: 8,
        }],
      },
      chartOptions: {
        responsive: true,
        plugins: {
          legend: { display: false },
          title: { display: true, text: "Subscribers" },
        },
        scales: { y: { beginAtZero: true } },
      },
    };
  },
  methods: {
    hoverButton(event, isHover) {
      if (isHover) {
        event.currentTarget.style.transform = "scale(1.05)";
        event.currentTarget.style.boxShadow = "0 4px 12px rgba(0,0,0,0.15)";
      } else {
        event.currentTarget.style.transform = "scale(1)";
        event.currentTarget.style.boxShadow = "none";
      }
    },
    getUsersTotal() {
      try {
        this.$refs.loader.loading = true;
        axios.get("/admin/get/total").then((response) => {
          this.totalTenants = response.data.totalTenants;
          this.totalLandlords = response.data.totalLandlords;
          this.totalCollection = response.data.totalCollection;
        });
      } catch (error) {
        console.error("Error fetching user totals:", error);
      } finally {
        this.$refs.loader.loading = false;
      }
    },
    async getTotals() {
      try {
        this.$refs.loader.loading = true;
        const response = await axios.get("/admin/get-subscribers-total");
        this.totalSubscribers = response.data.totalSubscribers;

        // Update chart with single total
        this.chartData.labels = ["Total Subscribers"];
        this.chartData.datasets[0].data = [this.totalSubscribers];
      } catch (error) {
        console.error("Error fetching totals:", error);
      } finally {
        this.$refs.loader.loading = false;
      }
    },
    downloadReport() {
      try {
        this.$refs.loader.loading = true;
        window.open("/generate-subscription-report", "_blank");
      } catch (error) {
        console.error(error);
      } finally {
        this.$refs.loader.loading = false;
      }
    }
  },
  mounted() {
    this.getTotals();
    this.getUsersTotal();
  },
};
</script>

<style scoped>
.card {
  border-radius: 14px;
  min-height: 180px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.display-5 {
  margin: 0;
}

@media (max-width: 768px) {
  .card {
    min-height: 160px;
    padding: 2rem 1rem;
  }
}
</style>
