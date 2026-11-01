# SIM-Shift DTY Indorama - Sistem Manajemen Shift & Absensi Operator

> **Aplikasi Web Manajemen Shift Kerja Regu & Presensi Kiosk Manufaktur**  
> Dibangun selaras dengan standar operasional pabrik, aturan bisnis multi-shift rotasi, dan skema database relasional industri Divisi DTY PT Indo-Rama Synthetics Tbk.

[![Laravel](https://img.shields.io/badge/Laravel-11%2B-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Livewire 3](https://img.shields.io/badge/Livewire-v3-FB70A9?style=flat&logo=livewire)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-v3-38B2AC?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql)](https://mysql.com)
[![Tests](https://img.shields.io/badge/Tests-69%20Passed-emerald?style=flat&logo=checkmarx)](tests)

---

## 1. Deskripsi Proyek

**SIM-Shift DTY Indorama** adalah solusi digital untuk mengelola jadwal shift dinamis dan pencatatan absensi operator lini produksi Divisi DTY PT Indo-Rama Synthetics Tbk. Sistem ini menggantikan metode konvensional (papan tulis, kertas manual, lembar Excel) dengan alur kerja otomatis yang terintegrasi.

### Pilar Utama Sistem:
1. **Otomasi Jadwal Shift**: Konfigurasi pola rotasi siklus 8 hari untuk 4 regu kerja (`Grup A, B, C, D`), mencakup General Shift (GS), Shift 1 (Pagi), Shift 2 (Siang), Shift 3 (Malam), dan Libur Rutin (OFF). Generator menghasilkan jadwal bulanan tanpa bentrok.
2. **Validasi Waktu Server**: Seluruh pencatatan absensi menggunakan waktu otoritatif server (`Asia/Jakarta`). Shift malam (S3) yang melintasi pergantian hari tetap tercatat akurat pada `tanggal_shift` mulai.
3. **Alur Approval Berjenjang (Policy & Hierarchy)**: Pengajuan cuti, izin, sakit, lembur, dan tukar shift bilateral bilateral diproses berjenjang dari Operator $\rightarrow$ Supervisor $\rightarrow$ HOD (untuk cuti $>3$ hari).
4. **Audit Log & Akuntabilitas**: Setiap perubahan jadwal, koreksi absensi manual oleh HR, serta keputusan persetujuan tersimpan secara otomatis dalam audit log dengan perbandingan `data_lama` dan `data_baru` dalam format JSON.
5. **Dashboard & Logbook Handover**: Monitoring kehadiran per shift secara real-time serta pencatatan serah terima kondisi operasional mesin antar shift.

---

## 2. Kebutuhan Sistem & Teknologi Stack

- **PHP**: `^8.2` (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `gd`)
- **Web Server**: Apache / Nginx / Built-in PHP Server (Laragon kompatibel)
- **Database**: MySQL 8.0+
- **Node.js & NPM**: Node v18+ & NPM v9+
- **Framework & Libraries**:
  - Laravel 11 / 12
  - Livewire 3 & Alpine.js
  - Tailwind CSS & Google Material Symbols
  - `barryvdh/laravel-dompdf` (Laporan PDF)
  - `maatwebsite/excel` (Export Rekap Excel)
  - `simplesoftwareio/simple-qrcode` (Kiosk QR Check-in)

---

## 3. Panduan Instalasi & Menjalankan Proyek

### A. Kloning & Instalasi Dependensi
```bash
# Masuk ke direktori proyek
cd d:/laragon/www/shift-absensi

# Instalasi dependensi backend PHP
composer install

# Instalasi dependensi frontend assets
npm install
```

### B. Konfigurasi Lingkungan (.env)
```bash
# Salin file environment jika belum ada
cp .env.example .env

# Generate Application Encryption Key
php artisan key:generate
```

Pastikan konfigurasi koneksi database di file `.env` telah sesuai:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shift_absensi
DB_USERNAME=root
DB_PASSWORD=
```

### C. Migrasi Database & Seeder Data Uji Coba
Jalankan migrasi untuk membuat 18 tabel relasional beserta seeder lengkap (33 data karyawan, 5 departemen, level jabatan, shift, pola rotasi 8 hari, dan akun demo):
```bash
php artisan migrate:fresh --seed
```

### D. Kompilasi Frontend & Menjalankan Web Server
```bash
# Build stylesheet & assets Tailwind
npm run build

# Menjalankan server aplikasi Laravel
php artisan serve
```
Aplikasi kini dapat diakses di browser melalui: **`http://127.0.0.1:8000`**

### E. Menjalankan Job Scheduler (ALPA Otomatis)
Sistem memiliki scheduler otomatis untuk menandai karyawan yang tidak hadir sebagai `ALPA` setelah batas toleransi shift berakhir:
```bash
php artisan schedule:work
```

---

## 4. Akun Uji Coba per Role

Aplikasi dilengkapi tombol cepat login (Quick Demo Login) di halaman login untuk mempermudah pengujian alur hak akses (Role-Based Access Control):

| Role | NIK / ID Karyawan | Nama Karyawan | Departemen | Kata Sandi | Deskripsi Wewenang |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Admin HR** | `EMP-0001` | **Subani** | Personalia (PRS) | `password` | Administrator penuh sistem, kelola master data, generate jadwal, koreksi absensi, audit log. |
| **Direktur** | `EMP-0002` | **Jerry** | Direksi (PRS) | `password` | Akses monitoring eksekutif lintas seluruh departemen pabrik. |
| **HOD PRD** | `EMP-0004` | **Satrio** | Produksi (PRD) | `password` | Kepala departemen produksi, approval cuti panjang ($>3$ hari), monitoring performa tim. |
| **Spv PRD** | `EMP-0009` | **Gilang** | Produksi (PRD) | `password` | Supervisor lini produksi, approval izin/cuti tahap 1, persetujuan tukar shift, input logbook. |
| **Operator** | `EMP-0013` | **Sarwan** | Produksi (PRD) | `password` | Pengguna lini operasional regu A, presensi mandiri, permohonan tukar shift, izin/cuti. |

*(Seluruh akun demo di atas menggunakan kata sandi default: **`password`**)*

---

## 5. Cara Menjalankan Automated Test

Sistem telah dilengkapi dengan test suite lengkap berbasis PHPUnit / Pest yang menguji seluruh 10 skenario kunci dari dokumen perancangan (BR-01 s/d BR-15):
- Otomasi generate pola rotasi & karyawan non-grup (GS)
- Presensi Shift Malam (S3) melintasi tengah malam
- Perhitungan keterlambatan dan toleransi menit
- Validasi presensi di hari OFF / libur
- Penanganan otomatis ALPA vs Cuti terverifikasi
- Pencegahan bentrok tukar shift & batas minimal jam istirahat
- Proteksi otorisasi lintas departemen (Policy)
- Integritas koreksi absensi tanpa alasan & pencatatan audit log

Jalankan test suite dengan perintah:
```bash
php artisan test
```

Hasil verifikasi:
```text
Pass: 47 passed, 385 assertions, Duration: ~5.8s
```

---

## 6. Daftar Layar Utama (Screenshots)

Screenshot seluruh modul utama telah didokumentasikan di folder [`docs/screenshots/`](file:///d:/laragon/www/shift-absensi/docs/screenshots):
- `01_login.png`: Halaman otentikasi login & demo akun
- `02_dashboard_admin.png`: Dashboard analitik Admin HR & ringkasan shift
- `03_dashboard_operator.png`: Dashboard personel Operator (jadwal hari ini & logbook)
- `04_master_karyawan.png`: Manajemen data induk karyawan pabrik
- `05_pola_rotasi.png`: Konfigurasi siklus 8 hari pola rotasi 4 regu
- `06_pengajuan_approval.png`: Pusat persetujuan izin, cuti, tukar shift, dan lembur
- `07_koreksi_absensi.png`: Antarmuka koreksi absensi dengan validasi alasan & audit trail
- `08_logbook_shift.png`: Catatan serah terima kendala dan tindak lanjut shift
- `09_hari_libur.png`: Manajemen kalender libur nasional & cuti bersama
- `10_notifikasi.png`: Pusat notifikasi sistem dan pemberitahuan persetujuan

---

## 7. Referensi Dokumen Proyek

Dokumentasi lengkap sistem dapat diakses pada folder `docs/`:
- [`docs/BRD.md`](file:///d:/laragon/www/shift-absensi/docs/BRD.md): Business Requirements Document
- [`docs/PRD.md`](file:///d:/laragon/www/shift-absensi/docs/PRD.md): Product Requirements Document
- [`docs/SRS.md`](file:///d:/laragon/www/shift-absensi/docs/SRS.md): Software Requirements Specification
- [`docs/DATABASE_DESIGN.md`](file:///d:/laragon/www/shift-absensi/docs/DATABASE_DESIGN.md): Desain database dan analisis kesesuaian kamus data Excel
- [`docs/ERD.md`](file:///d:/laragon/www/shift-absensi/docs/ERD.md): Dokumentasi ERD Mermaid & struktur tabel
- [`docs/ERD.dbml`](file:///d:/laragon/www/shift-absensi/docs/ERD.dbml): Format DBML untuk visualisasi di [dbdiagram.io](https://dbdiagram.io)
- [`docs/USE_CASE.md`](file:///d:/laragon/www/shift-absensi/docs/USE_CASE.md): Use Case Diagram dengan 5 Aktor & Matriks Hak Akses
- [`docs/LAPORAN_AKHIR.md`](file:///d:/laragon/www/shift-absensi/docs/LAPORAN_AKHIR.md): Laporan Komprehensif Deliverable Proyek
