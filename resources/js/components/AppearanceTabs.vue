<script setup lang="ts">
import { Monitor, Moon, Sun } from '@lucide/vue';
import { onMounted, ref } from 'vue';
import type { Appearance } from '@/types';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();

const isMounted = ref(false);

onMounted(() => {
    isMounted.value = true;
});

const tabs = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
    { value: 'system', Icon: Monitor, label: 'System' },
] as const;

const isActive = (value: Appearance) => {
    return isMounted.value && appearance.value === value;
};
</script>

<template>
    <div
        class="inline-flex rounded-2xl border border-neutral-200 bg-neutral-100 p-1 dark:border-neutral-800 dark:bg-black"
    >
        <button
            v-for="{ value, Icon, label } in tabs"
            :key="value"
            type="button"
            @click="updateAppearance(value)"
            :aria-pressed="isActive(value)"
            :class="[
                'flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition-colors',
                isActive(value)
                    ? 'bg-white text-black shadow-sm dark:bg-[#111111] dark:text-white'
                    : 'text-neutral-500 hover:text-black dark:text-neutral-500 dark:hover:text-white',
            ]"
        >
            <component :is="Icon" class="h-4 w-4" />
            <span>{{ label }}</span>
        </button>
    </div>
</template>
