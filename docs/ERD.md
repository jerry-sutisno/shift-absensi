# Dokumentasi Entity-Relationship Diagram (ERD)
## Sistem Manajemen Shift & Absensi Operator (SIM-Shift DTY Indorama)

Dokumen ini memuat perancangan basis data relasional (ERD) untuk aplikasi Manajemen Shift dan Absensi Operator Pabrik, yang dibangun berdasarkan 5 tabel master perusahaan (hasil penyempurnaan kamus data Excel) dan tabel-tabel transaksi operasional.

File sumber DBML tersedia di: [`docs/ERD.dbml`](file:///d:/laragon/www/shift-absensi/docs/ERD.dbml). File ini dapat diunggah langsung ke [dbdiagram.io](https://dbdiagram.io), Navicat Data Modeler, atau draw.io.

---

### 1. Diagram Relasi Entitas (Mermaid ERD)

```mermaid
erDiagram
    DEPARTEMEN ||--o{ JABATAN : "memiliki"
    DEPARTEMEN ||--o{ KARYAWAN : "menaungi"
    DEPARTEMEN ||--o{ LOGBOOK_SHIFT : "lingkup"
    JABATAN ||--o{ KARYAWAN : "diduduki"
    GRUP_KARYAWAN ||--o{ KARYAWAN : "beranggotakan"
    GRUP_KARYAWAN ||--o{ POLA_ROTASI_DETAIL : "mengikuti"
    GRUP_KARYAWAN ||--o{ LOGBOOK_SHIFT : "melaporkan"
    
    SHIFT ||--o{ POLA_ROTASI_DETAIL : "dijadwalkan"
    SHIFT ||--o{ JADWAL_SHIFT : "ditugaskan"
    SHIFT ||--o{ LOGBOOK_SHIFT : "periode"

    KARYAWAN ||--|| USERS : "memiliki_akun"
    KARYAWAN ||--o{ JADWAL_SHIFT : "memiliki"
    KARYAWAN ||--o{ ABSENSI : "mencatat"
    KARYAWAN ||--o{ PENGAJUAN : "mengajukan"
    KARYAWAN ||--o{ LEMBUR : "melakukan"
    KARYAWAN ||--o{ TUKAR_SHIFT : "pemohon"
    KARYAWAN ||--o{ TUKAR_SHIFT : "pengganti"

    USERS ||--o{ JADWAL_SHIFT : "dibuat_oleh"
    USERS ||--o{ ABSENSI : "dikoreksi_oleh"
    USERS ||--o{ PENGAJUAN : "disetujui_oleh"
    USERS ||--o{ TUKAR_SHIFT : "disetujui_oleh"
    USERS ||--o{ LEMBUR : "disetujui_oleh"
    USERS ||--o{ LOGBOOK_SHIFT : "dibuat_oleh"
    USERS ||--o{ NOTIFIKASI : "menerima"
    USERS ||--o{ AUDIT_LOG : "mencatat_aktivitas"

    POLA_ROTASI ||--o{ POLA_ROTASI_DETAIL : "memiliki_siklus"
    POLA_ROTASI ||--o{ JADWAL_SHIFT : "dasar_generasi"

    JADWAL_SHIFT ||--|| ABSENSI : "diverifikasi_oleh"
    JADWAL_SHIFT ||--o{ TUKAR_SHIFT : "jadwal_pemohon"
    JADWAL_SHIFT ||--o{ TUKAR_SHIFT : "jadwal_pengganti"

    DEPARTEMEN {
        int id_departemen PK
        string nama_departemen
        string kode_departemen UK
        text deskripsi
    }

    JABATAN {
        int id_jabatan PK
        string kode_jabatan
        string nama_jabatan
        int id_departemen FK
        string level_jabatan
    }

    GRUP_KARYAWAN {
        int id_grup PK
        string nama_grup UK
        text keterangan
    }

    SHIFT {
        string kode_shift PK
        string nama_shift
        time jam_masuk
        time jam_pulang
        boolean lintas_hari
        tinyint toleransi_menit
        string warna
        boolean is_libur
        boolean is_aktif
    }

    KARYAWAN {
        string id_karyawan PK
        string nama_karyawan
        string jns_kelamin
        date tgl_lahir
        int id_departemen FK
        int id_jabatan FK
        int id_grup FK
        string status_karyawan
        string no_hp
    }

    USERS {
        bigint id_user PK
        string id_karyawan FK,UK
        string password
        string pin_absen
        string role
        boolean is_active
        datetime last_login_at
    }

    POLA_ROTASI {
        int id_pola PK
        string nama_pola
        tinyint panjang_siklus_hari
        text keterangan
        boolean is_aktif
    }

    POLA_ROTASI_DETAIL {
        int id_detail PK
        int id_pola FK
        int id_grup FK
        tinyint hari_ke
        string kode_shift FK
    }

    JADWAL_SHIFT {
        bigint id_jadwal PK
        string id_karyawan FK
        date tanggal
        string kode_shift FK
        string sumber
        string status
        int id_pola FK
        bigint dibuat_oleh FK
    }

    ABSENSI {
        bigint id_absensi PK
        bigint id_jadwal FK,UK
        string id_karyawan FK
        date tanggal_shift
        datetime waktu_masuk
        datetime waktu_pulang
        string metode_masuk
        string metode_pulang
        string status
        smallint menit_terlambat
        boolean pulang_cepat
        smallint menit_kerja
        text catatan
        bigint dikoreksi_oleh FK
    }

    PENGAJUAN {
        int id_pengajuan PK
        string id_karyawan FK
        string jenis
        date tgl_mulai
        date tgl_selesai
        text alasan
        string status
        string tahap
        bigint id_approver FK
    }

    TUKAR_SHIFT {
        int id_tukar PK
        string id_pemohon FK
        string id_pengganti FK
        bigint id_jadwal_pemohon FK
        bigint id_jadwal_pengganti FK
        text alasan
        string status
        bigint id_approver FK
    }

    LEMBUR {
        int id_lembur PK
        string id_karyawan FK
        date tanggal
        time jam_mulai
        time jam_selesai
        text alasan
        string status
        bigint id_approver FK
        smallint menit_lembur
    }

    LOGBOOK_SHIFT {
        int id_logbook PK
        date tanggal
        string kode_shift FK
        int id_departemen FK
        int id_grup FK
        bigint id_pembuat FK
        text ringkasan
        text kendala
        text tindak_lanjut
    }

    HARI_LIBUR {
        date tanggal PK
        string keterangan
        string jenis
    }

    PENGATURAN {
        string kunci PK
        string nilai
        string keterangan
    }

    NOTIFIKASI {
        int id_notifikasi PK
        bigint id_user FK
        string judul
        text pesan
        string url
        datetime dibaca_at
    }

    AUDIT_LOG {
        bigint id_log PK
        bigint id_user FK
        string aksi
        string tabel
        string id_record
        json data_lama
        json data_baru
        string ip_address
    }
```

---

### 2. Klasifikasi dan Struktur Tabel

#### A. Tabel Master Organisasi (Turunan Excel Perusahaan)
1. **`departemen`**: Menyimpan daftar departemen/divisi (PRS, PRD, LGS, QCT, PCKG).
2. **`jabatan`**: Menentukan hirarki jabatan dan level otoritas (`DIREKTUR`, `HOD`, `SUPERVISOR`, `OPERATOR`).
3. **`grup_karyawan`**: Kelompok kerja rotasi (Grup A, B, C, D).
4. **`shift`**: Definisi jam kerja dan shift khusus:
   - `GS` (General Shift, 08:00 - 17:00)
   - `S1` (Shift Pagi, 07:00 - 15:00)
   - `S2` (Shift Siang, 15:00 - 23:00)
   - `S3` (Shift Malam Lintas Hari, 23:00 - 07:00, `lintas_hari = 1`)
   - `OFF` (Libur Rutin, jam NULL)
5. **`karyawan`**: Data induk kepegawaian dengan PK `id_karyawan` (VARCHAR 20, contoh `EMP-0001`).

#### B. Tabel Pola Rotasi & Penjadwalan
6. **`pola_rotasi`**: Header pola siklus kerja (contoh: Siklus 8 Hari 4 Regu).
7. **`pola_rotasi_detail`**: Matriks penugasan shift per regu pada hari ke-1 s/d ke-8 (`UNIQUE(id_pola, id_grup, hari_ke)`).
8. **`jadwal_shift`**: Kalender penugasan kerja per individu per tanggal. Menjamin integritas `UNIQUE(id_karyawan, tanggal)`.

#### C. Tabel Absensi & Transaksi Lapangan
9. **`absensi`**: Catatan kehadiran berbasis waktu server (`Asia/Jakarta`), terikat 1-ke-1 dengan jadwal shift (`id_jadwal`).
10. **`pengajuan`**: Izin, Sakit, dan Cuti bertingkat (Supervisor -> HOD jika > 3 hari).
11. **`tukar_shift`**: Transaksi tukar shift bilateral (Operator Pemohon <-> Operator Pengganti -> Approval Supervisor).
12. **`lembur`**: Surat perintah / pengajuan kerja lembur sebelum/sesudah shift.
13. **`logbook_shift`**: Serah terima kondisi operasional, mesin, kendala, dan tindak lanjut antar shift.

#### D. Tabel Tata Kelola & Sistem
14. **`users`**: Kredensial login akun (username = `id_karyawan`, password hash, PIN Kiosk).
15. **`hari_libur`**: Kalender hari libur nasional dan cuti bersama perusahaan.
16. **`pengaturan`**: Parameter bisnis dinamis (`toleransi_terlambat_menit`, `min_istirahat_jam`, dsb).
17. **`notifikasi`**: Pesan pemberitahuan status pengajuan dan peringatan jadwal.
18. **`audit_log`**: Jejak rekam perubahan data sensitif (`aksi`, `tabel`, `id_record`, `data_lama`, `data_baru`).

---

### 3. Cara Menggunakan File `docs/ERD.dbml`
1. Buka browser dan kunjungi [dbdiagram.io](https://dbdiagram.io).
2. Salin isi file [`docs/ERD.dbml`](file:///d:/laragon/www/shift-absensi/docs/ERD.dbml) ke panel editor sebelah kiri.
3. dbdiagram.io akan secara otomatis merender diagram visual interaktif lengkap dengan garis relasi kardinalitas.
4. Anda dapat mengekspor diagram ke format **PDF**, **PNG**, atau **SQL DDL**.
