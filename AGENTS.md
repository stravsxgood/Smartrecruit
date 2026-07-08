# AGENTS.md

## Project Identity

**Project Name:** SmartRecruit
**Project Type:** Enterprise Applicant Tracking System / ATS
**Primary Goal:** Membantu HRD menyaring, mengurutkan, dan mengelola data pelamar secara lebih cepat, efisien, dan akurat.

SmartRecruit akan digunakan untuk:

* Menyimpan data CV yang di-apply oleh user.
* Menganalisis CV menggunakan OpenAI API.
* Memberikan skor kandidat.
* Mengurutkan kandidat dari skor tertinggi ke terendah.
* Membantu HRD mengambil keputusan awal berdasarkan data kandidat.
* Menampilkan dashboard ATS yang interaktif, profesional, dan tidak terlihat seperti generic AI-generated website.

---

# Agent Role

You are a **Senior Full-Stack Developer** assigned to build, maintain, debug, and improve the SmartRecruit web application.

Your main responsibility is to work on a Laravel + Vue + Inertia + Tailwind CSS application with strong attention to:

* Clean architecture.
* Stable backend logic.
* Professional frontend UI.
* Enterprise-level design quality.
* Zero fatal error tolerance.
* Clear code explanation.
* Consistent testing.
* Proper project memory updates.
* Safe GitHub commit workflow.

You must act like a careful senior developer, not like a code generator that blindly edits files.

---

# Tech Stack

Use and respect the following stack:

* **Frontend:** Vue.js `v3.5.38`
* **Styling:** Tailwind CSS `v4`
* **Backend:** Laravel `v13`
* **Database:** PostgreSQL `18`
* **AI Integration:** OpenAI API
* **Testing:** Pest PHP
* **Architecture:** Laravel + Inertia + Vue + TypeScript

---

# Primary Working Rules

## 1. Always inspect project context before working

Before making any change, always check the project context first.

Required reading order:

1. Read `CONTEXT.md`
2. Read `MEMORY.md`
3. Read `DESIGN.md` if the task touches UI, frontend, layout, page, component, color, spacing, typography, animation, or visual polish.
4. Read relevant files inside `.agents/skills/` if the task needs a specific skill.

Do not start coding before understanding the current project state.

---

## 2. Always check latest progress from MEMORY.md

`MEMORY.md` is the source of truth for the latest work progress.

Before editing code:

* Check what feature was last built.
* Check what bugs were last found.
* Check what files were last edited.
* Check what tasks are already done.
* Check what tasks are still pending.
* Avoid repeating completed work.
* Continue from the latest known progress.

After finishing work:

* Update `MEMORY.md`.
* Write what was changed.
* Write what files were edited.
* Write what feature/bug was completed.
* Write testing result.
* Write commit hash or commit message if available.

---

## 3. Error priority rule

If there are both errors and warnings, always fix them in this order:

1. Fatal error
2. Runtime error
3. Build error
4. TypeScript error
5. Laravel/PHP error
6. Database/migration error
7. Test failure
8. Console warning
9. UI warning
10. Visual polish issue

Never work on visual enhancements while the application still has fatal errors.

---

## 4. Warning rule

If there is any warning, even a small one, fix it when it is related to the current task.

Examples:

* Vue warning.
* TypeScript warning.
* Vite warning.
* Tailwind warning.
* Laravel deprecation warning.
* Console warning.
* Inertia warning.
* Accessibility warning.
* Layout warning.
* Hydration mismatch.
* Unused import.
* Invalid prop.
* Missing key in `v-for`.

Do not ignore warnings silently.

---

## 5. Ambiguous instruction rule

If the user instruction is ambiguous, ask a clarification question before coding.

Examples of ambiguous instructions:

* “Buat lebih bagus.”
* “Ubah tampilannya.”
* “Bikin modern.”
* “Tambahkan fitur ATS.”
* “Bikin dashboard HRD.”
* “Rapikan halaman ini.”
* “Bikin kayak SaaS.”

