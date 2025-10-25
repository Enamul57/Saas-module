<template>

    <Head title="Permission Management" />
    <AuthenticatedLayout>
        <div class="p-6 space-y-6">
            <h2 class="text-2xl font-semibold text-gray-700">Role Permission Management - {{ role_name }}</h2>

            <!-- Modules & Permissions -->
            <div class="space-y-6">
                <div v-for="module in roles[0]?.features" :key="module.id" class="p-4 border rounded shadow">
                    <h3 class="font-bold text-gray-700 mb-2">{{ module.name }}</h3>

                    <div v-if="module.permissions && module.permissions.length > 0" class="flex flex-wrap gap-2">
                        <label v-for="permission in module.permissions" :key="permission.id"
                            class="flex items-center cursor-pointer bg-gray-50 hover:bg-amber-100 rounded-lg px-3 py-2 shadow-sm">
                            <input type="checkbox" :value="permission.id"
                                v-model="assignedPermissions.modules[module.id]"
                                class="accent-amber-500 w-4 h-4 rounded border-gray-300" />
                            <span class="ml-2 text-sm text-gray-800 font-medium">{{ permission.name }}</span>
                        </label>
                    </div>

                    <div v-else class="text-gray-400 italic text-sm">
                        No permissions available for this module.
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
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
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
// Props
const props = defineProps({
    roles: { type: Object, required: true },
    role_name: { type: String, required: true },
    role_id: { type: Number, required: true }
});
console.log(props.roles[0]?.features);
const featuresPermission = ref([]);
// Track selected permissions per module
const assignedPermissions = useForm({
    modules: {} as Record<number, number[]>
})

// Initialize module arrays
onMounted(() => {
    props.roles[0]?.features.forEach((module) => {
        if (!assignedPermissions.modules[module.id]) {
            assignedPermissions.modules[module.id] = [];
        }
    })
});
const isEditable = ref(false);

// Reset form
const cancelEdit = () => {
    assignedPermissions.modules = {};
    isEditable.value = false;
};

// Save assigned permissions
const assignModules = () => {
    assignedPermissions.post(route('role.permission.store', { role: props.role_id }), {
        onSuccess: () => {
            cancelEdit();
            notyf.success("Permissions assigned successfully!");
        }
    });
};
</script>
