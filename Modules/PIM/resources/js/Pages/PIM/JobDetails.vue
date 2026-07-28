<template>

    <Head title="PIM - Job Details" />
    <AuthenticatedLayout>
        <div class="flex h-screen p-6 bg-gray-50">
            <!-- Sidebar -->
            <aside class="w-64 rounded-xl flex flex-col h-full mr-6">
                <EmployeeNav current="PIM.JobDetails" :employee="employee" />
            </aside>
            <main class="flex-1 flex flex-col h-full overflow-auto">
                <!-- Navigation Bar -->
                <nav
                    class="flex flex-wrap items-center gap-4 bg-white p-4 rounded-xl shadow-md border border-gray-200 mb-6">
                    <button @click="navigateTo('pim.index')"
                        :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all', isActive('pim.index') ? 'primary' : 'secondaryColor']">
                        <i class="bx bx-user-plus text-xl"></i>
                        <span>Add Employee</span>
                    </button>
                    <button @click="navigateTo('PIM.EmployeeList')"
                        :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all', isActive('PIM.EmployeeList') ? 'primary' : 'secondaryColor']">
                        <i class="bx bx-list-ul text-xl"></i>
                        <span>Employee List</span>
                    </button>
                    <button @click="navigateTo('PIM.Reports')"
                        :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all', isActive('PIM.Reports') ? 'primary' : 'secondaryColor']">
                        <i class="bx bx-bar-chart-alt-2 text-xl"></i>
                        <span>Reports</span>
                    </button>
                </nav>

                <div class="bg-white rounded-xl shadow-md p-6 flex-1 overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-semibold text-gray-700">Job Details</h2>
                        <span class="text-sm text-gray-500">Employee: {{ employee.first_name }} {{ employee.last_name
                            }}</span>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- Row 1: Job Title (Searchable Dropdown) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Job Title <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" v-model="jobTitleSearch" @input="filterJobTitles"
                                        @focus="showJobTitleDropdown = true"
                                        @blur="setTimeout(() => showJobTitleDropdown = false, 200)"
                                        placeholder="Search or enter job title..."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent" />
                                    <div v-if="showJobTitleDropdown && filteredJobTitles.length > 0"
                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        <div v-for="title in filteredJobTitles" :key="title.id"
                                            @mousedown="selectJobTitle(title)"
                                            class="px-4 py-2 hover:bg-amber-50 cursor-pointer transition-colors">
                                            {{ title.job_title_name }}
                                        </div>
                                        <div @mousedown="selectJobTitle({ job_title_name: jobTitleSearch, id: null })"
                                            class="px-4 py-2 hover:bg-amber-50 cursor-pointer text-amber-600 border-t">
                                            + Create "{{ jobTitleSearch }}"
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" v-model="form.job_title_id" />
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Department <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" v-model="departmentSearch" @input="filterDepartments"
                                        @focus="showDepartmentDropdown = true"
                                        @blur="setTimeout(() => showDepartmentDropdown = false, 200)"
                                        placeholder="Search or enter department..."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent" />
                                    <div v-if="showDepartmentDropdown && filteredDepartments.length > 0"
                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        <div v-for="dept in filteredDepartments" :key="dept.id"
                                            @mousedown="selectDepartment(dept)"
                                            class="px-4 py-2 hover:bg-amber-50 cursor-pointer transition-colors">
                                            {{ dept.job_category_name }}
                                        </div>
                                        <div @mousedown="selectDepartment({ job_category_name: departmentSearch, id: null })"
                                            class="px-4 py-2 hover:bg-amber-50 cursor-pointer text-amber-600 border-t">
                                            + Create "{{ departmentSearch }}"
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" v-model="form.job_category_id" />
                            </div>
                        </div>

                        <!-- Row 2: Job Unit and Location -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Job Unit</label>
                                <div class="relative">
                                    <input type="text" v-model="unitSearch" @input="filterUnits"
                                        @focus="showUnitDropdown = true"
                                        @blur="setTimeout(() => showUnitDropdown = false, 200)"
                                        placeholder="Search or enter job unit..."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent" />
                                    <div v-if="showUnitDropdown && filteredUnits.length > 0"
                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                        <div v-for="unit in filteredUnits" :key="unit.id" @mousedown="selectUnit(unit)"
                                            class="px-4 py-2 hover:bg-amber-50 cursor-pointer transition-colors">
                                            {{ unit.job_unit_name }}
                                        </div>
                                        <div @mousedown="selectUnit({ job_unit_name: unitSearch, id: null })"
                                            class="px-4 py-2 hover:bg-amber-50 cursor-pointer text-amber-600 border-t">
                                            + Create "{{ unitSearch }}"
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" v-model="form.job_unit_id" />
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Location</label>
                                <input v-model="form.location" type="text" placeholder="Enter location"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent" />
                            </div>
                        </div>

                        <!-- Row 3: Employee Status and Employment Type -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Employee Status</label>
                                <select v-model="form.employee_status"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="terminated">Terminated</option>
                                    <option value="resigned">Resigned</option>
                                    <option value="on_leave">On Leave</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Employee Type</label>
                                <select v-model="form.employee_type"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent">
                                    <option value="">Select Employee Type</option>
                                    <option value="full_time">Full Time</option>
                                    <option value="part_time">Part Time</option>
                                    <option value="contract">Contract</option>
                                    <option value="intern">Intern</option>
                                    <option value="temporary">Temporary</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 4: Joining Date and Confirmation Date -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Joining Date</label>
                                <input v-model="form.joining_date" type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Confirmation Date</label>
                                <input v-model="form.confirmation_date" type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent" />
                            </div>
                        </div>

                        <!-- Row 5: Contract Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Contract Start Date</label>
                                <input v-model="form.contract_start_date" type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent" />
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Contract End Date</label>
                                <input v-model="form.contract_end_date" type="date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent" />
                            </div>
                        </div>

                        <!-- Row 6: Shift and Work Location -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Shift</label>
                                <select v-model="form.shift"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent">
                                    <option value="">Select Shift</option>
                                    <option value="morning">Morning (9 AM - 5 PM)</option>
                                    <option value="afternoon">Afternoon (1 PM - 9 PM)</option>
                                    <option value="night">Night (9 PM - 5 AM)</option>
                                    <option value="rotating">Rotating</option>
                                    <option value="flexible">Flexible</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Work Location</label>
                                <input v-model="form.work_location" type="text" placeholder="Enter work location"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent" />
                            </div>
                        </div>

                        <!-- Row 7: Reports To (Manager/Supervisor) -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Reports To (Manager/Supervisor)</label>
                            <div class="relative">
                                <input type="text" v-model="managerSearch" @input="filterManagers"
                                    @focus="showManagerDropdown = true"
                                    @blur="setTimeout(() => showManagerDropdown = false, 200)"
                                    placeholder="Search for manager or enter name..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent" />
                                <div v-if="showManagerDropdown && filteredManagers.length > 0"
                                    class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                    <div v-for="manager in filteredManagers" :key="manager.id"
                                        @mousedown="selectManager(manager)"
                                        class="px-4 py-2 hover:bg-amber-50 cursor-pointer transition-colors flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden flex-shrink-0">
                                            <img :src="manager.img || `https://ui-avatars.com/api/?name=${encodeURIComponent(manager.first_name + ' ' + manager.last_name)}&background=f59e0b&color=fff&size=32`"
                                                class="w-full h-full object-cover" />
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ manager.first_name }} {{ manager.last_name }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ manager.email }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="showManagerDropdown && managerSearch && filteredManagers.length === 0"
                                    class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg">
                                    <div @mousedown="selectManager({ first_name: managerSearch, last_name: '', id: null })"
                                        class="px-4 py-2 hover:bg-amber-50 cursor-pointer text-amber-600">
                                        + Create new manager "{{ managerSearch }}"
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" v-model="form.reports_to" />
                            <div v-if="form.reports_to && typeof form.reports_to === 'string'"
                                class="mt-1 text-sm text-gray-500">
                                Manager: {{ form.reports_to }}
                            </div>
                        </div>

                        <!-- Row 8: Job Description -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Job Description</label>
                            <textarea v-model="form.job_description" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                                placeholder="Enter job description"></textarea>
                        </div>

                        <!-- Row 9: Responsibilities -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Responsibilities</label>
                            <textarea v-model="form.responsibilities" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                                placeholder="Enter responsibilities"></textarea>
                        </div>

                        <!-- Row 10: Qualifications -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Qualifications</label>
                            <textarea v-model="form.qualifications" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                                placeholder="Enter qualifications"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" @click="navigateTo('PIM.getPersonalDetails', employee.id)"
                                class="px-6 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="primary px-6 py-2 rounded-lg flex items-center gap-2"
                                :disabled="form.processing">
                                <i v-if="form.processing" class="bx bx-loader-alt animate-spin"></i>
                                {{ form.processing ? 'Saving...' : 'Save Job Details' }}
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
import { ref, computed, onMounted, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import EmployeeNav from '../../Components/EmployeeNav.vue';
import { Notyf } from "notyf";
import "notyf/notyf.min.css";

const notyf = new Notyf({
    duration: 3000,
    position: { x: 'right', y: 'top' },
    dismissible: true,
});

const props = defineProps({
    employee: {
        type: Object,
        required: true,
    },
    jobCategories: {
        type: Array,
        default: () => []
    },
    jobTitles: {
        type: Array,
        default: () => []
    },
    jobUnits: {
        type: Array,
        default: () => []
    },
    managers: {
        type: Array,
        default: () => []
    }
});

// Get existing job details
const jobDetails = props.employee?.job_details || {};

// Form data
const form = useForm({
    job_title: jobDetails.job_title || '',
    job_category: jobDetails.job_category || '',
    job_unit: jobDetails.job_unit || '',
    location: jobDetails.location || '',
    employee_status: jobDetails.employee_status || 'active',
    employee_type: jobDetails.employee_type || '',
    joining_date: jobDetails.joining_date || '',
    confirmation_date: jobDetails.confirmation_date || '',
    contract_start_date: jobDetails.contract_start_date || '',
    contract_end_date: jobDetails.contract_end_date || '',
    shift: jobDetails.shift || '',
    work_location: jobDetails.work_location || '',
    job_description: jobDetails.job_description || '',
    responsibilities: jobDetails.responsibilities || '',
    qualifications: jobDetails.qualifications || '',
    reports_to: jobDetails.reports_to || '',
    job_category_id: jobDetails.job_category_id || '',
    job_title_id: jobDetails.job_title_id || '',
    job_unit_id: jobDetails.job_unit_id || '',
});

// Searchable dropdowns
const jobTitleSearch = ref(jobDetails.job_title || '');
const departmentSearch = ref(jobDetails.job_category || '');
const unitSearch = ref(jobDetails.job_unit || '');
const managerSearch = ref('');

const showJobTitleDropdown = ref(false);
const showDepartmentDropdown = ref(false);
const showUnitDropdown = ref(false);
const showManagerDropdown = ref(false);

// Filtered lists
const filteredJobTitles = ref([]);
const filteredDepartments = ref([]);
const filteredUnits = ref([]);
const filteredManagers = ref([]);

// Filter functions
const filterJobTitles = () => {
    const search = jobTitleSearch.value.toLowerCase();
    filteredJobTitles.value = props.jobTitles.filter(title =>
        title.job_title_name.toLowerCase().includes(search)
    );
};

const filterDepartments = () => {
    const search = departmentSearch.value.toLowerCase();
    filteredDepartments.value = props.jobCategories.filter(dept =>
        dept.job_category_name.toLowerCase().includes(search)
    );
};

const filterUnits = () => {
    const search = unitSearch.value.toLowerCase();
    filteredUnits.value = props.jobUnits.filter(unit =>
        unit.job_unit_name.toLowerCase().includes(search)
    );
};

const filterManagers = () => {
    const search = managerSearch.value.toLowerCase();
    filteredManagers.value = props.managers.filter(manager =>
        `${manager.first_name} ${manager.last_name}`.toLowerCase().includes(search) ||
        manager.email.toLowerCase().includes(search)
    );
};

// Selection functions
const selectJobTitle = (title) => {
    form.job_title = title.job_title_name;
    form.job_title_id = title.id;
    jobTitleSearch.value = title.job_title_name;
    showJobTitleDropdown.value = false;
};

const selectDepartment = (dept) => {
    form.job_category = dept.job_category_name;
    form.job_category_id = dept.id;
    departmentSearch.value = dept.job_category_name;
    showDepartmentDropdown.value = false;
};

const selectUnit = (unit) => {
    form.job_unit = unit.job_unit_name;
    form.job_unit_id = unit.id;
    unitSearch.value = unit.job_unit_name;
    showUnitDropdown.value = false;
};

const selectManager = (manager) => {
    if (manager.id) {
        form.reports_to = manager.id;
        managerSearch.value = `${manager.first_name} ${manager.last_name}`;
    } else {
        form.reports_to = managerSearch.value;
    }
    showManagerDropdown.value = false;
};

// Navigation
const isActive = (routeName: string) => route().current() === routeName;

function navigateTo(routeName: string, params?: any) {
    if (params) {
        router.visit(route(routeName, params));
    } else {
        router.visit(route(routeName));
    }
}

// Submit form
function submitForm() {
    form.post(route("PIM.storeJobDetails", props.employee.id), {
        preserveScroll: true,
        onSuccess: () => {
            notyf.success("Job details saved successfully!");
        },
        onError: (errors) => {
            if (errors) {
                notyf.error("Please check the form for errors.");
            }
        }
    });
}

// Initialize dropdowns with existing values
onMounted(() => {
    if (jobDetails.job_title) {
        jobTitleSearch.value = jobDetails.job_title;
    }
    if (jobDetails.job_category) {
        departmentSearch.value = jobDetails.job_category;
    }
    if (jobDetails.job_unit) {
        unitSearch.value = jobDetails.job_unit;
    }
    if (jobDetails.reports_to) {
        // Try to find the manager name
        const manager = props.managers.find(m => m.id === jobDetails.reports_to);
        if (manager) {
            managerSearch.value = `${manager.first_name} ${manager.last_name}`;
        }
    }
});
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

.secondaryColor {
    background-color: #f3f4f6;
    color: #4b5563;
}

.secondaryColor:hover {
    background-color: #e5e7eb;
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

/* Fix for date inputs */
input[type="date"] {
    min-height: 44px;
}

/* Scrollbar styling for dropdowns */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #f59e0b;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #d97706;
}
</style>