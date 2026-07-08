<script setup lang="ts">
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Swal from 'sweetalert2';

type AuthUser = {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string | null;
    avatar_path?: string | null;
    avatar_url?: string | null;
};

type PageProps = {
    auth: {
        user: AuthUser;
    };
    mustVerifyEmail?: boolean;
    status?: string | null;
    [key: string]: unknown;
};

const props = withDefaults(
    defineProps<{
        mustVerifyEmail?: boolean;
        status?: string | null;
    }>(),
    {
        mustVerifyEmail: false,
        status: null,
    },
);

defineOptions({
    inheritAttrs: false,
    layout: {
        breadcrumbs: [
            {
                title: 'Profile settings',
                href: '/settings/profile',
            },
        ],
    },
});

const page = usePage<PageProps>();

const user = computed(() => page.props.auth.user);

const profileForm = useForm({
    name: user.value.name ?? '',
    email: user.value.email ?? '',
});

const avatarForm = useForm({
    avatar: null as File | null,
});

const deleteForm = useForm({
    password: '',
});

const avatarInput = ref<HTMLInputElement | null>(null);
const avatarInputKey = ref(0);
const avatarPreviewUrl = ref<string | null>(null);

const userInitial = computed(() => {
    return user.value.name?.charAt(0)?.toUpperCase() ?? 'U';
});

const currentAvatarUrl = computed(() => {
    return avatarPreviewUrl.value ?? user.value.avatar_url ?? null;
});

const handleAvatarChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0] ?? null;

    avatarForm.avatar = file;

    if (!file) {
        avatarPreviewUrl.value = null;
        return;
    }

    avatarPreviewUrl.value = URL.createObjectURL(file);
};

const clearAvatarInput = () => {
    avatarForm.avatar = null;
    avatarForm.reset('avatar');
    avatarPreviewUrl.value = null;

    if (avatarInput.value) {
        avatarInput.value.value = '';
    }

    avatarInputKey.value++;
};

const openAvatarFilePicker = () => {
    avatarInput.value?.click();
};

const submitAvatar = () => {
    if (!avatarForm.avatar) {
        Swal.fire({
            title: 'Belum ada gambar',
            text: 'Pilih profile image terlebih dahulu.',
            icon: 'warning',
            confirmButtonText: 'Oke',
            confirmButtonColor: '#ffffff',
            background: '#111111',
            color: '#ffffff',
        });

        return;
    }

    avatarForm.post('/profile/avatar', {
        forceFormData: true,
        preserveScroll: true,

        onStart: () => {
            Swal.fire({
                title: 'Mengupload profile image...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                background: '#111111',
                color: '#ffffff',
                didOpen: () => Swal.showLoading(),
            });
        },

        onSuccess: () => {
            clearAvatarInput();

            Swal.fire({
                title: 'Berhasil',
                text: 'Profile image berhasil diperbarui.',
                icon: 'success',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#ffffff',
                background: '#111111',
                color: '#ffffff',
            });
        },

        onError: (errors) => {
            Swal.fire({
                title: 'Upload gagal',
                text:
                    errors.avatar ??
                    'Profile image gagal diupload. Gunakan JPG, JPEG, PNG, atau WEBP maksimal 2 MB.',
                icon: 'error',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#ef4444',
                background: '#111111',
                color: '#ffffff',
            });
        },
    });
};

const submitProfile = () => {
    profileForm.patch('/settings/profile', {
        preserveScroll: true,

        onStart: () => {
            Swal.fire({
                title: 'Menyimpan account...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                background: '#111111',
                color: '#ffffff',
                didOpen: () => Swal.showLoading(),
            });
        },

        onSuccess: () => {
            Swal.fire({
                title: 'Berhasil',
                text: 'Account profile berhasil disimpan.',
                icon: 'success',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#ffffff',
                background: '#111111',
                color: '#ffffff',
            });
        },

        onError: () => {
            Swal.fire({
                title: 'Gagal menyimpan',
                text: 'Periksa kembali name dan email yang kamu masukkan.',
                icon: 'error',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#ef4444',
                background: '#111111',
                color: '#ffffff',
            });
        },
    });
};

