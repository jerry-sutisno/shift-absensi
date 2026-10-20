# DATABASE DESIGN - Sistem Manajemen Shift & Absensi Operator

Proyek: Tugas Project Manufaktur | Versi 1.0 | 20 Oktober 2026

Catatan: penomoran bab mengikuti dokumen rancangan lengkap (Bab 1 dan Bab 5). Ini sumber kebenaran skema database.

## 1. Analisis Database Perusahaan

### 1.1 Isi file Excel (sheet Master)

| Tabel | Fungsi | Kolom | Catatan dari file |
|---|---|---|---|
| Karyawan | Data induk operator dan staf | id_karyawan, nama_karyawan, jns_kelamin (L/P), tgl_lahir, id_departemen, id_jabatan, id_grup, created_at | id_grup bernilai NULL jika bukan karyawan shift grup |
| Departemen | Divisi perusahaan | id_departemen, nama_departemen, kode_departemen, deskripsi, created_at | Personalia (PRS), Logistik (LGS), Produksi (PRD), QCT, Packing (PCKG) |
| Jabatan | Posisi dan level | id_jabatan, nama_jabatan, id_departemen, level_jabatan, created_at | Level: Direktur, HOD, Supervisor, Operator |
| Grup | Kelompok kerja rotasi | id_grup, nama_grup, keterangan | Contoh Grup A, Grup B, dst. atau Operator 1 - 7 |
| Shift | Definisi shift | kode_shift, nama_shift, jam_masuk, jam_pulang | GS (General Shift), S1 (Pagi), S2 (Siang), S3 (Malam), OFF (Libur); jam NULL jika libur |

### 1.2 Temuan dan keputusan perbaikan

| No | Temuan | Keputusan di rancangan |
|---|---|---|
| 1 | Judul tabel tertulis Tabel Karywan (typo) | Dinamai karyawan |
| 2 | Keterangan id_karyawan tertulis Kode Departemen, misal DEP-01 (salah salin dari tabel departemen) | id_karyawan = nomor induk karyawan perusahaan, VARCHAR(20), contoh EMP-0001 |
| 3 | Tipe id_departemen tidak konsisten (INT/VARCHAR; di karyawan Varchar(20), di jabatan INT) | Diseragamkan INT UNSIGNED AUTO_INCREMENT di semua tabel; kode_departemen tetap kode bisnis (PRS, LGS, dst.) |
| 4 | Pada baris id_grup di tabel karyawan, kolom keterangan berisi Status kepegawaian saat ini (tidak cocok, kemungkinan kolom status terlewat) | Ditambah kolom status_karyawan ENUM(AKTIF, NONAKTIF) |
| 5 | Tabel shift belum punya primary key yang jelas | kode_shift (VARCHAR 10) dijadikan PK |
| 6 | Tidak ada penanda shift yang pulang di hari berikutnya (S3) | Ditambah kolom lintas_hari |
| 7 | Contoh nama grup ambigu: Grup A, B, dst. atau Operator 1 - 7 | Jumlah dan nama grup dibuat sebagai data (bukan hardcode); dikonfirmasi di Bab 9 |
| 8 | Belum ada tabel jadwal, absensi, pengajuan, dan akun login | Dirancang baru di Bab 5 |
| 9 | Contoh nama jabatan di tabel jabatan (Senior Software Engineer, HR Manager) terlihat seperti template umum | Level jabatan tetap dipakai sebagai dasar role; nama jabatan mengikuti data asli perusahaan |

### 1.3 Pemetaan database ke kebutuhan lapangan

| Kolom dari database perusahaan | Dipakai untuk |
|---|---|
| level_jabatan | Menentukan role dan hak approval (Direktur, HOD, Supervisor, Operator) |
| id_departemen | Membatasi data yang boleh dilihat dan disetujui HOD dan Supervisor |
| id_grup | Menentukan siapa ikut rotasi; NULL berarti karyawan GS tetap |
| kode_shift, jam_masuk, jam_pulang | Dasar validasi absen (tepat waktu, terlambat, pulang cepat) |

