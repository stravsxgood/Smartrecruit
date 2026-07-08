<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Swal from 'sweetalert2';

type JobPosition = {
    id: number;
    title: string;
};

defineProps<{
    jobPositions: JobPosition[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Upload CV',
                href: '/job-positions',
            },
        ],
    },
});

const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const isDragging = ref(false);
const isUploading = ref(false);
const uploadProgress = ref(0);

let progressTimer: number | null = null;

const isUploaded = computed(() => {
    return selectedFile.value !== null && uploadProgress.value === 100;
});

const formattedFileSize = computed(() => {
    if (!selectedFile.value) return '';

    const sizeInKb = selectedFile.value.size / 1024;

    if (sizeInKb < 1024) {
        return `${sizeInKb.toFixed(1)} KB`;
    }

    return `${(sizeInKb / 1024).toFixed(2)} MB`;
});

const progressLabel = computed(() => {
    if (!selectedFile.value) return 'Waiting for file';
    if (isUploaded.value) return 'Upload completed';
    if (isUploading.value) return 'Uploading CV';

    return 'Ready to upload';
});

const progressBarStyle = computed(() => ({
    width: `${uploadProgress.value}%`,
    backgroundColor: '#22c55e',
    transition: 'width 100ms linear',
}));

const isDarkMode = () => {
    return document.documentElement.classList.contains('dark');
};

const swalTheme = () => {
    return {
        background: isDarkMode() ? '#111111' : '#ffffff',
        color: isDarkMode() ? '#ffffff' : '#171717',
        confirmButtonColor: isDarkMode() ? '#ffffff' : '#171717',
    };
};

function openFilePicker() {
    if (selectedFile.value) return;

    fileInput.value?.click();
}

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        acceptFile(file);
    }
}

function handleDrop(event: DragEvent) {
    isDragging.value = false;

    const file = event.dataTransfer?.files?.[0];

    if (file) {
        acceptFile(file);
    }
}

function acceptFile(file: File) {
    const allowedExtensions = ['pdf'];
    const extension = file.name.split('.').pop()?.toLowerCase();

    if (!allowedExtensions.includes(extension || '')) {
        Swal.fire({
            icon: 'error',
            title: 'Format Tidak Sesuai',
            text: 'Gunakan file CV dengan format PDF.',
            confirmButtonText: 'Oke',
            ...swalTheme(),
        });

        return;
    }

    selectedFile.value = file;
    uploadProgress.value = 0;

    startProgressAnimation();
}

function startProgressAnimation() {
    if (progressTimer !== null) {
        window.clearInterval(progressTimer);
    }

    isUploading.value = true;
    uploadProgress.value = 0;

    progressTimer = window.setInterval(() => {
        if (uploadProgress.value >= 100) {
            uploadProgress.value = 100;
            isUploading.value = false;

            if (progressTimer !== null) {
                window.clearInterval(progressTimer);
                progressTimer = null;
            }

            return;
        }

        uploadProgress.value += 2;
    }, 40);
}

function resetUpload() {
    if (progressTimer !== null) {
        window.clearInterval(progressTimer);
        progressTimer = null;
    }

    selectedFile.value = null;
    isUploading.value = false;
    uploadProgress.value = 0;
    isDragging.value = false;

    if (fileInput.value) {
        fileInput.value.value = '';
    }
}

function submitForAnalysis() {
    if (!isUploaded.value || !selectedFile.value) return;

    Swal.fire({
        title: 'AI sedang menganalisis...',
        html: `Membaca data dari file <b>${selectedFile.value.name}</b><br/><br/>Mohon tunggu sebentar.`,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        ...swalTheme(),
        didOpen: () => {
            Swal.showLoading();
        },
    });

    const formData = new FormData();
    formData.append('cv', selectedFile.value);

    router.post('/cv-analysis/analyze', formData, {
        forceFormData: true,
        preserveScroll: true,

        onError: (errors) => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menganalisis CV',
                text:
                    errors.cv ??
                    'Analisis CV gagal. Periksa file CV atau konfigurasi API.',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#ef4444',
                background: isDarkMode() ? '#111111' : '#ffffff',
                color: isDarkMode() ? '#ffffff' : '#171717',
            });
        },

        onFinish: () => {
            Swal.close();
        },
    });
}
</script>