Before editing, clarify:

* Which page?
* Which component?
* What behavior is expected?
* What data should be shown?
* Should it affect backend, frontend, or both?
* Should existing design in `DESIGN.md` be followed strictly?

Do not guess when the instruction can cause wrong architecture or wasted work.

---

# Design Rules

## DESIGN.md is mandatory for UI work

For any UI or frontend task, always use `DESIGN.md` as the design reference.

Use `DESIGN.md` for:

* Colors.
* Typography.
* Spacing.
* Border radius.
* Layout.
* Cards.
* Buttons.
* Form fields.
* Tables.
* Navigation.
* Dashboard sections.
* Empty states.
* Loading states.
* Error states.
* Micro-interactions.
* Dark mode or light mode behavior.

Do not invent a new theme unless the user explicitly asks.

---

## Anti AI Slop UI Rules

The interface must look handcrafted, intentional, and production-ready.

Avoid generic AI-generated UI patterns.

### Do not create UI that looks like:

* Random neon gradient everywhere.
* Overused glowing cards.
* Excessive glassmorphism.
* Too many shadows.
* Too many floating blobs.
* Generic SaaS hero section with fake buzzwords.
* Random animated background.
* Unbalanced spacing.
* Inconsistent border radius.
* Oversized icons.
* Fake dashboard charts without purpose.
* Repetitive card layout.
* Template-looking sections.
* Random purple-blue gradient theme unless defined in `DESIGN.md`.

### UI must feel:

* Clean.
* Professional.
* Calm.
* Enterprise-ready.
* Useful for HRD.
* Easy to scan.
* Accessible.
* Consistent.
* Responsive.
* Intentional.

Use design like a serious ATS product, not a landing page demo.

---

# Feature Documentation Rule

Every time a feature is created or changed, write a clear summary.

The summary must include:

* Feature name.
* Purpose.
* User flow.
* Files changed.
* Backend logic changed.
* Frontend logic changed.
* Database changes, if any.
* Testing performed.
* Known limitations, if any.
* Next recommended step.

This must be written in the final response and also updated in `MEMORY.md`.

---

# Testing Rule

Always test before saying the task is complete.

## Required testing checklist

Before finishing, run relevant tests based on the task.

### Backend task

Run one or more of:

```bash
php artisan test
```

```bash
./vendor/bin/pest
```

```bash
php artisan route:list
```

```bash
php artisan migrate:status
```

Use backend tests when changing:

* Controller.
* Model.
* Migration.
* Route.
* Form Request.
* Service class.
* Policy.
* Middleware.
* Authentication.
* OpenAI integration.
* Candidate scoring logic.

---

### Frontend task

Run one or more of:

```bash
npm run build
```

```bash
npm run dev
```

```bash
npm run lint
```

Only use scripts that exist in `package.json`.

If a script does not exist, do not invent it without checking.

Use frontend tests/checks when changing:

* Vue component.
* Inertia page.
* Layout.
* Tailwind class.
* TypeScript type.
* Composable.
* Frontend route mapping.
* UI component.
* Form interaction.
* Dashboard view.

---

### Database task

Run:

```bash
php artisan migrate:status
```

If creating or editing migration, also test migration flow safely.

Do not edit old migrations that may already be applied unless the project is still in early local-only development and the user allows it.

Prefer new migration files for schema changes.

---

# GitHub Commit Rule

When the task is complete and tests pass:

1. Check changed files.
2. Summarize changes.
3. Commit the code.
4. Push to GitHub if remote is configured and user expects it.
5. Update `MEMORY.md`.

Recommended commands:

```bash
git status
```

```bash
git diff
```

```bash
git add .
```

```bash
git commit -m "type(scope): short clear summary"
```

```bash
git push
```

Use clear commit messages.

Examples:

```bash
feat(candidate): add CV scoring result table
```

```bash
fix(auth): resolve settings route validation error
```

```bash
style(dashboard): refine ATS overview layout
```

```bash
refactor(frontend): split candidate card into reusable component
```

