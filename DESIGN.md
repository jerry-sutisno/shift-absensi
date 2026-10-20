# DESIGN.md - Design Token & Komponen UI (Ekstrak Google Stitch)

Dokumen ini adalah acuan resmi desain antarmuka (UI/UX) untuk aplikasi **Sistem Manajemen Shift & Absensi Operator**, yang diekstrak langsung dari project Google Stitch **'Sistem Manajemen Shift Operator'** (Project ID: `14276075044353988999`).

> [!IMPORTANT]
> **Aturan Domain:** Jangan menyalin HTML mentah dari Stitch ke dalam aplikasi. Gunakan token, variabel Tailwind CSS, dan pola komponen di bawah ini untuk membangun template Blade / Livewire yang bersih, modular, dan terstruktur.

---

## 1. Daftar Layar & Referensi Visual

Tangkapan layar asli resolusi tinggi dari Google Stitch telah diunduh dan disimpan di direktori [`design-reference/`](file:///d:/laragon/www/shift-absensi/design-reference/) sebagai acuan implementasi:

| # | Nama Layar di Stitch | Tipe / Target Resolusi | File Screenshot Referensi |
|---|---|---|---|
| 01 | **Masuk ke Sistem - PlantShift OS** *(Layar 1)* | Desktop (1280px+) | [`01_Masuk_ke_Sistem_-_PlantShift_OS.png`](file:///d:/laragon/www/shift-absensi/design-reference/10_Masuk_ke_Sistem_-_PlantShift_OS.png) |
| 02 | **Layar 2 - Kiosk Absensi Operator** | Tablet Landscape (1280x800) | [`02_Layar_2_-_Kiosk_Absensi_Operator_Tablet_Landscape.png`](file:///d:/laragon/www/shift-absensi/design-reference/01_Layar_2_-_Kiosk_Absensi_Operator_Tablet_Landscape.png) |
| 03 | **Layar 3 - Dashboard Supervisor** | Desktop (1280px+) | [`03_Layar_3_-_Dashboard_Supervisor.png`](file:///d:/laragon/www/shift-absensi/design-reference/02_Layar_3_-_Dashboard_Supervisor.png) |
| 04 | **Layar 4 - Kalender Jadwal (Bulanan)** | Desktop (1280px+) | [`04_Layar_4_-_Kalender_Jadwal_Bulanan.png`](file:///d:/laragon/www/shift-absensi/design-reference/03_Layar_4_-_Kalender_Jadwal_Bulanan.png) |
| 05 | **Layar 5 - Pola Rotasi & Generator Jadwal** | Desktop (1280px+) | [`05_Layar_5_-_Pola_Rotasi_Generator_Jadwal.png`](file:///d:/laragon/www/shift-absensi/design-reference/04_Layar_5_-_Pola_Rotasi_Generator_Jadwal.png) |
| 06 | **Layar 6 - Beranda Operator (Mobile)** | Mobile (390x844) | [`06_Layar_6_-_Beranda_Operator_Mobile.png`](file:///d:/laragon/www/shift-absensi/design-reference/05_Layar_6_-_Beranda_Operator_Mobile.png) |
| 07 | **Layar 7 - Pengajuan & Approval** | Desktop (1280px+) | [`07_Layar_7_-_Pengajuan_Approval.png`](file:///d:/laragon/www/shift-absensi/design-reference/06_Layar_7_-_Pengajuan_Approval.png) |
| 08 | **Layar 8 - Rekap Bulanan** | Desktop (1280px+) | [`08_Layar_8_-_Rekap_Bulanan.png`](file:///d:/laragon/www/shift-absensi/design-reference/07_Layar_8_-_Rekap_Bulanan.png) |
| 09 | **Layar 9 - Master Data Karyawan** | Desktop (1280px+) | [`09_Layar_9_-_Master_Data_Karyawan.png`](file:///d:/laragon/www/shift-absensi/design-reference/08_Layar_9_-_Master_Data_Karyawan.png) |
| 10 | **Jadwal & Roster Shift Operator** *(Varian Detail)* | Desktop (1280px+) | [`10_Jadwal_Roster_Shift_Operator.png`](file:///d:/laragon/www/shift-absensi/design-reference/09_Jadwal_Roster_Shift_Operator.png) |
| 11 | **Monitoring Absensi Real-Time** *(Varian Feed)* | Desktop (1280px+) | [`11_Monitoring_Absensi_Real-Time.png`](file:///d:/laragon/www/shift-absensi/design-reference/11_Monitoring_Absensi_Real-Time.png) |
| 12 | **Pengajuan & Approval Operasional** *(Varian Tab)* | Desktop (1280px+) | [`12_Pengajuan_Approval_Operasional.png`](file:///d:/laragon/www/shift-absensi/design-reference/12_Pengajuan_Approval_Operasional.png) |
| 13 | **Portal Presensi Operator** *(Varian Kiosk)* | Tablet / Desktop | [`13_Portal_Presensi_Operator.png`](file:///d:/laragon/www/shift-absensi/design-reference/13_Portal_Presensi_Operator.png) |

---

## 2. Palet Warna (Color Tokens)

### 2.1 Warna Utama & Permukaan (Brand & Surface)
Tema mengadopsi palet modern industrial dengan kontras tinggi dan permukaan lembut (soft blue-gray).

| Token Desain | Kode HEX | Kelas Tailwind Rekomendasi | Penggunaan |
|---|---|---|---|
| **Primary** | `#0F4C81` | `bg-[#0F4C81]` / `text-[#0F4C81]` | Brand industri pabrik, header aktif, tombol utama |
| **Primary Container** | `#00355F` | `bg-[#00355F]` | Tombol penekanan tinggi, status gelap |
| **Secondary** | `#2563EB` | `bg-blue-600` | Aksi interaktif, tautan, chip General Shift |
| **Tertiary** | `#0284C7` | `bg-sky-600` | Aksen analitik, info status, perizinan |
| **Background** | `#F8FAFC` | `bg-slate-50` / `bg-[#F8F9FF]` | Latar belakang seluruh halaman aplikasi |
| **Surface Lowest** | `#FFFFFF` | `bg-white` | Kartu putih bersih, modal, bar samping (sidebar) |
| **Surface Low** | `#EFF4FF` | `bg-slate-100` / `bg-[#EFF4FF]` | Latar belakang input, strip tabel header |
| **Surface Container** | `#E5EEFF` | `bg-blue-50` | Kontainer seleksi, hover baris tabel |
| **Border / Outline** | `#E2E8F0` | `border-slate-200` | Garis batas kartu, tabel, dan pemisah |
| **Text Primary** | `#0B1C30` | `text-slate-900` | Judul, angka metrik tebal, teks utama |
| **Text Muted** | `#64748B` | `text-slate-500` | Label metadata, placeholder, subteks |

---

### 2.2 Warna Chip Shift (Shift Chips)
Chip shift wajib **selalu menampilkan label teks** bersamaan dengan warnanya (aksesibilitas & anti ambiguitas).

| Shift | Label UI | Hex Aksen | Background Badge | Warna Teks | Kelas Tailwind Komponen |
|---|---|---|---|---|---|
| **GS** | General Shift (08:00 - 17:00) | `#2563EB` | `#DBEAFE` | `#1E40AF` | `bg-blue-100 text-blue-900 font-bold border border-blue-200` |
| **S1** | Shift 1 / Pagi (07:00 - 15:00) | `#F59E0B` | `#FEF3C7` | `#92400E` | `bg-amber-100 text-amber-900 font-bold border border-amber-200` |
| **S2** | Shift 2 / Siang (15:00 - 23:00) | `#EA580C` | `#FFEDD5` | `#9A3412` | `bg-orange-100 text-orange-950 font-bold border border-orange-200` |
| **S3** | Shift 3 / Malam (23:00 - 07:00) | `#4338CA` | `#EEF2FF` | `#3730A3` | `bg-indigo-100 text-indigo-950 font-bold border border-indigo-200` |
| **OFF** | Libur Rutin / Off | `#94A3B8` | `#F1F5F9` | `#475569` | `bg-slate-100 text-slate-700 font-semibold border border-slate-200` |

---

### 2.3 Warna Status Absensi (Attendance Status)

| Status | Makna & Toleransi | Hex Aksen | Background | Warna Teks | Kelas Tailwind |
|---|---|---|---|---|---|
| **Hadir / Tepat Waktu** | Datang dalam toleransi | `#16A34A` | `#F0FDF4` | `#15803D` | `bg-emerald-50 text-emerald-700 border border-emerald-200` |
| **Terlambat** | Melebihi toleransi menit | `#CA8A04` | `#FEF3C7` | `#92400E` | `bg-amber-50 text-amber-800 border border-amber-200` |
| **Alpa** | Tidak hadir tanpa keterangan | `#DC2626` | `#FEF2F2` | `#991B1B` | `bg-rose-50 text-rose-700 border border-rose-200` |
| **Izin / Sakit / Cuti** | Disetujui pihak berwenang | `#0284C7` | `#F0F9FF` | `#0369A1` | `bg-sky-50 text-sky-700 border border-sky-200` |
| **Tukar Shift** | Pengajuan pergantian shift | `#7C3AED` | `#F5F3FF` | `#5B21B6` | `bg-purple-50 text-purple-700 border border-purple-200` |

---

## 3. Tipografi (Typography)

Menggunakan jenis huruf tunggal: **Inter** (`font-sans`), dengan varian ketebalan 400 (Regular), 500 (Medium), 600 (Semibold), dan 700 (Bold).

| Token Stitch | Ukuran | Line-Height | Weight | Letter Spacing | Penggunaan di Aplikasi |
|---|---|---|---|---|---|
| **display-lg** | 32px (`2rem`) | 40px | Bold (700) | `-0.02em` | Jam digital besar pada Kiosk, judul login |
| **headline-lg** | 24px (`1.5rem`) | 32px | Semibold (600) | `-0.015em` | Judul halaman utama, tombol aksi besar Kiosk |
| **headline-md** | 20px (`1.25rem`) | 28px | Semibold (600) | `-0.01em` | Judul kartu statistik, tombol keypad angka PIN |
| **title-sm** | 16px (`1rem`) | 24px | Semibold (600) | `0` | Header bagian kartu, nama brand sidebar |
| **body-lg** | 16px (`1rem`) | 24px | Regular (400) | `0` | Paragraf pengantar, form input teks besar |
| **body-md** | 14px (`0.875rem`)| 20px | Regular (400) | `0` | Isi tabel data, teks deskripsi keterangan |
| **body-sm** | 12px (`0.75rem`) | 16px | Regular (400) | `0` | Catatan kaki, bantuan validasi form input |
| **label-lg** | 14px (`0.875rem`)| 20px | Semibold (600) | `0.01em` | Tombol umum, menu navigasi aktif/inaktif |
| **label-md** | 12px (`0.75rem`) | 16px | Semibold (600) | `0.02em` | Label kolom tabel (`th`), tab filter |
| **label-sm** | 11px (`0.6875rem`)| 14px | Bold (700) | `0.04em` | Label kategori kapital, badge status sempit |
| **tabular-time**| 14px (`0.875rem`)| 20px | Medium (500) | `0` | Jam masuk/pulang, durasi lembur (`font-mono`) |

---

## 4. Spacing, Radius, dan Bayangan (Elevation)

### 4.1 Spacing Scale
- `space-xs`: `0.25rem` (4px)
- `space-sm`: `0.5rem` (8px)
- `space-md`: `0.75rem` (12px)
- `space-lg`: `1.0rem` (16px)
- `space-xl`: `1.5rem` (24px)
- `space-2xl`: `2.0rem` (32px)

### 4.2 Sudut Lengkung (Border Radius)
- `rounded-lg` (`8px`): Elemen kecil (input filter, chip kalender, tombol sekunder kecil).
- `rounded-xl` (`12px`): **Standar utama** (kartu konten, baris menu sidebar, modal dialog).
- `rounded-2xl` (`16px`): Elemen besar (kartu metrik summary, tombol raksasa Kiosk MASUK/PULANG).
- `rounded-full` (`9999px`): Badge status bulat, foto avatar pengguna.

### 4.3 Bayangan (Elevation & Shadows)
- `shadow-sm`: `0 1px 2px 0 rgb(0 0 0 / 0.05)` (kartu standar, tabel).
- `shadow-soft`: `0 1px 8px 0 rgba(0, 0, 0, 0.04)` (sidebar desktop, kartu dashboard mengambang).
- `shadow-md`: `0 4px 6px -1px rgb(0 0 0 / 0.08)` (dropdown menu, popover tanggal).
- `shadow-lg`: `0 10px 15px -3px rgb(0 0 0 / 0.1)` (tombol utama Kiosk, drawer formulir samping).
- `shadow-inner`: `inset 0 2px 4px 0 rgb(0 0 0 / 0.05)` (field input pencarian & input PIN Kiosk).

---

## 5. Pola Komponen UI (Component Patterns)

### 5.1 Navigasi Desktop (Sidebar)
- **Dimensi:** Lebar tetap `w-72` (288px), tinggi penuh `h-screen fixed left-0 top-0`, latar belakang `bg-white`, border kanan `border-r border-slate-200`, bayangan `shadow-soft`.
- **Brand Header:** Tinggi `h-16 px-6`, memuat logo pabrik, teks nama aplikasi `PlantShift OS` (`font-bold text-[#0F4C81]`), dan sub-teks `Shift & Absensi` (`text-slate-500 text-xs`).
- **Item Navigasi Inaktif:**
  ```html
  <a class="h-12 px-4 rounded-xl flex items-center gap-3 text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-medium text-sm transition-colors">
    <span class="material-symbols-outlined text-[22px]">calendar_month</span>
    <span>Kalender Shift</span>
  </a>
  ```
- **Item Navigasi Aktif:**
  ```html
  <a class="h-12 px-4 rounded-xl flex items-center gap-3 bg-[#0F4C81] text-white font-semibold text-sm shadow-sm">
    <span class="material-symbols-outlined text-[22px]">dashboard</span>
    <span>Dashboard Supervisor</span>
  </a>
  ```
- **Footer Profil Pengguna:** Terletak di bagian bawah sidebar, menampilkan avatar, nama karyawan, departemen, dan chip role (misal: `SUPERVISOR`, `HOD`).

---

### 5.2 Tombol (Buttons)
Semua tombol memiliki tinggi interaktif minimal 40px (desktop) atau 48px+ (touch target tablet/kiosk):

1. **Tombol Primary (Aksi Utama):**
   `h-11 px-5 rounded-xl bg-[#0F4C81] text-white font-semibold text-sm hover:bg-[#00355F] active:scale-[0.98] transition-all shadow-sm flex items-center gap-2`
2. **Tombol Sekunder (Filter / Batal):**
   `h-11 px-4 rounded-xl bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 active:scale-[0.98] transition-all flex items-center gap-2`
3. **Tombol Bahaya / Tolak (Danger):**
   `h-11 px-4 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 font-semibold text-sm transition-all`
4. **Tombol Persetujuan Cepat (Approval Setuju):**
   `h-9 px-3.5 rounded-lg bg-emerald-600 text-white font-semibold text-xs hover:bg-emerald-700 transition-colors shadow-sm`

---

### 5.3 Kartu & Ringkasan Metrik (Cards & Stats)
- **Kartu Konten Standar:**
  `p-6 rounded-xl bg-white border border-slate-200 shadow-sm flex flex-col`
- **Kartu Metrik KPI (Dashboard):**
  Latar putih dengan icon di container berwarna, angka metrik `font-bold text-3xl text-slate-900`, label deskripsi `text-slate-500 text-sm font-medium`, dan badge tren / persentase.

---

### 5.4 Tabel Data Karyawan & Absensi
- **Struktur Pembungkus:** `overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm`.
- **Header Tabel (`<thead>`):**
  `bg-slate-50 text-slate-600 font-semibold text-xs uppercase tracking-wider border-b border-slate-200`
- **Kolom Header (`<th>`):**
  `py-3.5 px-4 text-left font-semibold`
- **Baris Tabel (`<tr>`):**
  `border-b border-slate-100 hover:bg-slate-50/80 transition-colors`
- **Sel Tabel (`<td>`):**
  `py-3.5 px-4 text-sm text-slate-800`

---

### 5.5 Kiosk Absensi Operator (Tablet Landscape 1280x800)
- **Jam & Kalender Digital (Atas):** Teks jam super besar `font-bold text-5xl text-[#0F4C81] tracking-tight`, tanggal format Indonesia lengkap, dan informasi shift aktif saat ini.
- **Form Input & PIN Pad (Tengah):**
  - Input ID Karyawan / Scan Barcode: `h-14 bg-slate-100 rounded-xl px-4 text-lg font-bold text-slate-900 shadow-inner`.
  - Tombol Angka (Keypad 0-9): Tombol bujur sangkar `h-16 rounded-xl bg-slate-100 text-[#0F4C81] font-bold text-2xl hover:bg-slate-200 active:bg-[#0F4C81] active:text-white transition-all shadow-sm`.
  - Tombol Scan QR Kartu: Tombol lebar dengan icon QR dan instruksi arah kamera.
- **Tombol Aksi Utama Presensi:**
  - **PRESENSI MASUK:**
    `h-28 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white p-6 flex items-center justify-between shadow-lg transition-all`
    (Memuat judul "PRESENSI MASUK", jam mulai shift, dan icon checkmark).
  - **PRESENSI PULANG:**
    `h-28 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white p-6 flex items-center justify-between shadow-lg transition-all`
    (Memuat judul "PRESENSI PULANG", akhir shift, dan icon task complete).
- **Konfirmasi Hasil Presensi:** Modal / card instan muncul menampilkan foto/nama operator, departemen, chip shift (misal: `S3 Malam`), waktu server presisi, dan status badge `Tepat Waktu` (hijau) atau `Terlambat X Menit` (kuning).

---

### 5.6 Kalender Roster & Grid Shift (Desktop)
- **Filter Bar:** Terletak di bagian atas berisi dropdown Departemen, Grup A-D, Filter Shift, Bulan, dan tombol aksi `Buat Jadwal dari Pola` dan `Publish`.
- **Tabel Kalender:** Baris adalah operator, kolom adalah tanggal 1-31.
- **Cell Shift Chip:**
  `py-1 px-1.5 rounded text-xs font-bold text-center cursor-pointer hover:scale-105 transition-transform`
  - Cell S1: `bg-amber-100 text-amber-900 border border-amber-300`
  - Cell S2: `bg-orange-100 text-orange-950 border border-orange-300`
  - Cell S3: `bg-indigo-100 text-indigo-950 border border-indigo-300`
  - Cell GS: `bg-blue-100 text-blue-900 border border-blue-300`
  - Cell OFF: `bg-slate-200 text-slate-800 border border-slate-300`

---

### 5.7 Navigasi Mobile Operator (Layar 6 - 390x844)
- **Header:** Salam operator, foto profil, dan status shift hari ini.
- **Card Shift Berikutnya:** Kartu bergradien lembut memuat tanggal, jam shift berikutnya, dan hitung mundur.
- **Aksi Cepat (Quick Actions Grid):** 4 tombol pintas: *Ajukan Izin*, *Tukar Shift*, *Ajukan Lembur*, *Riwayat Absen*.
- **Bottom Navigation Bar:**
  `fixed bottom-0 left-0 w-full z-50 bg-white/90 backdrop-blur-md border-t border-slate-200 shadow-[0_-2px_12px_rgba(0,0,0,0.05)]`
  - 4 Menu: **Beranda** (`home`), **Jadwal** (`calendar_month`), **Pengajuan** (`assignment`), **Profil** (`person`).
  - Menu aktif diberi warna `text-[#0F4C81] font-bold`.

---

## 6. Konfigurasi Tailwind CSS (`tailwind.config.js`)

Tambahkan token warna industri ini ke dalam `tailwind.config.js` Laravel:

```javascript
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./app/Livewire/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          primary: '#0F4C81',
          container: '#00355F',
          secondary: '#2563EB',
          tertiary: '#0284C7',
          dark: '#0B1C30',
        },
        shift: {
          gs: '#2563EB',
          'gs-bg': '#DBEAFE',
          'gs-text': '#1E40AF',
          s1: '#F59E0B',
          's1-bg': '#FEF3C7',
          's1-text': '#92400E',
          s2: '#EA580C',
          's2-bg': '#FFEDD5',
          's2-text': '#9A3412',
          s3: '#4338CA',
          's3-bg': '#EEF2FF',
          's3-text': '#3730A3',
          off: '#94A3B8',
          'off-bg': '#F1F5F9',
          'off-text': '#475569',
        },
        attendance: {
          hadir: '#16A34A',
          'hadir-bg': '#F0FDF4',
          terlambat: '#CA8A04',
          'terlambat-bg': '#FEF3C7',
          alpa: '#DC2626',
          'alpa-bg': '#FEF2F2',
          izin: '#0284C7',
          'izin-bg': '#F0F9FF',
        }
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
      borderRadius: {
        'xl': '12px',
        '2xl': '16px',
      }
    },
  },
  plugins: [],
}
```
