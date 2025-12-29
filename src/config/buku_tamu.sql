-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2025 at 03:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `buku_tamu`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_admin` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_admin`, `created_at`) VALUES
(1, 'admin', '$2y$10$l8B0vH8VRiJQMOZdh2iynevALivD7Mk0YXrwLokVh0lW5nMHnIzVe', 'Admin Lawakfest', '2025-12-18 13:47:30');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(50) DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `harga`, `deskripsi`) VALUES
(1, 'VIP', 150000.00, 'Akses VIP dengan fasilitas khusus'),
(2, 'Reguler', 75000.00, 'Tiket umum untuk acara');

-- --------------------------------------------------------

--
-- Table structure for table `tamu`
--

CREATE TABLE `tamu` (
  `id` int(11) NOT NULL,
  `id_tamu` varchar(50) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `lokasi_acara` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('Reguler','VIP') DEFAULT 'Reguler',
  `kehadiran` varchar(20) DEFAULT 'Tidak Hadir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tamu`
--

INSERT INTO `tamu` (`id`, `id_tamu`, `nama`, `lokasi_acara`, `email`, `role`, `kehadiran`) VALUES
(171, 'TAMU1761285319906', 'pmahendra', 'Jogja', 'r322@gmail.com', 'VIP', 'Hadir'),
(181, 'TAMU1764677513970', 'sari22', 'Semarang', 'siapbosq9@gmail.com', 'VIP', 'Tidak Hadir'),
(182, 'TAMU1766067210177', 'Susila Utama', 'Semarang', 'maousaizen@gmail.com', 'VIP', 'Hadir'),
(184, 'TAMU1766280918976', 'Tegar', 'Semarang', 'ryanditata38@gmail.com', 'VIP', 'Hadir'),
(185, 'TAMU1766307996504', 'Pasha', 'Semarang', 'hasedekontol3@gmail.com', 'Reguler', 'Hadir'),
(186, 'TAMU1766407732844', 'Abel', 'Jakarta', 'bumdessubur2024@gmail.com', 'VIP', 'Hadir'),
(187, 'TAMU1766407975808', 'Nadhif', 'Semarang', 'nadhifbsl@gmail.com', 'VIP', 'Hadir'),
(189, 'TAMU1766414340896', 'Yuniati', 'Kudus', 'dwiastutiyuniati@gmail.com', 'VIP', 'Hadir'),
(190, 'TAMU1766414524599', 'Regi', 'Jogja', 'regu226@gmail.com', 'VIP', 'Hadir'),
(191, 'TAMU1766990115793', 'Icha', 'Jogja', 'danisya6406@gmail.com', 'VIP', 'Hadir'),
(192, 'TAMU1766990327707', 'Bisma Utama', 'Semarang', 'patiayam11@gmail.com', 'VIP', 'Hadir'),
(194, 'TAMU1766990384180', 'Okta', 'Semarang', '111202315150@mhs.dinus.ac.id', 'Reguler', 'Hadir'),
(195, 'TAMU1766990949133', 'andre', 'Jakarta', 'andrianahmadfr@gmail.com', 'Reguler', 'Hadir'),
(196, 'TAMU1766990978268', 'Irsya', 'Jogja', '111202315162@mhs.dinus.ac.id', 'VIP', 'Hadir'),
(197, 'TAMU1766991040823', 'Naufal', 'Semarang', '111202315175@mhs.dinus.ac.id', 'VIP', 'Hadir'),
(198, 'TAMU1766991283275', 'Vicky', 'Semarang', '111202315168@mhs.dinus.ac.id', 'VIP', 'Hadir'),
(199, 'TAMU1766991425838', 'Jeon', 'Semarang', '111202315139@mhs.dinus.ac.id', 'VIP', 'Hadir'),
(200, 'TAMU1767007640554', 'Laela', 'Semarang', 'laelaqdryhhhh@gmail.com', 'Reguler', 'Hadir'),
(201, 'TAMU1767007667541', 'Putri', 'Semarang', 'putriqoriamanda75@gmail.com', 'Reguler', 'Tidak Hadir'),
(202, 'TAMU1767007702631', 'Bella', 'Semarang', 'aysbellamy@gmail.com', 'Reguler', 'Hadir'),
(203, 'TAMU1767007740669', 'Syely', 'Semarang', 'syelyfebryana16229@gmail.com', 'Reguler', 'Hadir'),
(204, 'TAMU1767007761618', 'Agus Sigma', 'Semarang', 'agussigma969@gmail.com', 'Reguler', 'Hadir'),
(205, 'TAMU1767007799801', 'Firma', 'Semarang', 'firmansyahadammaulana@gmail.com', 'Reguler', 'Hadir'),
(206, 'TAMU1767007836789', 'Aditya', 'Semarang', '111202315170@mhs.dinus.ac.id', 'VIP', 'Hadir'),
(207, 'TAMU1767008013352', 'Muclis', 'Semarang', '111202315163@mhs.dinus.ac.id', 'VIP', 'Hadir'),
(208, 'TAMU1767015493843', 'Melfista', 'Semarang', 'teramelfista@gmail.com', 'Reguler', 'Tidak Hadir'),
(209, 'TAMU1767015547786', 'Hailu', 'Semarang', 'nhalo2313@gmail.com', 'Reguler', 'Tidak Hadir');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` varchar(20) NOT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `nama_pembeli` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status_pembayaran` varchar(50) DEFAULT 'Belum Bayar',
  `tanggal_transaksi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_produk`, `nama_pembeli`, `email`, `bukti_pembayaran`, `status_pembayaran`, `tanggal_transaksi`) VALUES
