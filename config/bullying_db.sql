-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for bullying_db
DROP DATABASE IF EXISTS `bullying_db`;
CREATE DATABASE IF NOT EXISTS `bullying_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `bullying_db`;

-- Dumping structure for table bullying_db.guru
DROP TABLE IF EXISTS `guru`;
CREATE TABLE IF NOT EXISTS `guru` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `jabatan_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `jabatan_id` (`jabatan_id`),
  CONSTRAINT `guru_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `guru_ibfk_2` FOREIGN KEY (`jabatan_id`) REFERENCES `jabatan_guru` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.guru: ~1 rows (approximately)
DELETE FROM `guru`;
INSERT INTO `guru` (`id`, `user_id`, `jabatan_id`) VALUES
	(4, 16, 2);

-- Dumping structure for table bullying_db.jabatan_guru
DROP TABLE IF EXISTS `jabatan_guru`;
CREATE TABLE IF NOT EXISTS `jabatan_guru` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_jabatan` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_jabatan` (`nama_jabatan`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.jabatan_guru: ~5 rows (approximately)
DELETE FROM `jabatan_guru`;
INSERT INTO `jabatan_guru` (`id`, `nama_jabatan`) VALUES
	(4, 'Guru BK'),
	(1, 'Guru Mata Pelajaran'),
	(3, 'Kepala Sekolah'),
	(5, 'Staf Tata Usaha'),
	(6, 'test'),
	(2, 'Wali Kelas');

-- Dumping structure for table bullying_db.jurusan
DROP TABLE IF EXISTS `jurusan`;
CREATE TABLE IF NOT EXISTS `jurusan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_jurusan` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_jurusan` (`nama_jurusan`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.jurusan: ~5 rows (approximately)
DELETE FROM `jurusan`;
INSERT INTO `jurusan` (`id`, `nama_jurusan`) VALUES
	(4, 'Akuntansi Keuangan Lembaga'),
	(5, 'Bisnis Daring dan Pemasaran'),
	(3, 'Multimedia'),
	(1, 'Rekayasa Perangkat Lunak'),
	(2, 'Teknik Komputer dan Jaringan');

-- Dumping structure for table bullying_db.kategori_laporan
DROP TABLE IF EXISTS `kategori_laporan`;
CREATE TABLE IF NOT EXISTS `kategori_laporan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_kategori` (`nama_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.kategori_laporan: ~4 rows (approximately)
DELETE FROM `kategori_laporan`;
INSERT INTO `kategori_laporan` (`id`, `nama_kategori`) VALUES
	(4, 'Cyberbullying'),
	(1, 'Fisik'),
	(3, 'Sosial'),
	(2, 'Verbal');

-- Dumping structure for table bullying_db.kelas
DROP TABLE IF EXISTS `kelas`;
CREATE TABLE IF NOT EXISTS `kelas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_kelas` (`nama_kelas`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.kelas: ~5 rows (approximately)
DELETE FROM `kelas`;
INSERT INTO `kelas` (`id`, `nama_kelas`) VALUES
	(1, 'X RPL 1'),
	(2, 'X RPL 2'),
	(4, 'XI MM 2'),
	(3, 'XI TKJ 1'),
	(5, 'XII AKL 3');

-- Dumping structure for table bullying_db.laporan
DROP TABLE IF EXISTS `laporan`;
CREATE TABLE IF NOT EXISTS `laporan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `kategori_id` int NOT NULL,
  `kronologi` text NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `tanggal_kejadian` date NOT NULL,
  `bukti` varchar(255) DEFAULT NULL,
  `status` enum('terkirim','diproses','Selesai','Ditolak') NOT NULL DEFAULT 'terkirim',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `laporan_ibfk_2` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_laporan` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.laporan: ~4 rows (approximately)
DELETE FROM `laporan`;
INSERT INTO `laporan` (`id`, `user_id`, `kategori_id`, `kronologi`, `lokasi`, `tanggal_kejadian`, `bukti`, `status`, `created_at`, `updated_at`) VALUES
	(17, 15, 4, 'Mukul kepala saya', 'Jalan Kyai Haji Tubagus Ismail, Kp Rokal, Ciwaduk, Cilegon, Java, 42411, Indonesia', '2025-07-09', 'bukti_686de459225e6.png', 'Selesai', '2025-07-09 03:39:05', '2025-07-09 03:49:15'),
	(18, 15, 3, 'test', 'Jalan Kyai Haji Tubagus Ismail, Kp Rokal, Ciwaduk, Cilegon, Java, 42411, Indonesia', '2025-07-09', 'bukti_686de86b0e6a8.png', 'diproses', '2025-07-09 03:56:27', '2025-07-09 03:59:41');

-- Dumping structure for table bullying_db.sanksi
DROP TABLE IF EXISTS `sanksi`;
CREATE TABLE IF NOT EXISTS `sanksi` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `laporan_id` int NOT NULL,
  `jenis_sanksi` enum('Ringan','Berat') NOT NULL,
  `deskripsi` text,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `diberikan_oleh` varchar(255) DEFAULT 'Guru BK',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `laporan_id` (`laporan_id`),
  CONSTRAINT `sanksi_ibfk_1` FOREIGN KEY (`laporan_id`) REFERENCES `laporan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.sanksi: ~2 rows (approximately)
DELETE FROM `sanksi`;
INSERT INTO `sanksi` (`id`, `laporan_id`, `jenis_sanksi`, `deskripsi`, `tanggal_mulai`, `tanggal_selesai`, `diberikan_oleh`, `created_at`, `updated_at`) VALUES
	(6, 17, 'Ringan', 'Saya berikan skors selama 5 hari', '2025-07-09', '2025-07-13', 'Guru Bk', '2025-07-09 03:43:06', '2025-07-09 03:43:06');

-- Dumping structure for table bullying_db.siswa
DROP TABLE IF EXISTS `siswa`;
CREATE TABLE IF NOT EXISTS `siswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `kelas_id` int DEFAULT NULL,
  `jurusan_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `kelas_id` (`kelas_id`),
  KEY `jurusan_id` (`jurusan_id`),
  CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `siswa_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswa_ibfk_3` FOREIGN KEY (`jurusan_id`) REFERENCES `jurusan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.siswa: ~2 rows (approximately)
DELETE FROM `siswa`;
INSERT INTO `siswa` (`id`, `user_id`, `kelas_id`, `jurusan_id`) VALUES
	(2, 11, 1, 3),
	(3, 15, 1, 1),
	(4, 18, NULL, NULL);

-- Dumping structure for table bullying_db.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nis_nip` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('siswa','guru','admin','kepsek') NOT NULL,
  `nama` varchar(255) NOT NULL,
  `foto_profile` varchar(255) DEFAULT 'default.png',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nis_nip` (`nis_nip`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.users: ~4 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `nis_nip`, `password`, `role`, `nama`, `foto_profile`, `created_at`, `updated_at`, `last_seen_at`) VALUES
	(11, '202112019', '$2y$10$m1bbM//hT3Y.VMNr8C3J2O2s60WVfQbcVOtKFy17TS1VpeFdOVykK', 'siswa', 'Adimas Rizki', 'profile_6852540bcf07d.jpg', '2025-06-18 05:19:57', '2025-06-18 07:14:00', NULL),
	(14, '10001', '$2y$10$RCnVK6gL0FlX4GKWZmuqFuYkoy8FSawhnfuav0MBRvhlHeRZ.Zkbu', 'admin', 'Admin Dimszz', 'profile_68525d7627fd0.jpg', '2025-06-18 06:20:47', '2025-07-07 08:20:37', '2025-06-18 07:39:04'),
	(15, '202112020', '$2y$10$m1bbM//hT3Y.VMNr8C3J2O2s60WVfQbcVOtKFy17TS1VpeFdOVykK', 'siswa', 'Sufaehah', 'profile_68525ea40657f.jpg', '2025-06-18 06:35:52', '2025-07-09 03:28:51', NULL),
	(16, '20001', '$2y$10$7l6yAh8DezkfzDF3.AAjN.PWWt9u7Z7QXD1/3Wzh22h5rD3AYFFFq', 'guru', 'Ayunda', 'profile_68526da16d813.jpg', '2025-06-18 07:35:50', '2025-06-18 07:41:21', NULL),
	(17, '30001', '$2y$10$yX6KyMJV9fs35fXExI1WSecDZrd8FTRx1BJsjGnrumpbX6FlHRKVa', 'kepsek', 'Bpk. Ahmad Sanjaya', 'profile_6853086c7189b.png', '2025-06-18 18:21:11', '2025-06-18 18:41:48', NULL),
	(18, '202112001', '$2y$10$DnRjXKLph/rdkHhe/snhtOR6anzuB5gGtNVs3kRKl9RIq69tLDWEK', 'siswa', 'Maherza', NULL, '2025-06-20 09:28:57', '2025-06-20 09:28:57', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
