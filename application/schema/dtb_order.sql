-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Waktu pembuatan: 27. April 2013 jam 12:42
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
-- Struktur dari tabel `dtb_order`
--

CREATE TABLE IF NOT EXISTS `dtb_order` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `product_id` bigint(20) unsigned NOT NULL,
  `po_no` char(6) NOT NULL,
  `product_no` varchar(10) NOT NULL,
  `order_date` date NOT NULL,
  `price` int(11) default NULL,
  `qty` varchar(10) default NULL,
  `quantity` int(11) default NULL,
  `payment` int(11) default NULL,
  `payment_flag` tinyint(4) default 0,
  `plan` bigint(10) default NULL,
  `keterangan` text,
  `description` text,
  `tanggal_terima` date default NULL,
  `receipt_date` date default NULL,
  `week_kirim` varchar(10) default NULL,
  `delivery_week` bigint(10) default NULL,
  `dept` varchar(5) default NULL,
  `status` varchar(20) default 'Progress',
  `status_flag` tinyint(4) default 0,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `dtb_order`
--

INSERT INTO `dtb_order` (`po_no`, `product_no`, `order_date`, `price`, `qty`, `payment`, `plan`, `keterangan`, `tanggal_terima`, `week_kirim`, `dept`, `status`) VALUES
('11999', 'PR004', '2011-06-27', '8300', '0', 2, '1385', '', '2011-07-07', '38', '426', 'Finish'),
('11663', 'PR002', '2011-06-17', '14400', '0', 2, '1333', '', '2011-06-29', '33', '424', 'Finish'),
('11967', 'PR002', '2011-06-25', '14400', '0', 2, '1341', '', '2011-07-07', '34', '427', 'Finish'),
('22119', 'PR015', '2012-04-09', '7000', '', 2, '2223', '=== BATAL ===', '2012-04-12', '22/2012', '423', 'Progress'),
('12443', 'PR002', '2011-07-11', '14400', '0', 2, '1391', '', '2011-09-29', '39', '427', 'Finish'),
('11426', 'PR001', '2011-06-06', '28600', '0', 2, '1291', '\r\n', '2011-06-09', '29', '428', 'Finish'),
('12441', 'PR002', '2011-07-11', '14400', '0', 2, '1391', '', '2011-07-15', '39', '427', 'Finish'),
('12514', 'PR005', '2011-07-12', '12000', '0', 2, '1341', '', '2011-07-15', '34', '424', 'Finish'),
('12515', 'PR006', '2011-07-12', '12000', '0', 2, '1341', '', '2011-07-15', '34', '424', 'Finish'),
('13379', 'PR004', '2011-08-02', '8300', '0', 2, '1422', '', '2011-08-04', '42', '427', 'Finish'),
('11544', 'PR003', '2011-06-14', '12510', '0', 2, '1321', '5=120; 6=220; 7=320; 8=220; 9=120\r\n=====BATAL=====', '2011-06-17', '32', '426', 'Progress'),
('11603', 'PR007', '2011-06-15', '44000', '0', 2, '1311', '', '2011-06-17', '31', '425', 'Finish'),
('11604', 'PR008', '2011-06-15', '44000', '0', 2, '1311', '', '2011-06-17', '31', '425', 'Finish'),
('12734', 'PR009', '2011-07-18', '44000', '0', 2, '1402', '<== export south africa ==>', '2011-07-20', '40', '426', 'Finish'),
('12733', 'PR010', '2011-07-18', '44000', '0', 2, '1402', '<== export south africa ==>', '2011-07-22', '40', '426', 'Finish'),
('11427', 'PR001', '2011-06-06', '28600', '0', 2, '1292', '', '2011-06-09', '29', '427', 'Finish'),
('11432', 'PR001', '2011-06-06', '28600', '0', 2, '1290', '', '2011-06-09', '29', '428', 'Finish'),
('11675', 'PR011', '2011-06-17', '31500', '0', 2, '1335', '', '2011-06-24', '33', '425', 'Finish'),
('11676', 'PR012', '2011-06-17', '32500', '0', 2, '1335', '', '2011-06-24', '33', '425', 'Finish'),
('11991', 'PR002', '2011-06-27', '14400', '0', 2, '1383', '', '2011-09-29', '38', '425', 'Finish'),
('11990', 'PR002', '2011-06-27', '14400', '0', 2, '1383', '', '2011-07-07', '38', '425', 'Finish'),
('13766', 'PR013', '2011-08-10', '39500', '0', 2, '1443', '', '2011-08-15', '44', '427', 'Finish'),
('13660', 'PR004', '2011-08-09', '8300', '0', 2, '1442', '', '2011-08-15', '44', '426', 'Finish'),
('20611', 'PR002', '2012-03-02', '14400', NULL, 2, '2173', '', '2012-03-08', '17/2012', '424', 'Finish'),
('13662', 'PR004', '2011-08-09', '8300', '0', 2, '1442', '', '2011-08-15', '44', '426', 'Finish'),
('13663', 'PR004', '2011-08-09', '8300', '0', 2, '1442', '', '2011-08-15', '44', '426', 'Finish'),
('14477', 'PR014', '2011-09-14', '14400', '0', 2, '1451', '', '2011-09-20', '45', '426', 'Finish'),
('14479', 'PR014', '2011-09-14', '14400', '0', 2, '1453', '', '2011-09-22', '45', '426', 'Finish'),
('14478', 'PR014', '2011-09-14', '14400', '0', 2, '1452', '', '2011-09-20', '45', '426', 'Finish'),
('14083', 'PR002', '2011-08-22', '14400', '0', 2, '1451', '', '2011-10-03', '45', '426', 'Finish'),
('14084', 'PR002', '2011-08-22', '14400', '0', 2, '1451', '', '2011-10-03', '45', '426', 'Finish'),
('14081', 'PR002', '2011-08-22', '14400', '0', 2, '1451', '', '2011-10-03', '45', '426', 'Finish'),
('14921', 'PR002', '2011-09-26', '14400', '0', 2, '1471', '', '2011-09-29', '47', '425', 'Finish'),
('14922', 'PR002', '2011-09-26', '14400', '0', 2, '1472', '', '2011-09-29', '47', '425', 'Finish'),
('14923', 'PR002', '2011-09-26', '14400', '0', 2, '1473', '', '2011-09-29', '47', '425', 'Finish'),
('15288', 'PR015', '2011-10-05', '7000', '0', 2, '1483', '', '2011-10-06', '48', '424', 'Finish'),
('15287', 'PR015', '2011-10-05', '7000', '0', 2, '1471', '', '2011-10-06', '47', '423', 'Finish'),
('15172', 'PR004', '2011-09-30', '8300', '0', 2, '1481', '', '2011-10-06', '48', '425', 'Finish'),
('15173', 'PR004', '2011-09-30', '8300', '0', 2, '1482', '', '2011-10-06', '48', '425', 'Finish'),
('20610', 'PR014', '2012-03-02', '14400', NULL, 2, '2172', '', '2012-03-08', '17/2012', '424', 'Finish'),
('15796', 'PR002', '2011-10-17', '14400', '0', 2, '1501', '', '2011-10-21', '50', '424', 'Finish'),
('15798', 'PR002', '2011-10-17', '14400', '0', 2, '1505', '', '2011-10-21', '50', '427', 'Finish'),
('15811', 'PR002', '2011-10-17', '14400', '0', 2, '1505', '', '2011-10-21', '50', '427', 'Finish'),
('16403', 'PR002', '2011-11-01', '14400', '0', 2, '1522', '', '2011-11-04', '52/2011', '424', 'Finish'),
('16404', 'PR002', '2011-11-01', '14400', '0', 2, '1523', '', '2011-11-04', '52/2011', '424', 'Finish'),
('17618', 'PR015', '2011-12-05', '7000', '0', 2, '2053', '', '2011-12-09', '05/2012', '428', 'Finish'),
('16597', 'PR004', '2011-11-04', '8300', '0', 2, '2021', '', '2011-11-11', '02/2011', '425', 'Finish'),
('20612', 'PR002', '2012-03-02', '14400', NULL, 2, '2174', '', '2012-03-08', '17/2012', '424', 'Finish'),
('16598', 'PR004', '2011-11-04', '8300', '0', 2, '2022', '', '2011-11-11', '02/2011', '425', 'Finish'),
('17045', 'PR016', '2011-11-16', '32895', '0', 2, '2033', '', '2011-11-25', '03/2012', '426', 'Finish'),
('17044', 'PR016', '2011-11-16', '32895', '0', 2, '2033', '', '2011-11-25', '03/2012', '426', 'Finish'),
('20609', 'PR014', '2012-03-02', '14400', NULL, 2, '2171', '', '2012-03-08', '17/2012', '424', 'Finish'),
('16124', 'PR002', '2011-10-24', '14400', '0', 2, '1511', '', '2011-10-27', '51/2011', '426', 'Finish'),
('16122', 'PR002', '2011-10-04', '14400', '0', 2, '1511', '', '2011-10-27', '51/2011', '426', 'Finish'),
('16118', 'PR014', '2011-10-04', '14400', '0', 2, '1514', '', '2011-10-27', '51/2011', '425', 'Finish'),
('16116', 'PR014', '2011-10-04', '14400', '0', 2, '1514', '', '2011-10-27', '51/2011', '425', 'Finish'),
('20162', 'PR029', '2012-02-22', '46000', NULL, 2, '2133', '', '2012-03-04', '13/2012', '424', 'Finish'),
('17152', 'PR013', '2011-11-20', '39500', '0', 2, '2022', '', '2011-12-15', '02/2011', '425', 'Finish'),
('15976', 'PR017', '0000-00-00', '37700', '0', 2, '149', '', '0000-00-00', '', '', 'Finish'),
('15977', 'PR018', '0000-00-00', '37700', '0', 2, '149', '', '0000-00-00', '', '', 'Finish'),
('17993', 'PR019', '2011-12-15', '39000', '0', 2, '1501', '<==Pengganti PO 16031==>', '2012-01-05', '50/2011', '425', 'Finish'),
('18015', 'PR020', '2011-12-15', '52800', '0', 2, '1492', '<==Pengganti PO 15978==>', '2012-01-05', '49/2011', '425', 'Finish'),
('20163', 'PR029', '2012-02-22', '46000', NULL, 2, '2134', '', '2012-03-04', '13/2012', '424', 'Finish'),
('17935', 'PR004', '2011-12-13', '8300', '0', 2, '2063', '', '2011-12-15', '06/2012', '424', 'Finish'),
('18198', 'PR021', '2011-12-21', '16500', '0', 2, '2074', '', '2012-01-05', '07/2012', '426', 'Finish'),
('18094', 'PR014', '2011-12-20', '14400', '0', 2, '2073', '', '2012-01-05', '07/2012', '424', 'Finish'),
('18299', 'PR014', '2012-01-02', '14400', '0', 2, '2085', '', '2012-01-05', '08/2012', '424', 'Finish'),
('20218', 'PR002', '2012-02-22', '14400', NULL, 2, '2156', '', '2012-03-04', '15/2012', '424', 'Finish'),
('18016', 'PR022', '2011-12-05', '52800', '0', 2, '1492', '<==Pengganti PO 15979==>', '2012-01-05', '49/2011', '425', 'Finish'),
('18043', 'PR023', '2011-12-16', '22800', '0', 2, '2063', '', '2012-01-05', '06/2012', '425', 'Finish'),
('17994', 'PR024', '2011-12-15', '39000', '0', 2, '1501', '<==Pengganti PO 16030==>', '2012-01-05', '50/2011', '425', 'Finish'),
('20220', 'PR028', '2012-02-22', '32895', NULL, 2, '2154', '', '2012-03-04', '15/2012', '424', 'Finish'),
('18044', 'PR023', '2011-12-16', '22800', '0', 2, '2064', '', '2012-01-05', '06/2012', '425', 'Finish'),
('18095', 'PR014', '2011-12-20', '14400', '0', 2, '2074', '', '2012-01-05', '07/2012', '424', 'Finish'),
('18197', 'PR021', '2011-12-21', '16500', '0', 2, '2073', '', '2012-01-05', '07/2012', '426', 'Finish'),
('20219', 'PR016', '2012-02-22', '32895', NULL, 2, '2155', '', '2012-03-04', '15/2012', '424', 'Finish'),
('18581', 'PR014', '2012-01-11', '14400', '0', 2, '2093', '', '2012-01-13', '09/2012', '424', 'Finish'),
('18583', 'PR014', '2012-01-11', '14400', '0', 2, '2093', '', '2012-01-13', '09/2012', '424', 'Finish'),
('18578', 'PR002', '2012-01-11', '14400', '0', 2, '2092', '', '2012-01-13', '09/2012', '424', 'Finish'),
('19632', 'PR004', '2012-02-07', '8300', NULL, 2, '2134', '', '2012-03-04', '13/2012', '426', 'Finish'),
('18577', 'PR002', '2012-01-11', '14400', '0', 2, '2091', '', '2012-01-13', '09/2012', '424', 'Finish'),
('18464', 'PR025', '2012-01-06', '22800', '0', 2, '2064', '', '2012-01-13', '06/2012', '425', 'Finish'),
('18465', 'PR025', '2012-01-06', '22800', '0', 2, '2065', '', '2012-01-13', '06/2012', '425', 'Finish'),
('19626', 'PR015', '2012-02-07', '7000', NULL, 2, '2133', '', '2012-03-04', '13/2012', '423', 'Finish'),
('18671', 'PR026', '2012-01-13', '14400', '0', 2, '2081', '', '2012-01-20', '08/2012', '425', 'Finish'),
('18920', 'PR027', '2012-01-19', '34500', '0', 2, '2091', '', '2012-01-26', '09/2012', '425', 'Finish'),
('18921', 'PR027', '2012-01-19', '34500', '0', 2, '2094', '', '2012-01-26', '09/2012', '425', 'Finish'),
('18986', 'PR014', '2012-01-24', '14400', '0', 2, '2111', '', '2012-01-26', '11/2012', '424', 'Finish'),
('19627', 'PR015', '2012-02-07', '7000', NULL, 2, '2133', '', '2012-03-04', '13/2012', '423', 'Finish'),
('18987', 'PR014', '2012-01-24', '14400', '0', 2, '2112', '', '2012-01-26', '11/2012', '424', 'Finish'),
('18988', 'PR014', '2012-01-24', '14400', '0', 2, '2112', '', '2012-01-26', '11/2012', '424', 'Finish'),
('18989', 'PR002', '2012-01-24', '14400', '0', 2, '2113', '', '2012-01-26', '11/2012', '424', 'Finish'),
('18990', 'PR002', '2012-01-24', '14400', '0', 2, '2114', '', '2012-01-26', '11/2012', '424', 'Finish'),
('19633', 'PR004', '2012-02-07', '8300', NULL, 2, '2135', '', '2012-03-04', '13/2012', '426', 'Finish'),
('18991', 'PR002', '2012-01-24', '14400', '0', 2, '2115', '', '2012-01-26', '11/2012', '424', 'Finish'),
('20967', 'PR002', '2012-03-09', '14400', NULL, 2, '2181', '', '2012-03-15', '18/2012', '424', 'Finish'),
('20969', 'PR002', '2012-03-09', '14400', NULL, 2, '2185', '', '2012-03-15', '18/2012', '424', 'Finish'),
('20966', 'PR015', '2012-03-09', '7000', NULL, 2, '2185', '', '2012-03-15', '18/2012', '423', 'Finish'),
('20965', 'PR015', '2012-03-09', '7000', NULL, 2, '2184', '', '2012-03-15', '18/2012', '423', 'Progress'),
('20970', 'PR014', '2012-03-09', '14400', NULL, 2, '2182', '', '2012-03-15', '18/2012', '424', 'Finish'),
('20972', 'PR004', '2012-03-09', '8300', NULL, 2, '2183', '', '2012-03-15', '18/2012', '424', 'Finish'),
('21296', 'PR030', '2012-03-19', '8300', NULL, 2, '2131', '', '2012-03-22', '13/2012', '426', 'Finish'),
('21799', 'PR031', '2012-04-02', '33500', NULL, 2, '2192', '', '2012-04-05', '19/2012', '426', 'Finish'),
('21924', 'PR002', '2012-04-03', '14400', NULL, 2, '2211', '', '2012-04-05', '21/2012', '426', 'Finish'),
('21923', 'PR014', '2012-04-03', '14400', NULL, 2, '2211', '', '2012-04-05', '21/2012', '426', 'Finish'),
('21798', 'PR032', '2012-04-02', '39000', NULL, 2, '2192', '', '2012-04-05', '19/2012', '426', 'Finish'),
('22117', 'PR015', '2012-04-09', '7000', '', 2, '2222', '=== BATAL ===', '2012-04-12', '22/2012', '423', 'Progress'),
('22277', 'PR033', '2012-04-11', '16500', NULL, 2, '2221', 'EXPORT SINGAPORE', '2012-04-12', '22/2012', '424', 'Finish'),
('22204', 'PR025', '2012-04-10', '22800', NULL, 2, '2225', '', '2012-04-12', '22/2012', '427', 'Finish'),
('22203', 'PR025', '2012-04-10', '22800', NULL, 2, '2224', '', '2012-04-12', '22/2012', '427', 'Progress'),
('22206', 'PR023', '2012-04-10', '22800', NULL, 2, '2222', '', '2012-04-12', '22/2012', '427', 'Finish'),
('22207', 'PR023', '2012-04-10', '22800', '', 2, '2223', 'change to PO 24274', '2012-04-12', '22/2012', '427', 'Progress'),
('22279', 'PR034', '2012-04-11', '16500', NULL, 2, '2221', 'EXPORT SINGAPORE', '2012-04-12', '22/2012', '424', 'Finish'),
('22416', 'PR014', '2012-04-16', '14400', NULL, 2, '2231', '', '2012-04-19', '23/2012', '424', 'Finish'),
('22407', 'PR035', '2012-04-16', '20900', NULL, 2, '2215', 'EXPORT ZAMBIA', '2012-04-19', '21/2012', '424', 'Finish'),
('22406', 'PR036', '2012-04-16', '20900', NULL, 2, '2215', 'EXPORT ZAMBIA', '2012-04-19', '21/2012', '424', 'Finish'),
('22841', 'PR037', '2012-04-24', '17500', NULL, 2, '2233', '', '2012-04-26', '23/2012', '424', 'Finish'),
('22842', 'PR037', '2012-04-24', '17500', NULL, 2, '2234', '', '2012-04-26', '23/2012', '424', 'Finish'),
('22843', 'PR037', '2012-04-24', '17500', NULL, 2, '2235', '', '2012-04-26', '23/2012', '424', 'Finish'),
('22625', 'PR038', '2012-04-19', '32000', NULL, 2, '2223', '', '2012-04-26', '22/2012', '424', 'Progress'),
('22624', 'PR039', '2012-04-19', '32000', NULL, 2, '2222', '', '2012-04-26', '22/2012', '424', 'Finish'),
('23231', 'PR040', '2012-05-04', '17500', NULL, 2, '2255', '', '2012-05-11', '25/2012', '424', 'Finish'),
('23230', 'PR040', '2012-05-04', '17500', NULL, 2, '2254', '', '2012-05-11', '25/2012', '424', 'Finish'),
('23233', 'PR040', '2012-05-04', '17500', NULL, 2, '2256', '', '2012-05-11', '25/2012', '424', 'Finish'),
('23275', 'PR002', '2012-05-04', '14400', NULL, 2, '2221', '', '2012-05-11', '22/2012', '425', 'Finish'),
('23315', 'PR004', '2012-05-04', '8300', NULL, 2, '2263', '', '2012-05-11', '26/2012', '424', 'Progress'),
('24567', 'PR014', '2012-06-04', '14400', NULL, 2, '2302', '', '2012-06-11', '30/2012', '424', 'Finish'),
('24569', 'PR014', '2012-06-04', '14400', NULL, 2, '2304', '', '2012-06-11', '30/2012', '424', 'Finish'),
('24568', 'PR014', '2012-06-04', '14400', NULL, 2, '2303', '', '2012-06-11', '30/2012', '424', 'Finish'),
('24259', 'PR014', '2012-05-25', '14400', NULL, 2, '2291', '', '2012-05-31', '29/2012', '424', 'Finish'),
('24260', 'PR014', '2012-05-25', '14400', NULL, 2, '2292', '', '2012-05-31', '29/2012', '424', 'Finish'),
('24261', 'PR002', '2012-05-25', '14400', NULL, 2, '2291', '', '2012-05-31', '29/2012', '424', 'Finish'),
('24274', 'PR023', '2012-05-25', '22800', '', 2, '2223', 'substitute of PO 22207', '2012-05-31', '22/2012', '427', 'Progress'),
('24270', 'PR032', '2012-05-25', '39000', NULL, 2, '2291', '', '2012-05-31', '29/2012', '425', 'Finish'),
('24271', 'PR031', '2012-05-25', '33500', NULL, 2, '2292', '', '2012-05-31', '29/2012', '425', 'Finish'),
('24536', 'PR037', '2012-06-04', '17500', NULL, 2, '2302', '', '2012-06-11', '30/2012', '424', 'Finish'),
('24535', 'PR037', '2012-06-04', '17500', NULL, 2, '2301', '', '0000-06-11', '30/2012', '424', 'Finish'),
('24759', 'PR041', '2012-06-07', '39000', '', 2, '2301', 'EXPORT SOUTH AFRICA', '2012-06-15', '30/2012', '425', 'Finish'),
('24757', 'PR042', '2012-06-07', '39000', '', 2, '2302', 'EXPORT SOUTH AFRICA', '2012-06-15', '30/2012', '425', 'Finish'),
('24845', 'PR043', '2012-06-11', '19500', '', 2, '2301', 'EXPORT SOUTH AFRICA', '2012-06-15', '30/2012', '426', 'Finish'),
('23582', 'PR002', '2012-01-01', '14400', '', 2, '2222', 'PO ASLI TIDAK ADA\r\n-PO ADA-', '2012-01-01', '22/2012', '424', 'Finish'),
('25075', 'PR002', '2012-06-15', '14400', NULL, 2, '2321', '', '2012-06-20', '32/2012', '424', 'Finish'),
('25248', 'PR034', '2012-06-19', '16500', '', 2, '2314', 'EXPORT SINGAPORE', '2012-06-21', '31/2012', '426', 'Progress'),
('25249', 'PR034', '2012-06-19', '16500', '', 2, '2314', 'EXPORT SINGAPORE', '2012-06-21', '31/2012', '426', 'Progress'),
('25245', 'PR033', '2012-06-19', '16500', '', 2, '2311', 'EKSPOR SINGAPORE', '2012-06-21', '31/2012', '426', 'Progress'),
('25250', 'PR034', '2012-06-19', '16500', '', 2, '2314', 'EKSPOR SINGAPORE', '2012-06-21', '31/2012', '426', 'Progress'),
('25246', 'PR033', '2012-06-19', '16500', '', 2, '2314', 'EKSPOR SINGAPORE', '2012-06-21', '31/2012', '426', 'Progress'),
('25118', 'PR044', '2012-06-18', '33500', NULL, 2, '2311', '', '2012-06-21', '31/2012', '424', 'Progress'),
('25247', 'PR033', '2012-06-19', '16500', NULL, 2, '2314', '', '2012-06-21', '31/2012', '426', 'Progress'),
('25482', 'PR014', '2012-06-25', '14400', NULL, 2, '2331', '', '2012-06-28', '33/2012', '424', 'Finish'),
('25417', 'PR014', '2012-06-22', '14400', '', 2, '2321', 'substiute of PO 25076', '2012-06-28', '32/2012', '424', 'Finish'),
('25486', 'PR002', '2012-06-25', '14400', NULL, 2, '2331', '', '2012-06-28', '33/2012', '424', 'Finish'),
('25485', 'PR014', '2012-06-25', '14400', NULL, 2, '2332', '', '2012-06-28', '33/2012', '424', 'Finish'),
('23587', 'PR028', '2012-01-01', '32895', '', 2, '2271', 'TIDAK ADA PO ASLI\r\n- PO ADA -', '2012-01-01', '27/2012', '424', 'Finish'),
('23902', 'PR004', '2012-01-01', '8300', '', 2, '2283', '', '2012-01-01', '28/2012', '424', 'Progress'),
('23586', 'PR016', '2012-01-01', '32895', '', 2, '2271', 'TIDAK ADA PO ASLI\r\n-PO ADA-', '2012-01-01', '27/2012', '424', 'Finish'),
('23271', 'PR045', '2012-05-04', '19500', NULL, 2, '2233', '', '2012-05-16', '23/2012', '426', 'Finish'),
('23270', 'PR045', '2012-05-04', '19500', NULL, 2, '2233', '', '2012-05-10', '23/2012', '426', 'Finish'),
('23269', 'PR046', '2012-05-04', '19500', NULL, 2, '2233', '', '2012-05-16', '23/2012', '426', 'Finish'),
('23268', 'PR046', '2012-05-04', '19500', '', 2, '2231', 'TOLONG DIBIKIN SURAT JALAN LG DGN size/pairs : 6/3', '2012-05-16', '23/2012', '426', 'Finish'),
('23581', 'PR014', '2012-05-11', '14400', NULL, 2, '2271', '', '2012-05-16', '27/2012', '424', 'Finish'),
('25788', 'PR004', '2012-06-29', '8300', NULL, 2, '2375', '', '2012-07-06', '37/2012', '426', 'Finish'),
('25619', 'PR014', '2012-06-27', '14400', NULL, 2, '2371', '', '2012-07-06', '37/2012', '424', 'Finish'),
('25618', 'PR002', '2012-06-27', '14400', NULL, 2, '2371', '', '2012-07-06', '37/2012', '424', 'Finish'),
('25787', 'PR004', '2012-06-29', '8300', NULL, 2, '2374', '', '2012-07-06', '37/2012', '426', 'Finish'),
('26028', 'PR016', '2012-07-09', '32895', NULL, 2, '2381', '', '2012-07-12', '38/2012', '426', 'Finish'),
('26009', 'PR028', '2012-07-09', '32895', NULL, 2, '2383', '', '2012-07-12', '38/2012', '424', 'Progress'),
('26013', 'PR028', '2012-07-09', '32895', NULL, 2, '2384', '', '2012-07-12', '38/2012', '424', 'Finish'),
('26030', 'PR028', '2012-07-09', '32895', NULL, 2, '2381', '', '2012-07-12', '38/2012', '426', 'Finish'),
('26632', 'PR014', '2012-07-24', '14400', NULL, 2, '2405', '', '2012-07-27', '40/2012', '424', 'Finish'),
('26631', 'PR014', '2012-07-24', '14400', NULL, 2, '2404', '', '2012-07-27', '40/2012', '424', 'Finish'),
('26548', 'PR047', '2012-07-23', '20000', '', 2, '2374', 'EXPORT ZAMBIA', '2012-07-27', '37/2012', '425', 'Finish'),
('26547', 'PR048', '2012-07-23', '20000', '', 2, '2374', 'EXPORT ZAMBIA', '2012-07-27', '37/2012', '425', 'Finish'),
('26636', 'PR002', '2012-07-24', '14400', NULL, 2, '2405', '', '2012-07-27', '40/2012', '424', 'Finish'),
('26635', 'PR002', '2012-07-24', '14400', NULL, 2, '2404', '', '2012-07-27', '40/2012', '424', 'Finish'),
('26634', 'PR002', '2012-07-24', '14400', NULL, 2, '2403', '', '2012-07-27', '40/2012', '424', 'Finish'),
('26633', 'PR002', '2012-07-24', '14400', NULL, 2, '2401', '', '2012-07-27', '40/2012', '424', 'Finish'),
('26887', 'PR014', '2012-07-30', '14400', NULL, 2, '2411', '', '2012-08-03', '41/2012', '424', 'Finish'),
('26888', 'PR002', '2012-07-30', '14400', NULL, 2, '2411', '', '2012-08-03', '41/2012', '424', 'Finish'),
('26316', 'PR004', '2012-07-16', '8300', NULL, 2, '2391', '', '2012-07-20', '39/2012', '426', 'Progress'),
('27365', 'PR030', '2012-08-10', '8300', NULL, 2, '2432', '', '2012-09-10', '43/2012', '424', 'Finish'),
('27366', 'PR030', '2012-08-10', '8300', NULL, 2, '2432', '', '2012-09-10', '43/2012', '424', 'Finish'),
('27782', 'PR037', '2012-09-10', '17500', NULL, 2, '2441', '', '2012-09-13', '44/2012', '424', 'Finish'),
('27799', 'PR037', '2012-09-10', '17500', NULL, 2, '2441', '', '2012-09-13', '44/2012', '424', 'Finish'),
('27778', 'PR040', '2012-09-10', '17500', NULL, 2, '2441', '', '2012-09-13', '44/2012', '424', 'Finish'),
('27779', 'PR040', '2012-09-10', '17500', NULL, 2, '2442', '', '2012-09-13', '44/2012', '424', 'Finish'),
('27780', 'PR040', '2012-09-10', '17500', NULL, 2, '2443', '', '2012-09-13', '44/2012', '424', 'Finish'),
('27776', 'PR002', '2012-09-10', '14400', NULL, 2, '2441', '', '2012-09-13', '44/2012', '424', 'Finish'),
('27777', 'PR002', '2012-09-10', '14400', NULL, 2, '2442', '', '2012-09-13', '44/2012', '424', 'Finish'),
('28201', 'PR004', '2012-09-18', '8300', NULL, 2, '2455', '', '2012-10-04', '45/2012', '426', 'Finish'),
('28561', 'PR002', '2012-09-26', '14400', NULL, 2, '2472', '', '2012-11-05', '47/2012', '425', 'Finish'),
('28560', 'PR002', '2012-09-26', '14400', NULL, 2, '2471', '', '2012-11-05', '47/2012', '425', 'Finish'),
('28583', 'PR037', '2012-09-26', '17500', NULL, 2, '2471', '', '2012-11-05', '47/2012', '425', 'Finish'),
('29085', 'PR046', '2012-10-11', '19500', NULL, 2, '2483', '', '2012-11-05', '48/2012', '423', 'Progress'),
('29088', 'PR046', '2012-10-11', '19500', NULL, 2, '2484', '', '2012-11-05', '48/2012', '423', 'Progress'),
('28482', 'PR002', '2012-09-25', '14400', NULL, 2, '2462', '', '2012-10-04', '46/2012', '424', 'Finish'),
('28481', 'PR002', '2012-09-25', '14400', NULL, 2, '2461', '', '2012-10-04', '46/2012', '424', 'Finish'),
('28526', 'PR030', '2012-09-25', '8300', NULL, 2, '2462', '', '2012-10-04', '46/2012', '427', 'Finish'),
('28528', 'PR030', '2012-09-25', '8300', NULL, 2, '2463', '', '2012-10-04', '46/2012', '427', 'Finish'),
('28525', 'PR004', '2012-09-25', '8300', NULL, 2, '2461', '', '2012-10-04', '46/2012', '427', 'Finish'),
('28524', 'PR004', '2012-09-25', '8300', NULL, 2, '2461', '', '2012-10-04', '46/2012', '427', 'Finish'),
('28486', 'PR037', '2012-09-25', '17500', NULL, 2, '2461', '', '2012-10-04', '46/2012', '424', 'Finish'),
('28485', 'PR037', '2012-09-25', '17500', NULL, 2, '2461', '', '2012-10-04', '46/2012', '424', 'Finish'),
('29089', 'PR045', '2012-10-11', '19500', NULL, 2, '2484', '', '2012-11-05', '48/2012', '423', 'Finish'),
('31132', 'PR014', '2013-01-03', '14400', NULL, 2, '3081', '', '2013-01-18', '08/2013', '424', 'Finish'),
('30521', 'PR014', '2011-01-01', '14400', NULL, 2, '3062', 'BELUM ADA PO ASLI.\r\ndetail hanya persepsi pribadi', '2011-01-01', '06/2013', '424', 'Progress'),
('31704', 'PR002', '2013-01-15', '14400', NULL, 2, '3101', '', '2013-02-21', '10/2013', '424', 'Progress'),
('31642', 'PR014', '2013-01-15', '14400', NULL, 2, '3101', '', '2013-02-21', '10/2013', '424', 'Progress'),
('31857', 'PR049', '2013-01-18', '21000', NULL, 2, '3093', '', '2013-01-29', '09/2013', '426', 'Progress'),
('31856', 'PR049', '2013-01-18', '21000', NULL, 2, '3092', '', '2013-01-29', '09/2013', '426', 'Progress'),
('31134', 'PR014', '2013-01-03', '14400', NULL, 2, '3082', '', '2013-01-18', '08/2013', '424', 'Finish'),
('31268', 'PR016', '2013-01-07', '32895', NULL, 2, '3092', '', '2013-01-18', '09/2013', '425', 'Progress'),
('31245', 'PR014', '2013-01-07', '14400', NULL, 2, '3092', '', '2013-01-18', '09/2013', '423', 'Progress'),
('31244', 'PR014', '2013-01-07', '14400', NULL, 2, '3092', '', '2013-01-18', '09/2013', '423', 'Finish'),
('31137', 'PR050', '2013-01-03', '21000', NULL, 2, '3052', '', '2013-01-18', '05/2013', '425', 'Finish'),
('31138', 'PR050', '2013-01-03', '21000', NULL, 2, '3051', '', '2013-01-18', '05/2013', '425', 'Finish'),
('31139', 'PR051', '2013-01-03', '21000', NULL, 2, '3051', 'Export singapore', '2013-01-18', '05/2013', '425', 'Finish'),
('31261', 'PR037', '2013-01-07', '17500', NULL, 2, '3092', '', '2013-01-10', '09/2013', '424', 'Finish'),
('31260', 'PR037', '2013-01-07', '17500', NULL, 2, '3092', '', '2013-01-10', '09/2013', '424', 'Finish'),
('33120', 'PR014', '2013-02-22', '14400', NULL, 2, '3161', '', '2013-02-28', '16/2013', '424', 'Progress'),
('33122', 'PR002', '2013-02-22', '14400', NULL, 2, '3161', '', '2013-02-28', '16/2013', '424', 'Progress'),
('30493', 'PR014', '2013-01-01', '14400', NULL, 2, '3061', '', '2013-01-01', '06/2013', '424', 'Finish'),
('30552', 'PR045', '2012-12-01', '19500', '', 2, '2461', 'BELUM ADA PO ASLI', '2012-12-01', '09/2013', '426', 'Progress'),
('32806', 'PR004', '2013-03-01', '8300', NULL, 2, '3142', '', '2013-03-01', '14/2013', '426', 'Progress'),
('32807', 'PR004', '2013-03-01', '8300', '', 2, '3142', 'BELUM ADA PO ASLI', '2013-03-01', '14/2013', '426', 'Progress');
