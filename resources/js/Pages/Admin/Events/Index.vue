<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    events: Array,
    upcoming: Array,
    month: Number,
    year: Number,
    statuses: Object,
    statusColors: Object,
});

// Calendar navigation
const currentMonth = ref(props.month);
const currentYear = ref(props.year);

const monthNames = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre',
];

const dayNames = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

const navigate = (dir) => {
    let m = currentMonth.value + dir;
    let y = currentYear.value;
    if (m < 1) { m = 12; y--; }
    if (m > 12) { m = 1; y++; }
    router.get(route('admin.events.index'), { month: m, year: y }, { preserveState: true, replace: true });
};

const goToday = () => {
    const now = new Date();
    router.get(route('admin.events.index'), { month: now.getMonth() + 1, year: now.getFullYear() }, { preserveState: true, replace: true });
};

// Build calendar grid
const calendarDays = computed(() => {
    const firstDay = new Date(currentYear.value, currentMonth.value - 1, 1);
    const lastDay = new Date(currentYear.value, currentMonth.value, 0);

    // Monday = 0 in our grid
    let startDow = firstDay.getDay() - 1;
    if (startDow < 0) startDow = 6;

    const days = [];

    // Previous month padding
    const prevMonthLast = new Date(currentYear.value, currentMonth.value - 1, 0).getDate();
    for (let i = startDow - 1; i >= 0; i--) {
        const d = prevMonthLast - i;
        const m = currentMonth.value - 1 < 1 ? 12 : currentMonth.value - 1;
        const y = currentMonth.value - 1 < 1 ? currentYear.value - 1 : currentYear.value;
        days.push({
            day: d,
            date: `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`,
            isCurrentMonth: false,
            isToday: false,
        });
    }

    // Current month days
    const today = new Date();
    const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

    for (let d = 1; d <= lastDay.getDate(); d++) {
        const dateStr = `${currentYear.value}-${String(currentMonth.value).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        days.push({
            day: d,
            date: dateStr,
            isCurrentMonth: true,
            isToday: dateStr === todayStr,
        });
    }

    // Next month padding to fill 6 rows
    const remaining = 42 - days.length;
    for (let d = 1; d <= remaining; d++) {
        const m = currentMonth.value + 1 > 12 ? 1 : currentMonth.value + 1;
        const y = currentMonth.value + 1 > 12 ? currentYear.value + 1 : currentYear.value;
        days.push({
            day: d,
            date: `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`,
            isCurrentMonth: false,
            isToday: false,
        });
    }

    return days;
});

const getEventsForDate = (date) => {
    return props.events.filter(e => e.date === date);
};

// Event detail popup
const selectedEvent = ref(null);
const showEventDetail = ref(false);

const openEvent = (event) => {
    selectedEvent.value = event;
    showEventDetail.value = true;
};

// New event modal
const showNewEvent = ref(false);
const newEventForm = useForm({
    title: '',
    event_date: '',
    time_start: '18:00',
    time_end: '23:00',
    venue: '',
    city: '',
    is_outdoor: false,
    guest_count: '',
    hours_contracted: 5,
    client_name: '',
    client_phone: '',
    event_type_label: '',
    notes: '',
    setup_notes: '',
});

const openNewEvent = (date) => {
    newEventForm.reset();
    newEventForm.event_date = date || '';
    showNewEvent.value = true;
};

const createEvent = () => {
    newEventForm.post(route('admin.events.store'), {
        onSuccess: () => {
            showNewEvent.value = false;
        },
    });
};

const formatDate = (date) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('es-MX', { day: 'numeric', month: 'short', year: 'numeric' });
};

const formatTime = (t) => t || '';
</script>

<template>
    <Head title="Agenda" />

    <AdminLayout title="Agenda">
        <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
            <!-- Calendar -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <!-- Calendar header -->
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-bold text-gray-800">
                            {{ monthNames[currentMonth - 1] }} {{ currentYear }}
                        </h3>
                        <button
                            @click="goToday"
                            class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-medium text-gray-600 hover:bg-gray-50"
                        >
                            Hoy
                        </button>
                    </div>
                    <div class="flex items-center gap-1">
                        <button
                            @click="navigate(-1)"
                            class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 transition-colors"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                        <button
                            @click="navigate(1)"
                            class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 transition-colors"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                        <button
                            @click="openNewEvent(null)"
                            class="ml-3 inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-600 transition-colors"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Evento
                        </button>
                    </div>
                </div>

                <!-- Day headers -->
                <div class="grid grid-cols-7 border-b border-gray-100">
                    <div
                        v-for="day in dayNames"
                        :key="day"
                        class="px-2 py-2 text-center text-xs font-semibold uppercase text-gray-500"
                    >
                        {{ day }}
                    </div>
                </div>

                <!-- Calendar grid -->
                <div class="grid grid-cols-7">
                    <div
                        v-for="(day, idx) in calendarDays"
                        :key="idx"
                        :class="[
                            'min-h-[90px] border-b border-r border-gray-100 p-1.5 cursor-pointer hover:bg-gray-50/50 transition-colors',
                            !day.isCurrentMonth && 'bg-gray-50/50',
                            day.isToday && 'bg-amber-50/50',
                        ]"
                        @dblclick="openNewEvent(day.date)"
                    >
                        <div class="flex items-center justify-between mb-1">
                            <span
                                :class="[
                                    'inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-medium',
                                    day.isToday ? 'bg-amber-500 text-white' : day.isCurrentMonth ? 'text-gray-700' : 'text-gray-400',
                                ]"
                            >
                                {{ day.day }}
                            </span>
                        </div>

                        <!-- Events in cell -->
                        <div class="space-y-0.5">
                            <button
                                v-for="event in getEventsForDate(day.date)"
                                :key="event.id"
                                @click.stop="openEvent(event)"
                                class="block w-full rounded px-1.5 py-0.5 text-left text-[10px] font-medium text-white truncate transition-opacity hover:opacity-80"
                                :style="{ backgroundColor: event.color }"
                                :title="event.title"
                            >
                                <span v-if="event.time_start" class="opacity-80">{{ event.time_start }}</span>
                                {{ event.title }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Upcoming -->
            <div class="space-y-4">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-4">Próximos Eventos</h3>

                    <div v-if="upcoming.length" class="space-y-3">
                        <Link
                            v-for="event in upcoming"
                            :key="event.id"
                            :href="route('admin.events.show', event.id)"
                            class="block rounded-lg border border-gray-100 p-3 hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex items-start gap-3">
                                <div
                                    class="mt-0.5 h-3 w-3 rounded-full shrink-0"
                                    :style="{ backgroundColor: event.color }"
                                />
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ event.title }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ formatDate(event.event_date) }}
                                        <span v-if="event.time_start"> · {{ event.time_start }}</span>
                                    </p>
                                    <p v-if="event.venue" class="text-xs text-gray-400 truncate">{{ event.venue }}</p>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <p v-else class="text-sm text-gray-400 text-center py-4">Sin eventos próximos</p>
                </div>

                <!-- Legend -->
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3">Estados</h3>
                    <div class="space-y-2">
                        <div v-for="(label, key) in statuses" :key="key" class="flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full" :style="{ backgroundColor: statusColors[key] }"></div>
                            <span class="text-xs text-gray-600">{{ label }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Detail Popup -->
        <ConfirmModal :show="showEventDetail" @close="showEventDetail = false">
            <template #title>{{ selectedEvent?.title }}</template>
            <template #default>
                <div v-if="selectedEvent" class="space-y-3 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-3 rounded-full" :style="{ backgroundColor: selectedEvent.color }"></div>
                        <span class="font-medium">{{ statuses[selectedEvent.status] }}</span>
                    </div>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <div><span class="text-gray-500">Fecha:</span> <span class="ml-1 font-medium">{{ formatDate(selectedEvent.date) }}</span></div>
                        <div v-if="selectedEvent.time_start"><span class="text-gray-500">Horario:</span> <span class="ml-1 font-medium">{{ selectedEvent.time_start }} - {{ selectedEvent.time_end }}</span></div>
                        <div v-if="selectedEvent.venue"><span class="text-gray-500">Lugar:</span> <span class="ml-1 font-medium">{{ selectedEvent.venue }}</span></div>
                        <div><span class="text-gray-500">Cliente:</span> <span class="ml-1 font-medium">{{ selectedEvent.client_name }}</span></div>
                        <div v-if="selectedEvent.client_phone"><span class="text-gray-500">Tel:</span> <span class="ml-1 font-medium">{{ selectedEvent.client_phone }}</span></div>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="showEventDetail = false" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cerrar
                </button>
                <Link
                    v-if="selectedEvent"
                    :href="route('admin.events.show', selectedEvent.id)"
                    class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600"
                >
                    Ver Detalle
                </Link>
            </template>
        </ConfirmModal>

        <!-- New Event Modal -->
        <ConfirmModal :show="showNewEvent" @close="showNewEvent = false" max-width="lg">
            <template #title>Nuevo Evento (Manual)</template>
            <template #default>
                <form @submit.prevent="createEvent" class="space-y-4" id="newEventForm">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <InputLabel for="ne_title" value="Título del evento *" />
                            <TextInput id="ne_title" v-model="newEventForm.title" type="text" class="mt-1 block w-full" required placeholder="Boda - Juan Pérez" />
                            <InputError class="mt-1" :message="newEventForm.errors.title" />
                        </div>

                        <div>
                            <InputLabel for="ne_date" value="Fecha *" />
                            <TextInput id="ne_date" v-model="newEventForm.event_date" type="date" class="mt-1 block w-full" required />
                            <InputError class="mt-1" :message="newEventForm.errors.event_date" />
                        </div>

                        <div>
                            <InputLabel for="ne_type" value="Tipo de evento" />
                            <TextInput id="ne_type" v-model="newEventForm.event_type_label" type="text" class="mt-1 block w-full" placeholder="Boda, XV años..." />
                        </div>

                        <div>
                            <InputLabel for="ne_start" value="Hora inicio" />
                            <TextInput id="ne_start" v-model="newEventForm.time_start" type="time" class="mt-1 block w-full" />
                        </div>

                        <div>
                            <InputLabel for="ne_end" value="Hora fin" />
                            <TextInput id="ne_end" v-model="newEventForm.time_end" type="time" class="mt-1 block w-full" />
                        </div>

                        <div>
                            <InputLabel for="ne_client" value="Nombre del cliente *" />
                            <TextInput id="ne_client" v-model="newEventForm.client_name" type="text" class="mt-1 block w-full" required />
                            <InputError class="mt-1" :message="newEventForm.errors.client_name" />
                        </div>

                        <div>
                            <InputLabel for="ne_phone" value="Teléfono" />
                            <TextInput id="ne_phone" v-model="newEventForm.client_phone" type="tel" class="mt-1 block w-full" />
                        </div>

                        <div class="sm:col-span-2">
                            <InputLabel for="ne_venue" value="Lugar" />
                            <TextInput id="ne_venue" v-model="newEventForm.venue" type="text" class="mt-1 block w-full" placeholder="Salón de eventos..." />
                        </div>

                        <div class="sm:col-span-2">
                            <InputLabel for="ne_notes" value="Notas" />
                            <textarea id="ne_notes" v-model="newEventForm.notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm" />
                        </div>
                    </div>
                </form>
            </template>
            <template #footer>
                <button @click="showNewEvent = false" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button
                    form="newEventForm"
                    type="submit"
                    :disabled="newEventForm.processing"
                    class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600 disabled:opacity-50"
                >
                    {{ newEventForm.processing ? 'Guardando...' : 'Crear Evento' }}
                </button>
            </template>
        </ConfirmModal>
    </AdminLayout>
</template>
