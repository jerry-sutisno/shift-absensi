/*
 Navicat Premium Dump SQL

 Source Server         : DatabasesJS
 Source Server Type    : MySQL
 Source Server Version : 80045 (8.0.45)
 Source Host           : localhost:3306
 Source Schema         : shift_absensi

 Target Server Type    : MySQL
 Target Server Version : 80045 (8.0.45)
 File Encoding         : 65001

 Date: 05/11/2026 14:30:19
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for absensi
-- ----------------------------
DROP TABLE IF EXISTS `absensi`;
CREATE TABLE `absensi`  (
  `id_absensi` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_jadwal` bigint UNSIGNED NOT NULL,
  `id_karyawan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_shift` date NOT NULL,
  `waktu_masuk` datetime NULL DEFAULT NULL,
  `waktu_pulang` datetime NULL DEFAULT NULL,
  `metode_masuk` enum('KIOSK','MOBILE','MANUAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `metode_pulang` enum('KIOSK','MOBILE','MANUAL') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` enum('HADIR','TERLAMBAT','ALPA','IZIN','SAKIT','CUTI','LIBUR') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `menit_terlambat` smallint NOT NULL DEFAULT 0,
  `pulang_cepat` tinyint(1) NOT NULL DEFAULT 0,
  `menit_kerja` smallint NULL DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `dikoreksi_oleh` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_absensi`) USING BTREE,
  UNIQUE INDEX `absensi_id_karyawan_tanggal_shift_unique`(`id_karyawan` ASC, `tanggal_shift` ASC) USING BTREE,
  UNIQUE INDEX `absensi_id_jadwal_unique`(`id_jadwal` ASC) USING BTREE,
  INDEX `absensi_dikoreksi_oleh_foreign`(`dikoreksi_oleh` ASC) USING BTREE,
  CONSTRAINT `absensi_dikoreksi_oleh_foreign` FOREIGN KEY (`dikoreksi_oleh`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `absensi_id_jadwal_foreign` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_shift` (`id_jadwal`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `absensi_id_karyawan_foreign` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of absensi
-- ----------------------------
INSERT INTO `absensi` VALUES (1, 1, 'EMP-0013', '2026-11-02', '2026-11-02 13:41:32', NULL, 'MANUAL', NULL, 'TERLAMBAT', 341, 0, NULL, NULL, NULL, '2026-11-02 06:41:32', '2026-11-02 06:41:32');
INSERT INTO `absensi` VALUES (2, 855, 'EMP-0001', '2026-11-02', '2026-11-02 14:46:01', NULL, 'MANUAL', NULL, 'TERLAMBAT', 406, 0, NULL, NULL, NULL, '2026-11-02 07:46:01', '2026-11-02 07:46:01');

-- ----------------------------
-- Table structure for audit_log
-- ----------------------------
DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE `audit_log`  (
  `id_log` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` bigint UNSIGNED NULL DEFAULT NULL,
  `aksi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabel` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_record` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_lama` json NULL,
  `data_baru` json NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`) USING BTREE,
  INDEX `audit_log_id_user_foreign`(`id_user` ASC) USING BTREE,
  CONSTRAINT `audit_log_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of audit_log
-- ----------------------------
INSERT INTO `audit_log` VALUES (1, 13, 'LOGIN', 'users', '13', NULL, '{\"role\": \"OPERATOR\", \"id_karyawan\": \"EMP-0013\"}', '127.0.0.1', '2026-11-02 13:41:00');
INSERT INTO `audit_log` VALUES (2, 13, 'LOGOUT', 'users', '13', NULL, '{\"id_karyawan\": \"EMP-0013\"}', '127.0.0.1', '2026-11-02 13:44:38');
INSERT INTO `audit_log` VALUES (3, 1, 'LOGIN', 'users', '1', NULL, '{\"role\": \"ADMIN_HR\", \"id_karyawan\": \"EMP-0001\"}', '127.0.0.1', '2026-11-02 13:46:30');
INSERT INTO `audit_log` VALUES (4, 1, 'CREATE_HARI_LIBUR', 'hari_libur', '2026-11-06', NULL, '{\"jenis\": \"PERUSAHAAN\", \"tanggal\": \"2026-11-06T00:00:00.000000Z\", \"created_at\": \"2026-11-02T06:50:07.000000Z\", \"keterangan\": \"Dies Natalis\", \"updated_at\": \"2026-11-02T06:50:07.000000Z\"}', '127.0.0.1', '2026-11-02 13:50:07');
INSERT INTO `audit_log` VALUES (5, 1, 'GENERATE_JADWAL', 'jadwal_shift', 'Dept:3-2026-11', NULL, '{\"bulan\": 11, \"tahun\": 2026, \"id_pola\": 1, \"departemen_id\": 3, \"total_karyawan\": 14, \"generated_count\": 419}', '127.0.0.1', '2026-11-02 13:53:11');
INSERT INTO `audit_log` VALUES (6, 1, 'GENERATE_JADWAL', 'jadwal_shift', 'Dept:3-2026-12', NULL, '{\"bulan\": 12, \"tahun\": 2026, \"id_pola\": 1, \"departemen_id\": 3, \"total_karyawan\": 14, \"generated_count\": 434}', '127.0.0.1', '2026-11-02 13:53:44');
INSERT INTO `audit_log` VALUES (7, 1, 'GENERATE_JADWAL', 'jadwal_shift', 'Dept:3-2026-12', NULL, '{\"bulan\": 12, \"tahun\": 2026, \"id_pola\": 1, \"departemen_id\": 3, \"total_karyawan\": 14, \"generated_count\": 434}', '127.0.0.1', '2026-11-02 13:53:49');
INSERT INTO `audit_log` VALUES (8, 1, 'GENERATE_JADWAL', 'jadwal_shift', 'Dept:3-2026-12', NULL, '{\"bulan\": 12, \"tahun\": 2026, \"id_pola\": 1, \"departemen_id\": 3, \"total_karyawan\": 14, \"generated_count\": 434}', '127.0.0.1', '2026-11-02 13:53:51');
INSERT INTO `audit_log` VALUES (9, 1, 'PUBLISH_JADWAL', 'jadwal_shift', '2026-12', '{\"status\": \"DRAFT\"}', '{\"count\": 434, \"status\": \"PUBLISHED\"}', '127.0.0.1', '2026-11-02 13:54:02');
INSERT INTO `audit_log` VALUES (10, 1, 'LOGOUT', 'users', '1', NULL, '{\"id_karyawan\": \"EMP-0001\"}', '127.0.0.1', '2026-11-02 13:54:18');
INSERT INTO `audit_log` VALUES (11, 13, 'LOGIN', 'users', '13', NULL, '{\"role\": \"OPERATOR\", \"id_karyawan\": \"EMP-0013\"}', '127.0.0.1', '2026-11-02 13:54:24');
INSERT INTO `audit_log` VALUES (12, 13, 'LOGOUT', 'users', '13', NULL, '{\"id_karyawan\": \"EMP-0013\"}', '127.0.0.1', '2026-11-02 13:54:45');
INSERT INTO `audit_log` VALUES (13, 9, 'LOGIN', 'users', '9', NULL, '{\"role\": \"SUPERVISOR\", \"id_karyawan\": \"EMP-0009\"}', '127.0.0.1', '2026-11-02 13:54:53');
INSERT INTO `audit_log` VALUES (14, 9, 'PUBLISH_JADWAL', 'jadwal_shift', '2026-11', '{\"status\": \"DRAFT\"}', '{\"count\": 419, \"status\": \"PUBLISHED\"}', '127.0.0.1', '2026-11-02 13:55:37');
INSERT INTO `audit_log` VALUES (15, 9, 'LOGOUT', 'users', '9', NULL, '{\"id_karyawan\": \"EMP-0009\"}', '127.0.0.1', '2026-11-02 13:56:32');
INSERT INTO `audit_log` VALUES (16, 13, 'LOGIN', 'users', '13', NULL, '{\"role\": \"OPERATOR\", \"id_karyawan\": \"EMP-0013\"}', '127.0.0.1', '2026-11-02 13:56:38');
INSERT INTO `audit_log` VALUES (17, 13, 'LOGOUT', 'users', '13', NULL, '{\"id_karyawan\": \"EMP-0013\"}', '127.0.0.1', '2026-11-02 13:57:49');
INSERT INTO `audit_log` VALUES (18, 9, 'LOGIN', 'users', '9', NULL, '{\"role\": \"SUPERVISOR\", \"id_karyawan\": \"EMP-0009\"}', '127.0.0.1', '2026-11-02 13:57:54');
INSERT INTO `audit_log` VALUES (19, 9, 'LOGOUT', 'users', '9', NULL, '{\"id_karyawan\": \"EMP-0009\"}', '127.0.0.1', '2026-11-02 13:59:41');
INSERT INTO `audit_log` VALUES (20, 9, 'LOGIN', 'users', '9', NULL, '{\"role\": \"SUPERVISOR\", \"id_karyawan\": \"EMP-0009\"}', '127.0.0.1', '2026-11-02 14:01:02');
INSERT INTO `audit_log` VALUES (21, 9, 'LOGOUT', 'users', '9', NULL, '{\"id_karyawan\": \"EMP-0009\"}', '127.0.0.1', '2026-11-02 14:43:47');
INSERT INTO `audit_log` VALUES (22, 1, 'LOGIN', 'users', '1', NULL, '{\"role\": \"ADMIN_HR\", \"id_karyawan\": \"EMP-0001\"}', '127.0.0.1', '2026-11-02 14:44:52');
INSERT INTO `audit_log` VALUES (23, 1, 'LOGOUT', 'users', '1', NULL, '{\"id_karyawan\": \"EMP-0001\"}', '127.0.0.1', '2026-11-02 14:48:28');

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache
-- ----------------------------

-- ----------------------------
-- Table structure for cache_locks
-- ----------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks`  (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`) USING BTREE,
  INDEX `cache_locks_expiration_index`(`expiration` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cache_locks
-- ----------------------------

-- ----------------------------
-- Table structure for departemen
-- ----------------------------
DROP TABLE IF EXISTS `departemen`;
CREATE TABLE `departemen`  (
  `id_departemen` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_departemen` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode_departemen` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_departemen`) USING BTREE,
  UNIQUE INDEX `departemen_kode_departemen_unique`(`kode_departemen` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of departemen
-- ----------------------------
INSERT INTO `departemen` VALUES (1, 'Personalia', 'PRS', 'Human Resources & General Affairs', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `departemen` VALUES (2, 'Logistik', 'LGS', 'Warehouse, Inventory & Supply Chain', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `departemen` VALUES (3, 'Produksi', 'PRD', 'Plant Manufacturing & Processing', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `departemen` VALUES (4, 'Quality Control Total', 'QCT', 'Quality Assurance & Quality Control', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `departemen` VALUES (5, 'Packing', 'PCKG', 'Packaging & Finished Goods Outbound', '2026-11-02 06:15:44', '2026-11-02 06:15:44');

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid` ASC) USING BTREE,
  INDEX `failed_jobs_connection_queue_failed_at_index`(`connection` ASC, `queue` ASC, `failed_at` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------

-- ----------------------------
-- Table structure for grup_karyawan
-- ----------------------------
DROP TABLE IF EXISTS `grup_karyawan`;
CREATE TABLE `grup_karyawan`  (
  `id_grup` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_grup` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_grup`) USING BTREE,
  UNIQUE INDEX `grup_karyawan_nama_grup_unique`(`nama_grup` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of grup_karyawan
-- ----------------------------
INSERT INTO `grup_karyawan` VALUES (1, 'Grup A', 'Grup regu kerja Grup A', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `grup_karyawan` VALUES (2, 'Grup B', 'Grup regu kerja Grup B', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `grup_karyawan` VALUES (3, 'Grup C', 'Grup regu kerja Grup C', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `grup_karyawan` VALUES (4, 'Grup D', 'Grup regu kerja Grup D', '2026-11-02 06:15:44', '2026-11-02 06:15:44');

-- ----------------------------
-- Table structure for hari_libur
-- ----------------------------
DROP TABLE IF EXISTS `hari_libur`;
CREATE TABLE `hari_libur`  (
  `tanggal` date NOT NULL,
  `keterangan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` enum('NASIONAL','PERUSAHAAN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NASIONAL',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`tanggal`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of hari_libur
-- ----------------------------
INSERT INTO `hari_libur` VALUES ('2026-08-17', 'Hari Kemerdekaan Republik Indonesia', 'NASIONAL', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `hari_libur` VALUES ('2026-11-06', 'Dies Natalis', 'PERUSAHAAN', '2026-11-02 06:50:07', '2026-11-02 06:50:07');
INSERT INTO `hari_libur` VALUES ('2026-12-25', 'Hari Raya Natal', 'NASIONAL', '2026-11-02 06:15:44', '2026-11-02 06:15:44');

-- ----------------------------
-- Table structure for jabatan
-- ----------------------------
DROP TABLE IF EXISTS `jabatan`;
CREATE TABLE `jabatan`  (
  `id_jabatan` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_jabatan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `nama_jabatan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_departemen` int UNSIGNED NOT NULL,
  `level_jabatan` enum('DIREKTUR','HOD','SUPERVISOR','OPERATOR') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jabatan`) USING BTREE,
  INDEX `jabatan_id_departemen_foreign`(`id_departemen` ASC) USING BTREE,
  CONSTRAINT `jabatan_id_departemen_foreign` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id_departemen`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jabatan
-- ----------------------------
INSERT INTO `jabatan` VALUES (1, 'JAB-DIR-01', 'Direktur Operasional', 1, 'DIREKTUR', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (2, 'JAB-HOD-PRS', 'Head of HR & GA', 1, 'HOD', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (3, 'JAB-SPV-PRS', 'Supervisor Personalia', 1, 'SUPERVISOR', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (4, 'JAB-OPR-PRS', 'Staf Administrasi HR', 1, 'OPERATOR', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (5, 'JAB-HOD-PRD', 'Head of Production', 3, 'HOD', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (6, 'JAB-SPV-PRD', 'Supervisor Produksi', 3, 'SUPERVISOR', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (7, 'JAB-OPR-PRD', 'Operator Mesin Pabrik', 3, 'OPERATOR', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (8, 'JAB-HOD-LGS', 'Head of Logistics', 2, 'HOD', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (9, 'JAB-SPV-LGS', 'Supervisor Logistik', 2, 'SUPERVISOR', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (10, 'JAB-OPR-LGS', 'Operator Forklift & Gudang', 2, 'OPERATOR', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (11, 'JAB-HOD-QCT', 'Head of Quality Control', 4, 'HOD', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (12, 'JAB-SPV-QCT', 'Supervisor QC', 4, 'SUPERVISOR', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (13, 'JAB-OPR-QCT', 'Operator Inspector QC', 4, 'OPERATOR', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (14, 'JAB-HOD-PCKG', 'Head of Packing', 5, 'HOD', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (15, 'JAB-SPV-PCKG', 'Supervisor Packing', 5, 'SUPERVISOR', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `jabatan` VALUES (16, 'JAB-OPR-PCKG', 'Operator Packing & Sealing', 5, 'OPERATOR', '2026-11-02 06:15:44', '2026-11-02 06:15:44');

-- ----------------------------
-- Table structure for jadwal_shift
-- ----------------------------
DROP TABLE IF EXISTS `jadwal_shift`;
CREATE TABLE `jadwal_shift`  (
  `id_jadwal` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_karyawan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `kode_shift` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sumber` enum('POLA','MANUAL','TUKAR_SHIFT') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'POLA',
  `status` enum('DRAFT','PUBLISHED') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DRAFT',
  `id_pola` int UNSIGNED NULL DEFAULT NULL,
  `dibuat_oleh` bigint UNSIGNED NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_jadwal`) USING BTREE,
  UNIQUE INDEX `jadwal_shift_id_karyawan_tanggal_unique`(`id_karyawan` ASC, `tanggal` ASC) USING BTREE,
  INDEX `jadwal_shift_kode_shift_foreign`(`kode_shift` ASC) USING BTREE,
  INDEX `jadwal_shift_id_pola_foreign`(`id_pola` ASC) USING BTREE,
  INDEX `jadwal_shift_dibuat_oleh_foreign`(`dibuat_oleh` ASC) USING BTREE,
  INDEX `jadwal_shift_tanggal_kode_shift_index`(`tanggal` ASC, `kode_shift` ASC) USING BTREE,
  CONSTRAINT `jadwal_shift_dibuat_oleh_foreign` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `jadwal_shift_id_karyawan_foreign` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `jadwal_shift_id_pola_foreign` FOREIGN KEY (`id_pola`) REFERENCES `pola_rotasi` (`id_pola`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `jadwal_shift_kode_shift_foreign` FOREIGN KEY (`kode_shift`) REFERENCES `shift` (`kode_shift`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 856 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jadwal_shift
-- ----------------------------
INSERT INTO `jadwal_shift` VALUES (1, 'EMP-0013', '2026-11-02', 'GS', 'MANUAL', 'PUBLISHED', NULL, NULL, '2026-11-02 06:41:32', '2026-11-02 06:41:32');
INSERT INTO `jadwal_shift` VALUES (2, 'EMP-0004', '2026-11-01', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (3, 'EMP-0009', '2026-11-01', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (4, 'EMP-0013', '2026-11-01', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (5, 'EMP-0014', '2026-11-01', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (6, 'EMP-0015', '2026-11-01', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (7, 'EMP-0016', '2026-11-01', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (8, 'EMP-0017', '2026-11-01', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (9, 'EMP-0018', '2026-11-01', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (10, 'EMP-0019', '2026-11-01', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (11, 'EMP-0020', '2026-11-01', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (12, 'EMP-0021', '2026-11-01', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (13, 'EMP-0022', '2026-11-01', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (14, 'EMP-0023', '2026-11-01', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (15, 'EMP-0024', '2026-11-01', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (16, 'EMP-0004', '2026-11-02', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (17, 'EMP-0009', '2026-11-02', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (18, 'EMP-0014', '2026-11-02', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (19, 'EMP-0015', '2026-11-02', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (20, 'EMP-0016', '2026-11-02', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (21, 'EMP-0017', '2026-11-02', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (22, 'EMP-0018', '2026-11-02', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (23, 'EMP-0019', '2026-11-02', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (24, 'EMP-0020', '2026-11-02', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (25, 'EMP-0021', '2026-11-02', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (26, 'EMP-0022', '2026-11-02', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (27, 'EMP-0023', '2026-11-02', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (28, 'EMP-0024', '2026-11-02', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (29, 'EMP-0004', '2026-11-03', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (30, 'EMP-0009', '2026-11-03', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (31, 'EMP-0013', '2026-11-03', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (32, 'EMP-0014', '2026-11-03', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (33, 'EMP-0015', '2026-11-03', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (34, 'EMP-0016', '2026-11-03', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (35, 'EMP-0017', '2026-11-03', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (36, 'EMP-0018', '2026-11-03', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (37, 'EMP-0019', '2026-11-03', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (38, 'EMP-0020', '2026-11-03', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (39, 'EMP-0021', '2026-11-03', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (40, 'EMP-0022', '2026-11-03', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (41, 'EMP-0023', '2026-11-03', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (42, 'EMP-0024', '2026-11-03', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (43, 'EMP-0004', '2026-11-04', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (44, 'EMP-0009', '2026-11-04', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (45, 'EMP-0013', '2026-11-04', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (46, 'EMP-0014', '2026-11-04', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (47, 'EMP-0015', '2026-11-04', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (48, 'EMP-0016', '2026-11-04', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (49, 'EMP-0017', '2026-11-04', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (50, 'EMP-0018', '2026-11-04', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (51, 'EMP-0019', '2026-11-04', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (52, 'EMP-0020', '2026-11-04', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (53, 'EMP-0021', '2026-11-04', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (54, 'EMP-0022', '2026-11-04', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (55, 'EMP-0023', '2026-11-04', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (56, 'EMP-0024', '2026-11-04', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (57, 'EMP-0004', '2026-11-05', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (58, 'EMP-0009', '2026-11-05', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (59, 'EMP-0013', '2026-11-05', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (60, 'EMP-0014', '2026-11-05', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (61, 'EMP-0015', '2026-11-05', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (62, 'EMP-0016', '2026-11-05', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (63, 'EMP-0017', '2026-11-05', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (64, 'EMP-0018', '2026-11-05', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (65, 'EMP-0019', '2026-11-05', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (66, 'EMP-0020', '2026-11-05', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (67, 'EMP-0021', '2026-11-05', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (68, 'EMP-0022', '2026-11-05', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (69, 'EMP-0023', '2026-11-05', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (70, 'EMP-0024', '2026-11-05', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (71, 'EMP-0004', '2026-11-06', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (72, 'EMP-0009', '2026-11-06', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (73, 'EMP-0013', '2026-11-06', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (74, 'EMP-0014', '2026-11-06', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (75, 'EMP-0015', '2026-11-06', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (76, 'EMP-0016', '2026-11-06', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (77, 'EMP-0017', '2026-11-06', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (78, 'EMP-0018', '2026-11-06', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (79, 'EMP-0019', '2026-11-06', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (80, 'EMP-0020', '2026-11-06', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (81, 'EMP-0021', '2026-11-06', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (82, 'EMP-0022', '2026-11-06', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (83, 'EMP-0023', '2026-11-06', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (84, 'EMP-0024', '2026-11-06', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (85, 'EMP-0004', '2026-11-07', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (86, 'EMP-0009', '2026-11-07', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (87, 'EMP-0013', '2026-11-07', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (88, 'EMP-0014', '2026-11-07', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (89, 'EMP-0015', '2026-11-07', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (90, 'EMP-0016', '2026-11-07', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (91, 'EMP-0017', '2026-11-07', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (92, 'EMP-0018', '2026-11-07', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (93, 'EMP-0019', '2026-11-07', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (94, 'EMP-0020', '2026-11-07', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (95, 'EMP-0021', '2026-11-07', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (96, 'EMP-0022', '2026-11-07', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (97, 'EMP-0023', '2026-11-07', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (98, 'EMP-0024', '2026-11-07', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (99, 'EMP-0004', '2026-11-08', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (100, 'EMP-0009', '2026-11-08', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (101, 'EMP-0013', '2026-11-08', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (102, 'EMP-0014', '2026-11-08', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (103, 'EMP-0015', '2026-11-08', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (104, 'EMP-0016', '2026-11-08', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (105, 'EMP-0017', '2026-11-08', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (106, 'EMP-0018', '2026-11-08', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (107, 'EMP-0019', '2026-11-08', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (108, 'EMP-0020', '2026-11-08', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (109, 'EMP-0021', '2026-11-08', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (110, 'EMP-0022', '2026-11-08', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (111, 'EMP-0023', '2026-11-08', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (112, 'EMP-0024', '2026-11-08', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (113, 'EMP-0004', '2026-11-09', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (114, 'EMP-0009', '2026-11-09', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (115, 'EMP-0013', '2026-11-09', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (116, 'EMP-0014', '2026-11-09', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (117, 'EMP-0015', '2026-11-09', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (118, 'EMP-0016', '2026-11-09', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (119, 'EMP-0017', '2026-11-09', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (120, 'EMP-0018', '2026-11-09', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (121, 'EMP-0019', '2026-11-09', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (122, 'EMP-0020', '2026-11-09', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (123, 'EMP-0021', '2026-11-09', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (124, 'EMP-0022', '2026-11-09', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (125, 'EMP-0023', '2026-11-09', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (126, 'EMP-0024', '2026-11-09', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (127, 'EMP-0004', '2026-11-10', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (128, 'EMP-0009', '2026-11-10', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (129, 'EMP-0013', '2026-11-10', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (130, 'EMP-0014', '2026-11-10', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (131, 'EMP-0015', '2026-11-10', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (132, 'EMP-0016', '2026-11-10', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (133, 'EMP-0017', '2026-11-10', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (134, 'EMP-0018', '2026-11-10', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (135, 'EMP-0019', '2026-11-10', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (136, 'EMP-0020', '2026-11-10', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (137, 'EMP-0021', '2026-11-10', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (138, 'EMP-0022', '2026-11-10', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (139, 'EMP-0023', '2026-11-10', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (140, 'EMP-0024', '2026-11-10', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (141, 'EMP-0004', '2026-11-11', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (142, 'EMP-0009', '2026-11-11', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (143, 'EMP-0013', '2026-11-11', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (144, 'EMP-0014', '2026-11-11', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (145, 'EMP-0015', '2026-11-11', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (146, 'EMP-0016', '2026-11-11', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (147, 'EMP-0017', '2026-11-11', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (148, 'EMP-0018', '2026-11-11', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (149, 'EMP-0019', '2026-11-11', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (150, 'EMP-0020', '2026-11-11', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (151, 'EMP-0021', '2026-11-11', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (152, 'EMP-0022', '2026-11-11', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (153, 'EMP-0023', '2026-11-11', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (154, 'EMP-0024', '2026-11-11', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (155, 'EMP-0004', '2026-11-12', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (156, 'EMP-0009', '2026-11-12', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (157, 'EMP-0013', '2026-11-12', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (158, 'EMP-0014', '2026-11-12', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (159, 'EMP-0015', '2026-11-12', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (160, 'EMP-0016', '2026-11-12', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (161, 'EMP-0017', '2026-11-12', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (162, 'EMP-0018', '2026-11-12', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (163, 'EMP-0019', '2026-11-12', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (164, 'EMP-0020', '2026-11-12', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (165, 'EMP-0021', '2026-11-12', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (166, 'EMP-0022', '2026-11-12', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (167, 'EMP-0023', '2026-11-12', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (168, 'EMP-0024', '2026-11-12', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (169, 'EMP-0004', '2026-11-13', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (170, 'EMP-0009', '2026-11-13', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (171, 'EMP-0013', '2026-11-13', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (172, 'EMP-0014', '2026-11-13', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (173, 'EMP-0015', '2026-11-13', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (174, 'EMP-0016', '2026-11-13', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (175, 'EMP-0017', '2026-11-13', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (176, 'EMP-0018', '2026-11-13', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (177, 'EMP-0019', '2026-11-13', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (178, 'EMP-0020', '2026-11-13', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (179, 'EMP-0021', '2026-11-13', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (180, 'EMP-0022', '2026-11-13', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (181, 'EMP-0023', '2026-11-13', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (182, 'EMP-0024', '2026-11-13', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (183, 'EMP-0004', '2026-11-14', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (184, 'EMP-0009', '2026-11-14', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (185, 'EMP-0013', '2026-11-14', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (186, 'EMP-0014', '2026-11-14', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (187, 'EMP-0015', '2026-11-14', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (188, 'EMP-0016', '2026-11-14', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (189, 'EMP-0017', '2026-11-14', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (190, 'EMP-0018', '2026-11-14', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (191, 'EMP-0019', '2026-11-14', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (192, 'EMP-0020', '2026-11-14', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (193, 'EMP-0021', '2026-11-14', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (194, 'EMP-0022', '2026-11-14', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (195, 'EMP-0023', '2026-11-14', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (196, 'EMP-0024', '2026-11-14', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (197, 'EMP-0004', '2026-11-15', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (198, 'EMP-0009', '2026-11-15', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (199, 'EMP-0013', '2026-11-15', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (200, 'EMP-0014', '2026-11-15', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (201, 'EMP-0015', '2026-11-15', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (202, 'EMP-0016', '2026-11-15', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (203, 'EMP-0017', '2026-11-15', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (204, 'EMP-0018', '2026-11-15', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (205, 'EMP-0019', '2026-11-15', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (206, 'EMP-0020', '2026-11-15', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (207, 'EMP-0021', '2026-11-15', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (208, 'EMP-0022', '2026-11-15', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (209, 'EMP-0023', '2026-11-15', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (210, 'EMP-0024', '2026-11-15', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (211, 'EMP-0004', '2026-11-16', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (212, 'EMP-0009', '2026-11-16', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (213, 'EMP-0013', '2026-11-16', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (214, 'EMP-0014', '2026-11-16', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (215, 'EMP-0015', '2026-11-16', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (216, 'EMP-0016', '2026-11-16', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (217, 'EMP-0017', '2026-11-16', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (218, 'EMP-0018', '2026-11-16', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (219, 'EMP-0019', '2026-11-16', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (220, 'EMP-0020', '2026-11-16', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (221, 'EMP-0021', '2026-11-16', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (222, 'EMP-0022', '2026-11-16', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (223, 'EMP-0023', '2026-11-16', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (224, 'EMP-0024', '2026-11-16', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (225, 'EMP-0004', '2026-11-17', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (226, 'EMP-0009', '2026-11-17', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (227, 'EMP-0013', '2026-11-17', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (228, 'EMP-0014', '2026-11-17', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (229, 'EMP-0015', '2026-11-17', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (230, 'EMP-0016', '2026-11-17', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (231, 'EMP-0017', '2026-11-17', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (232, 'EMP-0018', '2026-11-17', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (233, 'EMP-0019', '2026-11-17', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (234, 'EMP-0020', '2026-11-17', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (235, 'EMP-0021', '2026-11-17', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (236, 'EMP-0022', '2026-11-17', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (237, 'EMP-0023', '2026-11-17', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (238, 'EMP-0024', '2026-11-17', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (239, 'EMP-0004', '2026-11-18', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (240, 'EMP-0009', '2026-11-18', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (241, 'EMP-0013', '2026-11-18', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (242, 'EMP-0014', '2026-11-18', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (243, 'EMP-0015', '2026-11-18', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (244, 'EMP-0016', '2026-11-18', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (245, 'EMP-0017', '2026-11-18', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (246, 'EMP-0018', '2026-11-18', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (247, 'EMP-0019', '2026-11-18', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (248, 'EMP-0020', '2026-11-18', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (249, 'EMP-0021', '2026-11-18', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (250, 'EMP-0022', '2026-11-18', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (251, 'EMP-0023', '2026-11-18', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (252, 'EMP-0024', '2026-11-18', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (253, 'EMP-0004', '2026-11-19', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (254, 'EMP-0009', '2026-11-19', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (255, 'EMP-0013', '2026-11-19', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (256, 'EMP-0014', '2026-11-19', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (257, 'EMP-0015', '2026-11-19', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (258, 'EMP-0016', '2026-11-19', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (259, 'EMP-0017', '2026-11-19', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (260, 'EMP-0018', '2026-11-19', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (261, 'EMP-0019', '2026-11-19', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (262, 'EMP-0020', '2026-11-19', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (263, 'EMP-0021', '2026-11-19', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (264, 'EMP-0022', '2026-11-19', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (265, 'EMP-0023', '2026-11-19', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (266, 'EMP-0024', '2026-11-19', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (267, 'EMP-0004', '2026-11-20', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (268, 'EMP-0009', '2026-11-20', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (269, 'EMP-0013', '2026-11-20', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (270, 'EMP-0014', '2026-11-20', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (271, 'EMP-0015', '2026-11-20', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (272, 'EMP-0016', '2026-11-20', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (273, 'EMP-0017', '2026-11-20', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (274, 'EMP-0018', '2026-11-20', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (275, 'EMP-0019', '2026-11-20', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (276, 'EMP-0020', '2026-11-20', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (277, 'EMP-0021', '2026-11-20', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (278, 'EMP-0022', '2026-11-20', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (279, 'EMP-0023', '2026-11-20', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (280, 'EMP-0024', '2026-11-20', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (281, 'EMP-0004', '2026-11-21', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (282, 'EMP-0009', '2026-11-21', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (283, 'EMP-0013', '2026-11-21', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (284, 'EMP-0014', '2026-11-21', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (285, 'EMP-0015', '2026-11-21', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (286, 'EMP-0016', '2026-11-21', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (287, 'EMP-0017', '2026-11-21', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (288, 'EMP-0018', '2026-11-21', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (289, 'EMP-0019', '2026-11-21', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (290, 'EMP-0020', '2026-11-21', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (291, 'EMP-0021', '2026-11-21', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (292, 'EMP-0022', '2026-11-21', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (293, 'EMP-0023', '2026-11-21', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (294, 'EMP-0024', '2026-11-21', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (295, 'EMP-0004', '2026-11-22', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (296, 'EMP-0009', '2026-11-22', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (297, 'EMP-0013', '2026-11-22', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (298, 'EMP-0014', '2026-11-22', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (299, 'EMP-0015', '2026-11-22', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (300, 'EMP-0016', '2026-11-22', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (301, 'EMP-0017', '2026-11-22', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (302, 'EMP-0018', '2026-11-22', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (303, 'EMP-0019', '2026-11-22', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (304, 'EMP-0020', '2026-11-22', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (305, 'EMP-0021', '2026-11-22', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (306, 'EMP-0022', '2026-11-22', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (307, 'EMP-0023', '2026-11-22', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (308, 'EMP-0024', '2026-11-22', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (309, 'EMP-0004', '2026-11-23', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (310, 'EMP-0009', '2026-11-23', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (311, 'EMP-0013', '2026-11-23', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (312, 'EMP-0014', '2026-11-23', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (313, 'EMP-0015', '2026-11-23', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (314, 'EMP-0016', '2026-11-23', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (315, 'EMP-0017', '2026-11-23', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:10', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (316, 'EMP-0018', '2026-11-23', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (317, 'EMP-0019', '2026-11-23', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (318, 'EMP-0020', '2026-11-23', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (319, 'EMP-0021', '2026-11-23', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (320, 'EMP-0022', '2026-11-23', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (321, 'EMP-0023', '2026-11-23', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (322, 'EMP-0024', '2026-11-23', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (323, 'EMP-0004', '2026-11-24', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (324, 'EMP-0009', '2026-11-24', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (325, 'EMP-0013', '2026-11-24', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (326, 'EMP-0014', '2026-11-24', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (327, 'EMP-0015', '2026-11-24', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (328, 'EMP-0016', '2026-11-24', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (329, 'EMP-0017', '2026-11-24', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (330, 'EMP-0018', '2026-11-24', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (331, 'EMP-0019', '2026-11-24', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (332, 'EMP-0020', '2026-11-24', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (333, 'EMP-0021', '2026-11-24', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (334, 'EMP-0022', '2026-11-24', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (335, 'EMP-0023', '2026-11-24', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (336, 'EMP-0024', '2026-11-24', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (337, 'EMP-0004', '2026-11-25', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (338, 'EMP-0009', '2026-11-25', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (339, 'EMP-0013', '2026-11-25', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (340, 'EMP-0014', '2026-11-25', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (341, 'EMP-0015', '2026-11-25', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (342, 'EMP-0016', '2026-11-25', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (343, 'EMP-0017', '2026-11-25', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (344, 'EMP-0018', '2026-11-25', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (345, 'EMP-0019', '2026-11-25', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (346, 'EMP-0020', '2026-11-25', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (347, 'EMP-0021', '2026-11-25', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (348, 'EMP-0022', '2026-11-25', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (349, 'EMP-0023', '2026-11-25', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (350, 'EMP-0024', '2026-11-25', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (351, 'EMP-0004', '2026-11-26', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (352, 'EMP-0009', '2026-11-26', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (353, 'EMP-0013', '2026-11-26', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (354, 'EMP-0014', '2026-11-26', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (355, 'EMP-0015', '2026-11-26', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (356, 'EMP-0016', '2026-11-26', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (357, 'EMP-0017', '2026-11-26', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (358, 'EMP-0018', '2026-11-26', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (359, 'EMP-0019', '2026-11-26', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (360, 'EMP-0020', '2026-11-26', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (361, 'EMP-0021', '2026-11-26', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (362, 'EMP-0022', '2026-11-26', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (363, 'EMP-0023', '2026-11-26', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (364, 'EMP-0024', '2026-11-26', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (365, 'EMP-0004', '2026-11-27', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (366, 'EMP-0009', '2026-11-27', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (367, 'EMP-0013', '2026-11-27', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (368, 'EMP-0014', '2026-11-27', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (369, 'EMP-0015', '2026-11-27', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (370, 'EMP-0016', '2026-11-27', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (371, 'EMP-0017', '2026-11-27', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (372, 'EMP-0018', '2026-11-27', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (373, 'EMP-0019', '2026-11-27', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (374, 'EMP-0020', '2026-11-27', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (375, 'EMP-0021', '2026-11-27', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (376, 'EMP-0022', '2026-11-27', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (377, 'EMP-0023', '2026-11-27', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (378, 'EMP-0024', '2026-11-27', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (379, 'EMP-0004', '2026-11-28', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (380, 'EMP-0009', '2026-11-28', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (381, 'EMP-0013', '2026-11-28', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (382, 'EMP-0014', '2026-11-28', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (383, 'EMP-0015', '2026-11-28', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (384, 'EMP-0016', '2026-11-28', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (385, 'EMP-0017', '2026-11-28', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (386, 'EMP-0018', '2026-11-28', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (387, 'EMP-0019', '2026-11-28', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (388, 'EMP-0020', '2026-11-28', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (389, 'EMP-0021', '2026-11-28', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (390, 'EMP-0022', '2026-11-28', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (391, 'EMP-0023', '2026-11-28', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (392, 'EMP-0024', '2026-11-28', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (393, 'EMP-0004', '2026-11-29', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (394, 'EMP-0009', '2026-11-29', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (395, 'EMP-0013', '2026-11-29', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (396, 'EMP-0014', '2026-11-29', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (397, 'EMP-0015', '2026-11-29', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (398, 'EMP-0016', '2026-11-29', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (399, 'EMP-0017', '2026-11-29', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (400, 'EMP-0018', '2026-11-29', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (401, 'EMP-0019', '2026-11-29', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (402, 'EMP-0020', '2026-11-29', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (403, 'EMP-0021', '2026-11-29', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (404, 'EMP-0022', '2026-11-29', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (405, 'EMP-0023', '2026-11-29', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (406, 'EMP-0024', '2026-11-29', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (407, 'EMP-0004', '2026-11-30', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (408, 'EMP-0009', '2026-11-30', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (409, 'EMP-0013', '2026-11-30', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (410, 'EMP-0014', '2026-11-30', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (411, 'EMP-0015', '2026-11-30', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (412, 'EMP-0016', '2026-11-30', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (413, 'EMP-0017', '2026-11-30', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (414, 'EMP-0018', '2026-11-30', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (415, 'EMP-0019', '2026-11-30', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (416, 'EMP-0020', '2026-11-30', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (417, 'EMP-0021', '2026-11-30', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (418, 'EMP-0022', '2026-11-30', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (419, 'EMP-0023', '2026-11-30', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (420, 'EMP-0024', '2026-11-30', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:11', '2026-11-02 06:55:37');
INSERT INTO `jadwal_shift` VALUES (421, 'EMP-0004', '2026-12-01', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (422, 'EMP-0009', '2026-12-01', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (423, 'EMP-0013', '2026-12-01', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (424, 'EMP-0014', '2026-12-01', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (425, 'EMP-0015', '2026-12-01', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (426, 'EMP-0016', '2026-12-01', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (427, 'EMP-0017', '2026-12-01', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (428, 'EMP-0018', '2026-12-01', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (429, 'EMP-0019', '2026-12-01', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (430, 'EMP-0020', '2026-12-01', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (431, 'EMP-0021', '2026-12-01', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (432, 'EMP-0022', '2026-12-01', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (433, 'EMP-0023', '2026-12-01', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (434, 'EMP-0024', '2026-12-01', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (435, 'EMP-0004', '2026-12-02', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (436, 'EMP-0009', '2026-12-02', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (437, 'EMP-0013', '2026-12-02', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (438, 'EMP-0014', '2026-12-02', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (439, 'EMP-0015', '2026-12-02', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (440, 'EMP-0016', '2026-12-02', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (441, 'EMP-0017', '2026-12-02', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (442, 'EMP-0018', '2026-12-02', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (443, 'EMP-0019', '2026-12-02', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (444, 'EMP-0020', '2026-12-02', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (445, 'EMP-0021', '2026-12-02', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (446, 'EMP-0022', '2026-12-02', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (447, 'EMP-0023', '2026-12-02', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (448, 'EMP-0024', '2026-12-02', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (449, 'EMP-0004', '2026-12-03', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (450, 'EMP-0009', '2026-12-03', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (451, 'EMP-0013', '2026-12-03', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (452, 'EMP-0014', '2026-12-03', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (453, 'EMP-0015', '2026-12-03', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (454, 'EMP-0016', '2026-12-03', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (455, 'EMP-0017', '2026-12-03', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (456, 'EMP-0018', '2026-12-03', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (457, 'EMP-0019', '2026-12-03', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (458, 'EMP-0020', '2026-12-03', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (459, 'EMP-0021', '2026-12-03', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (460, 'EMP-0022', '2026-12-03', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (461, 'EMP-0023', '2026-12-03', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (462, 'EMP-0024', '2026-12-03', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (463, 'EMP-0004', '2026-12-04', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (464, 'EMP-0009', '2026-12-04', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (465, 'EMP-0013', '2026-12-04', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (466, 'EMP-0014', '2026-12-04', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (467, 'EMP-0015', '2026-12-04', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (468, 'EMP-0016', '2026-12-04', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (469, 'EMP-0017', '2026-12-04', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (470, 'EMP-0018', '2026-12-04', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (471, 'EMP-0019', '2026-12-04', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (472, 'EMP-0020', '2026-12-04', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (473, 'EMP-0021', '2026-12-04', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (474, 'EMP-0022', '2026-12-04', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (475, 'EMP-0023', '2026-12-04', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (476, 'EMP-0024', '2026-12-04', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (477, 'EMP-0004', '2026-12-05', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (478, 'EMP-0009', '2026-12-05', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (479, 'EMP-0013', '2026-12-05', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (480, 'EMP-0014', '2026-12-05', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (481, 'EMP-0015', '2026-12-05', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (482, 'EMP-0016', '2026-12-05', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (483, 'EMP-0017', '2026-12-05', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (484, 'EMP-0018', '2026-12-05', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (485, 'EMP-0019', '2026-12-05', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (486, 'EMP-0020', '2026-12-05', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (487, 'EMP-0021', '2026-12-05', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (488, 'EMP-0022', '2026-12-05', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (489, 'EMP-0023', '2026-12-05', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (490, 'EMP-0024', '2026-12-05', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (491, 'EMP-0004', '2026-12-06', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (492, 'EMP-0009', '2026-12-06', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (493, 'EMP-0013', '2026-12-06', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (494, 'EMP-0014', '2026-12-06', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (495, 'EMP-0015', '2026-12-06', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (496, 'EMP-0016', '2026-12-06', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (497, 'EMP-0017', '2026-12-06', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (498, 'EMP-0018', '2026-12-06', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (499, 'EMP-0019', '2026-12-06', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (500, 'EMP-0020', '2026-12-06', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (501, 'EMP-0021', '2026-12-06', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (502, 'EMP-0022', '2026-12-06', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (503, 'EMP-0023', '2026-12-06', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (504, 'EMP-0024', '2026-12-06', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (505, 'EMP-0004', '2026-12-07', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (506, 'EMP-0009', '2026-12-07', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (507, 'EMP-0013', '2026-12-07', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (508, 'EMP-0014', '2026-12-07', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (509, 'EMP-0015', '2026-12-07', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (510, 'EMP-0016', '2026-12-07', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (511, 'EMP-0017', '2026-12-07', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (512, 'EMP-0018', '2026-12-07', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (513, 'EMP-0019', '2026-12-07', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (514, 'EMP-0020', '2026-12-07', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (515, 'EMP-0021', '2026-12-07', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (516, 'EMP-0022', '2026-12-07', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (517, 'EMP-0023', '2026-12-07', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (518, 'EMP-0024', '2026-12-07', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (519, 'EMP-0004', '2026-12-08', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (520, 'EMP-0009', '2026-12-08', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (521, 'EMP-0013', '2026-12-08', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (522, 'EMP-0014', '2026-12-08', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (523, 'EMP-0015', '2026-12-08', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (524, 'EMP-0016', '2026-12-08', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (525, 'EMP-0017', '2026-12-08', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (526, 'EMP-0018', '2026-12-08', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (527, 'EMP-0019', '2026-12-08', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (528, 'EMP-0020', '2026-12-08', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (529, 'EMP-0021', '2026-12-08', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (530, 'EMP-0022', '2026-12-08', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (531, 'EMP-0023', '2026-12-08', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (532, 'EMP-0024', '2026-12-08', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (533, 'EMP-0004', '2026-12-09', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (534, 'EMP-0009', '2026-12-09', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (535, 'EMP-0013', '2026-12-09', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (536, 'EMP-0014', '2026-12-09', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (537, 'EMP-0015', '2026-12-09', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (538, 'EMP-0016', '2026-12-09', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (539, 'EMP-0017', '2026-12-09', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (540, 'EMP-0018', '2026-12-09', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (541, 'EMP-0019', '2026-12-09', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (542, 'EMP-0020', '2026-12-09', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (543, 'EMP-0021', '2026-12-09', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (544, 'EMP-0022', '2026-12-09', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (545, 'EMP-0023', '2026-12-09', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (546, 'EMP-0024', '2026-12-09', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (547, 'EMP-0004', '2026-12-10', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (548, 'EMP-0009', '2026-12-10', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (549, 'EMP-0013', '2026-12-10', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (550, 'EMP-0014', '2026-12-10', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (551, 'EMP-0015', '2026-12-10', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (552, 'EMP-0016', '2026-12-10', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (553, 'EMP-0017', '2026-12-10', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (554, 'EMP-0018', '2026-12-10', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (555, 'EMP-0019', '2026-12-10', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (556, 'EMP-0020', '2026-12-10', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (557, 'EMP-0021', '2026-12-10', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (558, 'EMP-0022', '2026-12-10', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (559, 'EMP-0023', '2026-12-10', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (560, 'EMP-0024', '2026-12-10', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (561, 'EMP-0004', '2026-12-11', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (562, 'EMP-0009', '2026-12-11', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (563, 'EMP-0013', '2026-12-11', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (564, 'EMP-0014', '2026-12-11', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (565, 'EMP-0015', '2026-12-11', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (566, 'EMP-0016', '2026-12-11', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (567, 'EMP-0017', '2026-12-11', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (568, 'EMP-0018', '2026-12-11', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (569, 'EMP-0019', '2026-12-11', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (570, 'EMP-0020', '2026-12-11', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (571, 'EMP-0021', '2026-12-11', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (572, 'EMP-0022', '2026-12-11', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (573, 'EMP-0023', '2026-12-11', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (574, 'EMP-0024', '2026-12-11', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (575, 'EMP-0004', '2026-12-12', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (576, 'EMP-0009', '2026-12-12', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (577, 'EMP-0013', '2026-12-12', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (578, 'EMP-0014', '2026-12-12', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:43', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (579, 'EMP-0015', '2026-12-12', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (580, 'EMP-0016', '2026-12-12', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (581, 'EMP-0017', '2026-12-12', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (582, 'EMP-0018', '2026-12-12', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (583, 'EMP-0019', '2026-12-12', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (584, 'EMP-0020', '2026-12-12', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (585, 'EMP-0021', '2026-12-12', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (586, 'EMP-0022', '2026-12-12', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (587, 'EMP-0023', '2026-12-12', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (588, 'EMP-0024', '2026-12-12', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (589, 'EMP-0004', '2026-12-13', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (590, 'EMP-0009', '2026-12-13', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (591, 'EMP-0013', '2026-12-13', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (592, 'EMP-0014', '2026-12-13', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (593, 'EMP-0015', '2026-12-13', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (594, 'EMP-0016', '2026-12-13', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (595, 'EMP-0017', '2026-12-13', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (596, 'EMP-0018', '2026-12-13', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (597, 'EMP-0019', '2026-12-13', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (598, 'EMP-0020', '2026-12-13', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (599, 'EMP-0021', '2026-12-13', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (600, 'EMP-0022', '2026-12-13', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (601, 'EMP-0023', '2026-12-13', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (602, 'EMP-0024', '2026-12-13', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (603, 'EMP-0004', '2026-12-14', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (604, 'EMP-0009', '2026-12-14', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (605, 'EMP-0013', '2026-12-14', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (606, 'EMP-0014', '2026-12-14', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (607, 'EMP-0015', '2026-12-14', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (608, 'EMP-0016', '2026-12-14', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (609, 'EMP-0017', '2026-12-14', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (610, 'EMP-0018', '2026-12-14', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (611, 'EMP-0019', '2026-12-14', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (612, 'EMP-0020', '2026-12-14', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (613, 'EMP-0021', '2026-12-14', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (614, 'EMP-0022', '2026-12-14', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (615, 'EMP-0023', '2026-12-14', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (616, 'EMP-0024', '2026-12-14', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (617, 'EMP-0004', '2026-12-15', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (618, 'EMP-0009', '2026-12-15', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (619, 'EMP-0013', '2026-12-15', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (620, 'EMP-0014', '2026-12-15', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (621, 'EMP-0015', '2026-12-15', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (622, 'EMP-0016', '2026-12-15', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (623, 'EMP-0017', '2026-12-15', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (624, 'EMP-0018', '2026-12-15', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (625, 'EMP-0019', '2026-12-15', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (626, 'EMP-0020', '2026-12-15', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (627, 'EMP-0021', '2026-12-15', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (628, 'EMP-0022', '2026-12-15', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (629, 'EMP-0023', '2026-12-15', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (630, 'EMP-0024', '2026-12-15', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (631, 'EMP-0004', '2026-12-16', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (632, 'EMP-0009', '2026-12-16', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (633, 'EMP-0013', '2026-12-16', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (634, 'EMP-0014', '2026-12-16', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (635, 'EMP-0015', '2026-12-16', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (636, 'EMP-0016', '2026-12-16', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (637, 'EMP-0017', '2026-12-16', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (638, 'EMP-0018', '2026-12-16', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (639, 'EMP-0019', '2026-12-16', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (640, 'EMP-0020', '2026-12-16', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (641, 'EMP-0021', '2026-12-16', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (642, 'EMP-0022', '2026-12-16', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (643, 'EMP-0023', '2026-12-16', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (644, 'EMP-0024', '2026-12-16', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (645, 'EMP-0004', '2026-12-17', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (646, 'EMP-0009', '2026-12-17', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (647, 'EMP-0013', '2026-12-17', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (648, 'EMP-0014', '2026-12-17', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (649, 'EMP-0015', '2026-12-17', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (650, 'EMP-0016', '2026-12-17', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (651, 'EMP-0017', '2026-12-17', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (652, 'EMP-0018', '2026-12-17', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (653, 'EMP-0019', '2026-12-17', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (654, 'EMP-0020', '2026-12-17', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (655, 'EMP-0021', '2026-12-17', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (656, 'EMP-0022', '2026-12-17', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (657, 'EMP-0023', '2026-12-17', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (658, 'EMP-0024', '2026-12-17', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (659, 'EMP-0004', '2026-12-18', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (660, 'EMP-0009', '2026-12-18', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (661, 'EMP-0013', '2026-12-18', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (662, 'EMP-0014', '2026-12-18', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (663, 'EMP-0015', '2026-12-18', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (664, 'EMP-0016', '2026-12-18', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (665, 'EMP-0017', '2026-12-18', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (666, 'EMP-0018', '2026-12-18', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (667, 'EMP-0019', '2026-12-18', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (668, 'EMP-0020', '2026-12-18', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (669, 'EMP-0021', '2026-12-18', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (670, 'EMP-0022', '2026-12-18', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (671, 'EMP-0023', '2026-12-18', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (672, 'EMP-0024', '2026-12-18', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (673, 'EMP-0004', '2026-12-19', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (674, 'EMP-0009', '2026-12-19', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (675, 'EMP-0013', '2026-12-19', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (676, 'EMP-0014', '2026-12-19', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (677, 'EMP-0015', '2026-12-19', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (678, 'EMP-0016', '2026-12-19', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (679, 'EMP-0017', '2026-12-19', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (680, 'EMP-0018', '2026-12-19', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (681, 'EMP-0019', '2026-12-19', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (682, 'EMP-0020', '2026-12-19', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (683, 'EMP-0021', '2026-12-19', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (684, 'EMP-0022', '2026-12-19', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (685, 'EMP-0023', '2026-12-19', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (686, 'EMP-0024', '2026-12-19', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (687, 'EMP-0004', '2026-12-20', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (688, 'EMP-0009', '2026-12-20', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (689, 'EMP-0013', '2026-12-20', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (690, 'EMP-0014', '2026-12-20', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (691, 'EMP-0015', '2026-12-20', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (692, 'EMP-0016', '2026-12-20', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (693, 'EMP-0017', '2026-12-20', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (694, 'EMP-0018', '2026-12-20', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (695, 'EMP-0019', '2026-12-20', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (696, 'EMP-0020', '2026-12-20', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (697, 'EMP-0021', '2026-12-20', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (698, 'EMP-0022', '2026-12-20', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (699, 'EMP-0023', '2026-12-20', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (700, 'EMP-0024', '2026-12-20', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (701, 'EMP-0004', '2026-12-21', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (702, 'EMP-0009', '2026-12-21', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (703, 'EMP-0013', '2026-12-21', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (704, 'EMP-0014', '2026-12-21', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (705, 'EMP-0015', '2026-12-21', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (706, 'EMP-0016', '2026-12-21', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (707, 'EMP-0017', '2026-12-21', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (708, 'EMP-0018', '2026-12-21', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (709, 'EMP-0019', '2026-12-21', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (710, 'EMP-0020', '2026-12-21', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (711, 'EMP-0021', '2026-12-21', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (712, 'EMP-0022', '2026-12-21', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (713, 'EMP-0023', '2026-12-21', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (714, 'EMP-0024', '2026-12-21', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (715, 'EMP-0004', '2026-12-22', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (716, 'EMP-0009', '2026-12-22', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (717, 'EMP-0013', '2026-12-22', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (718, 'EMP-0014', '2026-12-22', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (719, 'EMP-0015', '2026-12-22', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (720, 'EMP-0016', '2026-12-22', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (721, 'EMP-0017', '2026-12-22', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (722, 'EMP-0018', '2026-12-22', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (723, 'EMP-0019', '2026-12-22', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (724, 'EMP-0020', '2026-12-22', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (725, 'EMP-0021', '2026-12-22', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (726, 'EMP-0022', '2026-12-22', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (727, 'EMP-0023', '2026-12-22', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (728, 'EMP-0024', '2026-12-22', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (729, 'EMP-0004', '2026-12-23', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (730, 'EMP-0009', '2026-12-23', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (731, 'EMP-0013', '2026-12-23', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (732, 'EMP-0014', '2026-12-23', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (733, 'EMP-0015', '2026-12-23', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (734, 'EMP-0016', '2026-12-23', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (735, 'EMP-0017', '2026-12-23', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (736, 'EMP-0018', '2026-12-23', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (737, 'EMP-0019', '2026-12-23', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (738, 'EMP-0020', '2026-12-23', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (739, 'EMP-0021', '2026-12-23', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (740, 'EMP-0022', '2026-12-23', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (741, 'EMP-0023', '2026-12-23', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (742, 'EMP-0024', '2026-12-23', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (743, 'EMP-0004', '2026-12-24', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (744, 'EMP-0009', '2026-12-24', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (745, 'EMP-0013', '2026-12-24', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (746, 'EMP-0014', '2026-12-24', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (747, 'EMP-0015', '2026-12-24', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (748, 'EMP-0016', '2026-12-24', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (749, 'EMP-0017', '2026-12-24', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (750, 'EMP-0018', '2026-12-24', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (751, 'EMP-0019', '2026-12-24', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (752, 'EMP-0020', '2026-12-24', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (753, 'EMP-0021', '2026-12-24', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (754, 'EMP-0022', '2026-12-24', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (755, 'EMP-0023', '2026-12-24', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (756, 'EMP-0024', '2026-12-24', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (757, 'EMP-0004', '2026-12-25', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (758, 'EMP-0009', '2026-12-25', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (759, 'EMP-0013', '2026-12-25', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (760, 'EMP-0014', '2026-12-25', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (761, 'EMP-0015', '2026-12-25', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (762, 'EMP-0016', '2026-12-25', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (763, 'EMP-0017', '2026-12-25', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (764, 'EMP-0018', '2026-12-25', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (765, 'EMP-0019', '2026-12-25', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (766, 'EMP-0020', '2026-12-25', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (767, 'EMP-0021', '2026-12-25', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (768, 'EMP-0022', '2026-12-25', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (769, 'EMP-0023', '2026-12-25', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (770, 'EMP-0024', '2026-12-25', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (771, 'EMP-0004', '2026-12-26', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (772, 'EMP-0009', '2026-12-26', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (773, 'EMP-0013', '2026-12-26', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (774, 'EMP-0014', '2026-12-26', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (775, 'EMP-0015', '2026-12-26', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (776, 'EMP-0016', '2026-12-26', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (777, 'EMP-0017', '2026-12-26', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (778, 'EMP-0018', '2026-12-26', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (779, 'EMP-0019', '2026-12-26', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (780, 'EMP-0020', '2026-12-26', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (781, 'EMP-0021', '2026-12-26', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (782, 'EMP-0022', '2026-12-26', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (783, 'EMP-0023', '2026-12-26', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (784, 'EMP-0024', '2026-12-26', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (785, 'EMP-0004', '2026-12-27', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (786, 'EMP-0009', '2026-12-27', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (787, 'EMP-0013', '2026-12-27', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (788, 'EMP-0014', '2026-12-27', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (789, 'EMP-0015', '2026-12-27', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (790, 'EMP-0016', '2026-12-27', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (791, 'EMP-0017', '2026-12-27', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (792, 'EMP-0018', '2026-12-27', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (793, 'EMP-0019', '2026-12-27', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (794, 'EMP-0020', '2026-12-27', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (795, 'EMP-0021', '2026-12-27', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (796, 'EMP-0022', '2026-12-27', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (797, 'EMP-0023', '2026-12-27', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (798, 'EMP-0024', '2026-12-27', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (799, 'EMP-0004', '2026-12-28', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (800, 'EMP-0009', '2026-12-28', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (801, 'EMP-0013', '2026-12-28', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (802, 'EMP-0014', '2026-12-28', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (803, 'EMP-0015', '2026-12-28', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (804, 'EMP-0016', '2026-12-28', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (805, 'EMP-0017', '2026-12-28', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (806, 'EMP-0018', '2026-12-28', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (807, 'EMP-0019', '2026-12-28', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (808, 'EMP-0020', '2026-12-28', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (809, 'EMP-0021', '2026-12-28', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (810, 'EMP-0022', '2026-12-28', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (811, 'EMP-0023', '2026-12-28', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (812, 'EMP-0024', '2026-12-28', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (813, 'EMP-0004', '2026-12-29', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (814, 'EMP-0009', '2026-12-29', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (815, 'EMP-0013', '2026-12-29', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (816, 'EMP-0014', '2026-12-29', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (817, 'EMP-0015', '2026-12-29', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (818, 'EMP-0016', '2026-12-29', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (819, 'EMP-0017', '2026-12-29', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (820, 'EMP-0018', '2026-12-29', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (821, 'EMP-0019', '2026-12-29', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (822, 'EMP-0020', '2026-12-29', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (823, 'EMP-0021', '2026-12-29', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (824, 'EMP-0022', '2026-12-29', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (825, 'EMP-0023', '2026-12-29', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (826, 'EMP-0024', '2026-12-29', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (827, 'EMP-0004', '2026-12-30', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (828, 'EMP-0009', '2026-12-30', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (829, 'EMP-0013', '2026-12-30', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (830, 'EMP-0014', '2026-12-30', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (831, 'EMP-0015', '2026-12-30', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (832, 'EMP-0016', '2026-12-30', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (833, 'EMP-0017', '2026-12-30', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (834, 'EMP-0018', '2026-12-30', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (835, 'EMP-0019', '2026-12-30', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (836, 'EMP-0020', '2026-12-30', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (837, 'EMP-0021', '2026-12-30', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (838, 'EMP-0022', '2026-12-30', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (839, 'EMP-0023', '2026-12-30', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (840, 'EMP-0024', '2026-12-30', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (841, 'EMP-0004', '2026-12-31', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (842, 'EMP-0009', '2026-12-31', 'GS', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (843, 'EMP-0013', '2026-12-31', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (844, 'EMP-0014', '2026-12-31', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (845, 'EMP-0015', '2026-12-31', 'OFF', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (846, 'EMP-0016', '2026-12-31', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (847, 'EMP-0017', '2026-12-31', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (848, 'EMP-0018', '2026-12-31', 'S3', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (849, 'EMP-0019', '2026-12-31', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (850, 'EMP-0020', '2026-12-31', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (851, 'EMP-0021', '2026-12-31', 'S2', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (852, 'EMP-0022', '2026-12-31', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (853, 'EMP-0023', '2026-12-31', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (854, 'EMP-0024', '2026-12-31', 'S1', 'POLA', 'PUBLISHED', 1, 1, '2026-11-02 06:53:44', '2026-11-02 06:54:02');
INSERT INTO `jadwal_shift` VALUES (855, 'EMP-0001', '2026-11-02', 'GS', 'MANUAL', 'PUBLISHED', NULL, NULL, '2026-11-02 07:46:01', '2026-11-02 07:46:01');

-- ----------------------------
-- Table structure for job_batches
-- ----------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `cancelled_at` int NULL DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of job_batches
-- ----------------------------

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED NULL DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `jobs_queue_index`(`queue` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jobs
-- ----------------------------

-- ----------------------------
-- Table structure for karyawan
-- ----------------------------
DROP TABLE IF EXISTS `karyawan`;
CREATE TABLE `karyawan`  (
  `id_karyawan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_karyawan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jns_kelamin` enum('L','P') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_lahir` date NOT NULL,
  `id_departemen` int UNSIGNED NOT NULL,
  `id_jabatan` int UNSIGNED NOT NULL,
  `id_grup` int UNSIGNED NULL DEFAULT NULL,
  `status_karyawan` enum('AKTIF','NONAKTIF') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AKTIF',
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_karyawan`) USING BTREE,
  INDEX `karyawan_id_departemen_foreign`(`id_departemen` ASC) USING BTREE,
  INDEX `karyawan_id_jabatan_foreign`(`id_jabatan` ASC) USING BTREE,
  INDEX `karyawan_id_grup_foreign`(`id_grup` ASC) USING BTREE,
  CONSTRAINT `karyawan_id_departemen_foreign` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id_departemen`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `karyawan_id_grup_foreign` FOREIGN KEY (`id_grup`) REFERENCES `grup_karyawan` (`id_grup`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `karyawan_id_jabatan_foreign` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of karyawan
-- ----------------------------
INSERT INTO `karyawan` VALUES ('EMP-0001', 'Subani', 'L', '1988-04-12', 1, 4, NULL, 'AKTIF', '081259204507', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0002', 'Jerry', 'L', '1975-02-20', 1, 1, NULL, 'AKTIF', '081238368529', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0003', 'Dewi Lestari', 'P', '1982-08-15', 1, 2, NULL, 'AKTIF', '081222904295', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0004', 'Satrio', 'L', '1980-11-05', 3, 5, NULL, 'AKTIF', '081235000950', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0005', 'Bambang Wijaya', 'L', '1983-03-22', 2, 8, NULL, 'AKTIF', '081260328370', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0006', 'Ratna Sari', 'P', '1984-07-10', 4, 11, NULL, 'AKTIF', '081223598279', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0007', 'Joko Prasetyo', 'L', '1981-09-30', 5, 14, NULL, 'AKTIF', '081257018080', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0008', 'Siti Aminah', 'P', '1990-05-18', 1, 3, NULL, 'AKTIF', '081232456602', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0009', 'Gilang', 'L', '1989-01-25', 3, 6, NULL, 'AKTIF', '081280455077', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0010', 'Rudi Hartono', 'L', '1987-12-14', 2, 9, NULL, 'AKTIF', '081212965542', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0011', 'Eko Wahyudi', 'L', '1991-06-08', 4, 12, NULL, 'AKTIF', '081262306063', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0012', 'Dedi Kurniawan', 'L', '1992-04-03', 5, 15, NULL, 'AKTIF', '081243662060', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0013', 'Sarwan', 'L', '1996-03-11', 3, 7, 1, 'AKTIF', '081263720368', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0014', 'Galih Ramadhan', 'L', '1997-07-29', 3, 7, 1, 'AKTIF', '081236745463', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0015', 'Hendra Saputra', 'L', '1998-10-14', 3, 7, 1, 'AKTIF', '081297532483', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0016', 'Indra Lesmana', 'L', '1995-09-02', 3, 7, 2, 'AKTIF', '081228066771', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0017', 'Joni Iskandar', 'L', '1996-12-19', 3, 7, 2, 'AKTIF', '081234768176', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0018', 'Kiki Firmansyah', 'L', '1999-04-21', 3, 7, 2, 'AKTIF', '081249043951', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0019', 'Lukman Hakim', 'L', '1994-08-07', 3, 7, 3, 'AKTIF', '081268765530', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0020', 'Miftah Hidayat', 'L', '1997-02-14', 3, 7, 3, 'AKTIF', '081299526416', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0021', 'Nurul Huda', 'P', '1998-05-30', 3, 7, 3, 'AKTIF', '081283777458', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0022', 'Oki Setiawan', 'L', '1995-11-17', 3, 7, 4, 'AKTIF', '081222966804', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0023', 'Panji Gumilang', 'L', '1996-01-22', 3, 7, 4, 'AKTIF', '081258706047', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0024', 'Rian Hidayatullah', 'L', '1997-06-18', 3, 7, 4, 'AKTIF', '081216717987', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0025', 'Surya Kencana', 'L', '1993-05-12', 2, 10, 1, 'AKTIF', '081234757834', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0026', 'Taufik Rahman', 'L', '1994-09-28', 2, 10, 2, 'AKTIF', '081228053549', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0027', 'Utami Pujiastuti', 'P', '1996-10-15', 4, 13, 3, 'AKTIF', '081280507697', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0028', 'Vina Panduwinata', 'P', '1998-03-08', 4, 13, 4, 'AKTIF', '081216190950', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0029', 'Wahyu Santoso', 'L', '1995-07-20', 5, 16, 1, 'AKTIF', '081236125571', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0030', 'Yayan Ruhian', 'L', '1994-12-04', 5, 16, 2, 'AKTIF', '081259184294', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0031', 'Zulfikar Ali', 'L', '1992-02-17', 1, 4, NULL, 'AKTIF', '081299272383', '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `karyawan` VALUES ('EMP-0032', 'Anisa Rahmawati', 'P', '1995-08-23', 2, 10, NULL, 'AKTIF', '081296856959', '2026-11-02 06:15:45', '2026-11-02 06:15:45');

-- ----------------------------
-- Table structure for lembur
-- ----------------------------
DROP TABLE IF EXISTS `lembur`;
CREATE TABLE `lembur`  (
  `id_lembur` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_karyawan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('DIAJUKAN','DISETUJUI','DITOLAK') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DIAJUKAN',
  `id_approver` bigint UNSIGNED NULL DEFAULT NULL,
  `menit_lembur` smallint NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_lembur`) USING BTREE,
  INDEX `lembur_id_karyawan_foreign`(`id_karyawan` ASC) USING BTREE,
  INDEX `lembur_id_approver_foreign`(`id_approver` ASC) USING BTREE,
  CONSTRAINT `lembur_id_approver_foreign` FOREIGN KEY (`id_approver`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `lembur_id_karyawan_foreign` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of lembur
-- ----------------------------
INSERT INTO `lembur` VALUES (1, 'EMP-0013', '2026-11-03', '11:00:00', '14:00:00', 'Butuh Tambahan', 'DIAJUKAN', NULL, 180, '2026-11-02 06:44:19', '2026-11-02 06:44:19');

-- ----------------------------
-- Table structure for logbook_shift
-- ----------------------------
DROP TABLE IF EXISTS `logbook_shift`;
CREATE TABLE `logbook_shift`  (
  `id_logbook` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `kode_shift` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_departemen` int UNSIGNED NOT NULL,
  `id_grup` int UNSIGNED NULL DEFAULT NULL,
  `id_pembuat` bigint UNSIGNED NOT NULL,
  `ringkasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kendala` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `tindak_lanjut` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_logbook`) USING BTREE,
  INDEX `logbook_shift_kode_shift_foreign`(`kode_shift` ASC) USING BTREE,
  INDEX `logbook_shift_id_departemen_foreign`(`id_departemen` ASC) USING BTREE,
  INDEX `logbook_shift_id_grup_foreign`(`id_grup` ASC) USING BTREE,
  INDEX `logbook_shift_id_pembuat_foreign`(`id_pembuat` ASC) USING BTREE,
  CONSTRAINT `logbook_shift_id_departemen_foreign` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id_departemen`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `logbook_shift_id_grup_foreign` FOREIGN KEY (`id_grup`) REFERENCES `grup_karyawan` (`id_grup`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `logbook_shift_id_pembuat_foreign` FOREIGN KEY (`id_pembuat`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `logbook_shift_kode_shift_foreign` FOREIGN KEY (`kode_shift`) REFERENCES `shift` (`kode_shift`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of logbook_shift
-- ----------------------------

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` VALUES (2, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` VALUES (3, '2026_10_20_000001_create_departemen_table', 1);
INSERT INTO `migrations` VALUES (4, '2026_10_20_000002_create_jabatan_table', 1);
INSERT INTO `migrations` VALUES (5, '2026_10_20_000003_create_grup_karyawan_table', 1);
INSERT INTO `migrations` VALUES (6, '2026_10_20_000004_create_shift_table', 1);
INSERT INTO `migrations` VALUES (7, '2026_10_20_000005_create_karyawan_table', 1);
INSERT INTO `migrations` VALUES (8, '2026_10_20_000006_create_users_table', 1);
INSERT INTO `migrations` VALUES (9, '2026_10_20_000007_create_pola_rotasi_table', 1);
INSERT INTO `migrations` VALUES (10, '2026_10_20_000008_create_pola_rotasi_detail_table', 1);
INSERT INTO `migrations` VALUES (11, '2026_10_20_000009_create_jadwal_shift_table', 1);
INSERT INTO `migrations` VALUES (12, '2026_10_20_000010_create_absensi_table', 1);
INSERT INTO `migrations` VALUES (13, '2026_10_20_000011_create_pengajuan_table', 1);
INSERT INTO `migrations` VALUES (14, '2026_10_20_000012_create_tukar_shift_table', 1);
INSERT INTO `migrations` VALUES (15, '2026_10_20_000013_create_lembur_table', 1);
INSERT INTO `migrations` VALUES (16, '2026_10_20_000014_create_logbook_shift_table', 1);
INSERT INTO `migrations` VALUES (17, '2026_10_20_000015_create_hari_libur_table', 1);
INSERT INTO `migrations` VALUES (18, '2026_10_20_000016_create_pengaturan_table', 1);
INSERT INTO `migrations` VALUES (19, '2026_10_20_000017_create_notifikasi_table', 1);
INSERT INTO `migrations` VALUES (20, '2026_10_20_000018_create_audit_log_table', 1);

-- ----------------------------
-- Table structure for notifikasi
-- ----------------------------
DROP TABLE IF EXISTS `notifikasi`;
CREATE TABLE `notifikasi`  (
  `id_notifikasi` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` bigint UNSIGNED NOT NULL,
  `judul` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `dibaca_at` datetime NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notifikasi`) USING BTREE,
  INDEX `notifikasi_id_user_foreign`(`id_user` ASC) USING BTREE,
  CONSTRAINT `notifikasi_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of notifikasi
-- ----------------------------
INSERT INTO `notifikasi` VALUES (1, 9, 'Presensi Masuk: Sarwan', 'Sarwan telah absen masuk via dashboard pada 13:41:32 WIB (TERLAMBAT (341 Menit)).', '/absensi/koreksi', NULL, '2026-11-02 13:41:32');
INSERT INTO `notifikasi` VALUES (2, 9, 'Pengajuan Lembur Baru', 'Sarwan mengajukan lembur pada tanggal 2026-11-03 (180 menit). Alasan: Butuh Tambahan', '/pengajuan-approval', NULL, '2026-11-02 13:44:19');
INSERT INTO `notifikasi` VALUES (3, 8, 'Presensi Masuk: Subani', 'Subani telah absen masuk via dashboard pada 14:46:01 WIB (TERLAMBAT (406 Menit)).', '/absensi/koreksi', NULL, '2026-11-02 14:46:01');

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens`  (
  `id_karyawan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_karyawan`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for pengajuan
-- ----------------------------
DROP TABLE IF EXISTS `pengajuan`;
CREATE TABLE `pengajuan`  (
  `id_pengajuan` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_karyawan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` enum('CUTI','IZIN','SAKIT') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lampiran_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `status` enum('DIAJUKAN','DISETUJUI','DITOLAK','DIBATALKAN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DIAJUKAN',
  `tahap` enum('SUPERVISOR','HOD','SELESAI') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SUPERVISOR',
  `id_approver` bigint UNSIGNED NULL DEFAULT NULL,
  `catatan_approver` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `keputusan_at` datetime NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pengajuan`) USING BTREE,
  INDEX `pengajuan_id_karyawan_foreign`(`id_karyawan` ASC) USING BTREE,
  INDEX `pengajuan_id_approver_foreign`(`id_approver` ASC) USING BTREE,
  CONSTRAINT `pengajuan_id_approver_foreign` FOREIGN KEY (`id_approver`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `pengajuan_id_karyawan_foreign` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pengajuan
-- ----------------------------

-- ----------------------------
-- Table structure for pengaturan
-- ----------------------------
DROP TABLE IF EXISTS `pengaturan`;
CREATE TABLE `pengaturan`  (
  `kunci` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kunci`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pengaturan
-- ----------------------------
INSERT INTO `pengaturan` VALUES ('batas_alpa_menit_setelah_pulang', '30', 'Batas penandaan ALPA otomatis setelah jam pulang', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pengaturan` VALUES ('cuti_perlu_hod_hari', '3', 'Jumlah hari cuti yang membutuhkan persetujuan HOD', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pengaturan` VALUES ('jendela_absen_masuk_menit', '60', 'Absen masuk dibuka N menit sebelum jam masuk', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pengaturan` VALUES ('min_istirahat_jam', '8', 'Minimal jam istirahat antar dua shift kerja', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pengaturan` VALUES ('toleransi_terlambat_menit', '10', 'Toleransi keterlambatan dalam menit', '2026-11-02 06:15:44', '2026-11-02 06:15:44');

-- ----------------------------
-- Table structure for pola_rotasi
-- ----------------------------
DROP TABLE IF EXISTS `pola_rotasi`;
CREATE TABLE `pola_rotasi`  (
  `id_pola` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_pola` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `panjang_siklus_hari` tinyint NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `is_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_pola`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pola_rotasi
-- ----------------------------
INSERT INTO `pola_rotasi` VALUES (1, 'Pola 4 Grup 3 Shift (8 Hari)', 8, 'Siklus 8 hari: 2 hari S1, 2 hari S2, 2 hari S3, 2 hari OFF per grup bergantian.', 1, '2026-11-02 06:15:44', '2026-11-02 06:15:44');

-- ----------------------------
-- Table structure for pola_rotasi_detail
-- ----------------------------
DROP TABLE IF EXISTS `pola_rotasi_detail`;
CREATE TABLE `pola_rotasi_detail`  (
  `id_detail` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pola` int UNSIGNED NOT NULL,
  `id_grup` int UNSIGNED NOT NULL,
  `hari_ke` tinyint NOT NULL,
  `kode_shift` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_detail`) USING BTREE,
  UNIQUE INDEX `pola_rotasi_detail_id_pola_id_grup_hari_ke_unique`(`id_pola` ASC, `id_grup` ASC, `hari_ke` ASC) USING BTREE,
  INDEX `pola_rotasi_detail_id_grup_foreign`(`id_grup` ASC) USING BTREE,
  INDEX `pola_rotasi_detail_kode_shift_foreign`(`kode_shift` ASC) USING BTREE,
  CONSTRAINT `pola_rotasi_detail_id_grup_foreign` FOREIGN KEY (`id_grup`) REFERENCES `grup_karyawan` (`id_grup`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `pola_rotasi_detail_id_pola_foreign` FOREIGN KEY (`id_pola`) REFERENCES `pola_rotasi` (`id_pola`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `pola_rotasi_detail_kode_shift_foreign` FOREIGN KEY (`kode_shift`) REFERENCES `shift` (`kode_shift`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pola_rotasi_detail
-- ----------------------------
INSERT INTO `pola_rotasi_detail` VALUES (1, 1, 1, 1, 'S1', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (2, 1, 1, 2, 'S1', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (3, 1, 1, 3, 'S2', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (4, 1, 1, 4, 'S2', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (5, 1, 1, 5, 'S3', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (6, 1, 1, 6, 'S3', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (7, 1, 1, 7, 'OFF', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (8, 1, 1, 8, 'OFF', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (9, 1, 2, 1, 'OFF', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (10, 1, 2, 2, 'OFF', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (11, 1, 2, 3, 'S1', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (12, 1, 2, 4, 'S1', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (13, 1, 2, 5, 'S2', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (14, 1, 2, 6, 'S2', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (15, 1, 2, 7, 'S3', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (16, 1, 2, 8, 'S3', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (17, 1, 3, 1, 'S3', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (18, 1, 3, 2, 'S3', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (19, 1, 3, 3, 'OFF', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (20, 1, 3, 4, 'OFF', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (21, 1, 3, 5, 'S1', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (22, 1, 3, 6, 'S1', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (23, 1, 3, 7, 'S2', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (24, 1, 3, 8, 'S2', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (25, 1, 4, 1, 'S2', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (26, 1, 4, 2, 'S2', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (27, 1, 4, 3, 'S3', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (28, 1, 4, 4, 'S3', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (29, 1, 4, 5, 'OFF', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (30, 1, 4, 6, 'OFF', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (31, 1, 4, 7, 'S1', '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `pola_rotasi_detail` VALUES (32, 1, 4, 8, 'S1', '2026-11-02 06:15:44', '2026-11-02 06:15:44');

-- ----------------------------
-- Table structure for sessions
-- ----------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions`  (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NULL DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `sessions_user_id_index`(`user_id` ASC) USING BTREE,
  INDEX `sessions_last_activity_index`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of sessions
-- ----------------------------
INSERT INTO `sessions` VALUES ('bpEe3aThrWTI8MkHWe452LYcsRJSQs9SO5tNFAVk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJMN0ZUbGZZcE84OWRpQlZqYWp5bkk1Z2pmMWNSUXN2Vk9lazNFaUQ0IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1793863302);
INSERT INTO `sessions` VALUES ('vbGviKwI17mllG0hLuSgAAWuoCfrWWFLVS40YBtc', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIydmJOOE1hdG01VndoWktORXNzODlxWUhDYUYyc01KY0sySUF0aUFyIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==', 1793605709);

-- ----------------------------
-- Table structure for shift
-- ----------------------------
DROP TABLE IF EXISTS `shift`;
CREATE TABLE `shift`  (
  `kode_shift` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_shift` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jam_masuk` time NULL DEFAULT NULL,
  `jam_pulang` time NULL DEFAULT NULL,
  `lintas_hari` tinyint(1) NOT NULL DEFAULT 0,
  `toleransi_menit` tinyint NOT NULL DEFAULT 10,
  `warna` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_libur` tinyint(1) NOT NULL DEFAULT 0,
  `is_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`kode_shift`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of shift
-- ----------------------------
INSERT INTO `shift` VALUES ('GS', 'General Shift', '08:00:00', '17:00:00', 0, 10, '#2563EB', 0, 1, '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `shift` VALUES ('OFF', 'Libur Rutin', NULL, NULL, 0, 0, '#94A3B8', 1, 1, '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `shift` VALUES ('S1', 'Shift 1 (Pagi)', '07:00:00', '15:00:00', 0, 10, '#F59E0B', 0, 1, '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `shift` VALUES ('S2', 'Shift 2 (Siang)', '15:00:00', '23:00:00', 0, 10, '#EA580C', 0, 1, '2026-11-02 06:15:44', '2026-11-02 06:15:44');
INSERT INTO `shift` VALUES ('S3', 'Shift 3 (Malam)', '23:00:00', '07:00:00', 1, 10, '#4338CA', 0, 1, '2026-11-02 06:15:44', '2026-11-02 06:15:44');

-- ----------------------------
-- Table structure for tukar_shift
-- ----------------------------
DROP TABLE IF EXISTS `tukar_shift`;
CREATE TABLE `tukar_shift`  (
  `id_tukar` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pemohon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_pengganti` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_jadwal_pemohon` bigint UNSIGNED NOT NULL,
  `id_jadwal_pengganti` bigint UNSIGNED NOT NULL,
  `alasan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('MENUNGGU_PENGGANTI','MENUNGGU_SUPERVISOR','DISETUJUI','DITOLAK','DIBATALKAN') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MENUNGGU_PENGGANTI',
  `id_approver` bigint UNSIGNED NULL DEFAULT NULL,
  `keputusan_at` datetime NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_tukar`) USING BTREE,
  INDEX `tukar_shift_id_pemohon_foreign`(`id_pemohon` ASC) USING BTREE,
  INDEX `tukar_shift_id_pengganti_foreign`(`id_pengganti` ASC) USING BTREE,
  INDEX `tukar_shift_id_jadwal_pemohon_foreign`(`id_jadwal_pemohon` ASC) USING BTREE,
  INDEX `tukar_shift_id_jadwal_pengganti_foreign`(`id_jadwal_pengganti` ASC) USING BTREE,
  INDEX `tukar_shift_id_approver_foreign`(`id_approver` ASC) USING BTREE,
  CONSTRAINT `tukar_shift_id_approver_foreign` FOREIGN KEY (`id_approver`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `tukar_shift_id_jadwal_pemohon_foreign` FOREIGN KEY (`id_jadwal_pemohon`) REFERENCES `jadwal_shift` (`id_jadwal`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `tukar_shift_id_jadwal_pengganti_foreign` FOREIGN KEY (`id_jadwal_pengganti`) REFERENCES `jadwal_shift` (`id_jadwal`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `tukar_shift_id_pemohon_foreign` FOREIGN KEY (`id_pemohon`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `tukar_shift_id_pengganti_foreign` FOREIGN KEY (`id_pengganti`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tukar_shift
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id_user` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_karyawan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pin_absen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `role` enum('ADMIN_HR','DIREKTUR','HOD','SUPERVISOR','OPERATOR') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`) USING BTREE,
  UNIQUE INDEX `users_id_karyawan_unique`(`id_karyawan` ASC) USING BTREE,
  CONSTRAINT `users_id_karyawan_foreign` FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'EMP-0001', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'ADMIN_HR', 1, '2026-11-02 14:44:52', NULL, '2026-11-02 06:15:45', '2026-11-02 07:44:52');
INSERT INTO `users` VALUES (2, 'EMP-0002', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'DIREKTUR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (3, 'EMP-0003', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'HOD', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (4, 'EMP-0004', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'HOD', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (5, 'EMP-0005', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'HOD', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (6, 'EMP-0006', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'HOD', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (7, 'EMP-0007', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'HOD', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (8, 'EMP-0008', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'SUPERVISOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (9, 'EMP-0009', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'SUPERVISOR', 1, '2026-11-02 14:01:02', NULL, '2026-11-02 06:15:45', '2026-11-02 07:01:02');
INSERT INTO `users` VALUES (10, 'EMP-0010', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'SUPERVISOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (11, 'EMP-0011', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'SUPERVISOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (12, 'EMP-0012', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'SUPERVISOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (13, 'EMP-0013', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, '2026-11-02 13:56:38', NULL, '2026-11-02 06:15:45', '2026-11-02 06:56:38');
INSERT INTO `users` VALUES (14, 'EMP-0014', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (15, 'EMP-0015', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (16, 'EMP-0016', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (17, 'EMP-0017', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (18, 'EMP-0018', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (19, 'EMP-0019', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (20, 'EMP-0020', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (21, 'EMP-0021', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (22, 'EMP-0022', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (23, 'EMP-0023', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (24, 'EMP-0024', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (25, 'EMP-0025', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (26, 'EMP-0026', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (27, 'EMP-0027', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (28, 'EMP-0028', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (29, 'EMP-0029', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (30, 'EMP-0030', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (31, 'EMP-0031', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');
INSERT INTO `users` VALUES (32, 'EMP-0032', '$2y$12$z9o.GNyaECaRo35XuPYqeuN7paGsz6uYZrS8eKmOTS/aAdWDPM1Wm', '$2y$12$VgB2Ah2y6s/al9mw8HA2mu9mUgCOFHTuAxXtEJMqBsyvdvVRwSiKS', 'OPERATOR', 1, NULL, NULL, '2026-11-02 06:15:45', '2026-11-02 06:15:45');

SET FOREIGN_KEY_CHECKS = 1;
