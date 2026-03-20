<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import FlashMessages from '@/Components/FlashMessages.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

const sidebarOpen = ref(false);
const showUserMenu = ref(false);

const navigation = [
    {
        name: 'Dashboard',
        href: 'admin.dashboard',
        icon: 'home',
        permission: null,
    },
    {
        name: 'Usuarios',
        href: 'admin.users.index',
        icon: 'users',
        permission: 'users.view',
    },
    {
        name: 'Roles',
        href: 'admin.roles.index',
        icon: 'shield',
        permission: 'roles.view',
    },
    {
        name: 'Paquetes',
        href: 'admin.packages.index',
        icon: 'package',
        permission: 'packages.view',
    },
    {
        name: 'Cotizaciones',
        href: 'admin.quotations.index',
        icon: 'file-text',
        permission: 'quotations.view',
    },
    {
        name: 'Contratos',
        href: 'admin.contracts.index',
        icon: 'file-text',
        permission: 'contracts.view',
    },
    {
        name: 'Agenda',
        href: 'admin.events.index',
        icon: 'calendar',
        permission: 'events.view',
    },
    {
        name: 'Cobranza',
        href: 'admin.payments.index',
        icon: 'dollar-sign',
        permission: 'payments.view',
    },
    {
        name: 'Ajustes',
        href: 'admin.settings.index',
        icon: 'settings',
        permission: 'settings.view',
    },
];

const filteredNav = computed(() => {
    const permissions = page.props.auth.permissions;
    return navigation.filter(item => {
        if (!item.permission) return true;
        return permissions.includes(item.permission);
    });
});

const isActive = (routeName) => {
    try {
        // Support wildcard matching: admin.users.index → admin.users.*
        const prefix = routeName.replace(/\.[^.]+$/, '.*');
        return route().current(routeName) || route().current(prefix);
    } catch {
        return false;
    }
};

const logout = () => {
    router.post(route('logout'));
};

defineProps({
    title: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Mobile sidebar overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 w-64 transform bg-slate-900 transition-transform duration-200 ease-in-out lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            ]"
        >
            <!-- Logo -->
            <div class="flex h-16 items-center gap-3 px-6 border-b border-slate-800">
                <img src="/storage/images/logo-blanco-altavox.png" alt="Banda Alta Vox" class="h-9" />
                <div>
                    <p class="text-slate-400 text-xs">Panel de administración</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-4 px-3 space-y-1">
                <template v-for="item in filteredNav" :key="item.name">
                    <Link
                        v-if="!item.soon"
                        :href="route(item.href)"
                        :class="[
                            'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                            isActive(item.href)
                                ? 'bg-amber-500/10 text-amber-400'
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white',
                        ]"
                        @click="sidebarOpen = false"
                    >
                        <component :is="'Icon' + item.icon" class="h-5 w-5 shrink-0" />
                        <span>{{ item.name }}</span>
                    </Link>
                    <div
                        v-else
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-500 cursor-not-allowed"
                    >
                        <component :is="'Icon' + item.icon" class="h-5 w-5 shrink-0" />
                        <span>{{ item.name }}</span>
                        <span class="ml-auto text-[10px] bg-slate-800 text-slate-500 px-1.5 py-0.5 rounded">Pronto</span>
                    </div>
                </template>
            </nav>

            <!-- User section at bottom -->
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
            <!-- Top bar -->
            <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-gray-200 bg-white px-4 sm:px-6">
                <!-- Mobile menu button -->
                <button
                    @click="sidebarOpen = true"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 lg:hidden"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <!-- Page title -->
                <h2 v-if="title" class="text-lg font-semibold text-gray-800">
                    {{ title }}
                </h2>

                <div class="flex-1" />

                <!-- User dropdown -->
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

                    <!-- Dropdown menu -->
                    <div
                        v-if="showUserMenu"
                        @click="showUserMenu = false"
                        class="fixed inset-0 z-40"
                    />
                    <Transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <div
                            v-if="showUserMenu"
                            class="absolute right-0 z-50 mt-2 w-48 rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 py-1"
                        >
                            <Link
                                :href="route('admin.profile.edit')"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                @click="showUserMenu = false"
                            >
                                Mi Perfil
                            </Link>
                            <button
                                @click="logout"
                                class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50"
                            >
                                Cerrar Sesión
                            </button>
                        </div>
                    </Transition>
                </div>
            </header>

            <!-- Flash messages -->
            <FlashMessages />

            <!-- Page content -->
            <main class="p-4 sm:p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
