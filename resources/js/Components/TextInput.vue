<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});
const props = defineProps({
    placeholder: {
        type: String,
        default: '',
    },
});
const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="bg-white rounded-md md:ml-4 mt-5 shadow-sm focus:ring-orange-500 focus:border-orange-500 px-2 py-2 borderInput"
        v-model="model" ref="input" :placeholder="placeholder" />
</template>