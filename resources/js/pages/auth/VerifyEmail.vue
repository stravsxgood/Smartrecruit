<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'Email verification',
        description:
            'Please verify your email address by clicking on the link we just emailed to you.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Email verification" />

    <div
        v-if="status === 'verification-link-sent'"
        class="mb-4 rounded-lg bg-graphite/60 px-4 py-3 text-center text-sm font-medium text-amethyst"
    >
        A new verification link has been sent to the email address you provided
        during registration.
    </div>

    <Form
        v-bind="send.form()"
        class="flex flex-col items-center gap-5"
        v-slot="{ processing }"
    >
        <Button
            :disabled="processing"
            class="h-11 w-full rounded-full bg-paper-white text-[14px] font-medium text-carbon shadow-[rgba(0,0,0,0.2)_0px_18px_20px_0px] transition-colors hover:bg-bone"
        >
            <Spinner v-if="processing" />
            Resend verification email
        </Button>

        <div class="border-t border-graphite/50 pt-4 text-center w-full">
            <TextLink
                :href="logout()"
                as="button"
                class="text-[14px] font-medium text-mist hover:text-bone"
            >
                Log out
            </TextLink>
        </div>
    </Form>
</template>
