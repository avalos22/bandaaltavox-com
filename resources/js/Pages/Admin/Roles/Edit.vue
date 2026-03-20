<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    editRole: Object,
    permissionGroups: Object,
});

const form = useForm({
    name: props.editRole.name,
    permissions: [...props.editRole.permissions],
});

const groupLabels = {
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

const actionLabels = {
    view: 'Ver',
    create: 'Crear',
    edit: 'Editar',
    delete: 'Eliminar',
    manage: 'Administrar',
    admin: 'Admin',
    client: 'Cliente',
};

const getActionLabel = (permission) => {
    const action = permission.split('.')[1];
    return actionLabels[action] || action;
};

const toggleGroup = (groupPermissions) => {
    const allSelected = groupPermissions.every(p => form.permissions.includes(p));
    if (allSelected) {
        form.permissions = form.permissions.filter(p => !groupPermissions.includes(p));
    } else {
        const newPerms = groupPermissions.filter(p => !form.permissions.includes(p));
        form.permissions.push(...newPerms);
    }
};

const isGroupFullySelected = (groupPermissions) => {
    return groupPermissions.every(p => form.permissions.includes(p));
};

const selectAll = () => {
    const allPerms = Object.values(props.permissionGroups).flat();
    form.permissions = [...allPerms];
};

const deselectAll = () => {
    form.permissions = [];
};

const isSystemRole = ['Super Admin', 'Cliente'].includes(props.editRole.name);

const submit = () => {
    form.put(route('admin.roles.update', props.editRole.id));
};
</script>

<template>
    <Head :title="`Editar: ${editRole.name}`" />

    <AdminLayout title="Editar Rol">
        <!-- Breadcrumb -->
        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
            <Link :href="route('admin.roles.index')" class="hover:text-amber-600 transition-colors">Roles</Link>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span class="text-gray-800 font-medium">{{ editRole.name }}</span>
        </nav>

        <div class="mx-auto max-w-3xl">
            <form @submit.prevent="submit" class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <!-- System role notice -->
                <div v-if="isSystemRole" class="rounded-lg border border-amber-200 bg-amber-50 p-3">
                    <p class="text-sm text-amber-800">
                        <strong>Rol del sistema:</strong> Puedes modificar los permisos pero el nombre no se puede cambiar.
                    </p>
                </div>

                <!-- Name -->
                <div>
                    <InputLabel for="name" value="Nombre del rol" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        :disabled="isSystemRole"
                        :class="{ 'bg-gray-50 text-gray-500': isSystemRole }"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <!-- Permissions -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <InputLabel value="Permisos" />
                        <div class="flex gap-2">
                            <button type="button" @click="selectAll" class="text-xs text-amber-600 hover:text-amber-800">
                                Seleccionar todos
                            </button>
                            <span class="text-gray-300">|</span>
                            <button type="button" @click="deselectAll" class="text-xs text-gray-500 hover:text-gray-700">
                                Quitar todos
                            </button>
                        </div>
                    </div>

                    <InputError class="mb-2" :message="form.errors.permissions" />

                    <div class="space-y-3">
                        <div
                            v-for="(permissions, group) in permissionGroups"
                            :key="group"
                            class="rounded-lg border border-gray-100 p-4"
                        >
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    :checked="isGroupFullySelected(permissions)"
                                    @change="toggleGroup(permissions)"
                                    class="h-4 w-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400"
                                />
                                <span class="text-sm font-semibold text-gray-700">
                                    {{ groupLabels[group] || group }}
                                </span>
                                <span class="text-xs text-gray-400">({{ permissions.length }})</span>
                            </label>

                            <div class="mt-2 ml-7 flex flex-wrap gap-3">
                                <label
                                    v-for="perm in permissions"
                                    :key="perm"
                                    class="flex items-center gap-2 cursor-pointer"
                                >
                                    <input
                                        type="checkbox"
                                        :value="perm"
                                        v-model="form.permissions"
                                        class="h-3.5 w-3.5 rounded border-gray-300 text-amber-500 focus:ring-amber-400"
                                    />
                                    <span class="text-xs text-gray-600">{{ getActionLabel(perm) }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                    <Link
                        :href="route('admin.roles.index')"
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
        </div>
    </AdminLayout>
</template>
