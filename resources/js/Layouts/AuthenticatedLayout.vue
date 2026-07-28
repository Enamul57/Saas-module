<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const $page = usePage();
const sidebarOpen = ref(true);

// Toggle states
const userManagementManual = ref(false);
const roleManagementManual = ref(false);
const employeeManagementManual = ref(false);
const settingsManual = ref(false);

// ✅ Get user data from page props
const user = computed(() => $page.props.auth?.user);

// ✅ Get roles (already lowercase from backend)
const userRoles = computed(() => {
    return user.value?.roles || [];
});

// ✅ Check if user is Super Admin
const isSuperAdmin = computed(() => {
    return userRoles.value.includes('super_admin');
});

// ✅ Check if user has a specific role (case insensitive)
const hasRole = (role) => {
    if (isSuperAdmin.value) return true;
    return userRoles.value.some(r => r.toLowerCase() === role.toLowerCase());
};

// ✅ Check if user has any of the given roles
const hasAnyRole = (roles) => {
    if (isSuperAdmin.value) return true;
    const lowerRoles = roles.map(r => r.toLowerCase());
    return lowerRoles.some(role =>
        userRoles.value.some(r => r.toLowerCase() === role.toLowerCase())
    );
};

// ✅ Check if user has a specific permission
const can = (permission) => {
    if (isSuperAdmin.value) return true;
    const permissions = user.value?.permissions || [];
    return permissions.includes(permission);
};

// Debug
console.log('User:', user.value);
console.log('User roles:', userRoles.value);
console.log('Is Super Admin:', isSuperAdmin.value);
console.log('Has role super_admin:', hasRole('super_admin'));
console.log('Has any role [super_admin, admin]:', hasAnyRole(['super_admin', 'admin']));

// Toggle functions
const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const toggleUserManagement = () => {
    userManagementManual.value = !userManagementManual.value;
};

const toggleRoleManagement = () => {
    roleManagementManual.value = !roleManagementManual.value;
};

const toggleEmployeeManagement = () => {
    employeeManagementManual.value = !employeeManagementManual.value;
};

const toggleSettings = () => {
    settingsManual.value = !settingsManual.value;
};

// Active route helper
const currentRoute = computed(() => window.location.pathname);
const isActive = (routeUrl) => currentRoute.value === routeUrl;

// Routes for active state
const userManagementRoutes = [route('users.index')];
const roleManagementRoutes = [route('roles.index'), route('permissions.assign')];
const employeeManagementRoutes = [route('pim.index'), route('pim.EmployeeList'), route('pim.Reports')];
const settingsRoutes = [route('settings.index')];

const userManagementOpen = computed(() => {
    return userManagementManual.value || userManagementRoutes.some(r => isActive(r));
});

const roleManagementOpen = computed(() => {
    return roleManagementManual.value || roleManagementRoutes.some(r => isActive(r));
});

const employeeManagementOpen = computed(() => {
    return employeeManagementManual.value || employeeManagementRoutes.some(r => isActive(r));
});

const settingsOpen = computed(() => {
    return settingsManual.value || settingsRoutes.some(r => isActive(r));
});

