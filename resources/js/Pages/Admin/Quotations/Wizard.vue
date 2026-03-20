<script setup>
import { ref, computed, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    eventTypes: Array,
    packages: Array,
    addons: Array,
    addonCategories: Object,
    defaults: Object,
});

const step = ref(1);
const totalSteps = 5;

const form = useForm({
    // Step 1: Evento
    event_type_id: '',
    event_date: '',
    event_time_start: '18:00',
    event_time_end: '23:00',
    event_venue: '',
    event_city: '',
    event_is_outdoor: false,
    guest_count: '',
    hours_contracted: 5,
    // Step 2: Paquete (stored as items)
    // Step 3: Add-ons (stored as items)
    // Step 4: Cliente
    client_name: '',
    client_email: '',
    client_phone: '',
    client_address: '',
    // Step 5: Resumen
    observations: '',
    discount: 0,
    // Computed items
    items: [],
});

// --- Package selection ---
const selectedPackageId = ref(null);
const selectedPackage = computed(() =>
    props.packages.find(p => p.id === selectedPackageId.value)
);

// --- Addon selection ---
const selectedAddonIds = ref([]);
const addonQuantities = ref({});

const toggleAddon = (addonId) => {
    const idx = selectedAddonIds.value.indexOf(addonId);
    if (idx === -1) {
        selectedAddonIds.value.push(addonId);
        if (!addonQuantities.value[addonId]) {
            addonQuantities.value[addonId] = 1;
        }
    } else {
        selectedAddonIds.value.splice(idx, 1);
    }
};

// --- Smart suggestions ---
const suggestedAddonIds = computed(() => {
    const ids = [];
    // If outdoor, suggest equipment category addons (like lona)
    if (form.event_is_outdoor) {
        props.addons.forEach(a => {
            if (a.category === 'equipment') ids.push(a.id);
        });
    }
    // If many guests, suggest extra equipment
    if (form.guest_count >= 200) {
        props.addons.forEach(a => {
            if (a.category === 'equipment' && !ids.includes(a.id)) ids.push(a.id);
        });
    }
    return ids;
});

