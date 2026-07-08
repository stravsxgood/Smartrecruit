# CONTEXT.md - Project Charter & Roadmap

## 1. Project Overview
*   **Project Name:** SmartRecruit
*   **Description:** Enterprise Applicant Tracking System (ATS) untuk membantu HRD menyaring, mengurutkan, dan mengelola data pelamar secara efisien menggunakan analisis AI.
*   **Current Phase:** Phase 1 - Database Schema & Models Ready

---

## 2. Current State & Built-in Features
Proyek ini baru saja selesai di-inisialisasi. Struktur dasar yang sudah siap pakai saat ini meliputi:
*   **Framework:** Laravel 13 & Inertia.js (Vue 3 + TypeScript).
*   **Authentication:** Laravel Fortify (Sudah terpasang fitur Register, Login, 2FA, dan Passkeys/WebAuthn).
*   **Styling & UI:** Tailwind CSS & Shadcn/Vue base components.
*   **Testing Suite:** Pest PHP (Konfigurasi awal untuk Feature & Unit testing sudah siap).

*Catatan untuk AI: Jangan membuat ulang fitur autentikasi. Gunakan yang sudah disediakan oleh boilerplate.*

---

## 3. Database Schema (Initial & Core ATS)
Kondisi database saat ini memiliki tabel bawaan *auth* dan tabel inti ATS:
*   `users` (Data pengguna inti + kolom 2FA, `role`, `metadata`)
*   `passkeys` (Autentikasi tanpa password)
*   `cache` & `jobs` (Manajemen antrean dan cache Laravel)
*   `job_positions` (Data lowongan pekerjaan, struktur JSONB)
*   `applicant_profiles` (Data profil spesifik pelamar, struktur JSONB)
*   `applications` (Data lamaran yang mengikat pelamar dan lowongan)
*   `cv_analyses` (Data evaluasi dan skoring CV menggunakan OpenAI)

---

## 4. Development Workflow (How We Build)
Setiap kali menambahkan fitur baru dari awal, AI WAJIB mengikuti urutan *workflow* berikut agar kodingan tetap rapi:

1.  **Database First:** Buat file *migration* baru untuk tabel yang dibutuhkan.
2.  **Backend Logic:** 
    *   Buat Model beserta *relationship*-nya dengan benar.
    *   Buat *Dedicated Form Request* di `app/Http/Requests/` untuk validasi input.
    *   Buat Controller/Action untuk memproses data.
3.  **Frontend View:** Buat halaman Vue di `resources/js/pages/` dan hubungkan via Inertia.
4.  **Testing:** Tulis minimal 1 Feature Test menggunakan **Pest PHP** untuk memastikan fitur berjalan lancar.

---

## 5. Next Steps / Immediate Todo
Berikut adalah antrean fitur yang akan dibangun berikutnya (Urutkan dari yang paling mendesak):

- [x] **Task 1:** Membuat migrasi database dan model untuk fitur utama ATS (JobPosition, ApplicantProfile, Application, CvAnalysis).
- [ ] **Task 2:** Mengisi detail `DESIGN.md` untuk tema visual ATS (Warna dominan enterprise, typografi, card layout, dll).
- [ ] **Task 3:** Membuat Backend Logic (Form Request, Controller, Route) untuk Manajemen Lowongan Pekerjaan (HRD).
- [ ] **Task 4:** Membuat Backend Logic untuk Profil Pelamar dan Upload CV.
- [ ] **Task 5:** Membuat Frontend View (Vue/Inertia) untuk Dashboard HRD dan Manajemen Lowongan.
