<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array, // pagination links from Laravel
});
</script>

<template>
    <nav v-if="links && links.length > 3" class="flex items-center justify-between border-t border-gray-100 pt-4">
        <div class="flex gap-1">
            <template v-for="link in links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    :class="[
                        'inline-flex h-9 min-w-[36px] items-center justify-center rounded-lg px-3 text-sm transition-colors',
                        link.active
                            ? 'bg-amber-500 text-white font-medium'
                            : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'
                    ]"
                    v-html="link.label"
                    preserve-scroll
                />
                <span
                    v-else
                    class="inline-flex h-9 min-w-[36px] items-center justify-center rounded-lg px-3 text-sm text-gray-300 bg-gray-50 border border-gray-100"
                    v-html="link.label"
                />
            </template>
        </div>
    </nav>
</template>
