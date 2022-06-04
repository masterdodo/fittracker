<template>
    <section id="activities">
        <h2>Activities</h2>
        <button
            @click="openAddActivityModal"
            class="btn"
            id="add-activity-dialog-button"
        >
            <i class="fa fa-plus"></i> Add Activity
        </button>
        <el-badge :value="streak" class="item">
            <el-button size="small" type="danger" icon="el-icon-star-on"
                >Streak</el-button
            >
        </el-badge>
        <div class="search-bar-activities flex-in-between">
            <el-button
                @click="searchOn = !searchOn"
                icon="el-icon-search"
                circle
            ></el-button>
            <transition name="slide-fade">
                <el-input
                    @keyup.native="searchDelay"
                    class="ui-el-search-input"
                    v-if="searchOn"
                    :placeholder="searchPlaceholder"
                    suffix-icon="search"
                    v-model="filter"
                />
            </transition>
            <transition name="slide-fade">
                <el-select
                    class="ui-el-search-input"
                    v-model="searchValue"
                    placeholder="Select"
                    v-if="searchOn"
                >
                    <el-option
                        v-for="item in options"
                        :key="item.value"
                        :label="item.label"
                        :value="item.value"
                    >
                    </el-option>
                </el-select>
            </transition>
        </div>
        <vue-simple-spinner
            v-if="loading"
            size="large"
            message="Loading activities..."
        />
        <div v-if="dates.length != 0" id="activities-list">
            <dateActivities
                v-for="(numOfDates, date) in dates"
                v-bind:key="date + numOfDates"
                v-bind:date="date"
                v-bind:search="searchOn"
                v-bind:searchType="searchValue"
                v-bind:query="filter"
                v-bind:openModal="openAddActivityModal"
                v-bind:reloadActivities="loadActivitiesAfterAdd"
            ></dateActivities>
        </div>

        <!-- ADD DIALOG MODAL -->
        <div id="add-activity-dialog" class="modal">
            <div class="modal-content">
                <div class="flex-in-between">
                    <p class="modal-title">New Activity</p>
                    <button class="btn" id="new-activity-clear">
                        <i class="fa fa-eraser"></i> Erase all
                    </button>
                </div>
                <p class="txt-red" id="new-activity-error"></p>
                <div class="form_group field flex-in-between">
                    <date-picker
                        format="DD.MM.YYYY"
                        v-model="time1"
                        prefix-class="xmx"
                        :lang="lang"
                        :disabled-date="disableTime"
                        :default-value="defaultDate"
                        @change="activityDateChanged"
                    ></date-picker>
                    <input
                        type="hidden"
                        name="activity-date-text"
                        id="activity-date-text"
                        :value="this.activity_date"
                    />
                </div>
                <div class="form_group field">
                    <select
                        @change="activityTypeChanged"
                        name="activity-type"
                        id="activity-type"
                        class="form_select"
                    >
                        <option value="Weight">Weight</option>
                        <option value="Feeling">Feeling</option>
                        <option value="Routine">Routine</option>
                    </select>
                </div>
                <div id="activity-type-weight">
                    <div class="form_group field">
                        <input
                            class="form_field"
                            type="number"
                            min="0"
                            step=".1"
                            placeholder="Weight (kg)"
                            name="activity-weight"
                            id="activity-weight"
                        />
                        <label for="activity-weight" class="form_label"
                            >Weight (kg)</label
                        >
                    </div>
                </div>
                <div id="activity-type-feeling">
                    <label for="feeling-5"
                        ><input
                            type="radio"
                            name="activity-feeling"
                            id="feeling-5"
                            value="feeling-5"
                            checked
                        />&#128516;</label
                    >
                    <label for="feeling-4"
                        ><input
                            type="radio"
                            name="activity-feeling"
                            id="feeling-4"
                            value="feeling-4"
                        />&#128578;</label
                    >
                    <label for="feeling-3"
                        ><input
                            type="radio"
                            name="activity-feeling"
                            id="feeling-3"
                            value="feeling-3"
                        />&#128528;</label
                    >
                    <label for="feeling-2"
                        ><input
                            type="radio"
                            name="activity-feeling"
                            id="feeling-2"
                            value="feeling-2"
                        />&#128577;</label
                    >
                    <label for="feeling-1"
                        ><input
                            type="radio"
                            name="activity-feeling"
                            id="feeling-1"
                            value="feeling-1"
                        />&#128549;</label
                    >
                </div>
                <div id="activity-type-routine">
                    <div class="form_group field">
                        <select
                            @change="activityRoutineChanged"
                            name="activity-routine"
                            id="activity-routine"
                            class="form_select"
                        ></select>
                    </div>
                    <div class="form_group field">
                        <input
                            type="number"
                            min="1"
                            class="form_field"
                            placeholder="Sets"
                            name="activity-routine-sets"
                            id="activity-routine-sets"
                        />
                        <label class="form_label" for="activity-routine-sets"
                            >Sets</label
                        >
                    </div>
                    <div id="activity-routine-main"></div>
                </div>
                <button
                    @click="saveActivity"
                    class="btn btn-green"
                    id="save-activity-button"
                >
                    <i class="fa fa-save"></i> Add
                </button>
            </div>
        </div>
    </section>