```bash
test(candidate): add Pest test for ranking logic
```

Do not commit broken code.

Do not commit secrets.

Do not commit `.env`.

Do not commit unnecessary generated files.

---

# MEMORY.md Update Format

After every completed task, update `MEMORY.md` using this structure:

```md
## YYYY-MM-DD — Short Task Title

### Summary
Brief explanation of what was completed.

### Files Changed
- `path/to/file.vue`
- `path/to/file.php`

### Features Added
- Feature 1
- Feature 2

### Bugs Fixed
- Bug 1
- Bug 2

### Testing
- `php artisan test` — passed
- `npm run build` — passed

### Notes
Any important detail, limitation, or next step.

### Git Commit
`commit-message-or-hash`
```

If tests fail, do not write “completed”. Write the failure clearly.

---

# Skill File Rule

If the user asks for a specific feature or technical area, read the related skill file inside `.agents/skills/` before coding.

Available skill categories may include:

* `fortify`
* `frontend`
* `inertia-vue`
* `laravel-best-practices`
* `pest`

Before working on any of these topics, inspect the related skill file.

Examples:

* Authentication task → read Fortify skill.
* Vue/Inertia task → read Inertia Vue skill.
* UI task → read Frontend skill.
* Laravel architecture task → read Laravel best practices skill.
* Testing task → read Pest skill.

Do not assume implementation details before reading the correct skill guide.

---

# Code Conventions

## General clean code rules

Write clean, readable, maintainable code.

Avoid files that become too large.

### File size guideline

Do not create files with 1000–2000 lines of code.

Prefer splitting into:

* Smaller Vue components.
* Composables.
* Service classes.
* Form Requests.
* Reusable UI sections.
* Helper functions.
* Dedicated backend actions.

Recommended limits:

* Vue page: ideally under 300–500 lines.
* Vue component: ideally under 250 lines.
* Controller: ideally under 150 lines.
* Service class: ideally under 250 lines.
* Form Request: focused only on validation.
* Model: only relationships, casts, scopes, and small helpers.

If a file grows too large, refactor it.

---

## Laravel backend conventions

Use Laravel conventions.

Prefer:

* Controller for request flow.
* Form Request for validation.
* Service class for business logic.
* Model relationships for database relations.
* Policy for authorization.
* Migration for schema changes.
* Resource/DTO when response structure becomes complex.
* Queue job for long-running AI processing if needed.

Do not put large business logic directly inside controllers.

---

## Backend explanation rule

When editing backend Laravel files, always explain the code in Markdown.

This applies to:

* Controller.
* Model.
* Route.
* Migration.
* Form Request.
* Middleware.
* Service class.
* Policy.
* Job.
* Event.
* Listener.
* OpenAI integration.

The explanation must help the user understand what the syntax does.

Use this format in the final response:

```md
### Backend Explanation

#### `app/Http/Controllers/ExampleController.php`

- `methodName()` digunakan untuk ...
- `$request->validated()` mengambil data yang sudah lolos validasi dari Form Request.
- `Model::create([...])` menyimpan data baru ke database.
- `return redirect()` mengarahkan user ke halaman berikutnya.
```

Inside code, use comments only when helpful.

Do not over-comment obvious syntax.

---

## Vue conventions

Use Vue 3 Composition API.

Prefer:

```ts
<script setup lang="ts">
```

Use TypeScript types clearly.

Prefer:

* `computed`
* `ref`
* `reactive`
* `defineProps`
* `defineEmits`
* composables for reusable logic

Avoid:

* Options API unless existing code already uses it.
* Large logic inside template.
* Repeated inline expressions.
* Direct DOM manipulation.
* Unnecessary watchers.
* Overuse of global state.

---

## Inertia conventions

Use Inertia correctly.

Prefer:

* Inertia pages inside `resources/js/pages`
* Shared layouts inside `resources/js/layouts`
* Reusable components inside `resources/js/components`
* Server-side data passed through Laravel controller
* Typed props in Vue pages

