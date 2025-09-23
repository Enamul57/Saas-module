<template>
    <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="row in tableData" :key="row.id" class="flex justify-between">
            <!-- Render columns except "actions" -->
            <td v-for="col in columns" :key="col?.name" class=" px-3 py-1 whitespace-nowrap text-slate-600"
                v-if="col?.name !== 'actions'">
                {{ row[col.name] }}
            </td>
            <!-- Render actions slot -->
            <td v-if="columns.some(c => c.name === 'actions')" class="px-3 py-1 whitespace-nowrap space-x-2">
                <slot name="actions" :row="row"></slot>
            </td>
        </tr>
        <tr v-if="tableData.length === 0">
            <td :colspan="columns.length" class="px-3 py-2 text-center text-gray-400 text-sm">
                No data found.
            </td>
        </tr>
    </tbody>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';

const props = defineProps({
    tableData: { type: Array, required: true },
    columns: { type: Array, required: true, default: () => [] },
});
onMounted(() => {
    console.log(typeof (props.tableData));
});
</script>
