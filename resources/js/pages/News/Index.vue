<script lang="ts">
import { defineComponent, h } from 'vue';

const EmptyLayout = defineComponent({
    setup(_, { slots }) {
        return () => (slots.default ? slots.default() : h('div'));
    },
});

export default {
    layout: EmptyLayout,
};
</script>

<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { dashboard, login, register } from '@/routes';

interface NewsItem {
    id: number;
    title: string;
    slug: string;
    excerpt: string | null;
    image: string | null;
    image_url: string | null;
    category: string;
    author_name: string | null;
    published_at: string;
    views: number;
}

interface Props {
    news: {
        data: NewsItem[];
        current_page: number;
        last_page: number;
        per_page: number;
    };
    latestNews: NewsItem | null;
    popularNews: NewsItem[];
}

const props = defineProps<Props>();

// State untuk Tema dan Scroll
const isNight = ref(true);
const scrolled = ref(false);

// Logika Slider Headline
const currentSlide = ref(0);
const sliderNews = computed(() => {
    const items = [];
    if (props.latestNews) items.push(props.latestNews);
    if (props.news && props.news.data) {
        const more = props.news.data
            .filter(n => n.id !== props.latestNews?.id)
            .slice(0, 3);
        items.push(...more);
    }
    return items;
});

let slideTimer: ReturnType<typeof setInterval> | null = null;

const nextSlide = () => {
    if (sliderNews.value.length > 0) {
        currentSlide.value = (currentSlide.value + 1) % sliderNews.value.length;
    }
};

// Fungsi Baru untuk Mengatasi Error clearInterval & setInterval di Template
const goToSlide = (index: number) => {
    currentSlide.value = index;
    if (slideTimer) clearInterval(slideTimer);
    slideTimer = setInterval(nextSlide, 5000);
};

function toggleSky() {
    isNight.value = !isNight.value;
}

function onScroll() {
    scrolled.value = window.scrollY > 20;
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
    slideTimer = setInterval(nextSlide, 5000);
});

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
    if (slideTimer) clearInterval(slideTimer);
});

const navLinks = [
    { label: 'ABOUT ME', href: '/' },
    { label: 'JOBS', href: '/jobapply'},
    { label: 'NEWS', href: '/news' },
];

const page = usePage();
const user = computed(() => (page.props as any).auth?.user);

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const getCategoryColor = (category: string): string => {
    const colors: Record<string, string> = {
        News: 'bg-blue-500',
        Business: 'bg-green-500',
        Technology: 'bg-purple-500',
        Lifestyle: 'bg-pink-500',
        Sport: 'bg-orange-500',
        Entertainment: 'bg-yellow-500',
    };
    return colors[category] || 'bg-gray-500';
};

const handleImageError = (event: Event) => {
    const img = event.target as HTMLImageElement;
    img.style.display = 'none';

    const placeholder = document.createElement('div');
    placeholder.className =
        'w-full h-full bg-linear-to-br from-gray-800 to-gray-900 flex items-center justify-center';
    placeholder.innerHTML = `
        <svg class="w-16 h-16 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    `;
    img.parentElement?.appendChild(placeholder);
};
</script>

