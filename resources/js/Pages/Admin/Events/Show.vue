<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import FlashMessages from '@/Components/FlashMessages.vue';

const props = defineProps({
    event: Object,
    statuses: Object,
});

const statusColors = {
    confirmed: '#f59e0b',
    tentative: '#94a3b8',
    in_progress: '#3b82f6',
    completed: '#10b981',
    cancelled: '#ef4444',
};

const editing = ref(false);
const form = useForm({
    title: props.event.title,
    status: props.event.status,
    event_date: props.event.event_date?.split('T')[0] ?? '',
    time_start: props.event.time_start ?? '',
    time_end: props.event.time_end ?? '',
    venue: props.event.venue ?? '',
    city: props.event.city ?? '',
    notes: props.event.notes ?? '',
    setup_notes: props.event.setup_notes ?? '',
});

const updateEvent = () => {
    form.put(route('admin.events.update', props.event.id), {
        onSuccess: () => { editing.value = false; },
    });
};

const showDeleteModal = ref(false);

const deleteEvent = () => {
    router.delete(route('admin.events.destroy', props.event.id));
};

const changeStatus = (status) => {
    router.put(route('admin.events.update', props.event.id), { status }, { preserveScroll: true });
};

const formatDate = (date) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('es-MX', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
};

const formatMoney = (val) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(val ?? 0);
};
</script>

