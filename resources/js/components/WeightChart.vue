<template>
    <div>
        <canvas id="weight-chart"></canvas>
    </div>
</template>

<script>
import Chart from "chart.js";

export default {
    name: "WeightChart",
    props: {
        chartData: Object
    },
    mounted() {
        console.log("WChart loaded");
        const ctx = document.getElementById("weight-chart");
        console.log(this.chartData);
        new Chart(ctx, this.chartData);
    }
};
</script>
