<script setup>
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    method: String,
    email: String,
});

const form = useForm({
    code: '',
    trust_device: false,
});

const submit = () => {
    form.post(route('two-factor.verify'), {
        onFinish: () => {
            if (form.hasErrors) form.reset('code');
        },
    });
};

const resend = () => {
    router.post(route('two-factor.resend'));
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head title="Verificación de Seguridad" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-slate-900 px-4">
        <div class="mb-8 text-center">
            <img src="/storage/images/logo-blanco-altavox.png" alt="Banda Alta Vox" class="h-16 mx-auto" />
        </div>

        <div class="w-full max-w-sm rounded-2xl bg-white p-8 shadow-2xl">
            <div class="text-center mb-6">
                <!-- Lock icon -->
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-amber-100">
                    <svg class="h-7 w-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Verificación de Seguridad</h2>
                <p class="mt-2 text-sm text-gray-500">
                    <template v-if="method === 'totp'">
                        Ingresa el código de 6 dígitos de tu aplicación de autenticación.
                    </template>
                    <template v-else>
                        Enviamos un código de verificación a <strong>{{ email }}</strong>.
                    </template>
                </p>
            </div>

            <div v-if="$page.props.flash?.success" class="mb-4 rounded-lg bg-emerald-50 p-3 text-sm font-medium text-emerald-700">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.status" class="mb-4 rounded-lg bg-emerald-50 p-3 text-sm font-medium text-emerald-700">
                {{ $page.props.flash.status }}
            </div>

            <form @submit.prevent="submit">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Código de verificación</label>
                    <input
                        id="code"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="6"
                        autofocus
                        autocomplete="one-time-code"
                        class="mt-1 block w-full rounded-lg border-gray-300 text-center text-2xl tracking-[0.5em] shadow-sm focus:border-amber-500 focus:ring-amber-500"
                        v-model="form.code"
                        placeholder="000000"
                    />
                    <InputError class="mt-2" :message="form.errors.code" />
                </div>

                <!-- Trust this device -->
                <label class="mt-4 flex cursor-pointer items-center gap-2.5">
                    <input
                        type="checkbox"
                        v-model="form.trust_device"
                        class="h-4 w-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400"
                    />
                    <span class="text-sm text-gray-600">Confiar en este dispositivo por 30 días</span>
                </label>

                <PrimaryButton
                    class="mt-6 w-full justify-center rounded-lg bg-amber-500 py-2.5 hover:bg-amber-600 focus:bg-amber-600 focus:ring-amber-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Verificar
                </PrimaryButton>
            </form>

            <div v-if="method === 'email'" class="mt-4 text-center">
                <button @click="resend" class="text-sm text-amber-600 hover:text-amber-800 font-medium">
                    Reenviar código
                </button>
            </div>

            <div class="mt-4 text-center">
                <button @click="logout" class="text-sm text-gray-500 hover:text-gray-700">
                    Cerrar sesión
                </button>
            </div>
        </div>

        <p class="mt-6 text-sm text-slate-500">© 2026 Banda Alta Vox</p>
    </div>
</template>
