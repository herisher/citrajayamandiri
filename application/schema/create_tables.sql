-- MySQL dump 10.11
--
-- Host: localhost    Database: app_cawaii
-- ------------------------------------------------------
-- Server version	5.0.77

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `dtb_admin`
--

DROP TABLE IF EXISTS `dtb_admin`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtb_admin` (
  `id` int(10) unsigned NOT NULL,
  `type` int(11) NOT NULL,
  `account` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `lastlogin_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dtb_board`
--

DROP TABLE IF EXISTS `dtb_board`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtb_board` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `member_id` bigint(20) unsigned NOT NULL default '0',
  `item_id` bigint(20) unsigned NOT NULL default '0',
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `disp_flag` tinyint(4) NOT NULL default '1',
  `create_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dtb_category`
--

DROP TABLE IF EXISTS `dtb_category`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtb_category` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `genre_id` int(10) unsigned NOT NULL,
  `category_name` varchar(128) NOT NULL,
  `category_desc` text NOT NULL,
  `thumb_url` varchar(200) NOT NULL,
  `sort_order` int(11) NOT NULL default '0',
  `disp_flag` tinyint(4) NOT NULL default '1',
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dtb_genre`
--

DROP TABLE IF EXISTS `dtb_genre`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtb_genre` (
  `id` int(10) unsigned NOT NULL,
  `genre_name` varchar(128) NOT NULL,
  `genre_desc` text NOT NULL,
  `thumb_url` varchar(200) NOT NULL,
  `sort_order` int(11) NOT NULL default '0',
  `disp_flag` tinyint(4) NOT NULL default '1',
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dtb_item`
--

DROP TABLE IF EXISTS `dtb_item`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtb_item` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `member_id` bigint(20) unsigned NOT NULL default '0',
  `genre_id` int(10) unsigned NOT NULL default '0',
  `category_id` int(10) unsigned NOT NULL default '0',
  `sub_category_id` int(10) unsigned NOT NULL default '0',
  `item_name` varchar(200) NOT NULL,
  `content` text,
  `image_url` varchar(200) default NULL,
  `thumb_url` varchar(200) default NULL,
  `buy_url` varchar(200) default NULL,
  `likes` bigint(20) unsigned NOT NULL default '0',
  `reports` bigint(20) unsigned NOT NULL default '0',
  `memo` text,
  `disp_flag` tinyint(4) NOT NULL default '1',
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dtb_item_likes`
--

DROP TABLE IF EXISTS `dtb_item_likes`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtb_item_likes` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `member_id` bigint(20) unsigned NOT NULL default '0',
  `item_id` bigint(20) unsigned NOT NULL default '0',
  `create_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dtb_item_reports`
--

DROP TABLE IF EXISTS `dtb_item_reports`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtb_item_reports` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `member_id` bigint(20) unsigned NOT NULL default '0',
  `item_id` bigint(20) unsigned NOT NULL default '0',
  `create_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dtb_member`
--

DROP TABLE IF EXISTS `dtb_member`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtb_member` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `status` tinyint(4) default '2',
  `email` varchar(128) default NULL,
  `password` varchar(60) default NULL,
  `temp_password` varchar(60) default NULL,
  `accesskey` varchar(32) default NULL,
  `nickname` varchar(32) default NULL,
  `birthday` date NOT NULL,
  `birthday_public` tinyint(4) NOT NULL default '1',
  `address1` varchar(32) NOT NULL,
  `image_url` varchar(200) default NULL,
  `thumb_url` varchar(200) default NULL,
  `profile` text,
  `rank_id` tinyint(4) NOT NULL default '1',
  `points` bigint(20) NOT NULL default '0',
  `device_token` varchar(64) default NULL,
  `create_date` datetime default NULL,
  `update_date` datetime default NULL,
  `leave_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dtb_news`
--

DROP TABLE IF EXISTS `dtb_news`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtb_news` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `url` varchar(200) default NULL,
  `image_url` varchar(200) NOT NULL,
  `thumb_url` varchar(200) NOT NULL,
  `sort_order` int(11) NOT NULL default '0',
  `disp_date` date default NULL,
  `disp_flag` tinyint(4) NOT NULL default '1',
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dtb_notify`
--

DROP TABLE IF EXISTS `dtb_notify`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtb_notify` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `content` text NOT NULL,
  `send_count` bigint(20) unsigned NOT NULL default '0',
  `send_date` datetime NOT NULL,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dtb_rank`
--

DROP TABLE IF EXISTS `dtb_rank`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtb_rank` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `rank_name` varchar(128) NOT NULL,
  `points` int(10) unsigned NOT NULL default '0',
  `sort_order` int(11) NOT NULL default '0',
  `disp_flag` tinyint(4) NOT NULL default '1',
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dtb_sub_category`
--

DROP TABLE IF EXISTS `dtb_sub_category`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtb_sub_category` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `genre_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `sub_category_name` varchar(128) NOT NULL,
  `thumb_url` varchar(200) NOT NULL,
  `sort_order` int(11) NOT NULL default '0',
  `disp_flag` tinyint(4) NOT NULL default '1',
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-07-10 10:17:31
