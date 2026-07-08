<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { toUrl } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { edit as editSecurity } from '@/routes/security';

type AuthUser = {
    id: number;
    name: string;
    email: string;
    avatar_url?: string | null;
};

const page = usePage();

const user = computed(() => {
    return page.props.auth.user as AuthUser | null;
});

const userInitial = computed(() => {
    return user.value?.name?.charAt(0)?.toUpperCase() ?? 'U';
});

const sidebarNavItems = [
    {
        key: 'profile',
        title: 'Profile',
        description: 'Account identity',
        href: editProfile(),
    },
    {
        key: 'security',
        title: 'Security',
        description: 'Password & access',
        href: editSecurity(),
    },
    {
        key: 'appearance',
        title: 'Appearance',
        description: 'Theme preference',
        href: editAppearance(),
    },
] as const;

const { isCurrentOrParentUrl } = useCurrentUrl();

const isActive = (href: (typeof sidebarNavItems)[number]['href']) => {
    return isCurrentOrParentUrl(href);
};

const activeItem = computed(() => {
    return (
        sidebarNavItems.find((item) => isActive(item.href)) ??
        sidebarNavItems[0]
    );
});
</script>

<template>
    <div class="w-full min-w-0 text-neutral-950 dark:text-white">
        <div class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
            <!-- Header -->
            <header
                class="relative overflow-hidden rounded-3xl border border-neutral-200 bg-white px-6 py-7 shadow-sm transition-colors duration-300 md:px-8 md:py-8 dark:border-neutral-800 dark:bg-[#111111] dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)]"
            >
                <div
                    class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(0,0,0,0.055),transparent_32%)] dark:bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.08),transparent_32%)]"
                />

                <div
                    class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between"
                >
                    <div>
                        <div
                            class="mb-4 inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-neutral-100 px-4 py-2 text-xs font-semibold tracking-[0.22em] text-neutral-500 uppercase dark:border-neutral-800 dark:bg-black dark:text-neutral-500"
                        >
                            <span
                                class="h-1.5 w-1.5 rounded-full bg-neutral-950 dark:bg-white"
                            />
                            Account Settings
                        </div>

                        <h1
                            class="text-4xl leading-none font-semibold tracking-tight text-neutral-950 md:text-5xl dark:text-white"
                        >
                            Settings
                        </h1>

                        <p
                            class="mt-4 max-w-2xl text-sm leading-6 text-neutral-600 dark:text-neutral-500"
                        >
                            Manage your profile, security, and appearance
                            preferences in one clean workspace.
                        </p>
                    </div>

                    <div
                        class="flex w-full items-center gap-3 rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 transition-colors duration-300 lg:w-auto lg:min-w-[240px] dark:border-neutral-800 dark:bg-black"
                    >
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-neutral-200 bg-white text-neutral-950 transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:text-white"
                        >
                            <!-- Profile Photo -->
                            <img
                                v-if="
                                    activeItem.key === 'profile' &&
                                    user?.avatar_url
                                "
                                :src="user.avatar_url"
                                :alt="user.name"
                                class="h-full w-full object-cover"
                            />

                            <!-- Profile Initial Fallback -->
                            <span
                                v-else-if="activeItem.key === 'profile'"
                                class="text-sm font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ userInitial }}
                            </span>

                            <!-- Security Icon -->
                            <svg
                                v-else-if="activeItem.key === 'security'"
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect
                                    x="4"
                                    y="10"
                                    width="16"
                                    height="10"
                                    rx="2"
                                />
                                <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                            </svg>

                            <!-- Appearance Icon -->
                            <svg
                                v-else
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <circle cx="12" cy="12" r="4" />
                                <path d="M12 2v2" />
                                <path d="M12 20v2" />
                                <path d="m4.93 4.93 1.41 1.41" />
                                <path d="m17.66 17.66 1.41 1.41" />
                                <path d="M2 12h2" />
                                <path d="M20 12h2" />
                                <path d="m6.34 17.66-1.41 1.41" />
                                <path d="m19.07 4.93-1.41 1.41" />
                            </svg>
                        </div>

                        <div class="min-w-0">
                            <p
                                class="truncate text-sm font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ activeItem.title }}
                            </p>

                            <p class="truncate text-xs text-neutral-500">
                                Current page
                            </p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Body -->
            <div
                class="mt-6 grid w-full min-w-0 grid-cols-1 gap-6 lg:grid-cols-[280px_minmax(0,1fr)] lg:items-start"
            >
                <!-- Settings Navigation -->
                <aside class="w-full min-w-0 lg:sticky lg:top-6">
                    <div
                        class="rounded-3xl border border-neutral-200 bg-white p-4 shadow-sm transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)]"
                    >
                        <div
                            class="mb-3 px-2 pt-1 text-xs font-semibold tracking-[0.22em] text-neutral-500 uppercase dark:text-neutral-600"
                        >
                            Settings Menu
                        </div>

                        <nav
                            class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-1"
                            aria-label="Settings"
                        >
                            <Link
                                v-for="item in sidebarNavItems"
                                :key="toUrl(item.href)"
                                :href="item.href"
                                :aria-current="
                                    isActive(item.href) ? 'page' : undefined
                                "
                                :class="[
                                    'group relative flex min-h-[76px] min-w-0 items-center gap-4 rounded-2xl border px-4 py-4 text-left transition-all duration-300',
                                    isActive(item.href)
                                        ? 'border-neutral-300 bg-neutral-100 text-neutral-950 shadow-sm dark:border-neutral-600 dark:bg-neutral-900 dark:text-white dark:shadow-[0_14px_32px_rgba(255,255,255,0.045)]'
                                        : 'border-transparent bg-neutral-50 text-neutral-600 hover:border-neutral-200 hover:bg-neutral-100 hover:text-neutral-950 dark:bg-black/80 dark:text-neutral-500 dark:hover:border-neutral-700 dark:hover:bg-neutral-950 dark:hover:text-white',
                                ]"
                            >
                                <span
                                    v-if="isActive(item.href)"
                                    class="absolute top-1/2 left-0 h-9 w-1 -translate-y-1/2 rounded-r-full bg-neutral-950 shadow-[0_0_18px_rgba(0,0,0,0.18)] dark:bg-white/80 dark:shadow-[0_0_18px_rgba(255,255,255,0.25)]"
                                />

                                <span
                                    :class="[
                                        'flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl border transition-colors duration-300',
                                        isActive(item.href)
                                            ? 'border-neutral-300 bg-neutral-950 text-white dark:border-neutral-600 dark:bg-black dark:text-white'
                                            : 'border-neutral-200 bg-white text-neutral-500 group-hover:border-neutral-300 group-hover:text-neutral-950 dark:border-neutral-800 dark:bg-[#111111] dark:text-neutral-400 dark:group-hover:border-neutral-700 dark:group-hover:text-white',
                                    ]"
                                >
                                    <img
                                        v-if="
                                            item.key === 'profile' &&
                                            user?.avatar_url
                                        "
                                        :src="user.avatar_url"
                                        :alt="user.name"
                                        class="h-full w-full object-cover"
                                    />

                                    <span
                                        v-else-if="item.key === 'profile'"
                                        class="text-sm font-semibold"
                                    >
                                        {{ userInitial }}
                                    </span>

                                    <svg
                                        v-else-if="item.key === 'security'"
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <rect
                                            x="4"
                                            y="10"
                                            width="16"
                                            height="10"
                                            rx="2"
                                        />
                                        <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                                    </svg>

                                    <svg
                                        v-else
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <circle cx="12" cy="12" r="4" />
                                        <path d="M12 2v2" />
                                        <path d="M12 20v2" />
                                        <path d="m4.93 4.93 1.41 1.41" />
                                        <path d="m17.66 17.66 1.41 1.41" />
                                        <path d="M2 12h2" />
                                        <path d="M20 12h2" />
                                        <path d="m6.34 17.66-1.41 1.41" />
                                        <path d="m19.07 4.93-1.41 1.41" />
                                    </svg>
                                </span>

                                <span class="min-w-0 flex-1">
                                    <span
                                        class="block truncate text-sm leading-5 font-semibold"
                                    >
                                        {{ item.title }}
                                    </span>

                                    <span
                                        :class="[
                                            'mt-1.5 block truncate text-xs leading-5',
                                            isActive(item.href)
                                                ? 'text-neutral-600 dark:text-neutral-400'
                                                : 'text-neutral-500 dark:text-neutral-600 dark:group-hover:text-neutral-500',
                                        ]"
                                    >
                                        {{ item.description }}
                                    </span>
                                </span>

                                <svg
                                    v-if="isActive(item.href)"
                                    class="h-4 w-4 shrink-0 text-neutral-600 dark:text-neutral-300"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="m9 18 6-6-6-6" />
                                </svg>
                            </Link>
                        </nav>
                    </div>
                </aside>

                <!-- Settings Content -->
                <main class="min-w-0 overflow-hidden">
                    <section class="w-full min-w-0 space-y-6">
                        <slot />
                    </section>
                </main>
            </div>
        </div>
    </div>
</template>
