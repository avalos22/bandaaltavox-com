<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SearchInput from '@/Components/SearchInput.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
    packages: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status ?? '');

let debounce;
watch([search, statusFilter], () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(route('admin.packages.index'), {
            search: search.value || undefined,
            status: statusFilter.value !== '' ? statusFilter.value : undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

const showDeleteModal = ref(false);
const packageToDelete = ref(null);

const confirmDelete = (pkg) => {
    packageToDelete.value = pkg;
    showDeleteModal.value = true;
};

const deletePackage = () => {
    router.delete(route('admin.packages.destroy', packageToDelete.value.id), {
        onFinish: () => {
            showDeleteModal.value = false;
            packageToDelete.value = null;
        },
    });
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 0 }).format(price);
};
</script>

<template>
    <Head title="Paquetes" />

    <AdminLayout title="Paquetes">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <SearchInput v-model="search" placeholder="Buscar paquete..." class="w-full sm:w-64" />
                <select
                    v-model="statusFilter"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-amber-400 focus:ring-amber-400"
                >
                    <option value="">Todos</option>
                    <option value="1">Activos</option>
                    <option value="0">Inactivos</option>
                </select>
            </div>

            <div class="flex gap-2">
                <Link
                    :href="route('admin.addons.index')"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Servicios Adicionales
                </Link>
                <Link
                    :href="route('admin.packages.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nuevo Paquete
                </Link>
            </div>
        </div>

        <!-- Package Cards Grid -->
        <div v-if="packages.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="pkg in packages"
                :key="pkg.id"
                class="group relative rounded-xl border border-gray-200 bg-white shadow-sm transition-all hover:shadow-md overflow-hidden"
            >
                <!-- Image or placeholder -->
                <div class="relative h-40 bg-gradient-to-br from-slate-800 to-slate-700 flex items-center justify-center">
                    <img
                        v-if="pkg.image"
                        :src="`/storage/${pkg.image}`"
                        :alt="pkg.name"
                        class="h-full w-full object-cover"
                    />
                    <div v-else class="text-center">
                        <svg class="mx-auto h-10 w-10 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a2.25 2.25 0 0 0 1.632-2.163Zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 0 1-.99-3.467l2.31-.66A2.25 2.25 0 0 0 9 15.553Z" />
                        </svg>
                        <p class="mt-1 text-xs text-slate-400">Sin imagen</p>
                    </div>

                    <!-- Featured badge -->
                    <span
                        v-if="pkg.is_featured"
                        class="absolute top-2 left-2 rounded-full bg-amber-500 px-2.5 py-0.5 text-[10px] font-bold text-white uppercase tracking-wide"
                    >
                        Destacado
                    </span>

                    <!-- Status badge -->
                    <span
                        :class="[
                            'absolute top-2 right-2 rounded-full px-2 py-0.5 text-[10px] font-semibold',
                            pkg.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700',
                        ]"
                    >
                        {{ pkg.is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="text-lg font-bold text-gray-800">{{ pkg.name }}</h3>
                        <span class="shrink-0 text-lg font-bold text-amber-600">{{ formatPrice(pkg.price) }}</span>
                    </div>

                    <div class="mt-1 flex items-center gap-3 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ pkg.duration_hours }} horas
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ pkg.includes_count }} incluidos
                        </span>
                    </div>

                    <p v-if="pkg.description" class="mt-2 text-sm text-gray-500 line-clamp-2">{{ pkg.description }}</p>

                    <!-- Event type tags -->
                    <div v-if="pkg.event_types?.length" class="mt-3 flex flex-wrap gap-1">
                        <span
                            v-for="et in pkg.event_types"
                            :key="et.id"
                            class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600"
                        >
                            {{ et.name }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 flex items-center justify-end gap-2 border-t border-gray-100 pt-3">
                        <Link
                            :href="route('admin.packages.edit', pkg.id)"
                            class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Editar
                        </Link>
                        <button
                            @click="confirmDelete(pkg)"
                            class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="rounded-xl border border-gray-200 bg-white p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a2.25 2.25 0 0 0 1.632-2.163Zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 0 1-.99-3.467l2.31-.66A2.25 2.25 0 0 0 9 15.553Z" />
            </svg>
            <h3 class="mt-3 text-sm font-semibold text-gray-700">No hay paquetes</h3>
            <p class="mt-1 text-sm text-gray-500">Crea tu primer paquete para empezar</p>
            <Link
                :href="route('admin.packages.create')"
                class="mt-4 inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600"
            >
                Crear Paquete
            </Link>
        </div>

        <!-- Delete confirmation -->
        <ConfirmModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>Eliminar paquete</template>
            <template #default>
                <p class="text-sm text-gray-600">
                    ¿Estás seguro de eliminar el paquete <strong>{{ packageToDelete?.name }}</strong>?
                    Esta acción no se puede deshacer.
                </p>
            </template>
            <template #footer>
                <button
                    @click="showDeleteModal = false"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Cancelar
                </button>
                <button
                    @click="deletePackage"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                >
                    Sí, eliminar
                </button>
            </template>
        </ConfirmModal>
    </AdminLayout>
</template>
