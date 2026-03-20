<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Mi Perfil" />

    <AdminLayout title="Mi Perfil">
        <div class="mx-auto max-w-3xl space-y-6">
            <div class="bg-white p-4 shadow-sm rounded-xl sm:p-8 border border-gray-100">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="bg-white p-4 shadow-sm rounded-xl sm:p-8 border border-gray-100">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="bg-white p-4 shadow-sm rounded-xl sm:p-8 border border-gray-100">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </AdminLayout>
</template>
