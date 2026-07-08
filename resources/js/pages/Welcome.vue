<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import type { Ref } from 'vue';

import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Galaxy from '@/components/Galaxy/Galaxy.vue';

import { dashboard, login, register } from '@/routes';

const isNight = ref(true);
const scrolled = ref(false);
const mobileOpen = ref(false);

function toggleSky() {
    isNight.value = !isNight.value;
}

function onScroll() {
    scrolled.value = window.scrollY > 20;
}

const activeFeature = ref(0);
const countsAnimated = ref(false);

const candidateCount = ref(0);
const accuracyPercent = ref(0);
const hiringMultiplier = ref(0);
const hrTeamCount = ref(0);

const featurePreviews = [
    {
        title: 'Candidate Match',
        subtitle: 'AI-powered CV analysis',
        skills: ['Python', 'React', 'Laravel', 'PostgreSQL', 'Docker'],
        matchScore: 92,
    },
    {
        title: 'Smart Scoring',
        subtitle: 'Multi-dimensional evaluation',
        scores: [
            { label: 'Skill Match', value: 94, color: '#847dff' },
            { label: 'Experience', value: 87, color: '#dd90d8' },
            { label: 'Role Fit', value: 91, color: '#00b3dd' },
        ],
    },
    {
        title: 'Rank & Decide',
        subtitle: 'Candidate leaderboard',
        candidates: [
            { name: 'Sarah Chen', score: 96, rank: 1 },
            { name: 'Ahmad Rizki', score: 91, rank: 2 },
            { name: 'Lisa Park', score: 88, rank: 3 },
        ],
    },
];

let observer: IntersectionObserver | null = null;

const rafIds: number[] = [];

function animateCount(
    target: number,
    displayRef: Ref<number>,
    duration = 1500,
    decimals = 0,
) {
    const start = performance.now();

    function step(now: number) {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);

        displayRef.value =
            decimals > 0
                ? parseFloat((eased * target).toFixed(decimals))
                : Math.round(eased * target);

        if (progress < 1) {
            const id = requestAnimationFrame(step);
            rafIds.push(id);
        }
    }

    const id = requestAnimationFrame(step);
    rafIds.push(id);
}

function onCardMouseMove(e: MouseEvent) {
    const card = e.currentTarget as HTMLElement;
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    card.style.setProperty('--mouse-x', `${x}px`);
    card.style.setProperty('--mouse-y', `${y}px`);
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });

    observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');

                    if (
                        entry.target.classList.contains('stats-section') &&
                        !countsAnimated.value
                    ) {
                        countsAnimated.value = true;

                        animateCount(2400, candidateCount);
                        animateCount(94, accuracyPercent);
                        animateCount(3.2, hiringMultiplier, 1500, 1);
                        animateCount(150, hrTeamCount);
                    }
                }
            });
        },
        { threshold: 0.15 },
    );

    nextTick(() => {
        document.querySelectorAll('.scroll-reveal').forEach((el) => {
            observer?.observe(el);
        });
    });
});

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
    observer?.disconnect();
    rafIds.forEach((id) => cancelAnimationFrame(id));
});

const navLinks = [
    { label: 'ABOUT ME', href: '#about' },
    { label: 'JOBS', href: '/jobapply'},
    { label: 'NEWS', href: '/news' },
];
</script>

