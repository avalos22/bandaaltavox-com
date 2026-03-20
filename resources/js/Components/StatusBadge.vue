<script setup>
defineProps({
    active: Boolean,
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium',
            active
                ? 'bg-emerald-50 text-emerald-700'
                : 'bg-red-50 text-red-700'
        ]"
    >
        <span :class="['h-1.5 w-1.5 rounded-full', active ? 'bg-emerald-500' : 'bg-red-500']" />
        {{ active ? 'Activo' : 'Inactivo' }}
    </span>
</template>
