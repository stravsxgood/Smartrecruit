<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import {
    index as confirmOptions,
    store as confirmStore,
} from '@/actions/Laravel/Passkeys/Http/Controllers/PasskeyConfirmationController';
import InputError from '@/components/InputError.vue';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';

defineOptions({
    layout: {
        title: 'Confirm password',
        description:
            'This is a secure area of the application. Please confirm your password before continuing.',
    },
});
</script>

<template>
    <Head title="Confirm password" />

    <PasskeyVerify
        :routes="{
            options: confirmOptions(),
            submit: confirmStore(),
        }"
        label="Confirm with passkey"
        loading-label="Confirming..."
        separator="Or confirm with password"
    />

    <Form
        v-bind="store.form()"
        reset-on-success
        v-slot="{ errors, processing }"
    >
        <div class="space-y-5">
            <div class="grid gap-2">
                <Label
                    htmlFor="password"
                    class="text-[14px] font-medium text-frost"
                    >Password</Label
                >
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    autofocus
                    placeholder="Enter your password"
                    class="h-11 rounded-lg border-slate/40 bg-graphite/50 text-[14px] text-bone placeholder:text-mist/50 focus-visible:border-amethyst focus-visible:ring-2 focus-visible:ring-amethyst/20"
                />

                <InputError :message="errors.password" />
            </div>

            <Button
                class="h-11 w-full rounded-full bg-paper-white text-[14px] font-medium text-carbon shadow-[rgba(0,0,0,0.2)_0px_18px_20px_0px] transition-colors hover:bg-bone"
                :disabled="processing"
                data-test="confirm-password-button"
            >
                <Spinner v-if="processing" />
                Confirm password
            </Button>
        </div>
    </Form>
</template>
