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

// Array of routes under user management
const userManagementRoutes = [
    route('users.index'),
    route('roles.index'),
    route('permissions.assign'),
];

const userManagementOpen = computed(() => {
    return userManagementManual.value || userManagementRoutes.some(r => isActive(r));
});

const toggleUserManagement = () => {
    userManagementManual.value = !userManagementManual.value;
};

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
        <aside :class="[
            'sideBarColor fixed inset-y-0 left-0 z-20 flex flex-col transition-all duration-100',
            sidebarOpen ? 'w-64' : 'w-16'
        ]">
            <!-- Hamburger for sidebar -->
            <button @click="toggleSidebar" class="p-4 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <!-- Sidebar Menu -->
            <nav class="mt-4 flex flex-col gap-2 px-2  ">
                <!-- Dashboard link -->
                <!--dashboard-->
                <span class="flex items-start gap-2 p-2 rounded sideBarMenuColor">
                    <span> <i class="w-6 flex-shrink-0 bx bx-grid-alt text-xl"></i></span>
                    <span v-show="sidebarOpen" class="whitespace-nowrap ">
                        <!--  dropdown -->
                        <div>
                            <Link :href="route('central.dashboard')" class="flex items-start gap-2  rounded">
                            <transition name="fade">
                                <span v-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
                            </transition>
                            </Link>
                        </div>
                    </span>
                </span>
                <!--user management-->
                <span class="flex items-start gap-2 p-2 rounded sideBarMenuColor">
                    <span><i class='w-6 flex-shrink-0 bx bx-user mt-2'></i></span>
                    <span v-show="sidebarOpen" class="whitespace-nowrap ">
                        <!-- User Management with dropdown -->
                        <div>
                            <button @click="toggleUserManagement"
                                class="flex items-center gap-2 px-0 py-1 w-full rounded transition-colors focus:outline-none"
                                :class="userManagementOpen ? 'bg-orange-100 text-orange-600' : ''">
                                <transition name="fade">
                                    <span v-if="sidebarOpen">User Management</span>
                                </transition>
                                <svg v-show="sidebarOpen" :class="{ 'rotate-90': userManagementOpen }"
                                    class="ml-auto h-4 w-4 transition-transform" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div v-show="userManagementOpen" class="ml-6 mt-2 flex flex-col gap-1">
                                <Link :href="route('users.index')" :class="['flex items-center gap-2 p-2 rounded text-sm transition-colors sideBarMenuColor',
                                    isActive(route('users.index')) ? 'bg-orange-100 text-orange-600' : '']">
                                Users
                                </Link>
                                <Link :href="route('roles.index')" :class="['flex items-center gap-2 p-2 rounded text-sm transition-colors sideBarMenuColor',
                                    isActive(route('roles.index')) ? 'bg-orange-100 text-orange-600' : '']">
                                Roles
                                </Link>
                                <Link :href="route('permissions.assign')" :class="['flex items-center gap-2 p-2 rounded text-sm transition-colors sideBarMenuColor',
                                    isActive(route('permissions.assign')) ? 'bg-orange-100 text-orange-600' : '']">
                                Permissions
                                </Link>
                            </div>
                        </div>
                    </span>
                </span>
                <!--settings-->
                <!--dashboard-->
                <span class="flex items-start gap-2 p-2 rounded sideBarMenuColor">
                    <span><i class='w-6 flex-shrink-0 bx  bx-equalizer'></i> </span>
                    <span v-show="sidebarOpen" class="whitespace-nowrap ">
                        <!--  dropdown -->
                        <div>
                            <Link :href="route('central.dashboard')" class="flex items-start gap-2  rounded">
                            <transition name="fade">
                                <span v-show="sidebarOpen" class="whitespace-nowrap">Settings</span>
                            </transition>
                            </Link>
                        </div>
                    </span>
                </span>
                <!--end-->
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
                                    <DropdownLink :href="route('tenant.profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink :href="route('tenant.logout')" method="post" as="button">
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
            <Notification v-if="flashMessage" :type="flashMessage.type" :message="flashMessage.message" />
            <main class="flex-1 xl:px-[18rem] lg:px-[10rem] py-6 sm:px-[6rem] px-[5rem]">
                <slot />
            </main>
        </div>
    </div>
</template>
<style>
.fade-enter-active {
    transition: opacity 0.1s ease, transform 1s ease;
}

.fade-leave-active {
    transition: opacity 1s ease, transform 1s ease;
}

.fade-enter-from {
    opacity: 0;
    transform: translateX(-10px);
}

.fade-enter-to {
    opacity: 1;
    transform: translateX(0);
}

.fade-leave-from {
    opacity: 1;
    transform: translateX(0);
}

.fade-leave-to {
    opacity: 0;
    transform: translateX(-10px);
}

/* Same for slide-fade if you use it */
.slide-fade-enter-active {
    transition: all 0.1s ease;
}

.slide-fade-leave-active {
    transition: all 1s ease;
}

.slide-fade-enter-from {
    opacity: 0;
    transform: translateY(-5px);
}

.slide-fade-enter-to {
    opacity: 1;
    transform: translateY(0);
}

.slide-fade-leave-from {
    opacity: 1;
    transform: translateY(0);
}

.slide-fade-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}
</style>