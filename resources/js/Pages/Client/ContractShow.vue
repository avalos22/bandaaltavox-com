<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';

const props = defineProps({
    contract: Object,
    totalPaid: Number,
    balance: Number,
});

const formatMoney = (val) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val ?? 0);
const formatDate = (d) => d ? new Date(d).toLocaleDateString('es-MX', { day: 'numeric', month: 'long', year: 'numeric' }) : '—';

const progress = Math.min(100, Math.round((props.totalPaid / (parseFloat(props.contract.total_amount) || 1)) * 100));

const methodLabels = {
    cash: 'Efectivo',
    transfer: 'Transferencia',
    card: 'Tarjeta',
    deposit: 'Depósito',
    other: 'Otro',
};
</script>

<template>
    <Head :title="`Contrato ${contract.contract_number}`" />

    <ClientLayout :title="contract.contract_number">
        <template #actions>
            <Link :href="route('client.contracts')" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Mis Contratos
            </Link>
        </template>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <!-- Payment progress -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="grid gap-4 sm:grid-cols-3 mb-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Total</p>
                            <p class="mt-1 text-xl font-bold text-gray-800">{{ formatMoney(contract.total_amount) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Pagado</p>
                            <p class="mt-1 text-xl font-bold text-emerald-600">{{ formatMoney(totalPaid) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Saldo</p>
                            <p class="mt-1 text-xl font-bold" :class="balance > 0 ? 'text-red-600' : 'text-emerald-600'">{{ formatMoney(balance) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 h-3 rounded-full bg-gray-200 overflow-hidden">
                            <div
                                class="h-full rounded-full transition-all"
                                :class="progress >= 100 ? 'bg-emerald-500' : progress >= 50 ? 'bg-amber-500' : 'bg-red-500'"
                                :style="{ width: progress + '%' }"
                            />
                        </div>
                        <span class="text-sm font-semibold" :class="progress >= 100 ? 'text-emerald-600' : 'text-gray-700'">{{ progress }}%</span>
                    </div>
                </div>

                <!-- Contract details -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-base font-bold text-gray-800">Detalles del Contrato</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Tipo de Evento</dt>
                                <dd class="mt-1 text-sm text-gray-700">{{ contract.event_type_label || '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Fecha del Evento</dt>
                                <dd class="mt-1 text-sm text-gray-700 capitalize">{{ formatDate(contract.event_date) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Horario</dt>
                                <dd class="mt-1 text-sm text-gray-700">{{ contract.event_time_start || '—' }} — {{ contract.event_time_end || '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Lugar</dt>
                                <dd class="mt-1 text-sm text-gray-700">{{ contract.event_venue || '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Horas Contratadas</dt>
                                <dd class="mt-1 text-sm text-gray-700">{{ contract.hours_contracted }}h</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Fecha del Contrato</dt>
                                <dd class="mt-1 text-sm text-gray-700">{{ formatDate(contract.contract_date) }}</dd>
                            </div>
                        </dl>

                        <!-- Items from quotation -->
                        <div v-if="contract.quotation?.items?.length" class="mt-6 border-t border-gray-100 pt-4">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase mb-3">Servicios Contratados</h4>
                            <div class="space-y-2">
                                <div v-for="item in contract.quotation.items" :key="item.id" class="flex items-center justify-between py-1.5 text-sm">
                                    <span class="text-gray-700">{{ item.description }} <span v-if="item.quantity > 1" class="text-gray-400">×{{ item.quantity }}</span></span>
                                    <span class="font-medium text-gray-800">{{ formatMoney(item.total) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Clauses -->
                        <div v-if="contract.clauses?.length" class="mt-6 border-t border-gray-100 pt-4">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase mb-3">Cláusulas del Contrato</h4>
                            <ol class="space-y-2 list-decimal list-inside text-sm text-gray-600">
                                <li v-for="(clause, i) in contract.clauses" :key="i" class="leading-relaxed">{{ clause }}</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Payment history -->
                <div v-if="contract.payments?.length" class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-base font-bold text-gray-800">Historial de Pagos</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Monto</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Método</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Referencia</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(pmt, i) in contract.payments" :key="pmt.id">
                                    <td class="px-4 py-3 text-gray-500">{{ i + 1 }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ formatDate(pmt.payment_date) }}</td>
                                    <td class="px-4 py-3 font-semibold text-emerald-600">{{ formatMoney(pmt.amount) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                                            {{ methodLabels[pmt.method] || pmt.method }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ pmt.reference || '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Balance card -->
                <div class="rounded-xl border p-5 shadow-sm" :class="balance <= 0 ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50'">
                    <div class="text-center">
                        <p class="text-xs font-medium uppercase" :class="balance <= 0 ? 'text-emerald-600' : 'text-amber-600'">
                            {{ balance <= 0 ? '¡Pagado completo!' : 'Saldo pendiente' }}
                        </p>
                        <p class="mt-1 text-3xl font-bold" :class="balance <= 0 ? 'text-emerald-700' : 'text-amber-700'">
                            {{ formatMoney(Math.abs(balance)) }}
                        </p>
                    </div>
                </div>

                <!-- Event info -->
                <div v-if="contract.event" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">Tu Evento</h3>
                    <div class="space-y-2 text-sm">
                        <p class="font-medium text-gray-800">{{ contract.event.title }}</p>
                        <p class="text-gray-500 capitalize">{{ formatDate(contract.event.event_date) }}</p>
                        <p v-if="contract.event.venue" class="text-gray-500">{{ contract.event.venue }}</p>
                    </div>
                </div>

                <!-- Contact info -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">¿Dudas?</h3>
                    <p class="text-sm text-gray-600">Contáctanos para cualquier consulta sobre tu contrato o evento.</p>
                    <p class="mt-2 text-sm font-medium text-amber-600">Banda Alta Vox</p>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
