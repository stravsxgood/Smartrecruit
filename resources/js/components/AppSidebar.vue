<script setup lang="ts">
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faXmark } from '@fortawesome/free-solid-svg-icons';
import { faLaravel, faVuejs } from '@fortawesome/free-brands-svg-icons';

type SidebarNavKey = 'dashboard' | 'cv-correction' | 'candidate-file-upload';

type SidebarNavItem = {
    key: SidebarNavKey;
    title: string;
    href: string;
};

const page = usePage();

const mainNavItems: SidebarNavItem[] = [
    {
        key: 'dashboard',
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        key: 'cv-correction',
        title: 'CV Correction',
        href: '/job-positions',
    },
    {
        key: 'candidate-file-upload',
        title: 'Candidate File Upload',
        href: '/my-profile',
    },
];

const currentUrl = computed(() => page.url);

const normalizeUrl = (url: string) => {
    return url.split('?')[0].replace(/\/$/, '') || '/';
};

const isActive = (href: string) => {
    const current = normalizeUrl(currentUrl.value);
    const target = normalizeUrl(href);

    if (target === '/dashboard') {
        return current === '/dashboard';
    }

    return current === target || current.startsWith(`${target}/`);
};

const menuItemClass = (href: string) => {
    const active = isActive(href);

    return [
        'group relative flex w-full items-center gap-3 overflow-hidden rounded-2xl border px-3 py-3 text-sm font-semibold transition-all duration-300 ease-out',
        'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-950 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-white dark:focus-visible:ring-offset-black',
        active
            ? 'border-neutral-300 bg-neutral-100 text-neutral-950 shadow-sm dark:border-neutral-700 dark:bg-neutral-900 dark:text-white dark:shadow-[0_16px_38px_rgba(255,255,255,0.08)]'
            : 'border-transparent text-neutral-500 hover:-translate-y-0.5 hover:border-neutral-200 hover:bg-neutral-100 hover:text-neutral-950 hover:shadow-sm dark:text-neutral-500 dark:hover:border-neutral-800 dark:hover:bg-neutral-900/80 dark:hover:text-white dark:hover:shadow-[0_14px_30px_rgba(255,255,255,0.055)]',
    ];
};

