-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 23 Agu 2021 pada 03.04
-- Versi server: 10.4.14-MariaDB
-- Versi PHP: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pengadaan2021`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id_admin` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `token` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_admin`
--

INSERT INTO `tbl_admin` (`id_admin`, `nama`, `email`, `alamat`, `password`, `status`, `token`, `created_at`, `updated_at`) VALUES
(1, 'Admin Pengadaan', 'admin@gmail.com', 'Jalan Alamat No.2', 'eyJpdiI6ImxweThrS01IbXltVUIxUVkreGpJNkE9PSIsInZhbHVlIjoiMHlhaFl6MzZjMkYwRzhwSER2TUZOZz09IiwibWFjIjoiNWEyYmViZDA5YzAzZDQ1YjcwODExMjI3M2FmNGUyOTc2MTlkMWY5ZWZmM2VkNDgyZThmNGJhOGUwZjU2ODUzNCJ9', 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZF9BZG1pbiI6bnVsbH0.i_74V6WZIA5fdj-jZUm8vNqCgci2Ngc-gJbRM7EBTGk', '2021-08-01 20:15:51', '2021-08-18 19:11:32'),
(2, 'Mimin', 'mimin@gmail.com', 'Karawang', 'eyJpdiI6IkEyMlowWWtKNjFZUnBncVR4R0xuWmc9PSIsInZhbHVlIjoiXC94MERaUzdQakR2R0tSRGtjaGhmR1E9PSIsIm1hYyI6IjExZGEzYmRlNWQ0NTQwZWVhMGUwODY4ZTc4MGYzODk4MzYzN2IxZDZjMjFhMzY4NjRjNjM1NWI1NmZmMWVhZGMifQ==', 1, NULL, '2021-08-03 18:22:03', '2021-08-03 18:22:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_laporan`
--

CREATE TABLE `tbl_laporan` (
  `id_laporan` int(11) NOT NULL,
  `id_pengajuan` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `laporan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_laporan`
--

INSERT INTO `tbl_laporan` (`id_laporan`, `id_pengajuan`, `id_supplier`, `laporan`, `created_at`, `updated_at`) VALUES
(3, 1, 1, 'public/laporan/aN72h2o0Js8XqDXKW4OVPsSlcsIFzU3wv7dzSWef.pdf', '2021-08-15 19:27:39', '2021-08-15 19:27:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_pengadaan`
--

CREATE TABLE `tbl_pengadaan` (
  `id_pengadaan` int(11) NOT NULL,
  `nama_pengadaan` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar` text NOT NULL,
  `anggaran` double NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_pengadaan`
--

INSERT INTO `tbl_pengadaan` (`id_pengadaan`, `nama_pengadaan`, `deskripsi`, `gambar`, `anggaran`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Meja', 'https://res.cloudinary.com/ruparupa-com/image/upload/w_360,h_360,f_auto,q_auto/f_auto,q_auto:eco/v1624893096/Products/10437575_1.jpg', 'public/gambar/cgfXj4fNSMjph9SOVCKhLB5odEthCjCUS7M7rRYk.jpg', 20000000, 1, '2021-08-08 19:16:41', '2021-08-08 20:24:09'),
(5, 'Kursi', 'https://www.ikea.co.id/in/produk/ruang-kerja-kantor/kursi-kantor/markus-art-50261151?gclid=CjwKCAjwgb6IBhAREiwAgMYKRiA4HpbLaQeXHRAfMt__2CkLDKxAtbHpyJGa9ssnDrivcbOfBO3PdBoCu-AQAvD_BwE&gclsrc=aw.ds', 'public/gambar/gmk1ZJe0TjEspECjYWTKaGvMSDtEjq0rnb4KggPs.jpg', 15000000, 1, '2021-08-08 19:19:33', '2021-08-08 19:19:33'),
(6, 'Dispenser', 'https://www.ruparupa.com/kris-dispenser-air-table-top.html?gclid=CjwKCAjwgb6IBhAREiwAgMYKRrciM_W7KTCiQmYtCpMonJafvumnSXhBWuvgh4bnG7fdJj1C-EmRHxoCLG8QAvD_BwE', 'public/gambar/sI2vFDZTWX1BXNnmV1L3NE6ANctgt4gcAX9C1gRW.jpg', 1000000, 1, '2021-08-08 19:55:45', '2021-08-08 19:55:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_pengajuan`
--

CREATE TABLE `tbl_pengajuan` (
  `id_pengajuan` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `id_pengadaan` int(11) NOT NULL,
  `anggaran` double NOT NULL,
  `proposal` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_pengajuan`
--

INSERT INTO `tbl_pengajuan` (`id_pengajuan`, `id_supplier`, `id_pengadaan`, `anggaran`, `proposal`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 10000000, 'public/proposal/3QHMsKv3gDwK5cKXlXsA65Jruh1nVBSzFSw8jyEG.pdf', 2, '2021-08-08 22:27:05', '2021-08-11 18:33:42'),
(2, 1, 5, 12000000, 'public/proposal/QlJ4MFzPzvTJMOAiJNJj071ihgkLchoZZYQAwU7w.pdf', 1, '2021-08-09 00:45:09', '2021-08-09 19:02:44'),
(3, 1, 6, 900000, 'public/proposal/dckMjXaAXFDk6G5Hatbujltp0lUa4Lfm4rhpqdvh.pdf', 0, '2021-08-09 00:45:58', '2021-08-09 00:45:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_supplier`
--

CREATE TABLE `tbl_supplier` (
  `id_supplier` int(11) NOT NULL,
  `nama_usaha` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `no_npwp` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `token` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_supplier`
--

INSERT INTO `tbl_supplier` (`id_supplier`, `nama_usaha`, `email`, `alamat`, `no_npwp`, `password`, `status`, `token`, `created_at`, `updated_at`) VALUES
(1, 'PT. Angin Ribut', 'tasya@gmail.com', 'karawang', '123-232-1232-232', 'eyJpdiI6IkY0Z1Z6cDlnaUl0UHgyc09BdmVoYXc9PSIsInZhbHVlIjoiMXU4aVRpRXlnaFBIWURTK3llajRhQT09IiwibWFjIjoiMjdiOTVmMmIwNThjNzk2OGI4NDFhMDA2YWM3NWU2ZWMzZGQzMWI1YjczN2IxYjljNjM3ODAwZmIzNDgzYWEwZiJ9', 1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZF9zdXBwbGllciI6MX0.MG9cki8gVb5h-l0NQfQ-w_-xgRmGVM015Sat-K__uOU', '2021-08-19 01:42:02', '2021-08-18 18:42:02');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `tbl_laporan`
--
ALTER TABLE `tbl_laporan`
  ADD PRIMARY KEY (`id_laporan`);

--
-- Indeks untuk tabel `tbl_pengadaan`
--
ALTER TABLE `tbl_pengadaan`
  ADD PRIMARY KEY (`id_pengadaan`);

--
-- Indeks untuk tabel `tbl_pengajuan`
--
ALTER TABLE `tbl_pengajuan`
  ADD PRIMARY KEY (`id_pengajuan`);

--
-- Indeks untuk tabel `tbl_supplier`
--
ALTER TABLE `tbl_supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tbl_laporan`
--
ALTER TABLE `tbl_laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tbl_pengadaan`
--
ALTER TABLE `tbl_pengadaan`
  MODIFY `id_pengadaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tbl_pengajuan`
--
ALTER TABLE `tbl_pengajuan`
  MODIFY `id_pengajuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tbl_supplier`
--
ALTER TABLE `tbl_supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
