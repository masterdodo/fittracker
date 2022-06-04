"use strict";

const alertBox = document.getElementById("alert-box");
const scrollToTopButton = document.getElementById("js-top");
const addRoutineDialogModal = document.getElementById("add-routine-dialog");
const addRoutineDialogButton = document.getElementById(
    "add-routine-dialog-button"
);
const addExerciseButton = document.getElementById("add-exercise-button");
const newRoutineClear = document.getElementById("new-routine-clear");
const saveRoutineButton = document.getElementById("save-routine-button");
const updateRoutineButton = document.getElementById("update-routine-button");
const newRoutineErrorBox = document.getElementById("new-routine-error");
const routineList = document.getElementById("routine-list");
const addRoutineTitle = document.getElementById("add-routine-title");
const updateRoutineID = document.getElementById("update-routine-id");

const addActivityDialogModal = document.getElementById("add-activity-dialog");
const addActivityDialogButton = document.getElementById(
    "add-activity-dialog-button"
);
const newActivityErrorBox = document.getElementById("new-activity-error");
const activityType = document.getElementById("activity-type");
const activityTypeWeight = document.getElementById("activity-type-weight");
const activityTypeFeeling = document.getElementById("activity-type-feeling");
const activityTypeRoutine = document.getElementById("activity-type-routine");
const activityDate = document.getElementById("activity-date");
const activityDateText = document.getElementById("activity-date-text");
const activityFeelingRadios = document.getElementsByName("activity-feeling");
const addActivityRoutines = document.getElementById("activity-routine");
const saveActivityButton = document.getElementById("save-activity-button");
const newActivityClear = document.getElementById("new-activity-clear");
const activityFeelingLabels = document.querySelectorAll("[for*=feeling-]");
const activityRoutineMain = document.getElementById("activity-routine-main");
const activityWeight = document.getElementById("activity-weight");
const activityRoutineSets = document.getElementById("activity-routine-sets");
const activitiesList = document.getElementById("activities-list");

/* SCROLL TO TOP - START */
const scrollController = () => {
    let y = window.scrollY;
    if (y > 0) {
        scrollToTopButton.className = "top-link show";
    } else {
        scrollToTopButton.className = "top-link hide";
    }
};

window.addEventListener("scroll", scrollController);

const scrollToTop = () => {
    const c = document.documentElement.scrollTop || document.body.scrollTop;

    if (c > 0) {
        window.requestAnimationFrame(scrollToTop);
        window.scrollTo(0, c - c / 10);
    }
};

scrollToTopButton.onclick = function(e) {
    e.preventDefault();
    scrollToTop();
};
/* SCROLL TO TOP - END */

window.onclick = function(event) {
    if (event.target == addRoutineDialogModal) {
        routineModalClosed();
    } else if (event.target == addActivityDialogModal) {
        activityModalClosed();
    }
};

function removeAdditionalExercises() {
    let ex = document.getElementById("exercise_set_name_1").parentElement;
    ex = ex.nextElementSibling;
    while (ex.tagName == "DIV") {
        let temp = ex.nextElementSibling;
        ex.remove();
        ex = temp;
    }
}

function removeExercise(id) {
    id = parseInt(id);
    let x = document.getElementById("exercise_set_name_" + id).parentElement
        .nextElementSibling;
    document.getElementById("exercise_set_name_" + id).parentElement.remove();
    while (x.tagName == "DIV") {
        let y = x.firstElementChild;
        y.setAttribute("placeholder", "Exercise " + id);
        y.setAttribute("name", "exercise_set_name_" + id);
        y.setAttribute("id", "exercise_set_name_" + id);
        y = y.nextElementSibling;
        y.setAttribute("for", "exercise_set_name_" + id);
        y.innerHTML = "Exercise " + id;
        y = y.nextElementSibling;
        y.setAttribute("onclick", "removeExercise(" + id + ")");
        id++;
        x = x.nextElementSibling;
    }
}

