## Overview

This Laravel Inertia.js recruitment platform uses a **dual-package-manager architecture** to manage dependencies across PHP (backend) and JavaScript/TypeScript (frontend) ecosystems. Both ecosystems employ strict lockfile-based version pinning with automated update workflows via GitHub Dependabot.

---

## Backend: Composer (PHP)

### Package Manager & Lockfile Strategy
- **Manager**: [Composer](https://getcomposer.org/) v2.x
- **Manifest**: `composer.json` — declares direct dependencies with semantic version constraints
- **Lockfile**: `composer.lock` (406 KB, ~11,280 lines) — pins exact versions of all transitive dependencies for reproducible builds
- **Vendor directory**: `vendor/` (56 packages installed) — contains all resolved PHP dependencies

### Version Constraints
Production dependencies use caret (`^`) ranges:
- `php`: `^8.3` (minimum PHP 8.3)
- `laravel/framework`: `^13.7`
- `inertiajs/inertia-laravel`: `^3.0`
- `laravel/fortify`: `^1.37.2`
- `laravel/wayfinder`: `^0.1.14`
- `laravel/chisel`: `^0.1.0`

Dev dependencies similarly use caret ranges:
- `pestphp/pest`: `^4.7`
- `larastan/larastan`: `^3.9`
- `laravel/pint`: `^1.27`
- `laravel/sail`: `^1.53`

### Configuration Policies
```json
"config": {
    "optimize-autoloader": true,
    "preferred-install": "dist",
    "sort-packages": true,
    "allow-plugins": {
        "pestphp/pest-plugin": true,
        "php-http/discovery": true
    }
}
```
- **Autoloader optimization**: Enabled for production performance
- **Install preference**: `dist` (zip archives) over source clones for faster installs
- **Plugin allowlist**: Only `pestphp/pest-plugin` and `php-http/discovery` are permitted; all other Composer plugins are blocked by default
- **Stability policy**: `minimum-stability: stable`, `prefer-stable: true` — only stable releases are accepted

### Lifecycle Scripts
Key Composer scripts automate dependency-related workflows:
- `post-autoload-dump`: Runs `package:discover` to auto-register Laravel service providers
- `post-update-cmd`: Publishes Laravel assets and runs `boost:update` for Laravel Boost tooling sync
- `setup`: Full project bootstrap including `composer install`, `.env` creation, key generation, migrations, `npm install`, and frontend build
- `dev`: Concurrently starts PHP server, queue worker, and Vite dev server

---

## Frontend: npm (JavaScript/TypeScript)

### Package Manager & Lockfile Strategy
- **Primary manager**: npm (evidenced by `package-lock.json`, 284 KB, ~7,078 lines, lockfileVersion 3)
- **Manifest**: `package.json` — declares direct dependencies with semantic version constraints
- **Lockfile**: `package-lock.json` — pins exact versions of all transitive dependencies
- **Node modules**: `node_modules/` (316 packages installed)
- **pnpm workspace config**: `pnpm-workspace.yaml` exists but defines only the root package (`.`), suggesting pnpm compatibility or future monorepo intent rather than active multi-package usage

### Version Constraints
Production dependencies:
- `vue`: `^3.5.13`
- `@inertiajs/vue3`: `^3.0.0`
- `@inertiajs/vite`: `^3.0.0`
- `tailwindcss`: `^4.1.1`
- `@laravel/passkeys`: `^0.2.0`
- `reka-ui`: `^2.9.8`

Dev dependencies:
- `vite`: `^8.0.0`
- `typescript`: `^5.2.2`
- `eslint`: `^9.17.0`
- `prettier`: `^3.4.2`

### Optional Platform-Specific Dependencies
The project declares optional native binary dependencies for cross-platform build performance:
- `@rollup/rollup-linux-x64-gnu` and `@rollup/rollup-win32-x64-msvc` (pinned to `4.9.5`)
- `@tailwindcss/oxide-linux-x64-gnu` and `@tailwindcss/oxide-win32-x64-msvc` (`^4.0.1`)
- `lightningcss-linux-x64-gnu` and `lightningcss-win32-x64-msvc` (`^1.29.1`)

These enable optimal CSS processing and bundling performance on Linux and Windows without requiring all developers to install every platform's binaries.

### npm Configuration
`.npmrc` contains:
```
ignore-scripts=true
```
This disables automatic execution of `preinstall`, `install`, and `postinstall` scripts defined in dependency `package.json` files, improving security by preventing arbitrary code execution during dependency installation.

### pnpm Workspace Configuration
`pnpm-workspace.yaml` defines:
```yaml
packages:
  - '.'
publicHoistPattern:
  - '@inertiajs/core'
```
- Single-package workspace (root only)
- `@inertiajs/core` is hoisted to the workspace root to avoid duplication issues common with Inertia.js shared core dependencies

---

## Automated Dependency Updates

### GitHub Dependabot
`.github/dependabot.yml` configures automated updates for:
- **Ecosystem**: `github-actions` (CI/CD workflow dependencies)
- **Schedule**: Weekly checks
- **Cooldown**: 5 days between PRs for the same dependency
- **Grouping**: All GitHub Actions updates are grouped into a single PR (`github-actions` group with pattern `*`)

**Notable gap**: Dependabot does not currently monitor `composer` or `npm` ecosystems. PHP and JavaScript dependency updates must be triggered manually via `composer update` / `npm update` or through alternative automation not captured in this repository.

---

## AI Agent Skill Dependencies

### skills-lock.json
The project uses a skill-management system (likely for AI coding assistants like Claude Code or similar) with locked skill definitions:
- `find-skills`: sourced from `vercel-labs/skills` (GitHub)
- `frontend-design`: sourced from `anthropics/skills` (GitHub)

Each skill entry includes a `computedHash` for integrity verification, ensuring that skill definitions haven't been tampered with between installations.

### boost.json
Laravel Boost configuration references six development skills:
- `fortify-development`, `laravel-best-practices`, `wayfinder-development`, `pest-testing`, `inertia-vue-development`, `tailwindcss-development`

These skills live in `.agents/skills/` and provide AI-assisted development guidance but are not traditional software dependencies.

---

## Developer Conventions & Rules

1. **Always commit lockfiles**: Both `composer.lock` and `package-lock.json` must be committed to ensure deterministic builds across environments.

2. **Use Composer scripts for lifecycle operations**: Run `composer setup` for fresh installs, `composer dev` for local development, and `composer test` for CI validation.

3. **Respect plugin allowlist**: Do not add new Composer plugins without explicitly allowing them in `composer.json`'s `allow-plugins` configuration.

4. **npm script security**: The `ignore-scripts=true` setting in `.npmrc` is intentional for security. Do not override it unless you explicitly trust a dependency's install scripts.

5. **Version constraint discipline**: Use caret (`^`) ranges for all dependencies to allow compatible updates while preventing breaking changes. Avoid tilde (`~`) or exact pinning unless there is a specific compatibility issue.

6. **Platform-optional dependencies**: The optional native binary dependencies should remain as-is; they improve build performance on supported platforms without blocking installation on others.

7. **Manual update workflow**: Since Dependabot doesn't monitor Composer or npm, developers should periodically run `composer update` and `npm update` to check for available upgrades, then review changelogs before committing updated lockfiles.

8. **pnpm workspace awareness**: While `pnpm-workspace.yaml` exists, the project currently operates as a single-package workspace. If expanding to a monorepo, ensure `publicHoistPattern` is updated to prevent Inertia.js core duplication.