<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    editUser: Object,
    roles: Array,
});

const form = useForm({
    name: props.editUser.name,
    email: props.editUser.email,
    phone: props.editUser.phone || '',
    password: '',
    role: props.editUser.roles?.[0]?.name || '',
    two_factor_type: props.editUser.two_factor_type || 'email',
    is_active: Boolean(props.editUser.is_active),
});

const resetForm = useForm({});

const isCliente = computed(() => form.role === 'Cliente');

const submit = () => {
    form.put(route('admin.users.update', props.editUser.id));
};

const resetTwoFactor = () => {
    if (!confirm(`¿Restablecer el 2FA de ${props.editUser.name}? El usuario deberá volver a configurarlo al iniciar sesión.`)) return;
    resetForm.post(route('admin.users.reset-two-factor', props.editUser.id));
};

const twoFactorLabel = () => {
    if (!props.editUser.two_factor_type) return 'No configurado';
    if (!props.editUser.two_factor_confirmed_at) return props.editUser.two_factor_type === 'totp' ? 'TOTP — pendiente de configurar' : 'Email — pendiente';
    return props.editUser.two_factor_type === 'totp' ? 'TOTP (Autenticador)' : 'Email OTP';
};

const twoFactorActive = props.editUser.two_factor_type && props.editUser.two_factor_confirmed_at;
</script>

<template>
    <Head :title="`Editar: ${editUser.name}`" />

    <AdminLayout :title="`Editar Usuario`">
        <!-- Breadcrumb -->
        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
            <Link :href="route('admin.users.index')" class="hover:text-amber-600 transition-colors">Usuarios</Link>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span class="text-gray-800 font-medium">{{ editUser.name }}</span>
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
                    <InputLabel for="password" value="Nueva contraseña (dejar vacío para no cambiar)" />
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        placeholder="Solo si deseas cambiarla"
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
                        Guardar Cambios
                    </button>
                </div>
            </form>

            <!-- Two-Factor Auth Management -->
            <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900">Autenticación de dos factores (2FA)</h3>
                <div class="mt-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span
                            :class="twoFactorActive ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'"
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                        >
                            {{ twoFactorLabel() }}
                        </span>
                        <span v-if="editUser.two_factor_confirmed_at" class="text-xs text-gray-400">
                            Configurado el {{ new Date(editUser.two_factor_confirmed_at).toLocaleDateString('es-MX') }}
                        </span>
                    </div>
                    <button
                        v-if="editUser.two_factor_type && editUser.id !== $page.props.auth.user.id"
                        type="button"
                        :disabled="resetForm.processing"
                        @click="resetTwoFactor"
                        class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors disabled:opacity-50"
                    >
                        Restablecer 2FA
                    </button>
                </div>

                <!-- 2FA type selector for Clientes -->
                <div v-if="isCliente" class="mt-4 flex flex-col gap-2 border-t border-gray-100 pt-4">
                    <p class="text-xs font-medium text-gray-600 mb-1">Cambiar método 2FA:</p>
                    <label class="flex cursor-pointer items-center gap-2.5">
                        <input type="radio" v-model="form.two_factor_type" value="email" class="h-4 w-4 border-gray-300 text-amber-500 focus:ring-amber-400" />
                        <div>
                            <span class="text-sm font-medium text-gray-700">Código por correo electrónico</span>
                            <p class="text-xs text-gray-500">Código de 6 dígitos enviado al correo en cada acceso.</p>
                        </div>
                    </label>
                    <label class="flex cursor-pointer items-center gap-2.5">
                        <input type="radio" v-model="form.two_factor_type" value="totp" class="h-4 w-4 border-gray-300 text-amber-500 focus:ring-amber-400" />
                        <div>
                            <span class="text-sm font-medium text-gray-700">App autenticadora (QR / TOTP)</span>
                            <p class="text-xs text-gray-500">El cliente configura QR con Google Authenticator o Authy.</p>
                        </div>
                    </label>
                    <p class="mt-1 text-xs text-amber-600">Cambiar el método restablecerá el 2FA actual del usuario.</p>
                </div>

                <p class="mt-3 text-xs text-gray-500">
                    Al restablecer el 2FA, el usuario deberá volver a configurarlo al próximo inicio de sesión.
                    Útil cuando un usuario pierde acceso a su autenticador o no recibe el correo.
                </p>
            </div>
        </div>
    </AdminLayout>
</template>
