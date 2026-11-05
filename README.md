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

## SKENARIO ALUR DEMO PRESENTASI (PEMBAGIAN 3 ORANG)
Orang 1: Pembuka & Konsep (Latar Belakang & Arsitektur)
- Poin Bicara 1: Jelaskan latar belakang: Masalah operasional pabrik Divisi DTY PT Indo-Rama Synthetics Tbk yang sebelumnya manual (kertas, papan tulis, rawan bentrok jadwal rotasi shift).
- Poin Bicara 2: Perkenalkan SIM-Shift: Solusi berbasis Laravel 11, Livewire 3, dan Tailwind CSS dengan basis waktu server.
- Poin Bicara 3: Tunjukkan skema pola rotasi 4 regu 8 hari yang adil dan otomatis

Orang 2: Driver Demo 1 (Operator & Supervisor)
- Simulasi Operator (Sarwan): Login sebagai Sarwan -> Perlihatkan Card Presensi -> Klik 'Absen Masuk' -> Tunjukkan jadwal kalender -> Ajukan Tukar Shift dengan rekan se-divisi.
- Simulasi Supervisor (Gilang): Login sebagai Gilang -> Buka 'Antrean Approval' -> Setujui pengajuan tukar shift -> Buka 'Logbook Shift' -> Tunjukkan catatan serah terima mesin.

Orang 3: Driver Demo 2 & Penutup (HOD, Direktur & Admin HR)
- Simulasi HOD & Direktur: Login HOD (Satrio) untuk approval cuti panjang & pantau tim. Lalu login Direktur (Jerry) untuk perlihatkan Dashboard Eksekutif makro lintas 5 departemen.
- Simulasi Admin HR (Subani): Perlihatkan menu Koreksi Absensi (wajib alasan), cek riwayat Audit Log, dan unduh Rekap Bulanan dalam format PDF / Excel
- Penutup: Tegaskan kesimpulan sistem dan buka sesi tanya jawab (Q&A).
