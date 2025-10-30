<template>

    <Head title="PIM - Contact Details" />
    <AuthenticatedLayout>
        <div class="flex h-screen p-6">
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
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Contact Details</h2>

                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- Address, Division, City, Zip -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Address</label>
                                <Input v-model="form.address" type="text" placeholder="Enter address" />
                                <span v-if="form.errors.address" class="text-red-500 text-sm">{{ form.errors.address
                                    }}</span>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Division</label>
                                <Input v-model="form.division" type="text" placeholder="Enter division" />
                                <span v-if="form.errors.division" class="text-red-500 text-sm">{{ form.errors.division
                                    }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">City</label>
                                <Input v-model="form.city" type="text" placeholder="Enter city" />
                                <span v-if="form.errors.city" class="text-red-500 text-sm">{{ form.errors.city }}</span>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Zip / Postal Code</label>
                                <Input v-model="form.zip" type="text" placeholder="Enter zip/postal code" />
                                <span v-if="form.errors.zip" class="text-red-500 text-sm">{{ form.errors.zip }}</span>
                            </div>
                        </div>

                        <!-- Mobile & Emails -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Mobile (Home)</label>
                                <Input v-model="form.mobile_home" type="tel" placeholder="Enter home mobile" />
                                <span v-if="form.errors.mobile_home" class="text-red-500 text-sm">{{
                                    form.errors.mobile_home }}</span>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Mobile (Work)</label>
                                <Input v-model="form.mobile_work" type="tel" placeholder="Enter work mobile" />
                                <span v-if="form.errors.mobile_work" class="text-red-500 text-sm">{{
                                    form.errors.mobile_work }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Work Email</label>
                                <Input v-model="form.work_email" type="email" placeholder="Enter work email" />
                                <span v-if="form.errors.work_email" class="text-red-500 text-sm">{{
                                    form.errors.work_email }}</span>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Other Email</label>
                                <Input v-model="form.other_email" type="email" placeholder="Enter other email" />
                                <span v-if="form.errors.other_email" class="text-red-500 text-sm">{{
                                    form.errors.other_email }}</span>
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="primary px-6 py-2 rounded-lg" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save Contact Details' }}
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
import { Notyf } from "notyf";
import "notyf/notyf.min.css";

const props = defineProps({
    employee: {
        type: Object,
        required: true,
    }
});

const notyf = new Notyf({
    duration: 3000,
    position: { x: "right", y: "top" },
    dismissible: true,
});

const isActive = (routeName: string) => route().current() === routeName;

const form = useForm({
    address: props.employee?.contact_details?.address ?? "",
    division: props.employee?.contact_details?.division ?? "",
    city: props.employee?.contact_details?.city ?? "",
    zip: props.employee?.contact_details?.zip ?? "",
    mobile_home: props.employee?.contact_details?.mobile_home ?? "",
    mobile_work: props.employee?.contact_details?.mobile_work ?? "",
    work_email: props.employee?.email ?? "",
    other_email: props.employee?.contact_details?.other_email ?? "",
    processing: false,            // Optional: track submission
    errors: {},                   // Validation errors
});
function navigateTo(routeName: string) {
    router.visit(route(routeName));
}
function submitForm() {
    form.post(route("PIM.storeContactDetails", props.employee), {
        onSuccess: () => {
            notyf.success("Contact details saved successfully!");
            form.reset(); // reset form if needed
        },
    });
}

</script>
