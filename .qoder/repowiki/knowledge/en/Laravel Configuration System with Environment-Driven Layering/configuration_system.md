## Overview

This Laravel application uses the standard **Laravel configuration system** — a PHP array-based approach that layers environment variables (`.env`) over sensible defaults defined in `config/*.php` files. Configuration is bootstrapped through `bootstrap/app.php` using Laravel's modern `Application::configure()` API, with service providers registered via `bootstrap/providers.php`.

## Architecture and Approach

### Configuration Loading Pattern

The system follows Laravel's conventional two-layer configuration model:

1. **Environment Variables Layer** (`.env` file) — Runtime-specific secrets, URLs, database credentials, feature toggles
2. **Configuration Files Layer** (`config/*.php`) — Structured defaults with `env()` helper calls providing fallback values

All config files use the pattern `env('KEY', 'default')`, ensuring the application remains functional even without a `.env` file present (critical for testing and CI environments).

### Bootstrap Flow

1. `bootstrap/app.php` creates the application instance via `Application::configure(basePath: dirname(__DIR__))`
2. Service providers are loaded from `bootstrap/providers.php` (returns an array of provider classes)
3. Middleware pipeline is configured inline in `bootstrap/app.php`
4. Route files are registered (`routes/web.php`, `routes/console.php`)
5. Exception handling behavior is customized

### Key Design Decisions

- **Immutable dates**: `AppServiceProvider` enforces `CarbonImmutable` globally via `Date::use(CarbonImmutable::class)`
- **Production safety**: Destructive database commands are prohibited in production (`DB::prohibitDestructiveCommands(app()->isProduction())`)
- **Password policy**: Dynamic password rules based on environment — strict requirements in production, relaxed in development
- **Cookie encryption exceptions**: `appearance` and `sidebar_state` cookies are excluded from encryption to allow client-side JavaScript access (configured in `bootstrap/app.php`)

## Key Configuration Files

### Core Application Config (`config/app.php`)
Defines application identity, environment detection, debug mode, URL, locale settings, encryption key, and maintenance mode driver. All values pull from environment variables with sensible defaults.

### Inertia.js Config (`config/inertia.php`)
Configures server-side rendering (SSR enabled by default at `http://127.0.0.1:13714`), page component discovery paths (`resources/js/pages`), supported file extensions, and testing assertions.

### Fortify Auth Config (`config/fortify.php`)
Controls authentication features including registration, password reset, email verification, two-factor authentication, and passkeys (WebAuthn). Passkey relying party ID is dynamically derived from `app.url`.

### Database Config (`config/database.php`)
Supports multiple database drivers (SQLite default, MySQL, MariaDB, PostgreSQL, SQL Server). Redis connections configured for caching with retry/backoff strategies.

### Session Config (`config/session.php`)
Database-backed sessions by default, JSON serialization (secure against gadget chain attacks), configurable cookie security settings.

### Services Config (`config/services.php`)
Centralized third-party API credentials (Postmark, Resend, AWS SES, Slack notifications).

## Environment Variable Management

### `.env.example` as Template
The repository provides `.env.example` documenting all required environment variables:
- Application settings (`APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`)
- Database configuration (`DB_CONNECTION=sqlite` by default)
- Session/cache/queue drivers (all set to `database` for simplicity)
- Mail configuration (defaults to `log` driver for local development)
- AWS credentials for S3/file storage
- Vite integration (`VITE_APP_NAME` passed to frontend)

### Security Considerations
- `APP_KEY` must be generated via `php artisan key:generate` (required for encryption)
- `PASSKEYS_USER_HANDLE_SECRET` falls back to `APP_KEY` if not explicitly set
- Session cookies use `httponly` and `same_site=lax` by default
- Encryption cipher is fixed at `AES-256-CBC`

## Frontend Build Configuration

### Vite Config (`vite.config.ts`)
Integrates multiple plugins:
- `laravel-vite-plugin` — Asset compilation with hot module replacement
- `@inertiajs/vite` — Inertia.js SSR support
- `@laravel/vite-plugin-wayfinder` — Type-safe route generation with form variants
- `@tailwindcss/vite` — Tailwind CSS processing
- `@vitejs/plugin-vue` — Vue 3 SFC compilation
- Font optimization via `laravel-vite-plugin/fonts` (Bunny CDN for Instrument Sans)

## Developer Conventions

1. **Never commit `.env`** — Use `.env.example` as the template; each developer/deployment generates their own `.env`
2. **Use `env()` only in config files** — Never call `env()` directly in application code (it returns `null` when config is cached)
3. **Access config via `config()` helper** — Use `config('app.name')` syntax throughout the application
4. **Add new config keys to `.env.example`** — Any new `env()` call must be documented in the example file
5. **Sensitive values belong in `.env`** — API keys, passwords, and secrets must never appear in `config/*.php` files
6. **Config caching in production** — Run `php artisan config:cache` during deployment (makes `env()` calls return `null` outside config files)
7. **Feature flags via config** — Fortify features are enabled/disabled through the `features` array in `config/fortify.php`
8. **Environment-specific behavior** — Use `app()->isProduction()` or `app()->environment()` for conditional logic rather than checking env vars directly