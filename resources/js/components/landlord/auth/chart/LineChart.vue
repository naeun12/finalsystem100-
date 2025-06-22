<template>
    <Line :data="chartData" :options="chartOptions" />
</template>

<script setup>
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale
} from 'chart.js';

import { Line } from 'vue-chartjs';

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale
);

const chartData = {
    labels: ['00', '00', '00', '00', '00', '00', '00'],
    datasets: [
        {
            label: 'Data',
            data: [10, 20, 30, 40, 50, 60, 70],
            borderColor: '#2196f3',
            tension: 0.4
        }
    ]
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        y: {
            beginAtZero: true
        }
    }
};
</script>

<style scoped>
canvas {
    max-height: 300px;
}
</style>
