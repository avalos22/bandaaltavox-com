<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    settings: Object,
    packages: Array,
});

const scrolled = ref(false);
const mobileMenuOpen = ref(false);

const whatsappLink = computed(() => {
    const num = props.settings.whatsapp_number?.replace(/\D/g, '');
    return num ? `https://wa.me/${num}?text=${encodeURIComponent('Hola, me interesa cotizar un evento con Banda Alta Vox')}` : null;
});

function onScroll() {
    scrolled.value = window.scrollY > 50;
}

function scrollTo(id) {
    mobileMenuOpen.value = false;
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' });
}

onMounted(() => window.addEventListener('scroll', onScroll));
onUnmounted(() => window.removeEventListener('scroll', onScroll));

const eventTypes = [
    { icon: '💒', title: 'Bodas', desc: 'Música romántica y bailable para tu día especial' },
    { icon: '👑', title: 'XV Años', desc: 'El vals perfecto y toda la fiesta que mereces' },
    { icon: '🏢', title: 'Corporativos', desc: 'Ambiente profesional y entretenimiento de calidad' },
    { icon: '🎉', title: 'Fiestas Privadas', desc: 'Cumpleaños, aniversarios y celebraciones' },
    { icon: '🎄', title: 'Posadas y Ferias', desc: 'Eventos estacionales con la mejor vibra' },
    { icon: '🎶', title: 'Serenatas', desc: 'Sorprende a esa persona especial con música en vivo' },
];

function formatPrice(val) {
    return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', minimumFractionDigits: 0 }).format(val);
}
</script>

