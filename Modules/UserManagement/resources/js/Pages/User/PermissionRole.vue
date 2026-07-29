<template>

    <Head title="Permission Management" />
    <AuthenticatedLayout>
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-gray-700">
                    Role Permission Management - <span class="text-amber-600">{{ role_name }}</span>
                </h2>
                <span class="text-sm text-gray-500">Role ID: {{ role_id }}</span>
            </div>

            <!-- Modules & Permissions -->
            <div class="space-y-6">
                <div v-for="module in module_permission" :key="module.id"
                    class="p-4 border rounded-lg shadow-sm bg-white">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-gray-700 text-lg">{{ module.name }}</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400">
                                {{ getSelectedCount(module.id) }} / {{ module.permissions?.length || 0 }} selected
                            </span>
                            <button @click="toggleAllPermissions(module.id)"
                                class="text-xs text-amber-600 hover:text-amber-800 font-medium">
                                {{ isAllSelected(module.id) ? 'Deselect All' : 'Select All' }}
                            </button>
                        </div>
                    </div>

                    <div v-if="module.permissions && module.permissions.length > 0" class="flex flex-wrap gap-2">
                        <label v-for="permission in module.permissions" :key="permission.id"
                            class="flex items-center cursor-pointer bg-gray-50 hover:bg-amber-50 rounded-lg px-3 py-2 shadow-sm transition-colors duration-200 border border-transparent hover:border-amber-200"
                            :class="{ 'bg-amber-50 border-amber-200': isPermissionChecked(module.id, permission.id) }">
                            <input type="checkbox" :value="permission.id"
                                v-model="assignedPermissions.modules[module.id]"
                                class="accent-amber-500 w-4 h-4 rounded border-gray-300 focus:ring-amber-400" />
                            <span class="ml-2 text-sm text-gray-800 font-medium">{{ permission.name }}</span>
                            <!-- Show assigned badge -->
                            <span v-if="permission.assigned" class="ml-2 text-xs text-green-500">
                                ✓ Assigned
                            </span>
                        </label>
                    </div>

                    <div v-else class="text-gray-400 italic text-sm">
                        No permissions available for this module.
                    </div>
                </div>
            </div>

            <!-- Save button -->
            <div class="flex justify-end mt-6 gap-3 pt-4 border-t border-gray-200">
                <button type="button" @click="cancelEdit" class="cancelButton px-6 py-2 rounded-lg transition-colors"
                    v-show="isEditable">
                    Cancel
                </button>
                <button @click="assignModules"
                    class="primaryColor px-6 py-2 rounded-lg text-sm shadow cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    :disabled="assignedPermissions.processing">
                    <i v-if="assignedPermissions.processing" class="bx bx-loader-alt animate-spin mr-2"></i>
                    {{ assignedPermissions.processing ?
                        'Saving...' :
                        (isEditable ? 'Update Permissions' :
                            'Assign Permissions') }}
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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

// ✅ Props - Add module_permission
const props = defineProps({
    roles: { type: Object, required: true },
    module_permission: { type: Object, required: true }, // ✅ Add this
    role_name: { type: String, required: true },
    role_id: { type: Number, required: true }
});

// ✅ Use module_permission from props
const module_permission = computed(() => {
    return props.module_permission || [];
});

// Track selected permissions per module
const assignedPermissions = useForm({
    modules: {} as Record<number, number[]>
});

// Initialize module arrays with existing permissions
onMounted(() => {
    console.log('Module Permission Data:', props.module_permission);
    initializePermissions();
});

// ✅ Initialize with ONLY assigned permissions
const initializePermissions = () => {
    // Clear existing selections
    assignedPermissions.modules = {};

    props.module_permission.forEach((module: any) => {
        // ✅ Get permissions where assigned === true
        const assignedIds = module.permissions
            ?.filter((p: any) => p.assigned === true)
            .map((p: any) => p.id) || [];

        assignedPermissions.modules[module.id] = [...assignedIds];

        console.log(`Module ${module.id} (${module.name}):`, {
            total: module.permissions?.length || 0,
            assigned: assignedIds.length,
            assignedIds: assignedIds,
        });
    });
};

// Check if a specific permission is checked
const isPermissionChecked = (moduleId: number, permissionId: number) => {
    const modulePermissions = assignedPermissions.modules[moduleId] || [];
    return modulePermissions.includes(permissionId);
};

// Get the count of selected permissions for a module
const getSelectedCount = (moduleId: number) => {
    return (assignedPermissions.modules[moduleId] || []).length;
};

// Check if all permissions are selected for a module
const isAllSelected = (moduleId: number) => {
    const module = module_permission.value.find((m: any) => m.id === moduleId);
    if (!module || !module.permissions) return false;

    const total = module.permissions.length;
    const selected = getSelectedCount(moduleId);
    return total > 0 && selected === total;
};

// Toggle all permissions for a module
const toggleAllPermissions = (moduleId: number) => {
    const module = module_permission.value.find((m: any) => m.id === moduleId);
    if (!module || !module.permissions) return;

    const allIds = module.permissions.map((p: any) => p.id);
    const currentIds = assignedPermissions.modules[moduleId] || [];

    if (currentIds.length === allIds.length) {
        // Deselect all
        assignedPermissions.modules[moduleId] = [];
    } else {
        // Select all
        assignedPermissions.modules[moduleId] = allIds;
    }
};

// Reset form
const cancelEdit = () => {
    initializePermissions();
    isEditable.value = false;
};

// Track edit state
const isEditable = ref(false);

// Save assigned permissions
const assignModules = () => {
    // Prepare data for submission
    const modulesData: Record<string, number[]> = {};

    Object.keys(assignedPermissions.modules).forEach((key) => {
        const moduleId = parseInt(key);
        if (assignedPermissions.modules[moduleId]?.length > 0) {
            modulesData[moduleId] = assignedPermissions.modules[moduleId];
        }
    });

    console.log('Submitting data:', modulesData);

    assignedPermissions.modules = modulesData;

    assignedPermissions.post(route('role.permission.store', { role: props.role_id }), {
        onSuccess: () => {
            isEditable.value = false;
            notyf.success("Permissions assigned successfully!");
        },
        onError: (errors) => {
            notyf.error("Error assigning permissions!");
            console.error(errors);
        }
    });
};

// Watch for changes to mark as editable
watch(() => assignedPermissions.modules, () => {
    let hasChanges = false;

    props.module_permission.forEach((module: any) => {
        const originallyAssignedIds = module.permissions
            ?.filter((p: any) => p.assigned === true)
            .map((p: any) => p.id) || [];
        const currentIds = assignedPermissions.modules[module.id] || [];

        if (currentIds.length !== originallyAssignedIds.length ||
            currentIds.some(id => !originallyAssignedIds.includes(id)) ||
            originallyAssignedIds.some(id => !currentIds.includes(id))) {
            hasChanges = true;
        }
    });

    isEditable.value = hasChanges;
}, { deep: true });
</script>

<style scoped>
.primaryColor {
    background-color: #f59e0b;
    color: white;
    transition: all 0.3s ease;
}

.primaryColor:hover:not(:disabled) {
    background-color: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.primaryColor:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.cancelButton {
    background-color: #e5e7eb;
    color: #4b5563;
    transition: all 0.3s ease;
}

.cancelButton:hover {
    background-color: #d1d5db;
    transform: translateY(-1px);
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

label:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}
</style>