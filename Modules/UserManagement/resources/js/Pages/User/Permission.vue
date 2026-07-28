<template>

    <Head title="Permission Management" />
    <AuthenticatedLayout>
        <div class="p-6 space-y-6">
            <h2 class="text-2xl font-semibold text-gray-700">Permission Management</h2>

            <!-- Select Module -->
            <div class="relative w-full">
                <button @click="open = !open" class="w-full px-2 py-2 borderInput rounded shadow-sm text-left">
                    {{ selectedModuleName || "Select a module" }}
                </button>
                <ul v-if="open"
                    class="absolute w-full bg-white shadow rounded mt-1 z-10 max-h-48 overflow-y-auto flex flex-col space-y-1">
                    <li v-for="module in modules" :key="module.id" @click="selectModule(module)"
                        class="cursor-pointer px-2 py-1 primary hover:bg-amber-100">
                        {{ module.name }}
                    </li>
                </ul>

                <!-- Module Info -->
                <div v-if="selectedModule" class="mt-2 text-sm text-gray-500">
                    Module: <span class="font-semibold">{{ selectedModule.name }}</span>
                    <span class="ml-2 text-xs bg-gray-100 px-2 py-1 rounded">{{ selectedModule.slug }}</span>
                </div>

                <!-- Permissions Selection -->
                <div class="flex-1 mt-5">
                    <label class="block text-sm text-gray-600 mb-1 font-bold">Permissions</label>

                    <!-- Predefined Permission Templates -->
                    <div class="mb-3 flex flex-wrap gap-2">
                        <button @click="selectAllPermissions"
                            class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded">
                            Select All
                        </button>
                        <button @click="deselectAllPermissions"
                            class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded">
                            Deselect All
                        </button>
                        <button @click="selectCRUDPermissions"
                            class="text-xs bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded">
                            CRUD (Create, View, Edit, Delete)
                        </button>
                        <button @click="selectViewOnly"
                            class="text-xs bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-1 rounded">
                            View Only
                        </button>
                        <button @click="selectAdminPermissions"
                            class="text-xs bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1 rounded">
                            Admin (All)
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-2 max-h-60 overflow-y-auto p-2 border border-gray-200 rounded-lg">
                        <label v-for="permission in permissions" :key="permission.name"
                            class="flex items-center cursor-pointer bg-gray-50 hover:bg-amber-100 rounded-lg px-3 py-2 shadow-sm transition-colors duration-200">
                            <input type="checkbox" :value="permission.name" v-model="selectedPermissions"
                                class="accent-amber-500 w-4 h-4 rounded border-gray-300" />
                            <span class="ml-2 text-sm text-gray-800 font-medium">{{ permission.name }}</span>
                            <!-- Show if it's already assigned -->
                            <span v-if="isPermissionAssigned(permission.name)" class="ml-2 text-xs text-green-500">
                                ✓ Assigned
                            </span>
                        </label>
                    </div>

                    <!-- Selected count -->
                    <div class="mt-2 text-sm text-gray-500">
                        Selected: <span class="font-semibold">{{ selectedPermissions.length }}</span> permissions
                    </div>
                </div>
            </div>

            <!-- Save button -->
            <div class="flex justify-end mt-4 gap-2">
                <button type="button" @click="cancelEdit" class="cancelButton buttonSize px-4 py-2 rounded-lg"
                    v-show="isEditable">
                    Cancel
                </button>
                <button @click="assignModules"
                    class="primaryColor px-4 py-2 rounded-md text-sm shadow cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ !isEditable ? 'Assign Permissions' : 'Update Permissions' }}
                </button>
            </div>
        </div>

        <!-- Table of Module Permissions -->
        <div class="p-6 bg-white rounded-xl shadow-md mt-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Assigned Module Permissions</h3>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 font-medium text-gray-500 uppercase tracking-wider text-sm text-left">
                            Module</th>
                        <th class="px-3 py-2 font-medium text-gray-500 uppercase tracking-wider text-sm text-left">
                            Permissions</th>
                        <th class="px-3 py-2 font-medium text-gray-500 uppercase tracking-wider text-sm text-center">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(row) in module_permission" :key="row.module.id" class="hover:bg-gray-50">
                        <td class="px-3 py-2 whitespace-nowrap text-slate-600 font-medium">{{ row.module.name }}</td>
                        <td class="px-3 py-2 text-slate-600">
                            <div class="flex flex-wrap gap-1">
                                <span v-for="perm in row.permissions_list" :key="perm"
                                    class="text-xs bg-amber-100 text-amber-800 px-2 py-1 rounded-full">
                                    {{ perm }}
                                </span>
                            </div>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-slate-600 text-center">
                            <button @click="editModulePermission(row)"
                                class="text-blue-500 hover:underline text-sm font-semibold mx-2">
                                Edit
                            </button>
                            <button @click="deletePermission(row.module.id)"
                                class="text-red-500 hover:underline text-sm font-semibold">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="module_permission.length === 0">
                        <td class="px-3 py-2 text-center text-gray-400 text-sm" colspan="3">
                            No permissions assigned yet.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Popup Modal -->
        <Popup v-model:showPopup="showPopup" :popupMessage="popupMessage" />
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import Popup from '@/Components/Popup.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, watch, onMounted } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

