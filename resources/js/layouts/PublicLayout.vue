<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Navbar Horizontal (Publik) -->
        <nav class="border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link href="/" class="text-xl font-bold text-gray-900 dark:text-white">
                            SmartRecruit
                        </Link>
                        <div class="hidden md:ml-10 md:flex md:space-x-8">
                            <Link href="/" class="text-gray-500 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Home</Link>
                            <Link href="/jobapply" class="text-gray-500 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">Lowongan</Link>
                        </div>
                    </div>

                    <!-- Auth Buttons -->
                    <div class="flex items-center gap-4">
                        <template v-if="user">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Halo, {{ user.name }}</span>
                            <Link href="/dashboard" class="text-sm font-medium text-blue-600 hover:underline">Dashboard</Link>
                        </template>
                        <template v-else>
                            <Link href="/login" class="text-sm text-gray-600 dark:text-gray-300 hover:underline">Login</Link>
                            <Link href="/register" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                                Daftar
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Konten Halaman (Welcome / JobApply) akan masuk ke sini -->
        <main class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
            <slot />
        </main>
    </div>
</template>
