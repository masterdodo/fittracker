<template>
    <div :id="this.date" class="activity-day">
        <div class="activity-head-div">
            <h2>
                <button v-on:click="openModal(date)" class="btn btn-clear">
                    <i class="fa fa-plus"></i>
                </button>
                {{ this.formatedDate }}
                <span
                    v-if="this.feeling.feeling"
                    v-on:click="deleteFeeling(feeling.id)"
                >
                    {{ this.feelings[this.feeling.feeling] }}</span
                >
            </h2>
            <p
                v-if="this.weight.weight"
                class="activity-head-div-weight"
                v-on:click="deleteWeight(weight.id)"
            >
                {{ this.weight.weight + "kg" }}
            </p>
        </div>
        <div v-if="this.routineActivities" class="activity-body-div-routines">
            <routine-activity
                v-for="routineActivity in routineActivities"
                v-bind:key="routineActivity.id"
                v-bind:routine="routineActivity"
                v-bind:reloadDayActs="reloadActivities"
            ></routine-activity>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    props: {
        date: String,
        search: Boolean,
        searchType: String,
        query: String,
        openModal: Function,
        reloadActivities: Function
    },
    data() {
        return {
            feelings: {
                "feeling-5": "😄",
                "feeling-4": "🙂",
                "feeling-3": "😐",
                "feeling-2": "🙁",
                "feeling-1": "😥"
            },
            formatedDate: String,
            activities: [],
            weight: Object,
            feeling: Object,
            routineActivities: []
        };
    },
    methods: {
        deleteWeight(id) {
            axios
                .get("api/activity/weight/delete/" + id)
                .then(response => {
                    this.$message({
                        type: "success",
                        message: "Activity deleted"
                    });
                })
                .catch(error => {
                    this.$message({
                        type: "error",
                        message: "Problem deleting activity"
                    });
                });
            this.reloadDay();
            this.reloadActivities();
        },
        deleteFeeling(id) {
            axios
                .get("api/activity/feeling/delete/" + id)
                .then(response => {
                    this.$message({
                        type: "success",
                        message: "Activity deleted"
                    });
                })
                .catch(error => {
                    this.$message({
                        type: "error",
                        message: "Problem deleting activity"
                    });
                });
            this.reloadDay();
            this.reloadActivities();
        },
        reloadDay() {
            const realDate = new Date(this.date);
            const day = realDate.getDate();
            const month = realDate.toLocaleString("default", { month: "long" });
            const year = realDate.getFullYear();
            const combinedDate =
                day +
                ". " +
                month +
                " " +
                (year != new Date().getFullYear() ? year : "");
            this.formatedDate = combinedDate;
            this.weight = {};
            this.feeling = {};
            this.routineActivities = [];
            if (this.search && this.searchType == "routine") {
                if (this.query != "") {
                    axios
                        .post(
                            "api/activity/date/search-routine/" + this.query,
                            { date: this.date }
                        )
                        .then(response => {
                            this.activities = response.data;
                            this.activities.forEach(act => {
                                if ("weight" in act) {
                                    this.weight = act;
                                } else if ("feeling" in act) {
                                    this.feeling = act;
                                } else {
                                    this.routineActivities.push(act);
                                }
                            });
                        })
                        .catch(() => {
                            this.$message({
                                type: "error",
                                message: "Problem loading routines"
                            });
                        });
                    return;
                }
            }
            axios
                .post("api/activity/all/date", { date: this.date })
                .then(response => {
                    this.activities = response.data;
                    this.activities.forEach(act => {
                        if ("weight" in act) {
                            this.weight = act;
                        } else if ("feeling" in act) {
                            this.feeling = act;
                        } else {
                            this.routineActivities.push(act);
                        }
                    });
                })
                .catch(() => {
                    this.$message({
                        type: "error",
                        message: "Problem loading activities"
                    });
                });
        }
    },
    mounted() {
        this.reloadDay();
    }
};
</script>
