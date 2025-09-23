<template>
    <thead class="bg-gray-50">
        <tr class="flex justify-between">
            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-sm"
                v-for="header in tableHeader" :key="header.name">
                {{ header.name }}
            </th>
        </tr>
    </thead>
</template>
<script setup lang="ts">
import { onMounted } from 'vue';
const props = defineProps({
    tableHeader: {
        type: Array,
        required: true,
        default: () => []
    }
})

onMounted(() => {
    console.log(props.tableHeader);
});
</script>