const notyf = new Notyf({
    duration: 3000,
    position: {
        x: 'right',
        y: 'top',
    },
    dismissible: true,
});

// Props
const props = defineProps({
    modules: {
        type: Object,
        required: true,
    },
    module_permission: {
        type: Object,
        required: true,
    }
});

// Hooks
onMounted(() => {
    fetchModulePermission();
});

// Variables
const selectedModuleName = ref<any>(null);
const selectedModule = ref<any>(null);
const selectedPermissions = ref<string[]>([]);
const open = ref<boolean>(false);
const showPopup = ref(false);
const popupMessage = ref("");
const isEditable = ref<boolean>(false);
const oldModuleId = ref(null);
const modules = ref<any[]>([]);
const module_permission = ref<any[]>([]);
const allPermissions = ref<string[]>([]);

const tableHeader = [
    { name: "module" },
    { name: "permissions" },
    { name: "actions" }
];

// Predefined permission templates from seeder
const permissions = ref<any[]>([
    // User Management
    { name: 'view_users' },
    { name: 'create_users' },
    { name: 'edit_users' },
    { name: 'delete_users' },
    { name: 'assign_roles' },

    // Role & Permission Management
    { name: 'view_roles' },
    { name: 'create_roles' },
    { name: 'edit_roles' },
    { name: 'delete_roles' },
    { name: 'assign_permissions_to_roles' },

    // Employee Management (PIM)
    { name: 'view_employees' },
    { name: 'create_employee' },
    { name: 'edit_employee' },
    { name: 'delete_employee' },
    { name: 'view_employee_details' },
    { name: 'export_employees' },
    { name: 'import_employees' },
    { name: 'manage_employee_documents' },

    // Job Management
    { name: 'view_jobs' },
    { name: 'create_job' },
    { name: 'edit_job' },
    { name: 'delete_job' },
    { name: 'view_job_details' },
    { name: 'manage_job_categories' },
    { name: 'manage_job_titles' },
    { name: 'manage_job_units' },

    // Attendance
    { name: 'view_attendance' },
    { name: 'create_attendance' },
    { name: 'edit_attendance' },
    { name: 'delete_attendance' },
    { name: 'view_attendance_reports' },

    // Leave
    { name: 'view_leaves' },
    { name: 'create_leave' },
    { name: 'edit_leave' },
    { name: 'delete_leave' },
    { name: 'approve_leave' },
    { name: 'view_leave_reports' },

    // Payroll
    { name: 'view_payroll' },
    { name: 'create_payroll' },
    { name: 'edit_payroll' },
    { name: 'delete_payroll' },
    { name: 'process_payroll' },
    { name: 'view_salary_reports' },
    { name: 'view_employee_salary' },

    // Reports
    { name: 'view_reports' },
    { name: 'create_reports' },
    { name: 'generate_reports' },
    { name: 'export_reports' },
    { name: 'schedule_reports' },

    // Settings
    { name: 'view_settings' },
    { name: 'manage_settings' },
    { name: 'manage_company_settings' },
    { name: 'manage_email_settings' },
    { name: 'manage_security_settings' },

    // Dashboard
    { name: 'view_dashboard' },
    { name: 'view_analytics' },
]);

// Form for saving permissions
const assignedPermissions = useForm({
    modules: [],
    permissions: [],
});

