# AGENTS.md - Sistem Manajemen Shift & Absensi Operator

## Gambaran
Aplikasi web untuk mengelola jadwal shift dan absensi operator pabrik. Bahasa UI: Indonesia. Zona waktu: Asia/Jakarta.

Dokumen acuan (baca sebelum menulis kode):
- docs/BRD.md, docs/PRD.md, docs/SRS.md
- docs/DATABASE_DESIGN.md (skema database final)
- docs/UI_UX.md (UI/UX, stack, struktur folder)
- docs/PLAN.md (fase pengerjaan, skenario uji)
- DESIGN.md (design token hasil ekstrak dari Google Stitch, dibuat oleh agent lewat MCP)

## Stack
Laravel (versi stabil terbaru yang terpasang, minimal 11), PHP sesuai requirement Laravel, MySQL 8, Blade + Livewire 3 + Alpine.js, Tailwind CSS, Chart.js, maatwebsite/excel, barryvdh/laravel-dompdf, simplesoftwareio/simple-qrcode.

## Perintah
- composer install
- cp .env.example .env && php artisan key:generate
- php artisan migrate --seed
- npm install && npm run dev
- php artisan serve
- php artisan schedule:work   (job ALPA otomatis)
- php artisan test

## Aturan domain (wajib dipatuhi)
1. id_karyawan (VARCHAR 20) adalah PK karyawan sekaligus username login.
2. Satu karyawan hanya punya satu jadwal per tanggal (UNIQUE id_karyawan + tanggal).
3. tanggal_shift = tanggal MULAI shift. Shift S3 melewati tengah malam tetap dicatat pada tanggal mulai.
4. Waktu absensi selalu dari server (now() Asia/Jakarta). Jangan menerima waktu dari client.
5. Karyawan dengan id_grup NULL = GS Senin-Jumat, Sabtu-Minggu OFF.
6. Shift OFF tidak butuh absensi.
7. Perhitungan status absensi (HADIR, TERLAMBAT, pulang cepat, ALPA) hanya di App\Services\AttendanceService.
8. Jadwal berstatus DRAFT tidak boleh terlihat oleh Operator.
9. Parameter bisnis (toleransi, jendela absen, batas cuti ke HOD, istirahat minimal) dibaca dari tabel pengaturan, jangan hardcode.
10. Perubahan jadwal, koreksi absensi, dan keputusan approval wajib menulis audit_log (data lama dan baru).
11. Role: ADMIN_HR, DIREKTUR, HOD, SUPERVISOR, OPERATOR. Akses lewat Policy. HOD dan Supervisor hanya melihat dan menyetujui data departemennya.
12. Approval: Operator -> Supervisor -> HOD (jika cuti melebihi batas hari). Approver harus satu departemen dan berlevel lebih tinggi.

## Konvensi kode
- Nama tabel dan kolom snake_case sesuai database perusahaan (karyawan, jadwal_shift, id_karyawan). Model PascalCase singular dengan $table dan $primaryKey eksplisit.
- Controller tipis; logika bisnis di app/Services; validasi pakai FormRequest.
- Gunakan DB::transaction untuk generate jadwal, tukar shift, dan approval.
- Eager loading untuk list; hindari N+1.
- Satu tabel satu migration, urutan sesuai foreign key.
- 5 tabel master (karyawan, departemen, jabatan, grup_karyawan, shift) mengikuti Excel perusahaan: nama kolom jangan diubah; perbedaan yang diizinkan ada di docs/DATABASE_DESIGN.md bagian 5.6. created_at memakai useCurrent().
- Jangan commit .env atau data riil perusahaan. Seeder hanya data dummy.
- Teks UI Bahasa Indonesia; format tanggal DD-MM-YYYY, jam 24 jam.

## Definition of Done
- Migration, seeder, Policy, dan FormRequest tersedia.
- Feature test untuk alur utama (jadwal, absensi shift malam, approval, otorisasi).
- Responsif di 360 px (operator) dan 1280x800 (kiosk).
- Tidak ada N+1 query; audit_log tertulis.

## Urutan pengerjaan
1) Fondasi dan auth 2) Master data dan pola rotasi 3) Jadwal 4) Absensi 5) Pengajuan, tukar shift, lembur, logbook 6) Dashboard dan rekap 7) Testing.

## Desain UI
Jika koneksi MCP Google Stitch tersedia, ambil layar dan design token langsung dari project Stitch lewat MCP (jangan menyalin HTML manual). Jika tidak tersedia, ikuti docs/UI_UX.md (warna shift dan status, prinsip UX).
