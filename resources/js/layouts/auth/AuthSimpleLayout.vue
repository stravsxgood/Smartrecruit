<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center bg-obsidian p-6 md:p-10"
    >
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <!-- Brand & Header -->
                <div class="flex flex-col items-center gap-5">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-3"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-carbon"
                        >
                            <AppLogoIcon
                                class="size-5 fill-current text-paper-white"
                            />
                        </div>
                        <span
                            class="font-[var(--font-lyon-display)] text-[22px] font-light tracking-[-0.3px] text-bone"
                            >SmartRecruit</span
                        >
                    </Link>
                    <div v-if="title || description" class="space-y-2 text-center">
                        <h1
                            class="font-[var(--font-lyon-display)] text-[24px] font-light leading-[1.2] tracking-[-0.3px] text-bone"
                        >
                            {{ title }}
                        </h1>
                        <p
                            v-if="description"
                            class="text-[14px] leading-[1.5] text-frost"
                        >
                            {{ description }}
                        </p>
                    </div>
                </div>

                <!-- Dark Card -->
                <div
                    class="rounded-2xl bg-carbon p-6 shadow-[rgba(0,0,0,0.2)_0px_18px_20px_0px] sm:p-8"
                >
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
