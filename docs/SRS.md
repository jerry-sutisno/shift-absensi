# SRS - Sistem Manajemen Shift & Absensi Operator

Proyek: Tugas Project Manufaktur | Versi 1.0 | 20 Oktober 2026

Catatan: penomoran bab mengikuti dokumen rancangan lengkap (Bab 4).

## 4. SRS (Software Requirements Specification)

### 4.1 Definisi

| Istilah | Arti |
|---|---|
| Shift | Blok jam kerja: GS, S1, S2, S3, OFF |
| Grup | Kelompok operator yang berotasi bersama |
| Pola rotasi | Urutan shift per grup yang berulang dalam satu siklus hari |
| Tanggal shift | Tanggal dimulainya shift (shift malam yang pulang esok hari tetap dicatat pada tanggal mulai) |
| Kiosk | Perangkat tetap (tablet/PC) di area kerja untuk absen |

### 4.2 Kebutuhan fungsional

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-01 | Sistem mengautentikasi pengguna dengan id_karyawan dan password | Must |
| FR-02 | Sistem membatasi akses menu dan data berdasarkan role dan departemen | Must |
| FR-03 | Admin HR dapat menambah, mengubah, menonaktifkan, dan mencari karyawan | Must |
| FR-04 | Admin HR dapat mengelola departemen, jabatan, grup, dan shift | Must |
| FR-05 | Sistem memvalidasi relasi: jabatan harus berada di departemen yang sama dengan karyawan | Must |
| FR-06 | Sistem menyediakan pola rotasi yang dapat diatur per grup dan siklus hari | Must |
| FR-07 | Sistem membuat jadwal DRAFT otomatis untuk rentang tanggal dan departemen tertentu | Must |
| FR-08 | Sistem menjadwalkan karyawan tanpa grup sebagai GS Senin sampai Jumat dan OFF di akhir pekan | Must |
| FR-09 | Supervisor dapat mengubah shift per karyawan per tanggal sebelum dipublish | Must |
| FR-10 | Sistem menolak jadwal ganda untuk satu karyawan pada tanggal yang sama | Must |
| FR-11 | Sistem menampilkan kalender jadwal dengan filter departemen, grup, shift, dan karyawan | Must |
| FR-12 | Sistem menyediakan halaman kiosk untuk absen masuk dan pulang | Must |
| FR-13 | Sistem memakai waktu server (zona Asia/Jakarta) untuk semua pencatatan absen | Must |
| FR-14 | Sistem menentukan status HADIR, TERLAMBAT, dan menandai pulang cepat sesuai aturan bisnis | Must |
| FR-15 | Sistem menandai ALPA otomatis melalui job terjadwal | Must |
| FR-16 | Operator dapat mengajukan CUTI, IZIN, dan SAKIT dengan lampiran opsional | Must |
| FR-17 | Sistem merutekan approval sesuai level jabatan dan departemen | Must |
| FR-18 | Pengajuan disetujui otomatis memperbarui status absensi tanggal terkait | Must |
| FR-19 | Dashboard menampilkan jumlah hadir, belum hadir, terlambat, dan izin per shift saat ini | Must |
| FR-20 | Sistem menghasilkan rekap bulanan dan export Excel dan PDF | Must |
| FR-21 | Operator dapat mengajukan tukar shift; sistem memvalidasi sebelum persetujuan | Should |
| FR-22 | Admin HR dan Supervisor dapat mengoreksi absensi dengan alasan wajib | Should |
| FR-23 | Sistem mencatat semua perubahan penting ke audit log | Should |
| FR-24 | Operator dapat mengajukan lembur; supervisor menyetujui; rekap lembur tersedia | Should |
| FR-25 | Supervisor dapat membuat logbook serah terima per shift | Should |
| FR-26 | Sistem mengirim notifikasi dalam aplikasi untuk publish jadwal dan keputusan approval | Should |
| FR-27 | Admin HR dapat mengelola hari libur dan pengaturan sistem (toleransi, jendela absen, dll.) | Should |
| FR-28 | Absen mandiri dari HP memakai QR dinamis yang berganti tiap 30 detik | Could |
| FR-29 | Import karyawan dari file Excel dengan validasi dan laporan error | Could |

### 4.3 Aturan bisnis

| ID | Aturan |
|---|---|
| BR-01 | Karyawan dengan id_grup NULL adalah karyawan non-rotasi: default GS Senin sampai Jumat, Sabtu dan Minggu OFF |
| BR-02 | Satu karyawan hanya memiliki satu jadwal per tanggal (UNIQUE id_karyawan + tanggal) |
| BR-03 | Tanggal shift adalah tanggal mulai shift. Contoh: S3 mulai 23:00 tanggal 1 dan pulang 07:00 tanggal 2 dicatat pada tanggal shift 1 |
| BR-04 | Absen masuk dibuka N menit sebelum jam_masuk (default 60 menit, dapat diatur) dan ditutup saat jam_pulang |
| BR-05 | TERLAMBAT jika waktu masuk lebih dari jam_masuk + toleransi (default 10 menit); menit_terlambat dihitung dari jam_masuk |
| BR-06 | Pulang cepat jika waktu pulang kurang dari jam_pulang |
| BR-07 | ALPA jika tidak ada absen masuk sampai jam_pulang + 30 menit dan tidak ada izin, sakit, atau cuti yang disetujui |
| BR-08 | Shift OFF tidak memerlukan absensi |
| BR-09 | Hanya jadwal berstatus PUBLISHED yang terlihat oleh Operator; jadwal tanggal lampau terkunci kecuali oleh Admin HR |
| BR-10 | Tukar shift: kedua karyawan satu departemen, tidak menghasilkan dua shift pada satu tanggal, dan istirahat antar shift minimal 8 jam (dapat diatur) |
| BR-11 | Approval berjenjang: Operator, Supervisor, lalu HOD jika cuti melebihi batas hari (default lebih dari 3 hari). Approver harus satu departemen dan berlevel lebih tinggi |
| BR-12 | Koreksi absensi wajib berisi alasan dan tercatat di audit log (data lama dan baru) |
| BR-13 | Karyawan NONAKTIF tidak dapat login dan tidak dimasukkan ke jadwal baru |
| BR-14 | Satu karyawan memiliki paling banyak satu akun pengguna |
| BR-15 | Waktu absensi diambil dari server, bukan dari perangkat pengguna |

### 4.4 Kebutuhan non-fungsional

| Kategori | Kebutuhan |
|---|---|
| Keamanan | Password bcrypt, PIN absen di-hash, proteksi CSRF dan XSS, rate limit login, otorisasi lewat Policy per role dan departemen |
| Performa | Halaman utama kurang dari 2 detik untuk sekitar 500 karyawan; indeks pada tanggal, id_karyawan, kode_shift |
| Usability | Mobile-first untuk operator, tombol besar dan kontras tinggi untuk kiosk, bahasa Indonesia |
| Keandalan | Backup database harian; job terjadwal idempoten (aman dijalankan ulang) |
| Auditabilitas | Perubahan jadwal, koreksi absensi, dan keputusan approval tercatat |
| Kompatibilitas | Chrome, Edge, Firefox, dan browser HP terbaru; kamera butuh HTTPS untuk scan QR |
| Lokalisasi | Zona waktu Asia/Jakarta, format tanggal DD-MM-YYYY, jam 24 jam |
