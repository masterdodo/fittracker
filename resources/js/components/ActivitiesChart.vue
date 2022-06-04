<template>
    <div>
        <canvas id="activities-chart"></canvas>
    </div>
</template>

<script>
import Chart from "chart.js";

export default {
    name: "ActivitiesChart",
    props: {
        chartData: Object
    },
    mounted() {
        const ctx = document.getElementById("activities-chart");
        new Chart(ctx, this.chartData);
    }
};
</script>
