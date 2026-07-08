# SmartRecruit — Quick Start Guide

## Prerequisites
- **PHP** 8.4+ with extensions: `pdo`, `mbstring`, `xml`, `curl`, `zip`
- **Composer** 2.x
- **Node.js** 20+ and **npm** 10+
- **PostgreSQL** 18 (or SQLite for local dev — configured by default)

## Installation

```bash
# 1. Clone the repository
git clone <repo-url> smartrecruit
cd smartrecruit

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Set up environment
cp .env.example .env
php artisan key:generate

# 5. Configure database (edit .env if using PostgreSQL)
#    Default: DB_CONNECTION=sqlite (no extra config needed)
#    For PostgreSQL: set DB_CONNECTION=pgsql, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 6. Run migrations
php artisan migrate

# 7. Build frontend assets (or use dev server below)
npm run build
```

## Running the Application

```bash
# Option A — Start both Laravel + Vite dev server (recommended for development)
npm run dev          # Terminal 1: starts Vite with hot reload
php artisan serve    # Terminal 2: starts Laravel at http://localhost:8000

# Option B — Build for production
npm run build
php artisan serve
```

## Testing

```bash
# Run all tests
php artisan test

# Run only auth tests
php artisan test tests/Feature/Auth/

# Run with verbose output
php artisan test --compact
```

## Useful Commands

```bash
npm run lint          # Auto-fix ESLint issues
npm run format        # Format code with Prettier
npm run types:check   # Check TypeScript types
php artisan route:list  # View all registered routes
php artisan migrate:status  # Check migration status
```

## Tech Stack
Laravel 13 · Vue 3.5 · Inertia.js 3 · Tailwind CSS 4 · TypeScript · Pest PHP · Laravel Fortify

---

## 2026-06-25 — Redesign Auth Views & Welcome Page with Acctual Design System

### Summary
Redesigned all authentication-related views and the Welcome page using the "Acctual" design system from DESIGN.md. Applied consistent styling: pill-shaped onyx buttons, 20px card radius, cool-mist backgrounds, Invoice Blue accents, and Open Runde typography across all auth pages.

### Files Changed
- `resources/js/layouts/auth/AuthSimpleLayout.vue` — Rewritten with Acctual design (brand header, white card wrapper)
- `resources/js/pages/Welcome.vue` — Complete redesign (nav bar, hero, feature cards, footer)
- `resources/js/pages/auth/Login.vue` — Restyled with Acctual inputs and buttons
- `resources/js/pages/auth/Register.vue` — Restyled with Acctual design
- `resources/js/pages/auth/ForgotPassword.vue` — Restyled
- `resources/js/pages/auth/ResetPassword.vue` — Restyled
- `resources/js/pages/auth/ConfirmPassword.vue` — Restyled
- `resources/js/pages/auth/TwoFactorChallenge.vue` — Restyled with OTP/recovery code inputs
- `resources/js/pages/auth/VerifyEmail.vue` — Restyled with onyx resend button
- `resources/js/components/PasskeyVerify.vue` — Updated passkey button styling

### Features Added
- **AuthSimpleLayout**: Cool-mist bg, SmartRecruit branded logo with onyx icon, white card (20px radius, subtle shadow)
- **Welcome Page**: Sticky nav, Invoice Blue eyebrow, "Hire smarter not slower" hero, 3 feature cards (wash-sky/lilac/petal icons), simple footer
- **Auth Pages**: h-11 inputs (rounded-xl, cool-mist/50 bg, stone-edge/30 borders, invoice-blue focus), onyx pill buttons with shadow
- **PasskeyVerify**: Rounded-xl outline button, stone-edge separator on paper-white bg

### Bugs Fixed
- Fixed Write tool append bug: The Write tool appended new content instead of overwriting, creating duplicate `<script setup>` blocks in 10 Vue files. All fixed using SearchReplace to remove old content.

### Testing
- `php artisan test` — passed (43 tests, 143 assertions)
- `npm run build` — passed (all Vue files compiled successfully)

### Notes
- All Acctual design tokens were already defined in `resources/css/app.css` (@theme block)
- 2 minor warnings from third-party `@vueuse/core` annotation comments in `reka-ui` — harmless, not from our code
- Auth tests (25 tests) all passed — no auth logic was broken by UI changes

### Git Commit
*(pending)*

---

