<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    eventTypes: Array,
    packages: Array,
    addons: Array,
    addonCategories: Object,
    addonSubcategories: Object,
});

const step = ref(1);
const totalSteps = 4;

const form = useForm({
    event_type_id: '',
    event_date: '',
    event_time_start: '18:00',
    event_time_end: '23:00',
    event_venue: '',
    event_city: '',
    event_is_outdoor: false,
    guest_count: '',
    hours_contracted: 5,
    observations: '',
    items: [],
});

// --- Package selection ---
const selectedPackageId = ref(null);
const selectedPackage = computed(() => props.packages.find(p => p.id === selectedPackageId.value));

// --- Addon selection ---
const selectedAddonIds = ref([]);
const addonQuantities = ref({});

const toggleAddon = (addonId) => {
    const idx = selectedAddonIds.value.indexOf(addonId);
    if (idx === -1) {
        selectedAddonIds.value.push(addonId);
        if (!addonQuantities.value[addonId]) addonQuantities.value[addonId] = 1;
    } else {
        selectedAddonIds.value.splice(idx, 1);
    }
};

// --- Build items ---
const buildItems = () => {
    const items = [];
    if (selectedPackage.value) {
        items.push({
            type: 'package',
            reference_id: selectedPackage.value.id,
            description: `Paquete ${selectedPackage.value.name} (${form.hours_contracted} hrs)`,
            quantity: 1,
            unit_price: selectedPackage.value.price != null ? parseFloat(selectedPackage.value.price) : 0,
            price_pending: selectedPackage.value.price == null,
        });
    }
    selectedAddonIds.value.forEach(id => {
        const addon = props.addons.find(a => a.id === id);
        if (addon) {
            const qty = addonQuantities.value[id] || 1;
            items.push({
                type: 'addon',
                reference_id: addon.id,
                description: addon.name,
                quantity: qty,
                unit_price: addon.price != null ? parseFloat(addon.price) : 0,
                price_pending: addon.price == null,
            });
        }
    });
    return items;
};

// --- Totals ---
const subtotal = computed(() => {
    const items = buildItems();
    return items.reduce((sum, i) => sum + (i.quantity * i.unit_price), 0);
});

// --- Required addon for selected package ---
const requiredSubcategory = computed(() => selectedPackage.value?.required_addon_subcategory ?? null);

const requiredAddonSatisfied = computed(() => {
    if (!requiredSubcategory.value) return true;
    return props.addons.some(a =>
        a.subcategory === requiredSubcategory.value &&
        selectedAddonIds.value.includes(a.id)
    );
});

// --- Addon groups: category → subcategory → addons ---
const groupedAddons = computed(() => {
    const groups = {};
    props.addons.forEach(addon => {
        const cat = addon.category;
        const sub = addon.subcategory || '';
        if (!groups[cat]) groups[cat] = {};
        if (!groups[cat][sub]) groups[cat][sub] = [];
        groups[cat][sub].push(addon);
    });
    return groups;
});

// --- Navigation ---
const canNext = computed(() => {
    if (step.value === 1) return selectedPackageId.value !== null;
    if (step.value === 2) return form.hours_contracted >= 1;
    if (step.value === 3) return requiredAddonSatisfied.value;
    return true;
});

const nextStep = () => {
    if (step.value < totalSteps && canNext.value) step.value++;
};
const prevStep = () => {
    if (step.value > 1) step.value--;
};

const submit = () => {
    form.items = buildItems();
    form.post(route('client.quotations.store'));
};

const steps = [
    { num: 1, label: 'Paquete' },
    { num: 2, label: 'Evento' },
    { num: 3, label: 'Extras' },
    { num: 4, label: 'Confirmar' },
];

const formatPrice = (price) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 0 }).format(price);
</script>

