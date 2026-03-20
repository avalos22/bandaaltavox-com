<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import SearchInput from '@/Components/SearchInput.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    contracts: Object,
    filters: Object,
    stats: Object,
});

const search = ref(props.filters.search ?? '');
const filter = ref(props.filters.filter ?? '');

let searchTimeout;
watch(search, (val) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('admin.payments.index'), { search: val || undefined, filter: filter.value || undefined }, { preserveState: true, replace: true });
    }, 350);
});

const applyFilter = (f) => {
    filter.value = filter.value === f ? '' : f;
    router.get(route('admin.payments.index'), { search: search.value || undefined, filter: filter.value || undefined }, { preserveState: true, replace: true });
};

const formatMoney = (val) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val ?? 0);
};

const paymentProgress = (contract) => {
    const paid = parseFloat(contract.payments_sum_amount) || 0;
    const total = parseFloat(contract.total_amount) || 1;
    return Math.min(100, Math.round((paid / total) * 100));
};

const progressColor = (pct) => {
    if (pct >= 100) return 'bg-emerald-500';
    if (pct >= 50) return 'bg-amber-500';
    return 'bg-red-500';
};

const filterButtons = [
    { key: 'pending', label: 'Pendientes', color: 'bg-amber-100 text-amber-700 border-amber-200' },
    { key: 'paid', label: 'Pagados', color: 'bg-emerald-100 text-emerald-700 border-emerald-200' },
    { key: 'overdue', label: 'Vencidos', color: 'bg-red-100 text-red-700 border-red-200' },
];
</script>

<template>
    <Head title="Cobranza" />

    <AdminLayout title="Cobranza">
        <!-- Stats cards -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase">Contratos</p>
                <p class="mt-2 text-2xl font-bold text-gray-800">{{ stats.totalContracts }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase">Ingresos Esperados</p>
                <p class="mt-2 text-2xl font-bold text-gray-800">{{ formatMoney(stats.totalRevenue) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase">Cobrado</p>
                <p class="mt-2 text-2xl font-bold text-emerald-600">{{ formatMoney(stats.totalCollected) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase">Pendiente</p>
                <p class="mt-2 text-2xl font-bold text-red-600">{{ formatMoney(stats.totalPending) }}</p>
            </div>
        </div>

        <!-- Filters + search -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2">
                    <button
                        v-for="btn in filterButtons"
                        :key="btn.key"
                        @click="applyFilter(btn.key)"
                        :class="[
                            'rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors',
                            filter === btn.key ? btn.color : 'border-gray-200 text-gray-600 hover:bg-gray-50'
                        ]"
                    >
                        {{ btn.label }}
                    </button>
                </div>
                <div class="w-full sm:w-72">
                    <SearchInput v-model="search" placeholder="Buscar contrato o cliente..." />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contrato</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cliente</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cobrado</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase min-w-[180px]">Progreso</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pagos</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="contract in contracts.data" :key="contract.id" class="hover:bg-gray-50/50">
                            <td class="px-4 py-3">
                                <span class="text-xs font-mono font-medium text-gray-800">{{ contract.contract_number }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ contract.client_name }}</p>
                                <p v-if="contract.event" class="text-xs text-gray-500">{{ contract.event.venue }}</p>
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-800">{{ formatMoney(contract.total_amount) }}</td>
                            <td class="px-4 py-3 font-medium text-emerald-600">{{ formatMoney(contract.payments_sum_amount) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 rounded-full bg-gray-200 overflow-hidden">
                                        <div
                                            class="h-full rounded-full transition-all"
                                            :class="progressColor(paymentProgress(contract))"
                                            :style="{ width: paymentProgress(contract) + '%' }"
                                        />
                                    </div>
                                    <span class="text-xs font-medium text-gray-600 w-8 text-right">{{ paymentProgress(contract) }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ contract.payments_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link
                                    :href="route('admin.payments.show', contract.id)"
                                    class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Ver pagos
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty state -->
            <div v-if="!contracts.data.length" class="px-6 py-12 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>
                <p class="mt-3 text-sm text-gray-500">No hay contratos que mostrar</p>
            </div>

            <!-- Pagination -->
            <div class="border-t border-gray-100 px-6 py-4">
                <Pagination :links="contracts.links" />
            </div>
        </div>
    </AdminLayout>
</template>
