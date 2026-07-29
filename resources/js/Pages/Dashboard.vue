<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

// Get user data
const user = computed(() => page.props.auth?.user);
const userRoles = computed(() => user.value?.roles || []);
const userPermissions = computed(() => user.value?.permissions || []);

// Check if user is Super Admin
const isSuperAdmin = computed(() => {
    return userRoles.value.includes('super_admin');
});

// Check permissions
const can = (permission) => {
    if (isSuperAdmin.value) return true;
    return userPermissions.value.includes(permission);
};

// Check roles
const hasRole = (role) => {
    return userRoles.value.some(r => r.toLowerCase() === role.toLowerCase());
};

// Get statistics (you can fetch these from API)
const stats = {
    totalEmployees: 0,
    totalDepartments: 0,
    pendingLeaves: 0,
    todayAttendance: 0,
};
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                HRM Dashboard
            </h2>
        </template>

        <!-- Main Content -->
        <div class="p-6 space-y-6">
            <!-- Welcome Message -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-2xl font-bold text-gray-800">
                    Welcome back, {{ user?.name || 'Guest' }}! 👋
                </h3>
                <p class="text-gray-500 mt-2">
                    You are logged in as <span class="font-medium text-gray-700">{{ userRoles.join(', ') || 'No roles'
                        }}</span>
                </p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <span v-for="perm in userPermissions.slice(0, 5)" :key="perm"
                        class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                        {{ perm }}
                    </span>
                    <span v-if="userPermissions.length > 5" class="text-xs text-gray-400">
                        +{{ userPermissions.length - 5 }} more
                    </span>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Employees</p>
                            <p class="text-2xl font-bold text-gray-800">{{ stats.totalEmployees }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="bx bx-user text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Departments</p>
                            <p class="text-2xl font-bold text-gray-800">{{ stats.totalDepartments }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="bx bx-building text-2xl text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Pending Leaves</p>
                            <p class="text-2xl font-bold text-gray-800">{{ stats.pendingLeaves }}</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="bx bx-calendar text-2xl text-yellow-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Today's Attendance</p>
                            <p class="text-2xl font-bold text-gray-800">{{ stats.todayAttendance }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="bx bx-check-circle text-2xl text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-4">Quick Actions</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- My Profile - Available to all -->
                    <Link v-if="user?.employee_id" :href="route('pim.getPersonalDetails', user.employee_id)"
                        class="flex items-center gap-3 p-4 bg-gray-50 hover:bg-amber-50 rounded-lg transition-colors border border-gray-200">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="bx bx-user text-xl text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">My Profile</p>
                            <p class="text-sm text-gray-500">View and edit your details</p>
                        </div>
                    </Link>

                    <!-- Employee List - Super Admin, Admin, HR Manager, Dept Manager -->
                    <Link v-if="can('view_employees')" :href="route('pim.index')"
                        class="flex items-center gap-3 p-4 bg-gray-50 hover:bg-amber-50 rounded-lg transition-colors border border-gray-200">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="bx bx-list-ul text-xl text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">Employee List</p>
                            <p class="text-sm text-gray-500">View all employees</p>
                        </div>
                    </Link>

                    <!-- Add Employee - Super Admin, Admin, HR Manager -->
                    <Link v-if="can('create_employee')" :href="route('pim.create')"
                        class="flex items-center gap-3 p-4 bg-gray-50 hover:bg-amber-50 rounded-lg transition-colors border border-gray-200">
                        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                            <i class="bx bx-user-plus text-xl text-amber-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">Add Employee</p>
                            <p class="text-sm text-gray-500">Add new employee</p>
                        </div>
                    </Link>

                    <!-- User Management - Super Admin, Admin, HR Manager -->
                    <Link v-if="can('view_users')" :href="route('users.index')"
                        class="flex items-center gap-3 p-4 bg-gray-50 hover:bg-amber-50 rounded-lg transition-colors border border-gray-200">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="bx bx-group text-xl text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">User Management</p>
                            <p class="text-sm text-gray-500">Manage system users</p>
                        </div>
                    </Link>

                    <!-- Reports - Super Admin, Admin, HR Manager, Dept Manager -->
                    <Link v-if="can('view_reports')" :href="route('pim.Reports')"
                        class="flex items-center gap-3 p-4 bg-gray-50 hover:bg-amber-50 rounded-lg transition-colors border border-gray-200">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="bx bx-bar-chart-alt-2 text-xl text-red-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">Reports</p>
                            <p class="text-sm text-gray-500">View HR reports</p>
                        </div>
                    </Link>

                    <!-- Role Management - Super Admin, Admin -->
                    <Link v-if="can('view_roles')" :href="route('roles.index')"
                        class="flex items-center gap-3 p-4 bg-gray-50 hover:bg-amber-50 rounded-lg transition-colors border border-gray-200">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i class="bx bx-lock text-xl text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">Role Management</p>
                            <p class="text-sm text-gray-500">Manage roles & permissions</p>
                        </div>
                    </Link>

                    <!-- Settings - Super Admin, Admin -->
                    <Link v-if="can('manage_settings')" :href="route('settings.index')"
                        class="flex items-center gap-3 p-4 bg-gray-50 hover:bg-amber-50 rounded-lg transition-colors border border-gray-200">
                        <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="bx bx-cog text-xl text-white"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">Settings</p>
                            <p class="text-sm text-gray-500">System settings</p>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Create Company - Only Super Admin -->
            <div v-if="isSuperAdmin" class="bg-white rounded-xl shadow-md p-6 border-2 border-amber-200">
                <h4 class="text-lg font-semibold text-gray-700 mb-4">System Administration</h4>
                <div class="flex flex-wrap gap-4">
                    <Link :href="route('company.create')"
                        class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors">
                        <i class="bx bx-building"></i>
                        Create Company
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.bx {
    display: inline-block;
}
</style>