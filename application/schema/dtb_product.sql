-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Waktu pembuatan: 27. April 2013 jam 12:25
-- Versi Server: 5.0.51
-- Versi PHP: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cjm`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `dtb_product`
--

CREATE TABLE IF NOT EXISTS `dtb_product` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `product_no` varchar(10) default NULL,
  `article` varchar(10) default NULL,
  `project` varchar(50) default NULL,
  `singkatan` varchar(5) default NULL,
  `create_date` datetime default NULL,
  `update_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `dtb_product`
--

INSERT INTO `dtb_product` (`product_no`, `article`, `project`, `singkatan`) VALUES
('PR002', '873-4023', 'MORO', 'MOR23'),
('PR001', '874-4032', 'BOLD', 'BLD32'),
('PR003', '871-4012', 'JODY', 'JOD12'),
('PR004', '871-7172', 'ROMAWI', 'RMW72'),
('PR005', '871-4408', 'KAIRO', 'KRO08'),
('PR006', '871-6408', 'KAIRO', 'KRO08'),
('PR007', '871-4010', 'MORO', 'MOR10'),
('PR008', '874-6010', 'MORO', 'MOR10'),
('PR009', '873-6610', 'MORO', 'MOR10'),
('PR010', '873-4610', 'MORO', 'MOR10'),
('PR011', '871-4085', 'JUPHE', 'JPE85'),
('PR012', '871-6088', 'JUPHE', 'JPE88'),
('PR013', '874-6026', 'JAZZ', 'JAZ26'),
('PR014', '873-2023', 'MORO', 'MOR23'),
('PR015', '571-7069', 'CLAUDIA', 'CLA69'),
('PR016', '873-6098', 'FLEXFEET', 'FLX98'),
('PR017', '874-4529', 'JACKO', 'JKO29'),
('PR018', '874-6529', 'JACKO', 'JKO29'),
('PR019', '874-6028', 'JACKO', 'JKO28'),
('PR020', '874-4029', 'JACKO', 'JKO29'),
('PR021', '871-6809', 'FERDY', 'FDY09'),
('PR022', '874-6029', 'JACKO', 'JKO29'),
('PR023', '873-4813', 'KNIGHT', 'KNI13'),
('PR024', '874-4028', 'JACKO', 'JKO28'),
('PR025', '873-7613', 'KNIGHT', 'KNI13'),
('PR026', '873-6823', 'MORO', 'MOR23'),
('PR027', '874-6324', 'LAZZARO', 'LZR24'),
('PR028', '873-7098', 'FLEXFEET', 'FLX98'),
('PR029', '874-7090', 'KOMPAS', 'KOM90'),
('PR030', '871-9172', 'ROMAWI', 'RMW72'),
('PR031', '874-4062', 'LANDON', 'LAN62'),
('PR032', '874-4057', 'LANDON', 'LAN57'),
('PR033', '873-4530', 'MORO', 'MOR30'),
('PR034', '873-6530', 'MORO', 'MOR30'),
('PR035', '873-4508', 'MORO SPORE', 'MOR08'),
('PR036', '873-6508', 'MORO SPORE', 'MOR08'),
('PR037', '873-6024', 'NEW MORO', 'MOR24'),
('PR038', '874-6735', 'KOMPAS', 'KOM35'),
('PR039', '874-4735', 'KOMPAS', 'KOM35'),
('PR040', '873-4024', 'MORO', 'MOR24'),
('PR041', '874-6857', 'LANDON', 'LAN57'),
('PR042', '874-4857', 'LANDON', 'LAN57'),
('PR043', '871-6882', 'KENNETH', 'KEN82'),
('PR044', '874-6862', 'LANDON', 'LAN62'),
('PR045', '871-6082', 'KENNETH', 'KEN82'),
('PR046', '871-3082', 'KENNETH', 'KEN82'),
('PR047', '871-6560', 'LIZARD', 'LIZ60'),
('PR048', '871-4560', 'LIZARD', 'LIZ60'),
('PR049', '871-8091', 'KNIGHT', 'KNI91'),
('PR050', '871-4491', 'KNIGHT', 'KNI91'),
('PR051', '871-4591', 'KNIGHT', 'KNI91');
