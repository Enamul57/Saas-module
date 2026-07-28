<template>

    <Head title="Reports" />
    <AuthenticatedLayout>
        <div class="p-6 space-y-6">
            <!-- Navigation Bar -->
            <nav class="flex flex-wrap items-center gap-4 bg-white p-4 rounded-xl shadow-md border border-gray-200">
                <button @click="navigateTo('pim.index')"
                    :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive('pim.index') ? 'primary' : 'secondaryColor']">
                    <i class="bx bx-list-ul text-xl"></i>
                    <span>Employee List</span>
                </button>
                <button @click="navigateTo('pim.create')"
                    :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive('pim.create') ? 'primary' : 'secondaryColor']">
                    <i class="bx bx-user-plus text-xl"></i>
                    <span>Add Employee</span>
                </button>
                <button @click="navigateTo('PIM.Reports')"
                    :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive('PIM.Reports') ? 'primary' : 'secondaryColor']">
                    <i class="bx bx-bar-chart-alt-2 text-xl"></i>
                    <span>Reports</span>
                </button>
            </nav>

            <!-- Main Content -->
            <div class="mt-6 bg-white rounded-xl shadow-md p-6 min-h-[300px]">
                <h2 class="text-2xl font-semibold text-gray-700 mb-6">HR Reports</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Employees -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-80">Total Employees</p>
                                <p class="text-3xl font-bold">{{ stats.total_employees || 0 }}</p>
                            </div>
                            <i class="bx bx-user text-4xl opacity-50"></i>
                        </div>
                    </div>

                    <!-- Active Employees -->
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-80">Active Employees</p>
                                <p class="text-3xl font-bold">{{ stats.active_employees || 0 }}</p>
                            </div>
                            <i class="bx bx-user-check text-4xl opacity-50"></i>
                        </div>
                    </div>

                    <!-- Departments -->
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-80">Departments</p>
                                <p class="text-3xl font-bold">{{ stats.total_departments || 0 }}</p>
                            </div>
                            <i class="bx bx-building text-4xl opacity-50"></i>
                        </div>
                    </div>

                    <!-- New Hires This Month -->
                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm opacity-80">New Hires (This Month)</p>
                                <p class="text-3xl font-bold">{{ stats.new_hires || 0 }}</p>
                            </div>
                            <i class="bx bx-user-plus text-4xl opacity-50"></i>
                        </div>
                    </div>
                </div>

                <!-- Department Distribution -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Employee Distribution by Department</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div v-if="stats.departments && stats.departments.length > 0">
                            <div v-for="dept in stats.departments" :key="dept.id" class="mb-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span>{{ dept.job_category_name }}</span>
                                    <span>{{ dept.employees_count || 0 }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-amber-400 h-2.5 rounded-full transition-all duration-500"
                                        :style="{ width: getPercentage(dept.employees_count, stats.total_employees) + '%' }">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 text-center py-4">No department data available</p>
                    </div>
                </div>

                <!-- Recent Hires -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Hires</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div v-if="stats.recent_hires && stats.recent_hires.length > 0">
                            <div v-for="employee in stats.recent_hires" :key="employee.id"
                                class="flex items-center justify-between py-2 border-b last:border-0">
                                <div class="flex items-center gap-3">
                                    <img :src="employee.img || '/images/default-avatar.png'"
                                        class="w-10 h-10 rounded-full object-cover" @error="handleImageError" />
                                    <div>
                                        <p class="font-medium">{{ employee.first_name }} {{ employee.last_name }}</p>
                                        <p class="text-sm text-gray-500">{{ employee.email }}</p>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ formatDate(employee.created_at) }}
                                </span>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 text-center py-4">No recent hires</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({})
    }
});

const currentRoute = computed(() => page.url);

const isActive = (routeName) => {
    return route().current(routeName);
};

const navigateTo = (routeName) => {
    router.visit(route(routeName));
};

const getPercentage = (value, total) => {
    if (!total || total === 0) return 0;
    return Math.round((value / total) * 100);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const handleImageError = (event) => {
    event.target.src = '/images/default-avatar.png';
};
</script>

<style scoped>
.primary {
    background-color: #f59e0b;
    color: white;
    transition: all 0.3s ease;
}

.primary:hover {
    background-color: #d97706;
}

.secondaryColor {
    background-color: #f3f4f6;
    color: #4b5563;
}

.secondaryColor:hover {
    background-color: #e5e7eb;
}
</style>