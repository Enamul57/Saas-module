<template>
    <div v-if="showPopup" class="fixed inset-0 bg-transparent backdrop-blur-sm flex items-center justify-center z-50 "
        @click="$emit('update:showPopup', false)">
        <div class="popup rounded-lg p-6 w-80 text-center">
            <p class="text-slate-700 text-xl mb-4 ">{{ popupMessage }}</p>
            <button @click="$emit('update:showPopup', false)"
                class="text-white primaryColor rounded-full px-4 py-2 text-sm shadow-lg">
                OK
            </button>
        </div>
    </div>
</template>
<script setup lang="ts">
import { onMounted } from "vue";
const props = defineProps({
    showPopup: {
        type: Boolean,
        required: true,
    },
    popupMessage: {
        type: String,
        required: true,
    }
})
const emits = defineEmits(['update:showPopup', 'close']);
onMounted(() => {
    console.log(props.showPopup);
});
</script>