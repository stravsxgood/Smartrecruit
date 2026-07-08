<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    mode?: 'job-detail' | 'cv-analysis';
    fileName?: string;
    analysis?: {
        overall_score: number;
        summary: string;
        strengths: string[];
        weaknesses: string[];
        improvements: string[];
    };
    jobPosition?: {
        id: number;
        title: string;
        description: string;
        status: string;
        requirements: string[];
        benefits: string[];
        creator: {
            name: string;
        };
        created_at: string;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Job Positions',
                href: '/job-positions',
            },
            {
                title: 'Details',
                href: '#',
            },
        ],
    },
});

const isCvAnalysisMode = computed(() => {
    return props.mode === 'cv-analysis';
});

const pageTitle = computed(() => {
    if (isCvAnalysisMode.value) {
        return 'Hasil Analisis CV';
    }

    return props.jobPosition?.title ?? 'Job Position Details';
});

const scoreLabel = computed(() => {
    const score = props.analysis?.overall_score ?? 0;

    if (score >= 85) return 'Sangat Baik';
    if (score >= 70) return 'Cukup Baik';
    if (score >= 55) return 'Perlu Diperbaiki';

    return 'Kurang Siap';
});

const scoreBadgeClass = computed(() => {
    const score = props.analysis?.overall_score ?? 0;

    if (score >= 85) {
        return 'bg-green-500/10 text-green-600 dark:text-green-400';
    }

    if (score >= 70) {
        return 'bg-blue-500/10 text-blue-600 dark:text-blue-400';
    }

    if (score >= 55) {
        return 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400';
    }

    return 'bg-red-500/10 text-red-600 dark:text-red-400';
});

const progressBarStyle = computed(() => ({
    width: `${props.analysis?.overall_score ?? 0}%`,
}));
</script>

