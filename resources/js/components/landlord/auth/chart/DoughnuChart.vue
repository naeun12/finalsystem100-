<template>
    <div style="height: 300px;">
        <Doughnut :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale
} from 'chart.js';

import { Doughnut } from 'vue-chartjs';

// Register necessary Chart.js components
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale
);

// Accept props from parent
defineProps({
    chartData: Object,
    chartOptions: Object
});
</script>

<style scoped>
canvas {
    max-height: 300px;
}
</style>
