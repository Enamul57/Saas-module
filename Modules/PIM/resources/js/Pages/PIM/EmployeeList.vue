<template>

    <Head title="Employee List" />
    <AuthenticatedLayout>
        <div class="p-4 md:p-6 space-y-4 md:space-y-6">
            <!-- Navigation Bar -->
            <nav
                class="flex flex-wrap items-center gap-2 md:gap-4 bg-white p-3 md:p-4 rounded-xl shadow-md border border-gray-200">
                <button @click="navigateTo('pim.index')"
                    :class="['flex items-center gap-2 px-4 md:px-5 py-2 md:py-3 rounded-xl transition-all text-sm md:text-base', isActive('pim.index') ? 'primary' : 'secondaryColor']">
                    <i class="bx bx-list-ul text-lg md:text-xl"></i>
                    <span>Employee List</span>
                </button>
                <button @click="navigateTo('pim.create')"
                    :class="['flex items-center gap-2 px-4 md:px-5 py-2 md:py-3 rounded-xl transition-all text-sm md:text-base', isActive('pim.create') ? 'primary' : 'secondaryColor']">
                    <i class="bx bx-user-plus text-lg md:text-xl"></i>
                    <span>Add Employee</span>
                </button>
                <button @click="navigateTo('PIM.Reports')"
                    :class="['flex items-center gap-2 px-4 md:px-5 py-2 md:py-3 rounded-xl transition-all text-sm md:text-base', isActive('PIM.Reports') ? 'primary' : 'secondaryColor']">
                    <i class="bx bx-bar-chart-alt-2 text-lg md:text-xl"></i>
                    <span>Reports</span>
                </button>
            </nav>

            <!-- Main Content -->
            <div class="bg-white rounded-xl shadow-md p-4 md:p-6 min-h-[300px]">
                <!-- Header -->
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 md:mb-6 gap-3 md:gap-4">
                    <h2 class="text-xl md:text-2xl font-semibold text-gray-700">Employee List</h2>
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <div class="relative w-full sm:w-64">
                            <input type="text" v-model="search" @input="handleSearch" placeholder="Search employees..."
                                class="w-full px-4 py-2 pl-10 pr-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all text-sm" />
                            <i
                                class="bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
                        </div>
                        <button @click="navigateTo('pim.create')"
                            class="primary px-4 md:px-6 py-2 rounded-lg whitespace-nowrap text-sm md:text-base flex items-center justify-center">
                            <i class="bx bx-user-plus mr-2"></i>
                            Add Employee
                        </button>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-amber-400"></div>
                </div>

                <!-- Employee Table -->
                <div v-else class="overflow-x-auto">
                    <table class="w-full min-w-[800px]">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th
                                    class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    ID</th>
                                <th
                                    class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Photo</th>
                                <th
                                    class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Name</th>
                                <th
                                    class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Email</th>
                                <th
                                    class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Department</th>
                                <th
                                    class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="employees.data && employees.data.length === 0">
                                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                    <i class="bx bx-user-x text-5xl block mb-3 text-gray-300"></i>
                                    <p class="text-lg">No employees found</p>
                                    <p class="text-sm text-gray-400 mt-1">Try adjusting your search or add a new
                                        employee</p>
                                </td>
                            </tr>
                            <tr v-for="employee in employees.data" :key="employee.id"
                                class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-3 py-3 text-sm text-gray-600">{{ employee.employee_id || 'N/A' }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center justify-center">
                                        <div
                                            class="w-12 h-12 rounded-full overflow-hidden border border-gray-200 flex-shrink-0">
                                            <img :src="getEmployeeImage(employee)" :alt="employee.first_name"
                                                class="w-full h-full object-cover" @error="handleImageError"
                                                @load="handleImageLoad" v-if="imageLoaded[employee.id] !== false" />
                                            <div v-else
                                                class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-500 text-xs font-bold">
                                                {{ getInitials(employee) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="font-medium text-gray-800 text-sm">{{ employee.first_name }} {{
                                        employee.last_name }}</div>
                                </td>
                                <td class="px-3 py-3 text-sm text-gray-600">{{ employee.email }}</td>
                                <td class="px-3 py-3 text-sm text-gray-600">
                                    {{ employee.job_details?.job_category?.job_category_name || '-' }}
                                </td>
                                <td class="px-3 py-3">
                                    <span :class="[
                                        'inline-flex px-2.5 py-1 rounded-full text-xs font-medium',
                                        getStatusClass(employee.job_details?.employment_status || 'active')
                                    ]">
                                        {{ formatStatus(employee.job_details?.employment_status || 'active') }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <button @click="viewEmployee(employee.id)"
                                            class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-600 hover:text-blue-700 transition-colors"
                                            title="View Details">
                                            <i class="bx bx-show text-lg"></i>
                                        </button>
                                        <button @click="editEmployee(employee.id)"
                                            class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600 hover:text-amber-700 transition-colors"
                                            title="Edit">
                                            <i class="bx bx-edit text-lg"></i>
                                        </button>
                                        <button @click="deleteEmployee(employee.id)"
                                            class="p-1.5 rounded-lg hover:bg-red-50 text-red-600 hover:text-red-700 transition-colors"
                                            title="Delete">
                                            <i class="bx bx-trash text-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="employees.data && employees.data.length > 0"
                    class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4 pt-4 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        Showing <span class="font-medium">{{ employees.from || 0 }}</span> to
                        <span class="font-medium">{{ employees.to || 0 }}</span> of
                        <span class="font-medium">{{ employees.total || 0 }}</span> employees
                    </div>
                    <div class="flex gap-1">
                        <button v-for="link in employees.links" :key="link.label" @click="navigateToPage(link.url)"
                            v-html="link.label" :class="[
                                'px-3 py-1.5 rounded-lg text-sm transition-all',
                                link.active ? 'primary text-white shadow-sm' : 'bg-gray-100 hover:bg-gray-200 text-gray-700',
                                !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                            ]" :disabled="!link.url">
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted, reactive } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Notyf } from "notyf";
import "notyf/notyf.min.css";

