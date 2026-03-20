<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

const props = defineProps({
    quotation: Object,
    statuses: Object,
});

const formatMoney = (val) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val ?? 0);
const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-MX', { day: 'numeric', month: 'long', year: 'numeric' }) : '—';

const statusBadge = {
    draft: 'bg-gray-100 text-gray-700',
    sent: 'bg-blue-100 text-blue-700',
    accepted: 'bg-emerald-100 text-emerald-700',
    rejected: 'bg-red-100 text-red-700',
    expired: 'bg-yellow-100 text-yellow-700',
    converted: 'bg-purple-100 text-purple-700',
};

const itemTypeLabels = {
    package: 'Paquete',
    addon: 'Extra',
    custom: 'Personalizado',
};
</script>

<template>
    <Head :title="`Cotización ${quotation.quotation_number}`" />

    <ClientLayout :title="`Cotización ${quotation.quotation_number}`">
        <!-- Back link -->
        <div class="mb-4">
            <Link :href="route('client.quotations')" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
                Volver a cotizaciones
            </Link>
        </div>

        <div class="space-y-6">
            <!-- Header card -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 font-mono">{{ quotation.quotation_number }}</h3>
                        <p class="text-sm text-gray-500 mt-1">Creada el {{ formatDate(quotation.created_at) }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="statusBadge[quotation.status]">
                        {{ statuses[quotation.status] }}
                    </span>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div v-if="quotation.event_type" class="rounded-lg bg-gray-50 p-3">
                        <p class="text-xs text-gray-500 mb-1">Tipo de evento</p>
                        <p class="text-sm font-medium text-gray-800">{{ quotation.event_type.name }}</p>
                    </div>
                    <div v-if="quotation.event_date" class="rounded-lg bg-gray-50 p-3">
                        <p class="text-xs text-gray-500 mb-1">Fecha del evento</p>
                        <p class="text-sm font-medium text-gray-800">{{ formatDate(quotation.event_date) }}</p>
                    </div>
                    <div v-if="quotation.event_venue" class="rounded-lg bg-gray-50 p-3">
                        <p class="text-xs text-gray-500 mb-1">Lugar</p>
                        <p class="text-sm font-medium text-gray-800">{{ quotation.event_venue }}</p>
                    </div>
                    <div v-if="quotation.hours_contracted" class="rounded-lg bg-gray-50 p-3">
                        <p class="text-xs text-gray-500 mb-1">Horas contratadas</p>
                        <p class="text-sm font-medium text-gray-800">{{ quotation.hours_contracted }}h</p>
                    </div>
                    <div v-if="quotation.guest_count" class="rounded-lg bg-gray-50 p-3">
                        <p class="text-xs text-gray-500 mb-1">Invitados</p>
                        <p class="text-sm font-medium text-gray-800">{{ quotation.guest_count }}</p>
                    </div>
                    <div v-if="quotation.event_city" class="rounded-lg bg-gray-50 p-3">
                        <p class="text-xs text-gray-500 mb-1">Ciudad</p>
                        <p class="text-sm font-medium text-gray-800">{{ quotation.event_city }}</p>
                    </div>
                    <div v-if="quotation.expires_at" class="rounded-lg bg-gray-50 p-3">
                        <p class="text-xs text-gray-500 mb-1">Vigencia</p>
                        <p class="text-sm font-medium text-gray-800">{{ formatDate(quotation.expires_at) }}</p>
                    </div>
                </div>
            </div>

            <!-- Items table -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-800">Detalle de servicios</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                <th class="px-6 py-3">Servicio</th>
                                <th class="px-6 py-3 text-center">Tipo</th>
                                <th class="px-6 py-3 text-center">Cant.</th>
                                <th class="px-6 py-3 text-right">Precio</th>
                                <th class="px-6 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="item in quotation.items" :key="item.id" class="text-sm">
                                <td class="px-6 py-3 text-gray-800">{{ item.description }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-medium"
                                        :class="item.type === 'package' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700'">
                                        {{ itemTypeLabels[item.type] }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center text-gray-600">{{ item.quantity }}</td>
                                <td class="px-6 py-3 text-right text-gray-600">{{ formatMoney(item.unit_price) }}</td>
                                <td class="px-6 py-3 text-right font-medium text-gray-800">{{ formatMoney(item.total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="border-t border-gray-200 px-6 py-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="text-gray-700">{{ formatMoney(quotation.subtotal) }}</span>
                    </div>
                    <div v-if="parseFloat(quotation.discount) > 0" class="flex justify-between text-sm">
                        <span class="text-gray-500">Descuento</span>
                        <span class="text-red-600">-{{ formatMoney(quotation.discount) }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-100">
                        <span class="text-gray-800">Total</span>
                        <span class="text-amber-600">{{ formatMoney(quotation.total) }}</span>
                    </div>
                </div>
            </div>

            <!-- Observations -->
            <div v-if="quotation.observations" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h4 class="text-sm font-semibold text-gray-800 mb-2">Observaciones</h4>
                <p class="text-sm text-gray-600 whitespace-pre-line">{{ quotation.observations }}</p>
            </div>
        </div>
    </ClientLayout>
</template>
