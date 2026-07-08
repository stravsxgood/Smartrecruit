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
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { dashboard, login, register } from '@/routes';

// ==========================================
// NAVBAR STATE
// ==========================================
const isNight = ref(true);
const scrolled = ref(false);
const mobileOpen = ref(false);

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

// ==========================================
// INTERFACES
// ==========================================
interface Job {
    id: number;
    title: string;
    description: string;
    company?: string;
    location?: string;
    type?: string;
    skills?: string[];
    image_url?: string;
}

// ✅ TAMBAHKAN: Interface untuk dokumen CV user
interface UserDocument {
    id: number;
    name: string;
    path: string;
    upload_date: string;
    file_size?: string;
}

// ==========================================
// PROPS
// ==========================================
const props = withDefaults(
    defineProps<{
        jobs?: Job[];
        userDocuments?: UserDocument[]; // ✅ TAMBAHKAN: Daftar CV user
    }>(),
    {
        jobs: () => [],
        userDocuments: () => [], // ✅ Default array kosong
    },
);

const page = usePage();
const user = computed(() => (page.props as any).auth?.user);
const flash = computed(() => (page.props as any).flash);

// ==========================================
// MODAL APPLY STATE
// ==========================================
const showApplyModal = ref(false);
const selectedJob = ref<Job | null>(null);
const isSubmitting = ref(false);

// ✅ PERBAIKAN: Ganti cv_path dengan document_id
const applyForm = ref({
    phone: '',
    cover_letter: '',
    document_id: null as number | null, // ✅ ID dokumen yang dipilih
});

function openApplyModal(job: Job) {
    if (!user.value) {
        router.get(login());
        return;
    }
    selectedJob.value = job;
    showApplyModal.value = true;
    // ✅ Reset form dengan document_id null
    applyForm.value = { phone: '', cover_letter: '', document_id: null };

    // ✅ AUTO SELECT: Jika hanya ada 1 CV, otomatis pilih
    if (props.userDocuments && props.userDocuments.length === 1) {
        applyForm.value.document_id = props.userDocuments[0].id;
    }
}

function closeApplyModal() {
    showApplyModal.value = false;
    selectedJob.value = null;
}

