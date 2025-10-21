<template>

    <Head title="Settings" />

    <AuthenticatedLayout>
        <div class="p-6 space-y-6">
            <h2 class="text-2xl font-semibold text-gray-700">Settings</h2>

            <!-- Edit Mode Toggle -->
            <div class="mb-4">
                <button :class="[
                    'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none',
                    isOn ? 'primaryColor' : 'bg-gray-300'
                ]" @click="toggle">
                    <span :class="[
                        'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                        isOn ? 'translate-x-6' : 'translate-x-1'
                    ]" />
                </button>
                <span> Edit</span>
            </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 mb-2">
                    <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key" :class="tabClass(tab.key)"
                        class="whitespace-nowrap py-2 px-3 border-b-2 font-medium text-sm focus:outline-none">
                        {{ (tab.label).charAt(0).toUpperCase() + (tab.label).slice(1) }}
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
                            <th
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Value
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="setting in filteredSettings" :key="setting.id" class="bg-white">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucFirst(setting.key) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <TextInput v-model="setting.value" :type="'text'" :disabled="!editMode" />
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="w-full">
                    <button v-if="editMode" @click="updateSetting"
                        class="primaryColor px-6 py-2 rounded-lg font-semibold float-right mt-5">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, computed, onMounted, watch, emit } from 'vue';
import axios from 'axios';
import TextInput from '@/Components/Input.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

const notyf = new Notyf();
const props = defineProps({
    modelValue: { type: Boolean, default: false },
});

const settings = ref([] as any[]);
const tabs = ref([]);
const editMode = ref(false); // toggle edit mode
const emit = defineEmits(['update:modelValue']);
const form = useForm({
    settings: [] as any[],
});
const isOn = ref(props.modelValue);

watch(() => props.modelValue, (val) => {
    isOn.value = val;
});

const toggle = () => {
    isOn.value = !isOn.value;
    editMode.value = !editMode.value;
}
// Fetch settings on mount
onMounted(() => {
    axios.get(route('setting.json')).then(response => {
        settings.value = response.data;
        form.settings = response.data;
        const uniqueGroups = Array.from(new Set(settings.value.map((s: any) => s.group)));
        tabs.value = uniqueGroups.map((s: any) => ({ key: s, label: s }));
        if (tabs.value.length > 0) activeTab.value = tabs.value[0].key;
    });
});

// Tabs for grouping settings
const activeTab = ref('organization');

// Filter settings by active tab
const filteredSettings = computed(() => {
    return settings.value.filter(s => s.group === activeTab.value);
});

// Tab styling
const tabClass = (tabKey: string) => {
    return activeTab.value === tabKey
        ? 'primaryColor borderInput'
        : 'border-transparent text-gray-500 hover:bg-[#f59e0b] hover:text-white hover:rounded-3xl';
};

// Update setting API call
const updateSetting = async () => {
    try {
        // await axios.put(`/api/settings/`);
        form.post(route('settings.store'), {
            onSuccess: () => {
                notyf.success('Settings Updated Successfully!');
            }
        });
    } catch (error) {
        console.error(error);
        notyf.error('Error updating setting');
    }
};

const ucFirst = (text: string) => {
    const formattedText = text.replace(/_/g, ' ');
    return formattedText.charAt(0).toUpperCase() + formattedText.slice(1);
}
</script>
