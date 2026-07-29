<script setup>
import { useSlots } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';

const page = usePage();
const slots = useSlots();

const handleLogout = () => {
    if (confirm('Are you sure you want to logout?')) {
        router.post('/logout', {}, {
            onSuccess: () => {
                window.location.href = '/login';
            },
            onError: () => {
                window.location.href = '/logout';
            }
        });
    }
};
</script>

<template>
    <div class="flex min-h-screen bg-slate-100">
        <Sidebar @logout="handleLogout" />

        <div class="w-full flex flex-col">
            <nav
                class="border-b border-gray-100 header flex justify-between px-4 py-3 items-center relative left-0 w-full z-10">
                <div class="w-full ml-[14%] flex justify-between items-center toggleNav">
                    <div class="flex items-center gap-4">
                        <span class="md:text-lg font-semibold text-sm ml-4">HRM Dashboard</span>
                    </div>
                    <div class="sm:ms-6 sm:flex sm:items-center gap-4">
                        <div class="text-sm text-gray-600 hidden sm:block">
                            <span class="font-medium">{{ page.props.auth?.user?.name || 'Guest' }}</span>
                            <span class="text-gray-400 text-xs ml-2">
                                <template v-if="page.props.auth?.user?.roles && page.props.auth.user.roles.length > 0">
                                    ({{ page.props.auth.user.roles.join(', ') }})
                                </template>
                                <template v-else>
                                    (No roles)
                                </template>
                            </span>
                        </div>
                        <button @click="handleLogout"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-2">
                            <i class="bx bx-log-out"></i>
                            <span>Logout</span>
                        </button>
                    </div>
                </div>
            </nav>

            <header class="bg-white shadow" v-if="slots.header">
                <div class="flex-1 xl:px-[18rem] lg:px-[10rem] py-6 sm:px-[6rem] px-[5rem]">
                    <slot name="header" />
                </div>
            </header>

            <main class="flex-1 xl:pl-[18rem] lg:pl-[10rem] py-6 sm:pl-[6rem] pl-[5rem]">
                <slot />
            </main>
        </div>
    </div>
</template>