function submitApplication() {
    if (!selectedJob.value) return;

    // ✅ VALIDASI: Pastikan CV sudah dipilih jika ada dokumen tersedia
    if (props.userDocuments && props.userDocuments.length > 0 && !applyForm.value.document_id) {
        alert('Silakan pilih CV yang akan digunakan untuk melamar');
        return;
    }

    isSubmitting.value = true;

    // ✅ PERBAIKAN: Kirim sebagai object biasa (bukan FormData)
    router.post(`/jobapply/${selectedJob.value.id}/apply`, {
        phone: applyForm.value.phone,
        cover_letter: applyForm.value.cover_letter,
        document_id: applyForm.value.document_id, // ✅ Kirim ID dokumen
    }, {
        preserveScroll: true,
        onSuccess: () => {
            closeApplyModal();
        },
        onError: (errors) => {
            console.error('Error:', errors);
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
}

// Auto-close success message setelah 5 detik
watch(
    () => flash.value?.success,
    (val) => {
        if (val) {
            setTimeout(() => {
                router.reload({ only: ['flash'] });
            }, 5000);
        }
    },
);
</script>

<template>
    <Head title="Open Positions" />
    <div class="min-h-screen relative overflow-hidden bg-[#050505]">
        <!-- Ambient Glow -->
        <div class="pointer-events-none absolute inset-0 flex justify-center overflow-hidden z-0">
            <div class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] rounded-full bg-gray-500/10 blur-[120px]"></div>
            <div class="absolute top-[20%] right-[-10%] w-[40%] h-[60%] rounded-full bg-blue-500/10 blur-[120px]"></div>
            <div class="absolute bottom-[-20%] left-[20%] w-[60%] h-[50%] rounded-full bg-gray-600/10 blur-[120px]"></div>
        </div>
        <!-- ============================================================ -->
        <!-- FLASH MESSAGE (Success / Error) -->
        <!-- ============================================================ -->
        <Transition name="toast">
            <div
                v-if="flash?.success"
                class="fixed top-20 right-6 z-50 max-w-md rounded-2xl border-l-4 border-emerald-500 px-5 py-4 shadow-2xl backdrop-blur-md"
                :class="
                    isNight
                        ? 'bg-carbon/90 text-bone'
                        : 'bg-paper-white text-carbon'
                "
            >
                <div class="flex items-start gap-3">
                    <svg
                        class="h-6 w-6 shrink-0 text-emerald-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <div>
                        <p class="font-semibold">Berhasil!</p>
                        <p class="text-sm opacity-80">{{ flash.success }}</p>
                    </div>
                </div>
            </div>
        </Transition>

        <Transition name="toast">
            <div
                v-if="flash?.error"
                class="fixed top-20 right-6 z-50 max-w-md rounded-2xl border-l-4 border-red-500 px-5 py-4 shadow-2xl backdrop-blur-md"
                :class="
                    isNight
                        ? 'bg-carbon/90 text-bone'
                        : 'bg-paper-white text-carbon'
                "
            >
                <div class="flex items-start gap-3">
                    <svg
                        class="h-6 w-6 shrink-0 text-red-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <div>
                        <p class="font-semibold">Oops!</p>
                        <p class="text-sm opacity-80">{{ flash.error }}</p>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ============================================================ -->
        <!-- NAVBAR -->
        <!-- ============================================================ -->
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
            <nav
                class="mx-auto flex max-w-[1200px] items-center justify-between px-6 py-4"
            >
                <div class="flex shrink-0 items-center gap-2.5">
                    <div
                        class="flex h-7 w-7 items-center justify-center rounded-lg transition-colors duration-700"
                        :class="isNight ? 'bg-bone/8' : 'bg-obsidian/10'"
                    >
                        <AppLogoIcon
                            class="size-3.5 fill-current transition-colors duration-700"
                            :class="
                                isNight ? 'text-paper-white' : 'text-obsidian'
                            "
                        />
                    </div>
                    <span
                        class="text-body-sm font-medium tracking-tight transition-colors duration-700"
                        :class="isNight ? 'text-bone' : 'text-obsidian'"
                        >SmartRecruit</span
                    >
                </div>
                <div class="hidden items-center gap-1 lg:flex">
                    <Link
                        v-for="link in navLinks"
                        :key="link.label"
                        :href="link.href"
                        class="flex items-center gap-1 rounded-lg px-3 py-2 text-[11px] font-normal tracking-[0.182em] uppercase transition-colors duration-300"
                        :class="
                            isNight
                                ? 'text-frost hover:text-paper-white'
                                : 'text-slate hover:text-obsidian'
                        "
                        >{{ link.label }}</Link
                    >
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <button
                        @click="toggleSky"
                        class="group relative flex h-[36px] w-[72px] items-center rounded-full border transition-all duration-300 hover:scale-[1.05]"
                        :class="
                            isNight
                                ? 'border-graphite/30 bg-carbon/80'
                                : 'border-obsidian/20 bg-obsidian/5'
                        "
                    >
                        <span
                            class="absolute top-[3px] flex h-[28px] w-[28px] items-center justify-center rounded-full shadow-md transition-all duration-300 ease-out"
                            :class="
                                isNight
                                    ? 'left-[39px] bg-graphite'
                                    : 'left-[3px] bg-obsidian'
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
                                        : 'text-bone opacity-100'
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
                        :class="isNight ? 'text-frost' : 'text-slate'"
                        >{{ isNight ? 'Dark' : 'Light' }}</span
                    >

                    <template v-if="user">
                        <Link
                            :href="dashboard()"
                            class="inline-flex h-9 items-center gap-1.5 rounded-full px-4 text-[11px] font-normal tracking-[0.182em] uppercase shadow-lg transition-all duration-300"
                            :class="
                                isNight
                                    ? 'bg-paper-white text-carbon hover:bg-bone'
                                    : 'bg-obsidian text-paper-white hover:bg-obsidian/90'
                            "
                            >DASHBOARD &rarr;</Link
                        >
                    </template>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="hidden px-3 py-2 text-[11px] font-normal tracking-[0.182em] uppercase transition-colors duration-300 sm:block"
                            :class="
                                isNight
                                    ? 'text-paper-white hover:text-frost'
                                    : 'text-obsidian hover:text-obsidian/70'
                            "
                            >LOG IN</Link
                        >
                        <Link
                            :href="register()"
                            class="inline-flex h-9 items-center rounded-full px-4 text-[11px] font-normal tracking-[0.182em] uppercase shadow-lg transition-all duration-300"
                            :class="
                                isNight
                                    ? 'bg-paper-white text-carbon hover:bg-bone'
                                    : 'bg-obsidian text-paper-white hover:bg-obsidian/90'
                            "
                            >SIGN UP &rarr;</Link
                        >
                    </template>
                </div>
            </nav>
        </header>

        <!-- ============================================================ -->
        <!-- PAGE HEADER -->
        <!-- ============================================================ -->
        <div class="relative z-10 mx-auto max-w-7xl px-6 pt-24 pb-12 text-center">
            <div class="group inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1.5 mb-6 shadow-sm backdrop-blur-md transition-colors duration-300 hover:bg-white/10 hover:border-gray-500/50 cursor-pointer">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-400 opacity-75 group-hover:bg-gray-300 transition-colors duration-300"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-500 group-hover:bg-gray-400 transition-colors duration-300"></span>
                </span>
                <span class="text-xs font-bold tracking-widest text-gray-400 uppercase transition-colors duration-300 group-hover:text-gray-300">We Are Hiring</span>
            </div>
            <h1 class="text-5xl font-extrabold tracking-tight md:text-7xl mb-6 text-white drop-shadow-lg">
                Discover Your <br/>
                <span class="inline-block bg-linear-to-r from-gray-300 via-gray-400 to-gray-500 bg-clip-text text-transparent transition-all duration-300 hover:from-gray-100 hover:via-gray-300 hover:to-gray-400 cursor-default hover:scale-[1.02]">Next Career</span>
            </h1>
            <p class="mx-auto max-w-2xl text-lg text-neutral-400 md:text-xl leading-relaxed">
                Join a team of visionaries and builders. Explore opportunities to craft the future with us, one pixel at a time.
            </p>
        </div>

        <!-- ============================================================ -->
        <!-- JOB CARDS -->
        <!-- ============================================================ -->
        <div class="relative z-10 mx-auto grid max-w-7xl grid-cols-1 gap-8 p-6 pb-24 md:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="job in jobs"
                :key="job.id"
                @click="openApplyModal(job)"
                class="group relative flex h-[460px] cursor-pointer flex-col overflow-hidden rounded-3xl border border-white/10 bg-white/5 backdrop-blur-md transition-all duration-500 ease-out hover:-translate-y-3 hover:border-gray-500/50 hover:bg-white/10 hover:shadow-2xl hover:shadow-gray-500/20"
            >
                <!-- Image Section -->
                <div class="relative h-56 w-full overflow-hidden">
                    <img
                        :src="job.image_url"
                        :alt="job.title"
                        class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-110 opacity-80 group-hover:opacity-100"
                    />
                    <div class="absolute inset-0 bg-linear-to-t from-[#0a0a0a] via-black/40 to-transparent"></div>

                    <!-- Top Badges -->
                    <div class="absolute top-4 left-4 right-4 flex justify-between">
                        <span class="rounded-full border border-white/20 bg-black/50 px-3 py-1.5 text-[10px] font-bold tracking-widest text-white uppercase backdrop-blur-md shadow-lg">
                            {{ job.type || 'Full-time' }}
                        </span>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full border border-white/20 bg-black/50 text-white backdrop-blur-md shadow-lg transition-transform duration-500 group-hover:rotate-45 group-hover:bg-gray-500 group-hover:border-gray-400 group-hover:text-black">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>
                    </div>

                    <!-- Company Info -->
                    <div class="absolute bottom-4 left-4 right-4 flex flex-col">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider drop-shadow-md">{{ job.company || 'SmartRecruit Corp' }}</span>
                        <div class="flex items-center gap-1 mt-1 text-xs font-medium text-neutral-300 drop-shadow-md">
                            <svg class="h-3.5 w-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ job.location || 'Remote' }}
                        </div>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="flex flex-1 flex-col justify-between p-6">
                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-white transition-colors duration-300 group-hover:text-gray-400">
                            {{ job.title }}
                        </h2>
                        <p class="mt-3 line-clamp-2 text-sm leading-relaxed text-neutral-400 group-hover:text-neutral-300 transition-colors">
                            {{ job.description }}
                        </p>
                    </div>

                    <!-- Skills -->
                    <div class="mt-6 flex flex-wrap gap-2">
                        <span
                            v-for="(skill, idx) in job.skills"
                            :key="idx"
                            class="inline-flex rounded-lg bg-white/5 border border-white/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-widest text-neutral-300 transition-colors group-hover:border-gray-500/30 group-hover:text-gray-100"
                        >
                            {{ skill }}
                        </span>
                    </div>
                </div>

                <!-- Glowing Bottom Border Effect -->
                <div class="absolute bottom-0 left-0 h-1 w-full scale-x-0 bg-linear-to-r from-gray-400 to-gray-600 transition-transform duration-500 group-hover:scale-x-100 origin-left"></div>
            </div>
        </div>

        <div
            v-if="jobs.length === 0"
            class="relative z-10 mx-auto max-w-7xl px-6 pb-20 text-center"
        >
            <div class="rounded-3xl border border-white/10 bg-white/5 p-16 backdrop-blur-md">
                <svg class="mx-auto h-16 w-16 text-neutral-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="text-xl font-bold text-white">Belum Ada Lowongan</h3>
                <p class="mt-2 text-sm text-neutral-400">Lowongan baru akan segera hadir. Pantau terus halaman ini.</p>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODAL APPLY -->
        <!-- ============================================================ -->
        <Transition name="modal">
            <div
                v-if="showApplyModal && selectedJob"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
            >
                <!-- Backdrop -->
                <div
                    class="absolute inset-0 bg-black/80 backdrop-blur-xl transition-opacity"
                    @click="closeApplyModal"
                ></div>

                <!-- Modal Content -->
                <div class="relative w-full max-w-5xl overflow-hidden rounded-4xl border border-white/10 bg-[#0a0a0a] shadow-2xl transition-all flex flex-col md:flex-row shadow-gray-500/10">

                    <!-- Close Button (Mobile) -->
                    <button
                        @click="closeApplyModal"
                        class="absolute top-4 right-4 z-20 md:hidden flex h-10 w-10 items-center justify-center rounded-full bg-black/50 text-white backdrop-blur-md border border-white/10"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>

                    <!-- Left Column: Job Info -->
                    <div class="relative w-full md:w-5/12 h-56 md:h-auto overflow-hidden bg-neutral-900">
                        <img
                            :src="selectedJob.image_url"
                            :alt="selectedJob.title"
                            class="absolute inset-0 h-full w-full object-cover opacity-50 mix-blend-luminosity"
                        />
                        <div class="absolute inset-0 bg-linear-to-t md:bg-linear-to-r from-[#0a0a0a] via-[#0a0a0a]/80 to-transparent"></div>

                        <div class="absolute inset-0 p-8 flex flex-col justify-end md:justify-center">
                            <span class="inline-flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 drop-shadow-md">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                {{ selectedJob.company || 'SmartRecruit' }}
                            </span>
                            <h3 class="text-3xl md:text-4xl font-extrabold text-white leading-tight mb-4 drop-shadow-lg">{{ selectedJob.title }}</h3>

                            <div class="flex flex-col gap-2 mt-2 text-sm text-neutral-300 font-medium">
                                <span class="flex items-center gap-2">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-white/10 text-gray-400">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </div>
                                    {{ selectedJob.location || 'Remote' }}
                                </span>
                                <span class="flex items-center gap-2">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-white/10 text-gray-400">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    {{ selectedJob.type || 'Full-time' }}
                                </span>
                            </div>

                            <p class="mt-6 text-sm text-neutral-400 line-clamp-4 leading-relaxed">
                                {{ selectedJob.description }}
                            </p>
                        </div>
                    </div>

                    <!-- Right Column: Form -->
                    <div class="relative w-full md:w-7/12 p-8 md:p-10 flex flex-col justify-center">
                        <!-- Close Button (Desktop) -->
                        <button
                            @click="closeApplyModal"
                            class="absolute top-6 right-6 hidden md:flex h-10 w-10 items-center justify-center rounded-full bg-white/5 border border-white/10 text-neutral-400 hover:bg-white/10 hover:text-white transition-all hover:rotate-90"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>

                        <div class="mb-8">
                            <h4 class="text-2xl font-bold text-white mb-2">Kirim Lamaran</h4>
                            <p class="text-sm text-neutral-400">Lengkapi data di bawah ini untuk melamar posisi impian Anda.</p>
                        </div>

                        <form @submit.prevent="submitApplication" class="space-y-6">
                            <!-- Phone Input -->
                            <div>
                                <label class="mb-2 block text-xs font-bold tracking-widest text-neutral-300 uppercase">
                                    Nomor Telepon
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    </div>
                                    <input
                                        v-model="applyForm.phone"
                                        type="tel"
                                        placeholder="08123456789"
                                        required
                                        class="w-full rounded-2xl border border-white/10 bg-white/5 py-3.5 pl-12 pr-4 text-sm text-white placeholder-neutral-600 transition-all focus:border-gray-500 focus:bg-white/10 focus:outline-none focus:ring-4 focus:ring-gray-500/10"
                                    />
                                </div>
                            </div>

                            <!-- Cover Letter -->
                            <div>
                                <label class="mb-2 block text-xs font-bold tracking-widest text-neutral-300 uppercase">
                                    Surat Lamaran (Cover Letter)
                                </label>
                                <textarea
                                    v-model="applyForm.cover_letter"
                                    rows="4"
                                    placeholder="Ceritakan mengapa Anda adalah kandidat terbaik..."
                                    required
                                    class="w-full resize-none rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white placeholder-neutral-600 transition-all focus:border-gray-500 focus:bg-white/10 focus:outline-none focus:ring-4 focus:ring-gray-500/10"
                                ></textarea>
                            </div>

                            <!-- PEMILIHAN CV -->
                            <div v-if="userDocuments && userDocuments.length > 0">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-xs font-bold tracking-widest text-neutral-300 uppercase">
                                        Pilih CV Anda
                                    </label>
                                    <a href="/my-profile" class="text-xs font-semibold text-gray-500 hover:text-gray-400 transition">Upload Baru &rarr;</a>
                                </div>
                                <div class="grid grid-cols-1 gap-3 max-h-[160px] overflow-y-auto pr-2 custom-scrollbar">
                                    <div
                                        v-for="doc in userDocuments"
                                        :key="doc.id"
                                        @click="applyForm.document_id = doc.id"
                                        class="group flex cursor-pointer items-center gap-4 rounded-2xl border p-4 transition-all duration-300"
                                        :class="[
                                            applyForm.document_id === doc.id
                                                ? 'border-gray-500 bg-gray-500/10 shadow-[0_0_15px_rgba(107,114,128,0.1)]'
                                                : 'border-white/10 bg-white/5 hover:border-white/20 hover:bg-white/10',
                                        ]"
                                    >
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl transition-colors duration-300"
                                            :class="applyForm.document_id === doc.id ? 'bg-gray-500 text-black' : 'bg-white/10 text-neutral-400 group-hover:text-white group-hover:bg-white/20'"
                                        >
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-bold text-white">{{ doc.name }}</p>
                                            <p class="text-xs text-neutral-400 mt-0.5">Diunggah pada {{ doc.upload_date }}</p>
                                        </div>
                                        <div
                                            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full border-2 transition-all duration-300"
                                            :class="applyForm.document_id === doc.id ? 'border-gray-500 bg-gray-500 text-black scale-110' : 'border-neutral-600 bg-transparent'"
                                        >
                                            <svg v-if="applyForm.document_id === doc.id" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="rounded-2xl border-2 border-dashed border-white/10 bg-white/5 p-6 text-center">
                                <svg class="mx-auto h-12 w-12 text-neutral-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <p class="text-sm font-semibold text-white mb-1">Anda belum memiliki CV</p>
                                <p class="text-xs text-neutral-400 mb-4">Silakan upload CV di profil Anda untuk melamar.</p>
                                <a href="/my-profile" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-xs font-bold text-black transition hover:bg-neutral-200">
                                    Upload CV Sekarang
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                </a>
                            </div>

                            <!-- Actions -->
                            <div class="pt-6 flex gap-4">
                                <button
                                    type="button"
                                    @click="closeApplyModal"
                                    class="flex-1 rounded-2xl border border-white/10 bg-transparent px-4 py-3.5 text-sm font-bold text-white transition hover:bg-white/10"
                                    :disabled="isSubmitting"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    class="relative flex-2 overflow-hidden rounded-2xl bg-linear-to-r from-gray-500 to-gray-600 px-4 py-3.5 text-sm font-bold text-black shadow-[0_0_20px_rgba(107,114,128,0.3)] transition-all hover:scale-[1.02] hover:shadow-[0_0_30px_rgba(107,114,128,0.5)] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100"
                                    :disabled="isSubmitting || (!applyForm.document_id && userDocuments.length > 0)"
                                >
                                    <span v-if="isSubmitting" class="flex items-center justify-center gap-2">
                                        <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Mengirim...
                                    </span>
                                    <span v-else class="flex items-center justify-center gap-2">
                                        Submit Lamaran
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Transition>

    </div>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .relative.w-full,
.modal-leave-to .relative.w-full {
    transform: scale(0.9) translateY(20px);
}

.modal-enter-active .relative.w-full,
.modal-leave-active .relative.w-full {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.toast-enter-active,
.toast-leave-active {
    transition: all 0.4s ease;
}
.toast-enter-from {
    opacity: 0;
    transform: translateX(100px);
}
.toast-leave-to {
    opacity: 0;
    transform: translateX(100px);
}
</style>