## 2026-06-25 — Welcome Page Premium Redesign (Origin Financial Editorial Theme)

### Summary
Complete redesign of the Welcome landing page as a Senior Frontend Engineer, following DESIGN.md Origin Financial dark theme tokens precisely. Added sticky glassmorphism navbar with mobile hamburger, page-wide dark/light mode toggle (localized `isNight` ref, not global `.dark` class), Ken Burns animated hero background, floating chat/search bar, chromatic feature cards, stats trust strip, dashboard mockup card, and smooth entrance animations.

### Files Changed
- `resources/js/pages/Welcome.vue` — Full rewrite: Origin Financial editorial theme with sticky glass navbar, hero (Lyon Display 96px headline, white pill CTA + secondary ghost CTA, floating chat input), chromatic feature cards (amethyst/orchid/cyan-spark), stats strip, dark mockup card, footer. All elements respond to localized `isNight` dark/light toggle with 700ms transitions.
- `resources/css/app.css` — Added `animate-fade-up`, `animate-fade-in`, `animate-shimmer` CSS animation utilities with keyframes to `@layer utilities` block.
- `resources/js/components/AppHeader.vue` — Removed unused Tooltip imports (fixed lint errors).

### Features Added
- **Sticky Glassmorphism Navbar**: Transparent → `bg-obsidian/70 backdrop-blur-md` on scroll, responsive (desktop: logo/center links/actions, mobile: hamburger toggle with slide-down menu)
- **Mobile Hamburger Menu**: Hidden on desktop (`lg:hidden`), hamburger/X icon toggle, full-width dropdown with nav links
- **Page-Wide Dark/Light Mode**: Localized `isNight` ref (default `true`), affects navbar, hero, feature section heading, stats, mockup card, footer — all with smooth 700ms transitions. Does NOT use global `.dark` class.
- **Hero Section**: 90vh+, Ken Burns animated background (`scale(1.02)`, 25s alternate), promo pill with shimmer, Lyon Display serif headline (48-96px responsive), subtitle, dual CTAs (primary white pill + secondary ghost pill), floating chat/search input with circular submit button
- **Secondary CTA**: "LEARN MORE" ghost pill button alongside primary "GET STARTED FOR FREE"
- **Chromatic Feature Cards**: 3 cards (Parsing/Scoring/Ranking) with 30px radius, amethyst/orchid/cyan-spark backgrounds, dark gradient overlay
- **Stats Trust Strip**: 4 monospace stats (2,400+ candidates, 94% accuracy, 3.2x faster, 150+ HR teams) with vertical dividers
- **Dashboard Mockup Card**: Window bar with dots, side nav, stat cards, recent applications with colored initial avatars and CV scores
- **Entrance Animations**: `animate-fade-up` with staggered delays (100ms-500ms), `animate-shimmer` for sparkle icon

### Bugs Fixed
- Removed unused Tooltip imports from `AppHeader.vue` (fixed 4 ESLint errors)

### Testing
- `npm run build` — passed (586 modules, 0 errors)
- `php artisan test` — passed (43 tests, 143 assertions)
- `npm run lint` — passed (0 errors, 0 warnings)

### Notes
- Dark/light mode uses localized `isNight` ref with `:class` conditional bindings, NOT global `.dark` class toggle. This prevents theme leakage into auth pages and dashboard.
- Ken Burns scale kept at 1.02 (subtle) to avoid image pixelation at larger viewports.
- The `INVALID_ANNOTATION` warnings in build output are from `node_modules/reka-ui/@vueuse/core` — not our code, safe to ignore.
- Write tool may append content instead of overwriting. Always verify with `findstr /n "script setup" Welcome.vue` after writing.

### Git Commit
*(pending)*

---

## 2026-06-24 — Create Database Schema & Models for SmartRecruit

### Summary
Berhasil membuat 5 file migrasi database dan 5 model Eloquent terkait struktur inti aplikasi SmartRecruit (ATS) yang menggunakan struktur JSONB sesuai dengan PostgreSQL.

### Files Changed
- `database/migrations/2026_06_24_164756_add_role_and_metadata_to_users_table.php` (baru)
- `database/migrations/2026_06_24_164755_create_job_positions_table.php` (baru)
- `database/migrations/2026_06_24_164755_create_applicant_profiles_table.php` (baru)
- `database/migrations/2026_06_24_164755_create_applications_table.php` (baru)
- `database/migrations/2026_06_24_164756_create_cv_analyses_table.php` (baru)
- `app/Models/User.php` (diperbarui)
- `app/Models/JobPosition.php` (baru)
- `app/Models/ApplicantProfile.php` (baru)
- `app/Models/Application.php` (baru)
- `app/Models/CvAnalysis.php` (baru)

