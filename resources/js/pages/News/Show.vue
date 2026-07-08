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
    content: string;
    image: string | null;
    category: string;
    author_name: string | null;
    published_at: string;
    views: number;
}

interface Props {
    news: NewsItem;
    relatedNews: NewsItem[];
}

const props = defineProps<Props>();

const isNight = ref(true);
const scrolled = ref(false);

function toggleSky() {
    isNight.value = !isNight.value;
}

function onScroll() {
    scrolled.value = window.scrollY > 20;
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', onScroll));

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
        month: 'long',
        year: 'numeric',
    });
};

const getCategoryColor = (category: string): string => {
    const colors: Record<string, string> = {
        'News': 'bg-blue-500',
        'Business': 'bg-green-500',
        'Technology': 'bg-purple-500',
        'Lifestyle': 'bg-pink-500',
        'Sport': 'bg-orange-500',
        'Entertainment': 'bg-yellow-500',
    };
    return colors[category] || 'bg-gray-500';
};
</script>

<template>
    <Head :title="props.news.title" />

    <!-- Konsistensi Background dengan Index.vue -->
    <div class="min-h-screen relative overflow-hidden bg-[#050505]">

        <!-- Ambient Glow from Index -->
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

        <!-- Content Area dipastikan berada di atas Glow Effect -->
        <div class="relative z-10 transition-colors duration-700 text-white">
            <div class="max-w-4xl mx-auto px-6 py-8">

                <!-- Back Button Interaktif -->
                <Link href="/news" class="group inline-flex items-center gap-2 text-white/60 hover:text-gray-300 mb-8 transition-all duration-300">
                    <span class="p-2 rounded-full bg-white/5 border border-white/10 group-hover:bg-gray-500/10 group-hover:border-gray-500/30 group-hover:-translate-x-1 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </span>
                    <span class="font-medium tracking-wide text-sm">Kembali ke Berita</span>
                </Link>

                <!-- Article Header -->
                <div class="mb-8 space-y-5">
                    <div class="flex items-center gap-3">
                        <span
                            class="px-3.5 py-1.5 rounded-full text-xs font-semibold text-white tracking-wide shadow-lg border border-white/10"
                            :class="getCategoryColor(props.news.category)"
                        >
                            {{ props.news.category }}
                        </span>
                        <span class="flex items-center gap-1.5 text-white/60 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ formatDate(props.news.published_at) }}
                        </span>
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight tracking-tight drop-shadow-md">
                        {{ props.news.title }}
                    </h1>

                    <div class="flex items-center gap-4 pb-6 border-b border-white/10 pt-2">
                        <div class="h-12 w-12 rounded-full bg-linear-to-br from-gray-500 to-gray-700 flex items-center justify-center text-white font-bold text-lg shadow-lg border-2 border-white/10">
                            {{ props.news.author_name?.charAt(0).toUpperCase() || 'R' }}
                        </div>
                        <div>
                            <p class="text-white font-semibold text-base">{{ props.news.author_name || 'Redaksi SmartRecruit' }}</p>
                            <p class="text-gray-400/80 text-sm flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ props.news.views }} views
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div v-if="props.news.image" class="mb-10 group overflow-hidden rounded-3xl border border-white/10 shadow-2xl">
                    <img
                        :src="`/storage/${props.news.image}`"
                        :alt="props.news.title"
                        class="w-full max-h-[500px] object-cover transition-transform duration-[-10000ms] group-hover:scale-105"
                    />
                </div>

                <!-- Article Content -->
                <article class="prose prose-invert prose-lg md:prose-xl max-w-none mb-14 prose-a:text-gray-400 hover:prose-a:text-gray-300">
                    <div v-html="props.news.content" class="text-white/80 leading-relaxed font-serif space-y-6"></div>
                </article>

                <!-- Tags / Share -->
                <div class="border-t border-white/10 pt-8 mb-16 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-white/50 uppercase tracking-widest font-bold">Kategori</span>
                        <span class="px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-white/90 text-sm font-medium hover:bg-white/10 transition cursor-pointer">
                            {{ props.news.category }}
                        </span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-white/50 uppercase tracking-widest font-bold">Bagikan</span>
                        <button class="p-2.5 rounded-full bg-white/5 border border-white/10 hover:bg-gray-500 hover:border-gray-500 hover:-translate-y-1 hover:shadow-[0_0_15px_rgba(156,163,175,0.4)] transition-all text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </button>
                        <button class="p-2.5 rounded-full bg-white/5 border border-white/10 hover:bg-gray-500 hover:border-gray-500 hover:-translate-y-1 hover:shadow-[0_0_15px_rgba(156,163,175,0.4)] transition-all text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Related News -->
                <div v-if="props.relatedNews.length > 0" class="pt-8 border-t border-white/5">
                    <div class="flex items-center gap-3 mb-8">
                        <span class="h-6 w-1.5 rounded-full bg-gray-400 shadow-[0_0_10px_rgba(156,163,175,0.8)]"></span>
                        <h3 class="text-2xl font-bold text-white tracking-wide">Baca Juga</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="item in props.relatedNews"
                            :key="item.id"
                            class="group relative flex flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/5 shadow-lg backdrop-blur-md transition-all duration-500 hover:-translate-y-2 hover:border-gray-500/50 hover:bg-white/10 hover:shadow-[0_20px_40px_-15px_rgba(156,163,175,0.2)]"
                        >
                            <Link :href="`/news/${item.slug}`" class="flex h-full flex-col">
                                <div class="relative aspect-16/10 w-full overflow-hidden">
                                    <img
                                        v-if="item.image"
                                        :src="`/storage/${item.image}`"
                                        :alt="item.title"
                                        class="h-full w-full object-cover transition-transform duration-700 ease-in-out group-hover:scale-110"
                                        loading="lazy"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center bg-linear-to-br from-gray-800 to-gray-900 transition-transform duration-700 group-hover:scale-105">
                                        <svg class="h-12 w-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="absolute inset-0 bg-linear-to-t from-[#050505]/80 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                                </div>
                                <div class="flex flex-1 flex-col p-5">
                                    <span class="text-xs font-semibold text-gray-400 mb-2 block uppercase tracking-wider">{{ item.category }}</span>
                                    <h4 class="text-lg font-bold text-white leading-snug group-hover:text-gray-300 transition-colors line-clamp-2">
                                        {{ item.title }}
                                    </h4>
                                </div>
                            </Link>
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

/* Enhancing Prose Typograpy */
:deep(.prose) {
    font-size: 1.125rem;
    line-height: 1.8;
}

:deep(.prose p) {
    margin-bottom: 1.75em;
    color: rgba(255, 255, 255, 0.85);
}

:deep(.prose h2) {
    font-size: 2rem;
    font-weight: 800;
    margin-top: 2em;
    margin-bottom: 1em;
    color: white;
    letter-spacing: -0.025em;
}

:deep(.prose h3) {
    font-size: 1.5rem;
    font-weight: 700;
    margin-top: 1.5em;
    margin-bottom: 0.75em;
    color: white;
}

:deep(.prose ul), :deep(.prose ol) {
    margin-bottom: 1.5em;
    padding-left: 1.5em;
}

:deep(.prose li) {
    margin-bottom: 0.5em;
    color: rgba(255, 255, 255, 0.85);
}

:deep(.prose a) {
    color: #9ca3af; /* gray-400 */
    text-decoration: underline;
    text-decoration-thickness: 2px;
    text-underline-offset: 4px;
    transition: color 0.3s ease;
}

:deep(.prose a:hover) {
    color: #d1d5db; /* gray-300 */
}

:deep(.prose blockquote) {
    border-left: 4px solid #9ca3af;
    padding-left: 1.25em;
    font-style: italic;
    color: rgba(255, 255, 255, 0.7);
    background: rgba(255, 255, 255, 0.05);
    padding: 1em 1em 1em 1.5em;
    border-radius: 0 0.5rem 0.5rem 0;
}
</style>