## 5. Desain Database

MySQL 8 (selaras dengan tipe ENUM dan TIMESTAMP di file Excel perusahaan). Tabel bertanda (dari Excel) adalah tabel asli yang disempurnakan; kolom bertanda (baru) ditambahkan oleh rancangan ini.

### 5.1 Relasi utama

- departemen 1 - N jabatan
- departemen 1 - N karyawan; jabatan 1 - N karyawan; grup_karyawan 1 - N karyawan (opsional)
- karyawan 1 - 1 users
- shift 1 - N jadwal_shift; karyawan 1 - N jadwal_shift
- jadwal_shift 1 - 1 absensi
- pola_rotasi 1 - N pola_rotasi_detail; grup_karyawan 1 - N pola_rotasi_detail; shift 1 - N pola_rotasi_detail
- karyawan 1 - N pengajuan, tukar_shift, lembur
- departemen 1 - N logbook_shift

### 5.2 Tabel master (dari Excel, disempurnakan)

| Tabel | Kolom |
|---|---|
| departemen | id_departemen INT UNSIGNED PK AI; nama_departemen VARCHAR(50) NOT NULL; kode_departemen VARCHAR(10) UNIQUE NOT NULL; deskripsi TEXT NULL; created_at, updated_at |
| jabatan | id_jabatan INT UNSIGNED PK AI; kode_jabatan VARCHAR(20) NULL (baru, opsional: menampung kode asli perusahaan seperti JAB-01); nama_jabatan VARCHAR(50) NOT NULL; id_departemen FK; level_jabatan ENUM(DIREKTUR, HOD, SUPERVISOR, OPERATOR) NOT NULL; created_at, updated_at |
| grup_karyawan | id_grup INT UNSIGNED PK AI; nama_grup VARCHAR(50) UNIQUE NOT NULL; keterangan TEXT NULL |
| shift | kode_shift VARCHAR(10) PK; nama_shift VARCHAR(50) NOT NULL; jam_masuk TIME NULL; jam_pulang TIME NULL; lintas_hari BOOLEAN DEFAULT 0 (baru); toleransi_menit TINYINT DEFAULT 10 (baru); warna VARCHAR(7) (baru); is_libur BOOLEAN DEFAULT 0 (baru); is_aktif BOOLEAN DEFAULT 1 (baru) |
| karyawan | id_karyawan VARCHAR(20) PK; nama_karyawan VARCHAR(50) NOT NULL; jns_kelamin ENUM(L, P) NOT NULL; tgl_lahir DATE NOT NULL; id_departemen FK NOT NULL; id_jabatan FK NOT NULL; id_grup FK NULL; status_karyawan ENUM(AKTIF, NONAKTIF) DEFAULT AKTIF (baru); no_hp VARCHAR(20) NULL (baru); created_at, updated_at |

### 5.3 Tabel baru (transaksi dan pendukung)