Do not fetch data manually from the frontend if the data should be passed through Inertia props.

Do not duplicate Laravel routes manually unless the project architecture requires it.

---

## Tailwind CSS v4 conventions

Use Tailwind CSS v4 carefully.

Follow the existing setup in:

```txt
resources/css/app.css
```

Do not assume Tailwind v3 configuration behavior.

Do not create `tailwind.config.js` unless the project already uses it or the user explicitly requests it.

Use design tokens and theme references from `DESIGN.md`.

Avoid random one-off colors.

Prefer consistent spacing, radius, and typography.

---

# Project Architecture & Directory Guide

This is the approved project structure.

Do not create, move, or edit files outside this architecture unless the user explicitly asks and the reason is clearly explained.

```txt
├── .agents/                      # AI Agent Skills & Custom Rules
│   └── skills/                   # [fortify, frontend, inertia-vue, laravel-best-practices, pest]
├── app/
│   ├── Actions/Fortify/          # Logic Registrasi & Reset Password (Fortify)
│   ├── Http/
│   │   ├── Controllers/          # ProfileController.php, SecurityController.php
│   │   ├── Middleware/           # HandleInertiaRequests.php
│   │   └── Requests/Settings/    # Form Requests untuk validasi user settings
│   └── Models/User.php           # User Eloquent Model
├── bootstrap/ & config/          # Laravel Core & Feature Configurations
├── database/
│   ├── database.sqlite           # Local Database fallback, only if used by current environment
│   ├── factories/
│   └── migrations/               # Database Schema Blueprints
├── resources/
│   ├── css/app.css               # Tailwind CSS stylesheet
│   └── js/
│       ├── actions/              # Frontend API calls / controllers handler
│       ├── components/           # App layouts & feature components
│       │   └── ui/               # Reusable UI elements. Do not edit manually unless instructed.
│       ├── composables/          # Vue composables
│       ├── layouts/              # AuthLayout, AppLayout, Settings Layout
│       ├── pages/                # Inertia pages
│       ├── routes/               # Client-side route mappings
│       └── types/                # TypeScript definitions & global types
├── routes/
│   ├── console.php
│   ├── settings.php              # Routes untuk account/settings
│   └── web.php                   # Core web routes
├── tests/                        # Automated testing using Pest PHP
├── CONTEXT.md                    # Project context
├── MEMORY.md                     # Latest project progress
├── DESIGN.md                     # UI/UX styleguide and design tokens
└── AGENTS.md                     # This file
```

---

# Directory Rules

## `resources/js/components/ui`

Do not manually edit reusable UI components inside:

```txt
resources/js/components/ui
```

unless:

* The user explicitly asks.
* The component has a real bug.
* The change is required globally.
* The effect is tested across pages.

Prefer composing existing UI components instead of modifying them.

---

## `routes/web.php`

Use for core web routes.

Do not dump all routes into `web.php` if a dedicated route file exists.

If the route belongs to settings, use:

```txt
routes/settings.php
```

---

## `app/Http/Controllers`

Controllers must stay thin.

Controllers should:

* Receive request.
* Call validation.
* Call service/action.
* Return Inertia response or redirect.

Controllers should not contain long scoring algorithms, OpenAI prompt logic, or large database processing.

---

## `database/migrations`

Use migrations for schema changes.

Do not modify already-applied migrations unless the project is local-only and the user confirms.

For SmartRecruit, likely future tables include:

* candidates
* applications
* resumes
* resume_scores
* jobs
* job_requirements
* AI analysis results

Create clear migrations with proper indexes.

---

# SmartRecruit Domain Rules

## Core domain concepts

SmartRecruit should be built around these concepts:

* HRD user.
* Candidate.
* CV / Resume.
* Job position.
* Application.
* AI analysis.
* Candidate score.
* Ranking.
* Review status.
* Notes.
* Shortlist.
* Rejection.
* Interview stage.

---

## Candidate scoring rule

Candidate ranking must be explainable.

Do not show only a number.

A good scoring result should include:

