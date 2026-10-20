# Prompt Google Stitch - Sistem Manajemen Shift & Absensi Operator

Arsip saja: desain di Stitch sudah jadi, jadi agent tidak perlu membaca file ini. Dipakai kalau mau membuat ulang atau menambah layar.

## Prompt Global

```
Design a responsive web app for a factory called 'Sistem Manajemen Shift & Absensi Operator' (shift scheduling and attendance for plant operators). All UI text in Indonesian. Style: clean, modern industrial, high contrast, large touch targets (min 48px), rounded corners 12px, subtle shadows, Inter font, light mode. Primary color #0F4C81, background #F8FAFC, text slate-900.
Shift color chips: GS #2563EB, S1 (Pagi) #F59E0B, S2 (Siang) #EA580C, S3 (Malam) #4338CA, OFF #94A3B8. Status colors: Hadir #16A34A, Terlambat #CA8A04, Alpa #DC2626, Izin/Sakit/Cuti #0284C7. Always pair color with a text label.
Desktop layout: left sidebar navigation with icons, top bar with search, notification bell, and user avatar with role label. Mobile layout: bottom navigation bar. Departments: Personalia, Logistik, Produksi, QCT, Packing. Roles: Operator, Supervisor, HOD, Direktur, Admin HR.
```

## Prompt per layar

```
Layar 1 - Login: centered card with factory logo placeholder, title 'Masuk ke Sistem', fields 'ID Karyawan' and 'Password', primary button 'Masuk', small link 'Lupa password? Hubungi Personalia'. Soft industrial illustration on the left side for desktop.

Layar 2 - Kiosk Absensi (tablet landscape 1280x800): very large live clock and date at the top, current shift info. Center: input 'ID Karyawan', numeric PIN pad for 6-digit PIN, and a 'Scan QR Kartu' button. Two huge buttons 'MASUK' (green) and 'PULANG' (blue). After success show a confirmation card with employee name, department, shift chip (e.g. S3 Malam), time, and status 'Tepat Waktu' or 'Terlambat 12 menit'.

Layar 3 - Dashboard Supervisor (desktop): top summary cards 'Hadir', 'Belum Hadir', 'Terlambat', 'Izin/Sakit/Cuti' for the current shift. Below: table of operators in the shift with columns Nama, Grup, Shift, Jam Masuk, Status (colored badge). Right side panel 'Menunggu Persetujuan' list with Setuju and Tolak buttons. A small bar chart of attendance for the last 7 days.

Layar 4 - Kalender Jadwal (desktop): filter bar (Departemen, Grup, Shift, Bulan). Monthly grid where rows are employees and columns are dates, cells show colored shift chips (GS, S1, S2, S3, OFF). Top right buttons 'Buat Jadwal dari Pola' and 'Publish'. Show a 'Draft' badge on unpublished months and a legend of shift colors.

Layar 5 - Pola Rotasi & Generator Jadwal: left: editable table with rows Grup A-D and columns Hari 1-8 filled with colored shift chips, dropdown per cell. Right: form 'Generate Jadwal' with Departemen, Bulan, Pola, tanggal mulai siklus, and a preview panel showing coverage per day (S1, S2, S3 each filled) with a green check or red warning.

Layar 6 - Beranda Operator (mobile 390x844): greeting with name and shift chip, big card 'Shift Berikutnya' with date and hours, status absen hari ini, mini month calendar with colored dots, quick actions grid 'Ajukan Izin', 'Tukar Shift', 'Ajukan Lembur', 'Riwayat Absen'. Bottom navigation: Beranda, Jadwal, Pengajuan, Profil.

Layar 7 - Pengajuan & Approval: list with tabs 'Semua', 'Menunggu', 'Disetujui', 'Ditolak' and type filters (Cuti, Izin, Sakit, Tukar Shift, Lembur). Selecting a row opens a detail drawer with requester, dates, reason, attachment preview, approval timeline (Operator > Supervisor > HOD), a note field, and buttons 'Setuju' and 'Tolak'.

Layar 8 - Rekap Bulanan: filters (Bulan, Departemen), summary cards (Persentase Kehadiran, Total Terlambat, Total Alpa, Total Lembur), data table per employee with columns Hadir, Terlambat, Alpa, Izin, Sakit, Cuti, Jam Kerja, Lembur, a donut chart of status distribution, and buttons 'Export Excel' and 'Export PDF'.

Layar 9 - Master Data Karyawan: searchable table (ID Karyawan, Nama, Departemen, Jabatan, Level, Grup, Status) with filters and 'Tambah Karyawan' button; right drawer form with fields ID Karyawan, Nama, Jenis Kelamin, Tanggal Lahir, Departemen, Jabatan, Grup (optional), Status Aktif toggle.
```