| Tabel | Kolom dan constraint |
|---|---|
| users | id_user BIGINT PK AI; id_karyawan FK UNIQUE; password (bcrypt); pin_absen (hash, 6 digit); role ENUM(ADMIN_HR, DIREKTUR, HOD, SUPERVISOR, OPERATOR); is_active BOOLEAN; last_login_at; remember_token; timestamps |
| pola_rotasi | id_pola PK AI; nama_pola VARCHAR(50); panjang_siklus_hari TINYINT; keterangan TEXT NULL; is_aktif BOOLEAN |
| pola_rotasi_detail | id_detail PK AI; id_pola FK; id_grup FK; hari_ke TINYINT; kode_shift FK; UNIQUE(id_pola, id_grup, hari_ke) |
| jadwal_shift | id_jadwal BIGINT PK AI; id_karyawan FK; tanggal DATE; kode_shift FK; sumber ENUM(POLA, MANUAL, TUKAR_SHIFT); status ENUM(DRAFT, PUBLISHED); id_pola FK NULL; dibuat_oleh FK users; timestamps; UNIQUE(id_karyawan, tanggal); INDEX(tanggal, kode_shift) |
| absensi | id_absensi BIGINT PK AI; id_jadwal FK UNIQUE; id_karyawan FK; tanggal_shift DATE; waktu_masuk DATETIME NULL; waktu_pulang DATETIME NULL; metode_masuk, metode_pulang ENUM(KIOSK, MOBILE, MANUAL) NULL; status ENUM(HADIR, TERLAMBAT, ALPA, IZIN, SAKIT, CUTI, LIBUR); menit_terlambat SMALLINT DEFAULT 0; pulang_cepat BOOLEAN DEFAULT 0; menit_kerja SMALLINT NULL; catatan TEXT NULL; dikoreksi_oleh FK users NULL; timestamps; UNIQUE(id_karyawan, tanggal_shift) |
| pengajuan | id_pengajuan PK AI; id_karyawan FK; jenis ENUM(CUTI, IZIN, SAKIT); tgl_mulai DATE; tgl_selesai DATE; alasan TEXT; lampiran_path VARCHAR(255) NULL; status ENUM(DIAJUKAN, DISETUJUI, DITOLAK, DIBATALKAN); tahap ENUM(SUPERVISOR, HOD, SELESAI); id_approver FK users NULL; catatan_approver TEXT NULL; keputusan_at DATETIME NULL; timestamps |
| tukar_shift | id_tukar PK AI; id_pemohon FK karyawan; id_pengganti FK karyawan; id_jadwal_pemohon FK; id_jadwal_pengganti FK; alasan TEXT; status ENUM(MENUNGGU_PENGGANTI, MENUNGGU_SUPERVISOR, DISETUJUI, DITOLAK, DIBATALKAN); id_approver FK users NULL; keputusan_at DATETIME NULL; timestamps |
| lembur | id_lembur PK AI; id_karyawan FK; tanggal DATE; jam_mulai TIME; jam_selesai TIME; alasan TEXT; status ENUM(DIAJUKAN, DISETUJUI, DITOLAK); id_approver FK users NULL; menit_lembur SMALLINT NULL; timestamps |
| logbook_shift | id_logbook PK AI; tanggal DATE; kode_shift FK; id_departemen FK; id_grup FK NULL; id_pembuat FK users; ringkasan TEXT; kendala TEXT NULL; tindak_lanjut TEXT NULL; timestamps |
| hari_libur | tanggal DATE PK; keterangan VARCHAR(100); jenis ENUM(NASIONAL, PERUSAHAAN) |
| pengaturan | kunci VARCHAR(50) PK; nilai VARCHAR(255); keterangan VARCHAR(255) |
| notifikasi | id_notifikasi PK AI; id_user FK; judul VARCHAR(100); pesan TEXT; url VARCHAR(255) NULL; dibaca_at DATETIME NULL; created_at |
| audit_log | id_log BIGINT PK AI; id_user FK NULL; aksi VARCHAR(50); tabel VARCHAR(50); id_record VARCHAR(50); data_lama JSON NULL; data_baru JSON NULL; ip_address VARCHAR(45); created_at |

### 5.4 Data awal (seed)

Departemen: Personalia (PRS), Logistik (LGS), Produksi (PRD), QCT, Packing (PCKG).

Level jabatan: DIREKTUR, HOD, SUPERVISOR, OPERATOR.

Shift (jam hanya contoh, ganti dengan data perusahaan):

| kode_shift | nama_shift | jam_masuk | jam_pulang | lintas_hari |
|---|---|---|---|---|
| GS | General Shift | 08:00 | 17:00 | 0 |
| S1 | Shift Pagi | 07:00 | 15:00 | 0 |
| S2 | Shift Siang | 15:00 | 23:00 | 0 |
| S3 | Shift Malam | 23:00 | 07:00 | 1 |
| OFF | Libur | NULL | NULL | 0 |

