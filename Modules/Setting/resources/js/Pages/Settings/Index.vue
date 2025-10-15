<template>

    <Head title="Settings" />

    <AuthenticatedLayout>
        <div class="p-6 space-y-6">
            <h2 class="text-2xl font-semibold text-gray-700">Settings</h2>

            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key" :class="tabClass(tab.key)"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none">
                        {{ tab.label }}
                    </button>
                </nav>
            </div>

            <!-- Settings Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Key
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Value
                            </th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="setting in filteredSettings" :key="setting.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ setting.key }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <input v-model="setting.value" class="border rounded px-2 py-1 w-full" type="text" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="updateSetting(setting)"
                                    class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                    Save
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, watch } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import axios from 'axios';

// Tabs for grouping settings
const tabs = [
    { key: 'organization', label: 'Organization' },
    { key: 'users', label: 'Users & Access' },
    { key: 'employee', label: 'Employee Management' },
    { key: 'attendance', label: 'Attendance & Time' },
    { key: 'leave', label: 'Leave Management' },
    { key: 'payroll', label: 'Payroll' },
    { key: 'performance', label: 'Performance' },
    { key: 'recruitment', label: 'Recruitment' },
    { key: 'notifications', label: 'Notifications' },
    { key: 'system', label: 'System' },
];

const activeTab = ref('organization');

// Sample settings data (replace with API fetch)
const settings = ref([
    { id: 1, key: 'company_name', value: 'Acme Corp', group: 'organization' },
    { id: 2, key: 'timezone', value: 'UTC+6', group: 'organization' },
    { id: 3, key: 'default_role', value: 'Employee', group: 'users' },
    { id: 4, key: 'max_annual_leave', value: '25', group: 'leave' },
]);

// Filter settings by active tab
const filteredSettings = computed(() => {
    return settings.value.filter(s => s.group === activeTab.value);
});

// Tab styling
const tabClass = (tabKey: string) => {
    return activeTab.value === tabKey
        ? 'border-indigo-500 text-indigo-600'
        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';
};

// Update setting (replace with API call)
const updateSetting = async (setting: any) => {
    try {
        await axios.put(`/api/settings/${setting.id}`, { value: setting.value });
        alert('Setting updated!');
    } catch (error) {
        console.error(error);
        alert('Error updating setting');
    }
};
</script>

<style scoped>
/* Add optional hover/focus styles if needed */
</style>
