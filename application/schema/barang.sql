-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Waktu pembuatan: 27. April 2013 jam 03:52
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
-- Struktur dari tabel `barang`
--

CREATE TABLE IF NOT EXISTS `barang` (
  `kode_barang` varchar(10) NOT NULL,
  `nama_barang` varchar(50) default NULL,
  `price_barang` int(11) default NULL,
  `update` date default NULL,
  PRIMARY KEY  (`kode_barang`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`kode_barang`, `nama_barang`, `price_barang`, `update`) VALUES
('30118', 'PVC NEW ROCK 2.5MM D.BROWN', 53200, NULL),
('34065', 'PVC ROCK E14 2.5MM D.BROWN', 49496, NULL),
('11864', 'SPLIT SUEDE BORNEO 0.9-1.0MM', 6804, NULL),
('12094', 'PATENT LEA SHINY 2.0-2.2 NRN', 23357, NULL),
('20135', 'INSOLE BRD. CELLEX 0.9MM 1X1.5', 7700, NULL),
('34066', 'PVC ROCK E14 2.5MM BLACK', 49496, NULL),
('77795', 'TPR ''BATA'' GARIS BLACK/RED CERAMIC', 375, '2012-05-27'),
('30125', 'PVC NEW ROCK 2.5MM BLACK', 53200, NULL),
('29019', 'EVA SOFT 5MM 110X190CM', 85000, NULL),
('33528', 'PVC HILEX 3.0MM E11 OLIVE', 48485, '2012-05-27'),
('12064', 'NUBUCK BLACK 6015 2.0-2.2MM', 23357, NULL),
('60114', 'WEBING JUTE 35MM BLACK', 3500, NULL),
('31020', 'TRD MESH BLK+2MM FOAM BLK', 12800, NULL),
('31164', 'TRD MESH BRN + 2MM FOAM BLK + TRC(J)', 16764, NULL),
('90191', 'MIDSOLE PU MORO BLACK R.8', 10500, NULL),
('11013', 'SPLIT SUEDE GREY 0.9-1.0MM', 7204, '2012-05-31'),
('11854', 'FG. NAPPA 2.0-2.2MM GREY', 21834, NULL),
('20121', 'JERSY BROWN + 2MM EVA + JERSY', 24150, '2012-05-27'),
('63823', 'TALI KUR COTT. RND (ALL COLOUR)', 100, NULL),
('63274', 'NYLON TAPE BIND. BROWN 11MM', 347, NULL),
('63271', 'NYLON TAPE BIND. 11MM BLACK', 450, NULL),
('60113', 'WEBING JUTE 35MM DARK BROWN', 3500, NULL),
('60075', 'NYLON THREAD 20 GREY P.027', 42500, NULL),
('60209', 'NYLON THREAD 20 BROWN 4017', 42500, NULL),
('12644', 'FG NAPPA L. BROWN 1.4-1.6MM', 21834, NULL),
('11503', 'SPLIT SUEDE BROWN 4025 0.9-1.0MM', 7204, '2012-08-04'),
('12024', 'NUBUCK BROWN 401 2.0-2.2MM', 23357, NULL),
('60237', 'NYLON THREAD NO.40 BEIGE 4049', 42500, NULL),
('61232', 'NYLON THREAD NO.40 NOCCIOLA 4054', 42500, NULL),
('61214', 'NYLON THREAD TKT20 R.4049 BEIGE', 42000, '2012-06-30'),
('96109', 'MIDSOLE PU JUPHE R.8', 4000, NULL),
('30049', 'PU AR.121 0.8MM BROWN', 31111, NULL),
('30075', 'PVC 0.7MM L.BROWN', 13704, NULL),
('22661', 'EVA 4MM BLACK', 9873, NULL),
('31064', 'PVC SILKY 0.7MM BROWN (RITA)', 14074, NULL),
('63493', 'QUICKLON 4" BLACK LOOP', 4950, NULL),
('63494', 'QUICKLON 4" BLACK HOOK', 4950, NULL),
('31678', 'PVC 0.7MM D.BROWN BACK/BLK 140', 13704, NULL),
('17054', 'PULL UP CRAZY HORSE BRN 2.0-2.2MM', 25896, NULL),
('50770', 'SHOE POLISH BROWN', 285000, NULL),
('71071', 'TPR VELCRO 4', 250, NULL),
('30192', 'MITICA TAUPE / BEIGE', 28148, NULL),
('70323', 'RIVET BRONZE DOBLE 6MM BRAS', 155, '2012-05-27'),
('11973', 'SPLIT SUEDE GREY 1.4MM', 10765, '2012-08-04'),
('12473', 'CRAZY HORSE BLACK (02) 2.0-2.2MM', 26344, '2012-05-27'),
('29982', 'JERSY BLACK + 2MM EVA + JERSY', 24150, '2012-05-27'),
('60378', 'LACES POLY RND 3MM BEIGE 60CM', 850, NULL),
('60152', 'NYLON THREAD 40 BROWN 4034', 43100, '2012-06-30'),
('34112', 'MITICA BLACK', 26667, NULL),
('60000', 'NYLON THREAD 40 BLACK', 43800, '2012-05-27'),
('60100', 'NYLON THREAD 60 BLACK 121', 42500, '2012-05-27'),
('61079', 'ELASTIC BLACK', 1800, NULL),
('11054', 'SPLIT SUEDE BLACK 1.6-1.8MM', 11170, NULL),
('31433', 'PU DE 14 - OLIVE 0.8MM', 38691, NULL),
('39010', 'PVC BOSTON 2.5MM BLACK', 52593, '2012-05-27'),
('31989', 'PVC 0.7MM BLACK BACK/BLK 69', 14815, '2012-05-27'),
('12244', 'NUBUCK MILLING 2.0-2.2MM BLACK', 26344, '2012-05-27'),
('18174', 'VEG. L. BLACK', 21326, NULL),
('18283', 'VEGETABLE LEATHER BROWN 2.8-3.0MM', 22581, NULL),
('37128', 'PU HB 102 OLIVE GREEN (CHINA)', 38691, '2012-05-27'),
('31028', 'PVC SILKY 0.7MM 918 POSTALK BEIGE', 14448, NULL),
('33527', 'PVC SILKY 0.7MM E10 OLIVE', 12119, NULL),
('20209', 'EVA 3MM BLACK 100X225', 8751, NULL),
('60170', 'MOCC. THREAD KHAKI | BEIGE NON WAXED', 125000, NULL),
('20014', 'PVC FIBER 2MM BROWN 90X90CM', 45000, NULL),
('12704', 'COW LINING D. BROWN 0.9-1.0MM', 14409, '2012-08-04'),
('13043', 'FG. SOFT NAPPA 1.4-1.6MM BLACK', 21834, NULL),
('15394', 'COW LINING BLACK', 14409, '2012-08-04'),
('70249', 'RIVET SMALL BRONZE', 50, NULL),
('12253', 'NUBUCK MILLING 2.0-2.2MM KHAKI', 26344, '2012-05-27'),
('60179', 'TALI KUR / LACE ROUND 3MM BEIGE', 800, '2012-05-27'),
('39011', 'PVC BOSTON 2.5MM OLIVE', 52593, '2012-05-27'),
('31706', 'PVC 0.7MM 913 D. GREEN', 10740, '2012-05-27'),
('30210', 'PVC SILKY 0.7MM BEIGE (OKINAWA)', 14478, '2012-05-27'),
('30232', 'REAL SUEDE 0.6MM BLACK', 34815, NULL),
('30774', 'MITICA TAN IBC', 28148, NULL),
('60194', 'NYLON THREAD 20 BLACK', 42500, NULL),
('17373', 'FG. NAPPA BLACK 1.6MM', 23118, NULL),
('70771', 'RIVET BRONZE SMALL 6MM', 50, NULL),
('60121', 'NYLON THREAD 40 (210D/3) BLACK BO.', 42500, NULL),
('20200', 'LATEX SPONGE NATURAL SMM 42"', 58000, NULL),
('60171', 'LACE ROUND 4MM BEIGE', 850, NULL),
('18173', 'VEGETABLE LEATHER BLACK 2.8-3.0MM', 22581, NULL),
('30773', 'REAL SUEDE BUCKSKIN 907', 34815, '2012-05-27'),
('16753', 'CRAZY HORSE KHAKI', 26344, NULL),
('30641', 'PVC HILEX 3.0MM E11 NAVY', 46464, '2012-05-27'),
('30240', 'MITICA ALEXANDRO BLACK', 22222, NULL),
('11193', 'SPLIT SUEDE BLACK 0.9-1.0MM', 7204, '2012-05-27'),
('11723', 'SPLIT SUEDE SAND 0.9-1.0 SOCK HF', 7527, '2012-05-27'),
('30018', 'MITIKA BLACK IBC', 26667, '2012-05-27'),
('11763', 'SPLIT SUEDE D.BRN 1.4MM', 10765, '2012-05-27'),
('12853', 'ACTION L. DARK BROWN', 12290, '2012-08-04'),
('30253', 'REAL SUEDE D. BROWN', 34815, '2012-08-04'),
('30017', 'MITICA D. BROWN', 28000, '2012-08-04'),
('30257', 'REAL SUEDE GREY GRANITE 179', 34815, '2012-08-04'),
('18213', 'SPLIT SUEDE DARK MOCCA 0.9-1.0 MM', 7205, '2012-08-04'),
('22263', 'LATEX SPONGE 3 1/2', 45000, '2012-08-04'),
('93003', 'MID SOLE KENNETH', 5000, '2012-08-04'),
('32191', 'PU ATLANTA 0.8 BLACK', 48148, '2012-08-04'),
('30233', 'REAL SUEDE 0.6 TAN', 34815, '2012-08-04'),
('32190', 'PU ATLANTA 0.8 TAN', 48148, '2012-08-04'),
('12663', 'FG NAPPA D. BROWN 1.4-1.6 MM', 23118, '2012-08-04'),
('13333', 'COW LINING KHAKI 0.9-1.0 MM', 14409, '2012-08-04'),
('11383', 'ACTION LEATHER BLACK 1.4-1.6MM', 12290, '2012-08-04'),
('60093', 'NYLON THREAD 11 BEIGE R.4049', 43000, '2012-08-04'),
('61494', 'NYLON THREAD TKT.60 BEIGE 4049', 43000, '2012-08-04');