('TRX1759829901436', 2, 'sari22', 'ri2130@gmail.com', NULL, 'Lunas', '2025-10-07 16:38:21'),
('TRX1759829905867', 2, 'sari22', 'ri2130@gmail.com', NULL, 'Lunas', '2025-10-07 16:38:25'),
('TRX1759830043110', 2, 'Ariu', 'siapbosq9@gmail.com', NULL, 'Lunas', '2025-10-07 16:40:43'),
('TRX1760162128580', 2, 'pmahendra', 'koliu@gmail.com', NULL, 'Lunas', '2025-10-11 12:55:28'),
('TRX1760162203249', 1, 'putri', 'putri.l@example.com', NULL, 'Lunas', '2025-10-11 12:56:43'),
('TRX1761285264785', 2, 'Gogeta', 'jaman5@gmail.com', NULL, 'Lunas', '2025-10-24 12:54:24'),
('TRX1761285319928', 1, 'pmahendra', 'r322@gmail.com', NULL, 'Lunas', '2025-10-24 12:55:19'),
('TRX1761287163542', 1, 'Picolo', 'namek@gmail.com', NULL, 'Lunas', '2025-10-24 13:26:03'),
('TRX1761294982396', 2, 'dd', 'koliu@gmail.com', NULL, 'Lunas', '2025-10-24 15:36:22'),
('TRX1764603587474', 1, 'GaeBolg', 'koliu@gmail.com', NULL, 'Lunas', '2025-12-01 22:39:47'),
('TRX1764665656463', 2, 'pmahendra', 'bismarezi@gmail.com', NULL, 'Lunas', '2025-12-02 15:54:16'),
('TRX1764665919610', 1, 'sari22', 'bismarezi@gmail.com', NULL, 'Lunas', '2025-12-02 15:58:39'),
('TRX1764666151324', 1, 'sari22', 'bismarezi@gmail.com', NULL, 'Lunas', '2025-12-02 16:02:31'),
('TRX1764667266489', 1, 'GaeBolg', 'bismarezi@gmail.com', NULL, 'Lunas', '2025-12-02 16:21:06'),
('TRX1764667947928', 2, 'pmahendra', 'bismarezi@gmail.com', NULL, 'Lunas', '2025-12-02 16:32:27'),
('TRX1764668105755', 2, 'pmahendra', 'bismarezi@gmail.com', NULL, 'Lunas', '2025-12-02 16:35:05'),
('TRX1764677513227', 1, 'sari22', 'siapbosq9@gmail.com', NULL, 'Lunas', '2025-12-02 19:11:53'),
('TRX1766067210787', 1, 'Susila Utama', 'maousaizen@gmail.com', NULL, 'Lunas', '2025-12-18 21:13:30'),
('TRX1766280918290', 1, 'Tegar', 'ryanditata38@gmail.com', NULL, 'Lunas', '2025-12-21 08:35:18'),
('TRX1766307996458', 2, 'Pasha', 'hasedekontol3@gmail.com', NULL, 'Lunas', '2025-12-21 16:06:36'),
('TRX1766407732693', 1, 'Abel', 'bumdessubur2024@gmail.com', NULL, 'Lunas', '2025-12-22 19:48:52'),
('TRX1766407975562', 1, 'Nadhif', 'nadhifbsl@gmail.com', NULL, 'Lunas', '2025-12-22 19:52:55'),
('TRX1766413632749', 1, 'Yuniati', 'dwiastutiyuniati@gmail.com', NULL, 'Lunas', '2025-12-22 21:27:12'),
('TRX1766414340472', 1, 'Yuniati', 'dwiastutiyuniati@gmail.com', NULL, 'Lunas', '2025-12-22 21:39:00'),
('TRX1766414524480', 1, 'Danisya', 'regu226@gmail.com', NULL, 'Lunas', '2025-12-22 21:42:04'),
('TRX1766990115312', 1, 'Icha', 'danisya6406@gmail.com', NULL, 'Lunas', '2025-12-29 13:35:15'),
('TRX1766990327421', 1, 'Bisma Utama', 'patiayam11@gmail.com', NULL, 'Lunas', '2025-12-29 13:38:47'),
('TRX1766990341372', 2, 'pmahendra', 'bismarezi@gmail.com', NULL, 'Lunas', '2025-12-29 13:39:01'),
('TRX1766990384557', 2, 'Okta', '111202315150@mhs.dinus.ac.id', NULL, 'Lunas', '2025-12-29 13:39:44'),
('TRX1766990949952', 2, 'andre', 'andrianahmadfr@gmail.com', NULL, 'Lunas', '2025-12-29 13:49:09'),
('TRX1766990978482', 1, 'Irsya', '111202315162@mhs.dinus.ac.id', NULL, 'Lunas', '2025-12-29 13:49:38'),
('TRX1766991040994', 1, 'Naufal', '111202315175@mhs.dinus.ac.id', NULL, 'Lunas', '2025-12-29 13:50:40'),
('TRX1766991283768', 1, 'Vicky', '111202315168@mhs.dinus.ac.id', NULL, 'Lunas', '2025-12-29 13:54:43'),
('TRX1766991425282', 1, 'Jeon', '111202315139@mhs.dinus.ac.id', NULL, 'Lunas', '2025-12-29 13:57:05'),
('TRX1767007640256', 2, 'Laela', 'laelaqdryhhhh@gmail.com', NULL, 'Lunas', '2025-12-29 18:27:20'),
('TRX1767007667581', 2, 'Putri', 'putriqoriamanda75@gmail.com', NULL, 'Lunas', '2025-12-29 18:27:47'),
('TRX1767007702723', 2, 'Bella', 'aysbellamy@gmail.com', NULL, 'Lunas', '2025-12-29 18:28:22'),
('TRX1767007740540', 2, 'Syely', 'syelyfebryana16229@gmail.com', NULL, 'Lunas', '2025-12-29 18:29:00'),
('TRX1767007761164', 2, 'Agus Sigma', 'agussigma969@gmail.com', NULL, 'Lunas', '2025-12-29 18:29:21'),
('TRX1767007799882', 2, 'Firma', 'firmansyahadammaulana@gmail.com', NULL, 'Lunas', '2025-12-29 18:29:59'),
('TRX1767007836397', 1, 'Aditya', '111202315170@mhs.dinus.ac.id', NULL, 'Lunas', '2025-12-29 18:30:36'),
('TRX1767008013202', 1, 'Muclis', '111202315163@mhs.dinus.ac.id', NULL, 'Lunas', '2025-12-29 18:33:33'),
('TRX1767015493120', 2, 'Melfista', 'teramelfista@gmail.com', NULL, 'Lunas', '2025-12-29 20:38:13'),
('TRX1767015547340', 2, 'Hailu', 'nhalo2313@gmail.com', NULL, 'Lunas', '2025-12-29 20:39:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `tamu`
--
ALTER TABLE `tamu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_tamu` (`id_tamu`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_produk` (`id_produk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tamu`
--
ALTER TABLE `tamu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
