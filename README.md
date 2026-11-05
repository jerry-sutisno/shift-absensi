PEDOMAN PENGGUNAAN & PANDUAN DEMO APLIKASI
SIM-Shift DTY Indorama • Sistem Manajemen Shift & Presensi Pabrik
1. CARA MENJALANKAN APLIKASI (PERSIAPAN SEBELUM DEMO)
Pastikan aplikasi dijalankan dengan langkah-langkah mudah berikut:
a.	Langkah 1 (Laragon): Buka Laragon, lalu klik tombol 'Start All' (pastikan MySQL & Apache/Nginx menyala hijau).
b.	Langkah 2 (Buka Terminal): Buka terminal di folder project (d:\laragon\www\shift-absensi).
c.	Langkah 3 (Jalankan Server): Ketik perintah: php artisan serve lalu tekan Enter.
d.	Langkah 4 (Buka Browser): Buka browser Chrome/Edge dan ketik alamat: http://127.0.0.1:8000 (atau http://shift-absensi.test).
e.	Reset Database (Opsional): Jika ingin membersihkan database agar kembali bersih seperti semula sebelum demo, jalankan: php artisan migrate:fresh --seed

---

## Fitur Utama

1. **Absensi Mandiri di Dashboard**:
   - Begitu login (Operator, SPV, HOD, Direktur, Admin), karyawan bisa langsung klik **Absen Masuk** dan **Absen Pulang** dari dashboard masing-masing.
   - Status presensi dan keterlambatan (dalam menit) dihitung otomatis oleh sistem.

2. **Jadwal Rotasi Shift Otomatis (4 Regu 8 Hari)**:
   - Pola kerja rotasi untuk 4 grup (Grup A, B, C, D) dengan siklus 8 hari (Shift 1 Pagi, Shift 2 Siang, Shift 3 Malam, dan Libur/OFF).
   - Generate jadwal bulanan sekali klik tanpa bentrok.

3. **Shift Malam Lintas Hari**:
   - Shift 3 (23:00 - 07:00) yang melewati tengah malam tetap tercatat rapi pada tanggal shift yang bersangkutan berdasarkan waktu server (Asia/Jakarta).

4. **Tukar Shift & Approval Berjenjang**:
   - Operator hanya bisa mengajukan tukar shift dengan rekan **satu divisi dan setara jabatan**.
   - Cuti biasa (1-3 hari) disetujui Supervisor.
   - Cuti panjang (> 3 hari) otomatis naik ke HOD.

5. **Logbook Shift (Serah Terima Handover)**:
   - Catatan ringkasan kerja, kendala mesin/material, dan tindak lanjut antar regu shift kerja.

6. **Koreksi Absensi & Audit Log**:
   - HR bisa koreksi absensi manual dengan syarat wajib mengisi alasan tertulis.
   - Semua riwayat perubahan tersimpan di menu Audit Log.

7. **Rekap & Laporan**:
   - Rekap absensi bulanan dan ekspor ke **PDF** dan **Excel**.

---

## Akun Demo untuk Pengujian

Di halaman login sudah ada tombol instan **Quick Demo Login**. Tinggal klik tombol role yang mau dicoba:

| Role | NIK | Nama | Password | Tugas Utama di Sistem |
| :--- | :--- | :--- | :--- | :--- |
| **Admin HR** | `EMP-0001` | Subani | `password` | Kelola karyawan, pola shift, generate jadwal, koreksi absen, audit log. |
| **Direktur** | `EMP-0002` | Jerry | `password` | Pantau dashboard makro semua departemen, kalender jadwal, rekap bulanan. |
| **HOD PRD** | `EMP-0004` | Satrio | `password` | Approval cuti panjang (> 3 hari), pantau absensi & logbook divisinya. |
| **Supervisor** | `EMP-0009` | Gilang | `password` | Approval izin/cuti biasa & tukar shift, isi Logbook Shift timnya. |
| **Operator** | `EMP-0013` | Sarwan | `password` | Absen masuk/pulang, cek jadwal sendiri, ajukan izin/cuti/tukar shift. |

*(PIN default untuk absensi: **`123456`**)*

---

## Cara Menjalankan Project

1. Buka Laragon, klik **Start All** (Apache & MySQL).
2. Buka terminal di folder project:
   ```bash
   cd d:\laragon\www\shift-absensi
   ```
3. Setup database dan seeder data awal:
   ```bash
   php artisan migrate:fresh --seed
   ```
4. Build asset frontend:
   ```bash
   npm run build
   ```
5. Jalankan server:
   ```bash
   php artisan serve
   ```
6. Buka browser di: **`http://127.0.0.1:8000`** *(atau `http://shift-absensi.test`)*.

---

## Dokumen Tambahan
Dokumentasi teknis ada di folder `docs/`:
- `docs/BRD.md` & `docs/PRD.md` (Kebutuhan Bisnis & Produk)
- `docs/DATABASE_DESIGN.md` & `docs/ERD.md` (Desain Database & Relasi Tabel)
- `docs/USE_CASE.md` (Diagram Use Case & Hak Akses)
- `docs/LAPORAN_AKHIR.md` (Laporan Proyek)
