<template>

    <Head title="PIM" />
    <AuthenticatedLayout>
        <div class="p-6 space-y-6">
            <!-- Navigation Bar -->
            <nav class="flex flex-wrap items-center gap-4 bg-white p-4 rounded-xl shadow-md border border-gray-200">
                <button @click="navigateTo('pim.index')"
                    :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive('pim.index') ? 'primary' : 'secondaryColor']">
                    <i class="bx bx-user-plus text-xl"></i>
                    <span>Add Employee</span>
                </button>

                <button @click="navigateTo('PIM.EmployeeList')"
                    :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive('PIM.EmployeeList') ? 'primary' : 'secondaryColor']">
                    <i class="bx bx-list-ul text-xl"></i>
                    <span>Employee List</span>
                </button>

                <button @click="navigateTo('PIM.Reports')"
                    :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive('PIM.Reports') ? 'primary' : 'secondaryColor']">
                    <i class="bx bx-bar-chart-alt-2 text-xl"></i>
                    <span>Reports</span>
                </button>
            </nav>

            <!-- Main Content -->
            <div class="mt-6 bg-white rounded-xl shadow-md p-6 min-h-[300px]">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-gray-700">Add Employee</h2>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitForm" class="bg-white rounded-xl p-6 space-y-4">
                    <!-- Employee Photo -->
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div
                            class="w-32 h-32 bg-gray-100 rounded-full overflow-hidden flex items-center justify-center border">
                            <img v-if="form.imgPreview" :src="form.imgPreview" class="w-full h-full object-cover"
                                alt="Employee Logo" />
                            <span v-else class="text-gray-400 text-sm">No Image</span>
                        </div>
                        <div>
                            <input type="file" ref="fileInput" accept="image/*" @change="previewLogo" class="hidden" />
                            <button class="mt-2 text-base primaryColor px-6 py-2 rounded-lg font-semibold"
                                @click.prevent="$refs.fileInput.click()">
                                Upload Image
                            </button>
                        </div>
                    </div>

                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">First Name <span
                                    class="text-red-500">*</span></label>
                            <Input v-model="form.first_name" type="text" placeholder="Enter first name" />
                            <span v-if="form.errors.first_name" class="text-red-500 text-sm">{{ form.errors.first_name
                            }}</span>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Middle Name</label>
                            <Input v-model="form.middle_name" type="text" placeholder="Enter middle name" />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Last Name <span
                                    class="text-red-500">*</span></label>
                            <Input v-model="form.last_name" type="text" placeholder="Enter last name" />
                            <span v-if="form.errors.last_name" class="text-red-500 text-sm">{{ form.errors.last_name
                            }}</span>
                        </div>
                    </div>

                    <!-- Employee ID & Email -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Employee ID <span
                                    class="text-red-500">*</span></label>
                            <Input v-model="form.employee_id" type="text" placeholder="Enter employee ID" />
                            <span v-if="form.errors.employee_id" class="text-red-500 text-sm">{{ form.errors.employee_id
                            }}</span>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Employee Email <span
                                    class="text-red-500">*</span></label>
                            <Input v-model="form.email" type="email" placeholder="Enter employee email" />
                            <span v-if="form.errors.email" class="text-red-500 text-sm">{{ form.errors.email }}</span>
                        </div>
                    </div>

                    <!-- OPTION 1: Create Login Credentials -->
                    <div class="border-t pt-4 mt-2">
                        <div class="flex items-center gap-3">
                            <label class="text-gray-700 font-medium">Create Login Credentials</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="form.showCredentials" class="sr-only peer" />
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-amber-400
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:rounded-full after:h-5 after:w-5
                                    after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>

                        <transition name="fade">
                            <div v-if="form.showCredentials" class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Password <span
                                            class="text-red-500">*</span></label>
                                    <TextInput v-model="form.password" type="password" placeholder="Enter password"
                                        class="w-full" />
                                    <span v-if="form.errors.password" class="text-red-500 text-sm">{{
                                        form.errors.password }}</span>
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Confirm Password <span
                                            class="text-red-500">*</span></label>
                                    <TextInput v-model="form.password_confirmation" type="password" class="w-full"
                                        placeholder="Confirm password" />
                                </div>
                            </div>
                        </transition>
                    </div>

                    <!-- OPTION 2: Link to Existing User -->
                    <div class="border-t pt-4">
                        <div class="flex items-center gap-3">
                            <label class="text-gray-700 font-medium">Link to Existing User</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="form.linkUser" class="sr-only peer" />
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-amber-400
                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                after:bg-white after:rounded-full after:h-5 after:w-5
                after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>

                        <!-- Link Existing User Section -->
                        <div v-if="form.linkUser" class="mt-4">
                            <div class="relative">
                                <input type="text" v-model="userSearch" @input="searchUsers"
                                    placeholder="Search for a user by name or email..."
                                    class="w-full px-4 py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400" />
                                <i
                                    class="bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>

                            <!-- User List Dropdown -->
                            <div v-if="userSearch.length > 0 && filteredUsers.length > 0"
                                class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                <div v-for="user in filteredUsers" :key="user.id" @click="selectUser(user)"
                                    class="px-4 py-2 hover:bg-amber-50 cursor-pointer transition-colors flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-800">{{ user.name }}</div>
                                        <div class="text-xs text-gray-500">{{ user.email }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- No results message -->
                            <div v-if="userSearch.length >= 2 && filteredUsers.length === 0 && !searching"
                                class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-yellow-700">
                                No available users found. Try a different search term.
                            </div>

                            <!-- Selected User -->
                            <div v-if="form.link_user_id"
                                class="mt-2 p-2 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
                                <i class="bx bx-check-circle text-green-500 text-xl"></i>
                                <span class="text-sm text-gray-700">
                                    <span class="font-medium">{{ selectedUser?.name }}</span> ({{ selectedUser?.email
                                    }}) will be linked to this employee.
                                </span>
                                <button @click="clearSelectedUser" class="ml-auto text-red-500 hover:text-red-700">
                                    <i class="bx bx-x text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-6 border-t pt-4">
                        <button type="submit" class="primary px-6 py-2 rounded-lg" :disabled="form.processing">
                            <i v-if="form.processing" class="bx bx-loader-alt animate-spin mr-2"></i>
                            {{ form.processing ? 'Saving...' : 'Save Employee' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import { ref, reactive, computed, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Input from "@/Components/Input.vue";
import TextInput from "@/Components/TextInput.vue";
import { Notyf } from "notyf";
import "notyf/notyf.min.css";

const notyf = new Notyf({
    duration: 3000,
    position: {
        x: 'right',
        y: 'top',
    },
    dismissible: true,
});

// Route active check
const page = usePage();
const currentRoute = computed(() => page.url);

const isActive = (routeName: string) => {
    return route().current(routeName);
};

// ============================================
// USER SEARCH FOR LINKING - ADD THIS SECTION
// ============================================
const userSearch = ref('');
const filteredUsers = ref([]);
const selectedUser = ref<any>(null);


const searching = ref(false);

const searchUsers = async () => {
    if (userSearch.value.length < 2) {
        filteredUsers.value = [];
        return;
    }

    searching.value = true;

    try {
        const response = await fetch(`/admin/users/search?q=${encodeURIComponent(userSearch.value)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Search results:', data);
        filteredUsers.value = data;
    } catch (error) {
        console.error('Error searching users:', error);

        filteredUsers.value = [];
    } finally {
        searching.value = false;
    }
};

const selectUser = (user: any) => {
    selectedUser.value = user;
    form.link_user_id = user.id;
    form.email = user.email;
    userSearch.value = '';
    filteredUsers.value = [];
};

const clearSelectedUser = () => {
    selectedUser.value = null;
    form.link_user_id = null;
};
// ============================================
// END OF USER SEARCH SECTION
// ============================================

// Form
const form = useForm({
    img: null,
    imgPreview: null,
    first_name: "",
    middle_name: "",
    last_name: "",
    employee_id: "",
    email: "",
    password: "",
    password_confirmation: "",
    showCredentials: false,
    linkUser: false,
    link_user_id: null,  // ✅ This stores the selected user ID
});

// Watch to prevent both options being selected
watch(() => form.linkUser, (val) => {
    if (val) {
        form.showCredentials = false;
    }
});

watch(() => form.showCredentials, (val) => {
    if (val) {
        form.linkUser = false;
        form.link_user_id = null;
        selectedUser.value = null;
    }
});

function previewLogo(e: Event) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.img = file;
        form.imgPreview = URL.createObjectURL(file);
    }
}

function submitForm() {
    form.post(route("PIM.storeEmployee"), {
        preserveScroll: true,
        onSuccess: () => {
            notyf.success("Employee added successfully!");
            form.reset();
            form.showCredentials = false;
            form.linkUser = false;
            form.link_user_id = null;
            selectedUser.value = null;
        },
        onError: (errors) => {
            if (errors) {
                notyf.error("Please check the form for errors.");
            }
        }
    });
}

function navigateTo(routeName: string) {
    router.visit(route(routeName));
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

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

.secondaryColor {
    background-color: #f3f4f6;
    color: #4b5563;
}

.secondaryColor:hover {
    background-color: #e5e7eb;
    transform: translateY(-1px);
}

.primaryColor {
    background-color: #f59e0b;
    color: white;
    transition: all 0.3s ease;
}

.primaryColor:hover {
    background-color: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}
</style>