<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SearchInput from '@/Components/SearchInput.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    contracts: Object,
    filters: Object,
    statuses: Object,
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

let debounce;
watch([search, statusFilter], () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
        router.get(route('admin.contracts.index'), {
            search: search.value || undefined,
            status: statusFilter.value || undefined,
        }, { preserveState: true, replace: true });
    }, 300);
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 0 }).format(price);
};

const formatDate = (date) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' });
};

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    active: 'bg-emerald-100 text-emerald-700',
    completed: 'bg-blue-100 text-blue-700',
    cancelled: 'bg-red-100 text-red-700',
};
</script>

<template>
    <Head title="Contratos" />

    <AdminLayout title="Contratos">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <SearchInput v-model="search" placeholder="Buscar contrato..." class="w-full sm:w-64" />
                <select
                    v-model="statusFilter"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-amber-400 focus:ring-amber-400"
                >
                    <option value="">Todos los estados</option>
                    <option v-for="(label, key) in statuses" :key="key" :value="key">{{ label }}</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div v-if="contracts.data.length" class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Contrato</th>
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
                        v-for="contract in contracts.data"
                        :key="contract.id"
                        class="hover:bg-gray-50/50 transition-colors"
                    >
                        <td class="px-6 py-4">
                            <Link
                                :href="route('admin.contracts.show', contract.id)"
                                class="font-mono text-sm font-bold text-purple-600 hover:text-purple-700"
                            >
                                {{ contract.contract_number }}
                            </Link>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ contract.client_name }}</p>
                            <p v-if="contract.client_phone" class="text-xs text-gray-500">{{ contract.client_phone }}</p>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <p class="text-gray-700">{{ contract.event_type_label }}</p>
                            <p v-if="contract.event_date" class="text-xs text-gray-500">{{ formatDate(contract.event_date) }}</p>
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-800">
                            {{ formatPrice(contract.total_amount) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span :class="['inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium', statusColors[contract.status]]">
                                {{ statuses[contract.status] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 hidden lg:table-cell text-xs text-gray-500">
                            {{ formatDate(contract.contract_date) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <Link
                                :href="route('admin.contracts.show', contract.id)"
                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                Ver
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="px-6 pb-4">
                <Pagination :links="contracts.links" />
            </div>
        </div>

        <!-- Empty state -->
        <div v-else class="rounded-xl border border-gray-200 bg-white p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <h3 class="mt-3 text-sm font-semibold text-gray-700">No hay contratos</h3>
            <p class="mt-1 text-sm text-gray-500">Los contratos se generan al cerrar una venta desde una cotización aceptada</p>
        </div>
    </AdminLayout>
</template>
