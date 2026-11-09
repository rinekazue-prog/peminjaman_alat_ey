-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql305.infinityfree.com
-- Waktu pembuatan: 09 Nov 2026 pada 09.39
-- Versi server: 11.4.13-MariaDB
-- Versi PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_43208907_peminjaman`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `alat`
--

CREATE TABLE `alat` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kategori_id` int(11) DEFAULT NULL,
  `stok` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `alat`
--

INSERT INTO `alat` (`id`, `nama`, `kategori_id`, `stok`) VALUES
(12, 'Infocus', 1, 5),
(13, 'Kamera DSLR Canon', 13, 3),
(14, 'Tripod Kamera', 13, 5),
(15, 'Speaker Portable', 13, 4),
(16, 'Mikrofon Wireless', 13, 6),
(17, 'Spidol Whiteboard', 9, 30),
(18, 'Penggaris Besi 50 cm', 9, 20),
(19, 'Busur dan Jangka Papan Tulis', 9, 8),
(20, 'Bor Listrik', 2, 4),
(21, 'Gergaji Tangan', 2, 6),
(22, 'Tang dan Obeng Set', 2, 10),
(23, 'Proyektor LCD', 1, 6),
(24, 'Laptop Sekolah', 1, 10),
(25, 'Kabel HDMI 5 Meter', 1, 12),
(26, 'Terminal Listrik (Stop Kontak)', 1, 15),
(27, 'Bola Basket', 14, 8),
(28, 'Bola Voli', 14, 8),
(29, 'Matras Senam', 14, 10),
(30, 'Mikroskop', 15, 10),
(31, 'Multimeter Digital', 15, 8),
(32, 'Gelas Ukur 100 ml', 15, 20),
(33, 'Sapu Lantai', 16, 10),
(34, 'Alat Pel', 16, 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `deskripsi`) VALUES
(1, 'Elektronik', 'elektronik'),
(2, 'Pertukangan', 'pertukangan'),
(9, 'Alat tulis', '-'),
(13, 'Audio & Dokumentasi', '-'),
(14, 'Olahraga', 'Perlengkapan olahraga dan pendidikan jasmani'),
(15, 'Laboratorium', 'Peralatan praktikum IPA dan komputer'),
(16, 'Kebersihan', 'Perlengkapan kebersihan dan perawatan kelas');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_aktifitas`
--

CREATE TABLE `log_aktifitas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `aksi` varchar(100) DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `log_aktifitas`
--

