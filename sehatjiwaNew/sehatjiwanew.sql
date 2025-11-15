-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2025 at 05:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sehatjiwanew`
--

-- --------------------------------------------------------

--
-- Table structure for table `artikel`
--

CREATE TABLE `artikel` (
  `artikel_id` int(11) NOT NULL,
  `dokter_id` int(11) DEFAULT NULL,
  `judul` varchar(200) DEFAULT NULL,
  `isi` text DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artikel`
--

INSERT INTO `artikel` (`artikel_id`, `dokter_id`, `judul`, `isi`, `tanggal`) VALUES
(1, 1, 'Cara Mengatasi Stres di Tempat Kerja', 'Manajemen waktu dan komunikasi yang baik dapat mengurangi stres kerja.', '2025-11-01 00:00:00'),
(2, 2, 'Manfaat Meditasi untuk Kesehatan Mental', 'Meditasi rutin membantu meningkatkan fokus dan ketenangan.', '2025-11-03 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `pasien_id` int(11) DEFAULT NULL,
  `dokter_id` int(11) DEFAULT NULL,
  `jadwal_id` int(11) DEFAULT NULL,
  `status` enum('menunggu','disetujui','selesai','dibatalkan') DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `pasien_id`, `dokter_id`, `jadwal_id`, `status`, `catatan`) VALUES
(1, 3, 1, 1, 'menunggu', 'Pertama kali ingin mencoba konseling.'),
(2, 4, 1, 1, 'disetujui', 'Ingin konsultasi tentang stres kerja.'),
(3, 5, 1, 2, 'selesai', 'Konseling berjalan lancar.'),
(4, 4, 2, 4, 'dibatalkan', 'Pasien membatalkan karena bentrok jadwal.'),
(5, 4, 2, 5, 'disetujui', 'Ingin konsultasi tentang manajemen waktu.'),
(6, 4, 2, 6, 'menunggu', 'Ingin membahas permasalahan kecemasan.'),
(7, 4, 1, 9, 'selesai', 'Konseling sudah selesai dengan hasil baik.'),
(8, 4, 2, 10, 'menunggu', 'Butuh bantuan dalam menangani burnout.');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_dokter`
--

CREATE TABLE `jadwal_dokter` (
  `jadwal_id` int(11) NOT NULL,
  `dokter_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jam` time DEFAULT NULL,
  `status` enum('tersedia','penuh') DEFAULT 'tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal_dokter`
--

INSERT INTO `jadwal_dokter` (`jadwal_id`, `dokter_id`, `tanggal`, `jam`, `status`) VALUES
(1, 1, '2025-11-15', '09:00:00', 'tersedia'),
(2, 1, '2025-11-15', '13:00:00', 'tersedia'),
(3, 1, '2025-11-16', '10:00:00', 'penuh'),
(4, 2, '2025-11-15', '08:30:00', 'tersedia'),
(5, 2, '2025-11-16', '14:00:00', 'penuh'),
(6, 2, '2025-11-17', '09:00:00', 'tersedia'),
(7, 3, '2025-11-18', '10:00:00', 'tersedia'),
(8, 3, '2025-11-18', '15:00:00', 'penuh'),
(9, 1, '2025-11-19', '11:00:00', 'tersedia'),
(10, 2, '2025-11-19', '13:30:00', 'tersedia');

-- --------------------------------------------------------

--
-- Table structure for table `rating_testimoni`
--

CREATE TABLE `rating_testimoni` (
  `rating_id` int(11) NOT NULL,
  `pasien_id` int(11) DEFAULT NULL,
  `dokter_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `testimoni` text DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rating_testimoni`
--

INSERT INTO `rating_testimoni` (`rating_id`, `pasien_id`, `dokter_id`, `rating`, `testimoni`, `tanggal`) VALUES
(1, 3, 1, 5, 'Pelayanan sangat baik, penjelasan dokter jelas dan menenangkan.', '2025-11-10 09:30:00'),
(2, 4, 1, 4, 'Sesi berjalan lancar, tapi sedikit molor dari jadwal.', '2025-11-11 10:45:00'),
(3, 5, 1, 5, 'Dokter Budi sangat membantu saya mengatasi stres kerja.', '2025-11-12 15:20:00'),
(4, 3, 2, 4, 'Dokter Sinta sabar dan komunikatif, saya merasa lebih tenang.', '2025-11-13 08:50:00'),
(5, 4, 2, 5, 'Sesi konseling berjalan sangat baik, sangat direkomendasikan!', '2025-11-14 14:10:00'),
(6, 5, 2, 3, 'Cukup membantu, tapi waktu sesi terasa singkat.', '2025-11-15 11:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('dokter','pasien') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Dr. Budi Santoso', 'budi@konseling.com', '12345', 'dokter'),
(2, 'Dr. Sinta Lestari', 'sinta@konseling.com', '12345', 'dokter'),
(3, 'Andi Pratama', 'andi@gmail.com', '12345', 'pasien'),
(4, 'Rina Ayu', 'rina@gmail.com', '12345', 'pasien'),
(5, 'Teguh Hadi', 'teguh@gmail.com', '12345', 'pasien');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artikel`
--
ALTER TABLE `artikel`
  ADD PRIMARY KEY (`artikel_id`),
  ADD KEY `dokter_id` (`dokter_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `pasien_id` (`pasien_id`,`jadwal_id`),
  ADD KEY `dokter_id` (`dokter_id`),
  ADD KEY `jadwal_id` (`jadwal_id`);

--
-- Indexes for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  ADD PRIMARY KEY (`jadwal_id`),
  ADD KEY `dokter_id` (`dokter_id`);

--
-- Indexes for table `rating_testimoni`
--
ALTER TABLE `rating_testimoni`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `pasien_id` (`pasien_id`,`dokter_id`),
  ADD KEY `dokter_id` (`dokter_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artikel`
--
ALTER TABLE `artikel`
  MODIFY `artikel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  MODIFY `jadwal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rating_testimoni`
--
ALTER TABLE `rating_testimoni`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `artikel`
--
ALTER TABLE `artikel`
  ADD CONSTRAINT `artikel_ibfk_1` FOREIGN KEY (`dokter_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`pasien_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`dokter_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `booking_ibfk_3` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal_dokter` (`jadwal_id`);

--
-- Constraints for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  ADD CONSTRAINT `jadwal_dokter_ibfk_1` FOREIGN KEY (`dokter_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `rating_testimoni`
--
ALTER TABLE `rating_testimoni`
  ADD CONSTRAINT `rating_testimoni_ibfk_1` FOREIGN KEY (`pasien_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `rating_testimoni_ibfk_2` FOREIGN KEY (`dokter_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
