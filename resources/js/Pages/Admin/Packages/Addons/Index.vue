<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SearchInput from '@/Components/SearchInput.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
    addons: Array,
    categories: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const categoryFilter = ref(props.filters?.category || '');

let debounce;
watch([search, categoryFilter], () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(route('admin.addons.index'), {
            search: search.value || undefined,
            category: categoryFilter.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

const showDeleteModal = ref(false);
const addonToDelete = ref(null);

const confirmDelete = (addon) => {
    addonToDelete.value = addon;
    showDeleteModal.value = true;
};

const deleteAddon = () => {
    router.delete(route('admin.addons.destroy', addonToDelete.value.id), {
        onFinish: () => {
            showDeleteModal.value = false;
            addonToDelete.value = null;
        },
    });
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 0 }).format(price);
};

const categoryColors = {
    audio:                  'bg-blue-100 text-blue-700',
    pantallas_video:        'bg-purple-100 text-purple-700',
    iluminacion:            'bg-yellow-100 text-yellow-700',
    efectos_especiales:     'bg-orange-100 text-orange-700',
    mobiliario:             'bg-teal-100 text-teal-700',
    entretenimiento:        'bg-pink-100 text-pink-700',
    produccion_estructuras: 'bg-indigo-100 text-indigo-700',
    produccion_logistica:   'bg-cyan-100 text-cyan-700',
    video:                  'bg-rose-100 text-rose-700',
};
</script>

<template>
    <Head title="Servicios Adicionales" />

    <AdminLayout title="Servicios Adicionales">
        <!-- Breadcrumb -->
        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
            <Link :href="route('admin.packages.index')" class="hover:text-amber-600 transition-colors">Paquetes</Link>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span class="text-gray-800 font-medium">Servicios Adicionales</span>
        </nav>

        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <SearchInput v-model="search" placeholder="Buscar servicio..." class="w-full sm:w-64" />
                <select
                    v-model="categoryFilter"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-amber-400 focus:ring-amber-400"
                >
                    <option value="">Todas las categorías</option>
                    <option v-for="(label, key) in categories" :key="key" :value="key">{{ label }}</option>
                </select>
            </div>

            <Link
                :href="route('admin.addons.create')"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo Servicio
            </Link>
        </div>

        <!-- Table -->
        <div v-if="addons.length" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <!-- Desktop table -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Servicio</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Categoría</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Precio</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Unidad</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="addon in addons" :key="addon.id" class="hover:bg-gray-50/50">
                            <td class="px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ addon.name }}</p>
                                    <p v-if="addon.description" class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ addon.description }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="[categoryColors[addon.category] || 'bg-gray-100 text-gray-700', 'rounded-full px-2.5 py-0.5 text-xs font-medium']">
                                    {{ categories[addon.category] || addon.category }}
                                </span>
                                <p v-if="addon.subcategory" class="mt-0.5 text-[11px] text-gray-400">{{ addon.subcategory }}</p>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                {{ addon.price != null ? formatPrice(addon.price) : 'Cotizar' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ addon.unit }}</td>
                            <td class="px-4 py-3">
                                <span :class="[
                                    'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium',
                                    addon.is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700',
                                ]">
                                    <span :class="['h-1.5 w-1.5 rounded-full', addon.is_active ? 'bg-green-500' : 'bg-red-500']" />
                                    {{ addon.is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="route('admin.addons.edit', addon.id)" class="text-xs font-medium text-amber-600 hover:text-amber-800">Editar</Link>
                                    <button @click="confirmDelete(addon)" class="text-xs font-medium text-red-500 hover:text-red-700">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile cards -->
            <div class="sm:hidden divide-y divide-gray-100">
                <div v-for="addon in addons" :key="addon.id" class="p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-medium text-gray-800">{{ addon.name }}</p>
                            <span :class="[categoryColors[addon.category] || 'bg-gray-100 text-gray-700', 'mt-1 inline-block rounded-full px-2 py-0.5 text-[10px] font-medium']">
                                {{ categories[addon.category] || addon.category }}
                            </span>
                        </div>
                        <p class="text-sm font-bold text-amber-600">
                            {{ addon.price > 0 ? formatPrice(addon.price) : 'Cotizar' }}
                        </p>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-xs text-gray-500">{{ addon.unit }}</span>
                        <div class="flex gap-3">
                            <Link :href="route('admin.addons.edit', addon.id)" class="text-xs font-medium text-amber-600">Editar</Link>
                            <button @click="confirmDelete(addon)" class="text-xs font-medium text-red-500">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="rounded-xl border border-gray-200 bg-white p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <h3 class="mt-3 text-sm font-semibold text-gray-700">No hay servicios adicionales</h3>
            <p class="mt-1 text-sm text-gray-500">Agrega servicios que se puedan sumar a los paquetes</p>
            <Link
                :href="route('admin.addons.create')"
                class="mt-4 inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600"
            >
                Crear Servicio
            </Link>
        </div>

        <!-- Delete modal -->
        <ConfirmModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>Eliminar servicio</template>
            <template #default>
                <p class="text-sm text-gray-600">
                    ¿Eliminar <strong>{{ addonToDelete?.name }}</strong>? Esta acción no se puede deshacer.
                </p>
            </template>
            <template #footer>
                <button @click="showDeleteModal = false" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
                <button @click="deleteAddon" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Sí, eliminar</button>
            </template>
        </ConfirmModal>
    </AdminLayout>
</template>
