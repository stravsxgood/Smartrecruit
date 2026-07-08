<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import {
    Briefcase,
    CheckCircle2,
    XCircle,
    Activity,
    Search,
    Filter,
    TrendingUp,
    Calendar,
    Eye,
    ArrowUpRight,
    FileText,
    Clock,
    Star,
    Award,
} from 'lucide-vue-next';

interface Job {
    id: number;
    job_title: string;
    company: string;
    applied_date: string;
    status: string;
    ai_score?: number | null;
    cv_file_name?: string | null;
    cv_url?: string | null;
    cover_letter?: string | null;
    summary?: string | null;
}

interface Stats {
    total_applied: number;
    pending: number;
    reviewed: number;
    accepted: number;
    rejected: number;
}

const props = withDefaults(
    defineProps<{
        applications?: Job[];
        stats?: Stats;
    }>(),
    {
        applications: () => [],
        stats: () => ({
            total_applied: 0,
            pending: 0,
            reviewed: 0,
            accepted: 0,
            rejected: 0,
        }),
    },
);

const page = usePage();
const searchQuery = ref('');
const statusFilter = ref<string>('all');
const selectedApplication = ref<Job | null>(null);
const isModalOpen = ref(false);
const isNight = ref(true);
const hoveredStat = ref<string | null>(null);

const filteredApplications = computed(() => {
    return props.applications.filter((app) => {
        const matchesSearch =
            app.company
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            app.job_title.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesStatus =
            statusFilter.value === 'all' ||
            app.status.toLowerCase() === statusFilter.value.toLowerCase();
        return matchesSearch && matchesStatus;
    });
});

const statCards = computed(() => [
    {
        label: 'Total Applied',
        value: props.stats.total_applied,
        icon: Briefcase,
        color: 'from-blue-500 to-cyan-500',
        bgColor: 'bg-blue-500/10',
        borderColor: 'border-blue-500/20',
        textColor: 'text-blue-600',
        description: 'Total lamaran dikirim',
        trend: '+12%',
    },
    {
        label: 'In Progress',
        value: props.stats.pending + props.stats.reviewed,
        icon: Activity,
        color: 'from-amber-500 to-orange-500',
        bgColor: 'bg-amber-500/10',
        borderColor: 'border-amber-500/20',
        textColor: 'text-amber-600',
        description: 'Sedang diproses',
        trend: '+5%',
    },
    {
        label: 'Accepted',
        value: props.stats.accepted,
        icon: CheckCircle2,
        color: 'from-emerald-500 to-green-500',
        bgColor: 'bg-emerald-500/10',
        borderColor: 'border-emerald-500/20',
        textColor: 'text-emerald-600',
        description: 'Diterima',
        trend: '+2',
    },
    {
        label: 'Rejected',
        value: props.stats.rejected,
        icon: XCircle,
        color: 'from-red-500 to-rose-500',
        bgColor: 'bg-red-500/10',
        borderColor: 'border-red-500/20',
        textColor: 'text-red-600',
        description: 'Ditolak',
        trend: '-1',
    },
]);

const getStatusBadgeClass = (status: string): string => {
    const statusMap: Record<string, string> = {
        pending: 'bg-amber-500/10 text-amber-600 border-amber-500/20',
        reviewed: 'bg-blue-500/10 text-blue-600 border-blue-500/20',
        accepted: 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
        rejected: 'bg-red-500/10 text-red-600 border-red-500/20',
    };
    return (
        statusMap[status.toLowerCase()] ||
        'bg-gray-500/10 text-gray-600 border-gray-500/20'
    );
};

const getScoreColor = (score: number | null): string => {
    if (!score) return 'text-gray-400';
    if (score >= 80) return 'text-emerald-500';
    if (score >= 60) return 'text-amber-500';
    return 'text-red-500';
};

const openModal = (application: Job) => {
    selectedApplication.value = application;
    isModalOpen.value = true;
    document.body.style.overflow = 'hidden';
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => {
        selectedApplication.value = null;
        document.body.style.overflow = '';
    }, 300);
};

onMounted(() => {
    const handleEscape = (e: KeyboardEvent) => {
        if (e.key === 'Escape' && isModalOpen.value) closeModal();
    };
    window.addEventListener('keydown', handleEscape);
    return () => window.removeEventListener('keydown', handleEscape);
});
</script>

