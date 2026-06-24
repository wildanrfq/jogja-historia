-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 24, 2026 at 11:30 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jogja_historia`
--

-- --------------------------------------------------------

--
-- Table structure for table `acara`
--

CREATE TABLE `acara` (
  `id` int(11) NOT NULL,
  `id_tempat` int(11) DEFAULT NULL,
  `judul` varchar(255) NOT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `link_tiket` varchar(255) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `peran` enum('admin','user') DEFAULT 'user',
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama`, `email`, `kata_sandi`, `peran`, `dibuat_pada`) VALUES
(1, 'Administrator', 'admin@jogjahistoria.id', '$2y$10$0DeBvyLG1rrL9LD3aSTCuedgrcck.mCOyXqz6OfBgEVDzOMGpA1mm', 'admin', '2025-11-22 15:08:28');

-- --------------------------------------------------------

--
-- Table structure for table `rencana_perjalanan`
--

CREATE TABLE `rencana_perjalanan` (
  `id` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `urutan_tempat` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`urutan_tempat`)),
  `durasi_menit` int(11) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sumber`
--

CREATE TABLE `sumber` (
  `id` int(11) NOT NULL,
  `id_tempat` int(11) DEFAULT NULL,
  `tipe_sumber` varchar(50) DEFAULT NULL,
  `url` text DEFAULT NULL,
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tempat`
--

