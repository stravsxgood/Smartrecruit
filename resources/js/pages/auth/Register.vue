<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    },
});
</script>

<template>
    <Head title="Register" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-5">
            <div class="grid gap-2">
                <Label
                    for="name"
                    class="text-[14px] font-medium text-frost"
                    >Full name</Label
                >
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Your full name"
                    class="h-11 rounded-lg border-slate/40 bg-graphite/50 text-[14px] text-bone placeholder:text-mist/50 focus-visible:border-amethyst focus-visible:ring-2 focus-visible:ring-amethyst/20"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label
                    for="email"
                    class="text-[14px] font-medium text-frost"
                    >Email address</Label
                >
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="email@example.com"
                    class="h-11 rounded-lg border-slate/40 bg-graphite/50 text-[14px] text-bone placeholder:text-mist/50 focus-visible:border-amethyst focus-visible:ring-2 focus-visible:ring-amethyst/20"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label
                    for="password"
                    class="text-[14px] font-medium text-frost"
                    >Password</Label
                >
                <PasswordInput
                    id="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Create a password"
                    :passwordrules="passwordRules"
                    class="h-11 rounded-lg border-slate/40 bg-graphite/50 text-[14px] text-bone placeholder:text-mist/50 focus-visible:border-amethyst focus-visible:ring-2 focus-visible:ring-amethyst/20"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label
                    for="password_confirmation"
                    class="text-[14px] font-medium text-frost"
                    >Confirm password</Label
                >
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Confirm your password"
                    :passwordrules="passwordRules"
                    class="h-11 rounded-lg border-slate/40 bg-graphite/50 text-[14px] text-bone placeholder:text-mist/50 focus-visible:border-amethyst focus-visible:ring-2 focus-visible:ring-amethyst/20"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 h-11 w-full rounded-full bg-paper-white text-[14px] font-medium text-carbon shadow-[rgba(0,0,0,0.2)_0px_18px_20px_0px] transition-colors hover:bg-bone"
                tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" />
                Create account
            </Button>
        </div>

        <div
            class="border-t border-graphite/50 pt-5 text-center text-[14px] text-mist"
        >
            Already have an account?
            <TextLink
                :href="login()"
                class="font-medium text-bone hover:text-amethyst"
                :tabindex="6"
                >Log in</TextLink
            >
        </div>
    </Form>
</template>
