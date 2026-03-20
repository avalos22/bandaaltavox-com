<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import FlashMessages from '@/Components/FlashMessages.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const sidebarOpen = ref(false);
const showUserMenu = ref(false);

const navigation = [
    { name: 'Inicio', href: 'client.dashboard', icon: 'home' },
    { name: 'Mis Cotizaciones', href: 'client.quotations', icon: 'clipboard' },
    { name: 'Mis Eventos', href: 'client.events', icon: 'calendar' },
    { name: 'Mis Contratos', href: 'client.contracts', icon: 'file' },
    { name: 'Mis Pagos', href: 'client.payments', icon: 'dollar' },
];

const isActive = (routeName) => {
    try {
        return route().current(routeName) || route().current(routeName + '.*');
    } catch {
        return false;
    }
};

const logout = () => {
    router.post(route('logout'));
};

defineProps({
    title: { type: String, default: '' },
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Mobile sidebar overlay -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" />

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 transform bg-slate-900 transition-transform duration-200 ease-in-out lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            ]"
        >
            <div class="flex h-16 items-center gap-3 px-6 border-b border-slate-800">
                <img src="/storage/images/logo-blanco-altavox.png" alt="Banda Alta Vox" class="h-9" />
                <div>
                    <p class="text-slate-400 text-xs">Mi Portal</p>
                </div>
            </div>

            <nav class="mt-4 px-3 space-y-1">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="route(item.href)"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                        isActive(item.href)
                            ? 'bg-amber-500/10 text-amber-400'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white',
                    ]"
                    @click="sidebarOpen = false"
                >
                    <!-- Home icon -->
                    <svg v-if="item.icon === 'home'" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <!-- Clipboard icon -->
                    <svg v-else-if="item.icon === 'clipboard'" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                    </svg>
                    <!-- Calendar icon -->
                    <svg v-else-if="item.icon === 'calendar'" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v9.75" />
                    </svg>
                    <!-- File icon -->
                    <svg v-else-if="item.icon === 'file'" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <!-- Dollar icon -->
                    <svg v-else-if="item.icon === 'dollar'" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>{{ item.name }}</span>
                </Link>
            </nav>

            <!-- User at bottom -->
            <div class="absolute bottom-0 left-0 right-0 border-t border-slate-800 p-3">
                <div class="flex items-center gap-3 rounded-lg px-3 py-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-700 text-white text-xs font-medium">
                        {{ user?.name?.charAt(0)?.toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ user?.name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ user?.email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:pl-64">
            <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-gray-200 bg-white px-4 sm:px-6">
                <button
                    @click="sidebarOpen = true"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 lg:hidden"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <h2 v-if="title" class="text-lg font-semibold text-gray-800">{{ title }}</h2>
                <div class="flex-1" />

                <div class="relative">
                    <button
                        @click="showUserMenu = !showUserMenu"
                        class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                    >
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-700 text-xs font-semibold">
                            {{ user?.name?.charAt(0)?.toUpperCase() }}
                        </div>
                        <span class="hidden sm:block">{{ user?.name }}</span>
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div v-if="showUserMenu" @click="showUserMenu = false" class="fixed inset-0 z-40" />
                    <Transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <div v-if="showUserMenu" class="absolute right-0 z-50 mt-2 w-48 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 py-1">
                            <button @click="logout" class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">
                                Cerrar Sesión
                            </button>
                        </div>
                    </Transition>
                </div>
            </header>

            <FlashMessages />

            <main class="p-4 sm:p-6">
                <!-- Page title slot for actions -->
                <div v-if="$slots.actions" class="mb-4 flex items-center justify-between">
                    <div></div>
                    <slot name="actions" />
                </div>
                <slot />
            </main>
        </div>
    </div>
</template>
