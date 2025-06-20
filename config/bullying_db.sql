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
CREATE DATABASE IF NOT EXISTS `bullying_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `bullying_db`;

-- Dumping structure for table bullying_db.guru
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
ALTER TABLE guru AUTO_INCREMENT = 1;

-- Dumping structure for table bullying_db.jabatan_guru
CREATE TABLE IF NOT EXISTS `jabatan_guru` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_jabatan` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_jabatan` (`nama_jabatan`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.jabatan_guru: ~5 rows (approximately)
DELETE FROM `jabatan_guru`;
INSERT INTO `jabatan_guru` (`id`, `nama_jabatan`) VALUES
	(1, 'Guru BK'),
	(2, 'Guru Mata Pelajaran'),
	(3, 'Kepala Sekolah'),
	(4, 'Staf Tata Usaha'),
	(5, 'test'),
	(6, 'Wali Kelas');

-- Dumping structure for table bullying_db.jurusan
CREATE TABLE IF NOT EXISTS `jurusan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_jurusan` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_jurusan` (`nama_jurusan`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.jurusan: ~5 rows (approximately)
DELETE FROM `jurusan`;
INSERT INTO `jurusan` (`id`, `nama_jurusan`) VALUES
	(1, 'Akuntansi Keuangan Lembaga'),
	(2, 'Bisnis Daring dan Pemasaran'),
	(3, 'Multimedia'),
	(4, 'Rekayasa Perangkat Lunak'),
	(5, 'Teknik Komputer dan Jaringan');

-- Dumping structure for table bullying_db.kategori_laporan
CREATE TABLE IF NOT EXISTS `kategori_laporan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_kategori` (`nama_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.kategori_laporan: ~4 rows (approximately)
DELETE FROM `kategori_laporan`;
INSERT INTO `kategori_laporan` (`id`, `nama_kategori`) VALUES
	(1, 'Cyberbullying'),
	(2, 'Fisik'),
	(3, 'Sosial'),
	(4, 'Verbal');

-- Dumping structure for table bullying_db.kelas
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table bullying_db.laporan: ~4 rows (approximately)
DELETE FROM `laporan`;
ALTER TABLE laporan AUTO_INCREMENT = 1;

-- Dumping structure for table bullying_db.siswa
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
ALTER TABLE siswa AUTO_INCREMENT = 1;

-- Dumping structure for table bullying_db.users
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
ALTER TABLE users AUTO_INCREMENT = 1;
INSERT INTO `users` (`id`, `nis_nip`, `password`, `role`, `nama`, `foto_profile`, `created_at`, `updated_at`, `last_seen_at`) VALUES
	(1, '10001', '$2y$10$MuPVzxR2snV5hd2LAxPy0eTHyc/7hXkCNmtqNtlNCEzrnFBZ3grwC', 'admin', 'Admin Adimas', 'profile_68525d7627fd0.jpg', '2025-06-18 06:20:47', '2025-06-18 07:39:04', '2025-06-18 07:39:04'),

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
