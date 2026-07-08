<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Log in to your account',
        description: 'Enter your email and password below to log in',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <div
        v-if="status"
        class="mb-4 rounded-lg bg-graphite/60 px-4 py-3 text-center text-sm font-medium text-amethyst"
    >
        {{ status }}
    </div>

    <PasskeyVerify />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-5">
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
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="h-11 rounded-lg border-slate/40 bg-graphite/50 text-[14px] text-bone placeholder:text-mist/50 focus-visible:border-amethyst focus-visible:ring-2 focus-visible:ring-amethyst/20"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label
                        for="password"
                        class="text-[14px] font-medium text-frost"
                        >Password</Label
                    >
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-[13px] text-pearl hover:text-amethyst"
                        :tabindex="5"
                    >
                        Forgot password?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Enter your password"
                    class="h-11 rounded-lg border-slate/40 bg-graphite/50 text-[14px] text-bone placeholder:text-mist/50 focus-visible:border-amethyst focus-visible:ring-2 focus-visible:ring-amethyst/20"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center">
                <Label
                    for="remember"
                    class="flex cursor-pointer items-center gap-2.5"
                >
                    <Checkbox
                        id="remember"
                        name="remember"
                        :tabindex="3"
                        class="rounded-md border-slate/40 bg-graphite/50 data-[state=checked]:bg-amethyst data-[state=checked]:border-amethyst"
                    />
                    <span class="text-[14px] text-frost">Remember me</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-2 h-11 w-full rounded-full bg-paper-white text-[14px] font-medium text-carbon shadow-[rgba(0,0,0,0.2)_0px_18px_20px_0px] transition-colors hover:bg-bone"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Log in
            </Button>
        </div>

        <div
            class="border-t border-graphite/50 pt-5 text-center text-[14px] text-mist"
        >
            Don't have an account?
            <TextLink
                :href="register()"
                class="font-medium text-bone hover:text-amethyst"
                :tabindex="5"
                >Sign up</TextLink
            >
        </div>
    </Form>
</template>
