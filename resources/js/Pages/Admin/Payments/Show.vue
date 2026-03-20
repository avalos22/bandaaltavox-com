<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import FlashMessages from '@/Components/FlashMessages.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    contract: Object,
    totalPaid: Number,
    balance: Number,
    paymentMethods: Object,
});

const formatMoney = (val) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val ?? 0);
};

const progress = Math.min(100, Math.round((props.totalPaid / (parseFloat(props.contract.total_amount) || 1)) * 100));

// New payment form
const showPaymentForm = ref(false);
const paymentForm = useForm({
    amount: '',
    method: 'transfer',
    payment_date: new Date().toISOString().split('T')[0],
    reference: '',
    notes: '',
});

const submitPayment = () => {
    paymentForm.post(route('admin.payments.store', props.contract.id), {
        onSuccess: () => {
            showPaymentForm.value = false;
            paymentForm.reset();
            paymentForm.payment_date = new Date().toISOString().split('T')[0];
            paymentForm.method = 'transfer';
        },
    });
};

// Delete payment
const paymentToDelete = ref(null);
const showDeleteModal = ref(false);

const confirmDelete = (payment) => {
    paymentToDelete.value = payment;
    showDeleteModal.value = true;
};

const deletePayment = () => {
    if (!paymentToDelete.value) return;
    router.delete(route('admin.payments.destroy', paymentToDelete.value.id), {
        onSuccess: () => { showDeleteModal.value = false; paymentToDelete.value = null; },
    });
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
    <Head :title="`Pagos - ${contract.contract_number}`" />

    <AdminLayout :title="`Pagos: ${contract.contract_number}`">
        <template #actions>
            <Link
                :href="route('admin.payments.index')"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Cobranza
            </Link>
        </template>

        <FlashMessages />

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Summary bar -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="grid gap-4 sm:grid-cols-3 mb-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Total Contrato</p>
                            <p class="mt-1 text-xl font-bold text-gray-800">{{ formatMoney(contract.total_amount) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Cobrado</p>
                            <p class="mt-1 text-xl font-bold text-emerald-600">{{ formatMoney(totalPaid) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Saldo Pendiente</p>
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

                <!-- Payment form -->
                <div v-if="showPaymentForm" class="rounded-xl border border-amber-200 bg-amber-50/50 p-6 shadow-sm">
                    <h3 class="text-base font-bold text-gray-800 mb-4">Registrar Pago</h3>
                    <form @submit.prevent="submitPayment" class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <InputLabel for="pay_amount" value="Monto *" />
                                <TextInput id="pay_amount" v-model="paymentForm.amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" required placeholder="0.00" />
                                <InputError class="mt-1" :message="paymentForm.errors.amount" />
                            </div>
                            <div>
                                <InputLabel for="pay_method" value="Método *" />
                                <select id="pay_method" v-model="paymentForm.method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm">
                                    <option v-for="(label, key) in paymentMethods" :key="key" :value="key">{{ label }}</option>
                                </select>
                                <InputError class="mt-1" :message="paymentForm.errors.method" />
                            </div>
                            <div>
                                <InputLabel for="pay_date" value="Fecha de pago *" />
                                <TextInput id="pay_date" v-model="paymentForm.payment_date" type="date" class="mt-1 block w-full" required />
                                <InputError class="mt-1" :message="paymentForm.errors.payment_date" />
                            </div>
                            <div>
                                <InputLabel for="pay_ref" value="Referencia" />
                                <TextInput id="pay_ref" v-model="paymentForm.reference" type="text" class="mt-1 block w-full" placeholder="No. transferencia, recibo..." />
                            </div>
                            <div class="sm:col-span-2">
                                <InputLabel for="pay_notes" value="Notas" />
                                <textarea id="pay_notes" v-model="paymentForm.notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            <button type="button" @click="showPaymentForm = false" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="paymentForm.processing" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600 disabled:opacity-50">
                                {{ paymentForm.processing ? 'Guardando...' : 'Registrar Pago' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Payment history -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <h3 class="text-base font-bold text-gray-800">Historial de Pagos</h3>
                        <button
                            v-if="!showPaymentForm"
                            @click="showPaymentForm = true"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-600 transition-colors"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Registrar Pago
                        </button>
                    </div>

                    <div v-if="contract.payments?.length" class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Monto</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Método</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Referencia</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Registró</th>
                                    <th class="px-4 py-2.5"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(pmt, index) in contract.payments" :key="pmt.id" class="hover:bg-gray-50/50">
                                    <td class="px-4 py-3 text-gray-500">{{ index + 1 }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ new Date(pmt.payment_date).toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' }) }}</td>
                                    <td class="px-4 py-3 font-semibold text-emerald-600">{{ formatMoney(pmt.amount) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                                            {{ methodLabels[pmt.method] || pmt.method }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ pmt.reference || '—' }}</td>
                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ pmt.recorder?.name || '—' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <button
                                            @click="confirmDelete(pmt)"
                                            class="text-red-400 hover:text-red-600 transition-colors"
                                            title="Eliminar pago"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else class="px-6 py-12 text-center">
                        <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                        <p class="mt-3 text-sm text-gray-500">No hay pagos registrados</p>
                        <button
                            @click="showPaymentForm = true"
                            class="mt-3 inline-flex items-center gap-1 rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600"
                        >
                            Registrar primer pago
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Contract info -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-4">Contrato</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-500">Número:</span>
                            <Link :href="route('admin.contracts.show', contract.id)" class="ml-1 font-medium text-amber-600 hover:underline">
                                {{ contract.contract_number }}
                            </Link>
                        </div>
                        <div>
                            <span class="text-gray-500">Estado:</span>
                            <span class="ml-1 font-medium text-gray-700 capitalize">{{ contract.status }}</span>
                        </div>
                        <div v-if="contract.event_date">
                            <span class="text-gray-500">Fecha evento:</span>
                            <span class="ml-1 font-medium text-gray-700">{{ new Date(contract.event_date).toLocaleDateString('es-MX') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Client info -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">Cliente</h3>
                    <div class="space-y-2 text-sm">
                        <p class="font-medium text-gray-800">{{ contract.client_name }}</p>
                        <p v-if="contract.client_phone" class="text-gray-600">
                            <a :href="'tel:' + contract.client_phone" class="text-amber-600 hover:underline">{{ contract.client_phone }}</a>
                        </p>
                        <p v-if="contract.client_email" class="text-gray-500 text-xs">{{ contract.client_email }}</p>
                    </div>
                </div>

                <!-- Event link -->
                <div v-if="contract.event" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">Evento</h3>
                    <Link
                        :href="route('admin.events.show', contract.event.id)"
                        class="text-sm font-medium text-amber-600 hover:underline"
                    >
                        {{ contract.event.title }}
                    </Link>
                    <p class="text-xs text-gray-500 mt-1">{{ contract.event.venue }}</p>
                </div>

                <!-- Quick balance info -->
                <div class="rounded-xl border p-5 shadow-sm" :class="balance <= 0 ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50'">
                    <div class="text-center">
                        <p class="text-xs font-medium uppercase" :class="balance <= 0 ? 'text-emerald-600' : 'text-amber-600'">
                            {{ balance <= 0 ? '¡Pagado completo!' : 'Saldo pendiente' }}
                        </p>
                        <p class="mt-1 text-2xl font-bold" :class="balance <= 0 ? 'text-emerald-700' : 'text-amber-700'">
                            {{ formatMoney(Math.abs(balance)) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete payment modal -->
        <ConfirmModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>¿Eliminar Pago?</template>
            <template #default>
                <p class="text-sm text-gray-600">
                    Se eliminará el pago de <strong>{{ formatMoney(paymentToDelete?.amount) }}</strong>. Esta acción no se puede deshacer.
                </p>
            </template>
            <template #footer>
                <button @click="showDeleteModal = false" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button @click="deletePayment" class="rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600">
                    Eliminar
                </button>
            </template>
        </ConfirmModal>
    </AdminLayout>
</template>
