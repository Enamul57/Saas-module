<template>
    <div class="flex flex-wrap items-center justify-between gap-2 mt-6"
        v-if="pagination && paginationButtons.length > 1">
        <!-- Previous Button -->
        <button @click="goToPage(currentPage - 1)" :disabled="!pagination.prev_page_url"
            class="text-sm px-3 py-1.5 border rounded-full cursor-pointer text-[#121212] hover:bg-gray-100 disabled:opacity-50">
            ← Previous
        </button>

        <!-- Page Buttons -->
        <div class="flex space-x-5">
            <template v-for="(link, index) in paginationButtons" :key="index">
                <span v-if="link === '...'" class="text-sm px-2">...</span>
                <button v-else @click="onPageClick(link)" :class="[
                    'w-8 h-8 rounded-full text-sm font-semibold cursor-pointer',
                    link === currentPage ? 'bg-[#e6532e] text-white' : 'hover:bg-gray-200 text-[#121212]'
                ]">
                    {{ link }}
                </button>
            </template>
        </div>

        <!-- Next Button -->
        <button @click="goToPage(currentPage + 1)" :disabled="!pagination.next_page_url"
            class="text-sm px-3 py-1.5 border rounded-full cursor-pointer text-[#121212] hover:bg-gray-100 disabled:opacity-50">
            Next →
        </button>

        <!-- Per Page Dropdown -->
        <select v-model.number="perPageRef" @change="onPerPageChange" class="border rounded-md px-8 py-1">
            <option v-for="option in [5, 10, 25, 50]" :key="option" :value="option"> {{ option }}</option>
        </select>
    </div>
</template>


<script setup lang="ts">
import { computed, defineProps, defineEmits, ref, watch } from 'vue';
import { useStore } from 'vuex';

interface PaginationLink { url: string | null, label: string, active: boolean }

const props = defineProps({
    pagination: Object as () => { prev_page_url: string | null, next_page_url: string | null, current_page: number, last_page: number, links: PaginationLink[] },
    currentPage: Number,
    perPage: Number
});

const emit = defineEmits(['page-change', 'per-page-change']);


const store = useStore();
const perPageRef = ref(store.getters.perPage);


// Sync with Vuex store
watch(() => store.getters.perPage, val => perPageRef.value = val);

const lastPage = computed(() => props.pagination.last_page || 1);

// Pagination buttons
const paginationButtons = computed(() => {
    const total = lastPage.value;
    const current = props.currentPage;
    const delta = 2;
    const range: (number | string)[] = [];
    let left = current - delta;
    let right = current + delta;

    if (left < 2) { right += 2 - left; left = 2; }
    if (right > total - 1) { left -= right - (total - 1); right = total - 1; if (left < 2) left = 2; }

    range.push(1);
    if (left > 2) range.push('...');
    for (let i = left; i <= right; i++) range.push(i);
    if (right < total - 1) range.push('...');
    if (total > 1) range.push(total);

    return range;
});

const goToPage = (page: number) => {
    if (page >= 1 && page <= lastPage.value) emit('page-change', page);
};
const onPageClick = (page: number | string) => { if (typeof page === 'number') goToPage(page); };
const onPerPageChange = () => {
    store.dispatch('updatePerPage', perPageRef.value);
    emit('per-page-change', perPageRef.value);
};
</script>
