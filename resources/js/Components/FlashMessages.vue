<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const messages = ref([]);
let idCounter = 0;

const addMessage = (type, text) => {
    if (!text) return;
    const id = ++idCounter;
    messages.value.push({ id, type, text });
    setTimeout(() => removeMessage(id), 4000);
};

const removeMessage = (id) => {
    messages.value = messages.value.filter(m => m.id !== id);
};

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) addMessage('success', flash.success);
        if (flash?.error) addMessage('error', flash.error);
    },
    { immediate: true }
);
</script>

<template>
    <div class="fixed top-4 right-4 z-[100] space-y-2 pointer-events-none">
        <TransitionGroup
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform translate-x-full opacity-0"
            enter-to-class="transform translate-x-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="transform translate-x-0 opacity-100"
            leave-to-class="transform translate-x-full opacity-0"
        >
            <div
                v-for="msg in messages"
                :key="msg.id"
                :class="[
                    'pointer-events-auto flex items-center gap-3 rounded-lg px-4 py-3 shadow-lg min-w-[300px] max-w-md',
                    msg.type === 'success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : '',
                    msg.type === 'error' ? 'bg-red-50 border border-red-200 text-red-800' : '',
                ]"
            >
                <!-- Success icon -->
                <svg v-if="msg.type === 'success'" class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <!-- Error icon -->
                <svg v-if="msg.type === 'error'" class="h-5 w-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>

                <p class="text-sm font-medium">{{ msg.text }}</p>

                <button
                    @click="removeMessage(msg.id)"
                    class="ml-auto text-gray-400 hover:text-gray-600"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
