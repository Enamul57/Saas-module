<template>
    <Link v-if="href" :href="href" :method="method" :as="as"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
        <slot />
    </Link>
    <button v-else type="button" @click="handleClick"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
        <slot />
    </button>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        default: null
    },
    method: {
        type: String,
        default: 'get'
    },
    as: {
        type: String,
        default: 'a'
    }
});

const handleClick = () => {
    if (props.href) {
        router.visit(props.href, {
            method: props.method,
        });
    }
};
</script>