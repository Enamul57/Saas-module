<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    company_name: "",
    email: '',
    domain: '',
    password: '',
    password_confirmation: '',

});

const submit = () => {
    form.post(route('company.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthenticatedLayout>

        <Head title="Log in" />

        <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">

                <!-- Status Message -->
                <div v-if="status" class="rounded-md bg-green-50 p-4 text-green-700 text-center">
                    {{ status }}
                </div>

                <!-- Form Card -->
                <form @submit.prevent="submit" class="mt-8 space-y-6 bg-white shadow-lg rounded-lg p-8">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="company_name" value="Company Name" />
                            <TextInput id="company_name" type="text" v-model="form.company_name" required autofocus
                                autocomplete="company_name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <InputError class="mt-2 text-sm" :message="form.errors.company_name" />
                        </div>
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput id="email" type="email" v-model="form.email" required autofocus
                                autocomplete="email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <InputError class="mt-2 text-sm" :message="form.errors.email" />
                        </div>
                        <div>
                            <InputLabel for="domain" value="Domain" />
                            <TextInput id="domain" type="text" v-model="form.domain" required autofocus
                                autocomplete="domain"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <InputError class="mt-2 text-sm" :message="form.errors.domain" />
                        </div>
                        <div>
                            <InputLabel for="password" value="Password" />
                            <TextInput id="password" type="password" v-model="form.password" required
                                autocomplete="current-password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <InputError class="mt-2 text-sm" :message="form.errors.password" />
                        </div>
                        <div class="mt-4">
                            <InputLabel for="password_confirmation" value="Confirm Password" />

                            <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                                v-model="form.password_confirmation" required autocomplete="new-password" />

                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>
                    </div>

                    <PrimaryButton variant="primary" :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing">
                        Create Company
                    </PrimaryButton>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
