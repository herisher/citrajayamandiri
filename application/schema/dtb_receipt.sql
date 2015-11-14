-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Waktu pembuatan: 27. April 2013 jam 15:27
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
-- Struktur dari tabel `dtb_receipt`
--

CREATE TABLE IF NOT EXISTS `dtb_receipt` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `tt_no` char(5) default NULL,
  `receipt_no` varchar(20) default NULL,
  `total` int(11) default 0,
  `tt_tgl` date default NULL,
  `receipt_date` date default NULL,
  `create_date` datetime default NULL,
  `update_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `dtb_receipt`
--

INSERT INTO `dtb_receipt` (`tt_no`, `tt_tgl`) VALUES
('TT001', '2011-08-08'),
('TT002', '2011-10-10'),
('TT003', '2011-11-25'),
('TT004', '2012-01-09'),
('TT005', '2012-02-03'),
('TT006', '2012-02-10'),
('TT007', '2012-02-17'),
('TT008', '2012-03-20'),
('TT009', '2012-04-02'),
('TT010', '2012-04-03'),
('TT011', '2012-04-23'),
('TT012', '2012-05-10'),
('TT013', '2012-05-21'),
('TT014', '2012-06-15'),
('TT015', '2012-06-29'),
('TT017', '2012-07-05'),
('TT016', '2012-05-30'),
('TT018', '2012-07-22'),
('TT019', '2012-07-23'),
('TT020', '2012-07-25'),
('TT021', '2012-07-26'),
('TT022', '2012-07-27'),
('TT023', '2012-07-30'),
('TT024', '2012-08-01'),
('TT025', '2012-09-02'),
('TT026', '2012-09-06'),
('TT027', '2012-09-08'),
('TT028', '2012-09-11'),
('TT029', '2012-10-01'),
('TT030', '2012-10-02'),
('TT031', '2012-10-03'),
('TT032', '2012-10-10'),
('TT033', '2012-11-01'),
('TT034', '2012-11-02'),
('TT035', '2012-11-12'),
('TT036', '2012-12-05'),
('TT037', '2012-12-14'),
('TT038', '2012-12-15'),
('TT039', '2012-12-16'),
('TT040', '2013-01-07'),
('TT041', '2013-01-10'),
('TT042', '2013-01-11'),
('TT043', '2013-01-12'),
('TT044', '2013-01-13'),
('TT045', '2013-02-20'),
('TT046', '2013-02-21'),
('TT047', '2013-02-22'),
('TT048', '2013-02-23'),
('TT049', '2013-02-24'),
('TT050', '2013-03-01'),
('TT051', '2013-03-02'),
('TT052', '2013-03-03'),
('TT053', '2013-03-04'),
('TT054', '2013-03-05'),
('TT055', '2013-04-01'),
('TT056', '2013-04-02'),
('TT057', '2013-04-01'),
('TT058', '2013-04-01'),
('TT059', '2013-04-14'),
('TT060', '2013-04-15');
