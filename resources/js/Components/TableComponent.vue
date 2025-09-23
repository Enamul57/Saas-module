<template>
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <TableHeader :tableHeader="tableHeader" />
        <TableData :columns="tableHeader" :tableData="tableData">
            <!-- Forward actions slot -->
            <template #actions="{ row }">
                <slot name="tableAction" :row="row"></slot>
            </template>
        </TableData>
    </table>
</template>

<script setup lang="ts">
import TableHeader from './TableHeader.vue';
import TableData from './TableData.vue';
const props = defineProps({
    tableHeader: {
        type: Array,
        required: true,
        default: () => []
    },
    tableData: {
        type: Array,
        required: true,
    }
})
</script>