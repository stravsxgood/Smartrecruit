The project employs a modern, dual-stack build system orchestrated by **Composer** (PHP) and **Vite** (JavaScript/TypeScript), with automated quality gates enforced via **GitHub Actions**.

### Core Build Tools
- **Backend**: Managed by `composer.json`. Key scripts include `setup` for initial environment provisioning, `dev` for concurrent local development (server, queue, Vite), and `test`/`ci:check` for validation pipelines.
- **Frontend**: Managed by `package.json` and `vite.config.ts`. The frontend is built using Vite with plugins for Laravel, Inertia.js, Vue 3, Tailwind CSS, and Wayfinder (type-safe routing).
- **Testing**: Uses **Pest** for PHP feature/unit tests and **ESLint/Prettier/vue-tsc** for frontend linting, formatting, and type checking.

### CI/CD Architecture (GitHub Actions)
The repository uses two primary workflows triggered on push/PR to `main`, `develop`, or `master`:
1. **`tests.yml`**: 
   - Runs on `ubuntu-latest` across a matrix of PHP versions (`8.3`, `8.4`, `8.5`).
   - Installs Node 22 and PHP dependencies.
   - Generates an application key and builds frontend assets (`npm run build`).
   - Executes static analysis (`composer types:check` via PHPStan/Larastan) and the full test suite (`php artisan test`).
2. **`lint.yml`**: 
   - Focuses on code style enforcement.
   - Runs `composer lint` (Pint) for PHP and `npm run format`/`npm run lint` for the frontend.
   - Currently configured to check styles but has commented-out steps for auto-committing fixes.

### Development Conventions
- **Local Development**: Developers should use `composer run dev` to start the server, queue listener, and Vite dev server concurrently.
- **Environment Setup**: The `composer setup` script automates `.env` creation, key generation, database migration, and asset installation.
- **Type Safety**: The build process enforces strict type checking on both backend (via Larastan) and frontend (via `vue-tsc`), ensuring contract consistency between Laravel controllers and Vue components.