// Props from Laravel
const props = defineProps({
    employees: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

// Notyf instance
const notyf = new Notyf({
    duration: 3000,
    position: {
        x: 'right',
        y: 'top',
    },
    dismissible: true,
});

// Reactive state
const page = usePage();
const search = ref(props.filters?.search || '');
const loading = ref(false);
const imageLoaded = reactive({});

// Computed
const currentRoute = computed(() => page.url);

// Methods
const isActive = (routeName) => {
    return route().current(routeName);
};

const navigateTo = (routeName, params = {}) => {
    router.visit(route(routeName, params));
};

const navigateToPage = (url) => {
    if (url) {
        router.visit(url);
    }
};

const handleSearch = () => {
    loading.value = true;
    router.visit(route('PIM.EmployeeList', { search: search.value }), {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            loading.value = false;
        }
    });
};

const getEmployeeImage = (employee) => {
    // Check if employee has an image
    if (employee.img) {
        // If the URL is relative, make it absolute
        const imageUrl = employee.img;
        // Check if it's a relative path
        if (imageUrl.startsWith('/')) {
            return window.location.origin + imageUrl;
        }
        // Check if it's a full URL
        if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
            return imageUrl;
        }
        // Otherwise, assume it's a relative path from public
        return window.location.origin + '/' + imageUrl;
    }
    // Generate avatar from name if no image
    return generateAvatar(employee);
};