<template>
    <Head :title="pageTitle" />

    <!-- ============================================================ -->
    <!-- CV ANALYSIS RESULT MODE                                      -->
    <!-- ============================================================ -->
    <div
        v-if="isCvAnalysisMode"
        class="min-h-screen bg-white px-6 py-8 text-neutral-950 transition-colors duration-300 dark:bg-black dark:text-white md:px-10"
    >
        <div class="mx-auto flex w-full max-w-5xl flex-col gap-8">
            <header class="flex flex-col gap-5">
                <Link
                    href="/job-positions"
                    class="inline-flex w-fit items-center justify-center rounded-xl border border-neutral-200 bg-neutral-100 px-4 py-2 text-sm font-semibold text-neutral-700 transition-colors hover:border-neutral-300 hover:bg-neutral-200 hover:text-neutral-950 dark:border-neutral-800 dark:bg-[#111111] dark:text-neutral-400 dark:hover:border-neutral-700 dark:hover:bg-neutral-900 dark:hover:text-white"
                >
                    &larr; Kembali
                </Link>

                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-[0.22em] text-neutral-500"
                    >
                        CV Analysis Result
                    </p>

                    <h1
                        class="mt-3 text-4xl font-semibold tracking-tight text-neutral-950 dark:text-white md:text-6xl"
                    >
                        Hasil koreksi CV
                    </h1>

                    <p
                        class="mt-4 max-w-2xl text-sm leading-7 text-neutral-600 dark:text-neutral-500"
                    >
                        AI mengevaluasi CV kamu berdasarkan struktur,
                        kelengkapan informasi, kejelasan pengalaman, skill yang
                        ditampilkan, dan potensi keterbacaan oleh recruiter.
                    </p>
                </div>
            </header>

            <section
                class="rounded-3xl border border-neutral-200 bg-white p-6 text-neutral-950 shadow-sm transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:text-white dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)] md:p-8"
            >
                <div
                    class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between"
                >
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-neutral-500">
                            File yang dianalisis
                        </p>

                        <h2
                            class="mt-2 truncate text-2xl font-semibold tracking-tight text-neutral-950 dark:text-white"
                        >
                            {{ fileName ?? 'CV_Kandidat.pdf' }}
                        </h2>
                    </div>

                    <div
                        class="shrink-0 rounded-3xl border border-neutral-200 bg-neutral-950 px-6 py-5 text-white shadow-sm dark:border-neutral-800 dark:bg-black"
                    >
                        <p class="text-sm text-neutral-400">Overall Score</p>

                        <div class="mt-2 flex items-end gap-1">
                            <span class="text-5xl font-semibold leading-none">
                                {{ analysis?.overall_score ?? 0 }}
                            </span>

                            <span class="pb-1 text-neutral-400">/100</span>
                        </div>

                        <p
                            :class="[
                                'mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                scoreBadgeClass,
                            ]"
                        >
                            {{ scoreLabel }}
                        </p>
                    </div>
                </div>

                <div class="mt-8">
                    <div class="mb-3 flex items-center justify-between">
                        <p
                            class="text-sm font-semibold uppercase tracking-wider text-neutral-500"
                        >
                            CV Quality Score
                        </p>

                        <p
                            class="text-sm font-bold text-neutral-950 dark:text-white"
                        >
                            {{ analysis?.overall_score ?? 0 }}%
                        </p>
                    </div>

                    <div
                        class="h-3 w-full overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-800"
                    >
                        <div
                            class="h-full rounded-full bg-green-500 transition-all duration-500"
                            :style="progressBarStyle"
                        />
                    </div>
                </div>

                <p
                    class="mt-8 leading-7 text-neutral-600 dark:text-neutral-400"
                >
                    {{
                        analysis?.summary ??
                        'Hasil ringkasan analisis belum tersedia.'
                    }}
                </p>
            </section>

            <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div
                    class="rounded-3xl border border-neutral-200 bg-white p-6 text-neutral-950 shadow-sm transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:text-white dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)]"
                >
                    <div class="mb-5 flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-2xl border border-green-500/20 bg-green-500/10 text-green-600 dark:text-green-400"
                        >
                            ✓
                        </div>

                        <h2
                            class="text-xl font-semibold tracking-tight text-neutral-950 dark:text-white"
                        >
                            Kelebihan
                        </h2>
                    </div>

                    <ul
                        v-if="analysis?.strengths?.length"
                        class="space-y-4"
                    >
                        <li
                            v-for="(item, index) in analysis.strengths"
                            :key="index"
                            class="flex gap-3 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                        >
                            <span class="mt-1 text-green-500">●</span>
                            <span>{{ item }}</span>
                        </li>
                    </ul>

                    <p
                        v-else
                        class="text-sm leading-6 text-neutral-500"
                    >
                        Belum ada data kelebihan yang tersedia.
                    </p>
                </div>

                <div
                    class="rounded-3xl border border-neutral-200 bg-white p-6 text-neutral-950 shadow-sm transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:text-white dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)]"
                >
                    <div class="mb-5 flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-2xl border border-yellow-500/20 bg-yellow-500/10 text-yellow-600 dark:text-yellow-400"
                        >
                            !
                        </div>

                        <h2
                            class="text-xl font-semibold tracking-tight text-neutral-950 dark:text-white"
                        >
                            Kekurangan
                        </h2>
                    </div>

                    <ul
                        v-if="analysis?.weaknesses?.length"
                        class="space-y-4"
                    >
                        <li
                            v-for="(item, index) in analysis.weaknesses"
                            :key="index"
                            class="flex gap-3 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                        >
                            <span class="mt-1 text-yellow-500">●</span>
                            <span>{{ item }}</span>
                        </li>
                    </ul>

                    <p
                        v-else
                        class="text-sm leading-6 text-neutral-500"
                    >
                        Belum ada data kekurangan yang tersedia.
                    </p>
                </div>

                <div
                    class="rounded-3xl border border-neutral-200 bg-white p-6 text-neutral-950 shadow-sm transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:text-white dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)]"
                >
                    <div class="mb-5 flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-2xl border border-blue-500/20 bg-blue-500/10 text-blue-600 dark:text-blue-400"
                        >
                            ↗
                        </div>

                        <h2
                            class="text-xl font-semibold tracking-tight text-neutral-950 dark:text-white"
                        >
                            Perbaikan
                        </h2>
                    </div>

                    <ul
                        v-if="analysis?.improvements?.length"
                        class="space-y-4"
                    >
                        <li
                            v-for="(item, index) in analysis.improvements"
                            :key="index"
                            class="flex gap-3 text-sm leading-6 text-neutral-600 dark:text-neutral-400"
                        >
                            <span class="mt-1 text-blue-500">●</span>
                            <span>{{ item }}</span>
                        </li>
                    </ul>

                    <p
                        v-else
                        class="text-sm leading-6 text-neutral-500"
                    >
                        Belum ada data saran perbaikan yang tersedia.
                    </p>
                </div>
            </section>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- JOB POSITION DETAIL MODE                                     -->
    <!-- ============================================================ -->
    <div
        v-else-if="jobPosition"
        class="min-h-screen bg-white px-6 py-8 text-neutral-950 transition-colors duration-300 dark:bg-black dark:text-white md:px-10"
    >
        <div class="mx-auto w-full max-w-3xl">
            <div class="mb-12">
                <div class="mb-4 flex flex-wrap items-center gap-4">
                    <Link
                        href="/job-positions"
                        class="text-sm font-medium text-neutral-500 transition-colors hover:text-neutral-950 dark:hover:text-white"
                    >
                        &larr; Back to jobs
                    </Link>

                    <span
                        class="rounded-full border border-neutral-200 bg-neutral-100 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-neutral-600 dark:border-neutral-800 dark:bg-[#111111] dark:text-neutral-400"
                    >
                        {{ jobPosition.status }}
                    </span>
                </div>

                <h1
                    class="mb-6 text-4xl font-semibold leading-tight tracking-tight text-neutral-950 dark:text-white md:text-6xl"
                >
                    {{ jobPosition.title }}
                </h1>

                <div
                    class="mb-8 flex flex-wrap items-center gap-6 border-y border-neutral-200 py-4 text-sm text-neutral-600 dark:border-neutral-800 dark:text-neutral-500"
                >
                    <div class="flex items-center gap-2">
                        <span>Posted by:</span>

                        <span
                            class="font-medium text-neutral-950 dark:text-white"
                        >
                            {{ jobPosition.creator.name }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span>Posted on:</span>

                        <span
                            class="font-medium text-neutral-950 dark:text-white"
                        >
                            {{
                                new Date(
                                    jobPosition.created_at,
                                ).toLocaleDateString()
                            }}
                        </span>
                    </div>
                </div>
            </div>

            <div
                class="space-y-12 rounded-3xl border border-neutral-200 bg-white p-8 shadow-sm transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] md:p-12"
            >
                <section>
                    <h2
                        class="mb-4 text-xl font-semibold tracking-tight text-neutral-950 dark:text-white"
                    >
                        Role Description
                    </h2>

                    <p
                        class="whitespace-pre-wrap text-base leading-7 text-neutral-600 dark:text-neutral-400"
                    >
                        {{ jobPosition.description }}
                    </p>
                </section>

                <section v-if="jobPosition.requirements?.length">
                    <h2
                        class="mb-4 text-xl font-semibold tracking-tight text-neutral-950 dark:text-white"
                    >
                        Requirements
                    </h2>

                    <ul class="space-y-3">
                        <li
                            v-for="(req, index) in jobPosition.requirements"
                            :key="index"
                            class="flex items-start gap-3"
                        >
                            <span class="mt-1 text-green-500">✓</span>

                            <span
                                class="text-base leading-7 text-neutral-600 dark:text-neutral-400"
                            >
                                {{ req }}
                            </span>
                        </li>
                    </ul>
                </section>

                <section v-if="jobPosition.benefits?.length">
                    <h2
                        class="mb-4 text-xl font-semibold tracking-tight text-neutral-950 dark:text-white"
                    >
                        Benefits
                    </h2>

                    <ul class="space-y-3">
                        <li
                            v-for="(benefit, index) in jobPosition.benefits"
                            :key="index"
                            class="flex items-start gap-3"
                        >
                            <span class="mt-1 text-blue-500">✓</span>

                            <span
                                class="text-base leading-7 text-neutral-600 dark:text-neutral-400"
                            >
                                {{ benefit }}
                            </span>
                        </li>
                    </ul>
                </section>

                <div
                    class="flex justify-end border-t border-neutral-200 pt-8 dark:border-neutral-800"
                >
                    <button
                        type="button"
                        class="inline-flex h-11 items-center justify-center rounded-xl border border-neutral-950 bg-neutral-950 px-6 text-sm font-semibold text-white transition-colors hover:bg-neutral-800 dark:border-white dark:bg-white dark:text-black dark:hover:bg-neutral-200"
                    >
                        Apply for this role
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- FALLBACK -->
    <div
        v-else
        class="flex min-h-screen items-center justify-center bg-white px-6 text-neutral-950 transition-colors duration-300 dark:bg-black dark:text-white"
    >
        <div class="max-w-md text-center">
            <h1
                class="text-2xl font-semibold text-neutral-950 dark:text-white"
            >
                Data tidak ditemukan
            </h1>

            <p class="mt-3 text-sm leading-6 text-neutral-600 dark:text-neutral-500">
                Halaman ini membutuhkan data job position atau hasil analisis CV.
            </p>

            <Link
                href="/job-positions"
                class="mt-6 inline-flex h-11 items-center justify-center rounded-xl border border-neutral-950 bg-neutral-950 px-5 text-sm font-semibold text-white transition-colors hover:bg-neutral-800 dark:border-white dark:bg-white dark:text-black dark:hover:bg-neutral-200"
            >
                Kembali
            </Link>
        </div>
    </div>
</template>
