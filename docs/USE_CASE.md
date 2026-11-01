# Dokumentasi Use Case Diagram (5 Aktor)
## Sistem Manajemen Shift & Absensi Operator (SIM-Shift DTY Indorama)

Dokumen ini mendefinisikan spesifikasi fungsional sistem melalui **Use Case Diagram** yang memetakan interaksi antara **5 Aktor Utama** dengan modul-modul sistem sesuai hak akses berbasis role (Policy) pada spesifikasi aturan domain bisnis (PRD/SRS).

---

### 1. Definisi 5 Aktor Sistem

| No | Aktor | Contoh Akun Uji Coba | Deskripsi & Batas Wewenang |
| :---: | :--- | :--- | :--- |
| 1 | **OPERATOR** | `EMP-0013` (Sarwan) | Pengguna operasional lapangan. Melakukan absensi via Kiosk/Mobile, melihat jadwal kerja diri sendiri, mengajukan permohonan izin/sakit/cuti, mengajukan tukar shift bilateral dengan rekan kerja, serta mengisi/melihat logbook shift. |
| 2 | **SUPERVISOR** | `EMP-0009` (Gilang) | Pengawas lini produksi departemen terkait. Memantau kehadiran regu kerja, menyetujui/menolak pengajuan cuti/izin tahap 1, menyetujui tukar shift anggota tim, membuat dan menandatangani logbook serah terima shift. |
| 3 | **HOD (Head of Department)** | `EMP-0004` (Satrio) | Kepala departemen. Memonitor rekap kehadiran departemennya, melakukan approval final untuk pengajuan cuti jangka panjang (> 3 hari kerja), memvalidasi kebutuhan lembur, dan mengaudit logbook shift. |
| 4 | **DIREKTUR** | `EMP-0002` (Jerry) | Eksekutif perusahaan. Memiliki akses read-only menyeluruh (lintas seluruh departemen) untuk memantau performa kehadiran pabrik, tren keterlambatan, utilisasi tenaga kerja, dan laporan analitik eksekutif. |
| 5 | **ADMIN HR (Personalia)** | `EMP-0001` (Subani) | Administrator tata kelola kepegawaian dan sistem. Mengelola master data (karyawan, shift, pola rotasi), generate jadwal bulanan, konfigurasi parameter bisnis, kalender hari libur, koreksi absensi lapangan, dan memantau audit log. |

---

### 2. Use Case Diagram (Mermaid)

```mermaid
flowchart LR
    %% Definisi Aktor
    subgraph Aktor["Daftar 5 Aktor"]
        OP(("👤 Operator\n(Sarwan)"))
        SPV(("👤 Supervisor\n(Gilang)"))
        HOD(("👤 HOD\n(Satrio)"))
        DIR(("👤 Direktur\n(Jerry)"))
        HR(("👤 Admin HR\n(Subani)"))
    end

    %% Batas Sistem
    subgraph System["SIM-Shift DTY Indorama - Sistem Manajemen Shift & Absensi"]
        UC01["UC-01: Login & Autentikasi Role"]
        UC02["UC-02: Absensi Masuk & Pulang (Kiosk/Mobile)"]
        UC03["UC-03: Melihat Jadwal Shift Pribadi"]
        UC04["UC-04: Mengajukan Tukar Shift"]
        UC05["UC-05: Konfirmasi Kesediaan Tukar Shift"]
        UC06["UC-06: Mengajukan Cuti / Izin / Sakit"]
        UC07["UC-07: Monitoring Kehadiran Regu Lapangan"]
        UC08["UC-08: Approval Tahap 1 (Supervisor)"]
        UC09["UC-09: Approval Tahap 2 / Cuti > 3 Hari (HOD)"]
        UC10["UC-10: Pengisian Logbook Serah Terima Shift"]
        UC11["UC-11: Monitoring Dashboard Eksekutif Lintas Departemen"]
        UC12["UC-12: Kelola Master Karyawan & Akun"]
        UC13["UC-13: Kelola Pola Rotasi & Generate Jadwal"]
        UC14["UC-14: Koreksi Data Absensi & Tulis Audit Log"]
        UC15["UC-15: Kelola Hari Libur & Pengaturan Sistem"]
        UC16["UC-16: Melihat Rekap Absensi & Notifikasi"]
    end

    %% Hubungan Operator
    OP --> UC01
    OP --> UC02
    OP --> UC03
    OP --> UC04
    OP --> UC05
    OP --> UC06
    OP --> UC10
    OP --> UC16

    %% Hubungan Supervisor
    SPV --> UC01
    SPV --> UC07
    SPV --> UC08
    SPV --> UC10
    SPV --> UC16

    %% Hubungan HOD
    HOD --> UC01
    HOD --> UC07
    HOD --> UC09
    HOD --> UC10
    HOD --> UC16

    %% Hubungan Direktur
    DIR --> UC01
    DIR --> UC11
    DIR --> UC16

    %% Hubungan Admin HR
    HR --> UC01
    HR --> UC12
    HR --> UC13
    HR --> UC14
    HR --> UC15
    HR --> UC16
```

---

### 3. Matriks Hak Akses Use Case (Authorization Matrix)

| Kode UC | Nama Use Case | Operator | Supervisor | HOD | Direktur | Admin HR |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| **UC-01** | Login, Logout & Ganti Password | ✅ | ✅ | ✅ | ✅ | ✅ |
| **UC-02** | Absensi Kiosk (Waktu Server Asia/Jakarta) | ✅ | ✅ | - | - | - |
| **UC-03** | Lihat Kalender Jadwal Shift Pribadi | ✅ | ✅ | - | - | - |
| **UC-04** | Ajukan Permohonan Tukar Shift Bilateral | ✅ | - | - | - | - |
| **UC-05** | Konfirmasi Tukar Shift dari Rekan Pengganti | ✅ | - | - | - | - |
| **UC-06** | Pengajuan Izin / Sakit / Cuti Tahunan | ✅ | ✅ | ✅ | - | - |
| **UC-07** | Monitoring Kehadiran Realtime Tim (Dept Sendiri) | - | ✅ | ✅ | - | ✅ |
| **UC-08** | Approval Tahap 1 (Cuti <= 3 hari, Tukar Shift, Lembur) | - | ✅ | - | - | - |
| **UC-09** | Approval Tahap 2 (Cuti > 3 hari tingkat HOD) | - | - | ✅ | - | - |
| **UC-10** | Input & Review Logbook Serah Terima Shift | ✅ | ✅ | ✅ | - | ✅ |
| **UC-11** | Dashboard Eksekutif & Laporan Lintas Departemen | - | - | - | ✅ | ✅ |
| **UC-12** | Kelola Master Data Karyawan, Departemen, Jabatan | - | - | - | - | ✅ |
| **UC-13** | Konfigurasi Pola Rotasi & Generate Jadwal Bulanan | - | - | - | - | ✅ |
| **UC-14** | Koreksi Absensi Lapangan (Wajib Alasan & Audit Log) | - | - | - | - | ✅ |
| **UC-15** | Kelola Hari Libur & Parameter Bisnis (Tabel Pengaturan) | - | - | - | - | ✅ |
| **UC-16** | Menerima Notifikasi In-App & Riwayat Aktivitas | ✅ | ✅ | ✅ | ✅ | ✅ |