function getRoutineValuesCheck() {
    const routineName = document.getElementById("routine_name").value.trim();
    if (routineName == "") {
        newRoutineErrorBox.innerHTML = "Routine name cannot be empty.";
        showAlert("error", "Error saving this routine", 2000);
        return;
    }

    const routineType = document.getElementById("exercise_type").value.trim();
    if (routineType == "") {
        newRoutineErrorBox.innerHTML = "Routine type cannot be empty.";
        showAlert("error", "Error saving this routine", 2000);
        return;
    }

    const exercises = [];
    if (document.getElementById("exercise_set_name_1").value != "") {
        exercises.push(
            document.getElementById("exercise_set_name_1").value.trim()
        );
    }
    let exercise = document.getElementById("exercise_set_name_1").parentElement
        .nextElementSibling;
    while (exercise.tagName == "DIV") {
        if (exercise.firstElementChild.nextElementSibling.value != "") {
            exercises.push(
                exercise.firstElementChild.nextElementSibling.value.trim()
            );
        }
        exercise = exercise.nextElementSibling;
    }
    if (exercises.length == 0) {
        newRoutineErrorBox.innerHTML =
            "A routine has to have at least one exercise.";
        showAlert("error", "Error saving this routine", 2000);
        return;
    }
    newRoutineErrorBox.innerHTML = "";
    return {
        routineName,
        routineType,
        exercises
    };
}

function getRoutineUpdateValuesCheck() {
    const routineId = document.getElementById("update-routine-id").value.trim();
    if (routineId == "" || routineId == undefined) {
        newRoutineErrorBox.innerHTML =
            "There is a problem, please refresh the website.";
        showAlert("error", "Error saving this routine", 2000);
        return;
    }
    const routineName = document.getElementById("routine_name").value.trim();
    if (routineName == "") {
        newRoutineErrorBox.innerHTML = "Routine name cannot be empty.";
        showAlert("error", "Error saving this routine", 2000);
        return;
    }

    const routineType = document.getElementById("exercise_type").value.trim();
    if (routineType == "") {
        newRoutineErrorBox.innerHTML = "Routine type cannot be empty.";
        showAlert("error", "Error saving this routine", 2000);
        return;
    }

    const exercises = [];
    if (document.getElementById("exercise_set_name_1").value != "") {
        exercises.push({
            id: document.getElementById("exercise_set_id_1").value.trim(),
            name: document.getElementById("exercise_set_name_1").value.trim()
        });
    }
    let exercise = document.getElementById("exercise_set_name_1").parentElement
        .nextElementSibling;
    while (exercise.tagName == "DIV") {
        if (exercise.firstElementChild.nextElementSibling.value != "") {
            exercises.push({
                id: exercise.firstElementChild.value
                    ? exercise.firstElementChild.value.trim()
                    : "-1",
                name: exercise.firstElementChild.nextElementSibling.value.trim()
            });
        }
        exercise = exercise.nextElementSibling;
    }
    if (exercises.length == 0) {
        newRoutineErrorBox.innerHTML =
            "A routine has to have at least one exercise.";
        showAlert("error", "Error saving this routine", 2000);
        return;
    }
    newRoutineErrorBox.innerHTML = "";
    return {
        routineId,
        routineName,
        routineType,
        exercises
    };
}

function addExerciseToDialog() {
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
    delBtn.setAttribute("onclick", "removeExercise(" + lastExerciseNum + ")");
    const delBtnIcon = document.createElement("i");
    delBtnIcon.setAttribute("class", "fa fa-trash");
    delBtn.appendChild(delBtnIcon);
    container.appendChild(idInput);
    container.appendChild(input);
    container.appendChild(label);
    container.appendChild(delBtn);
    addExerciseButton.before(container);
}

function clearNewRoutine() {
    removeAdditionalExercises();
    document.getElementById("routine_name").value = "";
    document.getElementById("exercise_set_id_1").value = "";
    document.getElementById("exercise_set_name_1").value = "";
    newRoutineErrorBox.innerHTML = "";
}

function routineModalClosed() {
    document.body.style.overflow = "auto";
    addRoutineDialogModal.style.display = "none";
    newRoutineErrorBox.innerHTML = "";
    if (addRoutineTitle.innerHTML != "New Routine") {
        clearNewRoutine();
        addRoutineTitle.innerHTML = "New Routine";
    }
    saveRoutineButton.setAttribute("class", "btn btn-green");
    newRoutineClear.setAttribute("class", "btn");
    updateRoutineButton.setAttribute("class", "hidden");
}

function clearNewActivity() {
    activityType.value = "Weight";
    activityWeight.value = "";
    activityRoutineSets.value = "";
    newActivityErrorBox.innerHTML = "";
}

function activityModalClosed() {
    document.body.style.overflow = "auto";
    addActivityDialogModal.style.display = "none";
    clearNewActivity();
}