INSERT INTO `log_aktifitas` (`id`, `user_id`, `aksi`, `waktu`) VALUES
(1, 4, 'Mengembalikan alat', '2026-09-16 12:16:04'),
(2, 4, 'Mengembalikan alat', '2026-09-16 12:16:06'),
(3, 4, 'Mengembalikan alat', '2026-09-19 07:53:48'),
(4, 4, 'Ajukan peminjaman', '2026-09-21 01:25:11'),
(5, 4, 'Status berubah: pending -> disetujui', '2026-09-21 01:25:35'),
(6, 6, 'Ajukan peminjaman', '2026-09-21 07:25:43'),
(7, 6, 'Status berubah: pending -> disetujui', '2026-09-21 07:26:00'),
(8, 6, 'Kembalikan alat, denda: 0.00', '2026-09-21 07:26:36'),
(9, 6, 'Status berubah: disetujui -> selesai', '2026-09-21 07:26:36'),
(10, 6, 'Mengembalikan alat', '2026-09-21 07:26:37'),
(11, 6, 'Ajukan peminjaman', '2026-09-22 01:09:27'),
(12, 6, 'Status berubah: pending -> disetujui', '2026-09-22 01:09:40'),
(13, 6, 'Kembalikan alat, denda: 0.00', '2026-09-22 01:17:46'),
(14, 6, 'Status berubah: disetujui -> selesai', '2026-09-22 01:17:46'),
(15, 6, 'Mengembalikan alat', '2026-09-22 01:17:46'),
(16, 6, 'Ajukan peminjaman', '2026-09-22 01:41:15'),
(17, 6, 'Status berubah: pending -> disetujui', '2026-09-22 01:41:36'),
(18, 6, 'Ajukan peminjaman', '2026-09-23 07:08:43'),
(19, 6, 'Kembalikan alat, denda: 5000.00', '2026-09-23 07:08:58'),
(20, 6, 'Status berubah: disetujui -> selesai', '2026-09-23 07:08:58'),
(21, 6, 'Mengembalikan alat', '2026-09-23 07:08:58'),
(22, 6, 'Ajukan peminjaman', '2026-09-23 07:52:16'),
(23, 6, 'Status berubah: pending -> disetujui', '2026-09-23 07:52:34'),
(24, 6, 'Kembalikan alat, denda: 5000.00', '2026-09-25 03:44:10'),
(25, 6, 'Status berubah: disetujui -> selesai', '2026-09-25 03:44:10'),
(26, 6, 'Mengembalikan alat', '2026-09-25 03:44:10'),
(27, 6, 'Ajukan peminjaman', '2026-09-25 06:19:35'),
(28, 6, 'Status berubah: pending -> disetujui', '2026-09-25 06:19:53'),
(29, 4, 'Ajukan peminjaman', '2026-09-25 06:22:13'),
(30, 6, 'Status berubah: pending -> disetujui', '2026-09-25 06:22:58'),
(31, 6, 'Kembalikan alat, denda: 0.00', '2026-09-25 06:23:25'),
(32, 6, 'Status berubah: disetujui -> selesai', '2026-09-25 06:23:25'),
(33, 6, 'Mengembalikan alat', '2026-09-25 06:23:25'),
(34, 6, 'Kembalikan alat, denda: 10000.00', '2026-09-25 06:32:57'),
(35, 6, 'Status berubah: disetujui -> selesai', '2026-09-25 06:32:57'),
(36, 5, 'Menerima pengembalian alat \'Artileri\' dari Kim Jong Un, denda: Rp 10.000', '2026-09-25 06:32:57'),
(37, 4, 'Kembalikan alat, denda: 15000.00', '2026-09-25 06:32:59'),
(38, 4, 'Status berubah: disetujui -> selesai', '2026-09-25 06:32:59'),
(39, 5, 'Menerima pengembalian alat \'Lenovo Legion Pro I7\' dari Pemuda PP, denda: Rp 15.000', '2026-09-25 06:32:59'),
(40, 4, 'Status berubah: pending -> disetujui', '2026-09-25 12:26:02'),
(41, 4, 'Kembalikan alat, denda: 0.00', '2026-09-25 12:26:09'),
(42, 4, 'Status berubah: disetujui -> selesai', '2026-09-25 12:26:09'),
(43, 5, 'Menerima pengembalian alat \'Lenovo Legion Pro I7\' dari Pemuda PP', '2026-09-25 12:26:09'),
(44, 6, 'Ajukan peminjaman', '2026-09-30 02:48:14'),
(45, 6, 'Status berubah: pending -> disetujui', '2026-09-30 02:48:38'),
(46, 6, 'Status berubah: disetujui -> menunggu_konfirmasi', '2026-09-30 02:49:05'),
(47, 6, 'Kembalikan alat, denda: 0.00', '2026-09-30 02:49:38'),
(48, 6, 'Status berubah: menunggu_konfirmasi -> selesai', '2026-09-30 02:49:38'),
(49, 5, 'Mengonfirmasi penerimaan alat \'Snowman Permanent Marker G-12\' dari Kim Jong Un', '2026-09-30 02:49:38'),
(50, 6, 'Ajukan peminjaman', '2026-09-30 05:01:14'),
(51, 6, 'Status berubah: pending -> disetujui', '2026-09-30 05:01:29'),
(52, 6, 'Ajukan peminjaman', '2026-10-05 03:18:23'),
(53, 6, 'Status berubah: pending -> disetujui', '2026-10-05 03:19:13'),
(54, 6, 'Status berubah: disetujui -> menunggu_konfirmasi', '2026-10-05 03:21:34'),
(55, 6, 'Status berubah: disetujui -> menunggu_konfirmasi', '2026-10-05 03:21:38'),
(56, 5, 'Mengonfirmasi penerimaan alat \'Lenovo Legion Pro I7\' dari Kim Jong Un, denda: Rp 20.000', '2026-10-18 15:54:42'),
(57, 5, 'Mengonfirmasi penerimaan alat \'Snowman Permanent Marker G-12\' dari Kim Jong Un', '2026-10-18 15:58:52'),
(58, 5, 'Mengonfirmasi penerimaan alat \'Nuklir\' dari aha', '2026-10-19 01:56:00'),
(59, 5, 'Mengonfirmasi penerimaan alat \'Artileri\' dari Kim Jong Un, denda: Rp 55.000', '2026-10-31 03:45:08'),
(60, 5, 'Mengonfirmasi penerimaan alat \'Artileri\' dari Kim Jong Un, denda: Rp 45.000', '2026-10-31 03:45:10'),
(61, 5, 'Mengonfirmasi penerimaan alat \'Infocus\' dari Kim Jong Un', '2026-11-09 10:47:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `peminjam_id` int(11) DEFAULT NULL,
  `alat_id` int(11) DEFAULT NULL,
  `tgl_pinjam` date NOT NULL,
  `tgl_jatuh_tempo` date DEFAULT NULL,
  `tgl_ajukan_kembali` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `denda` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `peminjam_id`, `alat_id`, `tgl_pinjam`, `tgl_jatuh_tempo`, `tgl_ajukan_kembali`, `status`, `denda`) VALUES
