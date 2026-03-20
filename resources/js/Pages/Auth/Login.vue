<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar Sesión" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-slate-900 px-4">
        <!-- Logo -->
        <div class="mb-8 text-center">
            <Link :href="route('home')" class="inline-block">
                <img src="/storage/images/logo-blanco-altavox.png" alt="Banda Alta Vox" class="h-16 mx-auto" />
            </Link>
        </div>

        <!-- Login card -->
        <div class="w-full max-w-sm rounded-2xl bg-white p-8 shadow-2xl">
            <h2 class="text-xl font-bold text-gray-800 text-center mb-1">Iniciar Sesión</h2>
            <p class="text-sm text-gray-500 text-center mb-6">Ingresa a tu panel de administración</p>

            <div v-if="status" class="mb-4 rounded-lg bg-emerald-50 p-3 text-sm font-medium text-emerald-700">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="email" value="Correo electrónico" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="tu@correo.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Contraseña" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="ms-2 text-sm text-gray-600">Recuérdame</span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-amber-600 hover:text-amber-800"
                    >
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>

                <PrimaryButton
                    class="mt-6 w-full justify-center rounded-lg bg-amber-500 py-2.5 hover:bg-amber-600 focus:bg-amber-600 focus:ring-amber-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Iniciar Sesión
                </PrimaryButton>
            </form>
        </div>

        <p class="mt-6 text-sm text-slate-500">
            © 2026 Banda Alta Vox
        </p>
    </div>
</template>
