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

onMounted(() => {
    // Any setup code can go here
    // console.log($page.props);
    // console.log($slots);

});
const sidebarOpen = ref(true);
const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
// Submenu state
const userManagementOpen = ref(false);
const toggleUserManagement = () => {
    userManagementOpen.value = !userManagementOpen.value;
};
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
            'sideBarColor fixed inset-y-0 left-0 z-20 flex flex-col transition-all duration-300',
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
            <nav class="mt-4 flex flex-col gap-2 px-2 ">
                <Link href="#" class="flex items-center gap-2 p-2 rounded sideBarMenuColor">
                <span>
                    <i class='w-6 flex-shrink-0 bx bx-grid-alt text-xl'></i> </span>
                <span v-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
                </Link>


                <span v-show="sidebarOpen" class="whitespace-nowrap ">
                    <!-- User Management with dropdown -->
                    <div>
                        <button @click="toggleUserManagement"
                            class="flex items-center gap-2 p-2 w-full rounded transition-colors focus:outline-none sideBarMenuColor">
                            <i class='w-6 flex-shrink-0 bx bx-user text-xl'></i>
                            <span v-show="sidebarOpen" class="whitespace-nowrap">User Management</span>
                            <svg v-show="sidebarOpen" :class="{ 'rotate-90': userManagementOpen }"
                                class="ml-auto h-4 w-4 transition-transform" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <!-- Submenu -->
                        <div v-show="userManagementOpen" class="ml-6 mt-2 flex flex-col gap-1">
                            <Link :href="route('users.index')"
                                class="flex items-center gap-2 p-2 rounded transition-colors text-sm sideBarMenuColor">
                            Create Users
                            </Link>
                            <Link :href="route('users.index')"
                                class="flex items-center gap-2 p-2 rounded transition-colors text-sm sideBarMenuColor">
                            Create Roles
                            </Link>
                            <Link :href="route('permissions.create')"
                                class="flex items-center gap-2 p-2 rounded transition-colors text-sm sideBarMenuColor">
                            Create Permissions
                            </Link>
                        </div>
                    </div>
                </span>
                <Link href="#" class="flex items-center gap-2 p-2 rounded sideBarMenuColor">
                <span><i class='w-6 flex-shrink-0 bx  bx-equalizer'></i> </span>
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
