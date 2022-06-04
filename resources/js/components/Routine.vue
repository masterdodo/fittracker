<template>
    <div class="routine">
        <p>
            {{ name }}:
            {{ this.exercisesString.substring(0, 30 - name.length) + "..." }}
        </p>
        <div>
            <button @click="editRoutine" class="btn-clear btn-blue">
                <i class="fa fa-edit"></i>
            </button>
            <button v-on:click="openDeleteDialog" class="btn-clear btn-red">
                <i class="fa fa-trash"></i>
            </button>
        </div>
        <el-dialog title="Tips" :visible.sync="dialogVisible" width="40%">
            <span>This is a message</span>
            <span slot="footer" class="dialog-footer">
                <el-button @click="dialogVisible = false">Cancel</el-button>
                <el-button type="primary" @click="dialogVisible = false"
                    >Confirm</el-button
                >
            </span>
        </el-dialog>
    </div>
</template>

<script>
import axios from "axios";
export default {
    props: {
        id: Number,
        name: String,
        type: String,
        routinesReload: Function
    },
    data() {
        return {
            exercises: [],
            exercisesString: "",
            dialogVisible: false
        };
    },
    methods: {
        getExercises() {
            axios.get("api/exercise/" + this.id).then(response => {
                response.data.forEach(el => {
                    this.exercises.push({ id: el.id, name: el.name });
                    this.exercisesString += " " + el.name + ",";
                });
                if (this.exercisesString.slice(-1) == ",") {
                    this.exercisesString = this.exercisesString.substring(
                        0,
                        this.exercisesString.length - 1
                    );
                }
            });
        },
        editRoutine() {
            addRoutineTitle.innerHTML = "Update Routine";
            saveRoutineButton.setAttribute("class", "hidden");
            newRoutineClear.setAttribute("class", "hidden");
            updateRoutineButton.setAttribute("class", "btn btn-green");
            document.getElementById("update-routine-id").value = this.id;
            document.getElementById("routine_name").value = this.name;
            document.getElementById("exercise_type").value = this.type;
            document.getElementById(
                "exercise_set_id_1"
            ).value = this.exercises[0].id;
            document.getElementById(
                "exercise_set_name_1"
            ).value = this.exercises[0].name;
            for (let i = 1; i < this.exercises.length; i++) {
                addExerciseToDialog();
                addExerciseButton.previousElementSibling.firstElementChild.value = this.exercises[
                    i
                ].id;
                addExerciseButton.previousElementSibling.firstElementChild.nextElementSibling.value = this.exercises[
                    i
                ].name;
            }
            addRoutineDialogModal.style.display = "block";
        },
        openDeleteDialog() {
            this.$confirm(
                "This will permanently delete the routine and all activities related to it. Continue?",
                "Warning",
                {
                    confirmButtonText: "OK",
                    cancelButtonText: "Cancel",
                    type: "warning"
                }
            )
                .then(() => {
                    this.deleteRoutine();
                })
                .catch(() => {
                    this.$message({
                        type: "info",
                        message: "Delete canceled"
                    });
                });
        },
        deleteRoutine() {
            axios
                .get("api/routine/delete/" + this.id)
                .then(response => {
                    this.$message({
                        type: "success",
                        message: "Delete completed"
                    });
                    this.routinesReload();
                })
                .catch(err => {
                    this.$message({
                        type: "error",
                        message: "Error while deleting this routine"
                    });
                });
        }
    },
    mounted() {
        this.getExercises();
    }
};
</script>
