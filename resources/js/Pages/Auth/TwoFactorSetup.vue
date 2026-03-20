<script setup>
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    qrCodeUrl: String,
    secret: String,
});

const showSecret = ref(false);

const form = useForm({
    code: '',
});

const submit = () => {
    form.post(route('two-factor.confirm-setup'), {
        onFinish: () => {
            if (form.hasErrors) form.reset('code');
        },
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head title="Configurar Autenticación" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-slate-900 px-4 py-8">
        <div class="mb-8 text-center">
            <img src="/storage/images/logo-blanco-altavox.png" alt="Banda Alta Vox" class="h-16 mx-auto" />
        </div>

        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
            <div class="text-center mb-6">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-amber-100">
                    <svg class="h-7 w-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Configurar Autenticación</h2>
                <p class="mt-2 text-sm text-gray-500">
                    Escanea el código QR con tu aplicación de autenticación (Google Authenticator, Authy, etc.)
                </p>
            </div>

            <!-- QR Code -->
            <div class="flex justify-center mb-4">
                <div class="rounded-xl border-2 border-gray-100 bg-white p-4">
                    <img
                        :src="'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(qrCodeUrl)"
                        alt="Código QR"
                        class="h-48 w-48"
                    />
                </div>
            </div>

            <!-- Manual entry -->
            <div class="mb-6 text-center">
                <button @click="showSecret = !showSecret" class="text-sm text-amber-600 hover:text-amber-800 font-medium">
                    {{ showSecret ? 'Ocultar clave manual' : '¿No puedes escanear? Ingresa la clave manualmente' }}
                </button>
                <div v-if="showSecret" class="mt-3 rounded-lg bg-gray-50 p-3">
                    <p class="text-xs text-gray-500 mb-1">Clave secreta:</p>
                    <code class="text-sm font-mono font-semibold text-gray-800 tracking-wider select-all">{{ secret }}</code>
                </div>
            </div>

            <!-- Verify form -->
            <form @submit.prevent="submit">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                        Ingresa el código de 6 dígitos de tu app
                    </label>
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

                <PrimaryButton
                    class="mt-6 w-full justify-center rounded-lg bg-amber-500 py-2.5 hover:bg-amber-600 focus:bg-amber-600 focus:ring-amber-500"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Activar Autenticación
                </PrimaryButton>
            </form>

            <div class="mt-4 text-center">
                <button @click="logout" class="text-sm text-gray-500 hover:text-gray-700">
                    Cerrar sesión
                </button>
            </div>
        </div>

        <p class="mt-6 text-sm text-slate-500">© 2026 Banda Alta Vox</p>
    </div>
</template>
