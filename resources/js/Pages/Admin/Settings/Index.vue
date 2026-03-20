<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    settingGroups: Object,
    groupLabels: Object,
});

const groupIcons = {
    general: `<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a1.897 1.897 0 0 1-.592-1.349c0-1.036.84-1.875 1.875-1.875h13.293c1.036 0 1.875.84 1.875 1.875 0 .519-.21.989-.592 1.349m-17.859 0h17.859" />`,
    social: `<path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />`,
    branding: `<path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008Z" />`,
    quotation: `<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />`,
    email: `<path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />`,
};

const groups = computed(() => Object.keys(props.settingGroups));
const activeGroup = ref(groups.value[0] || 'general');

// Track which secret fields are visible
const visibleSecrets = ref({});

// Build form data from all settings
const buildFormData = () => {
    const data = {};
    for (const [, settings] of Object.entries(props.settingGroups)) {
        for (const setting of settings) {
            if (setting.type !== 'image') {
                data[setting.key] = setting.value ?? '';
            }
        }
    }
    return data;
};

const form = useForm({
    settings: buildFormData(),
});

const submit = () => {
    form.put(route('admin.settings.update'));
};

// Image upload
const uploadingKey = ref(null);

const handleImageUpload = (event, key) => {
    const file = event.target.files[0];
    if (!file) return;

    uploadingKey.value = key;
    router.post(route('admin.settings.upload-image'), {
        key: key,
        image: file,
    }, {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => {
            uploadingKey.value = null;
            event.target.value = '';
        },
    });
};

const deleteImage = (key) => {
    if (!confirm('¿Eliminar esta imagen?')) return;
    router.delete(route('admin.settings.delete-image'), {
        data: { key },
        preserveScroll: true,
    });
};

const getImageUrl = (value) => {
    if (!value) return null;
    return `/storage/${value}`;
};

const canEdit = computed(() => {
    const permissions = usePage().props.auth?.permissions || [];
    return permissions.includes('settings.edit');
});

import { usePage } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Ajustes" />

    <AdminLayout title="Ajustes">
        <div class="mx-auto max-w-4xl">
            <!-- Group Tabs -->
            <div class="mb-6 flex flex-wrap gap-2">
                <button
                    v-for="group in groups"
                    :key="group"
                    @click="activeGroup = group"
                    :class="[
                        'flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-all',
                        activeGroup === group
                            ? 'bg-amber-500 text-white shadow-sm'
                            : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50',
                    ]"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         v-html="groupIcons[group]" />
                    {{ groupLabels[group] || group }}
                </button>
            </div>

            <!-- Settings Form -->
            <form @submit.prevent="submit" class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <template v-for="group in groups" :key="group">
                    <div v-show="activeGroup === group" class="divide-y divide-gray-100">
                        <!-- Group Header -->
                        <div class="px-6 py-4 bg-gray-50/50 rounded-t-xl">
                            <h3 class="text-base font-semibold text-gray-800">{{ groupLabels[group] || group }}</h3>
                            <p class="mt-0.5 text-sm text-gray-500">
                                <template v-if="group === 'general'">Información general de tu negocio</template>
                                <template v-else-if="group === 'social'">Links a tus redes sociales</template>
                                <template v-else-if="group === 'branding'">Colores y personalización visual</template>
                                <template v-else-if="group === 'quotation'">Configuración para cotizaciones</template>
                                <template v-else-if="group === 'email'">Configuración de envío de correos con Resend</template>
                            </p>
                        </div>

                        <!-- Fields -->
                        <div
                            v-for="setting in settingGroups[group]"
                            :key="setting.key"
                            class="flex flex-col gap-2 px-6 py-4 sm:flex-row sm:items-center sm:gap-6"
                        >
                            <div class="sm:w-1/3">
                                <InputLabel :value="setting.label" class="text-sm font-medium text-gray-700" />
                            </div>
                            <div class="flex-1">
                                <!-- Image field -->
                                <template v-if="setting.type === 'image'">
                                    <div class="flex items-center gap-4">
                                        <div v-if="setting.value" class="relative">
                                            <img
                                                :src="getImageUrl(setting.value)"
                                                :alt="setting.label"
                                                class="h-16 w-16 rounded-lg object-cover border border-gray-200"
                                            />
                                            <button
                                                v-if="canEdit"
                                                type="button"
                                                @click="deleteImage(setting.key)"
                                                class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white text-xs hover:bg-red-600"
                                                title="Eliminar"
                                            >
                                                ×
                                            </button>
                                        </div>
                                        <div v-else class="flex h-16 w-16 items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5a1.5 1.5 0 0 0 1.5-1.5V5.25a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v14.25a1.5 1.5 0 0 0 1.5 1.5Z" />
                                            </svg>
                                        </div>
                                        <label v-if="canEdit" class="cursor-pointer">
                                            <span class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                                {{ uploadingKey === setting.key ? 'Subiendo...' : 'Cambiar imagen' }}
                                            </span>
                                            <input
                                                type="file"
                                                accept="image/*"
                                                class="hidden"
                                                :disabled="uploadingKey === setting.key"
                                                @change="handleImageUpload($event, setting.key)"
                                            />
                                        </label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-400">PNG, JPG o SVG. Máximo 2MB.</p>
                                </template>

                                <!-- Color field -->
                                <template v-else-if="setting.key.includes('color')">
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="color"
                                            v-model="form.settings[setting.key]"
                                            class="h-10 w-14 cursor-pointer rounded border border-gray-200 p-1"
                                            :disabled="!canEdit"
                                        />
                                        <TextInput
                                            v-model="form.settings[setting.key]"
                                            type="text"
                                            class="w-32 font-mono text-sm"
                                            :disabled="!canEdit"
                                        />
                                    </div>
                                </template>

                                <!-- Secret field (API keys) -->
                                <template v-else-if="setting.type === 'secret'">
                                    <div class="flex items-center gap-2">
                                        <div class="relative flex-1">
                                            <TextInput
                                                v-model="form.settings[setting.key]"
                                                :type="visibleSecrets[setting.key] ? 'text' : 'password'"
                                                class="w-full pr-10 font-mono text-sm"
                                                :disabled="!canEdit"
                                                :placeholder="setting.hasValue ? '••••••••••••••••••••' : 'No configurada'"
                                                autocomplete="new-password"
                                            />
                                            <button
                                                type="button"
                                                @click="visibleSecrets[setting.key] = !visibleSecrets[setting.key]"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                            >
                                                <svg v-if="!visibleSecrets[setting.key]" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p v-if="setting.key === 'resend_api_key'" class="mt-1 text-xs text-gray-400">
                                        Obtén tu API key en <a href="https://resend.com/api-keys" target="_blank" class="text-amber-600 hover:underline">resend.com/api-keys</a>. Deja vacío para no modificar.
                                    </p>
                                </template>

                                <!-- Text field -->
                                <template v-else>
                                    <TextInput
                                        v-model="form.settings[setting.key]"
                                        :type="setting.key.includes('email') ? 'email' : 'text'"
                                        class="w-full"
                                        :disabled="!canEdit"
                                        :placeholder="setting.label"
                                    />
                                </template>

                                <InputError :message="form.errors[`settings.${setting.key}`]" class="mt-1" />
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Save button -->
                <div v-if="canEdit" class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4">
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600 font-medium">
                        ✓ Guardado
                    </p>
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
