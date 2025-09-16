<template>
    <transition name="slide-fade">
        <div v-if="isVisible" class="fixed top-[4rem] right-5 z-50 w-80 md:w-96 flex justify-end">
            <div :class="bgClasses[type]"
                class="flex items-center justify-between p-4 rounded-lg shadow-lg text-white font-semibold space-x-4">
                <div class="flex-1">{{ message }}</div>
                <button @click="close" class="ml-4 text-white hover:text-gray-200 font-bold">×</button>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    type: { type: String, default: 'success' },
    message: { type: String, required: true },
    duration: { type: Number, default: 3000 },
});

const isVisible = ref(true);

const bgClasses = {
    success: 'bg-gradient-to-r from-green-500 to-green-400',
    danger: 'bg-gradient-to-r from-red-500 to-red-400',
    warning: 'bg-gradient-to-r from-yellow-500 to-yellow-400 text-black',
    info: 'bg-gradient-to-r from-blue-500 to-blue-400',
};

const close = () => {
    isVisible.value = false;
};

onMounted(() => {
    // Hide the notification after duration
    setTimeout(() => {
        isVisible.value = false;
    }, props.duration);
});
</script>

<style>
/* Slide + fade animation */
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all .3s ease;
}

.slide-fade-enter-from {
    opacity: 0;
    transform: translateX(20px);
}

.slide-fade-enter-to {
    opacity: 1;
    transform: translateX(0);
}

.slide-fade-leave-from {
    opacity: 1;
    transform: translateX(0);
}

.slide-fade-leave-to {
    opacity: 0;
    transform: translateX(20px);
}
</style>
