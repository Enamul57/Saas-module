<template>
    <div class="flex flex-col">
        <label v-if="tax !== true" :class="[
            'text-xl font-playfair font-bold ',
            contactLabel.label || 'text-[#121212]',
        ]">
            {{ label }}
        </label>

        <!-- Text Input -->
        <input v-if="
            type === 'text' ||
            type === 'search' ||
            type === 'email' ||
            type === 'number' ||
            type === 'tel'
        " :type="type" :placeholder="placeholder" v-model="model" :disabled="disabled"
            class="bg-white rounded-md md:ml-4  shadow-sm focus:ring-orange-500 focus:border-orange-500 px-2 py-2 borderInput" />

        <!-- Date Input -->
        <input v-else-if="type === 'Date'" type="date" v-model="model" :placeholder="placeholder" :disabled="disabled"
            :class="[
                'bg-white rounded-md md:ml-4 shadow-sm focus:ring-orange-500 focus:border-orange-500 px-2 py-2 borderInput font-inter font-normal text-[14px] leading-[22px] tracking-[0] outline-none focus:outline-none',
                contactLabel.label || 'text-[#121212]',
                tax === true ? 'text-[#656565]' : ''
            ]" />

        <!-- Select -->
        <div v-else-if="type === 'select'" class="relative md:ml-4" @click="toggleDropdown">
            <div
                class="borderInput w-full px-3 py-2 rounded-3xl bg-white cursor-pointer shadow-sm flex justify-between items-center text-gray-700">
                <span>
                    {{ model || (tax !== true ? `Select ${label}` : label) }}
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.08 1.04l-4.25 4.25a.75.75 0 01-1.08 0L5.25 8.27a.75.75 0 01-.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </div>

            <!-- Dropdown List -->
            <ul v-if="open"
                class="absolute w-full bg-white shadow rounded mt-1 z-10 max-h-48 overflow-y-auto flex flex-col space-y-1">
                <li v-for="(option, index) in options" :key="index" @click.stop="selectOption(option)"
                    class="cursor-pointer px-2 py-1 primary hover:font-bold">
                    {{ option }}
                </li>
            </ul>
        </div>

        <!-- Textarea -->
        <textarea v-else-if="type === 'textarea'" v-model="model" rows="5" :placeholder="placeholder"
            class="w-full bg-white mt-5 border border-[#DBDBDB] rounded md:px-4 py-3 text-base text-[#2E2E2E] font-normal outline-none focus:outline-none focus:ring-0 focus:border-gray-400 resize-none"></textarea>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
const model = defineModel<string | number | undefined>();

defineProps({
    label: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        default: "text",
    },
    options: {
        type: Array as () => string[],
        default: () => [],
    },
    variant: {
        type: String,
        default: "",
    },
    contactLabel: {
        type: Object,
        default: () => ({}),
    },
    tax: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    }
});

const open = ref(false)

function toggleDropdown() {
    open.value = !open.value
}

function selectOption(option: string) {
    model.value = option
    open.value = false
}

document.addEventListener('click', (e) => {
    const target = e.target as HTMLElement
    if (!target.closest('.relative')) open.value = false
})
</script>