(21, 6, 12, '2026-11-09', '2026-12-04', '2026-11-09', 'selesai', '0.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id` int(11) NOT NULL,
  `peminjaman_id` int(11) DEFAULT NULL,
  `tgl_kembali` date NOT NULL,
  `denda` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengembalian`
--

INSERT INTO `pengembalian` (`id`, `peminjaman_id`, `tgl_kembali`, `denda`) VALUES
(18, 21, '2026-11-09', '0.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT 'peminjam'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(3, 'admin', '$2y$12$GTXPZ67xzr1dmzcqCyvwZenyZD6BVg9zlC0grlTCFli2qZ.aYjIWy', 'admin'),
(4, 'Pemuda PP', '$2y$10$KaBGP5cpsuhNce4Td67nnOkzpbx.7Ttl0uaDZt/GJ3FJCxKWaqXPm', 'peminjam'),
(5, 'petugas', '$2y$12$uEOCGKh/niU4OcBRlapqGeU1102/oQuX8cTeT70CKCpWVzkxB1rRe', 'petugas'),
(6, 'Kim Jong Un', '$2y$10$j.RH9o7c/OKl5jtRmEB.4utTQjRVJs1O9CFb2Bpv4/5mqWarq2IbW', 'peminjam'),
(8, 'Abdul', '$2y$10$yGT8xRqD8GSfu5j8WQTxo.e0jybfmPDKST0b.86T1YUintOLp/QZq', 'peminjam'),
(9, 'Ellen raijn', '$2y$12$uV7HhKazouFibn.r51uBlOHqoVCD76YnPu.FN.WL5zEizO645eOay', 'peminjam'),
(10, 'aha', '$2y$12$IKeU1aOQZr/o2Em5VQbTwOlM17oVdNgTpgIgpHsTsNKBUsqcUX3nC', 'peminjam'),
(11, 'Hazel', '$2y$12$4hjN008G8agyvRdaEzBCFOo6Oymid9MWeOsO.1Cmh.UtXQHQLjuRW', 'peminjam');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `log_aktifitas`
--
ALTER TABLE `log_aktifitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_user` (`user_id`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_peminjaman_user` (`peminjam_id`),
  ADD KEY `fk_peminjaman_alat` (`alat_id`);

--
-- Indeks untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pengembalian_peminjaman` (`peminjaman_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `alat`
--
ALTER TABLE `alat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `log_aktifitas`
--
ALTER TABLE `log_aktifitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `alat`
--
ALTER TABLE `alat`
  ADD CONSTRAINT `alat_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`);

--
-- Ketidakleluasaan untuk tabel `log_aktifitas`
--
ALTER TABLE `log_aktifitas`
  ADD CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `log_aktifitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `fk_peminjaman_alat` FOREIGN KEY (`alat_id`) REFERENCES `alat` (`id`),
  ADD CONSTRAINT `fk_peminjaman_user` FOREIGN KEY (`peminjam_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`peminjam_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`alat_id`) REFERENCES `alat` (`id`);

--
-- Ketidakleluasaan untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `fk_pengembalian_peminjaman` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`),
  ADD CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
