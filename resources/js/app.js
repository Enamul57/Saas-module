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
