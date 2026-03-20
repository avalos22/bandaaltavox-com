<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    quotations: Object,
    statuses: Object,
});

const formatMoney = (val) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val ?? 0);
const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' }) : '—';

const statusBadge = {
    draft: 'bg-gray-100 text-gray-700',
    sent: 'bg-blue-100 text-blue-700',
    accepted: 'bg-emerald-100 text-emerald-700',
    rejected: 'bg-red-100 text-red-700',
    expired: 'bg-yellow-100 text-yellow-700',
    converted: 'bg-purple-100 text-purple-700',
};
</script>

<template>
    <Head title="Mis Cotizaciones" />

    <ClientLayout title="Mis Cotizaciones">
        <template #actions>
            <Link
                :href="route('client.quotations.create')"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Solicitar Cotización
            </Link>
        </template>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div v-if="quotations.data.length" class="divide-y divide-gray-100">
                <Link
                    v-for="q in quotations.data"
                    :key="q.id"
                    :href="route('client.quotations.show', q.id)"
                    class="flex items-center gap-4 px-6 py-5 hover:bg-gray-50/50 transition-colors"
                >
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm font-semibold text-gray-800 font-mono">{{ q.quotation_number }}</h3>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-medium" :class="statusBadge[q.status]">
                                {{ statuses[q.status] }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            <span v-if="q.event_type">{{ q.event_type.name }} · </span>
                            {{ formatDate(q.created_at) }}
                            <span v-if="q.event_date"> · Evento: {{ formatDate(q.event_date) }}</span>
                        </p>
                    </div>

                    <div class="shrink-0 text-right">
                        <p class="text-sm font-bold text-gray-800">{{ formatMoney(q.total) }}</p>
                        <p v-if="q.expires_at" class="text-xs text-gray-400">Vence: {{ formatDate(q.expires_at) }}</p>
                    </div>

                    <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </Link>
            </div>

            <div v-else class="px-6 py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                </svg>
                <p class="mt-3 text-sm text-gray-500">No tienes cotizaciones aún</p>
                <Link
                    :href="route('client.quotations.create')"
                    class="mt-4 inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600 transition-colors"
                >
                    Solicitar Cotización
                </Link>
            </div>

            <div v-if="quotations.data.length" class="border-t border-gray-100 px-6 py-4">
                <Pagination :links="quotations.links" />
            </div>
        </div>
    </ClientLayout>
</template>
