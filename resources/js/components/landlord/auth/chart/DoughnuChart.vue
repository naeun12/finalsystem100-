<template>
    <Doughnut :data="chartData" :options="chartOptions" />
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

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale
);

const chartData = {
    labels: ['Category A', 'Category B'],
    datasets: [
        {
            data: [40, 60],
            backgroundColor: ['#2196f3', '#9c27b0'],
            hoverOffset: 4
        }
    ]
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        }
    }
};
</script>

<style scoped>
canvas {
    max-height: 300px;
}
</style>