Pengaturan awal: jendela_absen_masuk_menit=60; toleransi_terlambat_menit=10; batas_alpa_menit_setelah_pulang=30; min_istirahat_jam=8; cuti_perlu_hod_hari=3.

Contoh pola rotasi 4 grup, siklus 8 hari (ilustrasi; jumlah grup dan pola mengikuti perusahaan):

| Grup | H1 | H2 | H3 | H4 | H5 | H6 | H7 | H8 |
|---|---|---|---|---|---|---|---|---|
| A | S1 | S1 | S2 | S2 | S3 | S3 | OFF | OFF |
| B | OFF | OFF | S1 | S1 | S2 | S2 | S3 | S3 |
| C | S3 | S3 | OFF | OFF | S1 | S1 | S2 | S2 |
| D | S2 | S2 | S3 | S3 | OFF | OFF | S1 | S1 |

Pada setiap hari, tiga shift selalu terisi satu grup dan satu grup libur.

### 5.5 Query inti

- Rekap bulanan: GROUP BY id_karyawan pada absensi (SUM per status, SUM menit_terlambat, SUM menit_kerja) untuk rentang tanggal_shift.
- Kehadiran real-time: join jadwal_shift (hari ini, shift berjalan) dengan absensi untuk menghitung hadir, belum hadir, terlambat.
- Validasi tukar shift: cek jadwal_shift kedua karyawan pada tanggal terkait dan tanggal sebelum dan sesudahnya untuk aturan istirahat.

### 5.6 Kesesuaian dengan file Excel perusahaan

Prinsip: 5 tabel master tetap memakai nama kolom, urutan, dan arti yang sama dengan Excel. Perubahan hanya yang tercantum di bawah.

| Tabel | Sama dengan Excel | Berbeda / ditambah | Alasan |
|---|---|---|---|
| karyawan | id_karyawan, nama_karyawan, jns_kelamin (L/P), tgl_lahir, id_departemen, id_jabatan, id_grup (NULL), created_at | id_departemen dan id_jabatan dari VARCHAR(20) menjadi INT UNSIGNED; tambah status_karyawan, no_hp, updated_at | Excel tidak konsisten (tabel departemen dan jabatan memakai INT); FK harus bertipe sama dengan PK yang dirujuk |
| departemen | id_departemen, nama_departemen, kode_departemen (PRS, LGS, PRD, QCT, PCKG), deskripsi, created_at | id_departemen dipastikan INT AI; tambah updated_at | Excel menulis INT / VARCHAR; kode bisnis sudah ada di kode_departemen |
| jabatan | id_jabatan, nama_jabatan, id_departemen, level_jabatan, created_at | id_jabatan dipastikan INT AI; tambah kode_jabatan (opsional) dan updated_at; nilai level_jabatan ditulis huruf besar (DIREKTUR, HOD, SUPERVISOR, OPERATOR) | Excel menulis INT / VARCHAR dan contoh kode JAB-01 |
| grup_karyawan | id_grup INT AI, nama_grup VARCHAR(50), keterangan TEXT | Tidak ada | Nama tabel mengikuti rujukan FK di Excel (grup_karyawan) |
| shift | kode_shift VARCHAR(10), nama_shift VARCHAR(50), jam_masuk TIME, jam_pulang TIME (NULL jika libur) | kode_shift dijadikan PK; tambah lintas_hari, toleransi_menit, warna, is_libur, is_aktif | Excel tidak menyebut PK; kolom tambahan dibutuhkan untuk shift malam dan UI |

Aturan migration untuk 5 tabel master: kolom created_at memakai default CURRENT_TIMESTAMP seperti di Excel (di Laravel: timestamp('created_at')->useCurrent()).

Jika data asli perusahaan diimport nanti: samakan penulisan level_jabatan ke huruf besar, dan petakan kode seperti DEP-01 atau JAB-01 ke kode_departemen atau kode_jabatan, bukan ke kolom id.
