<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref } from 'vue';
import Swal from 'sweetalert2';

interface DocumentFile {
    id: number;
    name: string;
    file_type: string;
    file_size: string;
    upload_date: string;
    url?: string;
    preview_url?: string;
}

const props = defineProps<{
    profile: {
        id?: number;
        resume_path?: string;
        education?: string[];
        portfolio_urls?: string[];
        documents?: DocumentFile[];
    } | null;

    files?: DocumentFile[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'My Profile',
                href: '/my-profile',
            },
        ],
    },
});

const form = useForm({
    resume: null as File | null,
    education: props.profile?.education || [],
    portfolio_urls: props.profile?.portfolio_urls || [],
});

const isClient = ref(false);
const resumeInput = ref<HTMLInputElement | null>(null);
const resumeInputKey = ref(0);

const showModal = ref(false);
const selectedFile = ref<DocumentFile | null>(null);
const searchQuery = ref('');
const sortKey = ref<keyof DocumentFile>('upload_date');
const sortAsc = ref(false);

onMounted(() => {
    isClient.value = true;
});

const isDarkMode = () => {
    return document.documentElement.classList.contains('dark');
};

const swalTheme = () => {
    return {
        background: isDarkMode() ? '#111111' : '#ffffff',
        color: isDarkMode() ? '#ffffff' : '#171717',
    };
};

const handleResumeChange = (event: Event) => {
    const input = event.target as HTMLInputElement;

    form.resume = input.files?.[0] ?? null;
};

const clearResumeInput = async () => {
    form.resume = null;
    form.reset('resume');

    await nextTick();

    if (resumeInput.value) {
        resumeInput.value.value = '';
    }

    resumeInputKey.value++;
};

const files = computed<DocumentFile[]>(() => {
    return props.files ?? props.profile?.documents ?? [];
});

const filteredFiles = computed(() => {
    let result = [...files.value];

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();

        result = result.filter(
            (file) =>
                file.name.toLowerCase().includes(q) ||
                file.file_type.toLowerCase().includes(q),
        );
    }

    result.sort((a, b) => {
        const valA = a[sortKey.value] ?? '';
        const valB = b[sortKey.value] ?? '';
        const cmp = String(valA).localeCompare(String(valB));

        return sortAsc.value ? cmp : -cmp;
    });

    return result;
});

const toggleSort = (key: keyof DocumentFile) => {
    if (sortKey.value === key) {
        sortAsc.value = !sortAsc.value;
        return;
    }

    sortKey.value = key;
    sortAsc.value = true;
};

const openDetails = (file: DocumentFile) => {
    selectedFile.value = file;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedFile.value = null;
};

const fileTypeIcon = (type: string) => {
    const t = type.toUpperCase();

    if (t === 'PDF') return '📄';
    if (t === 'DOCX' || t === 'DOC') return '📝';

    if (
        t === 'PNG' ||
        t === 'JPG' ||
        t === 'JPEG' ||
        t === 'SVG' ||
        t === 'WEBP'
    ) {
        return '🖼️';
    }

    if (t === 'ZIP') return '🗜️';

    return '📎';
};

const fileTypeColor = (type: string) => {
    const t = type.toUpperCase();

    if (t === 'PDF') {
        return 'border border-red-500/20 bg-red-500/10 text-red-600 dark:text-red-400';
    }

    if (t === 'DOCX' || t === 'DOC') {
        return 'border border-blue-500/20 bg-blue-500/10 text-blue-600 dark:text-blue-400';
    }

    if (t === 'PNG' || t === 'JPG' || t === 'JPEG' || t === 'WEBP') {
        return 'border border-purple-500/20 bg-purple-500/10 text-purple-600 dark:text-purple-400';
    }

    if (t === 'SVG') {
        return 'border border-orange-500/20 bg-orange-500/10 text-orange-600 dark:text-orange-400';
    }

    return 'border border-neutral-500/20 bg-neutral-500/10 text-neutral-600 dark:text-neutral-400';
};

