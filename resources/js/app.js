/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue").default;
window.Vue.prototype.$http = window.axios;
window.Vue.prototype.$userId = document
    .querySelector("meta[name='user-id']")
    .getAttribute("content");

import ElementUI from "element-ui";
import locale from "element-ui/lib/locale/lang/en";

Vue.use(ElementUI, { locale });
window.Vue.config.lang = "en";

Vue.component("admin", require("./components/Admin.vue").default);
Vue.component("profile", require("./components/Profile.vue").default);
Vue.component("leaderboard", require("./components/Leaderboard.vue").default);
Vue.component(
    "example-component",
    require("./components/ExampleComponent.vue").default
);
Vue.component("routines", require("./components/Routines.vue").default);
Vue.component("routine", require("./components/Routine.vue").default);
Vue.component("activities", require("./components/Activities.vue").default);
Vue.component(
    "dateActivities",
    require("./components/DateActivities.vue").default
);
Vue.component(
    "routine-activity",
    require("./components/RoutineActivity.vue").default
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: "#app"
});
