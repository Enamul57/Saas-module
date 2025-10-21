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
            class="bg-white rounded-md md:ml-4 mt-5 shadow-sm focus:ring-orange-500 focus:border-orange-500 px-2 py-2 borderInput" />

        <!-- Date Input -->
        <input v-else-if="type === 'Date'" type="date" v-model="model" :class="[
            'font-inter font-normal text-[14px] leading-[22px] tracking-[0] mdL:ml-4 mt-5 py-2 px-1 outlin-none focus:outline-none',
            contactLabel.label || 'text-[#121212]',
            tax == true ? 'text-[#656565]' : '',
        ]" />

        <!-- Select -->
        <select v-else-if="type === 'select'" v-model="model"
            class="md:ml-4 bg-white mt-5 text-[#656565] hover:border-gray-300 focus:border-gray-300 border-0 rounded md:px-3 py-2 outline-none focus:outline-none focus:ring-0">
            <option disabled selected value="" class="text-[#656565] text-base font-normal">
                {{ tax !== true ? `Select ${label}` : label }}
            </option>
            <option v-for="(option, index) in options" :key="index" :value="option"
                class="text-[#656565] text-base font-normal">
                {{ option }}
            </option>
        </select>

        <!-- Textarea -->
        <textarea v-else-if="type === 'textarea'" v-model="model" rows="5" :placeholder="placeholder"
            class="w-full bg-white mt-5 border border-[#DBDBDB] rounded md:px-4 py-3 text-base text-[#2E2E2E] font-normal outline-none focus:outline-none focus:ring-0 focus:border-gray-400 resize-none"></textarea>
    </div>
</template>

<script setup lang="ts">
const model = defineModel < string | number | undefined > ();

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
</script>
