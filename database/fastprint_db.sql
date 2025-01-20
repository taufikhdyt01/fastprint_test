-- phpMyAdmin SQL Dump
-- version 5.2.1
-- Sistem manajemen produk FastPrint

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `fastprint_db`
--
CREATE DATABASE IF NOT EXISTS `fastprint_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `fastprint_db`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `status`
--

CREATE TABLE `status` (
  `id_status` int(11) NOT NULL AUTO_INCREMENT,
  `nama_status` varchar(100) NOT NULL,
  PRIMARY KEY (`id_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `status`
--

INSERT INTO `status` (`id_status`, `nama_status`) VALUES
(1, 'bisa dijual'),
(2, 'tidak bisa dijual');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id_produk` varchar(100) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  PRIMARY KEY (`id_produk`),
  KEY `kategori_id` (`kategori_id`),
  KEY `status_id` (`status_id`),
  CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id_kategori`),
  CONSTRAINT `produk_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `status` (`id_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;