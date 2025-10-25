<script setup>
import { ref, onMounted, useSlots, watch, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import Notification from '@/Components/Notification.vue';
const $slots = useSlots();
const $page = usePage();
const checkPage = usePage();
const sidebarOpen = ref(true);
const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
const userManagementManual = ref(false);
const employeeManagementManual = ref(false);
const roleManagementManual = ref(false);


const toggleRoleManagement = () => {
    roleManagementManual.value = !roleManagementManual.value;
};
// Array of routes under user management
const userManagementRoutes = [
    route('users.index'),
];
const roleManagementRoutes = [
    route('roles.index'),
    route('permissions.assign'),
];
const employeeManagementRoutes = [
    route('pim.index'),
];
const userManagementOpen = computed(() => {
    return userManagementManual.value || userManagementRoutes.some(r => isActive(r));
});
const roleManagementOpen = computed(() => {
    return roleManagementManual.value || roleManagementRoutes.some(r => isActive(r));
});
const employeeManagementOpen = computed(() => {
    return employeeManagementManual.value || employeeManagementRoutes.some(r => isActive(r));
});

const toggleUserManagement = () => {
    userManagementManual.value = !userManagementManual.value;
};
const toggleEmployeeManagement = computed(() => {
    employeeManagementManual.value = !employeeManagementManual.value;
});
// Active route helper
const currentRoute = computed(() => window.location.pathname);
const isActive = (routeUrl) => currentRoute.value === routeUrl;

const flashMessage = computed(() => {
    const flash = $page.props.flash;
    if (!flash) return null;

    if (flash.success) return { type: 'success', message: flash.success };
    if (flash.danger) return { type: 'danger', message: flash.danger };
    if (flash.warning) return { type: 'warning', message: flash.warning };
    if (flash.info) return { type: 'info', message: flash.info };

    return null;
});

</script>

<template>
    <div class="flex min-h-screen bg-slate-100">
        <!-- Sidebar -->
        <!-- Sidebar -->
        <aside :class="[
            'sideBarColor fixed inset-y-0 left-0 z-20 flex flex-col transition-all duration-300',
            sidebarOpen ? 'w-64' : 'w-16'
        ]">
            <!-- Toggle Button -->
            <button @click="toggleSidebar" class="p-4 flex items-center justify-start ml-1 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span v-show="sidebarOpen" class="whitespace-nowrap ml-4"><img src="/images/logo.png" alt=""
                        class="w-40"></span>
            </button>

            <!-- Sidebar Menu -->
            <nav class="mt-4 flex flex-col gap-1 px-2">
                <!-- Dashboard -->
                <Link :href="route('central.dashboard')"
                    class="flex items-center gap-3 p-3 rounded-md sideBarMenuColor hover:bg-[#FF9B00] hover:text-white transition">
                <i class="bx bx-grid-alt text-2xl w-6 flex-shrink-0"></i>
                <span v-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
                </Link>

                <!-- User Management -->
                <div class="flex flex-col">
                    <button @click="toggleUserManagement"
                        class="flex items-center gap-3 p-3 rounded-md sideBarMenuColor hover:bg-[#FF9B00] hover:text-white transition w-full">
                        <i class="las la-user text-2xl w-6 flex-shrink-0"></i>
                        <span v-show="sidebarOpen" class="flex-1 text-left">User Management</span>
                        <svg v-show="sidebarOpen" :class="{ 'rotate-90': userManagementOpen }"
                            class="h-4 w-4 transition-transform ml-auto" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Nested Menu -->
                    <div v-show="userManagementOpen" class="ml-10 mt-1 flex flex-col gap-1">
                        <Link :href="route('users.index')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('users.index')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        Users
                        </Link>
                    </div>
                </div>
                <!-- Role Management -->
                <div class="flex flex-col">
                    <button @click="toggleRoleManagement"
                        class="flex items-center gap-3 p-3 rounded-md sideBarMenuColor hover:bg-[#FF9B00] hover:text-white transition w-full">
                        <i class="las la-user-lock text-2xl w-6 flex-shrink-0"></i>
                        <span v-show="sidebarOpen" class="flex-1 text-left">Role Management</span>
                        <svg v-show="sidebarOpen" :class="{ 'rotate-90': roleManagementOpen }"
                            class="h-4 w-4 transition-transform ml-auto" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Nested Menu -->
                    <div v-show="roleManagementOpen" class="ml-10 mt-1 flex flex-col gap-1">
                        <Link :href="route('roles.index')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('roles.index')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        Roles
                        </Link>
                        <Link :href="route('permissions.assign')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('permissions.assign')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        Permissions
                        </Link>
                    </div>
                </div>
                <!-- PIM -->
                <div class="flex flex-col">
                    <button @click="toggleEmployeeManagement"
                        class="flex items-center gap-3 p-3 rounded-md sideBarMenuColor hover:bg-[#FF9B00] hover:text-white transition w-full">
                        <i class="las la-users text-2xl w-6 flex-shrink-0"></i>
                        <span v-show="sidebarOpen" class="flex-1 text-left">PIM</span>
                        <svg v-show="sidebarOpen" :class="{ 'rotate-90': employeeManagementOpen }"
                            class="h-4 w-4 transition-transform ml-auto" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Nested Menu -->
                    <div v-show="employeeManagementOpen" class="ml-10 mt-1 flex flex-col gap-1">
                        <Link :href="route('pim.index')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('pim.index')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        Index
                        </Link>
                    </div>
                </div>

                <!-- Settings -->
                <Link :href="route('settings.index')"
                    class="flex items-center gap-3 p-3 rounded-md sideBarMenuColor hover:bg-[#FF9B00] hover:text-white transition">
                <i class="bx bx-equalizer text-2xl w-6 flex-shrink-0"></i>
                <span v-show="sidebarOpen" class="whitespace-nowrap">Settings</span>
                </Link>
            </nav>
        </aside>


        <!-- Main content with top navigation -->
        <div class="w-full flex flex-col">
            <!-- Top Navigation -->
            <nav
                class="border-b border-gray-100 header flex justify-between px-4 py-3 items-center relative left-0 w-full z-10">
                <div class="w-full ml-[14%] flex justify-between items-center toggleNav">
                    <div class="flex items-center gap-4 ">
                        <span class="md:text-lg font-semibold text-sm ml-4 ">HRM Dashboard</span>
                    </div>
                    <div class=" sm:ms-6 sm:flex sm:items-center">
                        <!-- Settings Dropdown -->
                        <div class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button type="button"
                                            class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none">
                                            {{ $page.props.auth.user.name }}

                                            <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink href="#">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>


            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="flex-1 xl:px-[18rem] lg:px-[10rem] py-6 sm:px-[6rem] px-[5rem]">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <!-- <Notification v-if="flashMessage" :type="flashMessage.type" :message="flashMessage.message" /> -->
            <main class="flex-1 xl:pl-[18rem] lg:pl-[10rem] py-6 sm:pl-[6rem] pl-[5rem]">
                <slot />
            </main>
        </div>
    </div>
</template>
