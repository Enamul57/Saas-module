<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- Page Header -->
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                HRM Dashboard
            </h2>
        </template>

        <!-- Main Content -->
        <div class="">
            <PrimaryButton>
                <Link variant="primary" :href="route('company.create')">
                Create Company
                </Link>
            </PrimaryButton>
        </div>
    </AuthenticatedLayout>
</template>
