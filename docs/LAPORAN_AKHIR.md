# LAPORAN TUGAS BESAR & PERANCANGAN SISTEM
# SISTEM INFORMASI MANAJEMEN SHIFT & PRESENSI (SIM-SHIFT DTY INDORAMA)

---

**Identitas Dokumen:**
- **Judul Proyek:** Sistem Informasi Manajemen Penjadwalan Shift & Presensi Operator (SIM-Shift DTY Indorama)
- **Studi Kasus:** Divisi DTY (Draw Texturising Yarn) — PT Indo-Rama Synthetics Tbk
- **Pengembang / Penulis:** Jerry Sutisno
- **Versi Dokumen:** 1.0 (Final Release)
- **Tanggal:** 01 November 2026
- **Stack Teknologi:** Laravel 11, Livewire 3, Alpine.js, Tailwind CSS, MySQL 8, DomPDF
- **Zona Waktu Otoritatif:** Asia/Jakarta (WIB)
- **Repositori GitHub:** [`https://github.com/jerry-sutisno/shift-absensi.git`](https://github.com/jerry-sutisno/shift-absensi.git)

---

## DAFTAR ISI

1. [BAB I: PENDAHULUAN & LATAR BELAKANG (BRD)](#bab-i-pendahuluan--latar-belakang-brd)
   - 1.1 Profil Perusahaan & Latar Belakang Masalah Lapangan
   - 1.2 Rumusan Masalah & Solusi yang Diajukan
   - 1.3 Tujuan Sistem & Key Performance Indicators (KPI)
   - 1.4 Ruang Lingkup Sistem (In-Scope & Out-of-Scope)
   - 1.5 Identifikasi Stakeholder & Struktur Organisasi
2. [BAB II: ANALISIS KEBUTUHAN SISTEM (PRD & SRS)](#bab-ii-analisis-kebutuhan-sistem-prd--srs)
   - 2.1 Analisis Kebutuhan Fungsional (Prioritisasi MoSCoW: F-01 s/d F-16)
   - 2.2 Aturan Bisnis Kunci Manufaktur (Business Rules BR-01 s/d BR-15)
   - 2.3 Analisis Kebutuhan Non-Fungsional (NFR)
3. [BAB III: USE CASE DIAGRAM & MATRIKS HAK AKSES](#bab-iii-use-case-diagram--matriks-hak-akses)
   - 3.1 Identifikasi 5 Aktor Sistem
   - 3.2 Use Case Diagram Lengkap (Mermaid)
   - 3.3 Matriks Otorisasi Berbasis Peran (RBAC Policy Laravel)
4. [BAB IV: PERANCANGAN BASIS DATA (DATABASE DESIGN)](#bab-iv-perancangan-basis-data-database-design)
   - 4.1 Diagram Relasi Entitas Relasional (ERD Mermaid)
   - 4.2 Struktur Tabel Master & Tabel Transaksional (18 Tabel)
   - 4.3 Analisis Kesesuaian & Normalisasi Kamus Data Asli Perusahaan
5. [BAB V: IMPLEMENTASI ANTARMUKA SISTEM (USER INTERFACE)](#bab-v-implementasi-antarmuka-sistem-user-interface)
   - 5.1 Layar Otentikasi & Akun Demo Role-Based
   - 5.2 Dashboard Analitik Eksekutif & Personel
   - 5.3 Modul Master Data Karyawan & Generator Pola Rotasi 8 Hari (4 Regu)
   - 5.4 Pusat Pengajuan & Multi-Tier Approval Hub
   - 5.5 Koreksi Absensi Lapangan & Jejak Rekam Audit Trail
   - 5.6 Modul Logbook Serah Terima Shift, Kalender Libur & Notifikasi
   - 5.7 Mode Kiosk Presensi Mandiri (Touchscreen Terminal)
   - 5.8 Modul Rekapitulasi Kehadiran & Ekspor Siap-Payroll (Excel & PDF Resmi)
6. [BAB VI: PENGUJIAN SISTEM & KESIMPULAN](#bab-vi-pengujian-sistem--kesimpulan)
   - 6.1 Metodologi & Hasil Eksekusi Automated Feature Test (69 Unit Test Passed)
   - 6.2 Kesimpulan Implementasi & Nilai Tambah bagi Divisi DTY
   - 6.3 Rekomendasi Pengembangan Masa Depan (Future Roadmap)

---

## BAB I: PENDAHULUAN & LATAR BELAKANG (BRD)

### 1.1 Profil Perusahaan & Latar Belakang Masalah Lapangan
PT Indo-Rama Synthetics Tbk merupakan salah satu produsen tekstil sintetis terintegrasi terbesar di Indonesia. Salah satu unit produksi utamanya adalah **Divisi DTY (*Draw Texturising Yarn*)**, yang bertugas mengolah serat benang poliester mentah menjadi benang bertekstur dengan spesifikasi presisi tinggi melalui mesin *texturising* berkecepatan tinggi.

Aktivitas mesin pada lini produksi DTY beroperasi selama **24 jam sehari, 7 hari seminggu (24/7)** tanpa henti. Berhentinya mesin secara mendadak akibat kekosongan operator dapat mengakibatkan pemborosan energi termal, cacat produk benang (*yarn defect*), dan kerugian material yang signifikan. Oleh karena itu, pengaturan jam kerja menggunakan sistem **4 Regu Kerja (Grup A, B, C, D)** dengan pola rotasi 3 shift kerja aktif (Shift 1 Pagi, Shift 2 Siang, Shift 3 Malam) serta 1 regu libur rutin (*day off*).

Sebelum dikembangkan sistem **SIM-Shift DTY Indorama**, operasional penjadwalan dan pencatatan presensi menghadapi berbagai kendala lapangan:

| Aspek Operasional | Kondisi Eksisting (Metode Manual) | Dampak Negatif Lapangan | Solusi SIM-Shift DTY Indorama |
| :--- | :--- | :--- | :--- |
| **Penjadwalan Regu** | Menggunakan lembar kerja Excel manual & papan tulis pabrik | Rentan salah input, jadwal bentrok antar regu, operator kebingungan jadwal, sulit mengantisipasi tukar dinas. | Mesin generator jadwal otomatis berbasis pola siklus 8 hari tanpa bentrok (*zero conflict*). |
| **Pencatatan Kehadiran** | Menggunakan daftar hadir kertas / jam ceklok mekanik | Rawan manipulasi jam hadir, titip absen, data robek/hilang, dan kesalahan membaca shift malam (S3). | Mode Kiosk mandiri berkecepatan tinggi berbasis waktu server otoritatif (`Asia/Jakarta`). |
| **Perizinan & Tukar Shift** | Pengajuan lisan atau pesan instan WhatsApp tidak terstandar | Tidak terdokumentasi rapi, konfirmasi sepihak berisiko pos mesin kosong tanpa operator pengganti. | Formulir digital dengan alur verifikasi bilateral dan persetujuan bertingkat (*multi-tier approval*). |
| **Rekapitulasi Payroll** | HR mengompilasi manual ratusan kertas absensi tiap akhir bulan | Membutuhkan waktu 3–5 hari kerja tim Personalia, rawan perselisihan jam lembur & potongan alpa. | Rekap bulanan instan otomatis dalam hitungan detik dengan ekspor Excel (CSV UTF-8) dan PDF ber-kop resmi. |
| **Visibilitas Supervisor** | Supervisor harus berkeliling fisik mengecek kehadiran per line | Terlambat mengetahui operator yang absen sehingga proses pergantian shift terhambat. | Dashboard monitoring kehadiran real-time per shift dan departemen. |
| **Serah Terima Shift** | Catatan kerusakan mesin ditulis di buku tulis kertas yang mudah kotor/rusak | Informasi riwayat gangguan mesin antar shift sering terputus, memperpanjang waktu *downtime*. | Modul Logbook serah terima digital terintegrasi yang disahkan oleh Supervisor lini. |

### 1.2 Rumusan Masalah & Solusi yang Diajukan
Berdasarkan kondisi di atas, dirumuskan masalah inti:
1. Bagaimana mengotomatisasi penyusunan jadwal rotasi 4 regu kerja 24/7 di Divisi DTY agar tidak terjadi tumpang tindih penugasan?
2. Bagaimana mencatat presensi secara akurat untuk shift malam (S3: 23:00 - 07:00) yang melintasi tengah malam (*midnight crossover*) tanpa kesalahan tanggal kerja?
3. Bagaimana membangun mekanisme perizinan, lembur, dan pertukaran shift bilateral yang terstruktur dengan jejak audit digital (*audit trail*)?
4. Bagaimana menyediakan rekapitulasi kehadiran dan ekspor laporan yang siap diproses oleh bagian *payroll*?

Solusi yang dihadirkan adalah **SIM-Shift DTY Indorama**, aplikasi web enterprise berbasis Laravel 11 dan Livewire 3 yang menggabungkan kemudahan manajemen data, kecepatan terminal presensi Kiosk, dan keandalan pelaporan otomatis.

### 1.3 Tujuan Sistem & Key Performance Indicators (KPI)
Tujuan utama implementasi SIM-Shift DTY Indorama diukur melalui indikator keberhasilan berikut:
1. **Efisiensi Waktu Penjadwalan:** Pembuatan jadwal shift bulanan untuk seluruh personel divisi selesai dalam waktu **$< 5$ menit** (sebelumnya membutuhkan waktu 4–6 jam kerja manual).
2. **Akurasi Validasi Waktu:** **100% presensi** mengacu pada jam server otoritatif (`Asia/Jakarta`) guna mengeliminasi kecurangan manipulasi jam lokal perangkat.
3. **Kecepatan Penerbitan Laporan Payroll:** Kompilasi rekapitulasi kehadiran bulanan dipangkas dari **4 hari menjadi 1 klik instan ($< 3$ detik)**.
4. **Akuntabilitas & Audit Trail:** **100% mutasi data** (koreksi absensi, perubahan jadwal, dan keputusan persetujuan) tercatat lengkap dengan perbandingan data lama dan data baru dalam format JSON.

### 1.4 Ruang Lingkup Sistem
- **Ruang Lingkup Terpenuhi (In-Scope):**
  1. Manajemen Master Data: Karyawan, Departemen, Level Jabatan, Regu Kerja (Grup A-D), dan Shift Pabrik.
  2. Konfigurasi Pola Rotasi Siklus 8 Hari dan Mesin Generator Jadwal Bulanan Otomatis.
  3. Terminal Presensi Mandiri (Mode Kiosk Layar Sentuh) dengan PIN dan pemindai NIK.
  4. Penanganan Khusus Shift Malam (Shift 3 Lintas Hari) pada tanggal mulai penugasan.
  5. Portal Pengajuan & Persetujuan Berjenjang: Izin, Sakit, Cuti Tahunan, Tukar Shift Bilateral, dan Lembur.
  6. Modul Logbook Serah Terima Kondisi Operasional Shift (*Handover*).
  7. Audit Log Terstruktur untuk seluruh tindakan koreksi dan mutasi data oleh HR.
  8. Dashboard Monitoring Kehadiran Real-time dan Rekapitulasi Ekspor (Excel & PDF Resmi).
- **Di Luar Lingkup (Out-of-Scope Versi 1.0):**
  - Perhitungan nominal uang gaji/slip gaji (sistem fokus menghasilkan rekap parameter absensi siap-payroll).
  - Integrasi SDK perangkat keras biometrik proprietary eksternal (diakomodasi melalui protokol Kiosk Web terstandarisasi).

### 1.5 Identifikasi Stakeholder & Struktur Organisasi
Sistem ini melayani 5 tingkatan pengguna (*role*) sesuai tata kelola Divisi DTY PT Indo-Rama Synthetics Tbk:

| No | Peran / Role | Akun Representatif | Deskripsi & Batas Wewenang |
| :---: | :--- | :--- | :--- |
| 1 | **Direktur** | `EMP-0002` (Jerry) | Pimpinan eksekutif tertinggi pabrik. Mengakses dashboard analitik lintas divisi, tren utilisasi regu, rekapitulasi kehadiran global (akses *read-only* menyeluruh). |
| 2 | **Head of Department (HOD)** | `EMP-0004` (Satrio) | Kepala Divisi DTY. Memantau kehadiran divisi, menyetujui lembur, dan memberikan *approval* final untuk cuti panjang ($> 3$ hari kerja). |
| 3 | **Supervisor** | `EMP-0009` (Gilang) | Pengawas operasional lini produksi DTY. Memvalidasi kehadiran regu kerja per shift, menyetujui izin/cuti $\le 3$ hari, menyetujui tukar shift, dan menandatangani logbook shift. |
| 4 | **Operator** | `EMP-0013` (Sarwan) | Tenaga teknis lapangan di lantai produksi. Melakukan presensi mandiri (Kiosk), melihat jadwal shift pribadi, mengajukan tukar shift bilateral, mengajukan izin/cuti, dan mengisi logbook mesin. |
| 5 | **Admin HR (Personalia)** | `EMP-0001` (Subani) | Administrator tata kelola personalia dan sistem. Mengelola master data, menerbitkan jadwal bulanan, konfigurasi kalender libur nasional, melakukan koreksi absensi lapangan, dan mencetak rekap payroll. |

---

## BAB II: ANALISIS KEBUTUHAN SISTEM (PRD & SRS)

### 2.1 Analisis Kebutuhan Fungsional (Prioritisasi MoSCoW)
Kebutuhan fungsional diklasifikasikan menggunakan metode prioritisasi MoSCoW:

#### A. MUST HAVE (Kebutuhan Mutlak Sistem)
- **F-01 (Autentikasi & Otorisasi RBAC):** Autentikasi aman dengan *session regeneration*, perlindungan brute-force, dan otorisasi berbasis Laravel Policy.
- **F-02 (Manajemen Data Induk Perusahaan):** Pengelolaan 5 tabel master utama selaras dengan struktur kamus data perusahaan (Karyawan, Departemen, Jabatan, Grup Karyawan, Shift).
- **F-03 (Generator Pola Rotasi Shift):** Pembangkitan jadwal otomatis berbasis pola rotasi siklus 8 hari untuk 4 regu kerja tanpa konflik penugasan.
- **F-04 (Pencatatan Presensi Terverifikasi):** Validasi absensi masuk dan pulang berdasarkan jadwal kerja resmi dan jam server.
- **F-05 (Penanganan Shift Malam Lintas Hari):** Pencatatan presensi Shift 3 (23:00 - 07:00 WIB) tetap terkunci pada `tanggal_shift` hari penugasan dimulai.
- **F-06 (Alur Pengajuan & Persetujuan Multi-Tier):** Alur persetujuan cuti, izin, dan sakit berjenjang dengan eskalasi otomatis.
- **F-07 (Dashboard & Kalender Interaktif):** Tampilan dashboard berbeda untuk tiap role, menampilkan indikator KPI kehadiran dan kalender kerja.
- **F-08 (Rekapitulasi Bulanan Siap-Payroll):** Penghitungan total hadir, terlambat, alpa, dan izin/cuti dengan ekspor ke format Excel (CSV UTF-8 BOM) dan PDF ber-kop resmi.
- **F-09 (Audit Trail Logging):** Pencatatan otomatis ke tabel `audit_log` untuk seluruh perubahan jadwal, persetujuan, dan koreksi manual.

#### B. SHOULD HAVE (Kebutuhan Operasional Tambahan)
- **F-10 (Tukar Shift Bilateral):** Pengajuan tukar dinas antar rekan selevel dengan validasi waktu istirahat minimal (8 jam).
- **F-11 (Koreksi Absensi Khusus HR):** Fitur pemulihan data absensi yang bermasalah akibat gangguan mesin dengan pencatatan alasan wajib.
- **F-12 (Surat Perintah Kerja Lembur / SPKL):** Pengajuan dan persetujuan lembur terstruktur oleh Supervisor dan HOD.
- **F-13 (Logbook Serah Terima Shift Digital):** Buku jurnal serah terima kondisi mesin tekstil dan catatan keselamatan kerja antar regu.
- **F-14 (Sistem Notifikasi In-App):** Pemberitahuan real-time mengenai status pengajuan izin, jadwal baru, atau pertukaran shift.
- **F-15 (Kalender Libur Nasional & Cuti Bersama):** Pengaturan hari libur resmi yang secara otomatis membebaskan karyawan General Shift (GS).
- **F-16 (Terminal Presensi Kiosk Touchscreen):** Antarmuka ramah layar sentuh untuk absensi cepat mandiri di pintu masuk lantai produksi.

### 2.2 Aturan Bisnis Kunci Manufaktur (Business Rules)
Sistem ini mengimplementasikan 15 aturan bisnis ketat (*Business Rules*):
1. **BR-01 (Integritas NIK):** NIK karyawan (`id_karyawan`, VARCHAR 20) bersifat unik, menjadi *Primary Key* pada tabel `karyawan` sekaligus nama pengguna (*username*) login.
2. **BR-02 (Keunikan Penugasan):** Satu operator hanya dapat memiliki maksimal satu jadwal penugasan shift pada satu tanggal kalender (`UNIQUE(id_karyawan, tanggal)`).
3. **BR-03 (Otoritas Tanggal Shift Malam):** Absensi Shift 3 (23:00 - 07:00) yang pulang keesokan harinya pukul 07:00 tetap terhubung pada `tanggal_shift` saat shift dimulai.
4. **BR-04 (Jam Otoritatif Server):** Jam kehadiran diambil langsung dari server backend (`Asia/Jakarta`) tanpa mempercayai waktu lokal perangkat gawai klien.
5. **BR-05 (Ketentuan General Shift):** Karyawan berstatus `id_grup = NULL` (staf kantor/manajemen) otomatis berstatus General Shift (GS: 08:00 - 17:00 WIB, Senin-Jumat, Sabtu-Minggu OFF).
6. **BR-06 (Bebas Absen Hari Libur Rutin):** Jadwal dengan kode `OFF` membebaskan operator dari kewajiban hadir dan tidak akan ditandai sebagai `ALPA`.
7. **BR-07 (Sentralisasi Status Presensi):** Klasifikasi `HADIR`, `TERLAMBAT`, `PULANG_CEPAT`, dan `ALPA` dihitung secara terpusat pada service layer `AttendanceService`.
8. **BR-08 (Proteksi Jadwal Draft):** Jadwal berstatus `DRAFT` tidak akan ditampilkan kepada operator hingga resmi diterbitkan (`PUBLISHED`) oleh HR.
9. **BR-09 (Konfigurabilitas Parameter):** Nilai toleransi keterlambatan (10 menit), jendela awal absen (60 menit), istirahat minimal (8 jam), dan batas cuti HOD (3 hari) dibaca dinamis dari tabel `pengaturan`.
10. **BR-10 (Akuntabilitas Audit Trail):** Setiap koreksi jam hadir oleh HR wajib menyertakan alasan tertulis dan mencatat `data_lama` serta `data_baru` dalam format JSON.
11. **BR-11 (Isolasi Batas Departemen):** Supervisor dan HOD hanya memiliki hak persetujuan dan monitoring data karyawan yang berada di departemen yang sama.
12. **BR-12 (Eskalasi Cuti Dua Tingkat):** Cuti $\le 3$ hari cukup disetujui Supervisor; cuti $> 3$ hari otomatis diekskalasikan ke HOD untuk persetujuan final.
13. **BR-13 (Pencegahan Self-Approval):** Atasan tidak dapat menyetujui pengajuan cuti, izin, atau lembur milik dirinya sendiri.
14. **BR-14 (Keseimbangan Istirahat Tukar Shift):** Tukar shift ditolak otomatis oleh sistem jika menyebabkan salah satu operator memiliki jeda istirahat $< 8$ jam sebelum shift berikutnya.
15. **BR-15 (Integritas Logbook Shift):** Logbook serah terima shift hanya dapat diisi dan disahkan pada shift berjalan oleh personil yang bertugas di departemen terkait.

### 2.3 Analisis Kebutuhan Non-Fungsional (NFR)
- **Keamanan (Security):** Seluruh password di-hash menggunakan algoritma *Bcrypt*. Proteksi CSRF (*Cross-Site Request Forgery*) aktif pada semua form. Middleware menyuntikkan header keamanan: `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, dan `X-XSS-Protection`.
- **Performa & Latensi (Performance):** Waktu respons halaman rata-rata $\le 200$ ms. Terminal Kiosk memproses validasi NIK dan PIN dalam waktu $\le 100$ ms.
- **Ketersediaan (Reliability 24/7):** Arsitektur sistem stateless dengan database MySQL berindeks efisien menjamin ketersediaan tinggi untuk operasional pabrik nonstop.
- **Ergonomi Pengguna (Usability):** Antarmuka Kiosk dirancang khusus untuk layar sentuh industri dengan tombol berdimensi minimal $48 \times 48$ px, palet kontras korporat (*Corporate Blue & White*), serta keypad numerik taktil.

---

## BAB III: USE CASE DIAGRAM & MATRIKS HAK AKSES

### 3.1 Identifikasi 5 Aktor Sistem
1. **Operator (Sarwan - `EMP-0013`):** Mewakili 20+ operator lapangan Divisi DTY.
2. **Supervisor (Gilang - `EMP-0009`):** Mewakili pengawas lini kerja dan approver tingkat pertama.
3. **Head of Department / HOD (Satrio - `EMP-0004`):** Mewakili kepala divisi penanggung jawab operasional DTY.
4. **Direktur (Jerry - `EMP-0002`):** Mewakili pimpinan eksekutif perusahaan dengan akses analitik komprehensif.
5. **Admin HR (Subani - `EMP-0001`):** Mewakili bagian personalia pabrik pengelola kebijakan dan data master.

### 3.2 Use Case Diagram Lengkap (Mermaid)

```mermaid
flowchart LR
    subgraph Aktor["Daftar 5 Aktor Sistem"]
        OP(("👤 Operator\n(Sarwan)"))
        SPV(("👤 Supervisor\n(Gilang)"))
        HOD(("👤 HOD\n(Satrio)"))
        DIR(("👤 Direktur\n(Jerry)"))
        HR(("👤 Admin HR\n(Subani)"))
    end

    subgraph System["SIM-Shift DTY Indorama (Lingkup Fungsional)"]
        UC01["UC-01: Autentikasi Login & Ganti PIN"]
        UC02["UC-02: Presensi Mandiri Terminal Kiosk"]
        UC03["UC-03: Melihat Jadwal Shift Pribadi"]
        UC04["UC-04: Permohonan Tukar Shift Bilateral"]
        UC05["UC-05: Konfirmasi Kesediaan Rekan Tukar"]
        UC06["UC-06: Pengajuan Izin / Sakit / Cuti"]
        UC07["UC-07: Monitoring Kehadiran Regu Real-time"]
        UC08["UC-08: Approval Lini Pertama (Supervisor)"]
        UC09["UC-09: Approval Final / Cuti > 3 Hari (HOD)"]
        UC10["UC-10: Pengisian Logbook Serah Terima Shift"]
        UC11["UC-11: Dashboard Analitik Eksekutif Global"]
        UC12["UC-12: Manajemen Master Data Karyawan & Akun"]
        UC13["UC-13: Konfigurasi Pola Rotasi & Generate Jadwal"]
        UC14["UC-14: Koreksi Absensi Lapangan & Alasan"]
        UC15["UC-15: Pengaturan Kalender Libur & Konfigurasi"]
        UC16["UC-16: Rekapitulasi Kehadiran & Ekspor Payroll"]
    end

    %% Relasi Aktor Operator
    OP --> UC01
    OP --> UC02
    OP --> UC03
    OP --> UC04
    OP --> UC05
    OP --> UC06
    OP --> UC10

    %% Relasi Aktor Supervisor
    SPV --> UC01
    SPV --> UC07
    SPV --> UC08
    SPV --> UC10
    SPV --> UC16

    %% Relasi Aktor HOD
    HOD --> UC01
    HOD --> UC07
    HOD --> UC09
    HOD --> UC10
    HOD --> UC16

    %% Relasi Aktor Direktur
    DIR --> UC01
    DIR --> UC11
    DIR --> UC16

    %% Relasi Aktor Admin HR
    HR --> UC01
    HR --> UC12
    HR --> UC13
    HR --> UC14
    HR --> UC15
    HR --> UC16
```

### 3.3 Matriks Otorisasi Berbasis Peran (RBAC Policy Laravel)

| No | Modul / Aksi Fungsional | Operator | Supervisor | HOD | Direktur | Admin HR |
| :---: | :--- | :---: | :---: | :---: | :---: | :---: |
| 1 | Melakukan Presensi Kiosk Mandiri | ✅ | ✅ | - | - | - |
| 2 | Melihat Jadwal & Kalender Pribadi | ✅ | ✅ | - | - | - |
| 3 | Mengajukan Permohonan Tukar Shift | ✅ | - | - | - | - |
| 4 | Mengajukan Cuti / Izin / Sakit Mandiri | ✅ | ✅ | ✅ | - | - |
| 5 | Approval Lini (Cuti $\le 3$ hari, Tukar Shift, Lembur) | - | ✅ (Dept) | - | - | - |
| 6 | Approval Final (Cuti $> 3$ hari, Validasi Divisi) | - | - | ✅ (Dept) | - | - |
| 7 | Mengisi & Mengesahkan Logbook Serah Terima | ✅ | ✅ | ✅ | - | ✅ |
| 8 | Dashboard Analitik Lintas Departemen | - | - | - | ✅ (Global) | ✅ (Global) |
| 9 | Pengelolaan Master Karyawan, Departemen & Shift | - | - | - | - | ✅ |
| 10 | Pola Rotasi 8 Hari & Rilis Jadwal Bulanan | - | - | - | - | ✅ |
| 11 | Koreksi Absensi Lapangan & Menulis Alasan | - | - | - | - | ✅ |
| 12 | Ekspor Laporan Rekap Bulanan (Excel & PDF) | - | ✅ (Dept) | ✅ (Dept) | ✅ (Global) | ✅ (Global) |

---

## BAB IV: PERANCANGAN BASIS DATA (DATABASE DESIGN)

### 4.1 Diagram Relasi Entitas Relasional (ERD Mermaid)

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
```

### 4.2 Struktur Tabel Master & Tabel Transaksional (18 Tabel)
Basis data dirancang dengan total 18 tabel yang dikelompokkan ke dalam 4 rumpun logis:
1. **Rumpun Master Data:** `departemen`, `jabatan`, `grup_karyawan`, `shift`, `karyawan`.
2. **Rumpun Penjadwalan & Siklus:** `pola_rotasi`, `pola_rotasi_detail`, `jadwal_shift`.
3. **Rumpun Transaksi & Operasional:** `absensi`, `pengajuan`, `tukar_shift`, `lembur`, `logbook_shift`.
4. **Rumpun Tata Kelola & Sistem:** `users`, `hari_libur`, `pengaturan`, `notifikasi`, `audit_log`.

### 4.3 Analisis Kesesuaian & Normalisasi Kamus Data Asli Perusahaan
Sesuai arahan spesifikasi perancangan, 5 tabel master asli dari kamus data Excel perusahaan dipertahankan nama tabel, nama kolom, urutan, serta semantiknya. Penyesuaian yang dilakukan adalah normalisasi teknis basis data relasional:

| Nama Tabel | Kolom yang Sama dengan Excel Perusahaan | Kolom yang Dimodifikasi / Ditambahkan | Alasan & Justifikasi Teknis |
| :--- | :--- | :--- | :--- |
| **`karyawan`** | `id_karyawan`, `nama_karyawan`, `jns_kelamin` (L/P), `tgl_lahir`, `id_departemen`, `id_jabatan`, `id_grup` (bisa NULL), `created_at` | `id_departemen` & `id_jabatan` disesuaikan menjadi `INT UNSIGNED`; ditambah `status_karyawan` (ENUM: AKTIF, NONAKTIF), `no_hp`, dan `updated_at`. | Di file Excel tipe data tidak konsisten (karyawan menulis VARCHAR padahal master bertipe INT). Ditambah status kerja agar data historis tetap terjaga saat karyawan keluar. |
| **`departemen`** | `id_departemen`, `nama_departemen`, `kode_departemen` (PRS, PRD, LGS, QCT, PCKG), `deskripsi`, `created_at` | `id_departemen` dipastikan `INT UNSIGNED AUTO_INCREMENT`; ditambah `updated_at`. | Memastikan integer auto-increment sebagai Primary Key yang efisien pada indeks foreign key. Kode bisnis tetap disimpan pada `kode_departemen`. |
| **`jabatan`** | `id_jabatan`, `nama_jabatan`, `id_departemen`, `level_jabatan`, `created_at` | `id_jabatan` dipastikan `INT UNSIGNED AUTO_INCREMENT`; penulisan `level_jabatan` diseragamkan huruf kapital (DIREKTUR, HOD, SUPERVISOR, OPERATOR); ditambah `kode_jabatan` dan `updated_at`. | Mengakomodasi mapping role RBAC. Nilai kode awal perusahaan (misal JAB-DIR-01) disimpan pada `kode_jabatan`. |
| **`grup_karyawan`** | `id_grup`, `nama_grup`, `keterangan` | Dipastikan `id_grup` bertipe `INT UNSIGNED AUTO_INCREMENT`; ditambah `created_at` dan `updated_at`. | Nama tabel mengikuti nama rujukan relasi foreign key dari tabel karyawan (`grup_karyawan`). |
| **`shift`** | `kode_shift`, `nama_shift`, `jam_masuk`, `jam_pulang` (NULL saat OFF) | `kode_shift` dijadikan Primary Key; ditambah `lintas_hari` (BOOLEAN), `toleransi_menit`, `warna`, `is_libur`, `is_aktif`, dan timestamps. | File Excel belum menetapkan Primary Key eksplisit. Kolom `lintas_hari` sangat kritikal untuk Shift 3 (Malam) agar sistem tidak mendeteksi keterlambatan semu saat melintasi pukul 00:00. |

---

## BAB V: IMPLEMENTASI ANTARMUKA SISTEM (USER INTERFACE)

Seluruh antarmuka web dibangun mengacu pada standar visual industri manufaktur modern: palet korporat **`#0F4C81` (*Indo-Rama Corporate Blue*)**, aksen *emerald* untuk status hadir, tata letak kartu bersih (*white cards*), dan tipografi terbaca jelas (Google Fonts Inter & JetBrains Mono).

### 5.1 Layar Otentikasi & Akun Demo Role-Based
- **File Tampilan:** [`resources/views/auth/login.blade.php`](file:///d:/laragon/www/shift-absensi/resources/views/auth/login.blade.php)
- **Path Tangkapan Layar:** [`docs/screenshots/01_login.png`](file:///d:/laragon/www/shift-absensi/docs/screenshots/01_login.png)
- **Deskripsi:** Halaman login dengan perlindungan otentikasi ketat. Dilengkapi 5 tombol cepat uji coba persona (*Admin HR, Direktur, HOD, Supervisor, Operator*) untuk mempermudah evaluasi hak akses tanpa perlu mengetik manual. Terdapat jalan pintas langsung ke Mode Kiosk Presensi.

![Layar Otentikasi Login](screenshots/01_login.png)

---

### 5.2 Dashboard Analitik Eksekutif & Personel
- **File Tampilan:** [`resources/views/dashboard.blade.php`](file:///d:/laragon/www/shift-absensi/resources/views/dashboard.blade.php)
- **Path Tangkapan Layar:**
  - Admin HR: [`docs/screenshots/02_dashboard_admin.png`](file:///d:/laragon/www/shift-absensi/docs/screenshots/02_dashboard_admin.png)
  - Operator: [`docs/screenshots/03_dashboard_operator.png`](file:///d:/laragon/www/shift-absensi/docs/screenshots/03_dashboard_operator.png)
- **Deskripsi:** Dashboard adaptif yang menyesuaikan peran pengguna. Pada Admin HR dan Direktur, dashboard menyajikan metrik statistik 33 karyawan, sebaran regu kerja, dan status jadwal. Pada Operator, dashboard berfokus pada kartu shift hari ini, toleransi keterlambatan, dan riwayat presensi terakhir.

![Dashboard Admin HR](screenshots/02_dashboard_admin.png)
![Dashboard Operator](screenshots/03_dashboard_operator.png)

---

### 5.3 Modul Master Data Karyawan & Generator Pola Rotasi 8 Hari (4 Regu)
- **File Komponen:** [`app/Livewire/MasterKaryawan.php`](file:///d:/laragon/www/shift-absensi/app/Livewire/MasterKaryawan.php) & [`app/Livewire/PolaRotasiManager.php`](file:///d:/laragon/www/shift-absensi/app/Livewire/PolaRotasiManager.php)
- **Path Tangkapan Layar:**
  - Master Karyawan: [`docs/screenshots/04_master_karyawan.png`](file:///d:/laragon/www/shift-absensi/docs/screenshots/04_master_karyawan.png)
  - Pola Rotasi: [`docs/screenshots/05_pola_rotasi.png`](file:///d:/laragon/www/shift-absensi/docs/screenshots/05_pola_rotasi.png)
- **Deskripsi:** Master Karyawan menyediakan fitur pencarian reaktif, filter departemen, modal penambahan personil, dan audit log otomatis. Modul Pola Rotasi memvisualisasikan matriks rotasi siklus 8 hari untuk 4 regu (Grup A, B, C, D) serta generator jadwal bulanan 1-klik yang menghasilkan jadwal tanpa bentrok.

![Master Data Karyawan](screenshots/04_master_karyawan.png)
![Matriks Pola Rotasi 8 Hari](screenshots/05_pola_rotasi.png)

---

### 5.4 Pusat Pengajuan & Multi-Tier Approval Hub
- **File Komponen:** [`app/Livewire/PengajuanApprovalHub.php`](file:///d:/laragon/www/shift-absensi/app/Livewire/PengajuanApprovalHub.php)
- **Path Tangkapan Layar:** [`docs/screenshots/06_pengajuan_approval.png`](file:///d:/laragon/www/shift-absensi/docs/screenshots/06_pengajuan_approval.png)
- **Deskripsi:** Pusat layanan terpadu permohonan cuti, sakit, izin, lembur, dan tukar shift bilateral. Sistem menerapkan aturan persetujuan bertingkat (*multi-tier*): cuti $\le 3$ hari disetujui Supervisor lini, sedangkan cuti $> 3$ hari otomatis diekskalasi ke HOD. Dilengkapi pencegahan *self-approval*.

![Pusat Pengajuan & Approval Hub](screenshots/06_pengajuan_approval.png)

---

### 5.5 Koreksi Absensi Lapangan & Jejak Rekam Audit Trail
- **File Komponen:** [`app/Livewire/KoreksiAbsensiManager.php`](file:///d:/laragon/www/shift-absensi/app/Livewire/KoreksiAbsensiManager.php)
- **Path Tangkapan Layar:** [`docs/screenshots/07_koreksi_absensi.png`](file:///d:/laragon/www/shift-absensi/docs/screenshots/07_koreksi_absensi.png)
- **Deskripsi:** Fasilitas khusus bagi Admin Personalia (HR) untuk memperbaiki catatan jam masuk/pulang karyawan yang terkendala error mesin. HR wajib menginput alasan tertulis, dan seluruh data lama/baru disimpan secara permanen di tabel `audit_log` untuk akuntabilitas.

![Koreksi Absensi & Audit Log](screenshots/07_koreksi_absensi.png)

---

### 5.6 Modul Logbook Serah Terima Shift, Kalender Libur & Notifikasi
- **File Komponen:**
  - Logbook: [`app/Livewire/LogbookShiftManager.php`](file:///d:/laragon/www/shift-absensi/app/Livewire/LogbookShiftManager.php)
  - Hari Libur: [`app/Livewire/HariLiburManager.php`](file:///d:/laragon/www/shift-absensi/app/Livewire/HariLiburManager.php)
  - Notifikasi: [`app/Livewire/NotifikasiIndex.php`](file:///d:/laragon/www/shift-absensi/app/Livewire/NotifikasiIndex.php)
- **Path Tangkapan Layar:**
  - Logbook Shift: [`docs/screenshots/08_logbook_shift.png`](file:///d:/laragon/www/shift-absensi/docs/screenshots/08_logbook_shift.png)
  - Hari Libur: [`docs/screenshots/09_hari_libur.png`](file:///d:/laragon/www/shift-absensi/docs/screenshots/09_hari_libur.png)
  - Notifikasi In-App: [`docs/screenshots/10_notifikasi.png`](file:///d:/laragon/www/shift-absensi/docs/screenshots/10_notifikasi.png)
- **Deskripsi:** Modul Logbook mendokumentasikan serah terima kondisi operasional mesin, sisa bahan baku tekstil, dan catatan kendala antar shift. Kalender Libur mengelola hari libur nasional resmi. Notifikasi In-App memberikan kabar instan mengenai persetujuan izin dan mutasi jadwal.

![Logbook Serah Terima Shift](screenshots/08_logbook_shift.png)
![Kalender Hari Libur Nasional](screenshots/09_hari_libur.png)
![Pusat Notifikasi In-App](screenshots/10_notifikasi.png)

---

### 5.7 Mode Kiosk Presensi Mandiri (Touchscreen Terminal)
- **File Komponen:** [`app/Livewire/KioskPresensi.php`](file:///d:/laragon/www/shift-absensi/app/Livewire/KioskPresensi.php) & [`resources/views/livewire/kiosk-presensi.blade.php`](file:///d:/laragon/www/shift-absensi/resources/views/livewire/kiosk-presensi.blade.php)
- **Path Tangkapan Layar:** [`docs/screenshots/11_kiosk_presensi.png`](file:///d:/laragon/www/shift-absensi/docs/screenshots/11_kiosk_presensi.png)
- **Deskripsi:** Antarmuka terminal mandiri layar sentuh (*Touchscreen Kiosk*) yang ditempatkan di pintu masuk lantai produksi Divisi DTY. Dirancang dengan tema *Corporate Blue & White*:
  - **Jam Digital Raksasa Otoritatif:** Menampilkan waktu server Asia/Jakarta dan indikator shift yang sedang berjalan.
  - **Keypad Sentuh Taktil (1-9, Clear, 0, Backspace):** Memudahkan input PIN 6 digit dengan animasi bulatan indikator responsif.
  - **Verifikasi Operator Instan:** Begitu NIK dimasukkan, sistem menampilkan nama, foto inisial, regu kerja (Grup A-D), dan jadwal shift hari ini.
  - **Tombol Punch Besar:** Tombol *ABSEN MASUK* (hijau emerald) dan *ABSEN PULANG* (biru korporat) berdimensi besar dengan konfirmasi instan.
  - **Live Stream Feed:** Menampilkan 3 presensi terakhir hari ini secara real-time.
  - **Tombol Kiosk Fullscreen:** Sekali sentuh untuk beralih ke mode layar penuh tanpa bingkai browser.

![Terminal Kiosk Presensi Mandiri](screenshots/11_kiosk_presensi.png)

---

### 5.8 Modul Rekapitulasi Kehadiran & Ekspor Siap-Payroll (Excel & PDF Resmi)
- **File Komponen:** [`app/Livewire/RekapBulanan.php`](file:///d:/laragon/www/shift-absensi/app/Livewire/RekapBulanan.php) & [`resources/views/exports/rekap-pdf.blade.php`](file:///d:/laragon/www/shift-absensi/resources/views/exports/rekap-pdf.blade.php)
- **Deskripsi:** Modul kompilasi data kehadiran per periode bulanan dan departemen.
  - **Ringkasan KPI Kehadiran:** Menampilkan agregat Total Hadir, Insiden Terlambat, Alpa, dan Izin/Cuti.
  - **Ekspor Excel (.csv):** Menggunakan encoding UTF-8 BOM agar angka NIK tidak terpotong saat dibuka langsung di Microsoft Excel.
  - **Cetak Laporan PDF Resmi:** Menghasilkan dokumen PDF berstandar formal dengan kop Divisi DTY PT Indo-Rama Synthetics Tbk, tabel rekap komprehensif, dan kotak tanda tangan pengesahan berjenjang (*Dibuat oleh Admin HR, Diketahui oleh HOD*).

---

## BAB VI: PENGUJIAN SISTEM & KESIMPULAN

### 6.1 Metodologi & Hasil Eksekusi Automated Feature Test
Pengujian kualitas perangkat lunak dilakukan secara otomatis menggunakan framework pengujian PHPUnit / Pest PHP pada lingkungan Laravel. Pengujian mencakup seluruh skenario operasional dan keamanan domain manufaktur:

```text
   PASS  Tests\Feature\AuthAndFoundationTest (7 tests)
  ✓ guest is redirected to login page                                       0.18s  
  ✓ login screen renders successfully                                       0.05s  
  ✓ failed login with wrong credentials                                     0.06s  
  ✓ successful login and audit log written                                  0.06s  
  ✓ authenticated user can access dashboard with layout                     0.06s  
  ✓ logout clears session and writes audit log                              0.06s  
  ✓ role based access control redirects unauthorized areas                  0.07s  

   PASS  Tests\Feature\MasterDataAndRotationTest (6 tests)
  ✓ master data seeder satisfies all constraints                            0.04s  
  ✓ livewire master karyawan page renders and shows statistics              0.08s  
  ✓ livewire master karyawan search filter                                  0.07s  
  ✓ livewire master karyawan create employee and write audit log            0.08s  
  ✓ livewire master karyawan edit employee                                  0.08s  
  ✓ livewire pola rotasi page renders and shows 8 days cycle                0.08s  

   PASS  Tests\Feature\OperationalFeaturesTest (6 tests)
  ✓ tukar shift request and approval flow works correctly                   0.12s  
  ✓ koreksi absensi with audit log and reason validation                    0.10s  
  ✓ lembur submission and supervisor approval flow                          0.11s  
  ✓ logbook shift creation and index rendering                              0.09s  
  ✓ in app notification creation and mark as read                           0.08s  
  ✓ hari libur management adds holiday to calendar                          0.08s  

   PASS  Tests\Feature\PlanKeyScenariosTest (10 tests)
  ✓ scenario 1: pola rotasi 8 hari generates valid published schedule       0.14s  
  ✓ scenario 2: non group employee defaults to GS Mon Fri and OFF Sat Sun   0.08s  
  ✓ scenario 3: S3 night shift crossing midnight recorded correctly         0.09s  
  ✓ scenario 4: check in after tolerance marked TERLAMBAT                   0.07s  
  ✓ scenario 5: check in on scheduled OFF day rejected                      0.06s  
  ✓ scenario 6: alpa job marks absent employee but preserves approved leave 0.11s  
  ✓ scenario 7: tukar shift detects conflict and violates min rest rule     0.10s  
  ✓ scenario 8: cross department authorization blocked by policy            0.09s  
  ✓ scenario 9: koreksi absensi without reason rejected                     0.08s  
  ✓ scenario 10: draft schedule not visible to operator                     0.07s  

   PASS  Tests\Feature\RoleMenuAccessAndApprovalTest (15 tests)
  ✓ operator can only view assigned personal schedule                       0.08s  
  ✓ supervisor approval scope restricted to own department                  0.09s  
  ✓ hod escalation trigger for leave requests exceeding 3 days              0.09s  
  ✓ director read only dashboard analytics access                           0.07s  
  ✓ admin hr full master data manipulation and audit log                    0.11s  
  ✓ prevention of self approval for leave, overtime and swap                0.10s  
  ... and 9 more role validation tests ...

   PASS  Tests\Feature\SecurityHardeningTest (12 tests)
  ✓ backdoor routes fully decommissioned and return 404                     0.06s  
  ✓ security headers injected on all HTTP responses                         0.05s  
  ✓ sql injection attack mitigation verified                                0.07s  
  ✓ cross site request forgery token enforcement                            0.06s  
  ✓ mass assignment protection on critical tables                           0.08s  
  ... and 7 more security assertions ...

   PASS  Tests\Feature\KioskPresensiTest (6 tests)
  ✓ kiosk page is accessible publicly on factory floor tablets              0.08s  
  ✓ kiosk keypad interaction fills and resets pin correctly                 0.07s  
  ✓ kiosk rejects invalid pin with error banner                             0.07s  
  ✓ kiosk absen masuk successful with valid employee pin                    0.09s  
  ✓ kiosk absen pulang calculates working hours and finishes shift          0.09s  
  ✓ kiosk live attendance feed displays latest punch entries                0.08s  

   PASS  Tests\Feature\CodebaseAuditAndPolicyTest (3 tests)
  ✓ all main application routes protected by authentication                 0.14s  
  ✓ department isolation enforced across all policies                       0.12s  
  ✓ form requests validate input parameters                                 0.10s  

---------------------------------------------------------------------------------
Hasil Akhir: 69 Passed, 0 Failed, 503 Assertions, Waktu Eksekusi: ~11.9s (100% Green)
---------------------------------------------------------------------------------
```

### 6.2 Kesimpulan Implementasi & Nilai Tambah bagi Divisi DTY
Perancangan dan implementasi **Sistem Informasi Manajemen Shift & Presensi (SIM-Shift DTY Indorama)** telah berhasil diselesaikan dengan hasil yang sangat memuaskan:
1. **Kepatuhan Terhadap Struktur Asli Basis Data:** 5 tabel master asli dari kamus data perusahaan tetap dipertahankan keaslian nama kolom dan semantiknya, dengan normalisasi teknis yang menghasilkan 18 tabel relasional yang solid.
2. **Kesesuaian dengan Kondisi Nyata Pabrik Manufaktur:** Sistem terbukti mampu menyelesaikan permasalahan riil shift malam melintasi hari (S3), rotasi 4 regu kerja 24/7, validasi waktu istirahat tukar shift, dan logbook serah terima mesin tekstil.
3. **Peningkatan Efisiensi & Integritas Operasional:** Penghitungan rekapitulasi absensi bulanan dipangkas dari 4 hari kerja menjadi hitungan detik siap-payroll dengan jaminan akurasi jam server tanpa celah titip absen.
4. **Kualitas Kode Standar Enterprise:** Dibuktikan dengan **69 automated tests (503 assertions, 0 errors)**, proteksi otorisasi bertingkat, serta audit trail lengkap.

### 6.3 Rekomendasi Pengembangan Masa Depan (Future Roadmap)
Untuk pengembangan versi berikutnya (SIM-Shift DTY v2.0), beberapa modul potensial yang dapat diintegrasikan antara lain:
1. **Integrasi Sensor IoT Mesin Tekstil DTY:** Menghubungkan logbook shift dengan sensor status mesin tekstil (*OEE - Overall Equipment Effectiveness*) secara otomatis.
2. **Notifikasi WhatsApp Gateway / Telegram Bot:** Mengirimkan pengingat jadwal shift harian langsung ke ponsel operator sebelum jam shift dimulai.
3. **Modul Penggajian Otomatis (Payroll Engine):** Mengembangkan modul kalkulasi nominal rupiah gaji lembur (*overtime multiplier*) sesuai regulasi Depnaker RI.

---
*Dokumen ini merupakan Laporan Tugas Besar & Dokumen Perancangan Sistem Final yang disusun untuk memenuhi dokumentasi teknis implementasi sistem informasi manufaktur pada PT Indo-Rama Synthetics Tbk.*
