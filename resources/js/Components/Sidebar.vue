<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const $page = usePage();
const sidebarOpen = ref(true);

// Toggle states
const userManagementManual = ref(false);
const roleManagementManual = ref(false);
const employeeManagementManual = ref(false);
const settingsManual = ref(false);

// Get user data from page props
const user = computed(() => $page.props.auth?.user);
const userRoles = computed(() => user.value?.roles || []);
const userPermissions = computed(() => user.value?.permissions || []);
const userFeatures = computed(() => user.value?.features || []);

// Check if user is Super Admin
const isSuperAdmin = computed(() => {
    return userRoles.value.some(r => r.toLowerCase() === 'super_admin');
});

// Check if user has a specific feature/module
const hasFeature = (featureName) => {
    if (isSuperAdmin.value) return true;
    return userFeatures.value.some(f => f.toLowerCase() === featureName.toLowerCase());
};

const can = (permission) => {
    if (isSuperAdmin.value) return true;
    // Check exact match first
    if (userPermissions.value.some(p => p === permission)) {
        return true;
    }
    // Check case-insensitive
    if (userPermissions.value.some(p => p.toLowerCase() === permission.toLowerCase())) {
        return true;
    }
    return false;
};


const routeExists = (routeName) => {
    try {
        return !!route(routeName);
    } catch (e) {
        return false;
    }
};

const modules = computed(() => ({
    users: {
        hasFeature: hasFeature('User Management'),
        canView: can('view_users'),
        canCreate: can('create_users'),
        canEdit: can('edit_users'),
        canDelete: can('delete_users'),
        hasAnyPermission: can('view_users') || can('create_users') || can('edit_users') || can('delete_users'),
    },
    roles: {
        hasFeature: hasFeature('Role Management'),
        canView: can('view_roles'),
        canCreate: can('create_roles'),
        canEdit: can('edit_roles'),
        canDelete: can('delete_roles'),
        hasAnyPermission: can('view_roles') || can('create_roles') || can('edit_roles') || can('delete_roles'),
    },
    permissions: {
        hasFeature: hasFeature('Permission Management'),
        canView: can('view_permissions'),
        canAssign: can('assign_permissions'),
        hasAnyPermission: can('view_permissions') || can('assign_permissions'),
    },
    pim: {
        // ✅ Check multiple feature names
        hasFeature: hasFeature('PIM') || hasFeature('Employee Management') || hasFeature('Payroll Management'),
        canView: can('view_employees'),
        canCreate: can('create_employee'),
        canEdit: can('edit_employee'),
        canDelete: can('delete_employee'),
        canViewDetails: can('view_employee_details'),
        canViewReports: can('view_reports'),
        // ✅ Check ALL PIM-related permissions
        hasAnyPermission: can('view_employees') || can('create_employee') || can('edit_employee') ||
            can('delete_employee') || can('view_employee_details') || can('view_reports') ||
            can('view_employee_salary') || can('view_jobs') || can('view_job_details') ||
            can('export_employees') || can('import_employees') || can('manage_employee_documents'),
    },
    settings: {
        hasFeature: hasFeature('Settings'),
        canManage: can('manage_settings'),
        hasAnyPermission: can('manage_settings'),
    },
    dashboard: {
        canView: can('view_dashboard'),
    }
}));