CREATE TABLE `tempat` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `deskripsi_singkat` text DEFAULT NULL,
  `deskripsi_lengkap` text DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `jam_buka` varchar(255) DEFAULT NULL,
  `info_tiket` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`info_tiket`)),
  `info_aksesibilitas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`info_aksesibilitas`)),
  `kontak` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `gambar` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gambar`)),
  `dibuat_oleh` int(11) DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tempat`
--

INSERT INTO `tempat` (`id`, `judul`, `slug`, `kategori`, `deskripsi_singkat`, `deskripsi_lengkap`, `alamat`, `latitude`, `longitude`, `jam_buka`, `info_tiket`, `info_aksesibilitas`, `kontak`, `website`, `gambar`, `dibuat_oleh`, `dibuat_pada`, `diperbarui_pada`) VALUES
(1, 'Keraton Yogyakarta', 'keraton-yogyakarta', 'Keraton', 'Istana resmi Kesultanan Yogyakarta yang masih berfungsi hingga kini sebagai pusat budaya Jawa.', 'Keraton Ngayogyakarta Hadiningrat atau Keraton Yogyakarta adalah istana resmi Kesultanan Ngayogyakarta Hadiningrat yang kini berlokasi di Kota Yogyakarta. Keraton ini didirikan oleh Sultan Hamengkubuwono I pada tahun 1755. Kompleks keraton ini merupakan pusat kebudayaan Jawa yang masih lestari hingga kini.', 'Jl. Rotowijayan No.1, Panembahan, Kraton, Kota Yogyakarta', -7.80510000, 110.36440000, '08:00-14:00 (Tutup Jumat)', '{\"dewasa\":15000,\"anak\":7500}', NULL, '', '', '[\"https:\\/\\/ratunyatravel.com\\/wp-content\\/uploads\\/2021\\/12\\/keraton-yogyakarta.jpg\",\"https:\\/\\/travelspromo.com\\/wp-content\\/uploads\\/2019\\/05\\/salah-satu-ruang-di-keraton-yogyakarta-Mujiman-Muji.jpg\",\"https:\\/\\/homestaydijogja.net\\/wp-content\\/uploads\\/2024\\/02\\/Wisata-Kraton-Jogja-3.jpg\",\"https:\\/\\/awsimages.detik.net.id\\/community\\/media\\/visual\\/2024\\/05\\/02\\/keraton-ngayogyakarta-hadiningrat-1_169.jpeg?w=1200\"]', 1, '2025-11-22 15:08:28', '2025-11-22 17:11:29'),
(2, 'Taman Sari Water Castle', 'taman-sari', 'Water Castle', 'Kompleks taman istana yang dulunya menjadi tempat rekreasi Sultan dan keluarganya.', 'Taman Sari atau Taman Sari Water Castle adalah kompleks taman dan kolam yang dibangun pada masa Sultan Hamengkubuwono I sebagai tempat peristirahatan Sultan. Kompleks ini memiliki kolam pemandian, lorong bawah tanah, dan masjid bawah tanah yang unik.', 'Jl. Taman, Patehan, Kraton, Kota Yogyakarta', -7.80990000, 110.35960000, '09:00-15:00', '{\"dewasa\": 15000, \"anak\": 8000}', NULL, NULL, NULL, '[\"https://dynamic-media-cdn.tripadvisor.com/media/photo-o/0b/42/6e/e2/img-20160513-wa0268-largejpg.jpg?w=900&h=500&s=1\"]', 1, '2025-11-22 15:08:28', '2025-11-22 15:18:39'),
(3, 'Museum Benteng Vredeburg', 'benteng-vredeburg', 'Museum', 'Benteng peninggalan Belanda yang kini menjadi museum sejarah perjuangan kemerdekaan Indonesia.', 'Benteng Vredeburg dibangun oleh Belanda pada tahun 1760. Kini benteng ini difungsikan sebagai Museum Khusus Perjuangan Nasional yang menyimpan berbagai koleksi tentang sejarah perjuangan Indonesia, khususnya di Yogyakarta.', 'Jl. Margo Mulyo No.6, Ngupasan, Gondomanan, Kota Yogyakarta', -7.79930000, 110.36610000, '08:00-16:00 (Tutup Senin)', '{\"dewasa\": 10000, \"anak\": 5000, \"pelajar\": 5000}', NULL, NULL, NULL, '[\"https://d6qyz3em3b312.cloudfront.net/upload/images/media/2020/08/13/shutterstock_1410364832.2048x1024.jpg\"]', 1, '2025-11-22 15:08:28', '2025-11-22 15:18:39'),
(4, 'Candi Prambanan', 'candi-prambanan', 'Candi', 'Kompleks candi Hindu terbesar di Indonesia yang dibangun pada abad ke-9.', 'Candi Prambanan adalah kompleks candi Hindu yang dibangun pada abad ke-9 Masehi. Candi ini dipersembahkan untuk Trimurti, tiga dewa utama Hindu yaitu Brahma, Wisnu, dan Siwa. Prambanan adalah situs Warisan Dunia UNESCO.', 'Jl. Raya Solo - Yogyakarta, Kranggan, Bokoharjo, Prambanan, Sleman', -7.75200000, 110.49150000, '06:00-17:00', '{\"dewasa_domestik\": 50000, \"anak_domestik\": 25000, \"asing\": 350000}', NULL, NULL, NULL, '[\"https://fatek.umsu.ac.id/wp-content/uploads/2023/06/Candi-Prambanan-Makna-Yang-Terkandung-di-Dalamnya.jpg\"]', 1, '2025-11-22 15:08:28', '2025-11-22 15:18:39'),
(5, 'Monumen Yogya Kembali', 'monjali', 'Monumen', 'Museum sejarah perjuangan kemerdekaan RI yang berbentuk kerucut menjulang.', 'Monumen Yogya Kembali (Monjali) didirikan untuk mengenang dan menghormati perjuangan rakyat Yogyakarta dalam mempertahankan kemerdekaan Indonesia. Museum ini diresmikan pada tahun 1989 dan berbentuk kerucut dengan tinggi 31,8 meter.', 'Jl. Ring Road Utara, Jongkang, Sariharjo, Ngaglik, Sleman', -7.75030000, 110.36770000, '08:00-16:00 (Tutup Senin)', '{\"dewasa\": 10000, \"anak\": 5000}', NULL, NULL, NULL, '[\"https://rentalmobiljogja.id/wp-content/uploads/2017/04/monumen-jogja-kembali-02-1080x810.jpg\"]', 1, '2025-11-22 15:08:28', '2026-06-24 21:24:13'),
(6, 'Museum Sonobudoyo', 'museum-sonobudoyo', 'Museum', 'Museum dengan koleksi kebudayaan Jawa terlengkap kedua setelah Museum Nasional Jakarta.', 'Museum Sonobudoyo menyimpan koleksi kebudayaan Jawa yang sangat lengkap, termasuk wayang, keris, topeng, arca, dan berbagai benda seni lainnya. Museum ini didirikan pada tahun 1935 oleh Java Instituut.', 'Jl. Pangurakan No.6, Ngupasan, Gondomanan, Kota Yogyakarta', -7.80280000, 110.36440000, '08:00-15:30 (Tutup Senin)', '{\"adult\": 10000, \"child\": 5000}', NULL, NULL, NULL, '[\"https://lp-cms-production.imgix.net/2019-06/5e667676eff4869feca15dbc8da5e099-sono-budoyo-museum.jpg\"]', 1, '2025-11-20 13:05:39', '2025-11-20 13:30:07'),
(7, 'Masjid Gedhe Kauman', 'masjid-gedhe-kauman', 'Religious Heritage', 'Masjid bersejarah yang dibangun bersamaan dengan berdirinya Keraton Yogyakarta.', 'Masjid Gedhe Kauman atau Masjid Agung Yogyakarta adalah masjid kesultanan yang dibangun pada tahun 1773. Masjid ini memiliki arsitektur tradisional Jawa dengan atap tumpang dan serambi yang luas.', 'Jl. Kauman, Kauman, Gondomanan, Kota Yogyakarta', -7.80500000, 110.36300000, '05:00-21:00', '{\"note\": \"Gratis, namun harus berpakaian sopan\"}', NULL, NULL, NULL, '[\"https://campatour.com/wp-content/uploads/2019/11/Masjid-Gedhe-Kauman.jpg\"]', 1, '2025-11-20 13:05:39', '2025-11-20 13:30:07'),
(8, 'Candi Sewu', 'candi-sewu', 'Candi', 'Kompleks candi Buddha terbesar kedua di Jawa Tengah setelah Borobudur.', 'Candi Sewu adalah kompleks candi Buddha yang dibangun pada abad ke-8. Nama \"Sewu\" berarti seribu dalam bahasa Jawa, mengacu pada legenda tentang jumlah candi di kompleks ini. Kompleks ini terdiri dari satu candi utama yang dikelilingi banyak candi perwara.', 'Bugisan, Prambanan, Kabupaten Klaten, Jawa Tengah', -7.74490000, 110.49470000, '06:00-17:00', '{\"adult\": 30000, \"child\": 15000}', NULL, NULL, NULL, '[\"https://www.bakpiamutiarajogja.com/wp-content/uploads/2017/01/candi-sewu.jpg\"]', 1, '2025-11-20 13:05:39', '2025-11-20 13:36:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acara`
--
ALTER TABLE `acara`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tempat` (`id_tempat`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `rencana_perjalanan`
--
ALTER TABLE `rencana_perjalanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `sumber`
--
ALTER TABLE `sumber`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tempat` (`id_tempat`);

--
-- Indexes for table `tempat`
--
ALTER TABLE `tempat`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acara`
--
ALTER TABLE `acara`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rencana_perjalanan`
--
ALTER TABLE `rencana_perjalanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sumber`
--
ALTER TABLE `sumber`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tempat`
--
ALTER TABLE `tempat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `acara`
--
ALTER TABLE `acara`
  ADD CONSTRAINT `acara_ibfk_1` FOREIGN KEY (`id_tempat`) REFERENCES `tempat` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rencana_perjalanan`
--
ALTER TABLE `rencana_perjalanan`
  ADD CONSTRAINT `rencana_perjalanan_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sumber`
--
ALTER TABLE `sumber`
  ADD CONSTRAINT `sumber_ibfk_1` FOREIGN KEY (`id_tempat`) REFERENCES `tempat` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tempat`
--
ALTER TABLE `tempat`
  ADD CONSTRAINT `tempat_ibfk_1` FOREIGN KEY (`dibuat_oleh`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
