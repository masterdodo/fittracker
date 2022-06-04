<template>
    <section id="routines">
        <h2>Routines</h2>
        <button
            v-on:click="addRoutineDialog"
            class="btn"
            id="add-routine-dialog-button"
        >
            <i class="fa fa-plus"></i> Add Routine
        </button>
        <vue-simple-spinner
            v-if="loading"
            size="large"
            message="Loading routines..."
        />
        <transition name="fade">
            <div v-if="allRoutines.length != 0" id="routine-list">
                <routine
                    ref="routine{{routine.id}}"
                    v-for="routine in allRoutines"
                    v-bind:key="routine.id"
                    v-bind:id="routine.id"
                    v-bind:name="routine.name"
                    v-bind:type="routine.type"
                    v-bind:routinesReload="loadRoutinesAfterAdd"
                ></routine>
            </div>
        </transition>
        <!-- ADD DIALOG MODAL -->
        <div id="add-routine-dialog" class="modal">
            <div class="modal-content">
                <input type="hidden" name="id" id="update-routine-id" />
                <div class="flex-in-between">
                    <p class="modal-title" id="add-routine-title">
                        New Routine
                    </p>
                    <button
                        class="btn"
                        id="new-routine-clear"
                        @click="clearNewRoutine"
                    >
                        <i class="fa fa-eraser"></i> Erase all
                    </button>
                </div>
                <p class="txt-red" id="new-routine-error"></p>
                <div class="form_group field">
                    <input
                        type="text"
                        class="form_field"
                        placeholder="Name"
                        name="routine_name"
                        id="routine_name"
                    />
                    <label for="routine_name" class="form_label">Name</label>
                </div>
                <div class="form_group field">
                    <select
                        name="exercise_type"
                        id="exercise_type"
                        class="form_select"
                    >
                        <option value="Repetition">Repetition</option>
                        <option value="Duration">Duration</option>
                    </select>
                </div>
                <div class="form_group field">
                    <input
                        type="hidden"
                        name="exercise_set_id_1"
                        id="exercise_set_id_1"
                    />
                    <input
                        type="text"
                        class="form_field"
                        placeholder="Exercise 1"
                        name="exercise_set_name_1"
                        id="exercise_set_name_1"
                    />
                    <label for="exercise_set_name_1" class="form_label"
                        >Exercise 1</label
                    >
                </div>
                <button
                    class="btn form_btn"
                    id="add-exercise-button"
                    @click="addExerciseToDialog"
                >
                    <i class="fa fa-plus"></i> Add Exercise
                </button>
                <button
                    v-on:click="saveRoutine"
                    class="btn btn-green"
                    id="save-routine-button"
                >
                    <i class="fa fa-save"></i> Save
                </button>
                <button
                    v-on:click="updateRoutine"
                    class="btn btn-green hidden"
                    id="update-routine-button"
                >
                    <i class="fa fa-save"></i> Update
                </button>
            </div>
        </div>
    </section>
</template>

<script>
import axios from "axios";
import VueSimpleSpinner from "vue-simple-spinner";

