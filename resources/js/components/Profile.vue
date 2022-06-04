<template>
    <div class="flex-in-between-with-wrap">
        <weight-chart
            class="chart-thing"
            v-if="weightChartLoaded"
            v-bind:chartData="weightData"
        ></weight-chart>
        <activities-chart
            class="chart-thing"
            v-if="activitiesChartLoaded"
            v-bind:chartData="activitiesData"
        ></activities-chart>
    </div>
</template>

<script>
import axios from "axios";
import WeightChart from "./WeightChart.vue";
import ActivitiesChart from "./ActivitiesChart.vue";

export default {
    components: { WeightChart, ActivitiesChart },
    data() {
        return {
            weightData: {},
            weightChartLoaded: false,
            activitiesData: {},
            activitiesChartLoaded: false
        };
    },
    mounted() {
        console.log("Profile mounted.");
        this.weightChartLoaded = false;
        this.activitiesChartLoaded = false;
        axios
            .get("api/profile/chart/weight")
            .then(response => {
                console.log(response.data);
                this.weightData = {
                    type: "line",
                    data: {
                        labels: Object.keys(response.data),
                        datasets: [
                            {
                                label: "Weight",
                                data: Object.values(response.data),
                                backgroundColor: "rgba(54,73,93,.5)",
                                borderColor: "#36495d",
                                borderWidth: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        lineTension: 1
                    }
                };
                console.log(this.weightData);
                this.weightChartLoaded = true;
            })
            .catch(() => {
                console.log("Weight Problem");
            });
        axios
            .get("api/profile/chart/activities")
            .then(response => {
                console.log(response.data);
                this.activitiesData = {
                    type: "bar",
                    data: {
                        labels: Object.keys(response.data),
                        datasets: [
                            {
                                label: "Activities",
                                data: Object.values(response.data),
                                backgroundColor: "rgba(54,73,93,.5)",
                                borderColor: "#36495d",
                                borderWidth: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        lineTension: 1
                    }
                };
                console.log(this.weightData);
                this.activitiesChartLoaded = true;
            })
            .catch(() => {
                console.log("Activities Problem");
            });
    }
};
</script>
