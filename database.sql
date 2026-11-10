CREATE DATABASE IF NOT EXISTS `spacenest_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `spacenest_db`;

-- Tabel Users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('pelapor', 'admin') NOT NULL DEFAULT 'pelapor',
  `prodi` VARCHAR(50) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel Reports (Laporan Kerusakan)
CREATE TABLE IF NOT EXISTS `reports` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `facility_name` VARCHAR(150) NOT NULL,
  `category` ENUM('Hardware/Lab', 'Jaringan/Internet', 'Mebel/Ruangan', 'Lainnya') NOT NULL,
  `priority` ENUM('Rendah', 'Sedang', 'Tinggi') NOT NULL DEFAULT 'Sedang',
  `location` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('Dikirim', 'Diverifikasi', 'Diproses', 'Selesai') NOT NULL DEFAULT 'Dikirim',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabel Status Logs (Riwayat Perubahan Status - US-10)
CREATE TABLE IF NOT EXISTS `status_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `report_id` INT NOT NULL,
  `status` ENUM('Dikirim', 'Diverifikasi', 'Diproses', 'Selesai') NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`report_id`) REFERENCES `reports`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Data Awal (Akun Sample)
-- Password untuk kedua akun ini adalah: password123
INSERT IGNORE INTO `users` (`name`, `email`, `password`, `role`, `prodi`) VALUES
('Mahasiswa FTST', 'mahasiswa@unisma.ac.id', '$2y$10$8sZYyZU8A8qXPO/suv2KZ.Asou.k1mYCu.MWWi9VWm2LMvq.Jql36', 'pelapor', 'Teknik Informatika'),
('Admin FTST', 'admin.ftst@unisma.ac.id', '$2y$10$8sZYyZU8A8qXPO/suv2KZ.Asou.k1mYCu.MWWi9VWm2LMvq.Jql36', 'admin', NULL)
ON DUPLICATE KEY UPDATE
  `password` = IF(
    `password` = '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1q9A1a9t9b3mGfW1g7hJ8kL2mN3oP4q',
    VALUES(`password`),
    `password`
  );