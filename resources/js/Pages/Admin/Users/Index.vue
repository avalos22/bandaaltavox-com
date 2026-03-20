<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SearchInput from '@/Components/SearchInput.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const roleFilter = ref(props.filters?.role || '');
const statusFilter = ref(props.filters?.status || '');

let debounce = null;

const applyFilters = () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(route('admin.users.index'), {
            search: search.value || undefined,
            role: roleFilter.value || undefined,
            status: statusFilter.value || undefined,
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
};

watch([search, roleFilter, statusFilter], applyFilters);

// Delete confirmation
const showDeleteModal = ref(false);
const userToDelete = ref(null);

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const deleteUser = () => {
    router.delete(route('admin.users.destroy', userToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            userToDelete.value = null;
        },
    });
};

const toggleActive = (user) => {
    router.patch(route('admin.users.toggle-active', user.id));
};
</script>

<template>
    <Head title="Usuarios" />

    <AdminLayout title="Usuarios">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-500">Administra las cuentas de acceso al sistema</p>
            </div>
            <Link
                :href="route('admin.users.create')"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo Usuario
            </Link>
        </div>

        <!-- Filters -->
        <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <SearchInput v-model="search" placeholder="Buscar por nombre, email o teléfono..." />

            <select
                v-model="roleFilter"
                class="rounded-lg border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-700 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20"
            >
                <option value="">Todos los roles</option>
                <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
            </select>

            <select
                v-model="statusFilter"
                class="rounded-lg border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-700 focus:border-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-400/20"
            >
                <option value="">Todos los estados</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>
        </div>

        <!-- Desktop Table -->
        <div class="hidden sm:block overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Usuario</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rol</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Teléfono</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50/50 transition-colors">
                        <!-- User info -->
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700 text-sm font-semibold">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ user.name }}</p>
                                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                                </div>
                            </div>
                        </td>
                        <!-- Role -->
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                                {{ user.roles?.[0]?.name ?? 'Sin rol' }}
                            </span>
                        </td>
                        <!-- Phone -->
                        <td class="px-5 py-4 text-sm text-gray-600">
                            {{ user.phone || '—' }}
                        </td>
                        <!-- Status -->
                        <td class="px-5 py-4">
                            <StatusBadge :active="user.is_active" />
                        </td>
                        <!-- Actions -->
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <Link
                                    :href="route('admin.users.edit', user.id)"
                                    class="rounded-lg p-2 text-gray-400 hover:bg-amber-50 hover:text-amber-600 transition-colors"
                                    title="Editar"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </Link>
                                <button
                                    @click="toggleActive(user)"
                                    :class="[
                                        'rounded-lg p-2 transition-colors',
                                        user.is_active
                                            ? 'text-gray-400 hover:bg-orange-50 hover:text-orange-600'
                                            : 'text-gray-400 hover:bg-emerald-50 hover:text-emerald-600'
                                    ]"
                                    :title="user.is_active ? 'Desactivar' : 'Activar'"
                                >
                                    <svg v-if="user.is_active" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </button>
                                <button
                                    @click="confirmDelete(user)"
                                    class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                                    title="Eliminar"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="5" class="px-5 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No se encontraron usuarios</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="sm:hidden space-y-3">
            <div v-for="user in users.data" :key="user.id" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700 text-sm font-semibold">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ user.name }}</p>
                            <p class="text-sm text-gray-500">{{ user.email }}</p>
                        </div>
                    </div>
                    <StatusBadge :active="user.is_active" />
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                            {{ user.roles?.[0]?.name ?? 'Sin rol' }}
                        </span>
                        <span v-if="user.phone" class="text-xs text-gray-500">{{ user.phone }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <Link
                            :href="route('admin.users.edit', user.id)"
                            class="rounded-lg p-2 text-gray-400 hover:bg-amber-50 hover:text-amber-600"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </Link>
                        <button @click="confirmDelete(user)" class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div v-if="users.data.length === 0" class="rounded-xl bg-white p-8 text-center border border-gray-200">
                <p class="text-sm text-gray-500">No se encontraron usuarios</p>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            <Pagination :links="users.links" />
        </div>

        <!-- Delete Confirmation Modal -->
        <ConfirmModal :show="showDeleteModal" title="Eliminar Usuario" @close="showDeleteModal = false">
            <p class="text-sm text-gray-600">
                ¿Estás seguro de que deseas eliminar a
                <strong>{{ userToDelete?.name }}</strong>?
                Esta acción no se puede deshacer.
            </p>
            <template #footer>
                <button
                    @click="showDeleteModal = false"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Cancelar
                </button>
                <button
                    @click="deleteUser"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors"
                >
                    Sí, Eliminar
                </button>
            </template>
        </ConfirmModal>
    </AdminLayout>
</template>
