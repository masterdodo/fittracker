<template>
    <div class="activity-body-div-routine">
        <vue-simple-spinner v-if="loading" size="medium" />
        <el-card v-if="!loading" class="box-card">
            <div slot="header" class="clearfix">
                <span>{{ name + " " + sets }}&nbsp;&nbsp;&nbsp;</span>
                <el-popconfirm
                    confirm-button-text="OK"
                    cancel-button-text="No, Thanks"
                    icon="el-icon-info"
                    icon-color="red"
                    title="Are you sure you want to delete this?"
                    @confirm="deleteRoutineActivity(routine.id)"
                >
                    <el-button
                        class="btn-red"
                        style="float: right; padding: 3px 0"
                        type="text"
                        slot="reference"
                        ><i class="fa fa-trash"></i
                    ></el-button>
                </el-popconfirm>
            </div>
            <div v-for="ex in exercises" :key="ex.id" class="text item">
                <b>{{ ex.name }}</b
                >: {{ ex.amount }}
                {{ ex.unit == null ? "reps" : ex.unit }}
            </div>
        </el-card>
    </div>
</template>

<script>
import axios from "axios";

export default {
    props: {
        routine: Object,
        reloadDayActs: Function
    },
    data() {
        return {
            loading: false,
            name: "",
            sets: "",
            exercises: []
        };
    },
    methods: {
        deleteRoutineActivity(id) {
            axios
                .get("api/activity/routine/delete/" + id)
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
            this.reloadDayActs();
        }
    },
    mounted() {
        this.loading = true;
        axios
            .get("api/activity/routine/" + this.routine.id)
            .then(response => {
                this.name = response.data.routine_id.name;
                this.sets = "(x" + response.data.sets + ")";
                this.exercises = response.data.exercises;
                this.loading = false;
            })
            .catch(error => {});
    }
};
</script>