function showAlert(type, msg, duration) {
    alertBox.innerHTML = msg;
    if (type == "success") {
        alertBox.style.backgroundColor = "green";
    } else if (type == "warn") {
        alertBox.style.backgroundColor = "orange";
    } else if (type == "error") {
        alertBox.style.backgroundColor = "red";
    }
    alertBox.style.opacity = 1;
    setTimeout(() => {
        alertBox.style.opacity = 0;
    }, duration);
}

//addExerciseButton.onclick = addExerciseToDialog;
//newRoutineClear.onclick = clearNewRoutine;
/*
function activityTypeChanged() {
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
}
*/
/*
function activityRoutineChanged() {
    const id = addActivityRoutines.value;
    const routines = JSON.parse(localStorage.getItem("routines"));
    if (routines.length == 0) {
        showAlert("warn", "No routines available", 2000);
        return;
    }
    let routine;
    routines.forEach(el => {
        if (el.id == id) {
            routine = el;
        }
    });
    if (routine === null) {
        showAlert("error", "Error loading routine", 2000);
        return;
    }
    activityRoutineMain.replaceChildren();
    const isRepetition = routine.type == "Repetition";
    let i = 0;
    routine.exercises.forEach(ex => {
        const div = document.createElement("div");
        div.setAttribute("class", "form_group field flex-in-between");
        const p = document.createElement("p");
        p.innerHTML = ex;
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
        activityRoutineMain.appendChild(div);
    });
}
*/
activityFeelingRadios.forEach(input => {
    input.addEventListener("change", event => {
        activityFeelingLabels.forEach(el => {
            el.style.backgroundColor = "transparent";
        });
        document.getElementById(
            event.target.value
        ).parentElement.style.backgroundColor = "#68c4c3";
    });
});
/*
activityDate.addEventListener("change", activityDateChanged);

activityDateText.addEventListener("focusout", () => {
    const value = activityDateText.value.trim();
    if (value.match(/^\d{4}-\d{2}-\d{2}$/)) {
        activityDate.value = value;
    } else {
        activityDate.value = "";
        showAlert("warn", "Date has to be correct", 1500);
    }
});*/

function getActivityValuesCheck() {
    const activityDateInput = activityDate.value.trim();
    const validDate = activityDateInput.match(/^\d{4}-\d{2}-\d{2}$/);
    let today = new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 10);
    if (!validDate) {
        newActivityErrorBox.innerHTML = "Invalid date specified.";
        showAlert("error", "Error saving this activity", 2000);
        return;
    }
    if (activityDateInput > today) {
        newActivityErrorBox.innerHTML = "Latest date passed can be today.";
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
            newActivityErrorBox.innerHTML = "Undefined feeling specified.";
            showAlert("error", "Error saving this activity", 2000);
            return;
        }
        if (activityFeelingInput.match(/^feeling-[1-5]$/) == null) {
            newActivityErrorBox.innerHTML = "Undefined feeling specified.";
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
            addActivityRoutines.options[addActivityRoutines.selectedIndex] ==
            null
        ) {
            newActivityErrorBox.innerHTML = "No routine available.";
            showAlert("error", "Error saving this activity", 2000);
            return;
        }
        const activityRoutineNameInput = addActivityRoutines.options[
            addActivityRoutines.selectedIndex
        ].text.trim();
        if (activityRoutineNameInput == "") {
            newActivityErrorBox.innerHTML = "Undefined routine specified.";
            showAlert("error", "Error saving this activity", 2000);
            return;
        }
        const activityRoutineSetsInput = activityRoutineSets.value;
        if (activityRoutineSetsInput == "") {
            newActivityErrorBox.innerHTML = "Number of sets not specified.";
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
            newActivityErrorBox.innerHTML = "Undefined routine type specified.";
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
                if (exercise.name == "" || exercise.repetitions == "") {
                    exErrors = true;
                }
                exercises.push(exercise);
            });
            if (exErrors) {
                newActivityErrorBox.innerHTML =
                    "All exercises need to have a value.";
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
                if (
                    exercise.name == "" ||
                    exercise.duration == "" ||
                    exercise.timeUnit == ""
                ) {
                    exErrors = true;
                }
                exercises.push(exercise);
            });
            if (exErrors) {
                newActivityErrorBox.innerHTML =
                    "All exercises need to have a value.";
                showAlert("error", "Error saving this activity", 2000);
                return;
            }
        }
        let routine = {};
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