const generateAvatar = (employee) => {
    const name = `${employee.first_name} ${employee.last_name}`;
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=f59e0b&color=fff&size=40&bold=true&rounded=true`;
};

const getInitials = (employee) => {
    const first = employee.first_name?.charAt(0) || '';
    const last = employee.last_name?.charAt(0) || '';
    return (first + last).toUpperCase();
};

const handleImageLoad = (event) => {
    // Image loaded successfully
    const img = event.target;
    if (img && img.parentElement) {
        img.parentElement.classList.remove('bg-gray-100');
        img.parentElement.classList.add('bg-transparent');
    }
};

const handleImageError = (event) => {
    const img = event.target;
    const employeeId = img.closest('tr')?.getAttribute('data-key') || 'unknown';

    // Mark this image as failed
    imageLoaded[employeeId] = false;

    // Get employee data from the row
    const tr = img.closest('tr');
    if (tr) {
        const nameCells = tr.querySelectorAll('td');
        if (nameCells.length > 2) {
            const nameText = nameCells[2]?.textContent || '';
            const names = nameText.trim().split(' ');
            const firstName = names[0] || 'User';
            const lastName = names.slice(1).join(' ') || '';

            // Use UI Avatars as fallback
            img.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(firstName + ' ' + lastName)}&background=f59e0b&color=fff&size=40&bold=true&rounded=true`;
            img.style.display = 'block';
        }
    }

    // If still can't load, show initials
    setTimeout(() => {
        if (!img.src || img.src.includes('ui-avatars.com') && !img.complete) {
            img.style.display = 'none';
            const parent = img.parentElement;
            if (parent) {
                const fallback = document.createElement('div');
                fallback.className = 'w-full h-full flex items-center justify-center bg-amber-100 text-amber-700 text-xs font-bold rounded-full';
                const names = (img.alt || 'User').split(' ');
                fallback.textContent = (names[0]?.[0] || '') + (names[1]?.[0] || '');
                parent.appendChild(fallback);
            }
        }
    }, 2000);
};

const getStatusClass = (status) => {
    const statusMap = {
        'active': 'bg-green-100 text-green-700',
        'inactive': 'bg-gray-100 text-gray-700',
        'terminated': 'bg-red-100 text-red-700',
        'resigned': 'bg-yellow-100 text-yellow-700',
        'on_leave': 'bg-blue-100 text-blue-700',
        'pending': 'bg-orange-100 text-orange-700'
    };
    return statusMap[status?.toLowerCase()] || 'bg-gray-100 text-gray-700';
};

const formatStatus = (status) => {
    const statusMap = {
        'active': 'Active',
        'inactive': 'Inactive',
        'terminated': 'Terminated',
        'resigned': 'Resigned',
        'on_leave': 'On Leave',
        'pending': 'Pending'
    };
    return statusMap[status?.toLowerCase()] || status;
};

const viewEmployee = (id) => {
    router.visit(route('PIM.getPersonalDetails', id));
};

const editEmployee = (id) => {
    router.visit(route('PIM.getPersonalDetails', id));
};

const deleteEmployee = (id) => {
    if (confirm('Are you sure you want to delete this employee?')) {
        router.delete(route('PIM.deleteEmployee', id), {
            onSuccess: () => {
                notyf.success('Employee deleted successfully!');
            },
            onError: () => {
                notyf.error('Failed to delete employee.');
            }
        });
    }
};

// Watch for filter changes
watch(() => props.filters, (newFilters) => {
    if (newFilters?.search !== undefined) {
        search.value = newFilters.search;
    }
}, { deep: true });

onMounted(() => {
    // Initialize image loaded state for all employees
    if (props.employees?.data) {
        props.employees.data.forEach(emp => {
            imageLoaded[emp.id] = true;
        });
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

.secondaryColor {
    background-color: #f3f4f6;
    color: #4b5563;
}

.secondaryColor:hover {
    background-color: #e5e7eb;
    transform: translateY(-1px);
}

/* Table styling */
table {
    border-collapse: collapse;
}

th:first-child,
td:first-child {
    padding-left: 0.5rem;
}

th:last-child,
td:last-child {
    padding-right: 0.5rem;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .p-6 {
        padding: 1rem;
    }

    .space-y-6 {
        gap: 1rem;
    }

    .min-h-\[300px\] {
        min-height: 200px;
    }
}

/* Animation for loading */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Table row hover effect */
tr {
    transition: background-color 0.2s ease;
}

/* Pagination button active state */
button[disabled] {
    cursor: not-allowed;
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

/* Image container fixes */
.rounded-full img {
    border-radius: 9999px !important;
}
</style>