-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Waktu pembuatan: 27. April 2013 jam 13:53
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
-- Struktur dari tabel `dtb_receipt_detail`
--

CREATE TABLE IF NOT EXISTS `dtb_receipt_detail` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `receipt_id` bigint(20) unsigned NOT NULL,
  `invoice_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;