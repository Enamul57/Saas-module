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
                    <li v-for="module in modules" :key="module.id" @click="selectModule(module.id)"
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
            <div class="flex justify-end mt-4">
                <button @click="assignModules"
                    class="primaryColor px-4 py-2 rounded-md text-sm shadow cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    Assign Permissions
                </button>
            </div>
        </div>
        <!-- Popup Modal -->
        <Popup v-model:showPopup="showPopup" :popupMessage="popupMessage" />
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import Popup from '@/Components/Popup.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, watch } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';


// Props
const props = defineProps({
    modules: {
        type: Object,
        required: true,
    }
});
// const selectedModule = ref(modules.value[0]);
//variable
const modules = computed(() => props.modules);
const selectedModuleName = ref<any>(null);
const selectedPermissions = ref<string[]>([]);
const open = ref<boolean>(false);
const showPopup = ref(false);
const popupMessage = ref("");

const permissions = ref<any[]>([
    { name: 'create' },
    { name: 'view' },
    { name: 'edit' },
    { name: 'delete' },
]);
// Form for saving permissions
const assignedPermissions = useForm({
    modules: [],
    permissions: [],
});
//functions
const assignModules = () => {
    if (!selectedModuleName.value) {
        popupMessage.value = "Please select a module first.";
        showPopup.value = true;
        return;
    }
    assignedPermissions.modules = selectedModuleName.value;
    assignedPermissions.permissions = selectedPermissions.value;

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
}
const selectModule = (moduleId: number) => {
    const module = modules.value.find(m => m.id === moduleId);
    if (module) {
        selectedModuleName.value = module.name;
        assignedPermissions.modules.push(module.id);
        open.value = false;
    }
};

</script>