// Logout
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

                <!-- ============================================ -->
                <!-- ✅ MY PROFILE - Visible to ALL authenticated users -->
                <!-- ============================================ -->
                <!-- Check if user has an employee record -->
                <Link v-if="user?.employee_id" :href="route('pim.getPersonalDetails', user.employee_id)"
                    class="flex items-center gap-3 p-3 rounded-md sideBarMenuColor hover:bg-[#FF9B00] hover:text-white transition">
                    <i class="bx bx-user-circle text-2xl w-6 flex-shrink-0"></i>
                    <span v-show="sidebarOpen" class="whitespace-nowrap">My Profile</span>
                </Link>

                <!-- If no employee record, show a message or hide the link -->
                <div v-else-if="user" class="flex items-center gap-3 p-3 rounded-md text-gray-400">
                    <i class="bx bx-user-circle text-2xl w-6 flex-shrink-0"></i>
                    <span v-show="sidebarOpen" class="whitespace-nowrap text-sm">No profile available</span>
                </div>
                <!-- ============================================ -->
                <!-- ✅ USER MANAGEMENT - Only Super Admin, Admin, HR Manager -->
                <!-- ============================================ -->
                <div v-if="hasAnyRole(['super_admin', 'admin', 'hr_manager'])" class="flex flex-col">
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
                    <div v-show="userManagementOpen" class="ml-10 mt-1 flex flex-col gap-1">
                        <Link :href="route('users.index')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('users.index')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                            <i class="bx bx-user text-sm"></i>
                            Users
                        </Link>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- ✅ ROLE MANAGEMENT - Only Super Admin and Admin -->
                <!-- ============================================ -->
                <div v-if="hasAnyRole(['super_admin', 'admin'])" class="flex flex-col">
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
                    <div v-show="roleManagementOpen" class="ml-10 mt-1 flex flex-col gap-1">
                        <Link :href="route('roles.index')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('roles.index')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                            <i class="bx bx-lock text-sm"></i>
                            Roles
                        </Link>
                        <Link :href="route('permissions.assign')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('permissions.assign')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                            <i class="bx bx-key text-sm"></i>
                            Permissions
                        </Link>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- ✅ PIM - Super Admin, Admin, HR Manager, Department Manager -->
                <!-- ============================================ -->
                <div v-if="hasAnyRole(['super_admin', 'admin', 'hr_manager', 'department_manager'])"
                    class="flex flex-col">
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
                    <div v-show="employeeManagementOpen" class="ml-10 mt-1 flex flex-col gap-1">
                        <Link v-if="can('view_employees')" :href="route('pim.index')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('pim.index')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                            <i class="bx bx-list-ul text-sm"></i>
                            Employee List
                        </Link>
                        <Link v-if="can('create_employee')" :href="route('pim.create')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('pim.create')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                            <i class="bx bx-user-plus text-sm"></i>
                            Add Employee
                        </Link>
                        <Link v-if="can('view_reports')" :href="route('pim.Reports')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('pim.Reports')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                            <i class="bx bx-bar-chart-alt-2 text-sm"></i>
                            Reports
                        </Link>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- ✅ SETTINGS - Only Super Admin and Admin -->
                <!-- ============================================ -->
                <div v-if="hasAnyRole(['super_admin', 'admin'])" class="flex flex-col">
                    <button @click="toggleSettings"
                        class="flex items-center gap-3 p-3 rounded-md sideBarMenuColor hover:bg-[#FF9B00] hover:text-white transition w-full">
                        <i class="bx bx-equalizer text-2xl w-6 flex-shrink-0"></i>
                        <span v-show="sidebarOpen" class="flex-1 text-left">Settings</span>
                        <svg v-show="sidebarOpen" :class="{ 'rotate-90': settingsOpen }"
                            class="h-4 w-4 transition-transform ml-auto" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div v-show="settingsOpen" class="ml-10 mt-1 flex flex-col gap-1">
                        <Link :href="route('settings.index')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('settings.index')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                            <i class="bx bx-cog text-sm"></i>
                            General Settings
                        </Link>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main content with top navigation -->
        <div class="w-full flex flex-col">
            <!-- Top Navigation -->
            <nav
                class="border-b border-gray-100 header flex justify-between px-4 py-3 items-center relative left-0 w-full z-10">
                <div class="w-full ml-[14%] flex justify-between items-center toggleNav">
                    <div class="flex items-center gap-4">
                        <span class="md:text-lg font-semibold text-sm ml-4">HRM Dashboard</span>
                    </div>
                    <div class="sm:ms-6 sm:flex sm:items-center gap-4">
                        <!-- User Info -->
                        <div class="text-sm text-gray-600 hidden sm:block">
                            <span class="font-medium">{{ $page.props.auth?.user?.name || 'Guest' }}</span>
                            <span class="text-gray-400 text-xs ml-2">
                                <!-- ✅ Check both roles array and role string -->
                                <template
                                    v-if="$page.props.auth?.user?.roles && $page.props.auth.user.roles.length > 0">
                                    ({{ $page.props.auth.user.roles.join(', ') }})
                                </template>
                                <template v-else-if="$page.props.auth?.user?.role">
                                    ({{ $page.props.auth.user.role }})
                                </template>
                                <template v-else>
                                    (No roles)
                                </template>
                            </span>
                        </div>

                        <!-- Logout Button -->
                        <button @click="handleLogout"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center gap-2">
                            <i class="bx bx-log-out"></i>
                            <span>Logout</span>
                        </button>
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
            <main class="flex-1 xl:pl-[18rem] lg:pl-[10rem] py-6 sm:pl-[6rem] pl-[5rem]">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Your existing styles */
</style>