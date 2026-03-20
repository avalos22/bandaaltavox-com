<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

const props = defineProps({
    upcomingEvents: Array,
    contracts: Array,
    recentPayments: Array,
    stats: Object,
    nextEvent: Object,
});

const formatMoney = (val) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val ?? 0);
const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const statusLabels = {
    confirmed: 'Confirmado',
    tentative: 'Tentativo',
    in_progress: 'En curso',
    completed: 'Completado',
    cancelled: 'Cancelado',
};

const statusColors = {
    confirmed: 'bg-amber-100 text-amber-700',
    tentative: 'bg-gray-100 text-gray-600',
    in_progress: 'bg-blue-100 text-blue-700',
    completed: 'bg-emerald-100 text-emerald-700',
    cancelled: 'bg-red-100 text-red-700',
};
</script>

<template>
    <Head title="Mi Portal" />

    <ClientLayout title="Mi Portal">
        <!-- Welcome + next event banner -->
        <div v-if="nextEvent" class="mb-6 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 p-6 text-white shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold">¡Tu próximo evento!</h2>
                    <p class="mt-1 text-amber-100 text-sm">{{ nextEvent.title }}</p>
                    <p class="mt-0.5 text-white/80 text-sm">
                        {{ formatDate(nextEvent.event_date) }}
                        <span v-if="nextEvent.time_start"> · {{ nextEvent.time_start }}</span>
                        <span v-if="nextEvent.venue"> · {{ nextEvent.venue }}</span>
                    </p>
                </div>
                <Link :href="route('client.events')" class="inline-flex items-center gap-1.5 rounded-lg bg-white/20 px-4 py-2 text-sm font-medium text-white backdrop-blur hover:bg-white/30 transition-colors">
                    Ver mis eventos
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </Link>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase">Mis Contratos</p>
                <p class="mt-2 text-2xl font-bold text-gray-800">{{ stats.totalContracts }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase">Total Contratado</p>
                <p class="mt-2 text-2xl font-bold text-gray-800">{{ formatMoney(stats.totalOwed) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase">Total Pagado</p>
                <p class="mt-2 text-2xl font-bold text-emerald-600">{{ formatMoney(stats.totalPaid) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-medium text-gray-500 uppercase">Saldo Pendiente</p>
                <p class="mt-2 text-2xl font-bold" :class="stats.balance > 0 ? 'text-red-600' : 'text-emerald-600'">{{ formatMoney(stats.balance) }}</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Upcoming events -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="text-base font-bold text-gray-800">Próximos Eventos</h3>
                    <Link :href="route('client.events')" class="text-xs font-medium text-amber-600 hover:underline">Ver todos</Link>
                </div>
                <div v-if="upcomingEvents.length" class="divide-y divide-gray-100">
                    <div v-for="event in upcomingEvents" :key="event.id" class="flex items-start gap-4 px-6 py-4">
                        <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <span class="text-xs font-medium leading-none">{{ new Date(event.event_date).toLocaleDateString('es-MX', { month: 'short' }).toUpperCase() }}</span>
                            <span class="text-lg font-bold leading-tight">{{ new Date(event.event_date).getDate() }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ event.title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ event.time_start || '' }}{{ event.time_start && event.venue ? ' · ' : '' }}{{ event.venue || '' }}
                            </p>
                        </div>
                        <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-medium" :class="statusColors[event.status]">
                            {{ statusLabels[event.status] }}
                        </span>
                    </div>
                </div>
                <p v-else class="px-6 py-8 text-center text-sm text-gray-400">Sin eventos próximos</p>
            </div>

            <!-- Recent payments -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="text-base font-bold text-gray-800">Últimos Pagos</h3>
                    <Link :href="route('client.payments')" class="text-xs font-medium text-amber-600 hover:underline">Ver todos</Link>
                </div>
                <div v-if="recentPayments.length" class="divide-y divide-gray-100">
                    <div v-for="pmt in recentPayments" :key="pmt.id" class="flex items-center justify-between px-6 py-3.5">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ formatMoney(pmt.amount) }}</p>
                            <p class="text-xs text-gray-500">{{ formatDate(pmt.payment_date) }} · {{ pmt.contract?.contract_number }}</p>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-medium text-emerald-700 capitalize">{{ pmt.method }}</span>
                    </div>
                </div>
                <p v-else class="px-6 py-8 text-center text-sm text-gray-400">Sin pagos registrados</p>
            </div>
        </div>
    </ClientLayout>
</template>
