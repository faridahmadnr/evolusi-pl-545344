# evolusi-pl-545344

Tugas 1 Praktikum Evolusi Perangkat Lunak - Manajemen GitHub & Prinsip CI  
**NIM:** 545344  
**Nama Mahasiswa:Farid Ahmad Nur Rahman
**Organisasi:** [KEPL2026](https://github.com/KEPL2026)  

---

##  Deskripsi Proyek

Repository ini berisi implementasi aplikasi web sederhana berbasis **Laravel 11** dengan menerapkan standar rekayasa perangkat lunak modern:
- **Git Flow Branching Strategy**: Pemisahan branch `main` (production), `dev` (development), dan `feature/*` (fitur spesifik).
- **Conventional Commits**: Standar penulisan pesan commit terstruktur (`feat:`, `fix:`, `chore:`, `ci:`, `docs:`, `test:`).
- **Continuous Integration (CI)**: Otomatisasi pengujian dan linting menggunakan **GitHub Actions** dengan 2 jobs (`code-linting` dan `test-suite`).
- **Branch Protection Rules**: Proteksi branch `main` dan `dev` untuk memastikan setiap perubahan melalui Pull Request dan lolos status check CI.

---

##  Alur Percabangan (Branching Model)

1. `main`: Branch utama (terlindungi, hanya menerima perubahan via Pull Request dari `dev`).
2. `dev`: Branch integrasi pengembangan (menerima perubahan dari `feature/*`).
3. `feature/<nama-fitur>`: Branch kerja untuk implementasi fitur nyata sebelum digabungkan ke `dev`.

---

##  GitHub Actions CI

Workflow CI didefinisikan pada `.github/workflows/ci.yml` dan terdiri dari dua job:
1. **`code-linting`**: Memeriksa kerapian dan standar penulisan kode PHP menggunakan Laravel Pint.
2. **`test-suite`**: Menjalankan pengujian otomatis (Unit & Feature Tests) menggunakan PHPUnit dengan database in-memory SQLite.

---

##  Menjalankan Aplikasi Secara Lokal

1. Clone repository:
   ```bash
   git clone https://github.com/faridahmadnr/evolusi-pl-545344.git
   cd evolusi-pl-545344
   ```
2. Install dependensi:
   ```bash
   composer install
   ```
3. Salin environment dan generate key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Jalankan migrasi database:
   ```bash
   php artisan migrate
   ```
5. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
6. Menjalankan automated tests:
   ```bash
   php artisan test
   ```