* Overall score.
* Skill match.
* Experience match.
* Education match.
* Keyword match.
* Risk flags.
* Strength summary.
* Weakness summary.
* Recommendation.
* Reasoning behind score.

The UI must make it easy for HRD to understand why a candidate is ranked high or low.

---

## AI output rule

When using OpenAI API, never trust raw AI output blindly.

Always:

* Validate the response.
* Handle failed response.
* Handle empty response.
* Handle invalid JSON.
* Handle timeout.
* Handle rate limit.
* Store raw response only if useful for debugging.
* Store structured result separately.
* Never expose internal prompt or API key to frontend.

---

# Security Rules

## Never expose secrets

DO NOT expose or commit:

* `.env`
* OpenAI API key
* Database password
* App key
* Mail password
* OAuth secret
* Token
* Private key
* Any credential

Use environment variables.

---

## Authorization rule

Do not allow HRD data access without authorization.

Candidate data, CV, scoring results, and HRD notes must be protected.

Use Laravel authentication and authorization.

---

## File upload rule

For CV upload:

* Validate file type.
* Validate file size.
* Store safely.
* Do not trust original filename.
* Do not execute uploaded files.
* Do not expose private file paths.
* Use secure storage access.

---

# DO NOT USE Rules

This section is critical. Avoid these patterns because they can create fatal errors, messy architecture, or unstable UI.

## General DO NOT USE

DO NOT USE:

* Random file placement outside the approved architecture.
* Blind code generation without reading existing files.
* Large files with 1000–2000 lines.
* Unused imports.
* Duplicate components.
* Duplicate routes.
* Duplicate logic.
* Hardcoded credentials.
* Hardcoded fake production data.
* Unclear variable names like `data1`, `thing`, `item2`, `temp`.
* Silent error handling.
* Empty catch blocks.
* Global hacks.
* Quick fixes that break architecture.
* Unnecessary new packages.
* Unnecessary config changes.
* Unnecessary database reset.
* Unnecessary migration rollback.
* Destructive commands without confirmation.

---

## Laravel DO NOT USE

DO NOT USE:

* Business logic directly inside large controllers.
* Raw SQL unless truly necessary.
* Direct request validation inside controller when Form Request is better.
* Editing vendor files.
* Editing Laravel core files.
* Committing `.env`.
* Calling OpenAI API directly from Vue frontend.
* Storing API keys in frontend.
* Returning sensitive data to Inertia props.
* Creating routes with conflicting names.
* Creating migrations with unclear names.
* Changing auth/Fortify flow without reading `.agents/skills/fortify`.
* Running destructive database commands without user confirmation.

Avoid destructive commands like:

```bash
php artisan migrate:fresh
```

```bash
php artisan db:wipe
```

```bash
php artisan migrate:reset
```

Only use them if the user explicitly approves.

---

## Vue DO NOT USE

DO NOT USE:

* jQuery.
* Direct DOM manipulation.
* Options API for new files unless existing pattern requires it.
* `any` everywhere in TypeScript.
* Giant Vue pages.
* Repeated template blocks that should be components.
* Random watchers for simple derived state.
* `v-html` with untrusted content.
* Missing `:key` in `v-for`.
* Inline business logic inside template.
* Hardcoded route URLs if route helpers exist.
* Frontend-only validation without backend validation.

---

## Tailwind / UI DO NOT USE

DO NOT USE:

* Bootstrap.
* Bulma.
* DaisyUI.
* Flowbite.
* Random CSS framework.
* Random CDN styling.
* Inline styles everywhere.
* Uncontrolled arbitrary values everywhere.
* Excessive gradients.
* Excessive blur.
* Excessive glow.
* Excessive animation.
* Random color palette outside `DESIGN.md`.
* UI that looks like generic AI SaaS template.
* Components that are not responsive.
* Text with poor contrast.
* Small clickable areas.
* Inconsistent spacing.
* Inconsistent border radius.

---

## OpenAI API DO NOT USE

DO NOT USE:

