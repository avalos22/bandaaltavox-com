<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    businessName: String,
    stats: Object,
    upcomingEvents: Array,
    recentPayments: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const roles = computed(() => page.props.auth.roles);
const permissions = computed(() => page.props.auth.permissions);

function formatCurrency(val) {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 0 }).format(val);
}

function formatDate(d) {
    return new Date(d).toLocaleDateString('es-MX', { day: 'numeric', month: 'short' });
}

const statusColors = {
    confirmed: 'bg-emerald-100 text-emerald-700',
    tentative: 'bg-amber-100 text-amber-700',
    in_progress: 'bg-blue-100 text-blue-700',
    completed: 'bg-slate-100 text-slate-600',
    cancelled: 'bg-red-100 text-red-700',
};

const statusLabels = {
    confirmed: 'Confirmado',
    tentative: 'Tentativo',
    in_progress: 'En curso',
    completed: 'Completado',
    cancelled: 'Cancelado',
};

const methodLabels = {
    cash: 'Efectivo',
    transfer: 'Transferencia',
    card: 'Tarjeta',
    deposit: 'Depósito',
    other: 'Otro',
};
</script>

<template>
    <Head title="Dashboard" />

    <AdminLayout title="Dashboard">
        <!-- Welcome card -->
        <div class="mb-6 rounded-xl bg-gradient-to-r from-slate-900 to-slate-800 p-6 text-white shadow-lg">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-amber-500/20 text-amber-400">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a2.25 2.25 0 0 0 1.632-2.163Zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 0 1-.99-3.467l2.31-.66A2.25 2.25 0 0 0 9 15.553Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold">¡Bienvenido, {{ user?.name }}!</h2>
                    <p class="text-slate-300 text-sm">
                        {{ businessName }} — Panel de Administración
                    </p>
                    <span class="mt-1 inline-block rounded-full bg-amber-500/20 px-2.5 py-0.5 text-xs text-amber-300 font-medium">
                        {{ roles?.[0] ?? 'Usuario' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Stats grid -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ stats.eventsThisMonth }}</p>
                        <p class="text-xs text-gray-500">Eventos este mes</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ stats.pendingQuotations }}</p>
                        <p class="text-xs text-gray-500">Cotizaciones pendientes</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ formatCurrency(stats.collectedThisMonth) }}</p>
                        <p class="text-xs text-gray-500">Cobrado este mes</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm border border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-800">{{ formatCurrency(stats.pendingPayments) }}</p>
                        <p class="text-xs text-gray-500">Saldo pendiente</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick actions -->
        <div class="mt-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Acciones rápidas</h3>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <Link
                    v-if="permissions.includes('quotations.create')"
                    :href="route('admin.quotations.create')"
                    class="flex flex-col items-center gap-2 rounded-xl border-2 border-dashed border-gray-200 bg-white p-4 text-gray-500 hover:border-amber-400 hover:text-amber-600 hover:bg-amber-50 transition-colors"
                >
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <span class="text-xs font-medium">Nueva Cotización</span>
                </Link>
                <Link
                    v-if="permissions.includes('events.view')"
                    :href="route('admin.events.index')"
                    class="flex flex-col items-center gap-2 rounded-xl border-2 border-dashed border-gray-200 bg-white p-4 text-gray-500 hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                >
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    <span class="text-xs font-medium">Ver Agenda</span>
                </Link>
                <Link
                    v-if="permissions.includes('payments.view')"
                    :href="route('admin.payments.index')"
                    class="flex flex-col items-center gap-2 rounded-xl border-2 border-dashed border-gray-200 bg-white p-4 text-gray-500 hover:border-emerald-400 hover:text-emerald-600 hover:bg-emerald-50 transition-colors"
                >
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span class="text-xs font-medium">Cobranza</span>
                </Link>
                <Link
                    v-if="permissions.includes('users.create')"
                    :href="route('admin.users.create')"
                    class="flex flex-col items-center gap-2 rounded-xl border-2 border-dashed border-gray-200 bg-white p-4 text-gray-500 hover:border-purple-400 hover:text-purple-600 hover:bg-purple-50 transition-colors"
                >
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <span class="text-xs font-medium">Nuevo Usuario</span>
                </Link>
            </div>
        </div>

        <!-- Two-column: Upcoming Events + Recent Payments -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Upcoming events -->
            <div class="rounded-xl bg-white border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Próximos Eventos</h3>
                    <Link :href="route('admin.events.index')" class="text-xs text-amber-600 hover:text-amber-700 font-medium">Ver agenda →</Link>
                </div>
                <div v-if="upcomingEvents.length" class="divide-y divide-gray-50">
                    <Link
                        v-for="event in upcomingEvents"
                        :key="event.id"
                        :href="route('admin.events.show', event.id)"
                        class="flex items-center gap-4 px-5 py-3.5 hover:bg-gray-50 transition-colors"
                    >
                        <div class="flex flex-col items-center justify-center w-12 h-12 rounded-lg bg-slate-100 text-center shrink-0">
                            <span class="text-xs font-medium text-slate-500 uppercase leading-none">{{ new Date(event.event_date).toLocaleDateString('es-MX', { month: 'short' }) }}</span>
                            <span class="text-lg font-bold text-slate-800 leading-none mt-0.5">{{ new Date(event.event_date).getDate() }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ event.title }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ event.venue || 'Sin lugar definido' }}</p>
                        </div>
                        <span :class="['text-[10px] font-semibold px-2 py-1 rounded-full', statusColors[event.status] || 'bg-gray-100 text-gray-600']">
                            {{ statusLabels[event.status] || event.status }}
                        </span>
                    </Link>
                </div>
                <div v-else class="px-5 py-8 text-center text-sm text-gray-400">
                    No hay eventos próximos
                </div>
            </div>

            <!-- Recent payments -->
            <div class="rounded-xl bg-white border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Últimos Pagos Registrados</h3>
                    <Link :href="route('admin.payments.index')" class="text-xs text-amber-600 hover:text-amber-700 font-medium">Ver cobranza →</Link>
                </div>
                <div v-if="recentPayments.length" class="divide-y divide-gray-50">
                    <div
                        v-for="payment in recentPayments"
                        :key="payment.id"
                        class="flex items-center gap-4 px-5 py-3.5"
                    >
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-emerald-700">+{{ formatCurrency(payment.amount) }}</p>
                            <p class="text-xs text-gray-500">
                                {{ payment.contract?.contract_number ?? 'Contrato' }} · {{ formatDate(payment.payment_date) }}
                            </p>
                        </div>
                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">
                            {{ methodLabels[payment.method] || payment.method }}
                        </span>
                    </div>
                </div>
                <div v-else class="px-5 py-8 text-center text-sm text-gray-400">
                    No hay pagos registrados
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
