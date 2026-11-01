# UI/UX & ARSITEKTUR - Sistem Manajemen Shift & Absensi Operator

Proyek: Tugas Project Manufaktur | Versi 1.0 | 20 Oktober 2026

Catatan: penomoran bab mengikuti dokumen rancangan lengkap sistem (Bab 6 dan Bab 7). Desain antarmuka dirancang untuk kebutuhan operasional pabrik manufaktur.

## 6. Rancangan UI/UX

### 6.1 Prinsip desain

- Mobile-first untuk Operator; kiosk dirancang landscape tablet; desktop untuk Supervisor, HOD, dan Admin HR.
- Kontras tinggi dan tombol minimal 48 px karena dipakai di lantai produksi (pencahayaan beragam, kadang memakai sarung tangan).
- Warna shift dan status selalu disertai label teks, tidak hanya warna.
- Aksi utama (absen, lihat jadwal, ajukan izin) maksimal 3 klik.
- Bahasa Indonesia yang sederhana, istilah sama dengan yang dipakai di lapangan (S1, S2, S3, GS, OFF).

### 6.2 Sitemap per role

| Role | Menu |
|---|---|
| Operator | Beranda, Jadwal Saya, Riwayat Absensi, Pengajuan (Izin/Sakit/Cuti, Tukar Shift, Lembur), Notifikasi, Profil |
| Supervisor | Dashboard, Jadwal (Kalender, Buat Jadwal), Absensi Tim, Approval, Logbook Shift, Laporan Tim |
| HOD | Dashboard Departemen, Jadwal Departemen, Approval, Laporan Departemen |
| Direktur | Dashboard Eksekutif, Laporan |
| Admin HR | Semua menu di atas ditambah Master Data, Pola Rotasi, Pengguna, Rekap dan Export, Hari Libur, Pengaturan, Audit Log |
| Kiosk | Halaman /kiosk terkunci per perangkat, hanya untuk absen |

### 6.3 Halaman utama

| Halaman | Pengguna | Komponen utama |
|---|---|---|
| Login | Semua | Input ID karyawan dan password, tombol masuk |
| Kiosk Absensi | Operator | Jam besar, input ID dan PIN atau scan QR, tombol Masuk dan Pulang, kartu konfirmasi (nama, shift, jam) |
| Beranda Operator | Operator | Kartu shift berikutnya, status absen hari ini, mini kalender, aksi cepat (Ajukan Izin, Tukar Shift) |
| Dashboard Supervisor | Supervisor, HOD | Kartu ringkasan (hadir, belum hadir, terlambat, izin), daftar kehadiran shift berjalan, antrean approval |
| Kalender Jadwal | Supervisor, HR | Grid bulanan atau mingguan, chip warna per shift, filter departemen, grup, dan shift, tombol Publish |
| Pola Rotasi dan Generator | Admin HR, Supervisor | Tabel pola per grup dan hari, form generate (departemen, bulan, pola), pratinjau jadwal |
| Pengajuan dan Approval | Semua | Daftar pengajuan dengan filter status, panel detail, tombol Setuju dan Tolak dengan catatan |
| Rekap Bulanan | Admin HR, HOD, Direktur | Filter bulan dan departemen, tabel rekap, grafik, tombol Export Excel dan PDF |
| Master Data Karyawan | Admin HR | Tabel dengan pencarian dan filter, form tambah dan ubah, tombol nonaktifkan |
| Logbook Shift | Supervisor | Form ringkasan, kendala, tindak lanjut, daftar catatan shift sebelumnya |

### 6.4 Kode warna

| Elemen | Warna | Hex |
|---|---|---|
| Shift GS | Biru | #2563EB |
| Shift S1 (Pagi) | Kuning amber | #F59E0B |
| Shift S2 (Siang) | Oranye | #EA580C |
| Shift S3 (Malam) | Indigo | #4338CA |
| OFF | Abu-abu | #94A3B8 |
| Hadir | Hijau | #16A34A |
| Terlambat | Kuning tua | #CA8A04 |
| Alpa | Merah | #DC2626 |
| Izin, Sakit, Cuti | Biru langit | #0284C7 |
| Warna utama UI | Biru industri | #0F4C81 |

## 7. Arsitektur dan Teknologi

### 7.1 Tech stack yang disarankan

| Lapisan | Pilihan | Alasan |
|---|---|---|
| Backend | Laravel (versi stabil terbaru, minimal 11) | Auth, Policy untuk RBAC, Scheduler, validasi, dan migration sudah tersedia; cocok untuk aplikasi CRUD dengan banyak aturan |
| Database | MySQL 8 | Selaras dengan tipe ENUM dan TIMESTAMP di skema Excel perusahaan |
| Frontend | Blade + Livewire + Alpine.js + Tailwind CSS | Interaktif tanpa SPA terpisah, cepat dikembangkan |
| Library | maatwebsite/excel, barryvdh/laravel-dompdf, simplesoftwareio/simple-qrcode, Chart.js | Export Excel, PDF, QR, dan grafik |
| Job terjadwal | Laravel Scheduler | Menandai ALPA berkala dan mengirim notifikasi |
| Deploy | Laragon atau XAMPP untuk lokal; hosting dengan HTTPS untuk demo | Kamera scan QR membutuhkan HTTPS |

Jika dosen mensyaratkan stack lain (misalnya CodeIgniter atau Next.js), skema database, aturan bisnis, dan alur tetap berlaku.

### 7.2 Metode absensi

| Metode | Cara kerja | Pencegahan titip absen |
|---|---|---|
| Kiosk ID + PIN (Must) | Tablet atau PC di pos area kerja; operator memasukkan ID (atau scan QR kartu) lalu PIN 6 digit | PIN pribadi, waktu server, audit log, supervisor melihat daftar hadir real-time |
| HP + QR dinamis (Could) | QR di layar kiosk berganti tiap 30 detik; operator login di HP lalu scan QR | Token kedaluwarsa cepat, harus login akun sendiri |

### 7.3 Struktur folder yang disarankan

```
app/
  Http/Controllers/        (tipis, hanya koordinasi)
  Livewire/                (komponen interaktif: kalender, kiosk, approval)
  Models/                  (Karyawan, Departemen, Jabatan, GrupKaryawan, Shift, JadwalShift, Absensi, Pengajuan, TukarShift, Lembur, LogbookShift, PolaRotasi, ...)
  Policies/                (aturan akses per role dan departemen)
  Services/                (ScheduleGeneratorService, AttendanceService, ApprovalService, SwapShiftService, ReportService)
  Console/Commands/        (MarkAbsentCommand)
database/migrations/       (satu tabel satu file, urut sesuai FK)
database/seeders/          (data dummy: departemen, jabatan, shift, grup, karyawan, pola)
resources/views/           (layouts, kiosk, operator, supervisor, admin)
routes/web.php
tests/Feature/             (alur jadwal, absensi, approval, otorisasi)
```
