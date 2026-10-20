# BRD - Sistem Manajemen Shift & Absensi Operator

Proyek: Tugas Project Manufaktur | Versi 1.0 | 20 Oktober 2026

Catatan: penomoran bab mengikuti dokumen rancangan lengkap (Bab 0 dan Bab 2). Rujukan "Bab 9" ada di docs/PLAN.md, "Bab 5" di docs/DATABASE_DESIGN.md.

## 0. Ringkasan Eksekutif

Sistem Manajemen Shift & Absensi Operator adalah aplikasi web untuk mendigitalisasi pengaturan jadwal shift dan pencatatan kehadiran operator di lapangan (pabrik), menggantikan pencatatan manual berbasis kertas, papan tulis, dan Excel. Rancangan ini disusun dari database asli perusahaan (file Excel berisi 5 tabel master: Karyawan, Departemen, Jabatan, Grup, Shift) dan dilengkapi tabel transaksi yang belum ada di database tersebut.

Empat pilar sistem:

1. Jadwal shift: rotasi per grup (GS, S1, S2, S3, OFF) dibuat sekali lewat pola rotasi, lalu dihasilkan otomatis per bulan.
2. Absensi: masuk dan pulang divalidasi terhadap shift yang dijadwalkan, termasuk shift malam yang melewati tengah malam.
3. Pengajuan dan approval: izin, sakit, cuti, tukar shift, dan lembur dengan persetujuan berjenjang sesuai level jabatan.
4. Pelaporan: dashboard real-time dan rekap bulanan siap-payroll (export Excel dan PDF).

Catatan penting: file Excel dari perusahaan hanya berisi kamus data (struktur tabel), belum berisi baris data karyawan. Jadi untuk demo perlu data dummy (seed), dan beberapa asumsi (jam shift, jumlah grup) ditandai untuk dikonfirmasi pada Bab 9.

## 2. BRD (Business Requirements Document)

### 2.1 Latar belakang dan masalah di lapangan

Pabrik dengan sistem shift rotasi umumnya menghadapi masalah berikut. Masalah ini kondisi umum yang perlu divalidasi lewat observasi atau wawancara singkat ke perusahaan (lihat Bab 9).

| Kondisi di lapangan (manual) | Dampak | Solusi digital |
|---|---|---|
| Jadwal shift disusun di Excel atau papan tulis | Salah input, bentrok, operator tidak tahu jadwal terbaru | Generator jadwal dari pola rotasi, kalender jadwal real-time |
| Absensi tanda tangan kertas atau dicatat supervisor | Titip absen, data terlambat dan hilang | Absensi digital tervalidasi shift dan waktu server |
| Izin, sakit, tukar shift lewat chat atau lisan | Tidak terdokumentasi, sulit dilacak | Pengajuan online dengan approval berjenjang |
| Rekap kehadiran untuk payroll dihitung manual | Lama dan rawan selisih | Rekap otomatis, export Excel dan PDF |
| Supervisor tidak tahu siapa yang hadir saat ini | Keputusan penempatan lambat | Dashboard kehadiran per shift dan departemen real-time |
| Serah terima antar shift tidak tercatat | Informasi kendala produksi hilang | Logbook serah terima shift |

### 2.2 Tujuan bisnis dan indikator keberhasilan

| Tujuan | Indikator (target proyek) |
|---|---|
| Mempercepat penyusunan jadwal | Jadwal 1 bulan satu departemen selesai kurang dari 10 menit |
| Merapikan data kehadiran | 100% absensi tersimpan digital dengan waktu server |
| Mempercepat rekap | Rekap bulanan tampil dan bisa diexport kurang dari 1 menit |
| Transparansi izin dan tukar shift | 100% pengajuan tercatat beserta pemberi persetujuan |
| Akses informasi operator | Operator bisa melihat jadwal sendiri dari HP kapan saja |

### 2.3 Ruang lingkup

Di dalam lingkup (in scope):

- Manajemen master data (karyawan, departemen, jabatan, grup, shift)
- Pola rotasi dan penyusunan jadwal shift
- Absensi masuk dan pulang, status kehadiran otomatis
- Pengajuan izin, sakit, cuti, tukar shift, dan lembur beserta approval
- Logbook serah terima shift
- Dashboard, rekap, dan export laporan
- Manajemen akun dan hak akses berbasis role, audit log

Di luar lingkup versi 1 (out of scope):

- Perhitungan gaji dan payroll lengkap (sistem hanya menghasilkan rekap siap-payroll)
- Integrasi perangkat keras fingerprint atau face recognition
- Aplikasi mobile native (cukup web responsif)
- Integrasi BPJS, pajak, dan sistem ERP perusahaan

### 2.4 Stakeholder

| Stakeholder | Peran | Kepentingan |
|---|---|---|
| Direktur | Pengambil keputusan | Melihat ringkasan kehadiran seluruh departemen |
| HOD (Head of Department) | Kepala departemen | Memantau dan menyetujui pengajuan di departemennya |
| Supervisor | Pengawas lapangan | Menyusun jadwal, memantau kehadiran, menyetujui pengajuan tim |
| Operator | Pengguna utama | Absen, melihat jadwal, mengajukan izin dan tukar shift |
| Personalia (HR/PRS) | Administrator sistem | Kelola master data dan akun, koreksi data, rekap bulanan |
| Dosen dan penilai | Penilai tugas | Menilai kesesuaian dengan data perusahaan dan kelengkapan dokumen |

### 2.5 Asumsi, batasan, dan risiko

| Jenis | Uraian | Mitigasi |
|---|---|---|
| Asumsi | Jam kerja tiap shift mengikuti tabel shift perusahaan | Konfirmasi jam asli; nilai di rancangan ini hanya contoh |
| Asumsi | Operator punya HP dengan browser, atau tersedia 1 tablet/PC kiosk | Sediakan mode kiosk sebagai jalur utama |
| Batasan | Waktu pengerjaan tugas terbatas | Prioritas MoSCoW; Must dikerjakan dulu |
| Risiko | Titip absen | Absen via kiosk ID + PIN, QR dinamis, audit log |
| Risiko | Data riil perusahaan sensitif | Pakai data dummy untuk demo, akses berbasis role, password di-hash |
| Risiko | Shift malam salah dihitung (lewat tengah malam) | Aturan tanggal shift (BR-03) dan test case khusus |