const isImageFile = (type: string) => {
    const t = type.toLowerCase();

    return ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'].includes(t);
};

const isPdfFile = (type: string) => {
    return type.toLowerCase() === 'pdf';
};

const isWordFile = (type: string) => {
    const t = type.toLowerCase();

    return ['doc', 'docx'].includes(t);
};

const filePreviewUrl = (file: DocumentFile) => {
    return file.preview_url ?? file.url ?? '';
};

const deleteFile = async (id: number) => {
    const result = await Swal.fire({
        title: 'Hapus dokumen?',
        text: 'Dokumen ini akan dihapus secara permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#525252',
        ...swalTheme(),
    });

    if (!result.isConfirmed) {
        return;
    }

    router.delete(`/my-profile/documents/${id}`, {
        preserveScroll: true,

        onSuccess: () => {
            closeModal();

            Swal.fire({
                title: 'Berhasil',
                text: 'Dokumen berhasil dihapus.',
                icon: 'success',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#22c55e',
                ...swalTheme(),
            });

            router.reload({
                only: ['profile', 'files'],
            });
        },

        onError: () => {
            Swal.fire({
                title: 'Gagal',
                text: 'Dokumen gagal dihapus. Coba lagi.',
                icon: 'error',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#ef4444',
                ...swalTheme(),
            });
        },
    });
};

