<template>

    <Head title="Permission Management" />
    <AuthenticatedLayout>
        <div class="p-6 space-y-6">
            <h2 class="text-2xl font-semibold text-gray-700">Permission Management</h2>

            <!-- Select Module -->
            <div class="bg-white shadow rounded p-6 flex items-center gap-4">
                <label class="block text-gray-600 font-medium">Select Module</label>
                <select v-model="selectedModule" @change="fetchPermissions"
                    class="rounded shadow-sm border border-orange-500 px-2 py-2 focus:ring-orange-500 focus:border-orange-500">
                    <option v-for="module in modules" :key="module" :value="module">{{ module }}</option>
                </select>
            </div>

            <!-- Permissions Table -->
            <div v-if="permissions.length" class="bg-white shadow rounded p-6">
                <h3 class="text-lg font-medium mb-4">Assign Permissions</h3>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Permission</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Assign</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="permission in permissions" :key="permission.id">
                            <td class="px-6 py-2 whitespace-nowrap !text-slate-600">{{ permission.name }}</td>
                            <td class="px-6 py-2 whitespace-nowrap !text-slate-600">
                                <input type="checkbox" v-model="assignedPermissions" :value="permission.id"
                                    class="h-5 w-5 text-orange-500">
                            </td>
                        </tr>
                        <tr v-if="permissions.length === 0">
                            <td colspan="2" class="px-6 py-2 text-center text-gray-400">No permissions found for this
                                module.</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 flex gap-4">
                    <button @click="savePermissions" class="primaryColor px-4 py-2 rounded-lg">Save Permissions</button>
                    <button @click="resetPermissions" class="cancelButton px-4 py-2 rounded-lg">Cancel</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, watch } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';

const modules = ref(['UserManagement', 'Profile', 'Posts', 'Settings']); // Example modules
const selectedModule = ref(modules.value[0]);

const permissions = ref<any[]>([]);
const assignedPermissions = ref<number[]>([]);

// Form for saving permissions
const form = useForm({
    module: '',
    permissions: [] as number[],
});

// ------------------- CRUD / API -------------------
const fetchPermissions = () => {
    // form.get(route('permissions.index'), { module: selectedModule.value }, {
    //     only: ['permissions', 'assigned'],
    //     onSuccess: page => {
    //         permissions.value = page.props.permissions || [];
    //         assignedPermissions.value = page.props.assigned || [];
    //     }
    // });
};

const savePermissions = () => {
    // form.post(route('permissions.store'), { module: selectedModule.value, permissions: assignedPermissions.value }, {
    //     onSuccess: () => {
    //         alert('Permissions assigned successfully!');
    //     }
    // });
};

const resetPermissions = () => {
    assignedPermissions.value = [];
    fetchPermissions();
};

// Fetch permissions on module change
watch(selectedModule, () => fetchPermissions());

// Initial fetch
fetchPermissions();
</script>
