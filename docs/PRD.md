# PRD - Sistem Manajemen Shift & Absensi Operator

Proyek: Tugas Project Manufaktur | Versi 1.0 | 20 Oktober 2026

Catatan: penomoran bab mengikuti dokumen rancangan lengkap (Bab 3).

## 3. PRD (Product Requirements Document)

### 3.1 Visi produk

Satu aplikasi web sederhana yang membuat setiap orang di lantai produksi tahu kapan dia bekerja, bisa mencatat kehadirannya dalam hitungan detik, dan membuat supervisor serta HR tidak lagi merekap manual.

### 3.2 Role dan hak akses

Role diturunkan dari level_jabatan pada database perusahaan; role Admin HR diberikan ke karyawan departemen Personalia (PRS) yang ditunjuk.

| Role | Sumber | Hak akses utama |
|---|---|---|
| Admin HR | Departemen PRS (ditunjuk) | Kelola master data dan akun, atur pola dan jadwal semua departemen, koreksi absensi, rekap dan export semua departemen, pengaturan sistem |
| Direktur | level DIREKTUR | Dashboard dan laporan semua departemen (read-only) |
| HOD | level HOD | Dashboard dan laporan departemennya, approval tahap akhir (cuti panjang), melihat jadwal departemen |
| Supervisor | level SUPERVISOR | Menyusun dan mempublish jadwal departemennya, approval izin dan tukar shift, koreksi absensi timnya, mengisi logbook |
| Operator | level OPERATOR | Absen, melihat jadwal sendiri, mengajukan izin, sakit, cuti, tukar shift, lembur, melihat riwayat |

### 3.3 Daftar fitur (MoSCoW)

| ID | Fitur | Prioritas |
|---|---|---|
| F-01 | Login, logout, ganti password, hak akses berbasis role | Must |
| F-02 | CRUD master data: karyawan, departemen, jabatan, grup, shift | Must |
| F-03 | Pola rotasi dan generator jadwal bulanan per grup | Must |
| F-04 | Kalender jadwal (harian, mingguan, bulanan) per departemen, grup, dan pribadi | Must |
| F-05 | Absensi masuk dan pulang via kiosk (ID + PIN atau scan QR kartu) | Must |
| F-06 | Penentuan status otomatis: hadir, terlambat, pulang cepat, alpa | Must |
| F-07 | Pengajuan izin, sakit, cuti dengan approval berjenjang | Must |
| F-08 | Dashboard kehadiran real-time per shift dan departemen | Must |
| F-09 | Rekap bulanan per karyawan dan departemen, export Excel dan PDF | Must |
| F-10 | Tukar shift antar operator dengan approval | Should |
| F-11 | Koreksi absensi manual dengan alasan wajib dan audit log | Should |
| F-12 | Pengajuan dan rekap lembur | Should |
| F-13 | Logbook serah terima shift | Should |
| F-14 | Notifikasi dalam aplikasi | Should |
| F-15 | Hari libur nasional dan perusahaan | Should |
| F-16 | Absen mandiri dari HP dengan QR dinamis | Could |
| F-17 | Import karyawan dari Excel | Could |
| F-18 | Selfie saat absen | Could |
| F-19 | PWA (bisa dipasang di HP) | Could |
| F-20 | Payroll, fingerprint/face recognition, aplikasi native | Won't (v1) |

### 3.4 Alur utama

A. Penyusunan jadwal bulanan

1. Admin HR atau Supervisor memilih departemen, bulan, dan pola rotasi.
2. Sistem membuat jadwal DRAFT untuk semua karyawan aktif: karyawan dengan grup mengikuti pola; karyawan tanpa grup otomatis GS (Senin sampai Jumat), Sabtu dan Minggu OFF.
3. Supervisor meninjau kalender, mengubah manual jika perlu (misalnya karyawan cuti), lalu menekan Publish.
4. Operator menerima notifikasi dan bisa melihat jadwalnya.

B. Absensi harian

1. Operator tiba, membuka kiosk, memasukkan ID karyawan + PIN (atau scan QR kartu).
2. Sistem mencari jadwal operator pada tanggal shift yang relevan, memeriksa jendela waktu, lalu mencatat waktu masuk memakai jam server.
3. Saat pulang, operator melakukan hal yang sama; sistem mengisi waktu pulang dan menghitung status serta menit kerja.
4. Jika tidak ada absen masuk sampai batas waktu, job terjadwal menandai ALPA (kecuali ada izin yang disetujui).

C. Izin, sakit, cuti

1. Operator mengisi form (jenis, tanggal, alasan, lampiran opsional).
2. Supervisor menyetujui atau menolak; untuk cuti lebih dari batas hari (diatur di pengaturan) dilanjutkan ke HOD.
3. Jika disetujui, absensi pada tanggal terkait otomatis berstatus IZIN, SAKIT, atau CUTI.

D. Tukar shift

1. Operator A memilih tanggal dan operator B (satu departemen) untuk bertukar shift.
2. Operator B menerima atau menolak, lalu Supervisor memberi persetujuan akhir.
3. Sistem memvalidasi (tidak bentrok, istirahat minimal), lalu menukar jadwal kedua karyawan dan menandai sumber TUKAR_SHIFT.

E. Rekap bulanan

1. Admin HR memilih bulan dan departemen.
2. Sistem menampilkan rekap hadir, terlambat, alpa, izin, sakit, cuti, lembur, dan total jam kerja per karyawan, lalu export ke Excel atau PDF.

### 3.5 User story dan kriteria penerimaan

| ID | Sebagai | Saya ingin | Kriteria penerimaan |
|---|---|---|---|
| US-01 | Operator | melihat jadwal shift saya bulan ini | Kalender pribadi tampil dengan warna per shift; hanya jadwal PUBLISHED yang terlihat |
| US-02 | Operator | absen masuk dengan cepat | Proses kurang dari 10 detik; muncul konfirmasi nama, shift, dan jam |
| US-03 | Operator | absen pulang di shift malam | Pulang setelah tengah malam tercatat pada tanggal shift yang sama dengan absen masuk |
| US-04 | Operator | mengajukan izin atau sakit | Pengajuan berstatus DIAJUKAN dan muncul di daftar approval supervisor |
| US-05 | Operator | tukar shift dengan rekan | Pengajuan ditolak otomatis jika menyebabkan dua shift di satu tanggal atau istirahat kurang dari batas |
| US-06 | Supervisor | membuat jadwal sebulan sekaligus | Jadwal DRAFT terbentuk dari pola; bisa diedit sebelum Publish |
| US-07 | Supervisor | melihat siapa yang sudah hadir di shift ini | Dashboard menampilkan hadir, belum hadir, dan terlambat secara real-time |
| US-08 | Supervisor | menyetujui atau menolak pengajuan | Keputusan tercatat beserta waktu dan catatan |
| US-09 | Supervisor | mengisi logbook serah terima shift | Catatan tersimpan dan terlihat oleh supervisor shift berikutnya |
| US-10 | HOD | memantau kehadiran departemen | Grafik dan tabel per shift dan per hari untuk departemennya saja |
| US-11 | Admin HR | mengoreksi absensi yang salah | Koreksi wajib berisi alasan dan tercatat di audit log |
| US-12 | Admin HR | mengunduh rekap bulanan | File Excel dan PDF sesuai filter bulan dan departemen |
| US-13 | Direktur | melihat ringkasan seluruh departemen | Dashboard read-only berisi persentase kehadiran per departemen |