<template>
    <Head title="Solicitar Cotización" />

    <ClientLayout title="Solicitar Cotización">
        <!-- Stepper -->
        <div class="mb-8">
            <div class="flex items-center justify-between max-w-xl mx-auto">
                <template v-for="(s, index) in steps" :key="s.num">
                    <div class="flex flex-col items-center gap-1.5">
                        <div
                            :class="[
                                'flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold transition-all',
                                step === s.num
                                    ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/30'
                                    : step > s.num
                                        ? 'bg-emerald-500 text-white'
                                        : 'bg-gray-200 text-gray-500',
                            ]"
                        >
                            <svg v-if="step > s.num" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span v-else>{{ s.num }}</span>
                        </div>
                        <span :class="['text-xs font-medium', step === s.num ? 'text-amber-600' : 'text-gray-400']">
                            {{ s.label }}
                        </span>
                    </div>
                    <div
                        v-if="index < steps.length - 1"
                        :class="['h-0.5 flex-1 mx-2 mb-5 rounded-full transition-colors', step > s.num ? 'bg-emerald-400' : 'bg-gray-200']"
                    />
                </template>
            </div>
        </div>

        <div class="mx-auto max-w-3xl">
            <form @submit.prevent="submit">
                <!-- ============ STEP 1: Paquete ============ -->
                <div v-show="step === 1" class="space-y-6">
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-800 mb-4">Selecciona tu paquete</h3>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div
                                v-for="pkg in packages"
                                :key="pkg.id"
                                @click="selectedPackageId = pkg.id"
                                :class="[
                                    'cursor-pointer rounded-xl border-2 p-5 transition-all',
                                    selectedPackageId === pkg.id
                                        ? 'border-amber-500 bg-amber-50/50 shadow-md'
                                        : 'border-gray-200 hover:border-gray-300 hover:shadow-sm',
                                ]"
                            >
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="font-semibold text-gray-800">{{ pkg.name }}</h4>
                                    <div
                                        :class="[
                                            'flex h-5 w-5 items-center justify-center rounded-full border-2 transition-colors',
                                            selectedPackageId === pkg.id ? 'border-amber-500 bg-amber-500' : 'border-gray-300',
                                        ]"
                                    >
                                        <svg v-if="selectedPackageId === pkg.id" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 mb-3">{{ pkg.description }}</p>
                                <p class="text-lg font-bold text-amber-600">
                                    {{ pkg.price != null ? formatPrice(pkg.price) : 'A cotizar' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1">{{ pkg.duration_hours }} horas incluidas</p>
                                <!-- Required addon badge -->
                                <div
                                    v-if="pkg.required_addon_subcategory"
                                    class="mt-2 inline-flex items-center gap-1 rounded-full bg-blue-50 border border-blue-200 px-2.5 py-0.5 text-[11px] font-medium text-blue-700"
                                >
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                    </svg>
                                    Requiere elegir: {{ pkg.required_addon_subcategory }}
                                </div>
                                <div v-if="pkg.includes?.length" class="mt-3 pt-3 border-t border-gray-100 space-y-1">
                                    <p v-for="inc in pkg.includes" :key="inc.id" class="text-xs text-gray-500 flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        {{ inc.description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.items" />
                    </div>
                </div>

                <!-- ============ STEP 2: Evento ============ -->
                <div v-show="step === 2" class="space-y-6">
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                        <h3 class="text-base font-semibold text-gray-800">Información del evento</h3>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <InputLabel for="event_type" value="Tipo de evento" />
                                <select
                                    id="event_type"
                                    v-model="form.event_type_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm"
                                >
                                    <option value="">-- Selecciona --</option>
                                    <option v-for="et in eventTypes" :key="et.id" :value="et.id">{{ et.name }}</option>
                                </select>
                            </div>

                            <div>
                                <InputLabel for="event_date" value="Fecha del evento" />
                                <TextInput id="event_date" type="date" v-model="form.event_date" class="mt-1 block w-full" />
                                <InputError class="mt-1" :message="form.errors.event_date" />
                            </div>

                            <div>
                                <InputLabel for="hours" value="Horas contratadas" />
                                <TextInput id="hours" type="number" v-model="form.hours_contracted" min="1" max="24" class="mt-1 block w-full" />
                                <InputError class="mt-1" :message="form.errors.hours_contracted" />
                            </div>

                            <div>
                                <InputLabel for="time_start" value="Hora inicio" />
                                <TextInput id="time_start" type="time" v-model="form.event_time_start" class="mt-1 block w-full" />
                            </div>

                            <div>
                                <InputLabel for="time_end" value="Hora fin" />
                                <TextInput id="time_end" type="time" v-model="form.event_time_end" class="mt-1 block w-full" />
                            </div>

                            <div>
                                <InputLabel for="event_venue" value="Lugar del evento" />
                                <TextInput id="event_venue" type="text" v-model="form.event_venue" class="mt-1 block w-full" placeholder="Nombre del salón o lugar" />
                            </div>

                            <div>
                                <InputLabel for="event_city" value="Ciudad" />
                                <TextInput id="event_city" type="text" v-model="form.event_city" class="mt-1 block w-full" placeholder="Monterrey, NL" />
                            </div>

                            <div>
                                <InputLabel for="guest_count" value="Invitados (aproximado)" />
                                <TextInput id="guest_count" type="number" v-model="form.guest_count" min="1" class="mt-1 block w-full" placeholder="150" />
                            </div>

                            <div class="flex items-center gap-3 pt-6">
                                <input id="outdoor" type="checkbox" v-model="form.event_is_outdoor" class="rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                                <InputLabel for="outdoor" value="Evento al aire libre" class="!mb-0" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ STEP 3: Extras ============ -->
                <div v-show="step === 3" class="space-y-6">
                    <!-- Required addon warning -->
                    <div
                        v-if="requiredSubcategory && !requiredAddonSatisfied"
                        class="rounded-xl border-2 border-amber-400 bg-amber-50 p-4"
                    >
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-amber-800">Este paquete requiere elegir un adicional</p>
                                <p class="text-xs text-amber-700 mt-0.5">
                                    Selecciona al menos una opción de <strong>{{ requiredSubcategory }}</strong> para continuar.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-800">Servicios adicionales</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Selecciona los extras que desees agregar (opcional)</p>
                        </div>

                        <div v-for="(subcategoryMap, category) in groupedAddons" :key="category">
                            <!-- Category header -->
                            <div class="bg-gray-50 border-b border-gray-200 px-6 py-2.5">
                                <h4 class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    {{ addonCategories[category] || category }}
                                </h4>
                            </div>

                            <div class="divide-y divide-gray-100">
                                <div
                                    v-for="(addonsInGroup, subcategory) in subcategoryMap"
                                    :key="subcategory"
                                    :class="[
                                        'px-6 py-3',
                                        requiredSubcategory && subcategory === requiredSubcategory && !requiredAddonSatisfied
                                            ? 'bg-amber-50/60'
                                            : '',
                                    ]"
                                >
                                    <!-- Subcategory label -->
                                    <div v-if="subcategory" class="mb-2 flex items-center gap-2">
                                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">{{ subcategory }}</p>
                                        <span
                                            v-if="requiredSubcategory && subcategory === requiredSubcategory && !requiredAddonSatisfied"
                                            class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700"
                                        >
                                            Requerido
                                        </span>
                                    </div>

                                    <div class="space-y-2">
                                        <div
                                            v-for="addon in addonsInGroup"
                                            :key="addon.id"
                                            @click="toggleAddon(addon.id)"
                                            :class="[
                                                'flex items-center gap-4 rounded-lg border p-4 cursor-pointer transition-all',
                                                selectedAddonIds.includes(addon.id)
                                                    ? 'border-amber-400 bg-amber-50/40'
                                                    : 'border-gray-200 hover:border-gray-300',
                                            ]"
                                        >
                                            <div
                                                :class="[
                                                    'flex h-5 w-5 shrink-0 items-center justify-center rounded border-2 transition-colors',
                                                    selectedAddonIds.includes(addon.id)
                                                        ? 'border-amber-500 bg-amber-500'
                                                        : 'border-gray-300',
                                                ]"
                                            >
                                                <svg v-if="selectedAddonIds.includes(addon.id)" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800">{{ addon.name }}</p>
                                                <p v-if="addon.description" class="text-xs text-gray-500 mt-0.5">{{ addon.description }}</p>
                                                <p v-if="addon.duration" class="text-[11px] text-gray-400 mt-0.5">{{ addon.duration }}</p>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div v-if="selectedAddonIds.includes(addon.id)" class="flex items-center gap-1" @click.stop>
                                                    <label class="text-xs text-gray-500">Cant:</label>
                                                    <input
                                                        type="number"
                                                        min="1"
                                                        max="99"
                                                        v-model.number="addonQuantities[addon.id]"
                                                        class="w-14 rounded border-gray-300 text-center text-xs shadow-sm focus:border-amber-400 focus:ring-amber-400"
                                                    />
                                                </div>
                                                <span class="text-sm font-semibold text-gray-700 whitespace-nowrap">
                                                    <span v-if="addon.price != null">
                                                        {{ formatPrice(addon.price) }}
                                                        <span v-if="addon.unit" class="text-xs text-gray-400 font-normal">/ {{ addon.unit }}</span>
                                                    </span>
                                                    <span v-else class="text-xs italic text-gray-400">A cotizar</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="!addons.length" class="text-center py-8">
                            <p class="text-sm text-gray-400">No hay servicios adicionales disponibles</p>
                        </div>
                    </div>
                </div>

                <!-- ============ STEP 4: Confirmar ============ -->
                <div v-show="step === 4" class="space-y-6">
                    <!-- Summary -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-800 mb-4">Resumen de tu cotización</h3>

                        <!-- Event info -->
                        <div v-if="form.event_type_id || form.event_date || form.event_venue" class="mb-4 rounded-lg bg-gray-50 p-4">
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Evento</h4>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div v-if="form.event_type_id">
                                    <span class="text-gray-500">Tipo:</span>
                                    <span class="ml-1 text-gray-800">{{ eventTypes.find(et => et.id == form.event_type_id)?.name }}</span>
                                </div>
                                <div v-if="form.event_date">
                                    <span class="text-gray-500">Fecha:</span>
                                    <span class="ml-1 text-gray-800">{{ form.event_date }}</span>
                                </div>
                                <div v-if="form.event_venue">
                                    <span class="text-gray-500">Lugar:</span>
                                    <span class="ml-1 text-gray-800">{{ form.event_venue }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Horas:</span>
                                    <span class="ml-1 text-gray-800">{{ form.hours_contracted }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="divide-y divide-gray-100">
                            <div v-for="item in buildItems()" :key="item.description" class="flex items-center justify-between py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ item.description }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ item.type === 'package' ? 'Paquete' : 'Extra' }}
                                        <span v-if="item.quantity > 1"> · Cantidad: {{ item.quantity }}</span>
                                    </p>
                                </div>
                                <p class="text-sm font-semibold text-gray-700">
                                    <span v-if="item.price_pending" class="text-xs italic text-gray-400">A cotizar</span>
                                    <span v-else>{{ formatPrice(item.quantity * item.unit_price) }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                            <span class="text-base font-bold text-gray-800">Total estimado</span>
                            <span class="text-xl font-bold text-amber-600">{{ formatPrice(subtotal) }}</span>
                        </div>
                    </div>

                    <!-- Observations -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <InputLabel for="observations" value="Observaciones o comentarios (opcional)" />
                        <textarea
                            id="observations"
                            v-model="form.observations"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm"
                            placeholder="¿Tienes algún requerimiento especial?"
                        />
                        <InputError class="mt-1" :message="form.errors.observations" />
                    </div>

                    <div class="rounded-lg bg-blue-50 border border-blue-200 p-4">
                        <p class="text-sm text-blue-700">
                            <strong>Nota:</strong> Al enviar tu solicitud, nuestro equipo la revisará y te contactará para confirmar los detalles y disponibilidad.
                        </p>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-8 flex items-center justify-between">
                    <button
                        v-if="step > 1"
                        type="button"
                        @click="prevStep"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                        Anterior
                    </button>
                    <div v-else />

                    <button
                        v-if="step < totalSteps"
                        type="button"
                        @click="nextStep"
                        :disabled="!canNext"
                        :class="[
                            'inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold shadow-sm transition-all',
                            canNext
                                ? 'bg-amber-500 text-white hover:bg-amber-600'
                                : 'bg-gray-200 text-gray-400 cursor-not-allowed',
                        ]"
                    >
                        Siguiente
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>

                    <button
                        v-else
                        type="submit"
                        :disabled="form.processing || buildItems().length === 0"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                        Enviar Solicitud
                    </button>
                </div>
            </form>
        </div>
    </ClientLayout>
</template>
