<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faBars, faHouse } from '@fortawesome/free-solid-svg-icons';

import type { BreadcrumbItem } from '@/types';
import { useSidebar } from '@/components/ui/sidebar/utils';

const props = withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const { toggleSidebar } = useSidebar();

const visibleBreadcrumbs = computed(() => props.breadcrumbs ?? []);

const currentPageTitle = computed(() => {
    const items = visibleBreadcrumbs.value;

    return items.length > 0 ? items[items.length - 1].title : 'Workspace';
});

const pageDescription = computed(() => {
    const title = currentPageTitle.value.toLowerCase();

    if (title.includes('dashboard')) {
        return 'Overview aktivitas dan progress aplikasi.';
    }

    if (title.includes('profile') || title.includes('my profile')) {
        return 'Kelola data akun dan dokumen kandidat.';
    }

    if (title.includes('security')) {
        return 'Atur keamanan akun dan akses login.';
    }

    if (title.includes('appearance')) {
        return 'Sesuaikan tampilan aplikasi.';
    }

    if (title.includes('upload') || title.includes('cv')) {
        return 'Upload dan analisis dokumen CV.';
    }

    return 'Kelola workspace aplikasi kamu.';
});
</script>

<template>
    <header
        class="sticky top-0 z-30 border-b border-neutral-200 bg-white/85 text-neutral-950 backdrop-blur-xl transition-colors duration-300 dark:border-neutral-800 dark:bg-black/85 dark:text-white"
    >
        <div class="relative overflow-hidden">
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(0,0,0,0.055),transparent_34%)] dark:bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.08),transparent_34%)]"
            />

            <div
                class="relative flex min-h-16 w-full items-center justify-between gap-3 px-3 sm:px-6 lg:px-8"
            >
                <!-- Left Area -->
                <div class="flex min-w-0 items-center gap-3">
                    <!-- Mobile Sidebar Toggle -->
                    <button
                        type="button"
                        aria-label="Open sidebar"
                        title="Open sidebar"
                        @click="toggleSidebar"
                        class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-700 shadow-sm transition-all duration-300 hover:border-neutral-300 hover:bg-neutral-100 hover:text-neutral-950 active:scale-95 lg:hidden dark:border-neutral-800 dark:bg-[#111111] dark:text-neutral-300 dark:hover:border-neutral-700 dark:hover:bg-neutral-900 dark:hover:text-white"
                    >
                        <FontAwesomeIcon :icon="faBars" class="h-4 w-4" />
                    </button>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <p
                                class="truncate text-[11px] font-semibold tracking-[0.22em] text-neutral-500 uppercase dark:text-neutral-600"
                            >
                                Current Workspace
                            </p>

                            <div class="hidden items-center gap-1.5 sm:flex">
                                <span
                                    class="h-1.5 w-1.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.8)]"
                                />

                                <p
                                    class="truncate text-[11px] font-semibold text-green-500"
                                >
                                    Active page
                                </p>
                            </div>
                        </div>

                        <h1
                            class="mt-1 truncate text-base font-semibold tracking-tight text-neutral-950 transition-colors duration-300 sm:text-lg dark:text-white"
                        >
                            {{ currentPageTitle }}
                        </h1>

                        <p
                            class="mt-0.5 hidden truncate text-xs leading-5 text-neutral-500 sm:block dark:text-neutral-600"
                        >
                            {{ pageDescription }}
                        </p>
                    </div>
                </div>

                <!-- Right Action: Back to Welcome -->
                <Link
                    href="/"
                    class="group inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-xl border border-neutral-200 bg-white px-3 text-sm font-semibold text-neutral-700 shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-neutral-300 hover:bg-neutral-100 hover:text-neutral-950 active:scale-95 sm:px-4 dark:border-neutral-800 dark:bg-[#111111] dark:text-neutral-300 dark:hover:border-neutral-700 dark:hover:bg-neutral-900 dark:hover:text-white"
                    title="Back to Welcome"
                >
                    <FontAwesomeIcon :icon="faHouse" class="h-4 w-4" />

                    <span>Home</span>
                </Link>
            </div>
        </div>
    </header>
</template>