const iconWrapClass = (href: string) => {
    const active = isActive(href);

    return [
        'flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border transition-all duration-300',
        active
            ? 'border-neutral-300 bg-neutral-950 text-white shadow-sm dark:border-neutral-500 dark:bg-white dark:text-black dark:shadow-[0_10px_26px_rgba(255,255,255,0.12)]'
            : 'border-neutral-200 bg-white text-neutral-500 group-hover:border-neutral-300 group-hover:bg-neutral-50 group-hover:text-neutral-950 dark:border-neutral-800 dark:bg-black dark:text-neutral-400 dark:group-hover:border-neutral-700 dark:group-hover:bg-neutral-950 dark:group-hover:text-white',
    ];
};
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset"
        class="border-r border-neutral-200 bg-white text-neutral-950 transition-colors duration-300 dark:border-neutral-800 dark:bg-black dark:text-white"
    >
        <!-- Sidebar Header -->
        <SidebarHeader
            class="bg-white px-4 pt-5 pb-6 transition-colors duration-300 dark:bg-black"
        >
            <div class="flex items-center justify-center gap-3">
                <!-- Laravel Logo -->
                <FontAwesomeIcon
                    :icon="faLaravel"
                    class="text-neutral-950 dark:text-white"
                    style="width: 40px; height: 40px"
                />

                <!-- Collaboration X -->
                <div
                    class="flex h-7 w-7 items-center justify-center rounded-full border border-neutral-200 bg-neutral-100 text-neutral-500 transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:text-neutral-500"
                    aria-hidden="true"
                >
                    <FontAwesomeIcon
                        :icon="faXmark"
                        class="text-neutral-500 dark:text-neutral-400"
                        style="width: 12px; height: 12px"
                    />
                </div>

                <!-- Vue Logo -->
                <FontAwesomeIcon
                    :icon="faVuejs"
                    class="text-neutral-950 dark:text-white"
                    style="width: 40px; height: 40px"
                />
            </div>
        </SidebarHeader>

        <!-- Sidebar Content -->
        <SidebarContent
            class="bg-white px-3 py-4 transition-colors duration-300 dark:bg-black"
        >
            <div
                class="mb-3 px-3 text-xs font-semibold tracking-widest text-neutral-500 uppercase group-data-[collapsible=icon]:hidden dark:text-neutral-600"
            >
                Workspace
            </div>

            <SidebarMenu class="space-y-2">
                <SidebarMenuItem v-for="item in mainNavItems" :key="item.key">
                    <Link :href="item.href" :class="menuItemClass(item.href)">
                        <!-- Active left indicator -->
                        <span
                            v-if="isActive(item.href)"
                            class="absolute top-1/2 left-0 h-8 w-1 -translate-y-1/2 rounded-r-full bg-neutral-950 shadow-[0_0_18px_rgba(0,0,0,0.18)] dark:bg-white dark:shadow-[0_0_18px_rgba(255,255,255,0.5)]"
                        />

                        <!-- Icon wrapper -->
                        <span :class="iconWrapClass(item.href)">
                            <!-- Dashboard Icon -->
                            <svg
                                v-if="item.key === 'dashboard'"
                                class="h-5 w-5 transition-transform duration-300 group-hover:scale-110"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <rect
                                    x="3"
                                    y="3"
                                    width="7"
                                    height="7"
                                    rx="1.5"
                                />
                                <rect
                                    x="14"
                                    y="3"
                                    width="7"
                                    height="7"
                                    rx="1.5"
                                />
                                <rect
                                    x="3"
                                    y="14"
                                    width="7"
                                    height="7"
                                    rx="1.5"
                                />
                                <rect
                                    x="14"
                                    y="14"
                                    width="7"
                                    height="7"
                                    rx="1.5"
                                />
                            </svg>

                            <!-- CV Correction Icon -->
                            <svg
                                v-else-if="item.key === 'cv-correction'"
                                class="h-5 w-5 transition-transform duration-300 group-hover:scale-110"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="M10 4h4" />
                                <path
                                    d="M4 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8Z"
                                />
                                <path d="M8 12h8" />
                                <path d="M8 16h5" />
                            </svg>

                            <!-- Candidate Upload Icon -->
                            <svg
                                v-else
                                class="h-5 w-5 transition-transform duration-300 group-hover:scale-110"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="M20 21a8 8 0 0 0-16 0" />
                                <circle cx="12" cy="7" r="4" />
                                <path d="M12 14v5" />
                                <path d="M9.5 16.5 12 14l2.5 2.5" />
                            </svg>
                        </span>

                        <!-- Label -->
                        <span
                            class="min-w-0 flex-1 truncate group-data-[collapsible=icon]:hidden"
                        >
                            {{ item.title }}
                        </span>

                        <!-- Active arrow -->
                        <svg
                            v-if="isActive(item.href)"
                            class="h-4 w-4 shrink-0 text-neutral-950 group-data-[collapsible=icon]:hidden dark:text-white"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="m9 18 6-6-6-6" />
                        </svg>

                        <!-- Hover arrow -->
                        <svg
                            v-else
                            class="h-4 w-4 shrink-0 translate-x-1 text-neutral-400 opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:text-neutral-600 group-hover:opacity-100 group-data-[collapsible=icon]:hidden dark:text-neutral-700 dark:group-hover:text-neutral-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </Link>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarContent>

        <!-- Sidebar Footer -->
        <SidebarFooter
            class="border-t border-neutral-200 bg-white px-3 py-4 transition-colors duration-300 dark:border-neutral-800 dark:bg-black"
        >
            <div
                class="mb-3 px-3 text-xs font-semibold tracking-widest text-neutral-500 uppercase group-data-[collapsible=icon]:hidden dark:text-neutral-600"
            >
                Account
            </div>

            <div
                class="rounded-2xl border border-neutral-200 bg-neutral-50 p-2 transition-all duration-300 hover:border-neutral-300 hover:bg-neutral-100 hover:shadow-sm dark:border-neutral-800 dark:bg-[#111111] dark:hover:border-neutral-700 dark:hover:bg-neutral-900 dark:hover:shadow-[0_16px_35px_rgba(255,255,255,0.05)]"
            >
                <NavUser />
            </div>
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