<template>
    <Head title="News" />

    <div class="min-h-screen relative overflow-hidden bg-[#050505]">
        <!-- Ambient Glow -->
        <div class="pointer-events-none absolute inset-0 flex justify-center overflow-hidden z-0 transition-opacity duration-1000" :class="isNight ? 'opacity-100' : 'opacity-30'">
            <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] rounded-full bg-gray-500/10 blur-[120px]"></div>
            <div class="absolute top-[20%] right-[-10%] w-[40%] h-[60%] rounded-full bg-blue-500/10 blur-[120px]"></div>
            <div class="absolute bottom-[-20%] left-[20%] w-[60%] h-[50%] rounded-full bg-gray-600/10 blur-[120px]"></div>
        </div>

        <!-- Header Start -->
        <header
            class="sticky top-0 z-40 w-full transition-all duration-700 ease-in-out"
            :class="
                scrolled
                    ? isNight
                        ? 'border-b border-bone/6 bg-obsidian/70 backdrop-blur-md'
                        : 'border-b border-obsidian/10 bg-bone/70 backdrop-blur-md'
                    : 'bg-transparent'
            "
        >
            <nav class="mx-auto flex max-w-[1200px] items-center justify-between px-6 py-4">
                <div class="flex shrink-0 items-center gap-2.5">
                    <div
                        class="flex h-7 w-7 items-center justify-center rounded-lg transition-colors duration-700"
                        :class="isNight ? 'bg-bone/8' : 'bg-obsidian/10'"
                    >
                        <AppLogoIcon
                            class="size-3.5 fill-current transition-colors duration-700"
                            :class="isNight ? 'text-paper-white' : 'text-obsidian'"
                        />
                    </div>
                    <span
                        class="text-body-sm font-medium tracking-tight transition-colors duration-700"
                        :class="isNight ? 'text-bone' : 'text-obsidian'"
                    >SmartRecruit</span>
                </div>
                <div class="hidden items-center gap-1 lg:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.label"
                        :href="link.href"
                        class="flex items-center gap-1 rounded-lg px-3 py-2 text-[11px] font-normal tracking-[0.182em] uppercase transition-colors duration-300"
                        :class="isNight ? 'text-frost hover:text-paper-white' : 'text-slate hover:text-obsidian'"
                    >{{ link.label }}</Link>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <button
                        @click="toggleSky"
                        class="group relative flex h-[36px] w-[72px] items-center rounded-full border transition-all duration-300 hover:scale-[1.05]"
                        :class="isNight ? 'border-graphite/30 bg-carbon/80' : 'border-obsidian/20 bg-obsidian/5'"
                    >
                        <span
                            class="absolute top-[3px] flex h-[28px] w-[28px] items-center justify-center rounded-full shadow-md transition-all duration-300 ease-out"
                            :class="isNight ? 'left-[39px] bg-graphite' : 'left-[3px] bg-obsidian'"
                        >
                            <svg class="absolute h-3.5 w-3.5 transition-opacity duration-300" :class="isNight ? 'text-bone opacity-100' : 'opacity-0'" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                            </svg>
                            <svg class="absolute h-3.5 w-3.5 transition-opacity duration-300" :class="isNight ? 'opacity-0' : 'text-bone opacity-100'" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>
                    <span class="ml-2 hidden text-[11px] tracking-[0.182em] uppercase transition-colors duration-700 md:inline" :class="isNight ? 'text-frost' : 'text-slate'">{{ isNight ? 'Dark' : 'Light' }}</span>

                    <template v-if="user">
                        <Link :href="dashboard()" class="inline-flex h-9 items-center gap-1.5 rounded-full px-4 text-[11px] font-normal tracking-[0.182em] uppercase shadow-lg transition-all duration-300" :class="isNight ? 'bg-paper-white text-carbon hover:bg-bone' : 'bg-obsidian text-paper-white hover:bg-obsidian/90'">DASHBOARD &rarr;</Link>
                    </template>
                    <template v-else>
                        <Link :href="login()" class="hidden px-3 py-2 text-[11px] font-normal tracking-[0.182em] uppercase transition-colors duration-300 sm:block" :class="isNight ? 'text-paper-white hover:text-frost' : 'text-obsidian hover:text-obsidian/70'">LOG IN</Link>
                        <Link :href="register()" class="inline-flex h-9 items-center rounded-full px-4 text-[11px] font-normal tracking-[0.182em] uppercase shadow-lg transition-all duration-300" :class="isNight ? 'bg-paper-white text-carbon hover:bg-bone' : 'bg-obsidian text-paper-white hover:bg-obsidian/90'">SIGN UP &rarr;</Link>
                    </template>
                </div>
            </nav>
        </header>
        <!-- Header End -->

        <div class="p-6 relative z-10 text-white transition-colors duration-700">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 lg:grid-cols-12">

            <!-- Kiri: Konten Berita Utama & Grid (Col 8) -->
            <div class="space-y-6 lg:col-span-8">

                <!-- Tab Menu / Live Assistance Banner Mini -->
                <div class="flex items-center justify-between gap-4 overflow-x-auto rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md p-4 text-xs text-neutral-300 shadow-lg transition-all hover:bg-white/10">
                    <div class="flex shrink-0 items-center gap-2">
                        <span class="rounded bg-teal-500/20 px-2 py-0.5 font-bold text-teal-400">Living Assistance</span>
                        <span>Harga Emas: <strong class="text-white">2,63 jt/gr</strong></span>
                    </div>
                    <div class="flex shrink-0 items-center gap-4">
                        <span>Jakarta: <strong class="text-white">29°C</strong></span>
                        <span>Pertandingan FT: <strong class="text-white">POR 2 - 1 CRO</strong></span>
                    </div>
                </div>

                <!-- Auto-Sliding Headline News -->
                <div v-if="sliderNews.length > 0" class="group relative block h-[420px] overflow-hidden rounded-3xl bg-neutral-900 shadow-2xl">
                    <Link
                        v-for="(item, index) in sliderNews"
                        :key="item.id"
                        :href="`/news/${item.slug}`"
                        class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                        :class="currentSlide === index ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'"
                    >
                        <!-- FIX TAILWIND WARNING: duration-[10000ms] -> duration-10000 -->
                        <img
                            v-if="item.image"
                            :src="`/storage/${item.image}`"
                            :alt="item.title"
                            class="absolute inset-0 h-full w-full object-cover transition-transform duration-10000 ease-linear group-hover:scale-110"
                        />
                        <div v-else class="absolute inset-0 h-full w-full bg-linear-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                             <span class="text-gray-500">No Image</span>
                        </div>

                        <!-- Smooth Dark Gradient Overlay -->
                        <div class="absolute inset-0 bg-linear-to-t from-[#050505] via-[#050505]/40 to-transparent"></div>

                        <!-- Konten Headline -->
                        <div class="absolute inset-x-0 bottom-0 space-y-4 p-8 transform transition-transform duration-500 translate-y-2 group-hover:translate-y-0">
                            <!-- Kategori Badge -->
                            <span class="rounded-full bg-blue-600/90 backdrop-blur-sm px-3 py-1 text-xs font-semibold tracking-wider text-white uppercase shadow-lg shadow-blue-500/30 border border-blue-400/20">
                                {{ item.category }}
                            </span>

                            <!-- Judul Berita -->
                            <h1 class="text-2xl leading-tight font-bold tracking-tight text-white transition-colors group-hover:text-gray-400 md:text-3xl lg:text-4xl drop-shadow-md">
                                {{ item.title }}
                            </h1>

                            <!-- Excerpt/Ringkasan -->
                            <p v-if="item.excerpt" class="line-clamp-2 max-w-2xl text-sm text-white/80 drop-shadow">
                                {{ item.excerpt }}
                            </p>

                            <!-- Meta Info -->
                            <div class="flex items-center gap-4 pt-2">
                                <span class="flex items-center gap-1.5 text-xs font-medium text-white/70">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ formatDate(item.published_at) }}
                                </span>
                                <span class="flex items-center gap-1.5 text-xs font-medium text-white/70">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    {{ item.views }} views
                                </span>
                                <span v-if="item.author_name" class="flex items-center gap-1.5 text-xs font-medium text-white/70">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ item.author_name }}
                                </span>
                            </div>
                        </div>
                    </Link>

                    <!-- Slider Indicators -->
                    <div class="absolute bottom-6 right-8 z-20 flex gap-2">
                        <!-- FIX TYPESCRIPT ERROR: Menggunakan fungsi goToSlide(index) -->
                        <button
                            v-for="(_, index) in sliderNews"
                            :key="'indicator-'+index"
                            @click="goToSlide(index)"
                            class="h-1.5 rounded-full transition-all duration-300"
                            :class="currentSlide === index ? 'w-8 bg-gray-400 shadow-[0_0_10px_rgba(156,163,175,0.7)]' : 'w-2 bg-white/40 hover:bg-white/70'"
                        ></button>
                    </div>
                </div>

                <!-- Placeholder jika belum ada headline -->
                <div v-else class="relative flex h-[420px] items-center justify-center overflow-hidden rounded-3xl bg-neutral-900 border border-white/10">
                    <p class="text-white/40 font-medium">Belum ada berita terbaru</p>
                </div>

                <!-- News Grid -->
                <template v-if="props.news.data && props.news.data.length > 0">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="item in props.news.data"
                            :key="item.id"
                            class="group relative flex flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-lg backdrop-blur-md transition-all duration-500 hover:-translate-y-2 hover:border-gray-500/50 hover:bg-white/10 hover:shadow-[0_20px_40px_-15px_rgba(156,163,175,0.2)]"
                        >
                            <Link :href="`/news/${item.slug}`" class="flex h-full flex-col">
                                <!-- Image Container -->
                                <!-- FIX TAILWIND WARNING: aspect-[16/10] -> aspect-16/10 -->
                                <div class="relative aspect-16/10 w-full overflow-hidden">
                                    <img
                                        v-if="item.image"
                                        :src="`/storage/${item.image}`"
                                        :alt="item.title"
                                        class="h-full w-full object-cover transition-transform duration-700 ease-in-out group-hover:scale-110"
                                        loading="lazy"
                                        @error="handleImageError"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center bg-linear-to-br from-gray-800 to-gray-900 transition-transform duration-700 group-hover:scale-105">
                                        <svg class="h-16 w-16 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>

                                    <!-- Hover Dark Overlay -->
                                    <div class="absolute inset-0 bg-linear-to-t from-[#050505]/80 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>

                                    <!-- Kategori Badge Glassmorphism -->
                                    <div class="absolute top-3 right-3">
                                        <span class="rounded-full border border-white/20 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-md shadow-lg" :class="getCategoryColor(item.category)">
                                            {{ item.category }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex flex-1 flex-col p-5">
                                    <div class="mb-3 flex items-center text-xs font-medium text-white/60">
                                        <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ formatDate(item.published_at) }}
                                    </div>

                                    <h3 class="mb-3 line-clamp-2 text-xl leading-snug font-bold text-white transition-colors duration-300 group-hover:text-gray-400">
                                        {{ item.title }}
                                    </h3>

                                    <p class="mb-4 line-clamp-3 text-sm leading-relaxed text-white/70">
                                        {{ item.excerpt || 'Tidak ada ringkasan tersedia.' }}
                                    </p>

                                    <!-- Baca Selengkapnya Button -->
                                    <div class="mt-auto border-t border-white/10 pt-4">
                                        <div class="flex items-center justify-between text-sm font-semibold text-gray-400 transition-colors group-hover:text-gray-300">
                                            <span>Baca Selengkapnya</span>
                                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-500/10 transition-transform duration-300 group-hover:translate-x-2 group-hover:bg-gray-500/20">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                    <!-- Animated bottom border -->
                                    <div class="absolute bottom-0 left-0 h-1 w-0 bg-gray-400 transition-all duration-500 group-hover:w-full"></div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <template v-else>
                    <div class="col-span-full flex min-h-[400px] flex-col items-center justify-center px-4 py-20">
                        <div class="group max-w-lg rounded-3xl border border-white/10 bg-white/5 p-12 text-center shadow-2xl backdrop-blur-sm transition-all duration-500 hover:border-white/20 hover:bg-white/10">
                            <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-white/5 transition-transform duration-500 group-hover:scale-110 group-hover:bg-white/10">
                                <svg class="h-12 w-12 text-white/40 transition-colors duration-500 group-hover:text-white/60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                            </div>
                            <h3 class="mb-3 text-2xl font-bold tracking-wide text-white">Berita Belum Ada</h3>
                            <p class="leading-relaxed text-white/60">
                                Belum ada berita yang dipublikasikan saat ini. Silakan kembali lagi nanti untuk mendapatkan update informasi terbaru.
                            </p>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Kanan: Sidebar Kompas Terpopuler & QR Widget (Col 4) -->
            <div class="space-y-6 lg:col-span-4">
                <!-- Blok Terpopuler -->
                <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md p-6 shadow-xl transition-all hover:border-white/20">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="h-5 w-1.5 rounded-full bg-gray-400 shadow-[0_0_8px_rgba(156,163,175,0.8)]"></span>
                        <h2 class="text-lg font-bold tracking-tight text-white">Terpopuler</h2>
                    </div>

                    <div class="divide-y divide-white/10">
                        <div v-for="(pop, idx) in popularNews" :key="idx" class="group flex items-start gap-4 py-3.5 transition-all hover:translate-x-1">
                            <span class="pt-0.5 text-2xl font-black text-white/20 transition-colors group-hover:text-gray-400">
                                {{ idx + 1 }}
                            </span>
                            <div class="space-y-1">
                                <h4 class="cursor-pointer text-sm leading-snug font-semibold text-white/80 transition-colors group-hover:text-white group-hover:underline">
                                    {{ pop.title }}
                                </h4>
                                <span class="text-xs text-gray-400/80 font-medium uppercase">{{ pop.category }}</span>
                            </div>
                        </div>
                    </div>
                    <button class="mt-4 w-full text-center text-xs font-bold text-gray-400 transition-colors hover:text-gray-300 hover:underline">
                        Terpopuler Lainnya &rarr;
                    </button>
                </div>

                <!-- Widget App Download -->
                <div class="space-y-6 rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md p-6 text-center shadow-xl hover:border-white/20 transition-all overflow-hidden relative">
                    <!-- Glow decoration -->
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-gray-500/20 blur-3xl rounded-full"></div>
                    <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-blue-500/20 blur-3xl rounded-full"></div>

                    <h3 class="relative px-2 text-sm leading-relaxed font-bold text-white">
                        Akses Berita Lebih Cepat<br/>
                        <span class="text-gray-400 font-medium">Download App SmartRecruit</span>
                    </h3>
                    
                    <div class="relative mx-auto flex h-36 w-36 items-center justify-center rounded-2xl bg-white p-3 shadow-2xl transition-transform hover:scale-105 group">
                        <!-- Simulated QR Code -->
                        <svg viewBox="0 0 100 100" class="h-full w-full text-neutral-900" fill="currentColor">
                            <path d="M5,5 h25 v25 h-25 z M10,10 h15 v15 h-15 z" />
                            <path d="M70,5 h25 v25 h-25 z M75,10 h15 v15 h-15 z" />
                            <path d="M5,70 h25 v25 h-25 z M10,75 h15 v15 h-15 z" />
                            <rect x="40" y="5" width="10" height="10" />
                            <rect x="55" y="5" width="10" height="25" />
                            <rect x="40" y="20" width="10" height="10" />
                            <rect x="5" y="40" width="25" height="10" />
                            <rect x="40" y="40" width="10" height="10" />
                            <rect x="55" y="40" width="20" height="10" />
                            <rect x="80" y="40" width="15" height="25" />
                            <rect x="5" y="55" width="10" height="10" />
                            <rect x="20" y="55" width="10" height="10" />
                            <rect x="40" y="55" width="35" height="10" />
                            <rect x="40" y="70" width="10" height="10" />
                            <rect x="55" y="70" width="10" height="25" />
                            <rect x="70" y="70" width="25" height="10" />
                            <rect x="70" y="85" width="10" height="10" />
                            <rect x="85" y="85" width="10" height="10" />
                            <!-- Center Logo inside QR -->
                            <rect x="38" y="38" width="24" height="24" fill="#ffffff" />
                            <path d="M42,42 h16 v16 h-16 z" fill="#4B5563" />
                            <path d="M45,45 h10 v10 h-10 z" fill="#1F2937" />
                        </svg>
                        
                        <!-- Scanner line animation -->
                        <div class="absolute top-0 left-0 w-full h-1 bg-blue-500/50 shadow-[0_0_10px_rgba(59,130,246,0.8)] opacity-0 transition-opacity duration-300 group-hover:opacity-100 scan-animation"></div>
                    </div>
                    
                    <p class="relative text-[11px] font-medium text-white/50 uppercase tracking-widest">Scan QR untuk Download</p>
                    
                    <div class="relative flex flex-col gap-3 pt-2">
                        <!-- App Store -->
                        <a href="#" class="flex w-full items-center justify-center gap-3 rounded-xl bg-[#111] border border-white/10 px-4 py-2.5 transition-all hover:bg-[#1a1a1a] hover:border-white/20 hover:scale-[1.02]">
                            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 512 512">
                                <path d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141.2 4 184.8 4 273.5q0 39.3 14.4 81.2c12.8 36.7 59 126.7 107.2 125.2 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-82.5 102.6-119.3-65.2-30.7-61.7-90-61.7-91.9zm-56.6-164.2c27.3-32.4 24.8-61.9 24-72.5-24.1 1.4-52 16.4-67.9 34.9-17.5 19.8-27.8 44.3-25.6 71.9 26.1 2 49.9-11.4 69.5-34.3z"/>
                            </svg>
                            <div class="flex flex-col items-start">
                                <span class="text-[9px] font-medium leading-none text-white/60">Download on the</span>
                                <span class="text-sm font-bold leading-tight text-white">App Store</span>
                            </div>
                        </a>
                        
                        <!-- Google Play -->
                        <a href="#" class="flex w-full items-center justify-center gap-3 rounded-xl bg-[#111] border border-white/10 px-4 py-2.5 transition-all hover:bg-[#1a1a1a] hover:border-white/20 hover:scale-[1.02]">
                            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 512 512">
                                <path d="M325.3 234.3L104.6 13l280.8 161.2-60.1 60.1zM47 0C34 6.8 25.3 19.2 25.3 35.3v441.3c0 16.1 8.7 28.5 21.7 35.3l256.6-256L47 0zm425.2 225.6l-58.9-34.1-65.7 64.5 65.7 64.5 60.1-34.1c18-14.3 18-46.5-1.2-60.8zM104.6 499l280.8-161.2-60.1-60.1L104.6 499z"/>
                            </svg>
                            <div class="flex flex-col items-start">
                                <span class="text-[9px] font-medium leading-none text-white/60">GET IT ON</span>
                                <span class="text-sm font-bold leading-tight text-white">Google Play</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Sosial Media -->
                <div class="rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md p-5 shadow-xl hover:border-white/20 transition-all">
                    <span class="mb-3 block text-xs font-medium text-white/60">Ikuti Kami:</span>
                    <div class="flex flex-wrap gap-2.5">
                        <span
                            v-for="social in ['FB', 'X', 'TG', 'IG', 'YT', 'TK']"
                            :key="social"
                            class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-white/5 text-xs font-bold text-white/80 border border-white/10 transition-all hover:bg-gray-600 hover:text-white hover:border-gray-600 hover:scale-110 hover:-translate-y-1 hover:shadow-lg hover:shadow-gray-500/30"
                        >
                            {{ social }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
@keyframes scan {
    0% { transform: translateY(0); }
    50% { transform: translateY(132px); }
    100% { transform: translateY(0); }
}
.scan-animation {
    animation: scan 2s ease-in-out infinite;
}
</style>
