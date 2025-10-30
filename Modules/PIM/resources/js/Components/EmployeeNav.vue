<template>
    <aside class="w-64 h-screen bg-white shadow-md border-r border-gray-200 flex flex-col p-4 rounded-xl">
        <h2 class="text-xl font-semibold mb-6 text-gray-700">Employee Menu</h2>

        <nav class="flex flex-col gap-3">
            <button v-for="item in menuItems" :key="item.route" @click="navigateTo(item.route)"
                :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive(item.route) ? 'primary' : 'secondaryColor']">
                <i :class="['bx', item.icon, 'text-lg']"></i>
                <span>{{ item.label }}</span>
            </button>
        </nav>
    </aside>
</template>

<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    current: { type: String, required: true },
    employee: { type: Object, required: true },
});

const menuItems = [
    { label: "Personal Details", icon: "bx-user", route: "PIM.getPersonalDetails" },
    { label: "Contact Details", icon: "bx-phone", route: "PIM.ContactDetails" },
    { label: "Job Details", icon: "bx-briefcase", route: "PIM.JobDetails" },
    { label: "Salary", icon: "bx-wallet", route: "PIM.SalaryDetails" },
];

function navigateTo(routeName: string) {
    router.visit(route(routeName, props.employee));
}

function isActive(routeName: string) {
    return route().current() === routeName;
}
</script>

<style scoped>
/* Optional: Sidebar scroll for long menus */
aside {
    overflow-y: auto;
}
</style>