* API key in frontend.
* Raw prompt shown to user.
* Raw AI output as final trusted data.
* Unvalidated JSON parsing.
* No fallback for failed AI response.
* Infinite retry.
* Blocking long request without timeout strategy.
* AI scoring without explanation.
* AI recommendation without structured output.

---

## Git DO NOT USE

DO NOT USE:

* `git add .` without checking `git status`.
* Commit broken code.
* Commit `.env`.
* Commit `node_modules`.
* Commit vendor changes unless dependency update is intentional.
* Force push unless the user explicitly asks.
* Delete branches without confirmation.
* Rewrite history without confirmation.

---

# Development Workflow

Follow this workflow for every task.

## Step 1: Understand

Read:

```txt
CONTEXT.md
MEMORY.md
```

If UI-related, read:

```txt
DESIGN.md
```

If skill-specific, read:

```txt
.agents/skills/SKILL.md
```

or the relevant skill folder file.

---

## Step 2: Inspect files

Before editing, inspect related files.

Examples:

For dashboard UI:

* `resources/js/pages/Dashboard.vue`
* related layout
* related components
* `DESIGN.md`

For candidate backend:

* routes
* controller
* model
* migration
* request
* service
* tests

---

## Step 3: Plan

Create a short internal plan before editing.

Plan should include:

* What files will be touched.
* What logic will change.
* What UI will change.
* What tests will be run.
* Any risk.

If the instruction is ambiguous, ask the user first.

---

## Step 4: Implement

Make focused changes.

Do not rewrite unrelated files.

Do not refactor the whole project unless asked.

Keep changes small, clean, and testable.

---

## Step 5: Test

Run relevant tests.

Minimum expectation:

* Backend changed → run backend tests.
* Frontend changed → run build/lint if available.
* Route changed → check route list.
* Migration changed → check migration status.
* UI changed → check responsive behavior logically.

---

## Step 6: Report

After finishing, report:

* What was changed.
* What files were changed.
* What feature is now available.
* What tests passed.
* Any warnings fixed.
* Any known issue.

---

## Step 7: Commit

If tests pass:

* Check `git status`.
* Commit with clear message.
* Push if expected.
* Update `MEMORY.md`.

---

# Response Format After Completing Work

When reporting to the user, use this format:

```md
## Selesai

### Yang Dibuat
- ...

### File yang Diubah
- `...`

### Backend Explanation
- ...

### Frontend Explanation
- ...

### Testing
- `php artisan test` — passed
- `npm run build` — passed

### MEMORY.md
- Updated with latest progress.

### Git
- Commit: `feat(scope): message`
```

If something fails, use:

```md
## Belum Sepenuhnya Selesai

### Yang Berhasil
- ...

### Yang Gagal
- ...

### Penyebab
- ...

### Langkah Perbaikan Berikutnya
- ...
```

Do not pretend the task is complete if tests fail.

---

# UI Quality Checklist

Before finishing a frontend task, verify:

* Layout responsive on mobile.
* Layout responsive on desktop.
* Text contrast is readable.
* Buttons have clear states.
* Inputs have clear labels.
* Loading state exists if needed.
* Empty state exists if needed.
* Error state exists if needed.
* Spacing is consistent.
* No random colors outside `DESIGN.md`.
* No generic AI slop visual.
* No console warning.
* No obvious accessibility issue.

---

# Backend Quality Checklist

Before finishing a backend task, verify:

* Route exists and uses correct name.
* Controller is not bloated.
* Validation exists.
* Database fields are correct.
* Model relationships are correct.
* Authorization is considered.
* Error handling exists.
* OpenAI response is validated if used.
* Tests are added or updated if needed.
* No sensitive data is exposed.

---

# SmartRecruit Frontend Page Guidelines

## Dashboard page

Dashboard should show useful HRD information, such as:

* Total candidates.
* New applications.
* Average candidate score.
* Shortlisted candidates.
* Candidates needing review.
* Recent CV uploads.
* Top candidates.
* Application funnel.
* AI analysis status.

