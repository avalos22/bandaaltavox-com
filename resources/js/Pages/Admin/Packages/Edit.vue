<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    editPackage: Object,
    eventTypes: Array,
});

const form = useForm({
    name: props.editPackage.name,
    description: props.editPackage.description || '',
    price: props.editPackage.price,
    duration_hours: props.editPackage.duration_hours,
    is_active: props.editPackage.is_active,
    is_featured: props.editPackage.is_featured,
    event_types: props.editPackage.event_types?.map(et => et.id) || [],
    includes: props.editPackage.includes?.map(inc => ({
        description: inc.description,
        is_highlighted: inc.is_highlighted,
    })) || [],
});

const addInclude = () => {
    form.includes.push({ description: '', is_highlighted: false });
};

const removeInclude = (index) => {
    form.includes.splice(index, 1);
};

const moveInclude = (index, direction) => {
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= form.includes.length) return;
    const temp = form.includes[index];
    form.includes[index] = form.includes[newIndex];
    form.includes[newIndex] = temp;
};

const submit = () => {
    form.put(route('admin.packages.update', props.editPackage.id));
};

// Image upload handling
const uploadingImage = ref(false);

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    uploadingImage.value = true;
    router.post(route('admin.packages.upload-image', props.editPackage.id), {
        image: file,
    }, {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => {
            uploadingImage.value = false;
            event.target.value = '';
        },
    });
};

const deleteImage = () => {
    if (!confirm('¿Eliminar la imagen del paquete?')) return;
    router.delete(route('admin.packages.delete-image', props.editPackage.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Editar: ${editPackage.name}`" />

    <AdminLayout title="Editar Paquete">
        <!-- Breadcrumb -->
        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500">
            <Link :href="route('admin.packages.index')" class="hover:text-amber-600 transition-colors">Paquetes</Link>
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
            <span class="text-gray-800 font-medium">{{ editPackage.name }}</span>
        </nav>

        <div class="mx-auto max-w-3xl">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Image Section -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-gray-800 mb-4">Imagen del paquete</h3>
                    <div class="flex items-center gap-4">
                        <div v-if="editPackage.image" class="relative">
                            <img
                                :src="`/storage/${editPackage.image}`"
                                :alt="editPackage.name"
                                class="h-24 w-36 rounded-lg object-cover border border-gray-200"
                            />
                            <button
                                type="button"
                                @click="deleteImage"
                                class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white text-xs hover:bg-red-600"
                                title="Eliminar"
                            >
                                ×
                            </button>
                        </div>
                        <div v-else class="flex h-24 w-36 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v14.25a1.5 1.5 0 0 0 1.5 1.5Z" />
                            </svg>
                        </div>
                        <label class="cursor-pointer">
                            <span class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                {{ uploadingImage ? 'Subiendo...' : 'Cambiar imagen' }}
                            </span>
                            <input type="file" accept="image/*" class="hidden" :disabled="uploadingImage" @change="handleImageUpload" />
                        </label>
                    </div>
                    <p class="mt-2 text-xs text-gray-400">PNG, JPG o SVG. Máximo 2MB.</p>
                </div>

                <!-- Basic Info -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
                    <h3 class="text-base font-semibold text-gray-800">Información del paquete</h3>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <InputLabel for="name" value="Nombre del paquete" />
                            <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                            <InputError class="mt-1" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="price" value="Precio (MXN)" />
                            <div class="relative mt-1">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm">$</span>
                                <TextInput id="price" v-model="form.price" type="number" step="100" min="0" class="block w-full pl-7" required />
                            </div>
                            <InputError class="mt-1" :message="form.errors.price" />
                        </div>

                        <div>
                            <InputLabel for="duration" value="Duración (horas)" />
                            <TextInput id="duration" v-model="form.duration_hours" type="number" min="1" class="mt-1 block w-full" required />
                            <InputError class="mt-1" :message="form.errors.duration_hours" />
                        </div>

                        <div class="sm:col-span-2">
                            <InputLabel for="description" value="Descripción" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-400 focus:ring-amber-400 text-sm"
                            />
                            <InputError class="mt-1" :message="form.errors.description" />
                        </div>

                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" v-model="form.is_active" class="h-4 w-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                                <span class="text-sm text-gray-700">Activo</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" v-model="form.is_featured" class="h-4 w-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                                <span class="text-sm text-gray-700">Destacado</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Event Types -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-gray-800 mb-3">Tipos de evento recomendados</h3>
                    <p class="text-sm text-gray-500 mb-4">Selecciona para qué tipo de eventos es ideal este paquete</p>

                    <div class="flex flex-wrap gap-2">
                        <label
                            v-for="et in eventTypes"
                            :key="et.id"
                            :class="[
                                'flex items-center gap-2 rounded-lg border px-3 py-2 text-sm cursor-pointer transition-all',
                                form.event_types.includes(et.id)
                                    ? 'border-amber-400 bg-amber-50 text-amber-800'
                                    : 'border-gray-200 text-gray-600 hover:bg-gray-50',
                            ]"
                        >
                            <input type="checkbox" :value="et.id" v-model="form.event_types" class="hidden" />
                            <span>{{ et.name }}</span>
                        </label>
                    </div>
                </div>

                <!-- Includes -->
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-semibold text-gray-800">¿Qué incluye?</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Lista de servicios incluidos en este paquete</p>
                        </div>
                        <button
                            type="button"
                            @click="addInclude"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Agregar
                        </button>
                    </div>

                    <div v-if="form.includes.length === 0" class="rounded-lg border-2 border-dashed border-gray-200 p-6 text-center">
                        <p class="text-sm text-gray-400">Aún no has agregado incluidos.</p>
                    </div>

                    <div v-else class="space-y-2">
                        <div
                            v-for="(item, index) in form.includes"
                            :key="index"
                            class="flex items-center gap-2 rounded-lg border border-gray-100 bg-gray-50/50 p-2"
                        >
                            <div class="flex flex-col">
                                <button type="button" @click="moveInclude(index, -1)" :disabled="index === 0" class="text-gray-400 hover:text-gray-600 disabled:opacity-30">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" /></svg>
                                </button>
                                <button type="button" @click="moveInclude(index, 1)" :disabled="index === form.includes.length - 1" class="text-gray-400 hover:text-gray-600 disabled:opacity-30">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                </button>
                            </div>

                            <TextInput
                                v-model="item.description"
                                type="text"
                                class="flex-1 text-sm"
                                placeholder="Ej: Servicio de audio e iluminación profesional"
                                required
                            />

                            <label class="flex items-center gap-1.5 shrink-0 cursor-pointer" title="Destacar">
                                <input type="checkbox" v-model="item.is_highlighted" class="h-3.5 w-3.5 rounded border-gray-300 text-amber-500 focus:ring-amber-400" />
                                <svg class="h-4 w-4" :class="item.is_highlighted ? 'text-amber-500' : 'text-gray-300'" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                </svg>
                            </label>

                            <button type="button" @click="removeInclude(index)" class="shrink-0 text-gray-400 hover:text-red-500 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <InputError class="mt-2" :message="form.errors.includes" />
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3">
                    <Link
                        :href="route('admin.packages.index')"
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