const submit = () => {
    if (form.resume) {
        form.post('/my-profile/documents', {
            forceFormData: true,
            preserveScroll: true,

            onStart: () => {
                Swal.fire({
                    title: 'Mengupload dokumen...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    ...swalTheme(),
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            },

            onSuccess: async () => {
                await clearResumeInput();

                Swal.fire({
                    title: 'Berhasil',
                    text: 'Dokumen berhasil disimpan.',
                    icon: 'success',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#22c55e',
                    ...swalTheme(),
                });

                router.visit('/my-profile', {
                    replace: true,
                    preserveScroll: true,
                    preserveState: false,
                    only: ['profile', 'files'],
                });
            },

            onError: (errors) => {
                Swal.fire({
                    title: 'Upload gagal',
                    text:
                        errors.resume ??
                        'Dokumen gagal diupload. Cek kembali format atau ukuran file.',
                    icon: 'error',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#ef4444',
                    ...swalTheme(),
                });
            },
        });

        return;
    }

    if (props.profile?.id) {
        form.put(`/my-profile/${props.profile.id}`, {
            preserveScroll: true,

            onSuccess: () => {
                Swal.fire({
                    title: 'Berhasil',
                    text: 'Profile berhasil disimpan.',
                    icon: 'success',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#22c55e',
                    ...swalTheme(),
                });
            },

            onError: () => {
                Swal.fire({
                    title: 'Gagal',
                    text: 'Profile gagal disimpan.',
                    icon: 'error',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#ef4444',
                    ...swalTheme(),
                });
            },
        });

        return;
    }

    form.post('/my-profile', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="My Profile" />

    <div
        class="flex min-h-screen flex-1 flex-col gap-12 overflow-x-auto bg-white p-8 font-sans text-neutral-950 transition-colors duration-300 dark:bg-black dark:text-white"
    >
        <div class="mx-auto w-full max-w-[1000px]">
            <div class="mb-10 text-center">
                <p class="mb-2 text-sm font-medium tracking-tight text-neutral-500">
                    Your Resume Data
                </p>

                <h1
                    class="mb-4 text-4xl font-semibold leading-tight tracking-tight text-neutral-950 dark:text-white md:text-6xl"
                >
                    Candidate File Upload
                </h1>

                <p
                    class="mx-auto max-w-lg text-base text-neutral-600 dark:text-neutral-500"
                >
                    Manage your uploaded resume and documents to match with open
                    positions.
                </p>
            </div>

            <div
                class="mb-10 rounded-3xl border border-neutral-200 bg-white p-8 shadow-sm transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)] md:p-12"
            >
                <form @submit.prevent="submit" class="space-y-8">
                    <div class="pb-4">
                        <label
                            class="mb-2 block text-base font-medium text-neutral-800 dark:text-neutral-200"
                        >
                            Resume Document
                        </label>

                        <div
                            class="flex flex-col gap-4 rounded-2xl border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-800 dark:bg-black sm:flex-row sm:items-center"
                        >
                            <input
                                :key="resumeInputKey"
                                ref="resumeInput"
                                type="file"
                                accept=".pdf,.doc,.docx,.png,.jpg,.jpeg,.webp"
                                @change="handleResumeChange"
                                class="text-sm text-neutral-500 file:mr-4 file:cursor-pointer file:rounded-xl file:border-0 file:bg-neutral-950 file:px-4 file:py-2 file:font-semibold file:text-white file:transition-colors hover:file:bg-neutral-800 dark:text-neutral-400 dark:file:bg-neutral-800 dark:file:text-neutral-200 dark:hover:file:bg-neutral-700"
                            />

                            <div
                                v-if="form.resume"
                                class="flex min-w-0 items-center gap-2 text-sm text-neutral-700 dark:text-neutral-300"
                            >
                                <span
                                    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-neutral-950 text-white dark:bg-white dark:text-black"
                                >
                                    ✓
                                </span>

                                <span class="max-w-[240px] truncate">
                                    {{ form.resume.name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex h-11 items-center justify-center rounded-xl border border-neutral-950 bg-neutral-950 px-6 text-sm font-semibold text-white transition-colors hover:bg-neutral-800 disabled:cursor-not-allowed disabled:border-neutral-300 disabled:bg-neutral-200 disabled:text-neutral-500 dark:border-white dark:bg-white dark:text-black dark:hover:bg-neutral-200 dark:disabled:border-neutral-800 dark:disabled:bg-neutral-800 dark:disabled:text-neutral-500"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Profile' }}
                        </button>
                    </div>
                </form>
            </div>

            <div
                class="overflow-hidden rounded-3xl border border-neutral-200 bg-white shadow-sm transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)]"
            >
                <div
                    class="flex flex-col items-start justify-between gap-4 border-b border-neutral-200 px-6 pb-4 pt-6 dark:border-neutral-800 sm:flex-row sm:items-center md:px-8"
                >
                    <h2
                        class="text-xl font-semibold tracking-tight text-neutral-950 dark:text-white"
                    >
                        Document Vault
                    </h2>

                    <div class="relative w-full sm:w-64">
                        <svg
                            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-500"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>

                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search documents..."
                            class="w-full rounded-xl border border-neutral-200 bg-neutral-50 py-2 pl-10 pr-4 text-sm text-neutral-950 placeholder:text-neutral-400 outline-none transition-colors focus:border-neutral-500 focus:ring-0 dark:border-neutral-800 dark:bg-black dark:text-neutral-200 dark:placeholder:text-neutral-600 dark:focus:border-neutral-600"
                        />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap text-left text-sm">
                        <thead>
                            <tr
                                class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-800 dark:bg-neutral-950/40"
                            >
                                <th
                                    @click="toggleSort('name')"
                                    class="cursor-pointer px-6 py-4 text-xs font-semibold uppercase tracking-wider text-neutral-500 transition-colors select-none hover:text-neutral-950 dark:hover:text-white md:px-8"
                                >
                                    <span class="inline-flex items-center gap-1">
                                        Document Name
                                        <svg
                                            v-if="sortKey === 'name'"
                                            class="h-3 w-3"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                v-if="sortAsc"
                                                d="M5 12l5-5 5 5H5z"
                                            />
                                            <path v-else d="M5 8l5 5 5-5H5z" />
                                        </svg>
                                    </span>
                                </th>

                                <th
                                    @click="toggleSort('file_type')"
                                    class="cursor-pointer px-4 py-4 text-xs font-semibold uppercase tracking-wider text-neutral-500 transition-colors select-none hover:text-neutral-950 dark:hover:text-white"
                                >
                                    <span class="inline-flex items-center gap-1">
                                        Type
                                        <svg
                                            v-if="sortKey === 'file_type'"
                                            class="h-3 w-3"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                v-if="sortAsc"
                                                d="M5 12l5-5 5 5H5z"
                                            />
                                            <path v-else d="M5 8l5 5 5-5H5z" />
                                        </svg>
                                    </span>
                                </th>

                                <th
                                    @click="toggleSort('file_size')"
                                    class="cursor-pointer px-4 py-4 text-xs font-semibold uppercase tracking-wider text-neutral-500 transition-colors select-none hover:text-neutral-950 dark:hover:text-white"
                                >
                                    <span class="inline-flex items-center gap-1">
                                        Size
                                        <svg
                                            v-if="sortKey === 'file_size'"
                                            class="h-3 w-3"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                v-if="sortAsc"
                                                d="M5 12l5-5 5 5H5z"
                                            />
                                            <path v-else d="M5 8l5 5 5-5H5z" />
                                        </svg>
                                    </span>
                                </th>

                                <th
                                    @click="toggleSort('upload_date')"
                                    class="cursor-pointer px-4 py-4 text-xs font-semibold uppercase tracking-wider text-neutral-500 transition-colors select-none hover:text-neutral-950 dark:hover:text-white"
                                >
                                    <span class="inline-flex items-center gap-1">
                                        Date Added
                                        <svg
                                            v-if="sortKey === 'upload_date'"
                                            class="h-3 w-3"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                v-if="sortAsc"
                                                d="M5 12l5-5 5 5H5z"
                                            />
                                            <path v-else d="M5 8l5 5 5-5H5z" />
                                        </svg>
                                    </span>
                                </th>

                                <th
                                    class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-neutral-500"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-neutral-200 dark:divide-neutral-800"
                        >
                            <tr
                                v-for="file in filteredFiles"
                                :key="file.id"
                                class="group transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-900/50"
                            >
                                <td class="px-6 py-4 md:px-8">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-lg dark:border-neutral-800 dark:bg-black"
                                        >
                                            {{ fileTypeIcon(file.file_type) }}
                                        </div>

                                        <span
                                            class="max-w-[200px] truncate font-medium text-neutral-800 transition-colors group-hover:text-neutral-950 dark:text-neutral-200 dark:group-hover:text-white"
                                        >
                                            {{ file.name }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <span
                                        class="inline-block rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="fileTypeColor(file.file_type)"
                                    >
                                        {{ file.file_type.toUpperCase() }}
                                    </span>
                                </td>

                                <td
                                    class="px-4 py-4 text-neutral-600 dark:text-neutral-400"
                                >
                                    {{ file.file_size }}
                                </td>

                                <td
                                    class="px-4 py-4 text-neutral-600 dark:text-neutral-400"
                                >
                                    {{ file.upload_date }}
                                </td>

                                <td class="px-4 py-4">
                                    <div
                                        class="flex items-center justify-center gap-3"
                                    >
                                        <button
                                            type="button"
                                            @click="openDetails(file)"
                                            class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-blue-500/20 bg-blue-500/10 px-4 text-sm font-semibold text-blue-600 transition-all duration-200 hover:border-blue-500/30 hover:bg-blue-500/20 dark:text-blue-400 dark:hover:text-blue-300"
                                            title="View Details"
                                        >
                                            <svg
                                                class="h-5 w-5 shrink-0"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                />
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                />
                                            </svg>

                                            <span>Detail</span>
                                        </button>

                                        <button
                                            type="button"
                                            @click="deleteFile(file.id)"
                                            class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-red-500/20 bg-red-500/10 px-4 text-sm font-semibold text-red-600 transition-all duration-200 hover:border-red-500/30 hover:bg-red-500/20 dark:text-red-400 dark:hover:text-red-300"
                                            title="Delete File"
                                        >
                                            <svg
                                                class="h-5 w-5 shrink-0"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                />
                                            </svg>

                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="filteredFiles.length === 0">
                                <td
                                    colspan="5"
                                    class="px-8 py-16 text-center text-sm text-neutral-500"
                                >
                                    <div
                                        class="flex flex-col items-center justify-center"
                                    >
                                        <svg
                                            class="mb-4 h-12 w-12 text-neutral-400 dark:text-neutral-700"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>

                                        No documents found.
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <Teleport v-if="isClient" to="body">
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 bg-neutral-950/50 backdrop-blur-sm dark:bg-black/80"
                @click.self="closeModal"
            />
        </transition>

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="showModal && selectedFile"
                class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-950/40 p-4 backdrop-blur-sm dark:bg-black/70"
                @click.self="closeModal"
            >
                <div
                    class="grid max-h-[90vh] w-full max-w-5xl overflow-hidden rounded-3xl border border-neutral-200 bg-white shadow-2xl dark:border-neutral-800 dark:bg-[#111111] lg:grid-cols-[1.4fr_0.8fr]"
                >
                    <!-- Preview Area -->
                    <div
                        class="flex min-h-[520px] items-center justify-center bg-neutral-50 p-4 dark:bg-black"
                    >
                        <div
                            v-if="
                                isImageFile(selectedFile.file_type) &&
                                filePreviewUrl(selectedFile)
                            "
                            class="flex h-full w-full items-center justify-center overflow-hidden rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-950"
                        >
                            <img
                                :src="filePreviewUrl(selectedFile)"
                                :alt="selectedFile.name"
                                class="max-h-[72vh] w-full object-contain"
                            />
                        </div>

                        <div
                            v-else-if="
                                isPdfFile(selectedFile.file_type) &&
                                filePreviewUrl(selectedFile)
                            "
                            class="h-[72vh] w-full overflow-hidden rounded-2xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-950"
                        >
                            <iframe
                                :src="filePreviewUrl(selectedFile)"
                                class="h-full w-full"
                                title="Document Preview"
                            />
                        </div>

                        <div
                            v-else-if="
                                isWordFile(selectedFile.file_type) &&
                                filePreviewUrl(selectedFile)
                            "
                            class="flex h-full w-full flex-col items-center justify-center rounded-2xl border border-dashed border-neutral-300 bg-white px-8 text-center dark:border-neutral-700 dark:bg-neutral-950"
                        >
                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-2xl border border-neutral-200 bg-neutral-50 text-4xl dark:border-neutral-800 dark:bg-black"
                            >
                                {{ fileTypeIcon(selectedFile.file_type) }}
                            </div>

                            <h3
                                class="mt-5 text-xl font-semibold text-neutral-950 dark:text-white"
                            >
                                Preview DOCX belum tersedia
                            </h3>

                            <p
                                class="mt-2 max-w-sm text-sm leading-6 text-neutral-600 dark:text-neutral-500"
                            >
                                File DOC/DOCX tersimpan, tetapi browser tidak
                                bisa menampilkan file ini secara langsung.
                            </p>

                            <a
                                :href="filePreviewUrl(selectedFile)"
                                target="_blank"
                                class="mt-6 inline-flex h-11 items-center justify-center rounded-xl border border-neutral-950 bg-neutral-950 px-5 text-sm font-semibold text-white transition-colors hover:bg-neutral-800 dark:border-white dark:bg-white dark:text-black dark:hover:bg-neutral-200"
                            >
                                Buka File
                            </a>
                        </div>

                        <div
                            v-else
                            class="flex h-full w-full flex-col items-center justify-center rounded-2xl border border-dashed border-neutral-300 bg-white px-8 text-center dark:border-neutral-700 dark:bg-neutral-950"
                        >
                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-2xl border border-neutral-200 bg-neutral-50 text-4xl dark:border-neutral-800 dark:bg-black"
                            >
                                {{ fileTypeIcon(selectedFile.file_type) }}
                            </div>

                            <h3
                                class="mt-5 text-xl font-semibold text-neutral-950 dark:text-white"
                            >
                                Preview belum tersedia
                            </h3>

                            <p
                                class="mt-2 max-w-sm text-sm leading-6 text-neutral-600 dark:text-neutral-500"
                            >
                                File dengan tipe
                                {{ selectedFile.file_type.toUpperCase() }}
                                belum bisa ditampilkan langsung sebagai preview.
                            </p>
                        </div>
                    </div>

                    <!-- Detail Area -->
                    <div class="flex max-h-[90vh] flex-col overflow-y-auto">
                        <div
                            class="flex items-center justify-between gap-4 border-b border-neutral-200 px-6 py-4 dark:border-neutral-800"
                        >
                            <h3
                                class="text-lg font-semibold text-neutral-950 dark:text-white"
                            >
                                Document Details
                            </h3>

                            <button
                                type="button"
                                @click="closeModal"
                                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-100 text-neutral-600 shadow-sm transition-all duration-200 hover:border-neutral-300 hover:bg-neutral-200 hover:text-neutral-950 active:scale-95 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:border-neutral-600 dark:hover:bg-neutral-700 dark:hover:text-white"
                                aria-label="Close modal"
                                title="Close"
                            >
                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.25"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-5 px-6 py-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-2xl dark:border-neutral-800 dark:bg-black"
                                >
                                    {{ fileTypeIcon(selectedFile.file_type) }}
                                </div>

                                <div class="min-w-0">
                                    <p
                                        class="break-all text-base font-semibold text-neutral-950 dark:text-white"
                                    >
                                        {{ selectedFile.name }}
                                    </p>

                                    <span
                                        class="mt-2 inline-block rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        :class="
                                            fileTypeColor(
                                                selectedFile.file_type,
                                            )
                                        "
                                    >
                                        {{
                                            selectedFile.file_type.toUpperCase()
                                        }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="space-y-3 border-t border-neutral-200 pt-4 dark:border-neutral-800"
                            >
                                <div
                                    class="flex items-center justify-between gap-4 py-1"
                                >
                                    <span class="text-sm text-neutral-500">
                                        File Type
                                    </span>

                                    <span
                                        class="text-sm font-medium text-neutral-700 dark:text-neutral-200"
                                    >
                                        {{
                                            selectedFile.file_type.toUpperCase()
                                        }}
                                    </span>
                                </div>

                                <div
                                    class="flex items-center justify-between gap-4 py-1"
                                >
                                    <span class="text-sm text-neutral-500">
                                        File Size
                                    </span>

                                    <span
                                        class="text-sm font-medium text-neutral-700 dark:text-neutral-200"
                                    >
                                        {{ selectedFile.file_size }}
                                    </span>
                                </div>

                                <div
                                    class="flex items-center justify-between gap-4 py-1"
                                >
                                    <span class="text-sm text-neutral-500">
                                        Upload Date
                                    </span>

                                    <span
                                        class="text-sm font-medium text-neutral-700 dark:text-neutral-200"
                                    >
                                        {{ selectedFile.upload_date }}
                                    </span>
                                </div>

                                <div
                                    class="flex items-center justify-between gap-4 py-1"
                                >
                                    <span class="text-sm text-neutral-500">
                                        File ID
                                    </span>

                                    <span
                                        class="text-sm font-medium text-neutral-400 dark:text-neutral-600"
                                    >
                                        #{{ selectedFile.id }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </Teleport>
</template>
