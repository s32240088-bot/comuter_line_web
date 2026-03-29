-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 29 Mar 2026 pada 19.12
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `comuter_line`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `No_telepon` varchar(20) DEFAULT NULL,
  `Alamat` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` varchar(20) DEFAULT 'petugas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `No_telepon`, `Alamat`, `Email`, `nama_lengkap`, `role`) VALUES
(2, 'yanto', '123', NULL, NULL, NULL, 'yanto', 'petugas'),
(3, 'daniel', '123', '081282477927', NULL, 'danielnihbos@gmail.com', 'Daniel Ardiansyah', 'petugas'),
(4, 'ronaldo', '123', '081282477927', 'lisboa', 'ronaldo@gmail.com', 'cristiano ronaldo', 'petugas'),
(5, 'messi', '123', '2134124434', 'barcelona', 'messigoat@gmail.comm', 'lionel messi', 'petugas'),
(6, 'neymar ', 'neymar12', '081213141223', 'rie de janeiro', 'neymar@gmail.com', 'neymar jr', 'petugas');

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang_temuan`
--

CREATE TABLE `barang_temuan` (
  `id_barang` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `kategori` enum('Elektronik','Dokumen','Aksesoris','Lainnya','Pakaian') NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `status` enum('Tersedia','Diambil') DEFAULT 'Tersedia',
  `tgl_lapor` timestamp NOT NULL DEFAULT current_timestamp(),
  `dilaporkan_oleh` varchar(50) NOT NULL,
  `jenis_laporan` varchar(10) DEFAULT 'temuan' COMMENT 'temuan = barang ditemukan, hilang = barang hilang'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang_temuan`
--

INSERT INTO `barang_temuan` (`id_barang`, `nama_barang`, `kategori`, `lokasi`, `deskripsi`, `foto`, `username`, `status`, `tgl_lapor`, `dilaporkan_oleh`, `jenis_laporan`) VALUES
(14, 'kacamata', 'Aksesoris', 'jatake', 'hitam', 'IMG_1774589159_69c614e7dc4a3.jpg', NULL, 'Diambil', '2026-03-26 17:00:00', 'asa', 'temuan'),
(15, 'laptop loq', 'Elektronik', 'tanah abang', 'warna abu abu', 'IMG_1774589837_69c6178d39451.jpeg', NULL, 'Tersedia', '2026-03-26 17:00:00', 'ambon', 'temuan'),
(16, 'gitar', 'Lainnya', 'duri', 'warna hitam', 'IMG_1774590566_69c61a665fb01.jpeg', NULL, 'Diambil', '2026-03-26 17:00:00', 'ambon', 'temuan'),
(17, 'tas', 'Aksesoris', 'cisauk', 'LV warna hitam', 'IMG_1774610026_69c6666a5be9e.jpeg', NULL, 'Tersedia', '2026-03-26 17:00:00', 'asa', 'temuan'),
(19, 'raket padel', 'Lainnya', 'poris', 'warna hijau', 'IMG_1774610238_69c6673ecc3c3.jpg', NULL, 'Tersedia', '2026-03-26 17:00:00', 'asa', 'temuan'),
(20, 'bola basket', 'Lainnya', 'ancol', 'merah deket pintu masuk', 'IMG_1774610314_69c6678a3ddd2.jpeg', NULL, 'Tersedia', '2026-03-26 17:00:00', 'asa', 'temuan'),
(23, 'baju fred perry', 'Pakaian', 'parung panjang', 'biru', 'IMG_1774613532_69c6741c180f2.jpg', NULL, 'Tersedia', '2026-03-26 17:00:00', 'asa', 'temuan'),
(24, 'folder vendor', 'Dokumen', 'bekasi', 'sammpul coklat', 'IMG_1774614782_69c678fe60108.jpg', NULL, 'Tersedia', '2026-03-26 17:00:00', 'asa', 'temuan'),
(25, 'ps 5', 'Elektronik', 'duri', 'warna putih', 'IMG_1774627248_69c6a9b036226.jpeg', NULL, 'Diambil', '2026-03-26 17:00:00', 'asa', 'temuan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `klaim_barang`
--

CREATE TABLE `klaim_barang` (
  `id_klaim` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL COMMENT 'FK ke barang_temuan',
  `id_laporan` int(11) DEFAULT NULL COMMENT 'FK ke laporan_kehilangan (opsional)',
  `username_klaim` varchar(100) NOT NULL,
  `nama_pemilik` varchar(200) NOT NULL,
  `no_identitas` varchar(50) NOT NULL COMMENT 'KTP/SIM/Passport',
  `keterangan` text DEFAULT NULL,
  `bukti_foto` varchar(255) DEFAULT NULL,
  `status_klaim` varchar(30) DEFAULT 'Menunggu' COMMENT 'Menunggu / Diverifikasi / Ditolak / Diserahkan',
  `tgl_klaim` datetime DEFAULT current_timestamp(),
  `tgl_verifikasi` datetime DEFAULT NULL,
  `catatan_petugas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `klaim_barang`
--

INSERT INTO `klaim_barang` (`id_klaim`, `id_barang`, `id_laporan`, `username_klaim`, `nama_pemilik`, `no_identitas`, `keterangan`, `bukti_foto`, `status_klaim`, `tgl_klaim`, `tgl_verifikasi`, `catatan_petugas`) VALUES
(1, 14, 3, 'ambon', 'ambon', '324456789101112', 'hitam warnanya', 'BUKTI_1774590198_69c618f6136a7.jpeg', 'Diserahkan', '2026-03-27 12:43:18', '2026-03-27 12:44:35', 'gas terus'),
(2, 16, 4, 'asa', 'asa', '89668699666', 'warna hitam', 'BUKTI_1774590827_69c61b6b6e8c6.jpeg', 'Diserahkan', '2026-03-27 12:53:47', '2026-03-27 12:54:47', ''),
(3, 25, NULL, 'ambon', 'ambon', '12321414143432', 'warna putih', 'BUKTI_1774627351_69c6aa17e4e4a.jpeg', 'Diserahkan', '2026-03-27 23:02:31', '2026-03-27 23:03:07', 'nih jangan di ilangin lagi');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_kehilangan`
--

CREATE TABLE `laporan_kehilangan` (
  `id_laporan` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `nama_barang` varchar(200) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `lokasi` varchar(200) NOT NULL,
  `tgl_hilang` date NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Mencari' COMMENT 'Mencari / Diproses / Ditemukan',
  `tgl_lapor` datetime DEFAULT current_timestamp(),
  `id_barang_cocok` int(11) DEFAULT NULL COMMENT 'ID barang_temuan yang dicocokkan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `laporan_kehilangan`
--

INSERT INTO `laporan_kehilangan` (`id_laporan`, `username`, `nama_barang`, `kategori`, `lokasi`, `tgl_hilang`, `deskripsi`, `foto`, `status`, `tgl_lapor`, `id_barang_cocok`) VALUES
(1, 'ezra', 'hp rog phone 2', 'Elektronik', 'bojong gede', '2026-03-25', 'hp rog gaada paketannya mati total', 'HILANG_1774433979_69c3b6bb05052.jpg', 'Mencari', '2026-03-25 17:19:39', NULL),
(2, 'ambon', 'hp rog', 'Elektronik', 'bojong gede', '2026-03-25', 'mati total', '', 'Mencari', '2026-03-26 19:51:09', NULL),
(3, 'ambon', 'kacamata', 'Aksesoris', 'jatake', '2026-03-27', 'hitam', 'HILANG_1774589896_69c617c8f4001.jpg', 'Ditemukan', '2026-03-27 12:38:17', 14),
(4, 'asa', 'gitar', 'Lainnya', 'duri', '2026-03-27', 'warna hitam', 'HILANG_1774590640_69c61ab0ee7c8.jpeg', 'Ditemukan', '2026-03-27 12:50:40', 16);

-- --------------------------------------------------------

--
-- Struktur dari tabel `register_user`
--

CREATE TABLE `register_user` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `register_user`
--

INSERT INTO `register_user` (`id`, `nama`, `username`, `telepon`, `email`, `alamat`, `password`, `created_at`) VALUES
(1, 'Calvinsen Colin Farrell', 'nabilasyg', '0866666', 'www@gmail.com', 'jalan pengkor 2', '4444', '2026-03-14 09:35:23'),
(2, 'ezra purwoko', 'ezra', '0814156161', 'ezra@gmail.com', 'jalan condet no 323', '1111', '2026-03-19 07:26:09'),
(3, 'ambon_java', 'ambon', '0899999', 'ambon@gmail.com', 'jln permata', '123', '2026-03-26 12:46:37'),
(4, 'asa', 'asa', '0876543211', 'asa@gmail.com', 'korea seoul blok b', '123', '2026-03-27 05:14:15'),
(5, 'ester yohana', 'ester', '081324243432', 'ester@gmail.com', 'jalan merpati', 'ester34', '2026-03-29 16:36:42');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_klaim_detail`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_klaim_detail` (
`id_klaim` int(11)
,`username_klaim` varchar(100)
,`nama_pemilik` varchar(200)
,`no_identitas` varchar(50)
,`keterangan` text
,`bukti_foto` varchar(255)
,`status_klaim` varchar(30)
,`tgl_klaim` datetime
,`tgl_verifikasi` datetime
,`catatan_petugas` text
,`id_barang` int(11)
,`nama_barang` varchar(100)
,`kategori` enum('Elektronik','Dokumen','Aksesoris','Lainnya','Pakaian')
,`lokasi` varchar(100)
,`foto_barang` varchar(255)
,`tgl_lapor` timestamp
,`status_barang` enum('Tersedia','Diambil')
,`id_laporan` int(11)
,`nama_barang_hilang` varchar(200)
,`tgl_hilang` date
);

-- --------------------------------------------------------

--
-- Struktur untuk view `v_klaim_detail`
--
DROP TABLE IF EXISTS `v_klaim_detail`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_klaim_detail`  AS SELECT `k`.`id_klaim` AS `id_klaim`, `k`.`username_klaim` AS `username_klaim`, `k`.`nama_pemilik` AS `nama_pemilik`, `k`.`no_identitas` AS `no_identitas`, `k`.`keterangan` AS `keterangan`, `k`.`bukti_foto` AS `bukti_foto`, `k`.`status_klaim` AS `status_klaim`, `k`.`tgl_klaim` AS `tgl_klaim`, `k`.`tgl_verifikasi` AS `tgl_verifikasi`, `k`.`catatan_petugas` AS `catatan_petugas`, `b`.`id_barang` AS `id_barang`, `b`.`nama_barang` AS `nama_barang`, `b`.`kategori` AS `kategori`, `b`.`lokasi` AS `lokasi`, `b`.`foto` AS `foto_barang`, `b`.`tgl_lapor` AS `tgl_lapor`, `b`.`status` AS `status_barang`, `lk`.`id_laporan` AS `id_laporan`, `lk`.`nama_barang` AS `nama_barang_hilang`, `lk`.`tgl_hilang` AS `tgl_hilang` FROM ((`klaim_barang` `k` join `barang_temuan` `b` on(`k`.`id_barang` = `b`.`id_barang`)) left join `laporan_kehilangan` `lk` on(`k`.`id_laporan` = `lk`.`id_laporan`)) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `barang_temuan`
--
ALTER TABLE `barang_temuan`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `dilaporkan_oleh` (`dilaporkan_oleh`);

--
-- Indeks untuk tabel `klaim_barang`
--
ALTER TABLE `klaim_barang`
  ADD PRIMARY KEY (`id_klaim`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indeks untuk tabel `laporan_kehilangan`
--
ALTER TABLE `laporan_kehilangan`
  ADD PRIMARY KEY (`id_laporan`),
  ADD KEY `id_barang_cocok` (`id_barang_cocok`);

--
-- Indeks untuk tabel `register_user`
--
ALTER TABLE `register_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `barang_temuan`
--
ALTER TABLE `barang_temuan`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `klaim_barang`
--
ALTER TABLE `klaim_barang`
  MODIFY `id_klaim` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `laporan_kehilangan`
--
ALTER TABLE `laporan_kehilangan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `register_user`
--
ALTER TABLE `register_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `barang_temuan`
--
ALTER TABLE `barang_temuan`
  ADD CONSTRAINT `barang_temuan_ibfk_1` FOREIGN KEY (`dilaporkan_oleh`) REFERENCES `register_user` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `klaim_barang`
--
ALTER TABLE `klaim_barang`
  ADD CONSTRAINT `klaim_barang_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang_temuan` (`id_barang`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `laporan_kehilangan`
--
ALTER TABLE `laporan_kehilangan`
  ADD CONSTRAINT `laporan_kehilangan_ibfk_1` FOREIGN KEY (`id_barang_cocok`) REFERENCES `barang_temuan` (`id_barang`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
