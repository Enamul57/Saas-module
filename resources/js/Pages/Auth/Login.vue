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
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('central.login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Log in" />

        <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <!-- Logo / Header -->
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-gray-900">Welcome Back!</h1>
                    <p class="mt-2 text-sm text-gray-600">Sign in to access your HRM Dashboard</p>
                </div>

                <!-- Status Message -->
                <div v-if="status" class="rounded-md bg-green-50 p-4 text-green-700 text-center">
                    {{ status }}
                </div>

                <!-- Form Card -->
                <form @submit.prevent="submit" class="mt-8 space-y-6 bg-white shadow-lg rounded-lg p-8">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput id="email" type="email" v-model="form.email" required autofocus
                                autocomplete="username"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <InputError class="mt-2 text-sm" :message="form.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="password" value="Password" />
                            <TextInput id="password" type="password" v-model="form.password" required
                                autocomplete="current-password"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            <InputError class="mt-2 text-sm" :message="form.errors.password" />
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center space-x-2">
                                <Checkbox name="remember" v-model:checked="form.remember" />
                                <span class="text-sm text-gray-600">Remember me</span>
                            </label>

                            <Link v-if="canResetPassword" :href="route('central.password.request')"
                                class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                            Forgot your password?
                            </Link>
                        </div>
                    </div>

                    <PrimaryButton
                        class="w-full mt-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600 text-white font-semibold rounded-md shadow-md transition duration-200 ease-in-out"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }" :disabled="form.processing">
                        Log in
                    </PrimaryButton>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>
