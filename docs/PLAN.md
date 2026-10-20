# PLAN - Rencana Pengerjaan, Pengujian, dan Konfirmasi

Proyek: Tugas Project Manufaktur | Versi 1.0 | 20 Oktober 2026

Catatan: penomoran bab mengikuti dokumen rancangan lengkap (Bab 8 dan Bab 9).

## 8. Rencana Pengerjaan dan Pengujian

### 8.1 Fase pengerjaan

Estimasi 6 sampai 7 minggu jika dikerjakan sendiri; sesuaikan dengan tenggat tugas dan kerjakan prioritas Must lebih dulu.

| Fase | Isi | Estimasi |
|---|---|---|
| 0 | Observasi dan konfirmasi (Bab 9), finalisasi skema | 0,5 minggu |
| 1 | Setup project, migration, seeder dummy, login dan role | 1 minggu |
| 2 | Master data dan pola rotasi | 1 minggu |
| 3 | Generator jadwal, kalender, publish | 1 minggu |
| 4 | Absensi kiosk, perhitungan status, job ALPA | 1 minggu |
| 5 | Pengajuan izin, sakit, cuti, approval; lalu tukar shift, lembur, logbook | 1,5 minggu |
| 6 | Dashboard, rekap, export Excel dan PDF | 1 minggu |
| 7 | Testing, perbaikan, dokumentasi, persiapan demo | 0,5 sampai 1 minggu |

### 8.2 Skenario pengujian kunci

| No | Skenario | Hasil yang diharapkan |
|---|---|---|
| 1 | Generate pola 4 grup selama 30 hari | Setiap hari S1, S2, S3 terisi satu grup dan satu grup OFF |
| 2 | Karyawan tanpa grup | GS Senin sampai Jumat, Sabtu dan Minggu OFF |
| 3 | S3: masuk 22:45 tanggal 1, pulang 07:05 tanggal 2 | Satu record dengan tanggal_shift 1, status HADIR |
| 4 | S1 (07:00): masuk 07:25 dengan toleransi 10 menit | Status TERLAMBAT, menit_terlambat 25 |
| 5 | Operator absen pada hari OFF | Ditolak dengan pesan jelas |
| 6 | Tidak absen sampai batas; kasus lain dengan cuti disetujui | ALPA; kasus cuti menjadi CUTI |
| 7 | Tukar shift yang membuat S3 disusul S1 keesokan harinya | Ditolak karena istirahat kurang dari 8 jam |
| 8 | Supervisor departemen A membuka data departemen B | Ditolak (403) |
| 9 | Koreksi absensi tanpa alasan, lalu dengan alasan | Pertama ditolak, kedua berhasil dan tercatat di audit_log |
| 10 | Operator membuka jadwal berstatus DRAFT | Tidak terlihat |

### 8.3 Deliverable untuk tugas

- Dokumen rancangan ini sebagai dasar laporan (latar belakang, analisis kebutuhan, perancangan).
- ERD (gambar dari docs/DATABASE_DESIGN.md memakai dbdiagram.io atau draw.io) dan use case diagram (aktor: Operator, Supervisor, HOD, Direktur, Admin HR).
- Source code dan database dummy, serta demo aplikasi.
- Slide presentasi atau video demo singkat yang menunjukkan alur: buat jadwal, publish, absen, izin, approval, rekap.

## 9. Hal yang Perlu Dikonfirmasi

Jika belum sempat bertanya ke perusahaan atau dosen, pakai asumsi pada dokumen dan tandai di laporan sebagai asumsi.

1. Jam kerja asli tiap shift (GS, S1, S2, S3) dan toleransi keterlambatan.
2. Jumlah dan nama grup sebenarnya (Grup A sampai D, atau Operator 1 sampai 7), serta pola rotasi dan panjang siklusnya.
3. Hari kerja karyawan GS: Senin sampai Jumat atau Senin sampai Sabtu.
4. Cara absensi saat ini (kertas, fingerprint, Excel) dan apakah ada data yang perlu diimpor.
5. Aturan cuti dan izin: kuota, jenis izin, dan siapa yang menyetujui.
6. Aturan tukar shift dan lembur, serta istirahat minimal antar shift.
7. Format rekap yang dibutuhkan HR atau payroll (kolom Excel).
8. Perangkat dan jaringan di lapangan: tersedia tablet atau PC dan WiFi di area kerja atau tidak.
9. Apakah boleh memakai data riil atau harus data dummy, dan apakah dosen mewajibkan stack tertentu.