const resendVerificationEmail = () => {
    router.post(
        '/email/verification-notification',
        {},
        {
            preserveScroll: true,

            onSuccess: () => {
                Swal.fire({
                    title: 'Verification email terkirim',
                    text: 'Cek inbox email kamu untuk melakukan verifikasi.',
                    icon: 'success',
                    confirmButtonText: 'Oke',
                    confirmButtonColor: '#ffffff',
                    background: '#111111',
                    color: '#ffffff',
                });
            },
        },
    );
};

const confirmDeleteAccount = async () => {
    const result = await Swal.fire({
        title: 'Delete account?',
        text: 'Akun kamu akan dihapus permanen. Tindakan ini tidak bisa dikembalikan.',
        icon: 'warning',
        input: 'password',
        inputLabel: 'Masukkan password akun',
        inputPlaceholder: 'Password',
        inputAttributes: {
            autocapitalize: 'off',
            autocorrect: 'off',
        },
        showCancelButton: true,
        confirmButtonText: 'Delete Account',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#525252',
        background: '#111111',
        color: '#ffffff',
        inputValidator: (value) => {
            if (!value) {
                return 'Password wajib diisi.';
            }

            return null;
        },
    });

    if (!result.isConfirmed || !result.value) {
        return;
    }

    deleteForm.password = result.value;

    deleteForm.delete('/settings/profile', {
        preserveScroll: true,

        onStart: () => {
            Swal.fire({
                title: 'Deleting account...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                background: '#111111',
                color: '#ffffff',
                didOpen: () => Swal.showLoading(),
            });
        },

        onError: (errors) => {
            Swal.fire({
                title: 'Delete account gagal',
                text:
                    errors.password ??
                    'Password salah atau akun gagal dihapus. Coba lagi.',
                icon: 'error',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#ef4444',
                background: '#111111',
                color: '#ffffff',
            });
        },

        onFinish: () => {
            deleteForm.reset('password');
        },
    });
};
</script>

