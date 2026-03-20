<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    contracts: Object,
});

const formatMoney = (val) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val ?? 0);
const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const statusLabels = {
    pending: 'Pendiente',
    active: 'Activo',
    completed: 'Completado',
    cancelled: 'Cancelado',
};

const statusBadge = {
    pending: 'bg-yellow-100 text-yellow-700',
    active: 'bg-emerald-100 text-emerald-700',
    completed: 'bg-blue-100 text-blue-700',
    cancelled: 'bg-red-100 text-red-700',
};

const paymentProgress = (contract) => {
    const paid = parseFloat(contract.payments_sum_amount) || 0;
    const total = parseFloat(contract.total_amount) || 1;
    return Math.min(100, Math.round((paid / total) * 100));
};
</script>

<template>
    <Head title="Mis Contratos" />

    <ClientLayout title="Mis Contratos">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div v-if="contracts.data.length" class="divide-y divide-gray-100">
                <Link
                    v-for="contract in contracts.data"
                    :key="contract.id"
                    :href="route('client.contracts.show', contract.id)"
                    class="flex items-center gap-4 px-6 py-5 hover:bg-gray-50/50 transition-colors"
                >
                    <!-- Contract icon -->
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm font-semibold text-gray-800 font-mono">{{ contract.contract_number }}</h3>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-medium" :class="statusBadge[contract.status]">
                                {{ statusLabels[contract.status] }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Fecha: {{ formatDate(contract.contract_date) }}
                            <span v-if="contract.event_date"> · Evento: {{ formatDate(contract.event_date) }}</span>
                        </p>
                        <!-- Progress bar -->
                        <div class="mt-2 flex items-center gap-3">
                            <div class="flex-1 h-1.5 rounded-full bg-gray-200 overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all"
                                    :class="paymentProgress(contract) >= 100 ? 'bg-emerald-500' : 'bg-amber-500'"
                                    :style="{ width: paymentProgress(contract) + '%' }"
                                />
                            </div>
                            <span class="text-[10px] font-medium text-gray-500">{{ paymentProgress(contract) }}% pagado</span>
                        </div>
                    </div>

                    <div class="shrink-0 text-right">
                        <p class="text-sm font-bold text-gray-800">{{ formatMoney(contract.total_amount) }}</p>
                        <p class="text-xs text-emerald-600">Pagado: {{ formatMoney(contract.payments_sum_amount) }}</p>
                    </div>

                    <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </Link>
            </div>

            <div v-else class="px-6 py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <p class="mt-3 text-sm text-gray-500">Aún no tienes contratos</p>
            </div>

            <div class="border-t border-gray-100 px-6 py-4">
                <Pagination :links="contracts.links" />
            </div>
        </div>
    </ClientLayout>
</template>