export default {
    methods: {
        loadRoutines() {
            this.loading = true;
            axios.get("api/routine/all").then(response => {
                this.allRoutines = [];
                this.allRoutines = response.data;
                this.loading = false;
            });
        },
        loadRoutinesAfterAdd() {
            this.allRoutines = [];
            axios.get("api/routine/all").then(response => {
                this.allRoutines = response.data;
            });
        },
        clearNewRoutine() {
            this.removeAdditionalExercises();
            document.getElementById("routine_name").value = "";
            document.getElementById("exercise_set_id_1").value = "";
            document.getElementById("exercise_set_name_1").value = "";
            newRoutineErrorBox.innerHTML = "";
        },
        removeAdditionalExercises() {
            let ex = document.getElementById("exercise_set_name_1")
                .parentElement;
            ex = ex.nextElementSibling;
            while (ex.tagName == "DIV") {
                let temp = ex.nextElementSibling;
                ex.remove();
                ex = temp;
            }
        },
        addExerciseToDialog() {
            const lastExerciseNum =
                parseInt(
                    addExerciseButton.previousElementSibling.firstElementChild
                        .getAttribute("id")
                        .split("_")[3]
                ) + 1;

            const container = document.createElement("div");
            container.setAttribute("class", "form_group field flex-in-between");
            const idInput = document.createElement("input");
            idInput.setAttribute("type", "hidden");
            idInput.setAttribute("name", "exercise_set_id_" + lastExerciseNum);
            idInput.setAttribute("id", "exercise_set_id_" + lastExerciseNum);
            const input = document.createElement("input");
            input.setAttribute("type", "text");
            input.setAttribute("class", "form_field");
            input.setAttribute("placeholder", "Exercise " + lastExerciseNum);
            input.setAttribute("name", "exercise_set_name_" + lastExerciseNum);
            input.setAttribute("id", "exercise_set_name_" + lastExerciseNum);
            const label = document.createElement("label");
            label.setAttribute("for", "exercise_set_name_" + lastExerciseNum);
            label.setAttribute("class", "form_label");
            label.innerHTML = "Exercise " + lastExerciseNum;
            const delBtn = document.createElement("button");
            delBtn.setAttribute("class", "btn-clear btn-red");
            delBtn.setAttribute(
                "onclick",
                "removeExercise(" + lastExerciseNum + ")"
            );
            const delBtnIcon = document.createElement("i");
            delBtnIcon.setAttribute("class", "fa fa-trash");
            delBtn.appendChild(delBtnIcon);
            container.appendChild(idInput);
            container.appendChild(input);
            container.appendChild(label);
            container.appendChild(delBtn);
            addExerciseButton.before(container);
        },
        addRoutineDialog() {
            document.getElementById("add-routine-dialog").style.display =
                "block";
            document.body.style.overflow = "hidden";
        },
        saveRoutine() {
            const clearRoutineValues = getRoutineValuesCheck();
            if (!clearRoutineValues) {
                return;
            }
            axios
                .post("api/routine/add", clearRoutineValues)
                .then(() => {
                    clearNewRoutine();
                    routineModalClosed();
                    this.$message({
                        type: "success",
                        message: "Routine added successfully"
                    });
                    this.loadRoutinesAfterAdd();
                })
                .catch(error => {
                    if (error.response.status == 400) {
                        document.getElementById("new-routine-error").innerHTML =
                            error.response.data.message;
                    } else if (error.response.status == 422) {
                        Object.keys(error.response.data.errors).forEach(el => {
                            let x = error.response.data.errors[el];
                            const regex = new RegExp("(exercises\.)[0-9]+");
                            if (regex.test(error.response.data.errors[el])) {
                                x =
                                    "The Exercise " +
                                    (parseInt(el.split(".")[1], 10) + 1) +
                                    " format is invalid.";
                            }
                            document.getElementById(
                                "new-routine-error"
                            ).innerHTML =
                                document.getElementById("new-routine-error")
                                    .innerHTML +
                                "<br>" +
                                x;
                        });
                    }

                    this.$message({
                        type: "error",
                        message: error.response.data.message
                    });
                });
        },
        updateRoutine() {
            const clearRoutineValues = getRoutineUpdateValuesCheck();
            if (!clearRoutineValues) {
                return;
            }
            axios
                .post(
                    "api/routine/edit/" + clearRoutineValues.routineId,
                    clearRoutineValues
                )
                .then(response => {
                    clearNewRoutine();
                    routineModalClosed();
                    this.$message({
                        type: "success",
                        message: "Routine updated successfully"
                    });
                    this.loadRoutinesAfterAdd();
                })
                .catch(error => {
                    if (error.response.status == 400) {
                        document.getElementById("new-routine-error").innerHTML =
                            error.response.data.message;
                    } else if (error.response.status == 422) {
                        Object.keys(error.response.data.errors).forEach(el => {
                            let x = error.response.data.errors[el];
                            const regex = new RegExp("(exercises\.)[0-9]+");
                            if (regex.test(error.response.data.errors[el])) {
                                x =
                                    "The Exercise " +
                                    (parseInt(el.split(".")[1], 10) + 1) +
                                    " format is invalid.";
                            }
                            document.getElementById(
                                "new-routine-error"
                            ).innerHTML =
                                document.getElementById("new-routine-error")
                                    .innerHTML +
                                "<br>" +
                                x;
                        });
                    }

                    this.$message({
                        type: "error",
                        message: error.response.data.message
                    });
                });
        }
    },
    components: {
        VueSimpleSpinner
    },
    data() {
        return {
            loading: false,
            allRoutines: []
        };
    },
    mounted() {
        this.loadRoutines();
    }
};
</script>