<template>
    <Head title="Dashboard" />

    <div
        class="min-h-screen bg-linear-to-br from-gray-50 via-white to-gray-100 p-4 sm:p-6 lg:p-8 dark:from-black dark:via-black dark:to-black"
    >
        <div class="mx-auto max-w-7xl space-y-8">
            <!-- Header dengan Animasi -->
            <div
                class="animate-fade-in flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <div class="mb-2 flex items-center gap-2">
                        <span
                            class="rounded-full bg-linear-to-r from-black to-gray-900 px-3 py-1 text-xs font-semibold text-white"
                        >
                            APPLICANT WORKSPACE
                        </span>
                        <span
                            class="flex h-2 w-2 animate-pulse rounded-full bg-emerald-500"
                        ></span>
                    </div>
                    <h1
                        class="bg-linear-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-4xl font-bold text-transparent sm:text-5xl dark:from-white dark:via-gray-200 dark:to-white"
                    >
                        Dashboard
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Track your applications, AI match score, and recruitment
                        progress
                    </p>
                </div>
            </div>

            <!-- Stats Cards dengan Animasi -->
            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-6"
            >
                <div
                    v-for="(card, index) in statCards"
                    :key="card.label"
                    @mouseenter="hoveredStat = card.label"
                    @mouseleave="hoveredStat = null"
                    class="group relative overflow-hidden rounded-3xl border transition-all duration-500 hover:scale-105 hover:shadow-2xl"
                    :class="[
                        card.borderColor,
                        hoveredStat === card.label ? 'shadow-2xl' : 'shadow-lg',
                        'bg-white/80 backdrop-blur-sm dark:bg-black',
                    ]"
                    :style="{ animationDelay: `${index * 100}ms` }"
                >
                    <div
                        class="absolute inset-0 bg-linear-to-br opacity-0 transition-opacity duration-500 group-hover:opacity-10"
                        :class="card.color"
                    ></div>

                    <div class="p-6">
                        <div class="mb-4 flex items-start justify-between">
                            <div class="rounded-2xl p-3" :class="card.bgColor">
                                <component
                                    :is="card.icon"
                                    class="h-6 w-6"
                                    :class="card.textColor"
                                />
                            </div>
                            <span
                                class="rounded-full bg-gray-900 px-2 py-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400"
                            >
                                {{ card.trend }}
                            </span>
                        </div>

                        <div class="space-y-1">
                            <p
                                class="origin-left text-4xl font-bold text-gray-900 transition-transform duration-300 group-hover:scale-110 dark:text-white"
                            >
                                {{ card.value }}
                            </p>
                            <p
                                class="text-sm font-semibold text-gray-700 dark:text-gray-300"
                            >
                                {{ card.label }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ card.description }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="absolute right-0 bottom-0 left-0 h-1 bg-linear-to-r opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                        :class="card.color"
                    ></div>
                </div>
            </div>

            <!-- Recent Applications Table -->
            <div
                class="overflow-hidden rounded-3xl border border-gray-200 bg-white/80 shadow-xl backdrop-blur-sm dark:border-gray-800 dark:bg-black"
            >
                <div
                    class="flex flex-col justify-between gap-4 border-b border-gray-200 p-6 sm:flex-row sm:items-center dark:border-gray-800"
                >
                    <div>
                        <h2
                            class="text-2xl font-bold text-gray-900 dark:text-white"
                        >
                            Recent Applications
                        </h2>
                        <p
                            class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                        >
                            Monitor your latest job application progress
                        </p>
                    </div>

                    <!-- Filter -->
                    <div class="flex items-center gap-2">
                        <select
                            v-model="statusFilter"
                            class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-black dark:border-gray-800 dark:bg-black"
                        >
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="reviewed">Reviewed</option>
                            <option value="accepted">Accepted</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white dark:bg-black">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Job Position
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Applied Date
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    AI Match Score
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold tracking-wider text-gray-500 uppercase dark:text-gray-400"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-200 dark:divide-gray-800"
                        >
                            <tr
                                v-for="app in filteredApplications"
                                :key="app.id"
                                class="group transition-all duration-300 hover:bg-gray-50/80 dark:hover:bg-gray-800/50"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <p
                                                class="font-semibold text-gray-900 dark:text-white"
                                            >
                                                {{ app.job_title }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        <span>
                                            {{
                                                app.applied_date ||
                                                '-'
                                            }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        v-if="app.ai_score"
                                        class="flex items-center gap-2"
                                    >
                                        <div class="relative">
                                            <svg
                                                class="h-10 w-10 -rotate-90 transform"
                                            >
                                                <circle
                                                    cx="20"
                                                    cy="20"
                                                    r="16"
                                                    stroke="currentColor"
                                                    stroke-width="3"
                                                    fill="none"
                                                    class="text-gray-200 dark:text-gray-700"
                                                />
                                                <circle
                                                    cx="20"
                                                    cy="20"
                                                    r="16"
                                                    stroke="currentColor"
                                                    stroke-width="3"
                                                    fill="none"
                                                    :class="
                                                        getScoreColor(
                                                            app.ai_score,
                                                        )
                                                    "
                                                    :stroke-dasharray="100"
                                                    :stroke-dashoffset="
                                                        100 -
                                                        (app.ai_score || 0)
                                                    "
                                                    class="transition-all duration-1000"
                                                />
                                            </svg>
                                            <span
                                                class="absolute inset-0 flex items-center justify-center text-xs font-bold"
                                                :class="
                                                    getScoreColor(app.ai_score)
                                                "
                                            >
                                                {{ app.ai_score }}%
                                            </span>
                                        </div>
                                    </div>
                                    <span v-else class="text-sm text-gray-400"
                                        >N/A</span
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold"
                                        :class="getStatusBadgeClass(app.status)"
                                    >
                                        {{ app.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        @click="openModal(app)"
                                        class="inline-flex items-center gap-2 rounded-xl bg-linear-to-r from-gray-950 to-black px-4 py-2 text-sm font-semibold text-white transition-all duration-300 hover:scale-105 hover:shadow-xl"
                                    >
                                        Lihat Detail
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredApplications.length === 0">
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center"
                                    >
                                        <FileText
                                            class="mb-4 h-16 w-16 text-gray-300 dark:text-gray-700"
                                        />
                                        <p
                                            class="text-gray-500 dark:text-gray-400"
                                        >
                                            No applications found
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Enhanced Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div
                    v-if="isModalOpen && selectedApplication"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
                >
                    <div
                        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                        @click="closeModal"
                    ></div>

                    <div
                        class="animate-modal-enter relative max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-3xl border border-gray-200 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900"
                    >
                        <!-- Modal Header -->
                        <div
                            class="sticky top-0 z-10 bg-linear-to-r from-gray-950 to-black p-6 text-white sm:p-8"
                        >
                            <button
                                @click="closeModal"
                                class="absolute top-4 right-4 rounded-xl bg-white/10 p-2 transition-colors hover:bg-white/20"
                            >
                                <XCircle class="h-6 w-6" />
                            </button>

                            <div class="mb-4 flex flex-wrap items-center gap-3">
                                <span
                                    class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur-sm"
                                >
                                    {{ selectedApplication.status }}
                                </span>
                                <span
                                    v-if="selectedApplication.ai_score"
                                    class="flex items-center gap-1 rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur-sm"
                                >
                                    <Star class="h-3 w-3" />
                                    AI Match:
                                    {{ selectedApplication.ai_score }}%
                                </span>
                            </div>

                            <h2 class="mb-2 text-3xl font-bold sm:text-4xl">
                                {{ selectedApplication.company }}
                            </h2>
                            <p class="flex items-center gap-2 text-white/80">
                                <Briefcase class="h-4 w-4" />
                                {{ selectedApplication.job_title }}
                            </p>
                        </div>

                        <!-- Modal Content -->
                        <div class="space-y-6 p-6 sm:p-8">
                            <!-- Application Summary -->
                            <div
                                class="rounded-2xl border border-gray-200 bg-gray-50/50 p-6 dark:border-gray-800 dark:bg-gray-800/50"
                            >
                                <h3
                                    class="mb-4 flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white"
                                >
                                    <Award class="h-5 w-5 text-orange-600" />
                                    Application Summary
                                </h3>
                                <p
                                    class="leading-relaxed text-gray-700 dark:text-gray-300"
                                >
                                    CV memiliki potensi yang baik dengan
                                    pengalaman magang yang relevan, namun
                                    struktur dokumen saat ini sangat berantakan
                                    dan tidak mengikuti standar ATS yang baik.
                                    Informasi kontak dan profil sudah ada,
                                    tetapi perlu penataan ulang agar lebih
                                    profesional dan mudah dibaca oleh sistem
                                    rekrutmen.
                                </p>
                            </div>

                            <!-- Recruitment Progress -->
                            <div
                                class="rounded-2xl border border-gray-200 p-6 dark:border-gray-800"
                            >
                                <h3
                                    class="mb-6 flex items-center gap-2 text-lg font-bold text-gray-900 dark:text-white"
                                >
                                    <Clock class="h-5 w-5 text-white" />
                                    Recruitment Progress
                                </h3>

                                <div class="space-y-4">
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-linear-to-r from-black to-gray-950 text-white shadow-lg"
                                        >
                                            <FileText class="h-5 w-5" />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="font-semibold text-gray-900 dark:text-white"
                                            >
                                                CV Submitted
                                            </p>
                                            <p
                                                class="text-sm text-gray-600 dark:text-gray-400"
                                            >
                                                Your CV has been attached to
                                                this application record.
                                            </p>
                                            <p
                                                class="mt-1 text-xs text-gray-500"
                                            >
                                                {{
                                                    selectedApplication.applied_date
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-4">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-xl border-2 border-gray-400 text-white"
                                        >
                                            <Activity class="h-5 w-5" />
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="font-semibold text-gray-900 dark:text-white"
                                            >
                                                Current Status:
                                                {{ selectedApplication.status }}
                                            </p>
                                            <p
                                                class="text-sm text-gray-600 dark:text-gray-400"
                                            >
                                                Your application is being
                                                reviewed by our recruitment
                                                team.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CV Preview -->
                            <div
                                class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800"
                            >
                                <div
                                    class="flex items-center justify-between border-b border-gray-200 bg-gray-50/50 p-4 dark:border-gray-800 dark:bg-gray-800/50"
                                >
                                    <div>
                                        <h3
                                            class="font-bold text-gray-900 dark:text-white"
                                        >
                                            CV Preview
                                        </h3>
                                        <p
                                            class="text-xs text-gray-600 dark:text-gray-400"
                                        >
                                            {{
                                                selectedApplication.cv_file_name ||
                                                'CV-' +
                                                    selectedApplication.company +
                                                    '.pdf'
                                            }}
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <a
                                            v-if="selectedApplication.cv_url"
                                            :href="selectedApplication.cv_url"
                                            target="_blank"
                                            class="rounded-lg p-2 transition-colors hover:bg-gray-200 dark:hover:bg-gray-700"
                                            title="Buka di tab baru"
                                        >
                                            <svg
                                                class="h-5 w-5 text-gray-600 dark:text-gray-400"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                                />
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                <!-- ✅ PREVIEW CV SEBENARNYA -->
                                <div
                                    class="bg-gray-100 dark:bg-gray-950"
                                    style="height: 500px"
                                >
                                    <iframe
                                        v-if="selectedApplication.cv_url"
                                        :src="selectedApplication.cv_url"
                                        class="h-full w-full border-0"
                                        title="CV Preview"
                                    ></iframe>

                                    <!-- Placeholder jika tidak ada CV -->
                                    <div
                                        v-else
                                        class="flex h-full items-center justify-center p-8"
                                    >
                                        <div class="text-center">
                                            <FileText
                                                class="mx-auto mb-4 h-16 w-16 text-gray-400"
                                            />
                                            <p
                                                class="font-semibold text-gray-600 dark:text-gray-400"
                                            >
                                                CV tidak tersedia
                                            </p>
                                            <p
                                                class="mt-2 text-sm text-gray-500"
                                            >
                                                Kandidat belum mengupload CV
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes modal-enter {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}

.animate-modal-enter {
    animation: modal-enter 0.3s ease-out;
}

.modal-enter-active,
.modal-leave-active {
    transition: all 0.3s ease-out;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from > div:last-child,
.modal-leave-to > div:last-child {
    transform: scale(0.95) translateY(20px);
}
</style>
