<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\GrupKaryawan;
use App\Models\Shift;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\PolaRotasi;
use App\Models\PolaRotasiDetail;
use App\Models\Pengaturan;
use App\Models\HariLibur;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Departemen
        $deptData = [
            ['nama_departemen' => 'Personalia', 'kode_departemen' => 'PRS', 'deskripsi' => 'Human Resources & General Affairs'],
            ['nama_departemen' => 'Logistik', 'kode_departemen' => 'LGS', 'deskripsi' => 'Warehouse, Inventory & Supply Chain'],
            ['nama_departemen' => 'Produksi', 'kode_departemen' => 'PRD', 'deskripsi' => 'Plant Manufacturing & Processing'],
            ['nama_departemen' => 'Quality Control Total', 'kode_departemen' => 'QCT', 'deskripsi' => 'Quality Assurance & Quality Control'],
            ['nama_departemen' => 'Packing', 'kode_departemen' => 'PCKG', 'deskripsi' => 'Packaging & Finished Goods Outbound'],
        ];

        $deptMap = [];
        foreach ($deptData as $d) {
            $dept = Departemen::create($d);
            $deptMap[$d['kode_departemen']] = $dept->id_departemen;
        }

        // 2. Jabatan
        $jabatanList = [
            // Personalia
            ['kode' => 'JAB-DIR-01', 'nama' => 'Direktur Operasional', 'dept' => 'PRS', 'level' => 'DIREKTUR'],
            ['kode' => 'JAB-HOD-PRS', 'nama' => 'Head of HR & GA', 'dept' => 'PRS', 'level' => 'HOD'],
            ['kode' => 'JAB-SPV-PRS', 'nama' => 'Supervisor Personalia', 'dept' => 'PRS', 'level' => 'SUPERVISOR'],
            ['kode' => 'JAB-OPR-PRS', 'nama' => 'Staf Administrasi HR', 'dept' => 'PRS', 'level' => 'OPERATOR'],

            // Produksi
            ['kode' => 'JAB-HOD-PRD', 'nama' => 'Head of Production', 'dept' => 'PRD', 'level' => 'HOD'],
            ['kode' => 'JAB-SPV-PRD', 'nama' => 'Supervisor Produksi', 'dept' => 'PRD', 'level' => 'SUPERVISOR'],
            ['kode' => 'JAB-OPR-PRD', 'nama' => 'Operator Mesin Pabrik', 'dept' => 'PRD', 'level' => 'OPERATOR'],

            // Logistik
            ['kode' => 'JAB-HOD-LGS', 'nama' => 'Head of Logistics', 'dept' => 'LGS', 'level' => 'HOD'],
            ['kode' => 'JAB-SPV-LGS', 'nama' => 'Supervisor Logistik', 'dept' => 'LGS', 'level' => 'SUPERVISOR'],
            ['kode' => 'JAB-OPR-LGS', 'nama' => 'Operator Forklift & Gudang', 'dept' => 'LGS', 'level' => 'OPERATOR'],

            // Quality Control
            ['kode' => 'JAB-HOD-QCT', 'nama' => 'Head of Quality Control', 'dept' => 'QCT', 'level' => 'HOD'],
            ['kode' => 'JAB-SPV-QCT', 'nama' => 'Supervisor QC', 'dept' => 'QCT', 'level' => 'SUPERVISOR'],
            ['kode' => 'JAB-OPR-QCT', 'nama' => 'Operator Inspector QC', 'dept' => 'QCT', 'level' => 'OPERATOR'],

            // Packing
            ['kode' => 'JAB-HOD-PCKG', 'nama' => 'Head of Packing', 'dept' => 'PCKG', 'level' => 'HOD'],
            ['kode' => 'JAB-SPV-PCKG', 'nama' => 'Supervisor Packing', 'dept' => 'PCKG', 'level' => 'SUPERVISOR'],
            ['kode' => 'JAB-OPR-PCKG', 'nama' => 'Operator Packing & Sealing', 'dept' => 'PCKG', 'level' => 'OPERATOR'],
        ];

        $jabatanMap = [];
        foreach ($jabatanList as $j) {
            $jab = Jabatan::create([
                'kode_jabatan' => $j['kode'],
                'nama_jabatan' => $j['nama'],
                'id_departemen' => $deptMap[$j['dept']],
                'level_jabatan' => $j['level'],
            ]);
            $jabatanMap[$j['kode']] = $jab->id_jabatan;
        }

        // 3. Shift
        $shifts = [
            [
                'kode_shift' => 'GS',
                'nama_shift' => 'General Shift',
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '17:00:00',
                'lintas_hari' => false,
                'toleransi_menit' => 10,
                'warna' => '#2563EB',
                'is_libur' => false,
                'is_aktif' => true,
            ],
            [
                'kode_shift' => 'S1',
                'nama_shift' => 'Shift 1 (Pagi)',
                'jam_masuk' => '07:00:00',
                'jam_pulang' => '15:00:00',
                'lintas_hari' => false,
                'toleransi_menit' => 10,
                'warna' => '#F59E0B',
                'is_libur' => false,
                'is_aktif' => true,
            ],
            [
                'kode_shift' => 'S2',
                'nama_shift' => 'Shift 2 (Siang)',
                'jam_masuk' => '15:00:00',
                'jam_pulang' => '23:00:00',
                'lintas_hari' => false,
                'toleransi_menit' => 10,
                'warna' => '#EA580C',
                'is_libur' => false,
                'is_aktif' => true,
            ],
            [
                'kode_shift' => 'S3',
                'nama_shift' => 'Shift 3 (Malam)',
                'jam_masuk' => '23:00:00',
                'jam_pulang' => '07:00:00',
                'lintas_hari' => true,
                'toleransi_menit' => 10,
                'warna' => '#4338CA',
                'is_libur' => false,
                'is_aktif' => true,
            ],
            [
                'kode_shift' => 'OFF',
                'nama_shift' => 'Libur Rutin',
                'jam_masuk' => null,
                'jam_pulang' => null,
                'lintas_hari' => false,
                'toleransi_menit' => 0,
                'warna' => '#94A3B8',
                'is_libur' => true,
                'is_aktif' => true,
            ],
        ];

        foreach ($shifts as $s) {
            Shift::create($s);
        }

        // 4. Grup Karyawan (A, B, C, D)
        $grupMap = [];
        foreach (['Grup A', 'Grup B', 'Grup C', 'Grup D'] as $gName) {
            $g = GrupKaryawan::create([
                'nama_grup' => $gName,
                'keterangan' => 'Grup regu kerja ' . $gName,
            ]);
            $grupMap[$gName] = $g->id_grup;
        }

        // 5. Pola Rotasi 8 Hari
        $pola = PolaRotasi::create([
            'nama_pola' => 'Pola 4 Grup 3 Shift (8 Hari)',
            'panjang_siklus_hari' => 8,
            'keterangan' => 'Siklus 8 hari: 2 hari S1, 2 hari S2, 2 hari S3, 2 hari OFF per grup bergantian.',
            'is_aktif' => true,
        ]);

        $matrix = [
            'Grup A' => ['S1', 'S1', 'S2', 'S2', 'S3', 'S3', 'OFF', 'OFF'],
            'Grup B' => ['OFF', 'OFF', 'S1', 'S1', 'S2', 'S2', 'S3', 'S3'],
            'Grup C' => ['S3', 'S3', 'OFF', 'OFF', 'S1', 'S1', 'S2', 'S2'],
            'Grup D' => ['S2', 'S2', 'S3', 'S3', 'OFF', 'OFF', 'S1', 'S1'],
        ];

        foreach ($matrix as $grupName => $shiftsPerDay) {
            $idGrup = $grupMap[$grupName];
            foreach ($shiftsPerDay as $dayIndex => $kodeShift) {
                PolaRotasiDetail::create([
                    'id_pola' => $pola->id_pola,
                    'id_grup' => $idGrup,
                    'hari_ke' => $dayIndex + 1,
                    'kode_shift' => $kodeShift,
                ]);
            }
        }

        // 6. Pengaturan Awal
        $settings = [
            ['kunci' => 'jendela_absen_masuk_menit', 'nilai' => '60', 'keterangan' => 'Absen masuk dibuka N menit sebelum jam masuk'],
            ['kunci' => 'toleransi_terlambat_menit', 'nilai' => '10', 'keterangan' => 'Toleransi keterlambatan dalam menit'],
            ['kunci' => 'batas_alpa_menit_setelah_pulang', 'nilai' => '30', 'keterangan' => 'Batas penandaan ALPA otomatis setelah jam pulang'],
            ['kunci' => 'min_istirahat_jam', 'nilai' => '8', 'keterangan' => 'Minimal jam istirahat antar dua shift kerja'],
            ['kunci' => 'cuti_perlu_hod_hari', 'nilai' => '3', 'keterangan' => 'Jumlah hari cuti yang membutuhkan persetujuan HOD'],
        ];

        foreach ($settings as $setting) {
            Pengaturan::create($setting);
        }

        // 7. Hari Libur Awal
        HariLibur::create([
            'tanggal' => '2026-08-17',
            'keterangan' => 'Hari Kemerdekaan Republik Indonesia',
            'jenis' => 'NASIONAL',
        ]);
        HariLibur::create([
            'tanggal' => '2026-12-25',
            'keterangan' => 'Hari Raya Natal',
            'jenis' => 'NASIONAL',
        ]);

        // 8. Karyawan & Akun Pengguna (Minimal 30 Karyawan + Akun)
        $passwordHash = Hash::make('password');
        $pinHash = Hash::make('123456');

        $karyawanData = [
            // Admin HR (Personalia)
            [
                'id' => 'EMP-0001', 'nama' => 'Budi Santoso', 'gender' => 'L', 'tgl' => '1988-04-12',
                'dept' => 'PRS', 'jabatan' => 'JAB-OPR-PRS', 'grup' => null, 'role' => 'ADMIN_HR'
            ],
            // Direktur Operasional
            [
                'id' => 'EMP-0002', 'nama' => 'Ir. Hendra Gunawan', 'gender' => 'L', 'tgl' => '1975-02-20',
                'dept' => 'PRS', 'jabatan' => 'JAB-DIR-01', 'grup' => null, 'role' => 'DIREKTUR'
            ],
            // HOD (5 Departemen)
            [
                'id' => 'EMP-0003', 'nama' => 'Dewi Lestari', 'gender' => 'P', 'tgl' => '1982-08-15',
                'dept' => 'PRS', 'jabatan' => 'JAB-HOD-PRS', 'grup' => null, 'role' => 'HOD'
            ],
            [
                'id' => 'EMP-0004', 'nama' => 'Agus Setiawan', 'gender' => 'L', 'tgl' => '1980-11-05',
                'dept' => 'PRD', 'jabatan' => 'JAB-HOD-PRD', 'grup' => null, 'role' => 'HOD'
            ],
            [
                'id' => 'EMP-0005', 'nama' => 'Bambang Wijaya', 'gender' => 'L', 'tgl' => '1983-03-22',
                'dept' => 'LGS', 'jabatan' => 'JAB-HOD-LGS', 'grup' => null, 'role' => 'HOD'
            ],
            [
                'id' => 'EMP-0006', 'nama' => 'Ratna Sari', 'gender' => 'P', 'tgl' => '1984-07-10',
                'dept' => 'QCT', 'jabatan' => 'JAB-HOD-QCT', 'grup' => null, 'role' => 'HOD'
            ],
            [
                'id' => 'EMP-0007', 'nama' => 'Joko Prasetyo', 'gender' => 'L', 'tgl' => '1981-09-30',
                'dept' => 'PCKG', 'jabatan' => 'JAB-HOD-PCKG', 'grup' => null, 'role' => 'HOD'
            ],
            // Supervisor (5 Departemen)
            [
                'id' => 'EMP-0008', 'nama' => 'Siti Aminah', 'gender' => 'P', 'tgl' => '1990-05-18',
                'dept' => 'PRS', 'jabatan' => 'JAB-SPV-PRS', 'grup' => null, 'role' => 'SUPERVISOR'
            ],
            [
                'id' => 'EMP-0009', 'nama' => 'Ahmad Fauzi', 'gender' => 'L', 'tgl' => '1989-01-25',
                'dept' => 'PRD', 'jabatan' => 'JAB-SPV-PRD', 'grup' => null, 'role' => 'SUPERVISOR'
            ],
            [
                'id' => 'EMP-0010', 'nama' => 'Rudi Hartono', 'gender' => 'L', 'tgl' => '1987-12-14',
                'dept' => 'LGS', 'jabatan' => 'JAB-SPV-LGS', 'grup' => null, 'role' => 'SUPERVISOR'
            ],
            [
                'id' => 'EMP-0011', 'nama' => 'Eko Wahyudi', 'gender' => 'L', 'tgl' => '1991-06-08',
                'dept' => 'QCT', 'jabatan' => 'JAB-SPV-QCT', 'grup' => null, 'role' => 'SUPERVISOR'
            ],
            [
                'id' => 'EMP-0012', 'nama' => 'Dedi Kurniawan', 'gender' => 'L', 'tgl' => '1992-04-03',
                'dept' => 'PCKG', 'jabatan' => 'JAB-SPV-PCKG', 'grup' => null, 'role' => 'SUPERVISOR'
            ],
            // Operator Produksi - Grup A
            [
                'id' => 'EMP-0013', 'nama' => 'Fajar Pratama', 'gender' => 'L', 'tgl' => '1996-03-11',
                'dept' => 'PRD', 'jabatan' => 'JAB-OPR-PRD', 'grup' => 'Grup A', 'role' => 'OPERATOR'
            ],
            [
                'id' => 'EMP-0014', 'nama' => 'Galih Ramadhan', 'gender' => 'L', 'tgl' => '1997-07-29',
                'dept' => 'PRD', 'jabatan' => 'JAB-OPR-PRD', 'grup' => 'Grup A', 'role' => 'OPERATOR'
            ],
            [
                'id' => 'EMP-0015', 'nama' => 'Hendra Saputra', 'gender' => 'L', 'tgl' => '1998-10-14',
                'dept' => 'PRD', 'jabatan' => 'JAB-OPR-PRD', 'grup' => 'Grup A', 'role' => 'OPERATOR'
            ],
            // Operator Produksi - Grup B
            [
                'id' => 'EMP-0016', 'nama' => 'Indra Lesmana', 'gender' => 'L', 'tgl' => '1995-09-02',
                'dept' => 'PRD', 'jabatan' => 'JAB-OPR-PRD', 'grup' => 'Grup B', 'role' => 'OPERATOR'
            ],
            [
                'id' => 'EMP-0017', 'nama' => 'Joni Iskandar', 'gender' => 'L', 'tgl' => '1996-12-19',
                'dept' => 'PRD', 'jabatan' => 'JAB-OPR-PRD', 'grup' => 'Grup B', 'role' => 'OPERATOR'
            ],
            [
                'id' => 'EMP-0018', 'nama' => 'Kiki Firmansyah', 'gender' => 'L', 'tgl' => '1999-04-21',
                'dept' => 'PRD', 'jabatan' => 'JAB-OPR-PRD', 'grup' => 'Grup B', 'role' => 'OPERATOR'
            ],
            // Operator Produksi - Grup C
            [
                'id' => 'EMP-0019', 'nama' => 'Lukman Hakim', 'gender' => 'L', 'tgl' => '1994-08-07',
                'dept' => 'PRD', 'jabatan' => 'JAB-OPR-PRD', 'grup' => 'Grup C', 'role' => 'OPERATOR'
            ],
            [
                'id' => 'EMP-0020', 'nama' => 'Miftah Hidayat', 'gender' => 'L', 'tgl' => '1997-02-14',
                'dept' => 'PRD', 'jabatan' => 'JAB-OPR-PRD', 'grup' => 'Grup C', 'role' => 'OPERATOR'
            ],
            [
                'id' => 'EMP-0021', 'nama' => 'Nurul Huda', 'gender' => 'P', 'tgl' => '1998-05-30',
                'dept' => 'PRD', 'jabatan' => 'JAB-OPR-PRD', 'grup' => 'Grup C', 'role' => 'OPERATOR'
            ],
            // Operator Produksi - Grup D
            [
                'id' => 'EMP-0022', 'nama' => 'Oki Setiawan', 'gender' => 'L', 'tgl' => '1995-11-17',
                'dept' => 'PRD', 'jabatan' => 'JAB-OPR-PRD', 'grup' => 'Grup D', 'role' => 'OPERATOR'
            ],
            [
                'id' => 'EMP-0023', 'nama' => 'Panji Gumilang', 'gender' => 'L', 'tgl' => '1996-01-22',
                'dept' => 'PRD', 'jabatan' => 'JAB-OPR-PRD', 'grup' => 'Grup D', 'role' => 'OPERATOR'
            ],
            [
                'id' => 'EMP-0024', 'nama' => 'Rian Hidayatullah', 'gender' => 'L', 'tgl' => '1997-06-18',
                'dept' => 'PRD', 'jabatan' => 'JAB-OPR-PRD', 'grup' => 'Grup D', 'role' => 'OPERATOR'
            ],
            // Operator Logistik (Grup A & B)
            [
                'id' => 'EMP-0025', 'nama' => 'Surya Kencana', 'gender' => 'L', 'tgl' => '1993-05-12',
                'dept' => 'LGS', 'jabatan' => 'JAB-OPR-LGS', 'grup' => 'Grup A', 'role' => 'OPERATOR'
            ],
            [
                'id' => 'EMP-0026', 'nama' => 'Taufik Rahman', 'gender' => 'L', 'tgl' => '1994-09-28',
                'dept' => 'LGS', 'jabatan' => 'JAB-OPR-LGS', 'grup' => 'Grup B', 'role' => 'OPERATOR'
            ],
            // Operator QCT (Grup C & D)
            [
                'id' => 'EMP-0027', 'nama' => 'Utami Pujiastuti', 'gender' => 'P', 'tgl' => '1996-10-15',
                'dept' => 'QCT', 'jabatan' => 'JAB-OPR-QCT', 'grup' => 'Grup C', 'role' => 'OPERATOR'
            ],
            [
                'id' => 'EMP-0028', 'nama' => 'Vina Panduwinata', 'gender' => 'P', 'tgl' => '1998-03-08',
                'dept' => 'QCT', 'jabatan' => 'JAB-OPR-QCT', 'grup' => 'Grup D', 'role' => 'OPERATOR'
            ],
            // Operator Packing (Grup A & B)
            [
                'id' => 'EMP-0029', 'nama' => 'Wahyu Santoso', 'gender' => 'L', 'tgl' => '1995-07-20',
                'dept' => 'PCKG', 'jabatan' => 'JAB-OPR-PCKG', 'grup' => 'Grup A', 'role' => 'OPERATOR'
            ],
            [
                'id' => 'EMP-0030', 'nama' => 'Yayan Ruhian', 'gender' => 'L', 'tgl' => '1994-12-04',
                'dept' => 'PCKG', 'jabatan' => 'JAB-OPR-PCKG', 'grup' => 'Grup B', 'role' => 'OPERATOR'
            ],
            // Operator Non-Rotasi (GS Tetap, id_grup = null)
            [
                'id' => 'EMP-0031', 'nama' => 'Zulfikar Ali', 'gender' => 'L', 'tgl' => '1992-02-17',
                'dept' => 'PRS', 'jabatan' => 'JAB-OPR-PRS', 'grup' => null, 'role' => 'OPERATOR'
            ],
            [
                'id' => 'EMP-0032', 'nama' => 'Anisa Rahmawati', 'gender' => 'P', 'tgl' => '1995-08-23',
                'dept' => 'LGS', 'jabatan' => 'JAB-OPR-LGS', 'grup' => null, 'role' => 'OPERATOR'
            ],
        ];

        foreach ($karyawanData as $k) {
            Karyawan::create([
                'id_karyawan' => $k['id'],
                'nama_karyawan' => $k['nama'],
                'jns_kelamin' => $k['gender'],
                'tgl_lahir' => $k['tgl'],
                'id_departemen' => $deptMap[$k['dept']],
                'id_jabatan' => $jabatanMap[$k['jabatan']],
                'id_grup' => $k['grup'] ? $grupMap[$k['grup']] : null,
                'status_karyawan' => 'AKTIF',
                'no_hp' => '0812' . rand(10000000, 99999999),
            ]);

            User::create([
                'id_karyawan' => $k['id'],
                'password' => $passwordHash, // 'password'
                'pin_absen' => $pinHash,      // '123456'
                'role' => $k['role'],
                'is_active' => true,
            ]);
        }
    }
}
