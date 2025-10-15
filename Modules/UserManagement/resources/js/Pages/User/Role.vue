<template>

    <Head title="User Management" />
    <AuthenticatedLayout>
        <div class="p-4 space-y-2">
            <!-- Create roles form -->
            <div class="bg-white shadow rounded p-4">
                <h3 class="text-sm font-medium mb-4">{{ isEditable ? 'Update' : 'Create' }} Roles</h3>
                <form @submit.prevent="createRole" class="flex gap-4">
                    <div class="w-full">
                        <label class="block text-sm text-gray-600">Name</label>
                        <input v-model="role.name" type="text"
                            class="mt-1  w-full rounded shadow-sm focus:ring-orange-500 focus:border-orange-500 px-2 py-2 borderInput" />
                        <span v-if="role.errors.name" class="text-red-500 text-sm">{{ role.errors.name }}</span>
                    </div>

                    <div class="flex items-end justify-end gap-2" style="align-items:end">
                        <button type="button" @click="cancelEdit" class="cancelButton buttonSize px-4 py-2 rounded-lg"
                            v-show="isEditable">
                            Cancel
                        </button>
                        <button type="submit" class="primaryColor buttonSize px-4 py-2 rounded-md text-sm shadow">
                            {{ isEditable ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
            <!-- Assign Modules -->
            <div class="bg-white shadow rounded p-4 mt-4">
                <h3 class="text-sm font-medium mb-4">Assign Modules to Role</h3>

                <div class="flex flex-col gap-4">
                    <!-- Select Role -->
                    <div class="relative w-full">
                        <button @click="open = !open" class="w-full px-2 py-2 borderInput rounded shadow-sm text-left">
                            {{ selectedRoleName || "Select a role" }}
                        </button>
                        <ul v-show="open"
                            class="absolute w-full bg-white shadow rounded mt-1 z-10 max-h-48 overflow-y-auto">
                            <li v-for="role in roles" :key="role.id" @click="selectRole(role)"
                                class="cursor-pointer px-2 py-1 primary">
                                {{ role.name }}
                            </li>
                        </ul>
                    </div>

                    <!-- Select Modules -->
                    <div class="flex-1" v-if="modules.length > 0">
                        <label class="block text-sm text-gray-600 mb-1 font-bold">Modules</label>
                        <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto">
                            <label v-for="module in modules" :key="module.id"
                                class="flex items-center cursor-pointer bg-gray-50 hover:bg-amber-100 rounded-lg px-3 py-2 shadow-sm transition-colors duration-200">
                                <input type="checkbox" :value="module.id" v-model="selectedModules"
                                    class="accent-amber-500 w-4 h-4 rounded border-gray-300" />
                                <span class="ml-2 text-sm text-gray-800 font-medium">{{ module.name }}</span>
                            </label>
                        </div>

                    </div>
                </div>

                <!-- Save button -->
                <div class="flex justify-end mt-4">
                    <button @click="assignModules"
                        class="primaryColor px-4 py-2 rounded-md text-sm shadow cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        Save Assignment
                    </button>
                </div>
            </div>
            <!-- Assign Users -->
            <div class="bg-white shadow rounded p-4 mt-4">
                <h3 class="text-sm font-medium mb-4">Assign Users to Role</h3>

                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Select Role -->
                    <div class="relative w-full">
                        <button @click="openUserRole = !openUserRole"
                            class="w-full px-2 py-2 borderInput rounded shadow-sm text-left">
                            {{ selectedRoleNameUser || "Select a role" }}
                        </button>
                        <ul v-show="openUserRole"
                            class="absolute w-full bg-white shadow rounded mt-1 z-10 max-h-48 overflow-y-auto">
                            <li v-for="role in roles" :key="role.id" @click="selectRoleUser(role)"
                                class="cursor-pointer px-2 py-1 primary">
                                {{ role.name }}
                            </li>
                        </ul>
                    </div>

                    <!-- Select users -->
                    <div class="relative w-full">
                        <button @click="openUser = !openUser"
                            class="w-full px-2 py-2 borderInput rounded shadow-sm text-left">
                            {{ selectedUserName || "Select a user" }}
                        </button>
                        <ul v-show="openUser"
                            class="absolute w-full bg-white shadow rounded mt-1 z-10 max-h-48 overflow-y-auto">
                            <li v-for="user in users" :key="user.id" @click="selectUser(user)"
                                class="cursor-pointer px-2 py-1 primary">
                                {{ user.name }}
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Save button -->
                <div class="flex justify-end mt-4">
                    <button @click="assignRoleUser"
                        class="primaryColor px-4 py-2 rounded-md text-sm shadow cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        Assign User
                    </button>
                </div>
            </div>
            <!--table of roles-->
            <!-- roles table -->
            <div class="bg-white shadow rounded p-4">
                <h3 class="text-base font-medium mb-3">Roles List</h3>
                <TableComponent :tableHeader="tableHeader" :tableData="roles">
                    <template #tableAction="{ row }">
                        <button @click="editRole(row)" class="text-blue-500 hover:underline text-sm font-semibold">
                            Edit
                        </button>
                        <button @click="deleteRole(row.id)" class="text-red-500 hover:underline text-sm font-semibold">
                            Delete
                        </button>
                        <Link :href="route('permission.role.index', { id: row.id })"
                            class="text-green-500 hover:underline text-sm font-semibold">
                        Set Permissions
                        </Link>
                    </template>
                </TableComponent>

                <!-- Popup Modal -->
                <Popup v-model:showPopup="showPopup" :popupMessage="popupMessage" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from "@/Components/Pagination.vue";
import { ref, computed, watch } from 'vue';
import { useForm, Head, router, Link } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import TableComponent from '@/Components/TableComponent.vue';
import Popup from '@/Components/Popup.vue';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';
//props 
const props = defineProps({
    roles: { type: Object, required: true },
    modules: { type: Object, required: true, default: () => [] },
    users: { type: Object, required: true, default: () => [] },
});

//variable
const notify = new Notyf();
const roles = computed(() => props.roles);
const isEditable = ref(false);
const editId = ref<number | null>(null);
const selectedRole = ref(null)
const selectedRoleName = ref('')
const open = ref(false)
const selectedUser = ref(null)
const selectedUserName = ref('')
const openUser = ref(false)
const selectedRoleUser = ref(null)
const selectedRoleNameUser = ref('')
const openUserRole = ref(false)
const selectedModules = ref([]);
const showPopup = ref(false);
const popupMessage = ref("");
const modules = computed(() => props.modules);
const users = computed(() => props.users);
const tableHeader = [
    { name: 'name' },
    { name: 'actions' }
]
// Form state
const role = useForm({
    name: '',
});
const assignModulesToRole = useForm({
    modules: [],
    role_id: null,
});
const assignRoleToUser = useForm({
    user_id: null,
    role_id: null,
})
//methods
const createRole = () => {

    if (!isEditable.value) {
        role.post(route('roles.store'), {
            onSuccess: () => {
                role.reset('name');
            }
        });
        notify.success('Role Created successfully!');
    } else {
        role.put(route('roles.update', { id: editId.value }), {
            preserveState: true,
            onSuccess: () => {
                cancelEdit();
            }
        });
        notify.success('Role Updated successfully!');

    }
};
const cancelEdit = () => {
    role.reset();
    isEditable.value = false;
    editId.value = null;
};
const editRole = (roleData: any) => {
    role.name = roleData.name;
    isEditable.value = true;
    editId.value = roleData.id;
}
const deleteRole = (roleId: number) => {
    role.delete(route('roles.destroy', { role: roleId }));
    notify.success('Role Deleted successfully!');

}

function selectRole(role) {
    selectedRole.value = role.id
    selectedRoleName.value = role.name
    open.value = !open.value;
}
const selectUser = (user) => {
    selectedUser.value = user.id
    selectedUserName.value = user.name
    openUser.value = !openUser.value;
}
function selectRoleUser(user) {
    selectedRoleUser.value = user.id
    selectedRoleNameUser.value = user.name
    openUserRole.value = !openUserRole.value;
}
const assignModules = () => {
    if (!selectedRole.value) {
        popupMessage.value = "Please select a role first.";
        showPopup.value = true;
        return;
    }
    if (selectedModules.value.length === 0) {
        popupMessage.value = "Please select at least one module.";
        showPopup.value = true;
        return;
    }
    //assigned to form and submit
    assignModulesToRole.modules = selectedModules.value;
    assignModulesToRole.role_id = selectedRole.value;
    assignModulesToRole.post(route('roles.modules.assign', { role: selectedRole.value }), {
        onSuccess: () => {
            selectedModules.value = [];
            selectedRole.value = null;
            selectedRoleName.value = '';
        },
        onError: (errors) => {
            if (errors.role_id) {
                popupMessage.value = errors.role_id;
                showPopup.value = true;
            }
        }
    });
    notify.success('Role has assigned to module successfully!');

}

const assignRoleUser = () => {
    if (!selectedRoleUser.value) {
        popupMessage.value = "Please select a role first.";
        showPopup.value = true;
        return;
    }
    if (!selectedUser.value) {
        popupMessage.value = "Please select a user first.";
        showPopup.value = true;
        return;
    }
    assignRoleToUser.user_id = selectedUser.value;
    assignRoleToUser.role_id = selectedRoleUser.value;

    assignRoleToUser.post(route('roles.users.assign'), {
        onSuccess: () => {
            selectedUser.value = null;
            selectedUserName.value = '';
            selectedRoleUser.value = null;
            selectedRoleNameUser.value = '';
        },
        onError: (errors) => {
            if (errors.user_id) {
                popupMessage.value = errors.user_id;
                showPopup.value = true;
            }
        }
    });
    notify.success('Role has assigned to user successfully!');

}
</script>
