<template>

    <Head title="User Management" />
    <AuthenticatedLayout>
        <div class="p-6 space-y-6">
            <h2 class="text-2xl font-semibold text-gray-700">User Management</h2>

            <!-- Create user form -->
            <div class="bg-white shadow rounded p-6">
                <h3 class="text-lg font-medium mb-4">Add New User</h3>
                <form @submit.prevent="createUser" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-600">Name</label>
                        <input v-model="form.name" type="text"
                            class="mt-1  w-3/4 rounded shadow-sm focus:ring-orange-500 focus:border-orange-500 px-2 py-2 borderInput" />
                        <span v-if="form.errors.name" class="text-red-500 text-sm">{{ form.errors.name }}</span>
                    </div>
                    <div>
                        <label class="block text-gray-600">Email</label>
                        <input v-model="form.email" type="email"
                            class="mt-1 w-3/4  rounded shadow-sm focus:ring-orange-500 focus:border-orange-500 px-2 py-2 borderInput" />
                        <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
                    </div>
                    <div>
                        <label class="block text-gray-600">Password</label>
                        <input v-model="form.password" type="password"
                            class="mt-1 w-3/4  rounded shadow-sm focus:ring-orange-500 focus:border-orange-500 px-2 py-2 borderInput" />
                        <span v-if="form.errors.password" class="text-red-500 text-sm">{{ form.errors.password }}</span>
                    </div>
                    <div class="md:col-span-3 mt-4 gap-4 flex">
                        <button type="button" @click="cancelEdit" class="cancelButton px-6 py-2 rounded-lg"
                            v-show="isEditable">Cancel</button>
                        <button type="submit" class="primaryColor px-4 py-2 rounded-lg">
                            {{ isEditable ? 'Update User' : 'Create User' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users table -->
            <div class="bg-white shadow rounded p-6">
                <h3 class="text-lg font-medium mb-4">Users List</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="user in users.data" :key="user.id">
                            <td class="px-6 py-2 whitespace-nowrap !text-slate-600">{{ user.name }}</td>
                            <td class="px-6 py-2 whitespace-nowrap !text-slate-600">{{ user.email }}</td>
                            <td class="px-6 py-2 whitespace-nowrap !text-slate-600 space-x-2">
                                <button @click="editUser(user)" class="text-blue-500 hover:underline">Edit</button>
                                <button @click="deleteUser(user.id)"
                                    class="text-red-500 hover:underline">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="3" class="px-6 py-2 text-center text-gray-400">No users found.</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <Pagination :pagination="users" :current-page="currentPage" :per-page="perPage" @page-change="fetchPage"
                    @per-page-change="onPerPageChange" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from "@/Components/Pagination.vue";
import { ref, computed, watch } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';

const props = defineProps({
    users: { type: Object, required: true }
});

const users = computed(() => props.users);
const currentPage = ref(props.users.current_page || 1);
const perPage = ref(parseInt(new URL(window.location.href).searchParams.get('per_page') || '10'));
const isEditable = ref(false);
const editId = ref<number | null>(null);

const form = useForm({ name: '', email: '', password: '' });

// ------------------- CRUD -------------------
const refreshUsers = () => {
    router.get(route('users.index'), { page: currentPage.value, per_page: perPage.value }, { only: ['users'], preserveState: true, preserveScroll: true });
};
const createUser = () => {
    if (!isEditable.value) {
        form.post(route('users.store'), { onSuccess: refreshUsers });
    } else {
        form.put(route('users.update', { id: editId.value }), {
            preserveState: true,
            onSuccess: () => {
                form.reset();
                cancelEdit();
                refreshUsers();
            }
        });
    }
};

const editUser = (user: any) => {
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    isEditable.value = true;
    editId.value = user.id;
};

const cancelEdit = () => {
    form.reset();
    isEditable.value = false;
    editId.value = null;
};

const deleteUser = (id: number) => {
    form.delete(route('users.destroy', id), {
        onSuccess: () => refreshUsers()
    });
};

// ------------------- Pagination -------------------
const fetchPage = (page: number) => {
    currentPage.value = page;
    router.get(route('users.index'), { page, per_page: perPage.value }, { only: ['users'], preserveScroll: true });
};

const onPerPageChange = (newPerPage: number) => {
    perPage.value = newPerPage;
    currentPage.value = 1;
    router.get(route('users.index'), { page: 1, per_page: perPage.value }, { only: ['users'], preserveScroll: true });
};

// Keep currentPage reactive on prop change
watch(() => props.users.current_page, val => currentPage.value = val);
</script>