<template>
    <Head title="SmartRecuit" />

    <div
        class="min-h-screen transition-colors duration-1000 ease-in-out"
        :class="isNight ? 'bg-obsidian' : 'bg-bone'"
    >
        <!-- ============================================================ -->
        <!-- FIXED BACKGROUND                                             -->
        <!-- ============================================================ -->
        <div
            class="fixed inset-0 z-0 pointer-events-none transition-opacity duration-1000"
            :class="isNight ? 'opacity-100' : 'opacity-0'"
        >
            <!-- Galaxy Background: tidak ikut berubah saat dark/light toggle -->
            <!-- Galaxy Background: tidak ikut dark/light mode -->
            <!-- Galaxy Background: tetap interaktif -->
            <div class="pointer-events-auto absolute inset-0 z-0">
                <Galaxy
                    class="h-full w-full"
                    :mouse-repulsion="true"
                    :mouse-interaction="true"
                    :repulsion-strength="0.5"
                    :density="1.6"
                    :glow-intensity="0.75"
                    :saturation="1.25"
                    :hue-shift="210"
                />
            </div>

            <!-- Color nebula overlay: tidak menghalangi mouse -->
            <div
                class="animate-nebula-drift pointer-events-none absolute inset-0 z-1 opacity-70 mix-blend-screen"
                style="
                    background:
                        radial-gradient(
                            circle at 18% 24%,
                            rgba(59, 130, 246, 0.34),
                            transparent 24%
                        ),
                        radial-gradient(
                            circle at 78% 18%,
                            rgba(168, 85, 247, 0.32),
                            transparent 25%
                        ),
                        radial-gradient(
                            circle at 48% 56%,
                            rgba(236, 72, 153, 0.18),
                            transparent 30%
                        ),
                        radial-gradient(
                            circle at 82% 78%,
                            rgba(34, 211, 238, 0.22),
                            transparent 27%
                        );
                "
            />

            <!-- Static dark overlay: tidak menghalangi mouse -->
            <div
                class="pointer-events-none absolute inset-0 z-2 bg-linear-to-b from-obsidian/35 via-obsidian/10 to-obsidian/70"
            />

            <!-- Bottom fade: tidak menghalangi mouse -->
            <div
                class="pointer-events-none absolute inset-x-0 bottom-0 z-3 h-56 bg-linear-to-b from-transparent to-obsidian"
            />
        </div>

        <!-- ============================================================ -->
        <!-- SCROLLABLE CONTENT                                           -->
        <!-- ============================================================ -->
        <div class="relative z-10 flex flex-col">

            <!-- ===== STICKY NAVBAR ===== -->
            <header
                class="sticky top-0 z-40 w-full transition-all duration-700 ease-in-out"
                :class="
                    scrolled
                        ? isNight
                            ? 'border-b border-bone/6 bg-obsidian/70 backdrop-blur-md'
                            : 'border-b border-bone/6 bg-obsidian/45 backdrop-blur-md'
                        : 'bg-transparent'
                "
            >
                <nav
                    class="mx-auto flex max-w-[1200px] items-center justify-between px-6 py-4"
                >
                    <!-- Left: Brand -->
                    <div class="flex shrink-0 items-center gap-2.5">
                        <div
                            class="flex h-7 w-7 items-center justify-center rounded-lg transition-colors duration-700"
                            :class="
                                isNight
                                    ? 'bg-bone/8'
                                    : 'bg-paper-white/12'
                            "
                        >
                            <AppLogoIcon
                                class="size-3.5 fill-current transition-colors duration-700"
                                :class="
                                    isNight
                                        ? 'text-paper-white'
                                        : 'text-paper-white'
                                "
                            />
                        </div>

                        <span
                            class="text-body-sm font-medium tracking-tight transition-colors duration-700"
                            :class="isNight ? 'text-bone' : 'text-paper-white'"
                        >
                            SmartRecruit
                        </span>
                    </div>

                    <!-- Center: Nav Links -->
                    <div class="hidden items-center gap-1 lg:flex">
                        <a
                            v-for="link in navLinks"
                            :key="link.label"
                            :href="link.href"
                            class="flex items-center gap-1 rounded-lg px-3 py-2 text-[11px] font-normal tracking-[0.182em] uppercase transition-colors duration-300"
                            :class="
                                isNight
                                    ? 'text-frost hover:text-paper-white'
                                    : 'text-paper-white/70 hover:text-paper-white'
                            "
                        >
                            {{ link.label }}

                        </a>
                    </div>

                    <!-- Right: Toggle + Auth -->
                    <div class="flex shrink-0 items-center gap-2">
                        <button
                            @click="toggleSky"
                            class="group relative flex h-[36px] w-[72px] items-center rounded-full border transition-all duration-300 hover:scale-[1.05] focus-visible:ring-2 focus-visible:ring-amethyst/50 focus-visible:outline-none"
                            :class="
                                isNight
                                    ? 'border-graphite/30 bg-carbon/80'
                                    : 'border-bone/30 bg-paper-white/10'
                            "
                            :aria-label="
                                isNight
                                    ? 'Switch to light mode'
                                    : 'Switch to dark mode'
                            "
                            :aria-pressed="isNight"
                        >
                            <span
                                class="absolute top-[3px] flex h-[28px] w-[28px] items-center justify-center rounded-full shadow-md transition-all duration-300 ease-out"
                                :class="
                                    isNight
                                        ? 'left-[39px] bg-graphite'
                                        : 'left-[3px] bg-paper-white'
                                "
                            >
                                <svg
                                    class="absolute h-3.5 w-3.5 transition-opacity duration-300"
                                    :class="
                                        isNight
                                            ? 'text-bone opacity-100'
                                            : 'opacity-0'
                                    "
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"
                                    />
                                </svg>

                                <svg
                                    class="absolute h-3.5 w-3.5 transition-opacity duration-300"
                                    :class="
                                        isNight
                                            ? 'opacity-0'
                                            : 'text-carbon opacity-100'
                                    "
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                        </button>

                        <span
                            class="ml-2 hidden text-[11px] tracking-[0.182em] uppercase transition-colors duration-700 md:inline"
                            :class="
                                isNight ? 'text-frost' : 'text-paper-white/70'
                            "
                        >
                            {{ isNight ? 'Dark' : 'Light' }}
                        </span>

                        <template v-if="$page.props.auth.user">
                            <Link
                                :href="dashboard()"
                                class="inline-flex h-9 items-center gap-1.5 rounded-full bg-paper-white px-4 text-[11px] font-normal tracking-[0.182em] text-carbon uppercase shadow-lg transition-all duration-300 hover:bg-bone"
                            >
                                DASHBOARD
                                <span aria-hidden="true">&rarr;</span>
                            </Link>
                        </template>



                        <template v-else>
                            <Link
                                :href="login()"
                                class="hidden px-3 py-2 text-[11px] font-normal tracking-[0.182em] uppercase transition-colors duration-300 sm:block"
                                :class="
                                    isNight
                                        ? 'text-paper-white hover:text-frost'
                                        : 'text-paper-white/80 hover:text-paper-white'
                                "
                            >
                                LOG IN
                            </Link>

                            <Link
                                :href="register()"
                                class="inline-flex h-9 items-center rounded-full bg-paper-white px-4 text-[11px] font-normal tracking-[0.182em] text-carbon uppercase shadow-lg transition-all duration-300 hover:bg-bone"
                            >
                                SIGN UP &rarr;
                            </Link>
                        </template>

                        <button
                            type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-full transition-colors duration-300 lg:hidden"
                            :class="
                                isNight
                                    ? 'bg-bone/8 hover:bg-bone/15'
                                    : 'bg-paper-white/10 hover:bg-paper-white/16'
                            "
                            aria-label="Toggle navigation"
                            :aria-expanded="mobileOpen"
                            aria-controls="mobile-nav"
                            @click="mobileOpen = !mobileOpen"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="size-4 text-bone transition-colors duration-700"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <template v-if="!mobileOpen">
                                    <line x1="3" y1="6" x2="21" y2="6" />
                                    <line x1="3" y1="12" x2="21" y2="12" />
                                    <line x1="3" y1="18" x2="21" y2="18" />
                                </template>

                                <template v-else>
                                    <line x1="18" y1="6" x2="6" y2="18" />
                                    <line x1="6" y1="6" x2="18" y2="18" />
                                </template>
                            </svg>
                        </button>
                    </div>
                </nav>

                <!-- Mobile Dropdown -->
                <div
                    id="mobile-nav"
                    v-show="mobileOpen"
                    class="border-t border-bone/6 bg-obsidian/90 px-6 pt-2 pb-4 backdrop-blur-md transition-colors duration-700 lg:hidden"
                >
                    <a
                        v-for="link in navLinks"
                        :key="link.label"
                        :href="link.href"
                        class="block rounded-lg px-3 py-3 text-[11px] font-normal tracking-[0.182em] uppercase transition-colors duration-300"
                        :class="
                            isNight
                                ? 'text-frost hover:text-paper-white'
                                : 'text-paper-white/70 hover:text-paper-white'
                        "
                        @click="mobileOpen = false"
                    >
                        {{ link.label }}
                    </a>
                </div>
            </header>

            <!-- ===== HERO CONTENT ===== -->
            <section
                class="pointer-events-none relative z-10 flex min-h-[calc(100vh-80px)] flex-col items-center justify-center px-6 pb-16"
            >
                <div
                    class="animate-fade-up pointer-events-auto mb-8 inline-flex items-center gap-2 rounded-[14385px] border px-4 py-1.5 backdrop-blur-sm transition-colors duration-700"
                    :class="
                        isNight
                            ? 'border-bone/10 bg-obsidian/50'
                            : 'border-paper-white/15 bg-paper-white/10'
                    "
                    :style="{ animationDelay: '100ms' }"
                >
                    <span class="animate-shimmer text-[11px]">&#10024;</span>

                    <span
                        class="text-[11px] font-normal tracking-[0.182em] uppercase transition-colors duration-700"
                        :class="isNight ? 'text-bone' : 'text-obsidian'"
                    >
                        Smart Applicant Tracking System
                    </span>
                </div>

                <h1
                    class="animate-fade-up mb-6 max-w-3xl text-center font-lyon-display text-[48px] leading-[0.9] font-light tracking-tight transition-colors duration-700 sm:text-[64px] md:text-heading-lg lg:text-display"
                    :class="isNight ? 'text-bone' : 'text-obsidian'"
                    :style="{ animationDelay: '200ms' }"
                >
                    Hire smarter,
                    <br class="hidden sm:block" />
                    not slower
                </h1>

                <p
                    class="animate-fade-up mb-10 max-w-[520px] text-center text-body leading-[1.5] font-light transition-colors duration-700 md:text-subheading"
                    :class="isNight ? 'text-frost' : 'text-slate'"
                    :style="{ animationDelay: '300ms' }"
                >
                    SmartRecruit helps HR teams screen, rank, and manage
                    applicants with AI-powered CV analysis.
                </p>

                <div
                    class="animate-fade-up pointer-events-auto mb-10 flex flex-col items-center gap-4 sm:flex-row"
                    :style="{ animationDelay: '400ms' }"
                >
                    <template v-if="!$page.props.auth.user">
                        <Link
                            :href="register()"
                            class="group relative inline-flex items-center gap-2.5 overflow-hidden rounded-full bg-paper-white px-8 py-4 text-base font-medium text-carbon transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_30px_rgba(245,245,247,0.15)] active:scale-[0.97] active:shadow-sm"
                        >
                            <span class="animate-shine"></span>
                            <span
                                class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-white/40 to-transparent"
                            ></span>
                            <span class="relative">Get Started</span>

                            <svg
                                class="relative h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"
                                />
                            </svg>
                        </Link>

                        <a
                            href="#about"
                            class="group inline-flex items-center gap-2 rounded-full border px-8 py-4 text-base font-medium backdrop-blur-sm transition-all duration-300"
                            :class="
                                isNight
                                    ? 'border-bone/20 bg-bone/5 text-bone hover:border-bone/30 hover:bg-bone/10'
                                    : 'border-paper-white/20 bg-paper-white/10 text-paper-white hover:border-paper-white/30 hover:bg-paper-white/15'
                            "
                        >
                            <span>Learn More</span>
                            <span
                                class="h-1.5 w-1.5 rounded-full transition-all duration-300 group-hover:scale-150"
                                :class="
                                    isNight
                                        ? 'bg-bone/40 group-hover:bg-bone/70'
                                        : 'bg-paper-white/50 group-hover:bg-paper-white/80'
                                "
                            ></span>
                        </a>
                    </template>

                    <template v-else>
                        <Link
                            :href="dashboard()"
                            class="group relative inline-flex items-center gap-2.5 overflow-hidden rounded-full bg-paper-white px-8 py-4 text-base font-medium text-carbon transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_8px_30px_rgba(245,245,247,0.15)] active:scale-[0.97] active:shadow-sm"
                        >
                            <span class="animate-shine"></span>
                            <span
                                class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-white/40 to-transparent"
                            ></span>
                            <span class="relative">Go to Dashboard</span>

                            <svg
                                class="relative h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"
                                />
                            </svg>
                        </Link>
                    </template>
                </div>

                <div
                    class="animate-fade-up pointer-events-auto w-full max-w-[600px]"
                    :style="{ animationDelay: '500ms' }"
                >
                    <div
                        class="flex items-center gap-3 rounded-3xl-2 px-5 py-3 backdrop-blur-sm transition-all duration-700 focus-within:ring-1"
                        :class="
                            isNight
                                ? 'bg-graphite/80 focus-within:bg-graphite/90 focus-within:ring-frost/20'
                                : 'bg-paper-white/10 focus-within:bg-paper-white/15 focus-within:ring-paper-white/20'
                        "
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5 shrink-0 transition-colors duration-700"
                            :class="
                                isNight ? 'text-mist' : 'text-paper-white/70'
                            "
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>

                        <input
                            type="text"
                            placeholder="Who's the best fit for Senior Developer?"
                            class="flex-1 bg-transparent text-body-sm transition-colors duration-700 placeholder:text-mist/60 focus:outline-none sm:text-body"
                            :class="isNight ? 'text-bone' : 'text-paper-white'"
                            readonly
                        />

                        <button
                            type="button"
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-paper-white text-carbon transition-transform duration-300 hover:scale-105"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="size-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </button>
                    </div>
                </div>
            </section>

            <!-- ============================================================ -->
            <!-- ABOUT ME SECTION                                             -->
            <!-- ============================================================ -->
            <section id="about" class="relative min-h-screen flex items-center justify-center px-6 py-24">
                <div class="mx-auto max-w-6xl w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                    <div class="scroll-reveal order-2 lg:order-1 flex justify-center">
                        <div class="group relative w-full max-w-sm rounded-[32px] p-[2px] overflow-hidden transition-all duration-500 hover:scale-[1.02]">
                            <div class="absolute inset-0 bg-linear-to-br from-amethyst/50 via-transparent to-cyan-400/50 opacity-0 transition-opacity duration-500 group-hover:opacity-100 blur-md pointer-events-none"></div>
                            <div class="absolute inset-0 bg-linear-to-br from-amethyst via-transparent to-cyan-400 opacity-20 pointer-events-none"></div>
                            <div class="relative rounded-3xl-2 p-2 h-[450px] w-full overflow-hidden border backdrop-blur-xl transition-colors duration-700"
                                 :class="isNight ? 'bg-obsidian/80 border-bone/10 shadow-2xl' : 'bg-bone/80 border-obsidian/10 shadow-xl'">
                                <img src="/images/about_me.jpg" alt="About Me Graphic" class="h-full w-full object-cover rounded-[22px] transition-transform duration-700 group-hover:scale-105" />
                            </div>
                        </div>
                    </div>

                    <div class="scroll-reveal order-1 lg:order-2 flex flex-col gap-6">
                        <div class="inline-flex items-center gap-2 rounded-full border px-4 py-1.5 w-fit transition-colors duration-700"
                             :class="isNight ? 'border-bone/10 bg-obsidian/40' : 'border-obsidian/10 bg-bone/40'">
                            <span class="animate-shimmer text-[11px] text-cyan-400">&#10024;</span>
                            <span class="text-[11px] font-normal tracking-[0.182em] uppercase transition-colors duration-700"
                                  :class="isNight ? 'text-bone' : 'text-obsidian'">Tentang Saya</span>
                        </div>
                        <h2 class="font-lyon-display text-4xl sm:text-5xl lg:text-6xl leading-[1.1] font-light transition-colors duration-700"
                            :class="isNight ? 'text-bone' : 'text-obsidian'">
                            Inovator. <br />
                            <span class="text-transparent bg-clip-text bg-linear-to-r from-amethyst to-cyan-400">Pengembang.</span> <br />
                            Pemikir Kritis.
                        </h2>
                        <p class="text-body leading-[1.6] font-light max-w-lg mt-2 transition-colors duration-700"
                           :class="isNight ? 'text-frost' : 'text-slate'">
                            Saya adalah seorang Senior Full-Stack Developer yang berdedikasi membangun pengalaman digital modern. Dengan keahlian dalam Laravel, Vue, dan Integrasi AI, saya menciptakan solusi elegan untuk masalah yang kompleks. Saya merancang ekosistem ATS yang terukur, responsif, dan berdampak.
                        </p>
                        <div class="flex flex-wrap gap-3 mt-4">
                            <span v-for="skill in ['Laravel', 'Vue 3', 'Tailwind v4', 'OpenAI']" :key="skill"
                                  class="px-4 py-2 rounded-full border text-xs tracking-wider uppercase transition-colors duration-700 cursor-default"
                                  :class="isNight ? 'border-bone/10 bg-bone/5 text-bone hover:bg-bone/10 hover:border-amethyst/50' : 'border-obsidian/10 bg-obsidian/5 text-obsidian hover:bg-obsidian/10 hover:border-amethyst/50'">
                                {{ skill }}
                            </span>
                        </div>
                    </div>

                </div>
            </section>

            <!-- ============================================================ -->
            <!-- RATINGS & TESTIMONIALS SECTION                               -->
            <!-- ============================================================ -->
            <section id="ratings" class="relative min-h-screen flex items-center justify-center px-6 py-24 transition-colors duration-700"
                     :class="isNight ? 'bg-linear-to-b from-transparent via-obsidian/50 to-transparent' : 'bg-linear-to-b from-transparent via-bone/50 to-transparent'">
                <div class="mx-auto max-w-6xl w-full flex flex-col items-center">

                    <div class="scroll-reveal flex flex-col items-center text-center mb-16">
                        <div class="inline-flex items-center gap-2 rounded-full border px-4 py-1.5 w-fit mb-6 transition-colors duration-700"
                             :class="isNight ? 'border-bone/10 bg-obsidian/40' : 'border-obsidian/10 bg-bone/40'">
                            <span class="text-[11px] font-normal tracking-[0.182em] uppercase transition-colors duration-700"
                                  :class="isNight ? 'text-bone' : 'text-obsidian'">Kualitas & Reputasi</span>
                        </div>
                        <h2 class="font-lyon-display text-4xl sm:text-5xl leading-[1.1] font-light transition-colors duration-700"
                            :class="isNight ? 'text-bone' : 'text-obsidian'">
                            Dipercaya oleh Klien
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">

                        <!-- Card 1 -->
                        <div class="scroll-reveal group relative rounded-3xl p-8 border backdrop-blur-md transition-all duration-300 hover:-translate-y-2"
                             :class="isNight ? 'border-bone/10 bg-obsidian/60 hover:border-amethyst/50 hover:bg-obsidian/80' : 'border-obsidian/10 bg-bone/60 hover:border-amethyst/50 hover:bg-bone/80'">
                            <div class="absolute inset-0 bg-linear-to-br from-amethyst/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100 rounded-3xl pointer-events-none"></div>
                            <div class="flex items-center gap-2 mb-4">
                                <template v-for="i in 5" :key="i">
                                    <svg class="w-5 h-5 text-yellow-400 drop-shadow-[0_0_8px_rgba(250,204,21,0.5)]" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </template>
                            </div>
                            <p class="text-body-sm font-light leading-[1.6] mb-6 transition-colors duration-700" :class="isNight ? 'text-frost' : 'text-slate'">
                                "Pengembangan yang luar biasa cepat dan arsitektur kode yang sangat bersih. Sistem ATS ini mengubah cara tim kami merekrut secara drastis."
                            </p>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-linear-to-br from-amethyst to-cyan-400 flex items-center justify-center text-bone font-medium">B</div>
                                <div>
                                    <h4 class="text-sm font-medium transition-colors duration-700" :class="isNight ? 'text-bone' : 'text-obsidian'">Budi Santoso</h4>
                                    <p class="text-xs transition-colors duration-700" :class="isNight ? 'text-frost' : 'text-slate'">HR Director, TechNusa</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="scroll-reveal group relative rounded-3xl p-8 border backdrop-blur-md transition-all duration-300 hover:-translate-y-2" style="transition-delay: 100ms;"
                             :class="isNight ? 'border-bone/10 bg-obsidian/60 hover:border-cyan-400/50 hover:bg-obsidian/80' : 'border-obsidian/10 bg-bone/60 hover:border-cyan-400/50 hover:bg-bone/80'">
                            <div class="absolute inset-0 bg-linear-to-br from-cyan-400/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100 rounded-3xl pointer-events-none"></div>
                            <h3 class="text-5xl font-lyon-display text-transparent bg-clip-text bg-linear-to-r from-cyan-400 to-blue-500 mb-2">98%</h3>
                            <p class="font-medium mb-4 transition-colors duration-700" :class="isNight ? 'text-bone' : 'text-obsidian'">Kepuasan Klien</p>
                            <p class="text-body-sm font-light leading-[1.6] transition-colors duration-700" :class="isNight ? 'text-frost' : 'text-slate'">
                                Metrik keberhasilan dari 50+ proyek pengembangan dalam dua tahun terakhir. Fokus utama pada stabilitas, keamanan, dan desain modern.
                            </p>
                        </div>

                        <!-- Card 3 -->
                        <div class="scroll-reveal group relative rounded-3xl p-8 border backdrop-blur-md transition-all duration-300 hover:-translate-y-2" style="transition-delay: 200ms;"
                             :class="isNight ? 'border-bone/10 bg-obsidian/60 hover:border-amethyst/50 hover:bg-obsidian/80' : 'border-obsidian/10 bg-bone/60 hover:border-amethyst/50 hover:bg-bone/80'">
                            <div class="absolute inset-0 bg-linear-to-br from-amethyst/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100 rounded-3xl pointer-events-none"></div>
                            <div class="flex items-center gap-2 mb-4">
                                <template v-for="i in 5" :key="i">
                                    <svg class="w-5 h-5 text-yellow-400 drop-shadow-[0_0_8px_rgba(250,204,21,0.5)]" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </template>
                            </div>
                            <p class="text-body-sm font-light leading-[1.6] mb-6 transition-colors duration-700" :class="isNight ? 'text-frost' : 'text-slate'">
                                "Kode yang sangat rapi dan UI yang menakjubkan. Integrasi AI-nya berjalan mulus tanpa error. Pengalaman pengguna yang sangat premium."
                            </p>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-linear-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-bone font-medium">A</div>
                                <div>
                                    <h4 class="text-sm font-medium transition-colors duration-700" :class="isNight ? 'text-bone' : 'text-obsidian'">Anita Wijaya</h4>
                                    <p class="text-xs transition-colors duration-700" :class="isNight ? 'text-frost' : 'text-slate'">Product Manager</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<style scoped>
.scroll-reveal {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 1s cubic-bezier(0.16, 1, 0.3, 1), transform 1s cubic-bezier(0.16, 1, 0.3, 1);
}
.scroll-reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
}

.preview-enter-active,
.preview-leave-active {
    transition:
        opacity 0.3s ease,
        transform 0.3s ease;
}

.preview-enter-from {
    opacity: 0;
    transform: translateY(8px);
}

.preview-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
@keyframes nebula-drift {
    0% {
        transform: translate3d(0, 0, 0) scale(1);
        filter: hue-rotate(0deg);
    }

    50% {
        transform: translate3d(-2%, 1.5%, 0) scale(1.04);
        filter: hue-rotate(18deg);
    }

    100% {
        transform: translate3d(0, 0, 0) scale(1);
        filter: hue-rotate(0deg);
    }
}

.animate-nebula-drift {
    animation: nebula-drift 16s ease-in-out infinite;
}
</style>
