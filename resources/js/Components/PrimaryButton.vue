<template>
    <button
        :class="['inline-flex items-center rounded-md border border-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out focus:outline-nones', classes]">
        <slot />
    </button>
</template>
<script setup>

import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
    }
})

const classes = computed(() => {
    switch (props.variant) {
        case 'secondary':
            return 'bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500 active:bg-gray-900';
        case 'success':
            return 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 active:bg-green-900';
        case 'danger':
            return 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 active:bg-red-900';
        case 'warning':
            return 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-400 active:bg-yellow-700';
        case 'info':
            return 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 active:bg-blue-900';
        default:
            return 'bg-gradient-to-r from-amber-400 to-amber-600 text-white  hover:from-amber-300 hover:to-orange-600   active:from-amber-500 active:to-orange-700';
    }
});
</script>