// Module is visible if user has the feature OR any permission
const isModuleVisible = (module) => {
    return module.hasFeature || module.hasAnyPermission;
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
const userManagementRoutes = [];
if (modules.value.users.canView && routeExists('users.index')) {
    userManagementRoutes.push(route('users.index'));
}

const roleManagementRoutes = [];
if (modules.value.roles.canView && routeExists('roles.index')) {
    roleManagementRoutes.push(route('roles.index'));
}
if (modules.value.permissions.canView && routeExists('permissions.assign')) {
    roleManagementRoutes.push(route('permissions.assign'));
}

const employeeManagementRoutes = [];
if (modules.value.pim.canView && routeExists('pim.index')) {
    employeeManagementRoutes.push(route('pim.index'));
}
if (modules.value.pim.canViewReports && routeExists('pim.Reports')) {
    employeeManagementRoutes.push(route('pim.Reports'));
}

const settingsRoutes = [];
if (modules.value.settings.canManage && routeExists('settings.index')) {
    settingsRoutes.push(route('settings.index'));
}

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

            <!-- Dashboard -->
            <Link v-if="modules.dashboard.canView" :href="route('central.dashboard')"
                class="flex items-center gap-3 p-3 rounded-md sideBarMenuColor hover:bg-[#FF9B00] hover:text-white transition">
                <i class="bx bx-grid-alt text-2xl w-6 flex-shrink-0"></i>
                <span v-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
            </Link>

            <!-- My Profile -->
            <Link v-if="user?.employee_id" :href="route('pim.getPersonalDetails', user.employee_id)"
                class="flex items-center gap-3 p-3 rounded-md sideBarMenuColor hover:bg-[#FF9B00] hover:text-white transition">
                <i class="bx bx-user-circle text-2xl w-6 flex-shrink-0"></i>
                <span v-show="sidebarOpen" class="whitespace-nowrap">My Profile</span>
            </Link>

            <!-- ============================================ -->
            <!-- USER MANAGEMENT - Shows based on FEATURE or PERMISSIONS -->
            <!-- ============================================ -->
            <div v-if="isModuleVisible(modules.users)" class="flex flex-col">
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

                <!-- Sub-items based on PERMISSIONS -->
                <div v-show="userManagementOpen" class="ml-10 mt-1 flex flex-col gap-1">
                    <!-- View Users - User HAS this permission -->
                    <Link v-if="modules.users.canView && routeExists('users.index')" :href="route('users.index')"
                        :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('users.index')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        <i class="bx bx-user text-sm"></i>
                        View Users
                        <span class="ml-auto text-xs text-green-500">✓</span>
                    </Link>

                    <!-- Create User - User DOES NOT have this permission -->
                    <Link v-if="modules.users.canCreate && routeExists('users.create')" :href="route('users.create')"
                        :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('users.create')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        <i class="bx bx-user-plus text-sm"></i>
                        Add User
                    </Link>



                    <div v-if="!modules.users.hasAnyPermission"
                        class="flex items-center gap-2 p-2 rounded-md text-sm text-gray-400 italic">
                        <i class="bx bx-lock-alt text-sm"></i>
                        No accessible actions - Contact administrator
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- ROLE MANAGEMENT - Shows based on FEATURE or PERMISSIONS -->
            <!-- ============================================ -->
            <div v-if="isModuleVisible(modules.roles) || isModuleVisible(modules.permissions)" class="flex flex-col">
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
                    <Link v-if="modules.roles.canView && routeExists('roles.index')" :href="route('roles.index')"
                        :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('roles.index')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        <i class="bx bx-lock text-sm"></i>
                        Roles
                    </Link>

                    <Link v-if="modules.permissions.canView && routeExists('permissions.assign')"
                        :href="route('permissions.assign')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('permissions.assign')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        <i class="bx bx-key text-sm"></i>
                        Permissions
                    </Link>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- PIM - Shows based on FEATURE or PERMISSIONS -->
            <!-- ============================================ -->
            <div v-if="isModuleVisible(modules.pim)" class="flex flex-col">
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
                    <Link v-if="modules.pim.canView && routeExists('pim.index')" :href="route('pim.index')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                        isActive(route('pim.index')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        <i class="bx bx-list-ul text-sm"></i>
                        Employee List
                    </Link>

                    <Link v-if="modules.pim.canCreate && routeExists('pim.create')" :href="route('pim.create')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                        isActive(route('pim.create')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        <i class="bx bx-user-plus text-sm"></i>
                        Add Employee
                    </Link>

                    <Link v-if="modules.pim.canViewReports && routeExists('pim.Reports')" :href="route('pim.Reports')"
                        :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
                            isActive(route('pim.Reports')) ? 'bg-[#FF9B00] text-white' : 'sideBarMenuColor']">
                        <i class="bx bx-bar-chart-alt-2 text-sm"></i>
                        Reports
                    </Link>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- SETTINGS - Shows based on FEATURE or PERMISSIONS -->
            <!-- ============================================ -->
            <div v-if="isModuleVisible(modules.settings)" class="flex flex-col">
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
                    <Link v-if="modules.settings.canManage && routeExists('settings.index')"
                        :href="route('settings.index')" :class="['flex items-center gap-2 p-2 rounded-md text-sm transition hover:bg-[#EBE389] hover:text-slate-700',
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
                    <p class="text-xs text-gray-400 truncate" v-if="userFeatures.length > 0">
                        Features: {{ userFeatures.join(', ') }}
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