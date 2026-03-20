<script setup>
import { Head } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    events: Object,
    statuses: Object,
    statusColors: Object,
});

const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-MX', { weekday: 'short', day: 'numeric', month: 'long', year: 'numeric' }) : '—';

const badgeColors = {
    confirmed: 'bg-amber-100 text-amber-700',
    tentative: 'bg-gray-100 text-gray-600',
    in_progress: 'bg-blue-100 text-blue-700',
    completed: 'bg-emerald-100 text-emerald-700',
    cancelled: 'bg-red-100 text-red-700',
};
</script>

<template>
    <Head title="Mis Eventos" />

    <ClientLayout title="Mis Eventos">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div v-if="events.data.length" class="divide-y divide-gray-100">
                <div v-for="event in events.data" :key="event.id" class="flex items-start gap-4 px-6 py-5">
                    <!-- Date block -->
                    <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl border border-gray-200 bg-gray-50">
                        <span class="text-[10px] font-semibold uppercase text-gray-500 leading-none">
                            {{ new Date(event.event_date).toLocaleDateString('es-MX', { month: 'short' }) }}
                        </span>
                        <span class="text-xl font-bold text-gray-800 leading-tight">
                            {{ new Date(event.event_date).getDate() }}
                        </span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm font-semibold text-gray-800">{{ event.title }}</h3>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-medium" :class="badgeColors[event.status]">
                                {{ statuses[event.status] }}
                            </span>
                        </div>

                        <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                            <span v-if="event.time_start" class="inline-flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                {{ event.time_start }}{{ event.time_end ? ' - ' + event.time_end : '' }}
                            </span>
                            <span v-if="event.venue" class="inline-flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                                {{ event.venue }}
                            </span>
                            <span v-if="event.hours_contracted" class="inline-flex items-center gap-1">
                                {{ event.hours_contracted }}h contratadas
                            </span>
                            <span v-if="event.event_type_label" class="inline-flex items-center gap-1">
                                {{ event.event_type_label }}
                            </span>
                        </div>

                        <p v-if="event.contract" class="mt-1.5 text-xs text-gray-400">
                            Contrato: {{ event.contract.contract_number }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-else class="px-6 py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v9.75" />
                </svg>
                <p class="mt-3 text-sm text-gray-500">Aún no tienes eventos registrados</p>
            </div>

            <div class="border-t border-gray-100 px-6 py-4">
                <Pagination :links="events.links" />
            </div>
        </div>
    </ClientLayout>
</template>
