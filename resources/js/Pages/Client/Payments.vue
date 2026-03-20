<script setup>
import { Head } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    payments: Object,
    totalPaid: Number,
});

const formatMoney = (val) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val ?? 0);
const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const methodLabels = {
    cash: 'Efectivo',
    transfer: 'Transferencia',
    card: 'Tarjeta',
    deposit: 'Depósito',
    other: 'Otro',
};

const methodIcons = {
    cash: '💵',
    transfer: '🏦',
    card: '💳',
    deposit: '📥',
    other: '📋',
};
</script>

<template>
    <Head title="Mis Pagos" />

    <ClientLayout title="Mis Pagos">
        <!-- Total paid summary -->
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-emerald-600 uppercase">Total Pagado</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-700">{{ formatMoney(totalPaid) }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100">
                    <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Payments list -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div v-if="payments.data.length" class="divide-y divide-gray-100">
                <div v-for="pmt in payments.data" :key="pmt.id" class="flex items-center gap-4 px-6 py-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 text-lg">
                        {{ methodIcons[pmt.method] || '📋' }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-800">{{ formatMoney(pmt.amount) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ formatDate(pmt.payment_date) }}
                            · {{ pmt.contract?.contract_number }}
                            <span v-if="pmt.reference"> · Ref: {{ pmt.reference }}</span>
                        </p>
                    </div>
                    <span class="shrink-0 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700">
                        {{ methodLabels[pmt.method] || pmt.method }}
                    </span>
                </div>
            </div>

            <div v-else class="px-6 py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>
                <p class="mt-3 text-sm text-gray-500">Aún no tienes pagos registrados</p>
            </div>

            <div class="border-t border-gray-100 px-6 py-4">
                <Pagination :links="payments.links" />
            </div>
        </div>
    </ClientLayout>
</template>
