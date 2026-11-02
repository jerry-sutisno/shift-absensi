# SIM-Shift DTY Indorama - Sistem Manajemen Shift & Absensi Operator

> **Aplikasi Web Manajemen Shift Kerja Regu & Presensi Karyawan Berbasis Waktu Server**  
> Dibangun selaras dengan standar operasional pabrik manufaktur, aturan bisnis rotasi multi-shift, dan skema database relasional industri Divisi DTY PT Indo-Rama Synthetics Tbk.

[![Laravel](https://img.shields.io/badge/Laravel-11%2B-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Livewire 3](https://img.shields.io/badge/Livewire-v3-FB70A9?style=flat&logo=livewire)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-v3-38B2AC?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql)](https://mysql.com)

---

## 1. Deskripsi Proyek

**SIM-Shift DTY Indorama** adalah solusi digital untuk mengelola rotasi jadwal kerja dinamis dan pencatatan absensi karyawan operasional pabrik Divisi DTY PT Indo-Rama Synthetics Tbk. Sistem ini menggantikan metode konvensional (papan tulis manual, buku serah terima fisik, dan lembar Excel) dengan sistem terintegrasi yang akurat dan transparan.

### ✨ Fitur Unggulan Sistem:
1. **Presensi Mandiri di Dashboard Personal**:
   - Setiap karyawan yang login (**Admin HR, Direktur, HOD, Supervisor, maupun Operator**) dapat langsung melakukan **Absen Masuk** dan **Absen Pulang** hanya dengan sekali klik tanpa perlu mengetik NIK/PIN ulang.
   - Status presensi dihitung otomatis secara *real-time* (Tepat Waktu, Terlambat dengan rincian menit, atau Pulang).
2. **Otomasi Pola Rotasi Shift 8 Hari (4 Regu)**:
   - Pengaturan siklus rotasi 8 hari bergantian untuk 4 regu kerja (`Grup A, B, C, D`), mencakup General Shift (GS), Shift 1 (Pagi), Shift 2 (Siang), Shift 3 (Malam), dan Libur Rutin (OFF).
   - Generator jadwal bulanan instan tanpa bentrok jadwal.
3. **Validasi Waktu Server & Shift Lintas Hari (*Cross Midnight*)**:
   - Seluruh pencatatan absensi mengacu mutlak pada waktu server (`Asia/Jakarta`). Shift malam (S3) yang melintasi tengah malam tetap tercatat akurat pada tanggal shift awal.
4. **Alur Approval Berjenjang & Tukar Shift Bilateral**:
   - Pengajuan tukar shift dibatasi ketat: hanya bisa bertukar dengan rekan **satu divisi dan setara level jabatan** serta mematuhi batas istirahat minimal antar-shift.
   - Persetujuan bertingkat: Operator $\rightarrow$ Supervisor $\rightarrow$ HOD (khusus cuti $> 3$ hari).
5. **Logbook Serah Terima Shift (*Handover*)**:
   - Supervisor mencatat ringkasan operasional, kendala mesin/material, dan catatan tindak lanjut antar regu jaga.
6. **Integritas Koreksi Absensi & Audit Log**:
   - Koreksi absensi manual oleh HR wajib menyertakan alasan tertulis, dan seluruh perubahan tersimpan otomatis di Audit Trail (`data_lama` vs `data_baru` format JSON).
7. **Laporan & Rekapitulasi Eksekutif**:
   - Ekspor rekap absensi bulanan ke **PDF** (`dompdf`) dan **Excel** (`maatwebsite/excel`).

---

## 2. Kebutuhan Sistem & Teknologi

- **PHP**: `^8.2` atau `^8.3` (Ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `gd`)
- **Web Server**: Apache / Nginx / Laragon
- **Database**: MySQL 8.0+
- **Node.js & NPM**: Node v18+ & NPM v9+
- **Framework & Libraries**:
  - Laravel 11 / 12
  - Livewire 3 & Alpine.js
  - Tailwind CSS & Google Material Symbols
  - `barryvdh/laravel-dompdf` (Laporan Rekap PDF)
  - `maatwebsite/excel` (Export Rekap Excel)

---

## 3. Panduan Instalasi & Menjalankan Proyek

### A. Kloning & Instalasi Dependensi
```bash
# Masuk ke direktori proyek
cd d:/laragon/www/shift-absensi

# Instalasi dependensi PHP & Assets Frontend
composer install
npm install
```

### B. Konfigurasi Lingkungan (.env)
```bash
# Buat salinan .env jika belum ada
cp .env.example .env

# Generate Application Encryption Key
php artisan key:generate
```

Pastikan koneksi database di file `.env` sudah benar:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shift_absensi
DB_USERNAME=root
DB_PASSWORD=
```

### C. Migrasi Database & Seeder Lengkap
Jalankan migrasi untuk membuat 18 tabel relasional beserta data awal (33 data karyawan, 5 departemen, akun demo per role, shift, dan pola rotasi):
```bash
php artisan migrate:fresh --seed
```

### D. Build Assets & Jalankan Server
```bash
# Build stylesheet Tailwind CSS
npm run build

# Menjalankan server lokal Laravel
php artisan serve
```
Akses aplikasi melalui browser: **`http://127.0.0.1:8000`** *(atau `http://shift-absensi.test` di Laragon)*.

---

## 4. Akun Uji Coba per Role (Hak Akses)

Di halaman login telah disediakan tombol instan (**Quick Demo Login**) untuk mempermudah demonstrasi sistem:

| Role | NIK | Nama | Departemen | Password | Deskripsi Wewenang & Tanggung Jawab |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Admin HR** | `EMP-0001` | **Subani** | Personalia (PRS) | `password` | Administrator penuh: kelola master data karyawan, konfigurasi pola rotasi, generate jadwal, koreksi absensi ber-alasan, dan audit log. |
| **Direktur** | `EMP-0002` | **Jerry** | Direksi (PRS) | `password` | Eksekutif: monitoring makro seluruh departemen pabrik, kalender jadwal, dan unduh rekap bulanan lintas divisi. |
| **HOD PRD** | `EMP-0004` | **Satrio** | Produksi (PRD) | `password` | Kepala Divisi: approval tahap 2 (cuti $>3$ hari), monitoring absensi departemennya, dan audit logbook shift. |
| **Spv PRD** | `EMP-0009` | **Gilang** | Produksi (PRD) | `password` | Pengawas Lini: approval tahap 1 (izin/cuti $\le 3$ hari, tukar shift), input buku serah terima (Logbook Shift), dan monitoring tim. |
| **Operator** | `EMP-0013` | **Sarwan** | Produksi (PRD) | `password` | Lini Lapangan: absensi mandiri, cek kalender shift pribadi, ajukan izin/cuti, dan ajukan tukar shift ke sesama operator. |

*(PIN default seluruh akun untuk presensi: **`123456`**)*

---

## 5. Menjalankan Automated Test Suite

Sistem telah dilengkapi dengan test suite lengkap berbasis PHPUnit yang menguji skenario kunci (BR-01 s/d BR-15):
```bash
php artisan test
```

---

## 6. Referensi Dokumen Perancangan

Dokumentasi lengkap perancangan sistem tersedia di folder `docs/`:
- [`docs/BRD.md`](docs/BRD.md): *Business Requirements Document*
- [`docs/PRD.md`](docs/PRD.md): *Product Requirements Document*
- [`docs/SRS.md`](docs/SRS.md): *Software Requirements Specification*
- [`docs/DATABASE_DESIGN.md`](docs/DATABASE_DESIGN.md): Desain Database & Analisis Skema
- [`docs/ERD.md`](docs/ERD.md): Dokumentasi ERD Mermaid
- [`docs/USE_CASE.md`](docs/USE_CASE.md): Use Case Diagram (5 Aktor & Matriks Hak Akses)
- [`docs/LAPORAN_AKHIR.md`](docs/LAPORAN_AKHIR.md): Laporan Akhir Proyek

---
*SIM-Shift DTY Indorama &bull; Divisi DTY PT Indo-Rama Synthetics Tbk*
