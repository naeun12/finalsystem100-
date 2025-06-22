<template>
    <div class="p-3">
        <h1>Analytics</h1>
        <div class="info">
            <p>Todays Date: {{ todayDate }}</p>
            <p>This month's income: {{ thisMonthsIncome }}</p>
        </div>
        <div class="charts">
            <div class="chart-container left">
                <h3>Highest Dorm Profits</h3>
                <p>00</p>
                <LineChart />
            </div>
            <div class="chart-container right">
                <h3>Booking Profits</h3>
                <p>00</p>
                <DoughnutChart />
                <div class="legend">
                    <div class="legend-item">
                        <span class="dot" style="background-color: #9c27b0;"></span>
                        <span>Key title goes here</span>
                        <span>00%</span>
                    </div>
                    <div class="legend-item">
                        <span class="dot" style="background-color: #2196f3;"></span>
                        <span>Key title goes here</span>
                        <span>00%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LineChart from './chart/LineChart.vue';
import DoughnutChart from './chart/DoughnuChart.vue';

export default {
    components: {
        LineChart,
        DoughnutChart,
    },
    data() {
        return {
            todayDate: '2/14/2025',
            thisMonthsIncome: '200000',
        };
    },
};
</script>

<style scoped>
.container {
    font-family: Arial, sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.charts {
    display: flex;
    gap: 20px;
}

.chart-container {
    flex: 1;
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
}

.left {
    width: 50%;
}

.right {
    width: 50%;
}

.legend {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
</style>