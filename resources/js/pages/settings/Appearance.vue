<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Monitor, Moon, Sun } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

import type { Appearance } from '@/types';
import { useAppearance } from '@/composables/useAppearance';

defineOptions({
    inheritAttrs: false,
    layout: {
        breadcrumbs: [
            {
                title: 'Appearance settings',
                href: '/settings/appearance',
            },
        ],
    },
});

const { appearance, resolvedAppearance, updateAppearance } = useAppearance();

const isMounted = ref(false);

onMounted(() => {
    isMounted.value = true;
});

const tabs = [
    {
        value: 'light',
        icon: Sun,
        label: 'Light',
        description: 'Gunakan tampilan terang.',
    },
    {
        value: 'dark',
        icon: Moon,
        label: 'Dark',
        description: 'Gunakan tampilan gelap.',
    },
    {
        value: 'system',
        icon: Monitor,
        label: 'System',
        description: 'Ikuti tema perangkat.',
    },
] as const;

const isActive = (value: Appearance) => {
    return isMounted.value && appearance.value === value;
};

const currentPreferenceLabel = computed(() => {
    if (!isMounted.value) {
        return 'Loading';
    }

    if (appearance.value === 'light') {
        return 'Light';
    }

    if (appearance.value === 'dark') {
        return 'Dark';
    }

    return 'System';
});

const resolvedThemeLabel = computed(() => {
    if (!isMounted.value) {
        return 'Loading';
    }

    return resolvedAppearance.value === 'dark' ? 'Dark' : 'Light';
});
</script>

<template>
    <Head title="Appearance settings" />

    <div class="w-full max-w-5xl space-y-6 text-neutral-950 dark:text-white">
        <section
            class="overflow-hidden rounded-3xl border border-neutral-200 bg-white shadow-sm transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)]"
        >
            <div
                class="border-b border-neutral-200 px-6 py-6 transition-colors duration-300 dark:border-neutral-800"
            >
                <h2
                    class="text-xl font-semibold tracking-tight text-neutral-950 dark:text-white"
                >
                    Appearance
                </h2>

                <p
                    class="mt-2 max-w-2xl text-sm leading-6 text-neutral-600 dark:text-neutral-500"
                >
                    Pilih preferensi tampilan aplikasi. Mode System akan
                    mengikuti pengaturan tema dari perangkat kamu.
                </p>
            </div>

            <div class="space-y-6 px-6 py-6">
                <div
                    v-if="isMounted"
                    class="grid grid-cols-1 gap-3 md:grid-cols-3"
                >
                    <button
                        v-for="tab in tabs"
                        :key="tab.value"
                        type="button"
                        @click="updateAppearance(tab.value)"
                        :aria-pressed="isActive(tab.value)"
                        :class="[
                            'group relative flex min-h-120 flex-col items-start gap-4 rounded-2xl border p-5 text-left transition-all duration-300',
                            isActive(tab.value)
                                ? 'border-neutral-300 bg-neutral-100 text-neutral-950 shadow-sm dark:border-neutral-600 dark:bg-neutral-900 dark:text-white dark:shadow-[0_14px_32px_rgba(255,255,255,0.045)]'
                                : 'border-neutral-200 bg-neutral-50 text-neutral-600 hover:border-neutral-300 hover:bg-neutral-100 hover:text-neutral-950 dark:border-neutral-800 dark:bg-black dark:text-neutral-500 dark:hover:border-neutral-700 dark:hover:bg-neutral-950 dark:hover:text-white',
                        ]"
                    >
                        <span
                            v-if="isActive(tab.value)"
                            class="absolute right-4 top-4 h-2.5 w-2.5 rounded-full bg-neutral-950 dark:bg-white"
                        />

                        <span
                            :class="[
                                'flex h-11 w-11 items-center justify-center rounded-xl border transition-colors duration-300',
                                isActive(tab.value)
                                    ? 'border-neutral-300 bg-neutral-950 text-white dark:border-neutral-600 dark:bg-white dark:text-black'
                                    : 'border-neutral-200 bg-white text-neutral-500 group-hover:text-neutral-950 dark:border-neutral-800 dark:bg-[#111111] dark:text-neutral-400 dark:group-hover:text-white',
                            ]"
                        >
                            <component :is="tab.icon" class="h-5 w-5" />
                        </span>

                        <span class="min-w-0">
                            <span
                                class="block text-sm font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ tab.label }}
                            </span>

                            <span
                                class="mt-1.5 block text-sm leading-6 text-neutral-600 dark:text-neutral-500"
                            >
                                {{ tab.description }}
                            </span>
                        </span>
                    </button>
                </div>

                <div
                    v-else
                    class="grid grid-cols-1 gap-3 md:grid-cols-3"
                    aria-hidden="true"
                >
                    <div
                        v-for="index in 3"
                        :key="index"
                        class="min-h-120 rounded-2xl border border-neutral-200 bg-neutral-50 p-5 dark:border-neutral-800 dark:bg-black"
                    />
                </div>

                <div
                    class="rounded-2xl border border-neutral-200 bg-neutral-50 p-5 transition-colors duration-300 dark:border-neutral-800 dark:bg-black"
                >
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.22em] text-neutral-500 dark:text-neutral-600"
                            >
                                Saved Preference
                            </p>

                            <p
                                class="mt-2 text-sm font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ currentPreferenceLabel }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.22em] text-neutral-500 dark:text-neutral-600"
                            >
                                Resolved Theme
                            </p>

                            <p
                                class="mt-2 text-sm font-semibold text-neutral-950 dark:text-white"
                            >
                                {{ resolvedThemeLabel }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