<template>
    <Head :title="`${settings.business_name} - ${settings.business_slogan}`" />

    <div class="min-h-screen bg-slate-950 text-white overflow-x-hidden">
        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- NAVBAR — sticky, glass effect on scroll                     -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <nav
            :class="[
                'fixed top-0 inset-x-0 z-50 transition-all duration-300',
                scrolled ? 'bg-slate-950/90 backdrop-blur-lg shadow-lg shadow-black/20 py-3' : 'bg-transparent py-5',
            ]"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <!-- Logo -->
                <button @click="scrollTo('hero')" class="flex items-center gap-3 group">
                    <img src="/storage/images/logo-blanco-altavox.png" alt="Banda Alta Vox" class="h-8 sm:h-10" />
                </button>

                <!-- Desktop links -->
                <div class="hidden md:flex items-center gap-8">
                    <button @click="scrollTo('hero')" class="text-sm font-medium text-slate-300 hover:text-blue-400 transition-colors">Inicio</button>
                    <button @click="scrollTo('servicios')" class="text-sm font-medium text-slate-300 hover:text-blue-400 transition-colors">Servicios</button>
                    <button @click="scrollTo('paquetes')" class="text-sm font-medium text-slate-300 hover:text-blue-400 transition-colors">Paquetes</button>
                    <button @click="scrollTo('contacto')" class="text-sm font-medium text-slate-300 hover:text-blue-400 transition-colors">Contacto</button>
                </div>

                <!-- Right side -->
                <div class="hidden md:flex items-center gap-4">
                    <!-- Social icons -->
                    <div class="flex items-center gap-3">
                        <a v-if="settings.facebook_url" :href="settings.facebook_url" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-blue-400 transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12S0 5.373 0 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.875v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a v-if="settings.instagram_url" :href="settings.instagram_url" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-blue-400 transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a v-if="settings.youtube_url" :href="settings.youtube_url" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-blue-400 transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <a v-if="settings.tiktok_url" :href="settings.tiktok_url" target="_blank" rel="noopener noreferrer" class="text-slate-400 hover:text-blue-400 transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                    </div>
                    <Link
                        :href="route('login')"
                        class="text-sm font-medium bg-blue-500 text-slate-900 px-5 py-2 rounded-full hover:bg-blue-400 transition-all hover:shadow-lg hover:shadow-blue-500/25"
                    >
                        Iniciar Sesión
                    </Link>
                </div>

                <!-- Mobile hamburger -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-slate-300 hover:text-white">
                    <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Mobile menu -->
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div v-if="mobileMenuOpen" class="md:hidden bg-slate-950/95 backdrop-blur-lg border-t border-slate-800/50 px-4 py-4 space-y-3">
                    <button @click="scrollTo('hero')" class="block w-full text-left text-sm font-medium text-slate-300 hover:text-blue-400 py-2">Inicio</button>
                    <button @click="scrollTo('servicios')" class="block w-full text-left text-sm font-medium text-slate-300 hover:text-blue-400 py-2">Servicios</button>
                    <button @click="scrollTo('paquetes')" class="block w-full text-left text-sm font-medium text-slate-300 hover:text-blue-400 py-2">Paquetes</button>
                    <button @click="scrollTo('contacto')" class="block w-full text-left text-sm font-medium text-slate-300 hover:text-blue-400 py-2">Contacto</button>
                    <Link :href="route('login')" class="block text-center text-sm font-medium bg-blue-500 text-slate-900 px-5 py-2.5 rounded-full hover:bg-blue-400 transition-colors mt-2">Iniciar Sesión</Link>
                </div>
            </transition>
        </nav>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- HERO — Full-screen with animated gradient background        -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section id="hero" class="relative min-h-screen flex items-center justify-center overflow-hidden">
            <!-- Animated gradient background -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"></div>
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-1/4 -left-20 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-500/5 rounded-full blur-3xl"></div>
            </div>
            <!-- Subtle grid pattern -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px); background-size: 60px 60px;"></div>

            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 text-center pt-20">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-500/30 bg-blue-500/10 px-5 py-2 text-blue-400 text-sm font-medium mb-8 backdrop-blur-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a2.25 2.25 0 0 0 1.632-2.163Zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 0 1-.99-3.467l2.31-.66A2.25 2.25 0 0 0 9 15.553Z" />
                    </svg>
                    Música en vivo para todo tipo de eventos
                </div>

                <!-- Main heading: real logo -->
                <img src="/storage/images/logo-blanco-altavox.png" alt="Banda Alta Vox" class="h-32 sm:h-44 mx-auto mb-8 drop-shadow-2xl" />

                <!-- Decorative line -->
                <div class="flex items-center justify-center gap-3 mb-8">
                    <div class="h-px w-16 bg-gradient-to-r from-transparent to-blue-500/60"></div>
                    <div class="h-1.5 w-1.5 rounded-full bg-blue-400"></div>
                    <div class="h-px w-16 bg-gradient-to-l from-transparent to-blue-500/60"></div>
                </div>

                <!-- Slogan -->
                <p class="text-xl sm:text-2xl text-slate-300 font-light max-w-2xl mx-auto mb-10 leading-relaxed">
                    {{ settings.business_slogan }}
                </p>

                <!-- CTAs -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a
                        v-if="whatsappLink"
                        :href="whatsappLink"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group inline-flex items-center gap-3 rounded-full bg-blue-500 px-8 py-4 text-base font-bold text-slate-900 hover:bg-blue-400 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/30 hover:scale-105"
                    >
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Cotizar por WhatsApp
                    </a>
                    <button
                        @click="scrollTo('paquetes')"
                        class="inline-flex items-center gap-2 rounded-full border-2 border-slate-500/40 px-8 py-4 text-base font-semibold text-slate-200 hover:border-blue-500/50 hover:text-blue-400 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10 backdrop-blur-sm"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" /></svg>
                        Ver Paquetes
                    </button>
                </div>

                <!-- Scroll indicator -->
                <div class="mt-16 sm:mt-24 animate-bounce">
                    <svg class="h-6 w-6 mx-auto text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- SERVICIOS — Types of events we cover                        -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section id="servicios" class="relative py-24 sm:py-32">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-900/50 to-slate-950"></div>
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section header -->
                <div class="text-center mb-16">
                    <span class="inline-block text-blue-400 text-sm font-semibold tracking-widest uppercase mb-4">Nuestros Servicios</span>
                    <h2 class="text-3xl sm:text-5xl font-bold text-white mb-6">
                        Música para <span class="text-blue-400">cada ocasión</span>
                    </h2>
                    <p class="text-slate-400 text-lg max-w-2xl mx-auto">
                        Contamos con un amplio repertorio y experiencia en todo tipo de eventos. ¡Hacemos que tu celebración sea única!
                    </p>
                </div>

                <!-- Event type cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="(ev, idx) in eventTypes"
                        :key="idx"
                        class="group relative bg-slate-900/50 border border-slate-800/50 rounded-2xl p-8 hover:border-blue-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/5 hover:-translate-y-1"
                    >
                        <span class="text-4xl block mb-4">{{ ev.icon }}</span>
                        <h3 class="text-xl font-bold text-white mb-2 group-hover:text-blue-400 transition-colors">{{ ev.title }}</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">{{ ev.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- PAQUETES — Featured packages                                -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section id="paquetes" class="relative py-24 sm:py-32">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-900/30 to-slate-950"></div>
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section header -->
                <div class="text-center mb-16">
                    <span class="inline-block text-blue-400 text-sm font-semibold tracking-widest uppercase mb-4">Paquetes</span>
                    <h2 class="text-3xl sm:text-5xl font-bold text-white mb-6">
                        Elige tu <span class="text-blue-400">paquete ideal</span>
                    </h2>
                    <p class="text-slate-400 text-lg max-w-2xl mx-auto">
                        Tenemos opciones para todo tipo de presupuestos. Todos nuestros paquetes incluyen sonido profesional e iluminación.
                    </p>
                </div>

                <!-- Package cards -->
                <div v-if="packages.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div
                        v-for="pkg in packages"
                        :key="pkg.id"
                        class="group relative bg-gradient-to-b from-slate-900/80 to-slate-900/40 border border-slate-800/50 rounded-2xl overflow-hidden hover:border-blue-500/40 transition-all duration-300 hover:shadow-xl hover:shadow-blue-500/10 hover:-translate-y-1"
                    >
                        <!-- Image or gradient header -->
                        <div class="relative h-48 overflow-hidden">
                            <img
                                v-if="pkg.image"
                                :src="`/storage/${pkg.image}`"
                                :alt="pkg.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <div v-else class="w-full h-full bg-gradient-to-br from-blue-500/20 via-slate-800 to-slate-900 flex items-center justify-center">
                                <svg class="h-16 w-16 text-blue-500/30" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a2.25 2.25 0 0 0 1.632-2.163Zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 0 1-.99-3.467l2.31-.66A2.25 2.25 0 0 0 9 15.553Z" /></svg>
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>
                            <!-- Duration badge -->
                            <div class="absolute top-4 right-4 bg-slate-900/80 backdrop-blur-sm text-blue-400 text-xs font-semibold px-3 py-1.5 rounded-full border border-blue-500/20">
                                {{ pkg.duration_hours }}h
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-white mb-1">{{ pkg.name }}</h3>
                            <p v-if="pkg.description" class="text-slate-400 text-sm mb-4 line-clamp-2">{{ pkg.description }}</p>

                            <!-- Price -->
                            <div class="mb-4">
                                <span class="text-3xl font-extrabold text-blue-400">{{ formatPrice(pkg.price) }}</span>
                            </div>

                            <!-- Includes -->
                            <ul v-if="pkg.includes?.length" class="space-y-2 mb-6">
                                <li v-for="inc in pkg.includes" :key="inc.id" class="flex items-start gap-2 text-sm text-slate-300">
                                    <svg class="h-4 w-4 text-blue-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                                    {{ inc.description }}
                                </li>
                            </ul>

                            <!-- CTA -->
                            <a
                                v-if="whatsappLink"
                                :href="whatsappLink"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="block w-full text-center rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 font-semibold py-3 hover:bg-blue-500 hover:text-slate-900 transition-all duration-300"
                            >
                                Solicitar Cotización
                            </a>
                        </div>
                    </div>
                </div>

                <!-- No packages fallback -->
                <div v-else class="text-center py-16">
                    <div class="text-5xl mb-4">🎵</div>
                    <p class="text-slate-400 text-lg">Próximamente nuestros paquetes disponibles. ¡Contáctanos para una cotización personalizada!</p>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- CONTACTO — Contact section                                  -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <section id="contacto" class="relative py-24 sm:py-32">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-900/50 to-slate-950"></div>
            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <span class="inline-block text-blue-400 text-sm font-semibold tracking-widest uppercase mb-4">Contacto</span>
                    <h2 class="text-3xl sm:text-5xl font-bold text-white mb-6">
                        ¿Listo para <span class="text-blue-400">tu evento</span>?
                    </h2>
                    <p class="text-slate-400 text-lg max-w-2xl mx-auto">
                        Contáctanos y con gusto te ayudamos a planear la música perfecta para tu celebración
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- WhatsApp -->
                    <a
                        v-if="whatsappLink"
                        :href="whatsappLink"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group flex flex-col items-center text-center bg-slate-900/50 border border-slate-800/50 rounded-2xl p-8 hover:border-green-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/5"
                    >
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-500/10 text-green-400 mb-4 group-hover:bg-green-500/20 transition-colors">
                            <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-1">WhatsApp</h3>
                        <p class="text-slate-400 text-sm">{{ settings.whatsapp_number }}</p>
                        <span class="mt-3 text-green-400 text-sm font-medium">Enviar mensaje →</span>
                    </a>

                    <!-- Phone -->
                    <a
                        v-if="settings.business_phone"
                        :href="`tel:${settings.business_phone}`"
                        class="group flex flex-col items-center text-center bg-slate-900/50 border border-slate-800/50 rounded-2xl p-8 hover:border-blue-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/5"
                    >
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-500/10 text-blue-400 mb-4 group-hover:bg-blue-500/20 transition-colors">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-1">Teléfono</h3>
                        <p class="text-slate-400 text-sm">{{ settings.business_phone }}</p>
                        <span class="mt-3 text-blue-400 text-sm font-medium">Llamar →</span>
                    </a>

                    <!-- Email -->
                    <a
                        v-if="settings.business_email"
                        :href="`mailto:${settings.business_email}`"
                        class="group flex flex-col items-center text-center bg-slate-900/50 border border-slate-800/50 rounded-2xl p-8 hover:border-blue-500/30 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/5"
                    >
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-500/10 text-blue-400 mb-4 group-hover:bg-blue-500/20 transition-colors">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-1">Email</h3>
                        <p class="text-slate-400 text-sm">{{ settings.business_email }}</p>
                        <span class="mt-3 text-blue-400 text-sm font-medium">Enviar correo →</span>
                    </a>
                </div>

                <!-- Social media strip -->
                <div class="mt-12 flex items-center justify-center gap-5" v-if="settings.facebook_url || settings.instagram_url || settings.youtube_url || settings.tiktok_url">
                    <span class="text-slate-500 text-sm mr-2">Síguenos:</span>
                    <a v-if="settings.facebook_url" :href="settings.facebook_url" target="_blank" rel="noopener noreferrer" class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-800/60 text-slate-400 hover:bg-blue-600 hover:text-white transition-all duration-300">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12S0 5.373 0 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.875v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a v-if="settings.instagram_url" :href="settings.instagram_url" target="_blank" rel="noopener noreferrer" class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-800/60 text-slate-400 hover:bg-gradient-to-tr hover:from-yellow-500 hover:via-pink-500 hover:to-purple-600 hover:text-white transition-all duration-300">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a v-if="settings.youtube_url" :href="settings.youtube_url" target="_blank" rel="noopener noreferrer" class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-800/60 text-slate-400 hover:bg-red-600 hover:text-white transition-all duration-300">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    <a v-if="settings.tiktok_url" :href="settings.tiktok_url" target="_blank" rel="noopener noreferrer" class="flex h-11 w-11 items-center justify-center rounded-full bg-slate-800/60 text-slate-400 hover:bg-black hover:text-white transition-all duration-300 hover:ring-1 hover:ring-white/20">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- FOOTER                                                      -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <footer class="relative border-t border-slate-800/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-3">
                        <img src="/storage/images/logo-blanco-altavox.png" alt="Banda Alta Vox" class="h-7" />
                    </div>
                    <div class="flex items-center gap-6 text-sm text-slate-500">
                        <button @click="scrollTo('hero')" class="hover:text-slate-300 transition-colors">Inicio</button>
                        <button @click="scrollTo('servicios')" class="hover:text-slate-300 transition-colors">Servicios</button>
                        <button @click="scrollTo('paquetes')" class="hover:text-slate-300 transition-colors">Paquetes</button>
                        <button @click="scrollTo('contacto')" class="hover:text-slate-300 transition-colors">Contacto</button>
                    </div>
                    <p class="text-sm text-slate-600">
                        © {{ new Date().getFullYear() }} {{ settings.business_name }}
                    </p>
                </div>
            </div>
        </footer>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- FLOATING WHATSAPP BUTTON                                    -->
        <!-- ═══════════════════════════════════════════════════════════ -->
        <a
            v-if="whatsappLink"
            :href="whatsappLink"
            target="_blank"
            rel="noopener noreferrer"
            class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-green-500 text-white shadow-lg shadow-green-500/30 hover:bg-green-400 hover:scale-110 transition-all duration-300"
            title="Enviar WhatsApp"
        >
            <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>
    </div>
</template>
