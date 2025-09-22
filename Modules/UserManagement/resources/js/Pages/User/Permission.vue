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
                    class="absolute w-full bg-white shadow rounded mt-1 z-10 max-h-48 overflow-y-auto flex flex-col space-y-1 ">
                    <li v-for="module in modules" :key="module.id" @click="selectModule(module)"
                        class="cursor-pointer px-2 py-1 primary ">
                        {{ module.name }}
                    </li>
                </ul>
                <!-- Select Modules -->
                <div class="flex-1 mt-5">
                    <label class="block text-sm text-gray-600 mb-1 font-bold">Permission</label>
                    <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto">
                        <label v-for="permission in permissions" :key="permission.name"
                            class="flex items-center cursor-pointer bg-gray-50 hover:bg-amber-100 rounded-lg px-3 py-2 shadow-sm transition-colors duration-200">
                            <input type="checkbox" :value="permission.name" v-model="selectedPermissions"
                                class="accent-amber-500 w-4 h-4 rounded border-gray-300" />
                            <span class="ml-2 text-sm text-gray-800 font-medium">{{ permission.name }}</span>
                        </label>
                    </div>

                </div>
            </div>
            <!-- Save button -->
            <div class="flex justify-end mt-4 gap-2">
                <button type="button" @click="cancelEdit" class="cancelButton buttonSize px-4 py-2 rounded-lg "
                    v-show="isEditable">
                    Cancel
                </button>
                <button @click="assignModules"
                    class="primaryColor px-4 py-2 rounded-md text-sm shadow cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ !isEditable ? 'Assign Permissions' : 'Update Permissions' }}
                </button>
            </div>
        </div>
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2  font-medium text-gray-500 uppercase tracking-wider text-sm"
                        v-for="header in tableHeader" :key="header.name">
                        {{ header.name }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 ">
                <tr v-for="(row) in module_permission" :key="row.id" class="text-center">
                    <!-- Render columns except "actions" -->
                    <td class=" px-3 py-1 whitespace-nowrap text-slate-600">{{ row.module['name'] }}</td>
                    <td class=" px-3 py-1 whitespace-nowrap text-slate-600 ">{{
                        row.permissions }}</td>
                    <td class=" px-3 py-1 whitespace-nowrap text-slate-600">
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
                    <td class="px-3 py-2 text-center text-gray-400 text-sm">
                        No data found.
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Popup Modal -->
        <Popup v-model:showPopup="showPopup" :popupMessage="popupMessage" />
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import Popup from '@/Components/Popup.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, watch } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';


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
// const selectedModuleName = ref(modules.value[0]);
//variable
const modules = computed(() => props.modules);
const selectedModuleName = ref<any>(null);
const selectedModule = ref<any>(null);
const selectedPermissions = ref<string[]>([]);
const open = ref<boolean>(false);
const showPopup = ref(false);
const popupMessage = ref("");
const isEditable = ref<boolean>(false);
const oldModuleId = ref(null);
const tableHeader = [
    {
        name: "module",
    },
    {
        name: "permissions"
    },
    {
        name: "actions",
    }
]
const module_permission = computed(() => {
    return props.module_permission.map((mp) => {
        let permissions = mp.permissions;
        let p = permissions.map((p) => p.name).join(', ');
        return {
            module: mp,
            permissions: p,
        }
    });
})

const permissions = ref<any[]>([
    { name: 'Create' },
    { name: 'View' },
    { name: 'Edit' },
    { name: 'Delete' },
]);
// Form for saving permissions
const assignedPermissions = useForm({
    modules: [],
    permissions: [],
});
//functions
const cancelEdit = () => {
    assignedPermissions.modules = [];
    assignedPermissions.permissions = [];
    isEditable.value = false;
    oldModuleId.value = null;
    selectedPermissions.value = null;
    selectedModuleName.value = null;
};
const assignModules = () => {
    if (!selectedModuleName.value) {
        popupMessage.value = "Please select a module first.";
        showPopup.value = true;
        return;
    }
    if (selectedPermissions.value.length === 0) {
        popupMessage.value = "Please select a permssion first.";
        showPopup.value = true;
        return;
    }
    assignedPermissions.modules = selectedModule.value;
    assignedPermissions.permissions = selectedPermissions.value;
    if (!isEditable.value) {

        assignedPermissions.post(route('permission.module.store'), {
            onSuccess: () => {
                selectedPermissions.value = [];
                selectedModuleName.value = null;
            },
            onError: (errors) => {
                if (errors.role_id) {
                    popupMessage.value = errors.role_id;
                    showPopup.value = true;
                }
            }
        });
    } else {
        assignedPermissions.put(route('permission.module.update', { id: oldModuleId.value }), {
            onSuccess: () => {
                selectedPermissions.value = [];
                selectedModuleName.value = null;
                cancelEdit();
            },
            onError: (errors) => {
                if (errors.role_id) {
                    popupMessage.value = errors.role_id;
                    showPopup.value = true;
                }
            }
        });
    }
}
const selectModule = (moduleObj: Object) => {
    open.value = false;
    const module = modules.value.find(m => m.id === moduleObj.id);
    if (module) {
        selectedModuleName.value = module.name;
        selectedModule.value = module;
        assignedPermissions.modules.push(module);
    }
};

const editModulePermission = (data: any) => {
    const matchedModule = props.modules.find((module) => module.id === data.module.id);
    oldModuleId.value = data.module.id;

    if (!matchedModule) return;

    selectedModuleName.value = matchedModule.name;
    selectedModule.value = matchedModule;

    // Only add module if not already added
    if (!assignedPermissions.modules.some(m => m.id === matchedModule.id)) {
        assignedPermissions.modules.push(matchedModule);
    }

    // Convert permission string to array and select matching permissions
    const convertPermissionToArray = data.permissions.split(',').map(i => i.trim());
    selectedPermissions.value = permissions.value.filter(p =>
        convertPermissionToArray.includes(p.name)
    ).map((p) => p.name);

    isEditable.value = true;
};
const deletePermission = (moduleId: number) => {

    assignedPermissions.delete(route('permission.module.delete', { id: moduleId }));
}

</script>