<template>
    <div class="w-full">
        <Head title="Profile settings" />

        <div class="w-full max-w-5xl space-y-6 text-neutral-950 dark:text-white">
            <!-- Profile Image -->
            <section
                class="overflow-hidden rounded-3xl border border-neutral-200 bg-white shadow-sm transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)]"
            >
                <div
                    class="flex flex-col gap-6 border-b border-neutral-200 px-6 py-6 transition-colors duration-300 dark:border-neutral-800 lg:flex-row lg:items-center lg:justify-between"
                >
                    <div class="flex items-center gap-5">
                        <img
                            v-if="currentAvatarUrl"
                            :src="currentAvatarUrl"
                            :alt="user.name"
                            class="h-20 w-20 shrink-0 rounded-2xl border border-neutral-200 object-cover dark:border-neutral-800"
                        />

                        <div
                            v-else
                            class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border border-neutral-200 bg-neutral-50 text-3xl font-semibold text-neutral-950 dark:border-neutral-800 dark:bg-black dark:text-white"
                        >
                            {{ userInitial }}
                        </div>

                        <div>
                            <h2
                                class="text-xl font-semibold tracking-tight text-neutral-950 dark:text-white"
                            >
                                Profile Image
                            </h2>

                            <p
                                class="mt-2 max-w-xl text-sm leading-6 text-neutral-600 dark:text-neutral-500"
                            >
                                Upload foto profile untuk ditampilkan pada akun.
                                Format yang didukung: JPG, JPEG, PNG, atau WEBP.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div
                        class="grid gap-5 rounded-2xl border border-neutral-200 bg-neutral-50 p-4 transition-colors duration-300 dark:border-neutral-800 dark:bg-black/60 lg:grid-cols-[1fr_auto] lg:items-center"
                    >
                        <div class="min-w-0 space-y-3">
                            <input
                                :key="avatarInputKey"
                                ref="avatarInput"
                                type="file"
                                accept="image/png,image/jpeg,image/jpg,image/webp"
                                @change="handleAvatarChange"
                                class="hidden"
                            />

                            <div
                                class="flex min-h-14 flex-col gap-3 rounded-2xl border border-neutral-200 bg-white px-4 py-4 transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div class="min-w-0">
                                    <p
                                        class="truncate text-sm font-medium text-neutral-800 dark:text-neutral-200"
                                    >
                                        {{
                                            avatarForm.avatar
                                                ? avatarForm.avatar.name
                                                : 'No image selected'
                                        }}
                                    </p>

                                    <p
                                        class="mt-1 text-xs leading-5 text-neutral-500 dark:text-neutral-600"
                                    >
                                        JPG, JPEG, PNG, atau WEBP maksimal 5 MB.
                                    </p>
                                </div>

                                <div class="flex shrink-0 items-center gap-2">
                                    <button
                                        type="button"
                                        @click="openAvatarFilePicker"
                                        class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-300 bg-neutral-950 px-4 text-sm font-semibold text-white transition-colors hover:bg-neutral-800 dark:border-neutral-700 dark:bg-neutral-900 dark:hover:border-neutral-600 dark:hover:bg-neutral-800"
                                    >
                                        Choose Image
                                    </button>

                                    <button
                                        v-if="avatarForm.avatar"
                                        type="button"
                                        @click="clearAvatarInput"
                                        class="inline-flex h-10 items-center justify-center rounded-xl border border-neutral-200 bg-white px-4 text-sm font-semibold text-neutral-600 transition-colors hover:border-neutral-300 hover:text-neutral-950 dark:border-neutral-800 dark:bg-black dark:text-neutral-400 dark:hover:border-neutral-700 dark:hover:text-white"
                                    >
                                        Clear
                                    </button>
                                </div>
                            </div>

                            <p
                                v-if="avatarForm.errors.avatar"
                                class="text-sm text-red-500 dark:text-red-400"
                            >
                                {{ avatarForm.errors.avatar }}
                            </p>
                        </div>

                        <button
                            type="button"
                            :disabled="
                                avatarForm.processing || !avatarForm.avatar
                            "
                            @click="submitAvatar"
                            class="inline-flex h-11 w-full items-center justify-center rounded-xl border border-neutral-950 bg-neutral-950 px-5 text-sm font-semibold text-white transition-colors hover:bg-neutral-800 disabled:cursor-not-allowed disabled:border-neutral-300 disabled:bg-neutral-200 disabled:text-neutral-500 dark:border-white dark:bg-white dark:text-black dark:hover:bg-neutral-200 dark:disabled:border-neutral-800 dark:disabled:bg-neutral-800 dark:disabled:text-neutral-500 lg:w-auto"
                        >
                            {{
                                avatarForm.processing
                                    ? 'Uploading...'
                                    : 'Save Image'
                            }}
                        </button>
                    </div>
                </div>
            </section>

            <!-- Account Information -->
            <section
                class="overflow-hidden rounded-3xl border border-neutral-200 bg-white shadow-sm transition-colors duration-300 dark:border-neutral-800 dark:bg-[#111111] dark:shadow-[0_18px_45px_rgba(255,255,255,0.035)]"
            >
                <div
                    class="border-b border-neutral-200 px-6 py-6 transition-colors duration-300 dark:border-neutral-800"
                >
                    <h2
                        class="text-xl font-semibold tracking-tight text-neutral-950 dark:text-white"
                    >
                        Account Information
                    </h2>

                    <p
                        class="mt-2 max-w-2xl text-sm leading-6 text-neutral-600 dark:text-neutral-500"
                    >
                        Update nama dan email yang terhubung dengan akun kamu.
                    </p>
                </div>

                <form
                    @submit.prevent="submitProfile"
                    class="space-y-6 px-6 py-6"
                >
                    <div class="grid gap-5 lg:grid-cols-2">
                        <div class="space-y-2">
                            <label
                                for="name"
                                class="block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                            >
                                Name
                            </label>

                            <input
                                id="name"
                                v-model="profileForm.name"
                                type="text"
                                required
                                autocomplete="name"
                                placeholder="Full name"
                                class="block w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-950 outline-none placeholder:text-neutral-400 transition-colors focus:border-neutral-500 focus:ring-0 dark:border-neutral-800 dark:bg-black dark:text-white dark:placeholder:text-neutral-700 dark:focus:border-neutral-600"
                            />

                            <p
                                v-if="profileForm.errors.name"
                                class="text-sm text-red-500 dark:text-red-400"
                            >
                                {{ profileForm.errors.name }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label
                                for="email"
                                class="block text-sm font-medium text-neutral-700 dark:text-neutral-300"
                            >
                                Email address
                            </label>

                            <input
                                id="email"
                                v-model="profileForm.email"
                                type="email"
                                required
                                autocomplete="username"
                                placeholder="Email address"
                                class="block w-full rounded-2xl border border-neutral-300 bg-white px-4 py-3 text-sm text-neutral-950 outline-none placeholder:text-neutral-400 transition-colors focus:border-neutral-500 focus:ring-0 dark:border-neutral-800 dark:bg-black dark:text-white dark:placeholder:text-neutral-700 dark:focus:border-neutral-600"
                            />

                            <p
                                v-if="profileForm.errors.email"
                                class="text-sm text-red-500 dark:text-red-400"
                            >
                                {{ profileForm.errors.email }}
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="props.mustVerifyEmail && !user.email_verified_at"
                        class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4 transition-colors duration-300 dark:border-neutral-800 dark:bg-black"
                    >
                        <p
                            class="text-sm leading-6 text-neutral-600 dark:text-neutral-500"
                        >
                            Email kamu belum terverifikasi.

                            <button
                                type="button"
                                @click="resendVerificationEmail"
                                class="font-medium text-neutral-950 underline underline-offset-4 dark:text-white"
                            >
                                Kirim ulang verification email.
                            </button>
                        </p>

                        <p
                            v-if="props.status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                        >
                            Link verifikasi baru sudah dikirim ke email kamu.
                        </p>
                    </div>

                    <div
                        class="flex justify-end border-t border-neutral-200 pt-6 transition-colors duration-300 dark:border-neutral-800"
                    >
                        <button
                            type="submit"
                            :disabled="profileForm.processing"
                            data-test="update-profile-button"
                            class="inline-flex h-11 items-center justify-center rounded-xl border border-neutral-950 bg-neutral-950 px-6 text-sm font-semibold text-white transition-colors hover:bg-neutral-800 disabled:cursor-not-allowed disabled:border-neutral-300 disabled:bg-neutral-200 disabled:text-neutral-500 dark:border-white dark:bg-white dark:text-black dark:hover:bg-neutral-200 dark:disabled:border-neutral-800 dark:disabled:bg-neutral-800 dark:disabled:text-neutral-500"
                        >
                            {{
                                profileForm.processing
                                    ? 'Saving...'
                                    : 'Save Account'
                            }}
                        </button>
                    </div>
                </form>
            </section>

            <!-- Delete Account -->
            <section
                class="overflow-hidden rounded-3xl border border-red-500/20 bg-red-500/5 shadow-[0_18px_45px_rgba(239,68,68,0.06)]"
            >
                <div
                    class="flex flex-col gap-6 px-6 py-6 lg:flex-row lg:items-center lg:justify-between"
                >
                    <div class="max-w-2xl">
                        <div
                            class="inline-flex w-fit items-center rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-red-500 dark:text-red-400"
                        >
                            Danger Zone
                        </div>

                        <h2
                            class="mt-4 text-xl font-semibold tracking-tight text-red-600 dark:text-red-300"
                        >
                            Delete Account
                        </h2>

                        <p
                            class="mt-2 text-sm leading-6 text-red-700/70 dark:text-red-200/70"
                        >
                            Setelah akun dihapus, semua data yang terkait dengan
                            akun ini akan hilang secara permanen. Pastikan kamu
                            benar-benar ingin menghapus akun sebelum
                            melanjutkan.
                        </p>
                    </div>

                    <button
                        type="button"
                        :disabled="deleteForm.processing"
                        @click="confirmDeleteAccount"
                        class="inline-flex h-11 shrink-0 items-center justify-center rounded-xl border border-red-400/20 bg-red-500 px-5 text-sm font-semibold text-white shadow-[0_12px_28px_rgba(239,68,68,0.18)] transition-colors hover:bg-red-400 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{
                            deleteForm.processing
                                ? 'Deleting...'
                                : 'Delete Account'
                        }}
                    </button>
                </div>
            </section>
        </div>
    </div>
</template>
