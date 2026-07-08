import type { ComputedRef, Ref } from 'vue';
import { computed, onMounted, ref } from 'vue';
import type { Appearance, ResolvedAppearance } from '@/types';

export type { Appearance, ResolvedAppearance };

export type UseAppearanceReturn = {
    appearance: Ref<Appearance>;
    resolvedAppearance: ComputedRef<ResolvedAppearance>;
    updateAppearance: (value: Appearance) => void;
};

const appearance = ref<Appearance>('system');
const systemIsDark = ref(false);

const STORAGE_KEY = 'appearance';

const isValidAppearance = (value: string | null): value is Appearance => {
    return value === 'light' || value === 'dark' || value === 'system';
};

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;

    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const mediaQuery = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.matchMedia('(prefers-color-scheme: dark)');
};

const getStoredAppearance = (): Appearance | null => {
    if (typeof window === 'undefined') {
        return null;
    }

    const storedAppearance = localStorage.getItem(STORAGE_KEY);

    return isValidAppearance(storedAppearance) ? storedAppearance : null;
};

const getResolvedAppearance = (value: Appearance): ResolvedAppearance => {
    if (value === 'system') {
        return mediaQuery()?.matches ? 'dark' : 'light';
    }

    return value;
};

export function updateTheme(value: Appearance): void {
    if (typeof window === 'undefined') {
        return;
    }

    const resolvedTheme = getResolvedAppearance(value);

    document.documentElement.classList.toggle('dark', resolvedTheme === 'dark');
    document.documentElement.style.colorScheme = resolvedTheme;
}

const handleSystemThemeChange = () => {
    systemIsDark.value = mediaQuery()?.matches ?? false;

    if (appearance.value === 'system') {
        updateTheme('system');
    }
};

export function initializeTheme(): void {
    if (typeof window === 'undefined') {
        return;
    }

    const savedAppearance = getStoredAppearance() ?? 'system';

    appearance.value = savedAppearance;
    systemIsDark.value = mediaQuery()?.matches ?? false;

    updateTheme(savedAppearance);

    mediaQuery()?.addEventListener('change', handleSystemThemeChange);
}

export function useAppearance(): UseAppearanceReturn {
    onMounted(() => {
        const savedAppearance = getStoredAppearance() ?? 'system';

        appearance.value = savedAppearance;
        systemIsDark.value = mediaQuery()?.matches ?? false;

        updateTheme(savedAppearance);
    });

    const resolvedAppearance = computed<ResolvedAppearance>(() => {
        if (appearance.value === 'system') {
            return systemIsDark.value ? 'dark' : 'light';
        }

        return appearance.value;
    });

    function updateAppearance(value: Appearance) {
        appearance.value = value;

        localStorage.setItem(STORAGE_KEY, value);
        setCookie(STORAGE_KEY, value);

        updateTheme(value);
    }

    return {
        appearance,
        resolvedAppearance,
        updateAppearance,
    };
}
