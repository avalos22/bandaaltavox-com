<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    editAddon: Object,
    categories: Object,
});

const unitOptions = ['por hora', 'por evento', 'por pieza'];

const form = useForm({
    name: props.editAddon.name,
    category: props.editAddon.category,
    description: props.editAddon.description || '',
    price: props.editAddon.price,
    unit: props.editAddon.unit,
    is_active: props.editAddon.is_active,
});

const submit = () => {
    form.put(route('admin.addons.update', props.editAddon.id));
};
</script>

<template>
    <Head :title="`Editar: ${editAddon.name}`" />

    <AdminLayout title="Editar Servicio Adicional">
        <!-- Breadcrumb -->
        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
            <Link :href="route('admin.packages.index')" class="hover:text-amber-600 transition-colors">Paquetes</Link>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <Link :href="route('admin.addons.index')" class="hover:text-amber-600 transition-colors">Servicios Adicionales</Link>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span class="text-gray-800 font-medium">{{ editAddon.name }}</span>
        </nav>

        <div class="mx-auto max-w-2xl">
            <form @submit.prevent="submit" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                <div>
                    <InputLabel for="name" value="Nombre del servicio" />
                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                    <InputError class="mt-1" :message="form.errors.name" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <InputLabel for="category" value="Categoría" />
                        <select
                            id="category"
                            v-model="form.category"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm"
                        >
                            <option v-for="(label, key) in categories" :key="key" :value="key">{{ label }}</option>
                        </select>
                        <InputError class="mt-1" :message="form.errors.category" />
                    </div>

                    <div>
                        <InputLabel for="unit" value="Unidad" />
                        <select
                            id="unit"
                            v-model="form.unit"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm"
                        >
                            <option v-for="u in unitOptions" :key="u" :value="u">{{ u }}</option>
                        </select>
                        <InputError class="mt-1" :message="form.errors.unit" />
                    </div>
                </div>

                <div>
                    <InputLabel for="price" value="Precio (MXN)" />
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm">$</span>
                        <TextInput id="price" v-model="form.price" type="number" step="100" min="0" class="block w-full pl-7" required />
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Usa $0 si el precio se define al cotizar</p>
                    <InputError class="mt-1" :message="form.errors.price" />
                </div>

                <div>
                    <InputLabel for="description" value="Descripción (opcional)" />
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm"
                    />
                    <InputError class="mt-1" :message="form.errors.description" />
                </div>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                    <span class="text-sm text-gray-700">Activo</span>
                </label>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                    <Link
                        :href="route('admin.addons.index')"
                        class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                    >
                        Cancelar
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 transition-colors disabled:opacity-50"
                    >
                        {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
