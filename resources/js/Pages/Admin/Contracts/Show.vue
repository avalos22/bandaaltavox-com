<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    contract: Object,
    statuses: Object,
});

const c = computed(() => props.contract);

const formatPrice = (price) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 0 }).format(price);
};

const formatDate = (date) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('es-MX', { day: 'numeric', month: 'long', year: 'numeric' });
};

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    active: 'bg-emerald-100 text-emerald-700',
    completed: 'bg-blue-100 text-blue-700',
    cancelled: 'bg-red-100 text-red-700',
};

const printContract = () => {
    window.print();
};

const remaining = computed(() => {
    return parseFloat(c.value.total_amount) - parseFloat(c.value.first_payment_amount);
});
</script>

<template>
    <Head :title="`Contrato ${c.contract_number}`" />

    <AdminLayout :title="`Contrato ${c.contract_number}`">
        <!-- Breadcrumb (hidden on print) -->
        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500 print:hidden">
            <Link :href="route('admin.contracts.index')" class="hover:text-amber-600 transition-colors">Contratos</Link>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span class="text-gray-800 font-medium">{{ c.contract_number }}</span>
        </nav>

        <!-- Actions bar (hidden on print) -->
        <div class="mb-6 flex items-center justify-between print:hidden">
            <span :class="['inline-flex items-center rounded-full px-3 py-1 text-sm font-medium', statusColors[c.status]]">
                {{ statuses[c.status] }}
            </span>
            <div class="flex items-center gap-2">
                <Link
                    v-if="c.quotation_id"
                    :href="route('admin.quotations.show', c.quotation_id)"
                    class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Ver Cotización
                </Link>
                <button
                    @click="printContract"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-4 py-1.5 text-xs font-semibold text-white hover:bg-amber-600 transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                    </svg>
                    Imprimir
                </button>
            </div>
        </div>

        <!-- ======== PRINTABLE CONTRACT ======== -->
        <div class="mx-auto max-w-3xl">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm print:border-none print:shadow-none print:rounded-none">
                <!-- Header -->
                <div class="border-b border-gray-200 p-8 text-center print:border-b-2 print:border-black">
                    <img src="/storage/images/logo-azul-altavox.png" alt="Banda Alta Vox" class="h-16 mx-auto mb-2" />
                    <p class="mt-1 text-sm text-gray-500">Contrato de Prestación de Servicios</p>
                    <p class="mt-2 text-xs text-gray-400 font-mono">{{ c.contract_number }}</p>
                </div>

                <div class="p-8 space-y-6 text-sm text-gray-700 leading-relaxed">
                    <!-- Intro paragraph -->
                    <p>
                        En la ciudad, a <strong>{{ formatDate(c.contract_date) }}</strong>,
                        se celebra el presente contrato de prestación de servicios para un evento de tipo
                        <strong>{{ c.event_type_label }}</strong> entre:
                    </p>

                    <!-- Parties -->
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg border border-gray-200 p-4 print:border-gray-400">
                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">El Cliente</h4>
                            <p class="font-semibold text-gray-900">{{ c.client_name }}</p>
                            <p v-if="c.client_phone" class="text-xs text-gray-500 mt-1">Tel: {{ c.client_phone }}</p>
                            <p v-if="c.client_email" class="text-xs text-gray-500">Email: {{ c.client_email }}</p>
                            <p v-if="c.client_address" class="text-xs text-gray-500">{{ c.client_address }}</p>
                        </div>
                        <div class="rounded-lg border border-gray-200 p-4 print:border-gray-400">
                            <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">El Prestador</h4>
                            <p class="font-semibold text-gray-900">Banda Alta Vox</p>
                            <p class="text-xs text-gray-500 mt-1">Rep: {{ c.representative_name }}</p>
                        </div>
                    </div>

                    <!-- Event details -->
                    <div class="rounded-lg bg-gray-50 p-4 print:bg-white print:border print:border-gray-400">
                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">Datos del evento</h4>
                        <div class="grid gap-2 sm:grid-cols-2 text-sm">
                            <div>
                                <span class="text-gray-500">Horas contratadas:</span>
                                <span class="ml-1 font-semibold">{{ c.hours_contracted }} hrs</span>
                            </div>
                            <div v-if="c.event_date">
                                <span class="text-gray-500">Fecha:</span>
                                <span class="ml-1 font-semibold">{{ formatDate(c.event_date) }}</span>
                            </div>
                            <div v-if="c.event_time_start">
                                <span class="text-gray-500">Horario:</span>
                                <span class="ml-1 font-semibold">{{ c.event_time_start }} - {{ c.event_time_end }}</span>
                            </div>
                            <div v-if="c.event_venue">
                                <span class="text-gray-500">Lugar:</span>
                                <span class="ml-1 font-semibold">{{ c.event_venue }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Observations -->
                    <div v-if="c.observations" class="rounded-lg border border-amber-200 bg-amber-50 p-4 print:bg-white print:border-gray-400">
                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Observaciones</h4>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ c.observations }}</p>
                    </div>

                    <!-- Clauses -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">Cláusulas</h4>
                        <ol class="space-y-3">
                            <li
                                v-for="(clause, idx) in c.clauses"
                                :key="idx"
                                class="text-sm text-gray-700 leading-relaxed pl-1"
                            >
                                <span class="font-semibold">{{ clause.split('.-')[0] }}.-</span>
                                {{ clause.split('.-').slice(1).join('.-') }}
                            </li>
                        </ol>
                    </div>

                    <!-- Financial -->
                    <div class="rounded-lg border-2 border-gray-800 p-4 text-center print:border-black">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Costo Total</p>
                                <p class="text-xl font-bold text-gray-900 mt-1">{{ formatPrice(c.total_amount) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Primer Pago</p>
                                <p class="text-xl font-bold text-emerald-600 mt-1">{{ formatPrice(c.first_payment_amount) }}</p>
                                <p class="text-[10px] text-gray-400">{{ formatDate(c.first_payment_date) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Restante</p>
                                <p class="text-xl font-bold text-amber-600 mt-1">{{ formatPrice(remaining) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quotation items if available -->
                    <div v-if="c.quotation?.items?.length">
                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-3">Servicios Contratados</h4>
                        <table class="w-full text-sm border border-gray-200 print:border-gray-400">
                            <thead class="bg-gray-50 print:bg-white text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-4 py-2 text-left border-b">Concepto</th>
                                    <th class="px-4 py-2 text-center border-b">Cant.</th>
                                    <th class="px-4 py-2 text-right border-b">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="item in c.quotation.items" :key="item.id">
                                    <td class="px-4 py-2">{{ item.description }}</td>
                                    <td class="px-4 py-2 text-center">{{ item.quantity }}</td>
                                    <td class="px-4 py-2 text-right font-medium">{{ formatPrice(item.total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Signatures -->
                    <div class="mt-12 grid grid-cols-2 gap-8 pt-8">
                        <div class="text-center">
                            <div class="border-t-2 border-gray-800 pt-3 mx-4">
                                <p class="text-sm font-semibold text-gray-900">{{ c.client_name }}</p>
                                <p class="text-xs text-gray-500">El Cliente</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="border-t-2 border-gray-800 pt-3 mx-4">
                                <p class="text-sm font-semibold text-gray-900">{{ c.representative_name }}</p>
                                <p class="text-xs text-gray-500">Representante — Banda Alta Vox</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style>
@media print {
    /* Hide sidebar and top bar when printing */
    aside, header, nav.print\:hidden, .print\:hidden {
        display: none !important;
    }
    .lg\:pl-64 {
        padding-left: 0 !important;
    }
    main {
        padding: 0 !important;
    }
}
</style>
