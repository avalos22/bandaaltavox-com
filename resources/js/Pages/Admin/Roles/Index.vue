<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

defineProps({
    roles: Array,
});

const showDeleteModal = ref(false);
const roleToDelete = ref(null);

const confirmDelete = (role) => {
    roleToDelete.value = role;
    showDeleteModal.value = true;
};

const deleteRole = () => {
    router.delete(route('admin.roles.destroy', roleToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            roleToDelete.value = null;
        },
    });
};

const permissionLabels = {
    users: 'Usuarios',
    roles: 'Roles',
    settings: 'Ajustes',
    packages: 'Paquetes',
    quotations: 'Cotizaciones',
    events: 'Eventos',
    contracts: 'Contratos',
    payments: 'Pagos',
    gallery: 'Galería',
    dashboard: 'Dashboard',
};
</script>

<template>
    <Head title="Roles y Permisos" />

    <AdminLayout title="Roles y Permisos">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-500">Define qué puede hacer cada tipo de usuario en el sistema</p>
            <Link
                :href="route('admin.roles.create')"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo Rol
            </Link>
        </div>

        <!-- Roles Grid -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="role in roles"
                :key="role.id"
                class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow"
            >
                <!-- Role header -->
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div :class="[
                            'flex h-10 w-10 items-center justify-center rounded-lg text-white',
                            role.name === 'Super Admin' ? 'bg-red-500' :
                            role.name === 'Admin' ? 'bg-blue-500' :
                            role.name === 'Vendedor' ? 'bg-emerald-500' :
                            role.name === 'Cliente' ? 'bg-amber-500' : 'bg-slate-500'
                        ]">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ role.name }}</h3>
                            <p class="text-xs text-gray-500">
                                {{ role.users_count }} {{ role.users_count === 1 ? 'usuario' : 'usuarios' }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-1">
                        <Link
                            :href="route('admin.roles.edit', role.id)"
                            class="rounded-lg p-1.5 text-gray-400 hover:bg-amber-50 hover:text-amber-600 transition-colors"
                            title="Editar"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </Link>
                        <button
                            v-if="!['Super Admin', 'Cliente'].includes(role.name)"
                            @click="confirmDelete(role)"
                            class="rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                            title="Eliminar"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Permission count -->
                <div class="mt-4 flex items-center gap-2 text-xs text-gray-500">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                    </svg>
                    {{ role.permissions_count }} {{ role.permissions_count === 1 ? 'permiso' : 'permisos' }}
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmModal :show="showDeleteModal" title="Eliminar Rol" @close="showDeleteModal = false">
            <p class="text-sm text-gray-600">
                ¿Estás seguro de que deseas eliminar el rol
                <strong>{{ roleToDelete?.name }}</strong>?
            </p>
            <p v-if="roleToDelete?.users_count > 0" class="mt-2 text-sm text-red-600 font-medium">
                ⚠️ Este rol tiene {{ roleToDelete?.users_count }} usuario(s) asignado(s).
                No se puede eliminar hasta reasignarlos.
            </p>
            <template #footer>
                <button
                    @click="showDeleteModal = false"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Cancelar
                </button>
                <button
                    @click="deleteRole"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors"
                >
                    Sí, Eliminar
                </button>
            </template>
        </ConfirmModal>
    </AdminLayout>
</template>