<template>
    <Head title="Upload CV" />

    <div
        class="min-h-full flex-1 overflow-x-hidden bg-white px-6 py-8 text-neutral-950 transition-colors duration-300 dark:bg-black dark:text-white md:px-10"
    >
        <div class="mx-auto flex w-full max-w-5xl flex-col gap-10">
            <header class="flex flex-col gap-4">
                <div
                    class="inline-flex w-fit items-center rounded-full border border-neutral-200 bg-neutral-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.22em] text-neutral-500 dark:border-neutral-800 dark:bg-[#111111] dark:text-neutral-500"
                >
                    Career Application
                </div>

                <div class="max-w-3xl">
                    <h1
                        class="text-5xl font-semibold leading-none tracking-tight text-neutral-950 dark:text-white md:text-7xl"
                    >
                        Upload your CV
                    </h1>

                    <p
                        class="mt-5 max-w-2xl text-sm leading-7 text-neutral-600 dark:text-neutral-500"
                    >
                        Upload CV kamu di sini untuk memulai proses analisis
                        kandidat. Gunakan file PDF.
                    </p>
                </div>
            </header>

            <main class="grid grid-cols-1 gap-6">
                <div class="flex items-center justify-end">
                    <button
                        type="button"
                        @click="submitForAnalysis"
                        :disabled="!isUploaded"
                        class="inline-flex h-11 items-center gap-2 rounded-xl border border-neutral-950 bg-neutral-950 px-5 text-sm font-semibold text-white transition-colors hover:bg-neutral-800 disabled:cursor-not-allowed disabled:border-neutral-300 disabled:bg-neutral-200 disabled:text-neutral-500 dark:border-white dark:bg-white dark:text-black dark:hover:bg-neutral-200 dark:disabled:border-neutral-800 dark:disabled:bg-neutral-800 dark:disabled:text-neutral-500"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M9.813 15.904L9 19L8.187 15.904C7.876 14.717 6.953 13.793 5.767 13.483L2.671 12.67L5.767 11.857C6.954 11.547 7.877 10.623 8.187 9.436L9 6.34L9.813 9.436C10.124 10.623 11.047 11.547 12.233 11.857L15.329 12.67L12.233 13.483C11.046 13.793 10.123 14.717 9.813 15.904Z"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M19.06 7.426L18.5 9.5L17.94 7.426C17.72 6.611 17.089 5.98 16.274 5.76L14.2 5.2L16.274 4.64C17.089 4.42 17.72 3.789 17.94 2.974L18.5 0.899994L19.06 2.974C19.28 3.789 19.911 4.42 20.726 4.64L22.8 5.2L20.726 5.76C19.911 5.98 19.28 6.611 19.06 7.426Z"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        Analisis CV Sekarang
                    </button>
                </div>

                <section
                    class="rounded-3xl border border-neutral-200 bg-white p-3 shadow-sm transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)]"
                >
                    <div
                        role="button"
                        tabindex="0"
                        @click="openFilePicker"
                        @keydown.enter="openFilePicker"
                        @keydown.space.prevent="openFilePicker"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop"
                        class="group relative flex min-h-[520px] cursor-pointer flex-col items-center justify-center overflow-hidden rounded-[1.5rem] border-2 border-dashed p-8 text-center transition-all duration-300 md:p-12"
                        :class="
                            isDragging
                                ? 'border-neutral-950 bg-neutral-100 dark:border-white dark:bg-neutral-900'
                                : 'border-neutral-300 bg-neutral-50 hover:border-neutral-950 hover:bg-white dark:border-neutral-800 dark:bg-black dark:hover:border-neutral-600 dark:hover:bg-neutral-950'
                        "
                    >
                        <div
                            class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(0,0,0,0.06),transparent_36%)] dark:bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.08),transparent_36%)]"
                        />

                        <input
                            ref="fileInput"
                            type="file"
                            class="hidden"
                            accept=".pdf"
                            @change="handleFileChange"
                        />

                        <div
                            class="relative z-10 flex w-full max-w-2xl flex-col items-center"
                        >
                            <div
                                class="mb-8 flex h-24 w-24 items-center justify-center rounded-full border border-neutral-200 bg-white shadow-sm transition-transform duration-300 group-hover:scale-105 dark:border-neutral-800 dark:bg-[#111111] dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)]"
                            >
                                <svg
                                    class="h-11 w-11 text-neutral-950 dark:text-white"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M12 15.5V4.75M12 4.75L7.75 9M12 4.75L16.25 9"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M5 15.25V17.75C5 18.5784 5.67157 19.25 6.5 19.25H17.5C18.3284 19.25 19 18.5784 19 17.75V15.25"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </div>

                            <p
                                class="text-xs font-semibold uppercase tracking-[0.24em] text-neutral-500"
                            >
                                CV Upload Area
                            </p>

                            <h2
                                class="mt-4 text-3xl font-semibold tracking-tight text-neutral-950 dark:text-white md:text-5xl"
                            >
                                Upload atau drag file
                            </h2>

                            <p
                                class="mt-5 max-w-xl text-sm leading-7 text-neutral-600 dark:text-neutral-500"
                            >
                                Tarik file CV ke kolom ini, atau klik area
                                upload untuk memilih file.
                            </p>

                            <div
                                v-if="selectedFile"
                                class="mt-8 flex w-full items-center justify-between gap-4 rounded-3xl border border-neutral-200 bg-white p-4 text-left shadow-sm dark:border-neutral-800 dark:bg-[#111111]"
                            >
                                <div class="flex min-w-0 items-center gap-4">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-neutral-950 text-white dark:bg-white dark:text-black"
                                    >
                                        <svg
                                            class="h-6 w-6"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >
                                            <path
                                                d="M7.75 3.75H13.25L18.25 8.75V20.25H7.75C6.64543 20.25 5.75 19.3546 5.75 18.25V5.75C5.75 4.64543 6.64543 3.75 7.75 3.75Z"
                                                stroke="currentColor"
                                                stroke-width="1.6"
                                                stroke-linejoin="round"
                                            />
                                            <path
                                                d="M13.25 3.75V8.75H18.25"
                                                stroke="currentColor"
                                                stroke-width="1.6"
                                                stroke-linejoin="round"
                                            />
                                            <path
                                                d="M8.75 13H15.25M8.75 16H13.25"
                                                stroke="currentColor"
                                                stroke-width="1.6"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                    </div>

                                    <div class="min-w-0">
                                        <p
                                            class="truncate text-sm font-semibold text-neutral-950 dark:text-white"
                                        >
                                            {{ selectedFile.name }}
                                        </p>

                                        <p
                                            class="mt-1 text-xs text-neutral-500"
                                        >
                                            {{ formattedFileSize }}
                                        </p>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    @click.stop="resetUpload"
                                    class="shrink-0 rounded-xl border border-neutral-200 bg-white px-4 py-2 text-xs font-semibold text-neutral-700 transition-colors hover:border-neutral-400 hover:text-neutral-950 dark:border-neutral-800 dark:bg-black dark:text-neutral-400 dark:hover:border-neutral-600 dark:hover:text-white"
                                >
                                    Reset
                                </button>
                            </div>

                            <div class="mt-9 w-full">
                                <div
                                    class="mb-3 flex items-center justify-between"
                                >
                                    <p
                                        class="text-xs font-semibold uppercase tracking-wider text-neutral-500"
                                    >
                                        {{ progressLabel }}
                                    </p>

                                    <p
                                        class="text-xs font-bold text-neutral-950 dark:text-white"
                                    >
                                        {{ uploadProgress }}%
                                    </p>
                                </div>

                                <div
                                    class="h-3 w-full overflow-hidden rounded-full bg-neutral-200 dark:bg-neutral-800"
                                >
                                    <div
                                        class="h-full rounded-full"
                                        :style="progressBarStyle"
                                    />
                                </div>

                                <div
                                    class="mt-4 flex items-center justify-center gap-2 text-xs text-neutral-500"
                                >
                                    <span
                                        class="h-2 w-2 rounded-full"
                                        :class="
                                            isUploaded
                                                ? 'bg-green-500'
                                                : isUploading
                                                  ? 'bg-yellow-400'
                                                  : 'bg-neutral-400'
                                        "
                                    />

                                    <span>
                                        {{
                                            isUploaded
                                                ? 'CV berhasil diproses.'
                                                : isUploading
                                                  ? 'Sedang mengupload file...'
                                                  : 'Belum ada file yang diupload.'
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</template>
