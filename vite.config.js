import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import path from "path";
export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: "127.0.0.1",
        port: 5173,
        hmr: {
            host: "127.0.0.1",
        },
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
            "@UserManagement": path.resolve(
                __dirname,
                "Modules/UserManagement/resources/js"
            ),
            "@Setting": path.resolve(__dirname, "Modules/Setting/resources/js"),
            "@PIM": path.resolve(__dirname, "Modules/PIM/resources/js"),
            "@Performance": path.resolve(
                __dirname,
                "Modules/Performance/resources/js"
            ),
            "@Profile": path.resolve(__dirname, "Modules/Profile/resources/js"),
            "@Employee": path.resolve(
                __dirname,
                "Modules/Employee/resources/js"
            ),
            "@Recruitment": path.resolve(
                __dirname,
                "Modules/Recruitment/resources/js"
            ),
            "@Time": path.resolve(__dirname, "Modules/Time/resources/js"),
            "@Leave": path.resolve(__dirname, "Modules/Leave/resources/js"),
            "@Admin": path.resolve(__dirname, "Modules/Admin/resources/js"),
            "@Floor": path.resolve(__dirname, "Modules/Floor/resources/js"),
        },
    },
});
