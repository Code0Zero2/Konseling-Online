-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2025 at 02:46 PM
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
  `isi` longtext DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artikel`
--

INSERT INTO `artikel` (`artikel_id`, `dokter_id`, `judul`, `isi`, `tanggal`) VALUES
(1, 1, 'Cara Mengatasi Stres di Tempat Kerja', 'Manajemen waktu dan komunikasi yang baik dapat mengurangi stres kerja.', '2025-11-01 00:00:00'),
(2, 2, 'Manfaat Meditasi untuk Kesehatan Mental', 'Meditasi rutin membantu meningkatkan fokus dan ketenangan.', '2025-11-03 00:00:00'),
(3, 1, 'Mengenal Gejala Awal Stress dan Cara Mengelolanya', 'Stress merupakan respons alami tubuh terhadap tekanan atau tuntutan dari lingkungan. Gejala awal stress dapat bervariasi pada setiap individu, namun umumnya meliputi:\n\n• Perubahan pola tidur (insomnia atau terlalu banyak tidur)\n• Mudah marah atau tersinggung\n• Sulit berkonsentrasi\n• Perubahan nafsu makan\n• Sering merasa cemas atau khawatir\n• Mudah lelah dan kehilangan energi\n\nCara mengelola stress:\n1. Lakukan relaksasi dengan teknik pernapasan dalam\n2. Berolahraga secara teratur\n3. Menjaga pola tidur yang sehat\n4. Berbicara dengan orang terpercaya\n5. Mengatur waktu dan prioritas dengan baik\n\nJika gejala stress berlanjut lebih dari 2 minggu, disarankan untuk berkonsultasi dengan profesional kesehatan mental.', '2024-01-15 10:30:00'),
(4, 2, 'Pentingnya Self-Care untuk Kesehatan Mental', 'Self-care bukanlah tindakan egois, melainkan investasi penting untuk kesehatan mental Anda. Berikut adalah beberapa praktik self-care yang dapat dilakukan:\n\nJenis-jenis self-care:\n\nPhysical Self-Care:\n• Tidur 7-8 jam per hari\n• Konsumsi makanan bergizi seimbang\n• Olahraga minimal 30 menit, 3 kali seminggu\n• Melakukan pemeriksaan kesehatan rutin\n\nEmotional Self-Care:\n• Mengenali dan menerima emosi yang dirasakan\n• Menulis jurnal untuk mengekspresikan perasaan\n• Membatasi paparan berita negatif\n• Mempraktikkan self-compassion\n\nSocial Self-Care:\n• Menjaga hubungan sehat dengan keluarga dan teman\n• Menetapkan batasan dalam hubungan\n• Bergabung dengan komunitas yang positif\n• Meminta bantuan ketika diperlukan\n\nMental Self-Care:\n• Membaca buku inspiratif\n• Belajar keterampilan baru\n• Melakukan hobi yang menyenangkan\n• Meditasi atau mindfulness\n\nIngat, self-care adalah proses berkelanjutan yang harus disesuaikan dengan kebutuhan pribadi Anda.', '2024-01-18 14:45:00'),
(5, 2, 'apalah ini', 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Expedita fugiat, vero eaque accusantium eveniet dicta, minima tempora obcaecati veniam sed velit animi adipisci dolorem pariatur commodi impedit deleniti magni similique eum. Quasi aut, aliquam, placeat repudiandae perferendis veritatis alias deserunt cum praesentium eveniet tempora esse minima? Impedit, ratione molestias. Quasi eius error sunt perferendis illo praesentium libero est explicabo, doloremque voluptates tenetur, adipisci aspernatur, minus hic quaerat iusto dolores impedit ducimus deleniti itaque dolorum. Magni, nostrum aspernatur deleniti ratione ipsam, recusandae iusto ex, odio molestiae excepturi dolorum consequuntur! Perspiciatis quis, enim distinctio corporis nam vero. Quam amet totam voluptates nulla?Lorem ipsum dolor sit amet consectetur adipisicing elit. Expedita fugiat, vero eaque accusantium eveniet dicta, minima tempora obcaecati veniam sed velit animi adipisci dolorem pariatur commodi impedit deleniti magni similique eum. Quasi aut, aliquam, placeat repudiandae perferendis veritatis alias deserunt cum praesentium eveniet tempora esse minima? Impedit, ratione molestias. Quasi eius error sunt perferendis illo praesentium libero est explicabo, doloremque voluptates tenetur, adipisci aspernatur, minus hic quaerat iusto dolores impedit ducimus deleniti itaque dolorum. Magni, nostrum aspernatur deleniti ratione ipsam, recusandae iusto ex, odio molestiae excepturi dolorum consequuntur! Perspiciatis quis, enim distinctio corporis nam vero. Quam amet totam voluptates nulla?', '2024-01-18 14:45:00');

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
(8, 4, 2, 10, 'disetujui', 'Butuh bantuan dalam menangani burnout.'),
(9, 5, 1, 1, 'dibatalkan', ''),
(10, 5, 1, 9, 'dibatalkan', 'mumet lek'),
(11, 5, 2, 4, 'dibatalkan', 'awalnya saya coba coba'),
(12, 5, 2, 6, 'disetujui', 'nganging\r\n'),
(13, 4, 9, 13, 'menunggu', 'gak tau males pengen beli trek ');

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
(7, 9, '2025-11-18', '10:00:00', 'tersedia'),
(8, 9, '2025-11-18', '15:00:00', 'penuh'),
(9, 1, '2025-11-19', '11:00:00', 'tersedia'),
(10, 2, '2025-11-19', '13:30:00', 'tersedia'),
(11, 1, '2025-11-20', '09:00:00', 'tersedia'),
(12, 2, '2025-11-20', '14:00:00', 'tersedia'),
(13, 9, '2025-11-21', '10:30:00', 'tersedia'),
(14, 1, '2025-11-22', '08:00:00', 'tersedia'),
(15, 2, '2025-11-23', '13:00:00', 'tersedia');

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
  `role` enum('dokter','pasien') DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `nama`, `email`, `password`, `role`, `no_hp`) VALUES
(1, 'Dr. Budi Santoso', 'budi@konseling.com', '12345', 'dokter', NULL),
(2, 'Dr. Sinta Lestari', 'sinta@konseling.com', '12345', 'dokter', NULL),
(3, 'Andi Pratama', 'andi@gmail.com', '12345', 'pasien', NULL),
(4, 'Rina Ayu', 'rina@gmail.com', '12345', 'pasien', NULL),
(5, 'Teguh Hadi', 'teguh@gmail.com', '12345', 'pasien', NULL),
(6, 'saya', 'saya@gmail.com', '12345', 'pasien', '+6288822227777'),
(8, 'test', 'test@gmail.com', '12345678', 'dokter', '+6299933335555'),
(9, 'dr yanto', 'tyantogaming@gmail.com', '123', 'dokter', '+6285144442222');

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
  MODIFY `artikel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `jadwal_dokter`
--
ALTER TABLE `jadwal_dokter`
  MODIFY `jadwal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `rating_testimoni`
--
ALTER TABLE `rating_testimoni`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
