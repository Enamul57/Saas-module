import "../css/app.css";
import "./bootstrap";
import "boxicons/css/boxicons.min.css";
import Vue3EasyDataTable from "vue3-easy-data-table";
import "vue3-easy-data-table/dist/style.css";

import { createInertiaApp } from "@inertiajs/vue3";
import { createApp, h } from "vue";
import store from "./store";
import { ZiggyVue } from "ziggy-js";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

// Define module page globs
const modules = {
    UserManagement: import.meta.glob(
        "../../Modules/UserManagement/resources/js/Pages/**/*.vue"
    ),
    Profile: import.meta.glob(
        "../../Modules/Profile/resources/js/Pages/**/*.vue"
    ),
    Setting: import.meta.glob(
        "../../Modules/Setting/resources/js/Pages/**/*.vue"
    ),
    Admin: import.meta.glob("../../Modules/Admin/resources/js/Pages/**/*.vue"),
    PIM: import.meta.glob("../../Modules/PIM/resources/js/Pages/**/*.vue"),
    Time: import.meta.glob("../../Modules/Time/resources/js/Pages/**/*.vue"),
    Performance: import.meta.glob(
        "../../Modules/Performance/resources/js/Pages/**/*.vue"
    ),
    Leave: import.meta.glob("../../Modules/Leave/resources/js/Pages/**/*.vue"),
    Employee: import.meta.glob(
        "../../Modules/Employee/resources/js/Pages/**/*.vue"
    ),
    Recruitment: import.meta.glob(
        "../../Modules/Recruitment/resources/js/Pages/**/*.vue"
    ),
    // Add more modules here
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,

    resolve: (name) => {
        // Check if this is a module page (ModuleName/Page/Path)
        const [moduleName, ...pageParts] = name.split("/");
        const pagePath = pageParts.join("/");

        if (modules[moduleName]) {
            const pageKey = `../../Modules/${moduleName}/resources/js/Pages/${pagePath}.vue`;
            if (!(pageKey in modules[moduleName])) {
                throw new Error(`Module page not found: ${pageKey}`);
            }
            return modules[moduleName][pageKey]();
        }

        // Default pages in resources/js/Pages
        const defaultPages = import.meta.glob("./Pages/**/*.vue");
        const defaultKey = `./Pages/${name}.vue`;
        if (!(defaultKey in defaultPages)) {
            throw new Error(`Default page not found: ${defaultKey}`);
        }
        return defaultPages[defaultKey]();
    },

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(store)
            .mount(el);
    },

    progress: { color: "#4B5563" },
});