// --- Build items for submission ---
const buildItems = () => {
    const items = [];
    // Package
    if (selectedPackage.value) {
        items.push({
            type: 'package',
            reference_id: selectedPackage.value.id,
            description: `Paquete ${selectedPackage.value.name} (${form.hours_contracted} hrs)`,
            quantity: 1,
            unit_price: parseFloat(selectedPackage.value.price),
        });
    }
    // Addons
    selectedAddonIds.value.forEach(id => {
        const addon = props.addons.find(a => a.id === id);
        if (addon) {
            const qty = addonQuantities.value[id] || 1;
            items.push({
                type: 'addon',
                reference_id: addon.id,
                description: addon.name,
                quantity: qty,
                unit_price: parseFloat(addon.price),
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

const total = computed(() => {
    return Math.max(0, subtotal.value - (parseFloat(form.discount) || 0));
});

const depositAmount = computed(() => {
    return Math.round(total.value * (props.defaults.deposit_percentage / 100));
});

// --- Addon groups ---
const groupedAddons = computed(() => {
    const groups = {};
    props.addons.forEach(addon => {
        const cat = addon.category;
        if (!groups[cat]) groups[cat] = [];
        groups[cat].push(addon);
    });
    return groups;
});

// --- Navigation ---
const canNext = computed(() => {
    if (step.value === 1) {
        return form.hours_contracted >= 1;
    }
    if (step.value === 2) {
        return selectedPackageId.value !== null;
    }
    if (step.value === 4) {
        return form.client_name.trim().length > 0;
    }
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
    form.post(route('admin.quotations.store'));
};

// Step labels
const steps = [
    { num: 1, label: 'Evento' },
    { num: 2, label: 'Paquete' },
    { num: 3, label: 'Extras' },
    { num: 4, label: 'Cliente' },
    { num: 5, label: 'Resumen' },
];

const formatPrice = (price) => {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 0 }).format(price);
};
</script>

<template>
    <Head title="Nueva Cotización" />

    <AdminLayout title="Nueva Cotización">
        <!-- Breadcrumb -->
        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
            <Link :href="route('admin.quotations.index')" class="hover:text-amber-600 transition-colors">Cotizaciones</Link>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span class="text-gray-800 font-medium">Nueva</span>
        </nav>

        <!-- Stepper -->
        <div class="mb-8">
            <div class="flex items-center justify-between max-w-2xl mx-auto">
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
                <!-- ============ STEP 1: Evento ============ -->
                <div v-show="step === 1" class="space-y-6">
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
                                    <option v-for="et in eventTypes" :key="et.id" :value="et.id">
                                        {{ et.name }}
                                    </option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.event_type_id" />
                            </div>

                            <div>
                                <InputLabel for="event_date" value="Fecha del evento" />
                                <TextInput id="event_date" v-model="form.event_date" type="date" class="mt-1 block w-full" />
                                <InputError class="mt-1" :message="form.errors.event_date" />
                            </div>

                            <div>
                                <InputLabel for="guest_count" value="Número de invitados (aprox.)" />
                                <TextInput id="guest_count" v-model="form.guest_count" type="number" min="1" class="mt-1 block w-full" placeholder="100" />
                                <InputError class="mt-1" :message="form.errors.guest_count" />
                            </div>

                            <div>
                                <InputLabel for="event_time_start" value="Hora inicio" />
                                <TextInput id="event_time_start" v-model="form.event_time_start" type="time" class="mt-1 block w-full" />
                                <InputError class="mt-1" :message="form.errors.event_time_start" />
                            </div>

                            <div>
                                <InputLabel for="event_time_end" value="Hora fin" />
                                <TextInput id="event_time_end" v-model="form.event_time_end" type="time" class="mt-1 block w-full" />
                                <InputError class="mt-1" :message="form.errors.event_time_end" />
                            </div>

                            <div>
                                <InputLabel for="hours_contracted" value="Horas contratadas" />
                                <TextInput id="hours_contracted" v-model="form.hours_contracted" type="number" min="1" class="mt-1 block w-full" required />
                                <InputError class="mt-1" :message="form.errors.hours_contracted" />
                            </div>

                            <div>
                                <InputLabel for="event_city" value="Ciudad" />
                                <TextInput id="event_city" v-model="form.event_city" type="text" class="mt-1 block w-full" placeholder="Guadalajara" />
                                <InputError class="mt-1" :message="form.errors.event_city" />
                            </div>

                            <div class="sm:col-span-2">
                                <InputLabel for="event_venue" value="Lugar / Salón" />
                                <TextInput id="event_venue" v-model="form.event_venue" type="text" class="mt-1 block w-full" placeholder="Salón de eventos Los Arcos" />
                                <InputError class="mt-1" :message="form.errors.event_venue" />
                            </div>

                            <div class="sm:col-span-2">
                                <label class="flex items-center gap-3 cursor-pointer rounded-lg border p-3 transition-colors"
                                    :class="form.event_is_outdoor ? 'border-amber-400 bg-amber-50' : 'border-gray-200 hover:bg-gray-50'"
                                >
                                    <input type="checkbox" v-model="form.event_is_outdoor" class="h-4 w-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Evento al aire libre</span>
                                        <p class="text-xs text-gray-500 mt-0.5">Se recomendarán extras como lona/techado</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ STEP 2: Paquete ============ -->
                <div v-show="step === 2" class="space-y-6">
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-800 mb-1">Selecciona un paquete</h3>
                        <p class="text-sm text-gray-500 mb-4">Elige el paquete base para esta cotización</p>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <button
                                v-for="pkg in packages"
                                :key="pkg.id"
                                type="button"
                                @click="selectedPackageId = pkg.id"
                                :class="[
                                    'relative rounded-xl border-2 p-5 text-left transition-all',
                                    selectedPackageId === pkg.id
                                        ? 'border-amber-400 bg-amber-50 ring-2 ring-amber-400/20'
                                        : 'border-gray-200 hover:border-gray-300 hover:shadow-sm',
                                ]"
                            >
                                <!-- Selected check -->
                                <div
                                    v-if="selectedPackageId === pkg.id"
                                    class="absolute top-3 right-3 flex h-6 w-6 items-center justify-center rounded-full bg-amber-500 text-white"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>

                                <h4 class="text-lg font-bold text-gray-800">{{ pkg.name }}</h4>
                                <p class="text-2xl font-bold text-amber-600 mt-1">{{ formatPrice(pkg.price) }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ pkg.duration_hours }} horas incluidas</p>

                                <div v-if="pkg.includes?.length" class="mt-3 space-y-1">
                                    <div
                                        v-for="inc in pkg.includes"
                                        :key="inc.id"
                                        class="flex items-start gap-2 text-sm text-gray-600"
                                    >
                                        <svg class="h-4 w-4 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                        <span :class="{ 'font-medium': inc.is_highlighted }">{{ inc.description }}</span>
                                    </div>
                                </div>
                            </button>
                        </div>
                        <InputError class="mt-2" :message="form.errors.items" />
                    </div>
                </div>

                <!-- ============ STEP 3: Extras / Add-ons ============ -->
                <div v-show="step === 3" class="space-y-6">
                    <!-- Smart suggestion banner -->
                    <div
                        v-if="suggestedAddonIds.length > 0"
                        class="rounded-xl border border-amber-200 bg-amber-50 p-4"
                    >
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-amber-800">Sugerencias para tu evento</p>
                                <p v-if="form.event_is_outdoor" class="text-xs text-amber-700 mt-0.5">
                                    Al ser un evento al aire libre, te recomendamos considerar equipamiento extra como lona/techado.
                                </p>
                                <p v-if="form.guest_count >= 200" class="text-xs text-amber-700 mt-0.5">
                                    Para {{ form.guest_count }} invitados, considera equipo de audio adicional.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-for="(addonsInGroup, category) in groupedAddons" :key="category" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-800 mb-3">
                            {{ addonCategories[category] || category }}
                        </h3>

                        <div class="space-y-2">
                            <button
                                v-for="addon in addonsInGroup"
                                :key="addon.id"
                                type="button"
                                @click="toggleAddon(addon.id)"
                                :class="[
                                    'flex w-full items-center gap-4 rounded-lg border-2 p-4 text-left transition-all',
                                    selectedAddonIds.includes(addon.id)
                                        ? 'border-amber-400 bg-amber-50'
                                        : suggestedAddonIds.includes(addon.id)
                                            ? 'border-amber-200 bg-amber-50/50 hover:border-amber-300'
                                            : 'border-gray-200 hover:border-gray-300',
                                ]"
                            >
                                <!-- Checkbox -->
                                <div
                                    :class="[
                                        'flex h-5 w-5 shrink-0 items-center justify-center rounded border-2 transition-colors',
                                        selectedAddonIds.includes(addon.id)
                                            ? 'border-amber-500 bg-amber-500'
                                            : 'border-gray-300',
                                    ]"
                                >
                                    <svg v-if="selectedAddonIds.includes(addon.id)" class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-800">{{ addon.name }}</span>
                                        <span
                                            v-if="suggestedAddonIds.includes(addon.id)"
                                            class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700"
                                        >
                                            Sugerido
                                        </span>
                                    </div>
                                    <p v-if="addon.description" class="text-xs text-gray-500 mt-0.5">{{ addon.description }}</p>
                                </div>

                                <div class="text-right shrink-0">
                                    <p class="text-sm font-bold text-amber-600">{{ formatPrice(addon.price) }}</p>
                                    <p v-if="addon.unit" class="text-[10px] text-gray-400">por {{ addon.unit }}</p>
                                </div>
                            </button>

                            <!-- Quantity control shown for selected addons -->
                            <template v-for="addon in addonsInGroup" :key="'qty-' + addon.id">
                                <div
                                    v-if="selectedAddonIds.includes(addon.id) && addon.unit"
                                    class="ml-9 flex items-center gap-3 pl-4"
                                >
                                    <span class="text-xs text-gray-500">Cantidad:</span>
                                    <div class="flex items-center gap-1">
                                        <button
                                            type="button"
                                            @click.stop="addonQuantities[addon.id] = Math.max(1, (addonQuantities[addon.id] || 1) - 1)"
                                            class="flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-500 hover:bg-gray-50"
                                        >−</button>
                                        <span class="w-8 text-center text-sm font-medium">{{ addonQuantities[addon.id] || 1 }}</span>
                                        <button
                                            type="button"
                                            @click.stop="addonQuantities[addon.id] = (addonQuantities[addon.id] || 1) + 1"
                                            class="flex h-7 w-7 items-center justify-center rounded-md border border-gray-200 text-gray-500 hover:bg-gray-50"
                                        >+</button>
                                    </div>
                                    <span class="text-xs text-gray-500 ml-2">
                                        = {{ formatPrice(addon.price * (addonQuantities[addon.id] || 1)) }}
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <p v-if="!Object.keys(groupedAddons).length" class="rounded-xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-500">
                        No hay servicios adicionales disponibles
                    </p>
                </div>

                <!-- ============ STEP 4: Cliente ============ -->
                <div v-show="step === 4" class="space-y-6">
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                        <h3 class="text-base font-semibold text-gray-800">Datos del cliente</h3>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <InputLabel for="client_name" value="Nombre completo *" />
                                <TextInput id="client_name" v-model="form.client_name" type="text" class="mt-1 block w-full" required placeholder="Juan Pérez" />
                                <InputError class="mt-1" :message="form.errors.client_name" />
                            </div>

                            <div>
                                <InputLabel for="client_email" value="Correo electrónico" />
                                <TextInput id="client_email" v-model="form.client_email" type="email" class="mt-1 block w-full" placeholder="juan@email.com" />
                                <InputError class="mt-1" :message="form.errors.client_email" />
                            </div>

                            <div>
                                <InputLabel for="client_phone" value="Teléfono" />
                                <TextInput id="client_phone" v-model="form.client_phone" type="tel" class="mt-1 block w-full" placeholder="33 1234 5678" />
                                <InputError class="mt-1" :message="form.errors.client_phone" />
                            </div>

                            <div class="sm:col-span-2">
                                <InputLabel for="client_address" value="Dirección" />
                                <TextInput id="client_address" v-model="form.client_address" type="text" class="mt-1 block w-full" placeholder="Calle, Colonia, Ciudad" />
                                <InputError class="mt-1" :message="form.errors.client_address" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============ STEP 5: Resumen ============ -->
                <div v-show="step === 5" class="space-y-6">
                    <!-- Event Summary -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-800 mb-3">Resumen del Evento</h3>
                        <div class="grid gap-2 sm:grid-cols-2 text-sm">
                            <div v-if="form.event_type_id">
                                <span class="text-gray-500">Tipo:</span>
                                <span class="ml-1 font-medium text-gray-800">{{ eventTypes.find(e => e.id == form.event_type_id)?.name }}</span>
                            </div>
                            <div v-if="form.event_date">
                                <span class="text-gray-500">Fecha:</span>
                                <span class="ml-1 font-medium text-gray-800">{{ form.event_date }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Horario:</span>
                                <span class="ml-1 font-medium text-gray-800">{{ form.event_time_start }} - {{ form.event_time_end }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Horas:</span>
                                <span class="ml-1 font-medium text-gray-800">{{ form.hours_contracted }} hrs</span>
                            </div>
                            <div v-if="form.event_venue">
                                <span class="text-gray-500">Lugar:</span>
                                <span class="ml-1 font-medium text-gray-800">{{ form.event_venue }}</span>
                            </div>
                            <div v-if="form.guest_count">
                                <span class="text-gray-500">Invitados:</span>
                                <span class="ml-1 font-medium text-gray-800">~{{ form.guest_count }}</span>
                            </div>
                            <div v-if="form.event_is_outdoor" class="sm:col-span-2">
                                <span class="inline-flex items-center gap-1 text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full text-xs font-medium">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" /></svg>
                                    Aire libre
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Client Summary -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-base font-semibold text-gray-800 mb-3">Datos del Cliente</h3>
                        <div class="grid gap-2 sm:grid-cols-2 text-sm">
                            <div><span class="text-gray-500">Nombre:</span> <span class="ml-1 font-medium text-gray-800">{{ form.client_name }}</span></div>
                            <div v-if="form.client_email"><span class="text-gray-500">Email:</span> <span class="ml-1 font-medium text-gray-800">{{ form.client_email }}</span></div>
                            <div v-if="form.client_phone"><span class="text-gray-500">Teléfono:</span> <span class="ml-1 font-medium text-gray-800">{{ form.client_phone }}</span></div>
                            <div v-if="form.client_address"><span class="text-gray-500">Dirección:</span> <span class="ml-1 font-medium text-gray-800">{{ form.client_address }}</span></div>
                        </div>
                    </div>

                    <!-- Items table -->
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-base font-semibold text-gray-800">Desglose</h3>
                        </div>
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-6 py-3 text-left">Concepto</th>
                                    <th class="px-6 py-3 text-center">Cant.</th>
                                    <th class="px-6 py-3 text-right">Precio</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="(item, idx) in buildItems()" :key="idx">
                                    <td class="px-6 py-3 text-gray-800">{{ item.description }}</td>
                                    <td class="px-6 py-3 text-center text-gray-600">{{ item.quantity }}</td>
                                    <td class="px-6 py-3 text-right text-gray-600">{{ formatPrice(item.unit_price) }}</td>
                                    <td class="px-6 py-3 text-right font-medium text-gray-800">{{ formatPrice(item.quantity * item.unit_price) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="border-t border-gray-200 px-6 py-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-medium text-gray-800">{{ formatPrice(subtotal) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-500">Descuento</span>
                                    <div class="relative w-28">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-2 text-gray-400 text-xs">$</span>
                                        <input
                                            v-model="form.discount"
                                            type="number"
                                            min="0"
                                            step="100"
                                            class="w-full rounded-md border-gray-200 py-1 pl-5 pr-2 text-xs focus:border-amber-400 focus:ring-amber-400"
                                        />
                                    </div>
                                </div>
                                <span class="text-red-500">-{{ formatPrice(form.discount || 0) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2">
                                <span class="text-gray-800">Total</span>
                                <span class="text-amber-600">{{ formatPrice(total) }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Anticipo sugerido ({{ defaults.deposit_percentage }}%)</span>
                                <span>{{ formatPrice(depositAmount) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Observations -->
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <InputLabel for="observations" value="Observaciones (opcional)" />
                        <textarea
                            id="observations"
                            v-model="form.observations"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm"
                            placeholder="Notas adicionales para esta cotización..."
                        />
                    </div>
                </div>

                <!-- ============ Navigation Buttons ============ -->
                <div class="mt-8 flex items-center justify-between">
                    <button
                        v-if="step > 1"
                        type="button"
                        @click="prevStep"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                        Anterior
                    </button>
                    <div v-else />

                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('admin.quotations.index')"
                            class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            Cancelar
                        </Link>

                        <button
                            v-if="step < totalSteps"
                            type="button"
                            @click="nextStep"
                            :disabled="!canNext"
                            class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors disabled:opacity-50"
                        >
                            Siguiente
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>

                        <button
                            v-else
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors disabled:opacity-50"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            {{ form.processing ? 'Guardando...' : 'Crear Cotización' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