</template>

<script>
import axios from "axios";
import lodash from "lodash";
import VueSimpleSpinner from "vue-simple-spinner";
import DatePicker from "vue2-datepicker";

export default {
    data() {
        return {
            lang: {
                formatLocale: {
                    firstDayOfWeek: 1
                },
                monthBeforeYear: true
            },
            searchOn: false,
            options: [
                { label: "Date", value: "date" },
                { label: "Routine", value: "routine" }
            ],
            streak: null,
            searchValue: "routine",
            filter: "",
            time1: null,
            loading: false,
            dates: [],
            allRoutines: [],
            rawActivities: [],
            activity_date: "",
            addActivityDialogModal: HTMLElement,
            addActivityDialogButton: HTMLElement,
            newActivityErrorBox: HTMLElement,
            activityType: HTMLElement,
            activityTypeWeight: HTMLElement,
            activityTypeFeeling: HTMLElement,
            activityTypeRoutine: HTMLElement,
            activityDate: HTMLElement,
            activityDateText: HTMLElement,
            activityFeelingRadios: HTMLElement,
            addActivityRoutines: HTMLElement,
            saveActivityButton: HTMLElement,
            newActivityClear: HTMLElement,
            activityFeelingLabels: HTMLElement,
            activityRoutineMain: HTMLElement,
            activityWeight: HTMLElement,
            activityRoutineSets: HTMLElement,
            activitiesList: HTMLElement
        };
    },
    watch: {
        searchOn: function(newVal, oldVal) {
            if (newVal == false) {
                this.loadActivitiesAfterAdd();
                this.searchValue = "routine";
                this.filter = "";
            }
        }
    },
    computed: {
        searchPlaceholder: function() {
            return this.searchValue == "date"
                ? "Filter by Date"
                : "Filter by Routine";
        }
    },
    components: {
        VueSimpleSpinner,
        DatePicker
    },
    methods: {
        searchDelay: _.debounce(function() {
            if (this.searchOn == false) {
                return;
            }
            if (this.filter == "") {
                this.loadActivitiesAfterAdd();
                return;
            }
            axios
                .get(
                    "api/activity/search-" +
                        this.searchValue +
                        "/" +
                        this.filter
                )
                .then(response => {
                    this.rawActivities = response.data;
                });
            axios.get("api/routine/all").then(response => {
                this.allRoutines = response.data;
            });
            axios
                .get(
                    "api/activity/search-" +
                        this.searchValue +
                        "/dates/" +
                        this.filter
                )
                .then(response => {
                    this.dates = response.data;
                });
        }, 500),
        disableTime(date) {
            return date > Date.now();
        },
        defaultDate() {
            return Date.now();
        },
        routinesInterval() {
            setInterval(() => {
                if (!this.searchOn) {
                    this.loadActivitiesAfterAdd();
                }
            }, 3000);
        },
        loadActivities() {
            this.loading = true;
            axios.get("api/activity/all").then(response => {
                this.rawActivities = response.data;
            });
            axios.get("api/routine/all").then(response => {
                this.allRoutines = response.data;
            });
            axios.get("api/activity/all/dates").then(response => {
                this.dates = response.data;
                this.loading = false;
                this.routinesInterval();
            });
            axios.get("api/activity/streak").then(response => {
                this.streak = response.data;
            });
        },
        loadActivitiesAfterAdd() {
            axios.get("api/activity/all").then(response => {
                this.rawActivities = response.data;
            });
            axios.get("api/routine/all").then(response => {
                this.allRoutines = response.data;
            });
            axios.get("api/activity/all/dates").then(response => {
                this.dates = response.data;
            });
            axios.get("api/activity/streak").then(response => {
                this.streak = response.data;
            });
        },
        saveActivity() {
            const clearActivityValues = this.getActivityValuesCheck();
            if (clearActivityValues == null) {
                return;
            }
            if (clearActivityValues.type == "Weight") {
                this.rawActivities.forEach(el => {
                    if (el.date == clearActivityValues.date && "weight" in el) {
                        this.dates[el.date]--;
                    }
                });
                axios
                    .post("api/activity/weight/add", clearActivityValues)
                    .then(() => {
                        this.$message({
                            type: "success",
                            message: "Activity added"
                        });
                    })
                    .catch(error => {
                        if (error.response.status == 400) {
                            document.getElementById(
                                "new-routine-error"
                            ).innerHTML = error.response.data.message;
                        } else if (error.response.status == 422) {
                            Object.keys(error.response.data.errors).forEach(
                                el => {
                                    let x = error.response.data.errors[el];
                                    document.getElementById(
                                        "new-routine-error"
                                    ).innerHTML =
                                        document.getElementById(
                                            "new-routine-error"
                                        ).innerHTML +
                                        "<br>" +
                                        x;
                                }
                            );
                        }
                        this.$message({
                            type: "error",
                            message: error.response.data.message
                        });
                    });
            } else if (clearActivityValues.type == "Feeling") {
                this.rawActivities.forEach(el => {
                    if (
                        el.date == clearActivityValues.date &&
                        "feeling" in el
                    ) {
                        this.dates[el.date]--;
                    }
                });
                axios
                    .post("api/activity/feeling/add", clearActivityValues)
                    .then(() => {
                        this.$message({
                            type: "success",
                            message: "Activity added successfully"
                        });
                    })
                    .catch(error => {
                        if (error.response.status == 400) {
                            document.getElementById(
                                "new-routine-error"
                            ).innerHTML = error.response.data.message;
                        } else if (error.response.status == 422) {
                            Object.keys(error.response.data.errors).forEach(
                                el => {
                                    let x = error.response.data.errors[el];
                                    document.getElementById(
                                        "new-routine-error"
                                    ).innerHTML =
                                        document.getElementById(
                                            "new-routine-error"
                                        ).innerHTML +
                                        "<br>" +
                                        x;
                                }
                            );
                        }
                        this.$message({
                            type: "error",
                            message: error.response.data.message
                        });
                    });
            } else if (clearActivityValues.type == "Routine") {
                axios
                    .post("api/activity/routine/add", clearActivityValues)
                    .then(() => {
                        this.$message({
                            type: "success",
                            message: "Activity added"
                        });
                    })
                    .catch(error => {
                        if (error.response.status == 400) {
                            document.getElementById(
                                "new-routine-error"
                            ).innerHTML = error.response.data.message;
                        } else if (error.response.status == 422) {
                            Object.keys(error.response.data.errors).forEach(
                                el => {
                                    let x = error.response.data.errors[el];
                                    document.getElementById(
                                        "new-routine-error"
                                    ).innerHTML =
                                        document.getElementById(
                                            "new-routine-error"
                                        ).innerHTML +
                                        "<br>" +
                                        x;
                                }
                            );
                        }
                        this.$message({
                            type: "error",
                            message: error.response.data.message
                        });
                    });
            } else {
                this.$message({
                    type: "error",
                    message: "Problem adding activity"
                });
            }
            clearNewActivity();
            activityModalClosed();
            this.loadActivitiesAfterAdd();
        },
        openAddActivityModal(date = null) {
            const today =
                typeof date != typeof null
                    ? new Date(date)
                    : new Date(
                          Date.now() - new Date().getTimezoneOffset() * 60000
                      );
            this.time1 = today;
            this.activityDateChanged();
            this.activityType.value = "Weight";
            this.activityTypeWeight.removeAttribute("class");
            this.activityTypeFeeling.setAttribute("class", "hidden");
            this.activityTypeRoutine.setAttribute("class", "hidden");
            let routines = [];
            axios.get("api/routine/all").then(response => {
                routines = response.data;
                this.addActivityRoutines.replaceChildren();
                routines.forEach(routine => {
                    const option = document.createElement("option");
                    option.setAttribute("value", routine.id);
                    option.innerHTML = routine.name;
                    this.addActivityRoutines.appendChild(option);
                });
                this.activityRoutineChanged();
                document.body.style.overflow = "hidden";
                this.addActivityDialogModal.style.display = "block";
            });
        },
        activityDateChanged() {
            const today = new Date(
                this.time1 - new Date().getTimezoneOffset() * 60000
            )
                .toISOString()
                .slice(0, 10);
            let activitiesDay = this.rawActivities.filter(act => {
                return act.date == today;
            });
            let hasWeight = false;
            let hasFeeling = false;
            activitiesDay.forEach(activity => {
                if ("weight" in activity) {
                    hasWeight = true;
                    this.activityWeight.value = activity.weight;
                } else if ("feeling" in activity) {
                    hasFeeling = true;
                    this.activityFeelingLabels.forEach(el => {
                        el.style.backgroundColor = "transparent";
                    });
                    const fInput = document.getElementById(activity.feeling);
                    fInput.checked = true;
                    fInput.parentElement.style.backgroundColor = "#68c4c3";
                }
            });
            if (!hasWeight) {
                this.activityWeight.value = "";
            }
            if (!hasFeeling) {
                this.activityFeelingRadios[0].selectedIndex = true;
            }
        },
        activityRoutineChanged() {
            const id = this.addActivityRoutines.value;
            if (this.allRoutines.length == 0) {
                showAlert("warn", "No routines available", 2000);
                return;
            }
            let routine;
            this.allRoutines.forEach(el => {
                if (el.id == id) {
                    routine = el;
                }
            });
            if (routine === null) {
                showAlert("error", "Error loading routine", 2000);
                return;
            }
            axios.get("api/exercise/" + routine.id).then(response => {
                routine.exercises = response.data;
                this.activityRoutineMain.replaceChildren();
                const isRepetition = routine.type == "Repetition";
                let i = 0;
                routine.exercises.forEach(ex => {
                    const div = document.createElement("div");
                    div.setAttribute(
                        "class",
                        "form_group field flex-in-between"
                    );
                    const p = document.createElement("p");
                    p.innerHTML = ex.name;
                    const idInput = document.createElement("input");
                    idInput.setAttribute("type", "hidden");
                    idInput.setAttribute("name", "id-exercise-" + i);
                    idInput.setAttribute("id", "id-exercise-" + i);
                    idInput.setAttribute("value", ex.id);
                    const input = document.createElement("input");
                    input.setAttribute(
                        "class",
                        "form_field smaller-field show-placeholder"
                    );
                    input.setAttribute("type", "number");
                    input.setAttribute(
                        "placeholder",
                        isRepetition ? "Repetitions" : "Duration"
                    );
                    input.setAttribute("name", "exercise-" + i);
                    input.setAttribute("id", "exercise-" + i++);
                    div.appendChild(p);
                    if (isRepetition) {
                        div.appendChild(input);
                    } else {
                        const innerDiv = document.createElement("div");
                        innerDiv.setAttribute("class", "div-duration");
                        const inputTimeUnit = document.createElement("select");
                        inputTimeUnit.setAttribute(
                            "class",
                            "form_field smaller-field-select-unit"
                        );
                        const optionSec = document.createElement("option");
                        optionSec.setAttribute("value", "sec");
                        optionSec.innerHTML = "sec";
                        const optionMin = document.createElement("option");
                        optionMin.setAttribute("value", "min");
                        optionMin.innerHTML = "min";
                        inputTimeUnit.appendChild(optionSec);
                        inputTimeUnit.appendChild(optionMin);
                        innerDiv.appendChild(input);
                        innerDiv.appendChild(inputTimeUnit);
                        div.appendChild(innerDiv);
                    }
                    div.appendChild(idInput);
                    this.activityRoutineMain.appendChild(div);
                });
            });
        },
        activityTypeChanged() {
            const type = activityType.value;
            if (type == "Weight") {
                activityTypeWeight.removeAttribute("class");
                activityTypeFeeling.setAttribute("class", "hidden");
                activityTypeRoutine.setAttribute("class", "hidden");
            } else if (type == "Feeling") {
                activityTypeFeeling.setAttribute("class", "flex-in-between");
                activityTypeWeight.setAttribute("class", "hidden");
                activityTypeRoutine.setAttribute("class", "hidden");
            } else if (type == "Routine") {
                activityTypeRoutine.removeAttribute("class");
                activityTypeFeeling.setAttribute("class", "hidden");
                activityTypeWeight.setAttribute("class", "hidden");
            } else {
                showAlert("error", "Problem with activity", 2000);
                activityTypeWeight.setAttribute("class", "hidden");
                activityTypeFeeling.setAttribute("class", "hidden");
                activityTypeRoutine.setAttribute("class", "hidden");
            }
        },
        getActivityValuesCheck() {
            const activityDateInput = new Date(
                this.time1 - new Date().getTimezoneOffset() * 60000
            )
                .toISOString()
                .slice(0, 10);
            const validDate = activityDateInput.match(/^\d{4}-\d{2}-\d{2}$/);
            let today = new Date(
                Date.now() - new Date().getTimezoneOffset() * 60000
            )
                .toISOString()
                .slice(0, 10);
            if (!validDate) {
                newActivityErrorBox.innerHTML = "Invalid date specified.";
                showAlert("error", "Error saving this activity", 2000);
                return;
            }
            if (activityDateInput > today) {
                newActivityErrorBox.innerHTML =
                    "Latest date passed can be today.";
                showAlert("error", "Error saving this activity", 2000);
                return;
            }
            const activityTypeInput = activityType.value.trim();
            if (
                activityTypeInput != "Weight" &&
                activityTypeInput != "Feeling" &&
                activityTypeInput != "Routine"
            ) {
                newActivityErrorBox.innerHTML = "Invalid type specified.";
                showAlert("error", "Error saving this activity", 2000);
                return;
            }
            if (activityTypeInput == "Weight") {
                const activityWeightInput = activityWeight.value.trim();
                if (activityWeightInput.match(/^\d+(.\d)?$/) == null) {
                    newActivityErrorBox.innerHTML = "Invalid weight specified.";
                    showAlert("error", "Error saving this activity", 2000);
                    return;
                }
                newActivityErrorBox.innerHTML = "";
                return {
                    date: activityDateInput,
                    type: activityTypeInput,
                    weight: activityWeightInput
                };
            } else if (activityTypeInput == "Feeling") {
                let activityFeelingInput;
                activityFeelingRadios.forEach(feeling => {
                    if (feeling.checked) activityFeelingInput = feeling.value;
                });
                if (activityFeelingInput == "") {
                    newActivityErrorBox.innerHTML =
                        "Undefined feeling specified.";
                    showAlert("error", "Error saving this activity", 2000);
                    return;
                }
                if (activityFeelingInput.match(/^feeling-[1-5]$/) == null) {
                    newActivityErrorBox.innerHTML =
                        "Undefined feeling specified.";
                    showAlert("error", "Error saving this activity", 2000);
                    return;
                }
                newActivityErrorBox.innerHTML = "";
                return {
                    date: activityDateInput,
                    type: activityTypeInput,
                    feeling: activityFeelingInput
                };
            } else if (activityTypeInput == "Routine") {
                if (
                    addActivityRoutines.options[
                        addActivityRoutines.selectedIndex
                    ] == null
                ) {
                    newActivityErrorBox.innerHTML = "No routine available.";
                    showAlert("error", "Error saving this activity", 2000);
                    return;
                }
                const activityRoutineIdInput = addActivityRoutines.value;
                const activityRoutineNameInput = addActivityRoutines.options[
                    addActivityRoutines.selectedIndex
                ].text.trim();
                if (activityRoutineNameInput == "") {
                    newActivityErrorBox.innerHTML =
                        "Undefined routine specified.";
                    showAlert("error", "Error saving this activity", 2000);
                    return;
                }
                const activityRoutineSetsInput = activityRoutineSets.value;
                if (activityRoutineSetsInput == "") {
                    newActivityErrorBox.innerHTML =
                        "Number of sets not specified.";
                    showAlert("error", "Error saving this activity", 2000);
                    return;
                }
                const activityRoutineTypeInput = document
                    .getElementById("exercise-0")
                    .placeholder.trim();
                if (
                    activityRoutineTypeInput != "Repetitions" &&
                    activityRoutineTypeInput != "Duration"
                ) {
                    newActivityErrorBox.innerHTML =
                        "Undefined routine type specified.";
                    showAlert("error", "Error saving this activity", 2000);
                    return;
                }
                let exercises = [];
                if (activityRoutineTypeInput == "Repetitions") {
                    let exErrors = false;
                    Array.from(activityRoutineMain.children).forEach(ex => {
                        let exercise = {};
                        exercise.name = ex.children[0].innerHTML;
                        exercise.repetitions = ex.children[1].value;
                        exercise.id = ex.children[2].value;
                        if (
                            exercise.id == "" ||
                            exercise.name == "" ||
                            exercise.repetitions == "" ||
                            exercise.repetitions < 0
                        ) {
                            exErrors = true;
                        }
                        exercises.push(exercise);
                    });
                    if (exErrors) {
                        newActivityErrorBox.innerHTML =
                            "All exercises need to have a positive value.";
                        showAlert("error", "Error saving this activity", 2000);
                        return;
                    }
                } else {
                    let exErrors = false;
                    Array.from(activityRoutineMain.children).forEach(ex => {
                        let exercise = {};
                        exercise.name = ex.children[0].innerHTML;
                        exercise.duration = ex.children[1].children[0].value;
                        exercise.timeUnit = ex.children[1].children[1].value;
                        exercise.id = ex.children[2].value;
                        if (
                            exercise.id == "" ||
                            exercise.name == "" ||
                            exercise.duration == "" ||
                            exercise.duration < 1 ||
                            exercise.timeUnit == ""
                        ) {
                            exErrors = true;
                        }
                        exercises.push(exercise);
                    });
                    if (exErrors) {
                        newActivityErrorBox.innerHTML =
                            "All exercises need to have a positive value.";
                        showAlert("error", "Error saving this activity", 2000);
                        return;
                    }
                }
                let routine = {};
                routine.id = activityRoutineIdInput;
                routine.name = activityRoutineNameInput;
                routine.type = activityRoutineTypeInput;
                routine.sets = activityRoutineSetsInput;
                routine.exercises = exercises;
                newActivityErrorBox.innerHTML = "";
                return {
                    date: activityDateInput,
                    type: activityTypeInput,
                    routine: routine
                };
            }
        }
    },
    mounted() {
        this.addActivityDialogModal = document.getElementById(
            "add-activity-dialog"
        );
        this.addActivityDialogButton = document.getElementById(
            "add-activity-dialog-button"
        );
        this.newActivityErrorBox = document.getElementById(
            "new-activity-error"
        );
        this.activityType = document.getElementById("activity-type");
        this.activityTypeWeight = document.getElementById(
            "activity-type-weight"
        );
        this.activityTypeFeeling = document.getElementById(
            "activity-type-feeling"
        );
        this.activityTypeRoutine = document.getElementById(
            "activity-type-routine"
        );
        this.activityDate = document.getElementById("activity-date");
        this.activityDateText = document.getElementById("activity-date-text");
        this.activityFeelingRadios = document.getElementsByName(
            "activity-feeling"
        );
        this.addActivityRoutines = document.getElementById("activity-routine");
        this.saveActivityButton = document.getElementById(
            "save-activity-button"
        );
        this.newActivityClear = document.getElementById("new-activity-clear");
        this.activityFeelingLabels = document.querySelectorAll(
            "[for*=feeling-]"
        );
        this.activityRoutineMain = document.getElementById(
            "activity-routine-main"
        );
        this.activityWeight = document.getElementById("activity-weight");
        this.activityRoutineSets = document.getElementById(
            "activity-routine-sets"
        );
        this.activitiesList = document.getElementById("activities-list");
        this.time1 = new Date();
        this.loadActivities();
    }
};
</script>