<template>
    <Head :title="event.title" />

    <AdminLayout :title="event.title">
        <template #actions>
            <Link
                :href="route('admin.events.index')"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Agenda
            </Link>
        </template>

        <FlashMessages />

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Event details card -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-4 w-4 rounded-full" :style="{ backgroundColor: statusColors[event.status] }"></div>
                            <h3 class="text-base font-bold text-gray-800">Información del Evento</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                v-if="!editing"
                                @click="editing = true"
                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50"
                            >
                                Editar
                            </button>
                            <button
                                @click="showDeleteModal = true"
                                class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>

                    <!-- View mode -->
                    <div v-if="!editing" class="p-6">
                        <dl class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Título</dt>
                                <dd class="mt-1 text-sm font-medium text-gray-800">{{ event.title }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Estado</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium" :style="{ backgroundColor: statusColors[event.status] + '20', color: statusColors[event.status] }">
                                        <span class="h-1.5 w-1.5 rounded-full" :style="{ backgroundColor: statusColors[event.status] }"></span>
                                        {{ statuses[event.status] }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Fecha</dt>
                                <dd class="mt-1 text-sm text-gray-700 capitalize">{{ formatDate(event.event_date) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Horario</dt>
                                <dd class="mt-1 text-sm text-gray-700">{{ event.time_start || '—' }} — {{ event.time_end || '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Lugar</dt>
                                <dd class="mt-1 text-sm text-gray-700">{{ event.venue || '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase">Ciudad</dt>
                                <dd class="mt-1 text-sm text-gray-700">{{ event.city || '—' }}</dd>
                            </div>
                            <div v-if="event.event_type_label">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Tipo de Evento</dt>
                                <dd class="mt-1 text-sm text-gray-700">{{ event.event_type_label }}</dd>
                            </div>
                            <div v-if="event.hours_contracted">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Horas contratadas</dt>
                                <dd class="mt-1 text-sm text-gray-700">{{ event.hours_contracted }}h</dd>
                            </div>
                            <div v-if="event.guest_count">
                                <dt class="text-xs font-medium text-gray-500 uppercase">No. Invitados</dt>
                                <dd class="mt-1 text-sm text-gray-700">{{ event.guest_count }}</dd>
                            </div>
                            <div v-if="event.is_outdoor !== null">
                                <dt class="text-xs font-medium text-gray-500 uppercase">Al aire libre</dt>
                                <dd class="mt-1 text-sm text-gray-700">{{ event.is_outdoor ? 'Sí' : 'No' }}</dd>
                            </div>
                        </dl>

                        <div v-if="event.notes" class="mt-5 border-t border-gray-100 pt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Notas</dt>
                            <dd class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ event.notes }}</dd>
                        </div>

                        <div v-if="event.setup_notes" class="mt-4 border-t border-gray-100 pt-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase">Notas de montaje</dt>
                            <dd class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ event.setup_notes }}</dd>
                        </div>
                    </div>

                    <!-- Edit mode -->
                    <form v-else @submit.prevent="updateEvent" class="p-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="text-xs font-medium text-gray-500 uppercase">Título</label>
                                <input v-model="form.title" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Estado</label>
                                <select v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm">
                                    <option v-for="(label, key) in statuses" :key="key" :value="key">{{ label }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Fecha</label>
                                <input v-model="form.event_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Hora Inicio</label>
                                <input v-model="form.time_start" type="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Hora Fin</label>
                                <input v-model="form.time_end" type="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Lugar</label>
                                <input v-model="form.venue" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 uppercase">Ciudad</label>
                                <input v-model="form.city" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-medium text-gray-500 uppercase">Notas</label>
                                <textarea v-model="form.notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-medium text-gray-500 uppercase">Notas de montaje</label>
                                <textarea v-model="form.setup_notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-end gap-3">
                            <button type="button" @click="editing = false" class="rounded-lg border border-gray-200 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
                            <button type="submit" :disabled="form.processing" class="rounded-lg bg-amber-500 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-600 disabled:opacity-50">Guardar</button>
                        </div>
                    </form>
                </div>

                <!-- Payments from contract -->
                <div v-if="event.contract?.payments?.length" class="rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-base font-bold text-gray-800">Pagos del Contrato</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Monto</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Método</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Referencia</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="pmt in event.contract.payments" :key="pmt.id">
                                    <td class="px-4 py-2.5 text-gray-700">{{ new Date(pmt.payment_date).toLocaleDateString('es-MX') }}</td>
                                    <td class="px-4 py-2.5 font-semibold text-gray-800">{{ formatMoney(pmt.amount) }}</td>
                                    <td class="px-4 py-2.5 text-gray-600 capitalize">{{ pmt.method }}</td>
                                    <td class="px-4 py-2.5 text-gray-500">{{ pmt.reference || '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick actions -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-4">Cambiar Estado</h3>
                    <div class="grid grid-cols-1 gap-2">
                        <button
                            v-for="(label, key) in statuses"
                            :key="key"
                            @click="changeStatus(key)"
                            :disabled="event.status === key"
                            class="flex items-center gap-2 rounded-lg border px-3 py-2 text-xs font-medium transition-colors"
                            :class="event.status === key ? 'border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed' : 'border-gray-200 text-gray-700 hover:bg-gray-50'"
                        >
                            <span class="h-2.5 w-2.5 rounded-full" :style="{ backgroundColor: statusColors[key] }"></span>
                            {{ label }}
                        </button>
                    </div>
                </div>

                <!-- Client info -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">Cliente</h3>
                    <div class="space-y-2 text-sm">
                        <p class="font-medium text-gray-800">{{ event.client_name || '—' }}</p>
                        <p v-if="event.client_phone" class="text-gray-600">
                            <a :href="'tel:' + event.client_phone" class="text-amber-600 hover:underline">{{ event.client_phone }}</a>
                        </p>
                    </div>
                </div>

                <!-- Contract link -->
                <div v-if="event.contract" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">Contrato Asociado</h3>
                    <Link
                        :href="route('admin.contracts.show', event.contract.id)"
                        class="text-sm font-medium text-amber-600 hover:underline"
                    >
                        {{ event.contract.contract_number }}
                    </Link>
                    <p class="text-xs text-gray-500 mt-1">Total: {{ formatMoney(event.contract.total) }}</p>
                </div>
            </div>
        </div>

        <!-- Delete modal -->
        <ConfirmModal :show="showDeleteModal" @close="showDeleteModal = false">
            <template #title>¿Eliminar Evento?</template>
            <template #default>
                <p class="text-sm text-gray-600">
                    Se eliminará permanentemente el evento <strong>{{ event.title }}</strong>. Esta acción no se puede deshacer.
                </p>
            </template>
            <template #footer>
                <button @click="showDeleteModal = false" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button @click="deleteEvent" class="rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600">
                    Eliminar
                </button>
            </template>
        </ConfirmModal>
    </AdminLayout>
</template>
