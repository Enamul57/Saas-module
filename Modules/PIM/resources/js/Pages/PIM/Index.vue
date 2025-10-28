<template>

    <Head title="PIM" />
    <AuthenticatedLayout>
        <div class="p-6 space-y-6">
            <!-- Navigation Bar -->
            <nav class="flex flex-wrap items-center gap-4 bg-white p-4 rounded-xl shadow-md border border-gray-200">
                <!-- Add Employee -->
                <button @click="navigateTo('pim.index')"
                    :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive('pim.index') ? 'primary' : 'secondaryColor']">
                    <i class="bx bx-user-plus text-xl"></i>
                    <span>Add Employee</span>
                </button>

                <!-- Employee List -->
                <button @click="navigateTo('PIM.EmployeeList')"
                    :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive('PIM.EmployeeList') ? 'primary' : 'secondaryColor']">
                    <i class="bx bx-list-ul text-xl"></i>
                    <span>Employee List</span>
                </button>

                <!-- Reports -->
                <button @click="navigateTo('PIM.Reports')"
                    :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive('PIM.Reports') ? 'primary' : 'secondaryColor']">
                    <i class="bx bx-bar-chart-alt-2 text-xl"></i>
                    <span>Reports</span>
                </button>
            </nav>

            <!-- Main Content -->
            <div class="mt-6 bg-white rounded-xl shadow-md p-6 min-h-[300px]">
                <!-- Add Employee -->
                <!-- Header -->
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
                            <button class="mt-2 text-base primaryColor px-6 py-2 rounded-lg font-semibold "
                                @click.prevent="$refs.fileInput.click()">Upload Image</button>
                        </div>
                    </div>

                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">First Name</label>
                            <Input v-model="form.first_name" type="text" :placeholder="'Enter first name'" />
                            <span v-if="form.errors.first_name" class="text-red-500 text-sm">{{
                                form.errors.first_name }}</span>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Middle Name</label>
                            <Input v-model="form.middle_name" type="text" :placeholder="'Enter middle name'" />
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Last Name</label>
                            <Input v-model="form.last_name" type="text" :placeholder="'Enter last name'" />
                            <span v-if="form.errors.last_name" class="text-red-500 text-sm">{{
                                form.errors.last_name }}</span>
                        </div>
                    </div>

                    <!-- Employee ID -->
                    <div class="grid grid-cols-2">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Employee ID</label>
                            <Input v-model="form.employee_id" type="text" :placeholder="'Enter employee ID'" />
                            <span v-if="form.errors.employee_id" class="text-red-500 text-sm">{{
                                form.errors.employee_id }}</span>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Employee Email</label>
                            <Input v-model="form.email" type="email" :placeholder="'Enter employee email'" />
                            <span v-if="form.errors.email" class="text-red-500 text-sm">{{
                                form.errors.email }}</span>
                        </div>
                    </div>

                    <!-- Switch for Login Credentials -->
                    <div class="flex items-center gap-3 pt-[100px]">
                        <label class="text-gray-700 font-medium">Create Login Credentials</label>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" v-model="form.showCredentials" class="sr-only peer" />
                            <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-amber-400
              after:content-[''] after:absolute after:top-[2px] after:left-[2px]
              after:bg-white after:rounded-full after:h-5 after:w-5
              after:transition-all peer-checked:after:translate-x-full"></div>
                        </label>
                    </div>

                    <!-- Login Credentials Section -->
                    <transition name="fade">
                        <div v-if="form.showCredentials" class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Password</label>
                                <TextInput v-model="form.password" type="password" :placeholder="'Enter password'"
                                    class="w-full" />
                                <span v-if="form.errors.password" class="text-red-500 text-sm">{{
                                    form.errors.password }}</span>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Confirm Password</label>
                                <TextInput v-model="form.password_confirmation" type="password" class="w-full"
                                    :placeholder="'Confirm password'" />
                            </div>
                        </div>
                    </transition>

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-6">
                        <button type="submit" class="primary px-6 py-2 rounded-lg" :disabled="form.processing">
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
import { ref, onMounted, reactive, computed, watch } from "vue";
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
//route active check
const page = usePage();
const currentRoute = computed(() => page.url);
const isActive = (routeName: string) => {
    console.log(route().current() === routeName);
    return route().current() === routeName;
    return route().current() === routeName;
    return route().current(routeName);
}
const routeList = reactive({
    addEmployee: route('pim.index'),
    employeeList: route('PIM.EmployeeList'),
    reports: route('PIM.Reports'),
});
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
});

function previewLogo(e) {
    const file = e.target.files[0];
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
        },
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
</style>