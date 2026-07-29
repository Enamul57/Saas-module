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

// ✅ Get roles
const userRoles = computed(() => {
    return user.value?.roles || [];
});

// ✅ Get permissions
const userPermissions = computed(() => {
    return user.value?.permissions || [];
});

// ✅ Check if user is Super Admin
const isSuperAdmin = computed(() => {
    return userRoles.value.includes('super_admin');
});

// ✅ Check if user has a specific role
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
    return userPermissions.value.includes(permission);
};

// ✅ Check if a route exists
const routeExists = (routeName) => {
    try {
        return !!route(routeName);
    } catch (e) {
        return false;
    }
};

// ✅ Define which modules are available
const availableModules = {
    users: routeExists('users.index'),
    roles: routeExists('roles.index'),
    permissions: routeExists('permissions.assign'),
    pim: routeExists('pim.index'),
    settings: routeExists('settings.index'),
};

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
const isActive = (routeUrl) => {
    if (!routeUrl) return false;
    return currentRoute.value === routeUrl;
};

// Routes for active state
const userManagementRoutes = availableModules.users ? [route('users.index')] : [];
const roleManagementRoutes = [];
if (availableModules.roles) roleManagementRoutes.push(route('roles.index'));
if (availableModules.permissions) roleManagementRoutes.push(route('permissions.assign'));

const employeeManagementRoutes = [];
if (availableModules.pim) {
    if (routeExists('pim.index')) employeeManagementRoutes.push(route('pim.index'));
    if (routeExists('pim.EmployeeList')) employeeManagementRoutes.push(route('pim.EmployeeList'));
    if (routeExists('pim.Reports')) employeeManagementRoutes.push(route('pim.Reports'));
}

const settingsRoutes = availableModules.settings ? [route('settings.index')] : [];

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

// Emit logout event
const emit = defineEmits(['logout']);

const handleLogout = () => {
    emit('logout');
};
</script>

<template>
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
            <span v-show="sidebarOpen" class="whitespace-nowrap ml-4">
                <img src="/images/logo.png" alt="" class="w-40">
            </span>
        </button>

        <!-- Sidebar Menu -->
        <nav class="mt-4 flex flex-col gap-1 px-2 overflow-y-auto flex-1">

            <!-- ============================================ -->
            <!-- 📊 DASHBOARD - All authenticated users -->
            <!-- ============================================ -->
            <Link :href="route('central.dashboard')"
                class="flex items-center gap-3 p-3 rounded-md sideBarMenuColor hover:bg-[#FF9B00] hover:text-white transition">
                <i class="bx bx-grid-alt text-2xl w-6 flex-shrink-0"></i>
                <span v-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
            </Link>

            <!-- ============================================ -->
            <!-- 👤 MY PROFILE - All authenticated users with employee record -->
            <!-- ============================================ -->
            <Link v-if="user?.employee_id" :href="route('pim.getPersonalDetails', user.employee_id)"
                class="flex items-center gap-3 p-3 rounded-md sideBarMenuColor hover:bg-[#FF9B00] hover:text-white transition">
                <i class="bx bx-user-circle text-2xl w-6 flex-shrink-0"></i>
                <span v-show="sidebarOpen" class="whitespace-nowrap">My Profile</span>
            </Link>

            <!-- ============================================ -->
            <!-- 👥 USER MANAGEMENT - Check PERMISSION instead of ROLE -->
            <!-- ============================================ -->
            <div v-if="availableModules.users && can('view_users')" class="flex flex-col">
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
            <!-- 🔐 ROLE MANAGEMENT - Check PERMISSION instead of ROLE -->
            <!-- ============================================ -->
            <div v-if="availableModules.roles && can('view_roles')" class="flex flex-col">
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
                    <Link v-if="availableModules.permissions" :href="route('permissions.assign')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                        isActive(route('permissions.assign')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        <i class="bx bx-key text-sm"></i>
                        Permissions
                    </Link>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 👥 PIM (Employee Management) - Check PERMISSION instead of ROLE -->
            <!-- ============================================ -->
            <div v-if="availableModules.pim && can('view_employees')" class="flex flex-col">
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
                    <Link :href="route('pim.index')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                        isActive(route('pim.index')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        <i class="bx bx-list-ul text-sm"></i>
                        Employee List
                    </Link>
                    <Link v-if="can('create_employee') && routeExists('pim.create')" :href="route('pim.create')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                        isActive(route('pim.create')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        <i class="bx bx-user-plus text-sm"></i>
                        Add Employee
                    </Link>
                    <Link v-if="can('view_reports') && routeExists('pim.Reports')" :href="route('pim.Reports')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                        isActive(route('pim.Reports')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        <i class="bx bx-bar-chart-alt-2 text-sm"></i>
                        Reports
                    </Link>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- ⚙️ SETTINGS - Check PERMISSION instead of ROLE -->
            <!-- ============================================ -->
            <div v-if="availableModules.settings && can('manage_settings')" class="flex flex-col">
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

        <!-- Bottom section with user info -->
        <div class="p-3 border-t border-gray-200" v-show="sidebarOpen">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-full bg-amber-500 flex items-center justify-center text-white font-bold text-sm">
                    {{ user?.name?.charAt(0) || 'U' }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-700 truncate">{{ user?.name || 'Guest' }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ user?.email || '' }}</p>
                    <p class="text-xs text-gray-400 truncate" v-if="userRoles.length > 0">
                        Roles: {{ userRoles.join(', ') }}
                    </p>
                </div>
            </div>
        </div>
    </aside>
</template>

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>