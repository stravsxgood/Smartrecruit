# Frontend Styling Architecture

## System Overview

The application uses **Tailwind CSS v4** as its primary styling framework, integrated with the **shadcn-vue** component library (New York v4 style) to provide a consistent, accessible UI component system. The styling architecture is built on CSS custom properties (design tokens) with full dark mode support via class-based theming.

## Core Technologies

- **CSS Framework**: Tailwind CSS v4 (`tailwindcss@^4.1.1`) with Vite plugin (`@tailwindcss/vite`)
- **Component Library**: shadcn-vue (style: `new-york-v4`) using reka-ui primitives
- **Animation**: `tw-animate-css` for transition utilities
- **Icon Library**: Lucide icons (`@lucide/vue`)
- **Utility Functions**: `class-variance-authority` (CVA) for variant composition, `clsx` + `tailwind-merge` for className merging
- **Font Loading**: Bunny font loader via `laravel-vite-plugin/fonts`

## Design Token System

Design tokens are defined as CSS custom properties in `resources/css/app.css` using Tailwind's `@theme` directive. Two token layers exist:

### Semantic Color Tokens (HSL-based)
All colors use HSL notation for easy manipulation. Light and dark themes are defined via `:root` and `.dark` selectors:

- **Core palette**: `--background`, `--foreground`, `--card`, `--popover`, `--primary`, `--secondary`, `--muted`, `--accent`, `--destructive`
- **UI elements**: `--border`, `--input`, `--ring`
- **Data visualization**: `--chart-1` through `--chart-5`
- **Sidebar-specific**: `--sidebar-background`, `--sidebar-foreground`, `--sidebar-primary`, `--sidebar-accent`, `--sidebar-border`, `--sidebar-ring`

### Custom Brand Tokens
A secondary `@theme` block defines named brand colors and typography scale:

- **Brand colors**: `--color-paper-white`, `--color-cool-mist`, `--color-stone-edge`, `--color-graphite-mute`, `--color-charcoal-whisper`, `--color-midnight-ink`, `--color-onyx-button`, `--color-tinted-shadow`, `--color-invoice-blue`, `--color-magenta-tile`, `--color-iris-glow`, plus wash variants
- **Typography fonts**: `--font-open-runde`, `--font-caveat`, `--font-sans` (Instrument Sans)
- **Typography scale**: Caption (12px), Body-sm (14px), Body (16px), Subheading (20px), Heading (32px), Heading-lg (40px), Display (64px) — each with corresponding line-height and tracking values
- **Spacing scale**: 4px through 100px in defined increments
- **Border radius**: `--radius-xl` (12px) through `--radius-full-2` (1250px)
- **Shadows**: `--shadow-subtle`, `--shadow-subtle-2`

### Radius System
Base radius is configurable via `--radius` (default: 0.5rem / 8px). Derived radii:
- `--radius-lg`: var(--radius)
- `--radius-md`: calc(var(--radius) - 2px)
- `--radius-sm`: calc(var(--radius) - 4px)

## Dark Mode Strategy

Dark mode is implemented via a **class-based toggle** on `<html>` element:

1. The `.dark` class is applied/removed on `document.documentElement`
2. A custom variant `@custom-variant dark (&:is(.dark *))` enables scoped dark styles
3. Theme switching is managed by the `useAppearance()` composable which:
   - Persists preference to `localStorage` and a cookie (for SSR)
   - Supports three modes: `'light'`, `'dark'`, `'system'`
   - Listens to `prefers-color-scheme` media query changes when in system mode
4. The `HandleAppearance` middleware reads the cookie server-side for initial render consistency

## Component Styling Patterns

### shadcn-vue Component Structure
Each UI component follows a consistent pattern:

```
components/ui/{component}/
├── {Component}.vue      # Main component with props interface
└── index.ts             # Exports component + CVA variants
```

### Variant Composition with CVA
Components use `class-variance-authority` to define typed variant systems. Example from Button:

```typescript
export const buttonVariants = cva(
  "base classes...",
  {
    variants: {
      variant: { default, destructive, outline, secondary, ghost, link },
      size: { default, sm, lg, icon, icon-sm, icon-lg }
    },
    defaultVariants: { variant: "default", size: "default" }
  }
)
```

Components consume variants via data attributes (`:data-variant`, `:data-size`) enabling CSS targeting if needed.

### ClassName Merging Utility
The `cn()` function in `@/lib/utils.ts` combines `clsx` (conditional classes) with `tailwind-merge` (conflict resolution):

```typescript
export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs))
}
```

This is used throughout components to merge variant classes with user-provided `class` props.

## Layout and Responsive Strategy

### App Layout Variants
Two layout patterns are supported via `AppVariant` type:
- `'header'`: Top navigation layout (`AppHeaderLayout.vue`)
- `'sidebar'`: Collapsible sidebar layout (`AppSidebarLayout.vue`)

The sidebar uses shadcn's `Sidebar` component with `collapsible="icon"` and `variant="inset"` props for responsive behavior.

### Base Layer Resets
Tailwind's `@layer base` applies global defaults:
- All elements get `border-border` and `outline-ring/50` for consistent focus states
- `body` and `html` get `bg-background text-foreground`
- Border color fallback to `gray-200` for Tailwind v4 compatibility

## Build Integration

Vite configuration (`vite.config.ts`) integrates:
- `@tailwindcss/vite` plugin for CSS processing
- `@inertiajs/vite` for SPA asset handling
- `laravel-vite-plugin` with font preloading (Instrument Sans weights 400, 500, 600)
- Source scanning includes Laravel pagination views and compiled Blade templates for class detection

## Developer Conventions

1. **Always use `cn()`** when combining conditional classes with Tailwind utilities
2. **Use semantic color tokens** (`bg-background`, `text-foreground`, etc.) rather than hardcoded colors
3. **Define new component variants** using CVA in the component's `index.ts`
4. **Support dark mode** by referencing CSS variables that switch between `:root` and `.dark` contexts
5. **Use Lucide icons** consistently via `@lucide/vue` imports
6. **Follow shadcn-vue structure** for new UI components (separate `.vue` and `index.ts`)
7. **Persist appearance changes** through the `useAppearance()` composable, not direct DOM manipulation
8. **Use the typography scale** (`text-caption`, `text-body`, `text-heading`, etc.) rather than arbitrary sizes