// Functions
const fetchModulePermission = async () => {
    try {
        const response = await axios.get(route('permissions.module.fetch'));
        modules.value = response.data.modules;
        module_permission.value = response.data.module_permission.map((mp) => {
            let permissions = mp.permissions;
            let permissionsList = permissions.map((p) => p.name);
            return {
                module: mp,
                permissions: permissionsList.join(', '),
                permissions_list: permissionsList,
            };
        });
        allPermissions.value = permissions.value.map(p => p.name);
    } catch (error) {
        console.error("Error fetching module permissions:", error);
    }
};

const cancelEdit = () => {
    assignedPermissions.modules = [];
    assignedPermissions.permissions = [];
    selectedPermissions.value = [];
    selectedModuleName.value = null;
    selectedModule.value = null;
    isEditable.value = false;
    oldModuleId.value = null;
};

const assignModules = async () => {
    if (!selectedModuleName.value) {
        popupMessage.value = "Please select a module first.";
        showPopup.value = true;
        return;
    }
    if (selectedPermissions.value.length === 0) {
        popupMessage.value = "Please select a permission first.";
        showPopup.value = true;
        return;
    }

    assignedPermissions.modules = selectedModule.value;
    assignedPermissions.permissions = selectedPermissions.value;

    const onSuccess = async () => {
        selectedPermissions.value = [];
        selectedModuleName.value = null;
        selectedModule.value = null;
        await fetchModulePermission();
        cancelEdit();
    };

    if (!isEditable.value) {
        await assignedPermissions.post(route('permission.module.store'), {
            onSuccess: () => {
                notyf.success('Permissions assigned successfully!');
                onSuccess();
            },
            onError: (errors) => {
                notyf.error('Error assigning permissions');
                console.error(errors);
            }
        });
    } else {
        await assignedPermissions.put(route('permission.module.update', { id: oldModuleId.value }), {
            onSuccess: () => {
                notyf.success('Permissions updated successfully!');
                onSuccess();
            },
            onError: (errors) => {
                notyf.error('Error updating permissions');
                console.error(errors);
            }
        });
    }
};

const selectModule = (moduleObj: Object) => {
    open.value = false;
    const module = modules.value.find(m => m.id === moduleObj.id);
    if (module) {
        selectedModuleName.value = module.name;
        selectedModule.value = module;
        assignedPermissions.modules = [module];
    }
};

const editModulePermission = (data: any) => {
    const matchedModule = modules.value.find((module) => module.id === data.module.id);
    oldModuleId.value = data.module.id;

    if (!matchedModule) return;

    selectedModuleName.value = matchedModule.name;
    selectedModule.value = matchedModule;

    if (!assignedPermissions.modules.some(m => m.id === matchedModule.id)) {
        assignedPermissions.modules.push(matchedModule);
    }

    // Set selected permissions based on existing permissions
    selectedPermissions.value = data.permissions_list || [];
    isEditable.value = true;
};

const deletePermission = (moduleId: number) => {
    if (confirm('Are you sure you want to delete these permissions?')) {
        assignedPermissions.delete(route('permission.module.delete', { id: moduleId }), {
            onSuccess: () => {
                module_permission.value = module_permission.value.filter(mp => mp.module.id !== moduleId);
                selectedPermissions.value = [];
                selectedModuleName.value = null;
                selectedModule.value = null;
                notyf.success('Permissions deleted successfully!');
            },
            onError: () => {
                notyf.error('Error deleting permissions');
            }
        });
    }
};

// Helper functions for permission selection
const selectAllPermissions = () => {
    selectedPermissions.value = permissions.value.map(p => p.name);
};

const deselectAllPermissions = () => {
    selectedPermissions.value = [];
};

const selectCRUDPermissions = () => {
    const crudPatterns = ['create', 'view', 'edit', 'delete'];
    selectedPermissions.value = permissions.value
        .filter(p => crudPatterns.some(pattern => p.name.includes(pattern)))
        .map(p => p.name);
};

const selectViewOnly = () => {
    selectedPermissions.value = permissions.value
        .filter(p => p.name.includes('view') || p.name.includes('view_'))
        .map(p => p.name);
};

const selectAdminPermissions = () => {
    selectedPermissions.value = permissions.value.map(p => p.name);
};

const isPermissionAssigned = (permissionName: string) => {
    // Check if this permission is already assigned to the current module
    if (selectedModule.value) {
        const module = module_permission.value.find(mp => mp.module.id === selectedModule.value.id);
        if (module) {
            return module.permissions_list.includes(permissionName);
        }
    }
    return false;
};
</script>