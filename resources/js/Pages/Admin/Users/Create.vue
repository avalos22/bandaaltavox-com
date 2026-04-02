<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    roles: Array,
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    role: props.roles?.[0] || '',
    two_factor_type: 'email',
    is_active: true,
});

const isCliente = computed(() => form.role === 'Cliente');

const submit = () => {
    form.post(route('admin.users.store'));
};
</script>

<template>
    <Head title="Nuevo Usuario" />

    <AdminLayout title="Nuevo Usuario">
        <!-- Breadcrumb -->
        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
            <Link :href="route('admin.users.index')" class="hover:text-amber-600 transition-colors">Usuarios</Link>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span class="text-gray-800 font-medium">Nuevo Usuario</span>
        </nav>

        <div class="mx-auto max-w-2xl">
            <form @submit.prevent="submit" class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <!-- Name -->
                <div>
                    <InputLabel for="name" value="Nombre completo" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        placeholder="Ej: Juan Pérez"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Email -->
                <div>
                    <InputLabel for="email" value="Correo electrónico" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                        placeholder="juan@ejemplo.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- Phone -->
                <div>
                    <InputLabel for="phone" value="Teléfono (opcional)" />
                    <TextInput
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                        class="mt-1 block w-full"
                        placeholder="55 1234 5678"
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <!-- Password -->
                <div>
                    <InputLabel for="password" value="Contraseña" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        placeholder="Mínimo 8 caracteres"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <!-- Role -->
                <div>
                    <InputLabel for="role" value="Rol" />
                    <select
                        id="role"
                        v-model="form.role"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                        <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.role" />
                </div>

                <!-- 2FA Method (only for Clientes) -->
                <div v-if="isCliente">
                    <InputLabel value="Método de autenticación 2FA" />
                    <div class="mt-2 flex flex-col gap-2">
                        <label class="flex cursor-pointer items-center gap-2.5">
                            <input type="radio" v-model="form.two_factor_type" value="email" class="h-4 w-4 border-gray-300 text-amber-500 focus:ring-amber-400" />
                            <div>
                                <span class="text-sm font-medium text-gray-700">Código por correo electrónico</span>
                                <p class="text-xs text-gray-500">Se envía un código de 6 dígitos al correo del cliente en cada acceso.</p>
                            </div>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2.5">
                            <input type="radio" v-model="form.two_factor_type" value="totp" class="h-4 w-4 border-gray-300 text-amber-500 focus:ring-amber-400" />
                            <div>
                                <span class="text-sm font-medium text-gray-700">App autenticadora (QR / TOTP)</span>
                                <p class="text-xs text-gray-500">El cliente escanea un QR con Google Authenticator o Authy en su primer acceso.</p>
                            </div>
                        </label>
                    </div>
                    <InputError class="mt-2" :message="form.errors.two_factor_type" />
                </div>

                <!-- Active -->
                <div class="flex items-center gap-3">
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="checkbox" v-model="form.is_active" class="peer sr-only" />
                        <div class="h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-amber-500 peer-focus:ring-4 peer-focus:ring-amber-400/20 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-all peer-checked:after:translate-x-full"></div>
                    </label>
                    <span class="text-sm text-gray-700">Usuario activo</span>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                    <Link
                        :href="route('admin.users.index')"
                        class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors disabled:opacity-50"
                    >
                        Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