### Features Added
- **Backend Task 3 & 4 (CRUD & Validasi)**: Membuat `JobPositionController` dan `ApplicantProfileController` beserta Form Request untuk validasi data (termasuk validasi struktur file resume dan JSON array).
- **Frontend Task 5 (UI/UX)**: Mendesain dan membangun halaman menggunakan Vue 3 + Inertia berdasarkan panduan `DESIGN.md` (mengimplementasikan *Acctual* style: `bg-cool-mist`, font `Open Runde`, `rounded-cards`, `bg-paper-white`).
- **Pest Testing**: Menguji `Store` dan `Update` dari Controller HRD dan Applicant dan seluruhnya Passed.
- Tabel dan model terkait untuk *Job Position* dan *Applicant Profile* telah dihubungkan secara fungsional.

### Bugs Fixed
- Fix `405 Method Not Allowed` pada `ApplicantProfileTest` dengan mengubah tipe method *route update* menjadi `PUT` di `web.php`.

### Testing
- `php artisan test` — passed (4 tests passed, CRUD logic secure).
- `npm run build` — passed (Vue components compiled successfully).

### Notes
- Pengaturan JSONB telah disematkan melalui file migrasi `$table->jsonb(...)` sesuai instruksi, sehingga ketika dieksekusi di database PostgreSQL secara native akan berformat JSONB. Di lokal menggunakan SQLite fallback juga berjalan mulus.
- Relasi Eloquent dan property *fillable* telah ditambahkan dengan lengkap untuk mencegah *Mass Assignment Exception*.

### Git Commit
`feat(schema): add core ATS models and migrations for candidate, jobs, and CV analysis`

---

## 2026-06-25 — Origin Financial Dark Theme Redesign + Dashboard Creation

### Summary
Complete UI redesign from the previous "Acctual" light theme to the new "Origin Financial" dark editorial theme as specified in DESIGN.md. All authentication pages (Login, Register, ForgotPassword, ResetPassword, ConfirmPassword, TwoFactorChallenge, VerifyEmail), Welcome page, and Dashboard have been redesigned. The dashboard now displays real ATS data.

