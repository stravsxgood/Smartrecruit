<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Forgot password',
        description: 'Enter your email to receive a password reset link',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Forgot password" />

    <div
        v-if="status"
        class="mb-4 rounded-lg bg-graphite/60 px-4 py-3 text-center text-sm font-medium text-amethyst"
    >
        {{ status }}
    </div>

    <div class="space-y-5">
        <Form v-bind="email.form()" v-slot="{ errors, processing }">
            <div class="grid gap-2">
                <Label
                    for="email"
                    class="text-[14px] font-medium text-frost"
                    >Email address</Label
                >
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="off"
                    autofocus
                    placeholder="email@example.com"
                    class="h-11 rounded-lg border-slate/40 bg-graphite/50 text-[14px] text-bone placeholder:text-mist/50 focus-visible:border-amethyst focus-visible:ring-2 focus-visible:ring-amethyst/20"
                />
                <InputError :message="errors.email" />
            </div>

            <Button
                class="mt-6 h-11 w-full rounded-full bg-paper-white text-[14px] font-medium text-carbon shadow-[rgba(0,0,0,0.2)_0px_18px_20px_0px] transition-colors hover:bg-bone"
                :disabled="processing"
                data-test="email-password-reset-link-button"
            >
                <Spinner v-if="processing" />
                Send reset link
            </Button>
        </Form>

        <div
            class="border-t border-graphite/50 pt-5 text-center text-[14px] text-mist"
        >
            <span>Or, return to</span>
            <TextLink
                :href="login()"
                class="font-medium text-bone hover:text-amethyst"
                >log in</TextLink
            >
        </div>
    </div>
</template>
