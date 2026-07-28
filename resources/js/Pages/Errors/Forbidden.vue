<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Icon -->
            <div class="mb-4">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                    <i class="bx bx-lock-alt text-5xl text-red-500"></i>
                </div>
            </div>

            <!-- Title -->
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Access Denied</h1>
            <p class="text-gray-600 mb-6">
                You do not have permission to access this page.
            </p>

            <!-- Permission Details -->
            <div v-if="permission" class="bg-gray-50 rounded-lg p-3 mb-6 text-sm">
                <p class="text-gray-600">
                    <span class="font-medium">Required Permission:</span>
                    <span class="text-red-500">{{ permission }}</span>
                </p>
            </div>

            <div v-if="message" class="bg-gray-50 rounded-lg p-3 mb-6 text-sm">
                <p class="text-gray-600">{{ message }}</p>
            </div>

            <!-- User Info -->
            <div v-if="user" class="bg-gray-50 rounded-lg p-3 mb-6 text-sm flex items-center justify-center gap-3">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                    {{ userInitials }}
                </div>
                <div class="text-left">
                    <p class="font-medium text-gray-800">{{ user.name }}</p>
                    <p class="text-gray-500 text-xs">{{ user.email }}</p>
                    <p class="text-gray-400 text-xs">Roles: {{ userRoles }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button @click="goBack"
                    class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    <i class="bx bx-arrow-back mr-2"></i>
                    Go Back
                </button>
                <button @click="logout"
                    class="px-6 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                    <i class="bx bx-log-out mr-2"></i>
                    Logout
                </button>
                <button @click="goToDashboard"
                    class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                    <i class="bx bx-home mr-2"></i>
                    Dashboard
                </button>
            </div>

            <p class="mt-6 text-xs text-gray-400">
                If you believe this is an error, please contact your system administrator.
            </p>
        </div>
    </div>
</template>

<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const props = defineProps({
    permission: {
        type: String,
        default: null
    },
    message: {
        type: String,
        default: 'You do not have permission to access this page.'
    }
});

// Get user info from page props
const user = computed(() => page.props.auth?.user || null);
const userName = computed(() => user.value?.name || 'Guest');
const userEmail = computed(() => user.value?.email || '');
const userRoles = computed(() => {
    if (user.value?.roles) {
        return user.value.roles.map(r => r.name).join(', ');
    }
    return 'No roles assigned';
});
const userInitials = computed(() => {
    if (userName.value && userName.value !== 'Guest') {
        return userName.value.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
    }
    return '?';
});

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        // Use Inertia's router to post to logout
        router.post(route('logout'), {}, {
            onSuccess: () => {
                // Force redirect to login page
                window.location.href = '/login';
            },
            onError: () => {
                // If route fails, try direct logout
                window.location.href = '/logout';
            }
        });
    }
}

function goBack() {
    window.history.back();
}

function goToDashboard() {
    router.visit('/dashboard');
}
</script>

<style scoped>
.bx {
    display: inline-block;
    vertical-align: middle;
}
</style>