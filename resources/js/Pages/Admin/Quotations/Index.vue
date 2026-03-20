<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SearchInput from '@/Components/SearchInput.vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
    quotations: Object,
    filters: Object,
    statuses: Object,
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

let debounce;
watch([search, statusFilter], () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(route('admin.quotations.index'), {
            search: search.value || undefined,
            status: statusFilter.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

const showDeleteModal = ref(false);
const quotationToDelete = ref(null);

const confirmDelete = (q) => {
    quotationToDelete.value = q;
    showDeleteModal.value = true;
};

const deleteQuotation = () => {
    router.delete(route('admin.quotations.destroy', quotationToDelete.value.id), {
        onFinish: () => {
            showDeleteModal.value = false;
            quotationToDelete.value = null;
        },
    });
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 0 }).format(price);
};

const formatDate = (date) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' });
};

const statusColors = {
    draft: 'bg-gray-100 text-gray-700',
    sent: 'bg-blue-100 text-blue-700',
    accepted: 'bg-emerald-100 text-emerald-700',
    rejected: 'bg-red-100 text-red-700',
    expired: 'bg-orange-100 text-orange-700',
    converted: 'bg-purple-100 text-purple-700',
};
</script>

<template>
    <Head title="Cotizaciones" />

    <AdminLayout title="Cotizaciones">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <SearchInput v-model="search" placeholder="Buscar cotización..." class="w-full sm:w-64" />
                <select
                    v-model="statusFilter"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-amber-400 focus:ring-amber-400"
                >
                    <option value="">Todos los estados</option>
                    <option v-for="(label, key) in statuses" :key="key" :value="key">{{ label }}</option>
                </select>
            </div>

            <Link
                :href="route('admin.quotations.create')"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nueva Cotización
            </Link>
        </div>

        <!-- Table -->
        <div v-if="quotations.data.length" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Cotización</th>
                        <th class="px-6 py-3 text-left">Cliente</th>
                        <th class="px-6 py-3 text-left hidden md:table-cell">Evento</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-center">Estado</th>
                        <th class="px-6 py-3 text-left hidden lg:table-cell">Fecha</th>
                        <th class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr
                        v-for="q in quotations.data"
                        :key="q.id"
                        class="hover:bg-gray-50/50 transition-colors"
                    >
                        <td class="px-6 py-4">
                            <Link
                                :href="route('admin.quotations.show', q.id)"
                                class="font-mono text-sm font-bold text-amber-600 hover:text-amber-700"
                            >
                                {{ q.quotation_number }}
                            </Link>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ q.client_name }}</p>
                            <p v-if="q.client_phone" class="text-xs text-gray-500">{{ q.client_phone }}</p>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <p class="text-gray-700">{{ q.event_type?.name || '—' }}</p>
                            <p v-if="q.event_date" class="text-xs text-gray-500">{{ formatDate(q.event_date) }}</p>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-800">
                            {{ formatPrice(q.total) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span :class="['inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium', statusColors[q.status]]">
                                {{ statuses[q.status] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell text-xs text-gray-500">
                            {{ formatDate(q.created_at) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="route('admin.quotations.show', q.id)"
                                    class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                                >
                                    Ver
                                </Link>
                                <button
                                    v-if="q.status === 'draft'"
                                    @click="confirmDelete(q)"
                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="px-6 pb-4">
                <Pagination :links="quotations.links" />
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="rounded-xl border border-gray-200 bg-white p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <h3 class="mt-3 text-sm font-semibold text-gray-700">No hay cotizaciones</h3>
            <p class="mt-1 text-sm text-gray-500">Crea tu primera cotización con el wizard</p>
            <Link
                :href="route('admin.quotations.create')"
                class="mt-4 inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600"
            >
                Nueva Cotización
            </Link>
        </div>

        <!-- Delete confirmation -->
        <ConfirmModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>Eliminar cotización</template>
            <template #default>
                <p class="text-sm text-gray-600">
                    ¿Estás seguro de eliminar la cotización <strong>{{ quotationToDelete?.quotation_number }}</strong>?
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
                    @click="deleteQuotation"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                >
                    Sí, eliminar
                </button>
            </template>
        </ConfirmModal>
    </AdminLayout>
</template>
