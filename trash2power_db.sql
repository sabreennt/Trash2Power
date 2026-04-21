-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Apr 2026 pada 08.34
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `trash2power_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_sampah`
--

CREATE TABLE `kategori_sampah` (
  `id_kategori` int(50) NOT NULL,
  `jenis_sampah` varchar(30) NOT NULL,
  `harga_sampah` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori_sampah`
--

INSERT INTO `kategori_sampah` (`id_kategori`, `jenis_sampah`, `harga_sampah`) VALUES
(1, 'Botol Plastik', 50.00),
(2, 'Kaleng Alumunium', 40.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `penukaran_saldo`
--

CREATE TABLE `penukaran_saldo` (
  `id_penukaran` int(11) NOT NULL,
  `id_warga` int(11) NOT NULL,
  `jenis_penukaran` varchar(50) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `nomor_tujuan` varchar(50) NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `status` enum('pending','berhasil','gagal') NOT NULL,
  `tgl_penukaran` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penukaran_saldo`
--

INSERT INTO `penukaran_saldo` (`id_penukaran`, `id_warga`, `jenis_penukaran`, `provider`, `nomor_tujuan`, `nominal`, `status`, `tgl_penukaran`) VALUES
(1, 1, 'ewallet', 'GoPay', '082199998896', 10.00, 'pending', '2026-04-20 03:28:18'),
(2, 1, 'ewallet', 'DANA', '089502511921', 10.00, 'pending', '2026-04-20 03:29:11'),
(3, 1, 'pulsa', 'Operator', '8912638939861', 100.00, 'pending', '2026-04-20 07:28:03'),
(4, 2, 'pulsa', 'Tri', '089502511921', 100.00, 'pending', '2026-04-20 07:33:29'),
(5, 2, 'listrik', '.', '87283727328173', 100.00, 'pending', '2026-04-20 07:35:32'),
(6, 2, 'ewallet', 'GoPay', '089502511921', 10.00, 'pending', '2026-04-20 07:35:53'),
(7, 2, 'ewallet', 'DANA', '089502511921', 10.00, 'pending', '2026-04-20 07:36:02'),
(8, 2, 'ewallet', 'OVO', '089502511921', 10.00, 'pending', '2026-04-20 07:36:11'),
(9, 2, 'ewallet', 'ShopeePay', '089502511921', 10.00, 'pending', '2026-04-20 07:36:21'),
(10, 2, 'pulsa', 'Tri', '089502511921', 100.00, 'pending', '2026-04-20 09:44:56'),
(11, 2, 'pulsa', 'Tri', '089502511921', 100.00, 'pending', '2026-04-20 09:54:50'),
(12, 2, 'pulsa', 'Tri', '089502511921', 100.00, 'pending', '2026-04-20 10:01:40'),
(13, 2, 'listrik', '.', '8912638939861', 100.00, 'pending', '2026-04-20 10:01:59'),
(14, 2, 'ewallet', 'GoPay', '089502511921', 10.00, 'pending', '2026-04-20 10:18:13'),
(15, 5, 'pulsa', 'Tri', '089502511921', 100.00, 'pending', '2026-04-21 04:53:59'),
(16, 5, 'listrik', '.', '8912638939861', 100.00, 'pending', '2026-04-21 05:00:50'),
(17, 5, 'ewallet', 'GoPay', '089502511921', 10.00, 'pending', '2026-04-21 05:01:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `setoran`
--

CREATE TABLE `setoran` (
  `id_setoran` int(11) NOT NULL,
  `id_warga` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `jumlah_sampah` int(11) NOT NULL,
  `hasil_pendapatan` decimal(15,2) NOT NULL,
  `tgl_penyetoran` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `setoran`
--

INSERT INTO `setoran` (`id_setoran`, `id_warga`, `id_kategori`, `jumlah_sampah`, `hasil_pendapatan`, `tgl_penyetoran`) VALUES
(1, 1, 1, 1, 50.00, '2026-04-19 08:58:34'),
(2, 1, 2, 1, 40.00, '2026-04-19 12:59:29'),
(3, 1, 2, 1, 40.00, '2026-04-19 13:09:50'),
(4, 2, 2, 1, 40.00, '2026-04-20 02:24:01'),
(5, 2, 2, 1, 40.00, '2026-04-20 03:30:45'),
(6, 2, 2, 1, 40.00, '2026-04-20 03:31:02'),
(7, 2, 1, 1, 50.00, '2026-04-20 05:30:28'),
(8, 2, 1, 1, 50.00, '2026-04-20 05:31:29'),
(9, 2, 1, 1, 50.00, '2026-04-20 06:25:11'),
(10, 2, 1, 1, 50.00, '2026-04-20 06:27:19'),
(11, 2, 1, 1, 50.00, '2026-04-20 06:53:45'),
(12, 2, 1, 1, 50.00, '2026-04-20 07:16:31'),
(13, 2, 1, 1, 50.00, '2026-04-20 07:17:27'),
(14, 2, 2, 1, 40.00, '2026-04-20 07:18:57'),
(15, 2, 1, 1, 50.00, '2026-04-20 07:20:18'),
(16, 2, 1, 1, 50.00, '2026-04-20 07:20:35'),
(17, 2, 1, 1, 50.00, '2026-04-20 09:22:39'),
(18, 2, 1, 1, 50.00, '2026-04-20 09:34:29'),
(19, 2, 1, 1, 50.00, '2026-04-20 10:08:13'),
(20, 2, 1, 1, 50.00, '2026-04-20 11:14:03'),
(21, 2, 1, 1, 50.00, '2026-04-20 14:10:13'),
(22, 2, 2, 1, 40.00, '2026-04-20 14:11:17'),
(23, 2, 1, 1, 50.00, '2026-04-20 14:19:51'),
(24, 2, 1, 1, 50.00, '2026-04-20 16:42:57'),
(25, 2, 1, 1, 50.00, '2026-04-20 17:28:16'),
(26, 2, 1, 1, 50.00, '2026-04-20 17:30:35'),
(27, 2, 1, 1, 50.00, '2026-04-20 17:31:21'),
(28, 2, 1, 1, 50.00, '2026-04-20 17:32:57'),
(29, 5, 1, 1, 50.00, '2026-04-21 04:16:56'),
(30, 6, 1, 1, 50.00, '2026-04-21 04:20:06'),
(31, 6, 1, 1, 50.00, '2026-04-21 04:21:12'),
(32, 6, 1, 1, 50.00, '2026-04-21 04:41:43'),
(33, 5, 1, 1, 50.00, '2026-04-21 05:06:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `gender` enum('pria','wanita') NOT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `role` enum('warga','admin') NOT NULL,
  `saldo` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `email`, `password`, `nama`, `no_hp`, `gender`, `foto_profil`, `role`, `saldo`) VALUES
(1, 'sabrinaimanih@gmail.com', '$2y$10$z/q6tuXZ4mq2Ap7841EBhu5OqDeZvyP1iXjPfURZEAff0p4uBlJ62', 'Sabrina Imani Husniadi', '087818243374', 'wanita', 'profile_1_1776573196.jpeg', 'warga', 220.00),
(5, 'hhaikall992@gmail.com', '$2y$10$3OezzXqgiFadHaxO2BD5Qu5h9zmDuoTelJhDr9yEGJasLHUBGmQCC', 'Husain Haikal ', '089502511921', 'pria', 'profile_5_1776747939.png', 'warga', 40.00),
(6, 'tatoleto50@gmail.com', '$2y$10$A5jWLmxbrwfI0wlc5R7Ma.zWN7W5V0P7olKG4fh/1atFveUj6w1kq', 'dewa', '', 'pria', NULL, 'warga', 0.00);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kategori_sampah`
--
ALTER TABLE `kategori_sampah`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `penukaran_saldo`
--
ALTER TABLE `penukaran_saldo`
  ADD PRIMARY KEY (`id_penukaran`),
  ADD KEY `fk_saldo_warga` (`id_warga`);

--
-- Indeks untuk tabel `setoran`
--
ALTER TABLE `setoran`
  ADD PRIMARY KEY (`id_setoran`),
  ADD KEY `fk_id_warga` (`id_warga`),
  ADD KEY `fk_id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `id_user` (`id_user`,`email`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kategori_sampah`
--
ALTER TABLE `kategori_sampah`
  MODIFY `id_kategori` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `penukaran_saldo`
--
ALTER TABLE `penukaran_saldo`
  MODIFY `id_penukaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `setoran`
--
ALTER TABLE `setoran`
  MODIFY `id_setoran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `penukaran_saldo`
--
ALTER TABLE `penukaran_saldo`
  ADD CONSTRAINT `fk_saldo_warga` FOREIGN KEY (`id_warga`) REFERENCES `penukaran_saldo` (`id_penukaran`);

--
-- Ketidakleluasaan untuk tabel `setoran`
--
ALTER TABLE `setoran`
  ADD CONSTRAINT `fk_id_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_sampah` (`id_kategori`),
  ADD CONSTRAINT `fk_id_warga` FOREIGN KEY (`id_warga`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