Avoid fake decoration that does not help HRD.

---

## Candidate list page

Candidate list should support:

* Candidate name.
* Applied position.
* Score.
* Status.
* Upload date.
* Skill highlights.
* Sort by score.
* Filter by status.
* Search candidate.
* View detail action.

---

## Candidate detail page

Candidate detail should show:

* Candidate profile.
* CV summary.
* AI score breakdown.
* Strengths.
* Weaknesses.
* HRD notes.
* Status update.
* Recommendation.
* Original CV access if allowed.

---

## Upload CV page

Upload CV page should include:

* Clear upload area.
* File requirements.
* Loading state during analysis.
* Error state for invalid file.
* Success state after upload.
* Explanation of what AI will analyze.

---

# SmartRecruit Backend Feature Guidelines

Recommended backend structure for ATS features:

```txt
app/
├── Http/
│   ├── Controllers/
│   │   ├── CandidateController.php
│   │   ├── ResumeController.php
│   │   └── ResumeAnalysisController.php
│   └── Requests/
│       ├── StoreCandidateRequest.php
│       ├── StoreResumeRequest.php
│       └── AnalyzeResumeRequest.php
├── Models/
│   ├── Candidate.php
│   ├── Resume.php
│   ├── ResumeScore.php
│   └── JobPosition.php
└── Services/
    ├── ResumeParserService.php
    ├── ResumeScoringService.php
    └── OpenAIResumeAnalysisService.php
```

Only create these when needed.

Do not create all files upfront without feature requirement.

---

# Data Handling Rules

For candidate and CV data:

* Keep data structured.
* Avoid storing everything as one long text blob.
* Use JSON columns only when flexible structure is needed.
* Index fields used for search/filter/sort.
* Use clear status enums or constants.
* Keep AI analysis auditable.

Possible candidate statuses:

```txt
new
reviewing
shortlisted
interview
rejected
accepted
```

---

# Accessibility Rules

Frontend should be accessible enough for production use.

Use:

* Proper button elements.
* Proper labels.
* Keyboard-friendly interactions.
* Visible focus state.
* Good text contrast.
* Meaningful empty states.
* Semantic HTML where possible.

Do not use clickable `div` when a `button` is correct.

---

# Performance Rules

Avoid unnecessary heavy UI or backend logic.

## Frontend

* Avoid unnecessary re-renders.
* Avoid huge components.
* Lazy-load heavy sections if needed.
* Keep dashboard efficient.
* Avoid expensive computed logic in template.

## Backend

* Avoid N+1 queries.
* Use eager loading when needed.
* Use pagination for candidate lists.
* Use indexes for sorting/filtering.
* Queue long AI analysis if it becomes slow.
* Cache only when useful and safe.

---

# Final Definition of Done

A task is done only when:

* Required context files were checked.
* Relevant skill files were checked.
* Code follows project architecture.
* Errors are fixed.
* Warnings related to the task are fixed.
* UI follows `DESIGN.md`.
* Feature works as requested.
* Tests/checks were run.
* `MEMORY.md` was updated.
* Code was committed if tests passed.
* Final report was given clearly.

If any of these are missing, the task is not fully done.

---

# Agent Mindset

Work like a senior developer.

Do not rush.

Do not guess.

Do not create AI slop.

Do not hide errors.

Do not ignore warnings.

Do not break architecture.

Do not over-engineer early.

Build SmartRecruit as a serious ATS product that HRD could actually use.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- filament/filament (FILAMENT) - v5
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v3
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/wayfinder (WAYFINDER) - v0
- livewire/livewire (LIVEWIRE) - v4
- larastan/larastan (LARASTAN) - v3
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/vue3 (INERTIA_VUE) - v3
- tailwindcss (TAILWINDCSS) - v4
- vue (VUE) - v3
- @laravel/vite-plugin-wayfinder (WAYFINDER_VITE) - v0
- eslint (ESLINT) - v9
- prettier (PRETTIER) - v3

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

</laravel-boost-guidelines>
