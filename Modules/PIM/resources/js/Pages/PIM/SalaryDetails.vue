<template>

    <Head title="PIM - Salary Details" />
    <AuthenticatedLayout>
        <div class="flex h-screen gap-2 p-6 space-x-26">
            <!-- Sidebar -->
            <aside class="w-64  rounded-xl flex flex-col h-full">
                <EmployeeNav current="PIM.PersonalDetails" :employee="employee" />
            </aside>
            <main class="flex-1 flex flex-col h-full overflow-auto space-y-6">
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
                <div class="mt-6 bg-white rounded-xl shadow-md p-6 h-full">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Salary Details</h2>

                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Basic Salary</label>
                                <Input v-model="form.basic_salary" type="number" placeholder="Enter basic salary" />
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Allowances</label>
                                <Input v-model="form.allowances" type="number" placeholder="Enter allowances" />
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="primary px-6 py-2 rounded-lg" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save Salary Details' }}
                            </button>
                        </div>
                    </form>
                </div>

            </main>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Head, useForm, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import EmployeeNav from '../../Components/EmployeeNav.vue';
import Input from "@/Components/Input.vue";

const props = defineProps({
    employee: {
        type: Object,
        required: true,
    }
});
const form = useForm({
    basic_salary: "",
    allowances: "",
});
const isActive = (routeName: string) => route().current() === routeName;
function navigateTo(routeName: string) {
    router.visit(route(routeName));
}

function submitForm() {
    form.post(route("PIM.storeSalaryDetails"), {
        onSuccess: () => alert("Saved successfully!"),
    });
}
</script>
