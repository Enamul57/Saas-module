<template>

    <Head title="User Management" />
    <AuthenticatedLayout>
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-semibold text-gray-700">User Management</h2>
                <span class="text-sm text-gray-500">Total: {{ users.total || 0 }} users</span>
            </div>

            <!-- Create user form -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">{{ isEditable ? 'Update User' : 'Add New User' }}</h3>
                <form @submit.prevent="createUser" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-600 font-medium mb-1">Name <span
                                class="text-red-500">*</span></label>
                        <input v-model="form.name" type="text"
                            class="mt-1 w-full rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 px-3 py-2 border border-gray-300 focus:outline-none" />
                        <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-600 font-medium mb-1">Email <span
                                class="text-red-500">*</span></label>
                        <input v-model="form.email" type="email"
                            class="mt-1 w-full rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 px-3 py-2 border border-gray-300 focus:outline-none" />
                        <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-600 font-medium mb-1">{{ isEditable ? 'New Password (optional)' :
                            'Password' }}</label>
                        <input v-model="form.password" type="password"
                            class="mt-1 w-full rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 px-3 py-2 border border-gray-300 focus:outline-none" />
                        <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
                    </div>
                    <div class="md:col-span-3 flex gap-4 mt-2">
                        <button type="button" @click="cancelEdit"
                            class="cancelButton px-6 py-2 rounded-lg transition-colors"
                            v-show="isEditable">Cancel</button>
                        <button type="submit" class="primary px-6 py-2 rounded-lg transition-colors"
                            :disabled="form.processing">
                            <i v-if="form.processing" class="bx bx-loader-alt animate-spin mr-2"></i>
                            {{ isEditable ? 'Update User' : 'Create User' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users table -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Users List</h3>

                <!-- Search -->
                <div class="relative w-full sm:w-64 mb-4">
                    <input type="text" v-model="search" @input="handleSearch" placeholder="Search users..."
                        class="w-full px-4 py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400" />
                    <i class="bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Name</th>
                                <th
                                    class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th
                                    class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Roles</th>
                                <th
                                    class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-800">{{ user.name
                                }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-600">{{ user.email }}</td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        <!-- Show roles as badges -->
                                        <span v-for="role in user.roles" :key="role" :class="[
                                            'px-2 py-1 text-xs font-medium rounded-full',
                                            getRoleColor(role)
                                        ]">
                                            {{ role }}
                                        </span>
                                        <span v-if="!user.roles || user.roles.length === 0"
                                            class="text-gray-400 text-xs">
                                            No roles
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm">
                                    <div class="flex gap-2">
                                        <button @click="editUser(user)"
                                            class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-600 hover:text-blue-700 transition-colors"
                                            title="Edit User">
                                            <i class="bx bx-edit text-lg"></i>
                                        </button>
                                        <button @click="deleteUser(user.id)"
                                            class="p-1.5 rounded-lg hover:bg-red-50 text-red-600 hover:text-red-700 transition-colors"
                                            title="Delete User">
                                            <i class="bx bx-trash text-lg"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.data && users.data.length === 0">
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                    <i class="bx bx-user-x text-3xl block mb-2"></i>
                                    No users found
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

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

const props = defineProps({
    users: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) }
});

const users = computed(() => props.users);
const currentPage = ref(props.users.current_page || 1);
const perPage = ref(parseInt(new URL(window.location.href).searchParams.get('per_page') || '10'));
const isEditable = ref(false);
const editId = ref<number | null>(null);
const search = ref(props.filters?.search || '');

// ✅ Role color mapping
const getRoleColor = (role: string): string => {
    const colors: Record<string, string> = {
        'super_admin': 'bg-purple-100 text-purple-700',
        'admin': 'bg-red-100 text-red-700',
        'hr_manager': 'bg-blue-100 text-blue-700',
        'department_manager': 'bg-green-100 text-green-700',
        'finance_manager': 'bg-amber-100 text-amber-700',
        'manager': 'bg-indigo-100 text-indigo-700',
        'employee': 'bg-gray-100 text-gray-700',
    };
    return colors[role] || 'bg-gray-100 text-gray-700';
};

const form = useForm({
    name: '',
    email: '',
    password: ''
});

// Search handler
const handleSearch = () => {
    router.get(
        route('users.index'),
        { search: search.value, page: currentPage.value, per_page: perPage.value },
        { only: ['users'], preserveState: true, preserveScroll: true }
    );
};

// CRUD Operations
const refreshUsers = () => {
    form.reset();
    router.get(route('users.index'), {
        page: currentPage.value,
        per_page: perPage.value,
        search: search.value
    }, {
        only: ['users'],
        preserveState: true,
        preserveScroll: true
    });
};

const createUser = () => {
    if (!isEditable.value) {
        form.post(route('users.store'), {
            onSuccess: () => {
                refreshUsers();
                notyf.success("User created successfully!");
            }
        });
    } else {
        form.put(route('users.update', { id: editId.value }), {
            preserveState: true,
            onSuccess: () => {
                cancelEdit();
                refreshUsers();
                notyf.success("User updated successfully!");
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
    if (confirm('Are you sure you want to delete this user?')) {
        form.delete(route('users.destroy', id), {
            onSuccess: () => {
                refreshUsers();
                notyf.success("User deleted successfully!");
            }
        });
    }
};

const assignRole = (user: any) => {
    router.visit(route('users.assign-role', user.id));
};

// Pagination
const fetchPage = (page: number) => {
    currentPage.value = page;
    router.get(route('users.index'), {
        page,
        per_page: perPage.value,
        search: search.value
    }, {
        only: ['users'],
        preserveScroll: true
    });
};

const onPerPageChange = (newPerPage: number) => {
    perPage.value = newPerPage;
    currentPage.value = 1;
    router.get(route('users.index'), {
        page: 1,
        per_page: perPage.value,
        search: search.value
    }, {
        only: ['users'],
        preserveScroll: true
    });
};

// Watch for page changes
watch(() => props.users.current_page, val => currentPage.value = val);
</script>

<style scoped>
.primary {
    background-color: #f59e0b;
    color: white;
    transition: all 0.3s ease;
}

.primary:hover {
    background-color: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
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

/* Scrollbar styling */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #f59e0b;
    border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #d97706;
}

/* Table row hover */
tr {
    transition: background-color 0.2s ease;
}
</style>