### Files Changed
- `resources/css/app.css` — Replaced entire @theme block with Origin Financial tokens (obsidian, carbon, graphite, slate, mist, frost, pearl, bone, paper-white, amethyst, orchid, cyan-spark, etc.), updated :root and .dark CSS variables to dark-first values
- `resources/views/app.blade.php` — Added Google Fonts (Playfair Display, Inter, JetBrains Mono), set HTML/body background to obsidian (#0f1011), forced dark class
- `resources/js/layouts/auth/AuthSimpleLayout.vue` — Dark obsidian bg, carbon card, bone headings, frost body text
- `resources/js/pages/auth/Login.vue` — Dark inputs (graphite bg, slate border, amethyst focus), white pill CTA
- `resources/js/pages/auth/Register.vue` — Same dark auth styling
- `resources/js/pages/auth/ForgotPassword.vue` — Dark theme
- `resources/js/pages/auth/ResetPassword.vue` — Dark theme
- `resources/js/pages/auth/ConfirmPassword.vue` — Dark theme
- `resources/js/pages/auth/TwoFactorChallenge.vue` — Dark OTP slots and inputs
- `resources/js/pages/auth/VerifyEmail.vue` — Dark theme
- `resources/js/components/PasskeyVerify.vue` — Dark graphite button, carbon separator bg
- `resources/js/pages/Welcome.vue` — Full dark editorial redesign: obsidian canvas, dusk-sky gradient hero, Playfair Display serif headlines, white pill CTA with arrow, chromatic feature cards (amethyst, orchid, cyan-spark) with 30px radius
- `resources/js/pages/Dashboard.vue` — New ATS dashboard with stat cards (Job Positions, Applicants, Applications, CV Analyses), recent applications table with score/status, job positions list, all in Origin Financial dark theme
- `routes/web.php` — Dashboard route now passes aggregated ATS data (counts, recent applications with scores, job positions with application counts)
- `resources/js/components/AppLogo.vue` — Changed from "Laravel Starter Kit" to "SmartRecruit", dark theme styling
- `resources/js/components/AppSidebar.vue` — SmartRecruit nav items (Dashboard, Job Positions, Candidates, Settings)
- `resources/js/components/AppHeader.vue` — Dark theme, SmartRecruit nav items, removed Laravel docs links

### Features Added
- Origin Financial dark theme applied system-wide via CSS @theme tokens
- Google Fonts loaded globally: Playfair Display (Lyon Display substitute), Inter (Suisse Int'l substitute), JetBrains Mono (Roboto Mono substitute)
- ATS Dashboard with real data: stat cards, recent applications table with CV analysis scores, job positions list
- Wide-tracked uppercase labels (0.182em letter-spacing) per design spec
- White pill CTA convention with trailing arrow (→)
- Chromatic feature cards on Welcome page

### Testing
- `npm run build` — passed (586 modules)
- `php artisan test` — passed (43 tests, 143 assertions)

### Notes
- The DESIGN.md specifies premium fonts (Lyon Display, Suisse Int'l). We use Google Fonts substitutes: Playfair Display, Inter, JetBrains Mono.
- Dark mode is now forced (not toggle-based) since the design is dark-first.
- The Write tool may append content to existing files instead of overwriting. Always verify with a build after using Write.


---

## 2026-07-02 - Fix Database Migration Order

### Summary
Memperbaiki error SQLSTATE[42P01] (Undefined table applications) saat menjalankan php artisan migrate:fresh dengan mengoreksi urutan timestamp pada file migrasi.

### Files Changed
- database/migrations/2026_06_24_164756_create_cv_analyses_table.php (renamed to 2026_07_01_153004_create_cv_analyses_table.php)

### Bugs Fixed
- Fix error migration cv_analyses yang mencoba membuat foreign key ke tabel pplications sebelum tabel tersebut dibuat, dengan merubah nama file (timestamp) sehingga cv_analyses dijalankan setelah pplications.

### Testing
- php artisan migrate:fresh - passed (semua tabel berhasil dibuat ulang)

### Git Commit
*(pending)*

---

## 2026-07-02 - Fix Tailwind CSS IDE Warnings in JobApply

### Summary
Fixed Tailwind CSS IDE warnings in JobApply.vue by upgrading deprecated v3 gradient classes to v4 linear-gradient utilities and adjusting variant stacking.

### Files Changed
- `resources/js/pages/JobApply.vue`

### Bugs Fixed
- Updated `bg-gradient-to-*` to `bg-linear-to-*`
- Changed `disabled:hover:scale-100` to `hover:disabled:scale-100`

---

## 2026-07-04 - Fix News Layout and Header

### Summary
Fixed the layout of `Index.vue` and `Show.vue` in the News folder to remove the User Side Sidebar and Header. Added a custom EmptyLayout and integrated the sticky glassmorphism header used in the Welcome and JobApply pages for a cohesive aesthetic.

### Files Changed
- `resources/js/pages/News/Index.vue`
- `resources/js/pages/News/Show.vue`

### Features Added
- Menerapkan `EmptyLayout` untuk melewati default `AppLayout` sehingga Sidebar dan Header dari User Side hilang.
- Menyalin layout Header / Navbar dari `JobApply.vue` lengkap dengan fungsionalitas scroll (`scrolled`), mode warna (`isNight`), serta *mobile toggle* yang terhubung dan responsif.
- Menyesuaikan parent class wrapper untuk transisi transparan ke mode gelap dan terang agar seragam dengan halaman Welcome.

### Bugs Fixed
- Resolved unclosed `<div>` tag errors that initially broke the `npm run build` process during replacement.

### Testing
- `npm run build` — passed (successfully compiled all Vue files).

### Notes
- Pengaturan default inertia pada `resources/js/app.ts` tidak dimodifikasi karena override di tingkat komponen Vue menggunakan `EmptyLayout` terbukti lebih aman.
- Warna teks dalam artikel pada halaman Show masih menyesuaikan karena *dark-theme* News sudah disiapkan spesifik dari kontennya.

### Git Commit
*(No git repo initialized)*

---

## 2026-07-04 - Tambahkan About Me & Ratings pada Welcome.vue

### Summary
Menambahkan bagian "About Me" dan "Ratings" di halaman depan `Welcome.vue` dengan Bahasa Indonesia interaktif. Menggunakan desain responsif, transisi warna untuk dark/light mode, animasi hover, serta memanfaatkan efek scroll reveal (`.scroll-reveal`). Struktur file direstrukturisasi sehingga background Galaxy tetap ter-render di belakang konten yang dapat di-scroll.

### Files Changed
- `resources/js/pages/Welcome.vue`
- `public/images/about_me.jpg` (Ditambahkan melalui generator AI)

### Features Added
- **Restrukturisasi Background**: Galaxy background kini dibuat `fixed` (tidak ikut tergulir) sehingga berfungsi sebagai *parallax backdrop* yang selalu ada di belakang semua section baru.
- **Section "Tentang Saya"**: Dilengkapi dengan profil interaktif, efek glassmorphism, tombol "skills" yang memiliki efek hover, dan placeholder gambar (sudah di-*generate* secara AI dan dihubungkan ke `public/images/about_me.jpg`).
- **Section "Kualitas & Reputasi (Ratings)"**: Berisi tiga kartu *testimonial/rating* interaktif dengan *scroll animations* dan hover state bergaya neon.
- **Scroll Reveal**: Mengaktifkan CSS kustom `.scroll-reveal` untuk memberikan efek "fade-up" ketika elemen masuk ke *viewport*.
- Memperbarui navlink "ABOUT ME" agar mengarah ke jangkar `#about` secara tepat.

### Bugs Fixed
- Menyelesaikan tumpang tindih elemen wrapper warna gelap/terang agar harmonis dengan background yang *fixed*.

### Testing
- `npm run build` — passed (Sukses memproses template baru).

### Git Commit
*(No git repo initialized)*

---

## 2026-07-04 - Redesign JobApply.vue dengan UI Premium Interaktif

### Summary
Melakukan perombakan total (redesign) pada halaman `JobApply.vue`. Background halaman diubah menjadi gelap pekat (hitam obsidian) dengan ambient glow sesuai permintaan pengguna, sambil mempertahankan komponen Header bawaan. Tampilan Job Card dan Modal Apply diubah menjadi jauh lebih modern, interaktif (hover effect, scale, glassmorphism), dan tidak menggunakan data palsu, murni menggunakan properti dari database.

### Files Changed
- `resources/js/pages/JobApply.vue`

### Features Added
- **Premium Background**: Menerapkan background `#050505` dengan efek ambient glow (blur lighting) di belakang layout.
- **Job Cards Redesign**:
  - Menggunakan efek *glassmorphism* pada kartu lamaran.
  - Menambahkan *hover interactions*: card naik (translate-y), border menyala amber, image scale-in.
  - Badges untuk tipe pekerjaan (Full-time/dll), lokasi, dan company dipindahkan ke dalam gambar sebagai *overlay*.
  - Tags *skills* kini berbentuk kapsul modern dengan efek *hover*.
  - *Bottom glowing border* yang muncul otomatis dari kiri ke kanan saat kartu di-hover.
- **Modal Apply Redesign**:
  - Tampilan diperbesar dengan skema 2 kolom. Kolom kiri untuk banner/informasi job, kolom kanan untuk formulir aplikasi.
  - Desain form menggunakan border minimalis putih transparan dengan *focus rings* berwarna amber premium.
  - Pilihan CV yang dinamis dilengkapi dengan *hover animations*, indikator centang (*check icon*), dan skala.
  - Tampilan *empty state* "Belum ada CV" dibuat lebih komunikatif dan elegan.
- **Strict Data Mapping**: Menghindari penggunaan data fiktif. Seluruh text (title, location, type, dll) murni ditarik dari interface `Job` bawaan DB.

### Testing
- `npm run build` — passed (Sukses memproses template baru).
- Uji navigasi, efek *hover*, dan rendering UI pada Modal tanpa error reaktivitas.

### Git Commit
*(No git repo initialized)*
---

## 2026-07-08 - Redesign News Show.vue & Konsistensi Warna (Abu-abu)

### Summary
Memperbarui tampilan Show.vue pada modul News agar lebih rapi, profesional, dan interaktif, serta menyelaraskan desain dengan Index.vue. Komponen card untuk daftar berita lain ("Baca Juga") diperbaiki layoutnya menggunakan rasio spect-16/10 agar gambar tampil jelas tanpa terpotong atau proporsi yang aneh. Seluruh warna aksen utama (font, border, hover, shadow) yang sebelumnya berwarna Orange/Amber diganti ke Abu-abu (Gray) secara menyeluruh pada file Show.vue dan Index.vue untuk memastikan konsistensi visual.

### Files Changed
- esources/js/pages/News/Index.vue
- esources/js/pages/News/Show.vue

### Features Added
- **Konsistensi Tema**: *Ambient glow effect* (blur lighting) pada background Show.vue sekarang disamakan kodenya dengan Index.vue, lengkap dengan skema warna abu-abu.
- **Related News Redesign**:
  - Image ratio dari spect-video diubah menjadi spect-16/10 meniru card Index.vue.
  - Padding pembungkus disesuaikan, image sekarang proporsional penuh (*full cover*).
  - Hover interactions pada gambar dan badge dimaksimalkan untuk transisi interaktif.
- **Color Switch**: Orange (mber-400, mber-500, dll) dan bayangan neon-nya diganti dengan nuansa abu-abu (gray-400, gray-500) yang elegan secara seragam di Show.vue dan Index.vue.

### Testing
- 
pm run build � passed (Sukses memproses template baru).
- UI Check � Perubahan styling Vue components berhasil.

### Git Commit
*(No git repo initialized)*
---

## 2026-07-08 - JobApply Redesign & Konsistensi Warna (Abu-abu)

### Summary
Memperbarui tampilan \JobApply.vue\ (sebelumnya disebutkan \Index.vue\ di News) dengan mengganti warna tombol "We Are Hiring", teks "Next Career", beserta \hover state\-nya menjadi skema Abu-abu (\gray\). Hal ini dilakukan agar tampilannya lebih konsisten dengan hasil desain \Show.vue\ (dan \Index.vue\) di folder News yang telah dirubah sebelumnya. Keseluruhan palet \mber\ diubah menjadi \gray\ di \JobApply.vue\.

### Files Changed
- \esources/js/pages/JobApply.vue\

### Features Added
- **Konsistensi Tema Abu-abu**: Mengubah komponen di halaman Job Apply, seperti *badge* "We Are Hiring", teks *highlight* "Next Career", *glow effect*, garis batas (*border*), tombol melamar, serta *ambient background glow* dari warna *amber/orange* menjadi abu-abu (*gray*).
- **Interaktivitas (*Hover States*)**: Menambahkan efek *hover* yang lebih halus (perubahan dari \gray-400\ ke \gray-300\, perbesaran skala saat di-\*hover\*) pada teks "Next Career" dan *badge* "We Are Hiring". 

### Testing
- \
pm run build\ � passed (Kompilasi sukses tanpa *error* *template*).
- \UI Check\ � Efek transisi warna abu-abu (dan hilangnya warna *amber*) berhasil diterapkan.

### Git Commit
*(No git repo initialized)*
---

## 2026-07-08 - News Index.vue QR Code & App Store Widget Redesign

### Summary
Memperbarui tampilan *Widget QR Code* pada bagian sidebar kanan halaman Index.vue di folder News. Desain *widget* lama yang sederhana kini diganti dengan desain yang jauh lebih profesional, menarik, dan interaktif yang menampilkan *mockup QR Code* dengan animasi garis *scanner* berserta tombol unduhan khusus bergaya modern untuk *App Store* dan *Google Play*. 

### Files Changed
- esources/js/pages/News/Index.vue

### Features Added
- **Interactive QR Code Mockup**: Membuat simulasi QR Code secara visual melalui elemen SVG khusus yang proporsional, dipadukan dengan desain frame membulat, bayangan 3D (*drop shadow*), dan interaksi skalabilitas saat di-hover.
- **Scanner Animation**: Menambahkan efek visual *laser scan* berjalan menggunakan kelas *animation* CSS keyframes scan yang ditambahkan pada tag <style scoped>.
- **App Download Badges**: Merancang dua tombol "Download on the App Store" dan "GET IT ON Google Play" otentik dalam format gelap (*dark mode*) yang dilengkapi vektor logo resmi masing-masing, tata letak teks responsif, serta efek interaksi *hover*.

### Testing
- 
pm run build � passed (Sukses memproses template baru dan animasi CSS).
- UI Check � Animasi scanner bergerak mulus dengan *keyframes scan*. Desain konsisten secara visual.

### Git Commit
*(No git repo initialized)*
