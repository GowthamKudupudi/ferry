-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 24, 2013 at 04:06 PM
-- Server version: 5.5.32-0ubuntu0.13.04.1
-- PHP Version: 5.4.9-4ubuntu2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `collegedb2`
--
CREATE DATABASE IF NOT EXISTS `collegedb2` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `collegedb2`;

-- --------------------------------------------------------

--
-- Table structure for table `06ece41`
--

CREATE TABLE IF NOT EXISTS `06ece41` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wg22,{*}}',
  `id` varchar(10) NOT NULL,
  `cn_int` int(11) DEFAULT NULL,
  `cn_ext` int(3) DEFAULT NULL,
  `oc_int` int(3) DEFAULT NULL,
  `oc_ext` int(3) DEFAULT NULL,
  `dc_int` int(3) DEFAULT NULL,
  `dc_ext` int(3) DEFAULT NULL,
  `mpi_int` int(3) DEFAULT NULL COMMENT '{rg12,{25-26,42}},{wg13,{25-26,42}}',
  `mpi_ext` int(3) DEFAULT NULL COMMENT '{rg14,{25-26,42}},{wg18,{25-26,42}}',
  `dsp_int` int(3) DEFAULT NULL COMMENT '{rg11,{25-26,42}},{wg19,{25-26,42}},{wg23,{25-26,42}}',
  `dsp_ext` int(3) DEFAULT NULL,
  `emi_int` int(3) DEFAULT NULL COMMENT '{rg8,{25-26,42}},{wg20,{25-26,42}}',
  `emi_ext` int(3) DEFAULT NULL COMMENT '{rg57,{43}},{wg10,{25-26,42}}',
  `MEL_int` int(3) DEFAULT NULL COMMENT '{rg58,{44}}',
  `MEL_ext` int(3) DEFAULT NULL,
  `mpl_int` int(3) DEFAULT NULL,
  `mpl_ext` int(3) DEFAULT NULL,
  `total` int(3) DEFAULT NULL,
  `percentage` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index` (`index`),
  KEY `cn_int` (`cn_int`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='al:Az0,o:331' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `11marks11_ece_hod`
--

CREATE TABLE IF NOT EXISTS `11marks11_ece_hod` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wo331,{*}}',
  `id` varchar(10) NOT NULL,
  `ENG_I_int` int(2) DEFAULT NULL COMMENT '{wg49,{*}}',
  `ENG_I_ext` int(2) DEFAULT NULL COMMENT '{rg48,{*}}',
  `M_I_int` int(2) DEFAULT NULL,
  `M_I_ext` int(2) DEFAULT NULL,
  `ENS_int` int(2) DEFAULT NULL COMMENT '{wg39,{*}}',
  `ENS_ext` int(2) DEFAULT NULL COMMENT '{rg42,{*}}',
  `ENG_PHY_I_int` int(2) DEFAULT NULL,
  `ENG_PHY_I_ext` int(2) DEFAULT NULL,
  `C_PNG_int` int(2) DEFAULT NULL,
  `C_PNG_ext` int(2) DEFAULT NULL,
  `ENG_CHM_I_int` int(2) DEFAULT NULL,
  `ENG_CHM_I_ext` int(2) DEFAULT NULL,
  `CP_LAB_int` int(2) DEFAULT NULL,
  `CP_LAB_ext` int(2) DEFAULT NULL,
  `EPEC_LAB_int` int(2) DEFAULT NULL,
  `EPEC_LAB_ext` int(2) DEFAULT NULL,
  `ENG_PRO_LAB_int` int(2) DEFAULT NULL,
  `ENG_PRO_LAB_ext` int(2) DEFAULT NULL,
  `EW_LAB_int` int(2) DEFAULT NULL,
  `EW_LAB_ext` int(2) DEFAULT NULL,
  `total` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:Az0,o:331' AUTO_INCREMENT=121 ;

--
-- Dumping data for table `11marks11_ece_hod`
--

INSERT INTO `11marks11_ece_hod` (`index`, `id`, `ENG_I_int`, `ENG_I_ext`, `M_I_int`, `M_I_ext`, `ENS_int`, `ENS_ext`, `ENG_PHY_I_int`, `ENG_PHY_I_ext`, `C_PNG_int`, `C_PNG_ext`, `ENG_CHM_I_int`, `ENG_CHM_I_ext`, `CP_LAB_int`, `CP_LAB_ext`, `EPEC_LAB_int`, `EPEC_LAB_ext`, `ENG_PRO_LAB_int`, `ENG_PRO_LAB_ext`, `EW_LAB_int`, `EW_LAB_ext`, `total`) VALUES
(1, '11331A0401', 22, 55, 24, 70, 25, 53, 20, 60, 22, 61, 23, 33, 22, 42, 24, 44, 22, 45, 22, 43, NULL),
(2, '11331A0402', 14, 20, 13, 3, 21, 12, 15, 7, 13, 9, 20, 16, 20, 30, 22, 41, 20, 36, 21, 31, NULL),
(3, '11331A0403', 22, 60, 21, 61, 25, 72, 21, 69, 23, 42, 25, 47, 23, 40, 24, 50, 23, 49, 25, 50, NULL),
(4, '11331A0404', 19, 59, 16, 16, 21, 61, 18, 39, 15, 48, 18, 36, 21, 35, 21, 40, 22, 44, 24, 42, NULL),
(5, '11331A0405', 24, 55, 25, 67, 25, 58, 25, 63, 25, 40, 24, 55, 23, 50, 24, 42, 22, 45, 23, 45, NULL),
(6, '11331A0406', 24, 60, 25, 75, 25, 53, 23, 71, 23, 63, 25, 52, 25, 50, 25, 44, 22, 47, 25, 50, NULL),
(7, '11331A0407', 13, 52, 9, 8, 12, 45, 9, 2, 7, 6, 9, 5, 21, 45, 20, 42, 20, 38, 20, 37, NULL),
(8, '11331A0408', 20, 66, 24, 63, 23, 51, 20, 59, 25, 45, 22, 33, 24, 44, 23, 44, 22, 39, 24, 43, NULL),
(9, '11331A0409', 23, 60, 23, 59, 23, 46, 21, 52, 23, 54, 25, 37, 24, 48, 24, 48, 22, 41, 20, 43, NULL),
(10, '11331A0410', 20, 36, 23, 75, 23, 49, 22, 35, 21, 40, 22, 35, 22, 44, 24, 46, 21, 38, 24, 42, NULL),
(11, '11331A0411', 20, 46, 23, 41, 24, 50, 19, 43, 22, 33, 21, 29, 22, 41, 23, 45, 22, 39, 20, 41, NULL),
(12, '11331A0412', 24, 69, 21, 50, 24, 63, 24, 57, 22, 49, 23, 38, 24, 46, 21, 50, 21, 44, 24, 44, NULL),
(13, '11331A0413', 22, 51, 24, 73, 25, 44, 22, 47, 21, 33, 23, 50, 21, 46, 25, 46, 19, 41, 22, 42, NULL),
(14, '11331A0414', 22, 33, 23, 36, 23, 40, 20, 35, 19, 54, 21, 40, 25, 48, 21, 48, 19, 41, 21, 44, NULL),
(15, '11331A0415', 18, 50, 22, 39, 24, 68, 19, 40, 20, 38, 23, 41, 21, 40, 21, 44, 19, 38, 21, 35, NULL),
(16, '11331A0416', 20, 65, 24, 65, 25, 47, 18, 46, 20, 48, 20, 36, 23, 43, 22, 44, 22, 41, 24, 46, NULL),
(17, '11331A0417', 23, 56, 15, 12, 22, 31, 18, 32, 22, 26, 23, 17, 21, 46, 21, 42, 22, 41, 25, 45, NULL),
(18, '11331A0418', 23, 48, 24, 75, 23, 44, 23, 63, 23, 64, 25, 54, 25, 50, 25, 49, 21, 43, 25, 44, NULL),
(19, '11331A0419', 20, 60, 20, 48, 21, 65, 20, 34, 15, 30, 20, 29, 22, 40, 23, 49, 22, 39, 25, 46, NULL),
(20, '11331A0420', 23, 68, 24, 75, 25, 40, 19, 46, 22, 51, 23, 37, 24, 46, 24, 48, 21, 43, 25, 43, NULL),
(21, '11331A0421', 23, 64, 25, 75, 24, 46, 22, 55, 24, 40, 24, 44, 25, 48, 24, 49, 23, 43, 24, 46, NULL),
(22, '11331A0422', 21, 41, 24, 38, 24, 49, 23, 50, 22, 47, 22, 40, 24, 48, 23, 45, 23, 43, 19, 37, NULL),
(23, '11331A0423', 24, 52, 22, 46, 20, 74, 22, 60, 18, 39, 24, 45, 21, 48, 23, 48, 22, 47, 23, 43, NULL),
(24, '11331A0424', 14, 51, 11, 10, 18, 44, 13, 28, 13, 27, 17, 15, 20, 38, 21, 40, 23, 44, 20, 39, NULL),
(25, '11331A0425', 25, 66, 21, 35, 24, 51, 21, 48, 22, 53, 22, 38, 25, 49, 20, 47, 23, 44, 21, 44, NULL),
(26, '11331A0426', 23, 44, 25, 58, 22, 47, 20, 64, 22, 55, 24, 43, 25, 48, 21, 50, 21, 43, 25, 43, NULL),
(27, '11331A0427', 17, 46, 14, 31, 19, 48, 15, 38, 20, 35, 14, 29, 25, 46, 18, 45, 21, 42, 22, 44, NULL),
(28, '11331A0428', 23, 68, 21, 48, 24, 65, 21, 34, 23, 47, 23, 33, 23, 45, 23, 44, 22, 45, 25, 43, NULL),
(29, '11331A0429', 23, 64, 25, 69, 23, 47, 21, 48, 22, 67, 23, 47, 24, 45, 22, 48, 23, 44, 21, 41, NULL),
(30, '11331A0430', 22, 45, 25, 40, 25, 48, 22, 53, 23, 44, 22, 37, 25, 47, 23, 44, 23, 44, 21, 45, NULL),
(31, '11331A0431', 23, 35, 21, 36, 20, 63, 18, 29, 20, 39, 19, 14, 21, 45, 21, 44, 23, 44, 25, 46, NULL),
(32, '11331A0432', 22, 70, 19, 38, 20, 54, 19, 40, 22, 47, 19, 26, 23, 46, 22, 46, 21, 44, 25, 50, NULL),
(33, '11331A0433', 24, 63, 22, 65, 22, 42, 22, 52, 24, 61, 24, 38, 22, 44, 24, 49, 22, 42, 25, 41, NULL),
(34, '11331A0434', 21, 46, 23, 75, 24, 53, 22, 75, 22, 59, 24, 44, 25, 49, 23, 44, 19, 42, 25, 44, NULL),
(35, '11331A0435', 22, 52, 23, 26, 25, 69, 22, 46, 23, 35, 24, 47, 22, 40, 24, 45, 20, 45, 25, 45, NULL),
(36, '11331A0436', 24, 71, 23, 52, 25, 62, 20, 47, 24, 56, 24, 46, 23, 45, 24, 44, 22, 46, 24, 43, NULL),
(37, '11331A0437', 23, 64, 25, 75, 25, 45, 21, 49, 24, 65, 24, 42, 22, 47, 24, 46, 21, 41, 23, 43, NULL),
(38, '11331A0438', 21, 44, 24, 75, 23, 48, 22, 63, 21, 42, 24, 39, 20, 46, 21, 43, 24, 41, 21, 45, NULL),
(39, '11331A0439', 14, 31, 19, 14, 19, 42, 12, 0, 15, 10, 11, 4, 20, 30, 20, 39, 24, 39, 21, 35, NULL),
(40, '11331A0440', 24, 65, 25, 62, 25, 63, 23, 47, 25, 57, 25, 45, 25, 50, 23, 46, 23, 43, 25, 45, NULL),
(41, '11331A0441', 21, 60, 24, 62, 23, 40, 21, 52, 20, 45, 25, 36, 25, 46, 24, 48, 23, 45, 25, 50, NULL),
(42, '11331A0442', 23, 43, 22, 75, 25, 47, 23, 68, 21, 46, 24, 41, 25, 48, 23, 49, 24, 43, 25, 46, NULL),
(43, '11331A0443', 23, 57, 23, 52, 24, 67, 22, 52, 23, 31, 23, 58, 22, 48, 22, 42, 25, 44, 24, 45, NULL),
(44, '11331A0444', 23, 64, 23, 39, 19, 53, 20, 39, 20, 35, 19, 26, 21, 41, 23, 42, 22, 41, 23, 42, NULL),
(45, '11331A0445', 22, 59, 24, 71, 25, 42, 21, 48, 22, 33, 25, 42, 23, 40, 21, 45, 22, 44, 24, 44, NULL),
(46, '11331A0446', 23, 48, 24, 75, 23, 53, 20, 65, 21, 58, 24, 42, 20, 45, 20, 47, 20, 36, 24, 40, NULL),
(47, '11331A0447', 24, 59, 25, 68, 25, 71, 22, 60, 25, 40, 25, 60, 22, 45, 23, 50, 23, 45, 22, 42, NULL),
(48, '11331A0448', 21, 63, 24, 54, 21, 55, 20, 43, 21, 52, 23, 39, 21, 44, 21, 45, 24, 45, 20, 44, NULL),
(49, '11331A0449', 22, 51, 23, 53, 21, 45, 18, 43, 24, 49, 23, 38, 25, 48, 22, 44, 22, 44, 25, 50, NULL),
(50, '11331A0450', 19, 31, 21, 64, 22, 43, 18, 44, 18, 26, 21, 19, 20, 40, 21, 42, 22, 38, 20, 42, NULL),
(51, '11331A0451', 24, 51, 25, 60, 21, 75, 25, 52, 19, 42, 24, 52, 22, 46, 23, 49, 22, 46, 22, 50, NULL),
(52, '11331A0452', 23, 62, 23, 74, 24, 62, 21, 39, 21, 56, 20, 48, 21, 50, 23, 47, 22, 40, 25, 41, NULL),
(53, '11331A0453', 24, 61, 25, 65, 22, 51, 23, 49, 24, 49, 22, 42, 23, 48, 23, 49, 24, 47, 24, 46, NULL),
(54, '11331A0454', 24, 64, 24, 75, 22, 54, 24, 72, 24, 44, 22, 53, 25, 50, 24, 48, 25, 45, 25, 50, NULL),
(55, '11331A0455', 21, 61, 19, 36, 25, 75, 19, 36, 19, 44, 19, 56, 22, 46, 22, 47, 25, 48, 25, 43, NULL),
(56, '11331A0456', 20, 53, 17, 38, 23, 40, 18, 29, 20, 35, 18, 26, 20, 40, 19, 41, 23, 43, 21, 35, NULL),
(57, '11331A0457', 20, 48, 18, 28, 22, 45, 18, 32, 18, 37, 19, 32, 24, 42, 22, 41, 25, 40, 22, 43, NULL),
(58, '11331A0458', 25, 56, 24, 75, 25, 59, 24, 75, 24, 69, 24, 63, 25, 50, 24, 49, 25, 46, 25, 43, NULL),
(59, '11331A0459', 23, 46, 25, 62, 21, 70, 21, 39, 23, 41, 23, 60, 21, 48, 23, 45, 25, 44, 22, 42, NULL),
(60, '11331A0460', 21, 67, 21, 30, 20, 46, 16, 29, 22, 26, 21, 26, 23, 46, 22, 44, 25, 43, 21, 40, NULL),
(61, '11331A0461', 19, 51, 21, 47, 21, 50, 22, 30, 17, 53, 18, 28, 22, 41, 23, 44, 22, 43, 21, 45, NULL),
(62, '11331A0462', 18, 34, 20, 8, 21, 34, 15, 31, 12, 12, 22, 26, 20, 40, 19, 38, 22, 42, 20, 45, NULL),
(63, '11331A0463', 20, 46, 18, 37, 25, 49, 23, 26, 22, 47, 22, 42, 25, 46, 22, 48, 21, 42, 23, 41, NULL),
(64, '11331A0464', 12, 43, 15, 12, 10, 16, 19, 29, 12, 4, 13, 15, 20, 39, 21, 40, 20, 43, 21, 36, NULL),
(65, '11331A0465', 14, 64, 9, 69, 20, 49, 17, 32, 11, 29, 13, 27, 15, 39, 23, 49, 23, 42, 21, 41, NULL),
(66, '11331A0466', 21, 39, 19, 60, 25, 54, 24, 49, 21, 40, 23, 39, 20, 46, 19, 49, 24, 46, 22, 45, NULL),
(67, '11331A0467', 23, 49, 24, 61, 21, 65, 25, 58, 21, 53, 24, 43, 24, 50, 24, 46, 24, 47, 25, 42, NULL),
(68, '11331A0468', 23, 64, 24, 70, 22, 41, 25, 59, 21, 46, 25, 48, 21, 45, 23, 48, 25, 47, 23, 45, NULL),
(69, '11331A0469', 19, 55, 19, 39, 25, 47, 22, 39, 20, 54, 22, 34, 21, 44, 22, 44, 21, 39, 22, 42, NULL),
(70, '11331A0470', 23, 55, 20, 67, 18, 49, 16, 65, 21, 32, 21, 40, 20, 41, 22, 44, 24, 46, 20, 41, NULL),
(71, '11331A0471', 23, 49, 23, 43, 24, 64, 24, 39, 22, 37, 22, 48, 23, 46, 21, 46, 23, 44, 25, 50, NULL),
(72, '11331A0472', 23, 69, 24, 71, 25, 65, 24, 54, 22, 60, 24, 60, 24, 48, 25, 49, 25, 45, 24, 41, NULL),
(73, '11331A0473', 24, 63, 24, 73, 24, 48, 24, 48, 22, 53, 23, 44, 21, 45, 23, 47, 23, 43, 25, 45, NULL),
(74, '11331A0474', 22, 47, 18, 26, 23, 49, 24, 48, 19, 33, 20, 34, 22, 44, 24, 44, 22, 43, 22, 42, NULL),
(75, '11331A0475', 22, 46, 18, 13, 22, 67, 20, 34, 20, 26, 21, 16, 21, 44, 24, 43, 19, 42, 24, 43, NULL),
(76, '11331A0476', 21, 69, 21, 54, 23, 44, 25, 48, 16, 52, 22, 48, 22, 46, 23, 45, 21, 46, 24, 44, NULL),
(77, '11331A0477', 22, 61, 21, 72, 24, 45, 25, 54, 22, 68, 23, 43, 24, 46, 23, 45, 25, 47, 24, 42, NULL),
(78, '11331A0478', 23, 45, 24, 71, 23, 34, 24, 32, 19, 10, 22, 31, 21, 44, 23, 40, 23, 47, 22, 40, NULL),
(79, '11331A0479', 19, 46, 14, 30, 21, 46, 19, 31, 13, 31, 15, 35, 20, 40, 21, 40, 21, 40, 21, 40, NULL),
(80, '11331A0480', 12, 63, 21, 47, 22, 38, 19, 38, 18, 62, 20, 26, 21, 41, 20, 42, 21, 39, 21, 42, NULL),
(81, '11331A0481', 22, 61, 21, 54, 24, 49, 22, 56, 21, 59, 23, 39, 23, 46, 23, 44, 22, 45, 25, 43, NULL),
(82, '11331A0482', 22, 41, 20, 37, 24, 49, 25, 49, 23, 43, 23, 40, 23, 48, 23, 44, 23, 43, 23, 38, NULL),
(83, '11331A0483', 22, 51, 25, 67, 25, 70, 24, 62, 22, 41, 24, 55, 23, 48, 24, 47, 23, 45, 23, 43, NULL),
(84, '11331A0484', 22, 66, 23, 75, 22, 56, 22, 52, 22, 54, 20, 36, 23, 48, 20, 44, 21, 39, 22, 42, NULL),
(85, '11331A0485', 25, 63, 20, 42, 24, 50, 24, 33, 21, 54, 21, 33, 23, 46, 22, 46, 24, 40, 22, 40, NULL),
(86, '11331A0486', 25, 52, 21, 26, 25, 57, 25, 56, 21, 42, 23, 51, 20, 43, 23, 45, 24, 47, 25, 50, NULL),
(87, '11331A0487', 24, 54, 23, 52, 24, 71, 25, 59, 22, 44, 22, 59, 22, 40, 22, 43, 25, 49, 25, 47, NULL),
(88, '11331A0488', 22, 66, 24, 75, 25, 51, 25, 67, 24, 45, 25, 44, 22, 43, 23, 45, 25, 44, 23, 45, NULL),
(89, '11331A0489', 24, 63, 19, 63, 23, 46, 21, 53, 21, 63, 20, 33, 21, 47, 23, 44, 24, 44, 22, 46, NULL),
(90, '11331A0490', 22, 31, 23, 75, 21, 43, 22, 41, 23, 37, 23, 34, 21, 41, 21, 40, 23, 38, 22, 45, NULL),
(91, '11331A0491', 22, 51, 23, 63, 23, 72, 24, 60, 24, 43, 21, 57, 22, 49, 24, 48, 25, 46, 24, 40, NULL),
(92, '11331A0492', 23, 57, 25, 75, 23, 65, 25, 62, 24, 59, 24, 62, 22, 49, 22, 47, 25, 45, 25, 50, NULL),
(93, '11331A0493', 23, 63, 23, 67, 23, 47, 24, 50, 23, 75, 22, 36, 24, 50, 23, 49, 25, 46, 24, 44, NULL),
(94, '11331A0494', 16, 48, 15, 30, 23, 26, 18, 39, 15, 8, 18, 30, 23, 47, 20, 38, 23, 41, 21, 38, NULL),
(95, '11331A0495', 24, 59, 22, 37, 24, 70, 24, 64, 22, 43, 22, 58, 23, 48, 24, 49, 24, 48, 24, 47, NULL),
(96, '11331A0496', 24, 62, 21, 49, 23, 65, 23, 60, 21, 58, 24, 61, 20, 48, 24, 47, 25, 49, 25, 50, NULL),
(97, '11331A0497', 20, 56, 21, 54, 24, 43, 24, 40, 22, 53, 20, 28, 20, 41, 19, 42, 22, 40, 22, 41, NULL),
(98, '11331A0498', 23, 72, 25, 58, 24, 55, 22, 56, 23, 55, 23, 35, 22, 49, 25, 47, 25, 44, 21, 50, NULL),
(99, '11331A0499', 16, 48, 20, 6, 21, 43, 19, 14, 13, 13, 13, 31, 21, 38, 20, 44, 25, 38, 21, 42, NULL),
(100, '11331A04A0', 19, 57, 11, 33, 20, 46, 19, 38, 15, 36, 18, 34, 20, 40, 21, 45, 24, 43, 22, 40, NULL),
(101, '11331A04A1', 16, 41, 14, 11, 15, 35, 14, 17, 15, 8, 18, 11, 20, 40, 20, 39, 23, 40, 21, 40, NULL),
(102, '11331A04A2', 23, 62, 24, 75, 24, 34, 24, 64, 24, 74, 24, 49, 23, 47, 22, 47, 24, 45, 22, 42, NULL),
(103, '11331A04A3', 21, 51, 17, 30, 21, 53, 21, 39, 22, 36, 21, 43, 21, 45, 23, 44, 25, 45, 24, 41, NULL),
(104, '11331A04A4', 22, 65, 17, 45, 23, 15, 24, 44, 22, 51, 19, 36, 24, 45, 21, 43, 25, 46, 21, 42, NULL),
(105, '11331A04A5', 22, 68, 23, 75, 24, 53, 25, 57, 24, 71, 25, 52, 22, 47, 24, 47, 24, 43, 25, 44, NULL),
(106, '11331A04A6', 15, 33, 23, 71, 20, 15, 19, 31, 16, 26, 19, 35, 23, 38, 19, 42, 22, 38, 22, 40, NULL),
(107, '11331A04A7', 24, 53, 25, 67, 25, 73, 25, 64, 25, 46, 25, 61, 23, 48, 25, 44, 24, 44, 25, 44, NULL),
(108, '11331A04A8', 18, 56, 24, 73, 25, 53, 23, 38, 22, 46, 22, 51, 24, 48, 24, 48, 23, 45, 22, 46, NULL),
(109, '11331A04A9', 22, 66, 23, 74, 25, 39, 25, 58, 24, 75, 25, 37, 22, 45, 24, 50, 24, 47, 25, 44, NULL),
(110, '11331A04B0', 22, 49, 24, 75, 25, 48, 25, 59, 24, 55, 25, 48, 23, 46, 23, 42, 24, 45, 22, 43, NULL),
(111, '11331A04B1', 21, 40, 21, 17, 23, 47, 22, 13, 22, 21, 19, 26, 20, 43, 20, 38, 22, 40, 21, 47, NULL),
(112, '11331A04B2', 17, 54, 20, 62, 17, 40, 18, 48, 11, 42, 16, 33, 21, 43, 20, 37, 25, 43, 23, 46, NULL),
(113, '11331A04B3', 21, 61, 22, 70, 22, 52, 20, 58, 18, 53, 17, 32, 20, 39, 20, 46, 25, 44, 24, 45, NULL),
(114, '11331A04B4', 24, 60, 22, 58, 23, 54, 23, 63, 22, 64, 23, 55, 25, 49, 25, 49, 25, 50, 25, 46, NULL),
(115, '11331A04B5', 24, 46, 24, 61, 24, 59, 23, 68, 24, 48, 24, 46, 25, 50, 24, 48, 25, 50, 25, 50, NULL),
(116, '11331A04B6', 22, 63, 21, 54, 23, 43, 22, 64, 21, 53, 23, 46, 24, 47, 23, 50, 25, 50, 25, 50, NULL),
(117, '11331A04B7', 24, 67, 24, 69, 25, 33, 25, 59, 24, 74, 25, 36, 22, 45, 24, 50, 25, 49, 25, 47, NULL),
(118, '11331A04B8', 22, 56, 17, 40, 19, 48, 25, 57, 19, 46, 20, 42, 22, 44, 23, 46, 24, 49, 23, 50, NULL),
(119, '11331A04B9', 16, 32, 16, 26, 21, 52, 16, 32, 14, 28, 13, 38, 21, 38, 19, 39, 23, 40, 22, 42, NULL),
(120, '11331A04C0', 21, 57, 24, 55, 24, 56, 25, 46, 20, 48, 22, 36, 21, 42, 23, 44, 23, 45, 23, 42, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `2011_41marks_ece_head`
--

CREATE TABLE IF NOT EXISTS `2011_41marks_ece_head` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wo331,{*}}',
  `id` varchar(10) NOT NULL DEFAULT '',
  `CN_INT` int(2) DEFAULT NULL,
  `CN_EXT` int(2) DEFAULT NULL,
  `EMI_INT` int(2) DEFAULT NULL,
  `EMI_EXT` int(2) DEFAULT NULL,
  `CMC_INT` int(2) DEFAULT NULL,
  `CMC_EXT` int(2) DEFAULT NULL,
  `RF_INT` int(2) DEFAULT NULL,
  `RF_EXT` int(2) DEFAULT NULL,
  `MCA_INT` int(2) DEFAULT NULL,
  `MCA_EXT` int(2) DEFAULT NULL,
  `DIP_INT` int(2) DEFAULT NULL,
  `DIP_EXT` int(2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='al:Az0,o:331' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `04331a04`
--

CREATE TABLE IF NOT EXISTS `04331a04` (
  `index` int(3) NOT NULL AUTO_INCREMENT,
  `id` varchar(10) NOT NULL,
  `uid` int(16) DEFAULT NULL,
  `passKey` varchar(13) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:Az0,o:331' AUTO_INCREMENT=121 ;

--
-- Dumping data for table `04331a04`
--

INSERT INTO `04331a04` (`index`, `id`, `uid`, `passKey`) VALUES
(1, '04331A0401', NULL, '4ee974d969ff4'),
(2, '04331A0402', NULL, '4ee974d96afdc'),
(3, '04331A0403', NULL, '4ee974d96bbc5'),
(4, '04331A0404', NULL, '4ee974d96cb81'),
(5, '04331A0405', NULL, '4ee974d96d882'),
(6, '04331A0406', NULL, '4ee974d96e5db'),
(7, '04331A0407', NULL, '4ee974d9742c8'),
(8, '04331A0408', NULL, '4ee974d975467'),
(9, '04331A0409', NULL, '4ee974d976578'),
(10, '04331A0410', NULL, '4ee974d977541'),
(11, '04331A0411', NULL, '4ee974d97843b'),
(12, '04331A0412', NULL, '4ee974d9797e7'),
(13, '04331A0413', NULL, '4ee974d97a62f'),
(14, '04331A0414', NULL, '4ee974d97b4be'),
(15, '04331A0415', NULL, '4ee974d97c4e9'),
(16, '04331A0416', NULL, '4ee974d97d65f'),
(17, '04331A0417', NULL, '4ee974d97e67e'),
(18, '04331A0418', NULL, '4ee974d97f548'),
(19, '04331A0419', NULL, '4ee974d98040d'),
(20, '04331A0420', NULL, '4ee974d981646'),
(21, '04331A0421', NULL, '4ee974d982540'),
(22, '04331A0422', NULL, '4ee974d983492'),
(23, '04331A0423', NULL, '4ee974d98459c'),
(24, '04331A0424', NULL, '4ee974d985614'),
(25, '04331A0425', NULL, '4ee974d986613'),
(26, '04331A0426', NULL, '4ee974d98752d'),
(27, '04331A0427', NULL, '4ee974d988404'),
(28, '04331A0428', NULL, '4ee974d989576'),
(29, '04331A0429', NULL, '4ee974d98a28d'),
(30, '04331A0430', NULL, '4ee974d98ad7d'),
(31, '04331A0431', NULL, '4ee974d98b91d'),
(32, '04331A0432', NULL, '4ee974d98c7fb'),
(33, '04331A0433', NULL, '4ee974d98d381'),
(34, '04331A0434', NULL, '4ee974d98e19d'),
(35, '04331A0435', NULL, '4ee974d98f3dd'),
(36, '04331A0436', NULL, '4ee974d990357'),
(37, '04331A0437', NULL, '4ee974d991312'),
(38, '04331A0438', NULL, '4ee974d9922bd'),
(39, '04331A0439', NULL, '4ee974d99341c'),
(40, '04331A0440', NULL, '4ee974d9943b7'),
(41, '04331A0441', NULL, '4ee974d995747'),
(42, '04331A0442', NULL, '4ee974d9966a2'),
(43, '04331A0443', NULL, '4ee974d997933'),
(44, '04331A0444', NULL, '4ee974d99870c'),
(45, '04331A0445', 64, 'REGISTERED'),
(46, '04331A0446', NULL, '4ee974d99a8a2'),
(47, '04331A0447', NULL, '4ee974d99ba22'),
(48, '04331A0448', NULL, '4ee974d99c9d8'),
(49, '04331A0449', NULL, '4ee974d99d8b9'),
(50, '04331A0450', NULL, '4ee974d99eaed'),
(51, '04331A0451', NULL, '4ee974d99fcab'),
(52, '04331A0452', NULL, '4ee974d9a0b7c'),
(53, '04331A0453', NULL, '4ee974d9a1d66'),
(54, '04331A0454', NULL, '4ee974d9a2cf3'),
(55, '04331A0455', NULL, '4ee974d9a3bcc'),
(56, '04331A0456', NULL, '4ee974d9a5594'),
(57, '04331A0457', NULL, '4ee974d9a668c'),
(58, '04331A0458', NULL, '4ee974d9a77a1'),
(59, '04331A0459', NULL, '4ee974d9a89e6'),
(60, '04331A0460', NULL, '4ee974d9a9c30'),
(61, '04331A0461', NULL, '4ee974d9aacbb'),
(62, '04331A0462', NULL, '4ee974d9abcfd'),
(63, '04331A0463', NULL, '4ee974d9ad208'),
(64, '04331A0464', NULL, '4ee974d9ae1d1'),
(65, '04331A0465', NULL, '4ee974d9af457'),
(66, '04331A0466', NULL, '4ee974d9b0c21'),
(67, '04331A0467', NULL, '4ee974d9b1c33'),
(68, '04331A0468', NULL, '4ee974d9b2cad'),
(69, '04331A0469', NULL, '4ee974d9b3bcb'),
(70, '04331A0470', NULL, '4ee974d9b4e1f'),
(71, '04331A0471', NULL, '4ee974d9b5d27'),
(72, '04331A0472', NULL, '4ee974d9b6c4d'),
(73, '04331A0473', NULL, '4ee974d9b7d64'),
(74, '04331A0474', NULL, '4ee974d9b8eb0'),
(75, '04331A0475', NULL, '4ee974d9ba02c'),
(76, '04331A0476', NULL, '4ee974d9bafa5'),
(77, '04331A0477', NULL, '4ee974d9bbed9'),
(78, '04331A0478', NULL, '4ee974d9bcfd2'),
(79, '04331A0479', NULL, '4ee974d9bdea4'),
(80, '04331A0480', NULL, '4ee974d9bedb7'),
(81, '04331A0481', NULL, '4ee974d9c0294'),
(82, '04331A0482', NULL, '4ee974d9c11eb'),
(83, '04331A0483', NULL, '4ee974d9c23dc'),
(84, '04331A0484', NULL, '4ee974d9c3363'),
(85, '04331A0485', NULL, '4ee974d9c452d'),
(86, '04331A0486', NULL, '4ee974d9c7ae6'),
(87, '04331A0487', NULL, '4ee974d9c8b95'),
(88, '04331A0488', NULL, '4ee974d9cd173'),
(89, '04331A0489', NULL, '4ee974d9d2c01'),
(90, '04331A0490', NULL, '4ee974d9d87f0'),
(91, '04331A0491', NULL, '4ee974d9dd63f'),
(92, '04331A0492', NULL, '4ee974d9e2d08'),
(93, '04331A0493', NULL, '4ee974d9e8c0d'),
(94, '04331A0494', NULL, '4ee974d9eda1f'),
(95, '04331A0495', NULL, '4ee974da0a02d'),
(96, '04331A0496', NULL, '4ee974da12705'),
(97, '04331A0497', NULL, '4ee974da1cc68'),
(98, '04331A0498', NULL, '4ee974da1dc0f'),
(99, '04331A0499', NULL, '4ee974da22b98'),
(100, '04331A04A0', NULL, '4ee974da2d12c'),
(101, '04331A04A1', NULL, '4ee974da2e063'),
(102, '04331A04A2', NULL, '4ee974da32fe2'),
(103, '04331A04A3', NULL, '4ee974da3d587'),
(104, '04331A04A4', NULL, '4ee974da43244'),
(105, '04331A04A5', NULL, '4ee974da45c6c'),
(106, '04331A04A6', NULL, '4ee974da4ae93'),
(107, '04331A04A7', NULL, '4ee974da50e25'),
(108, '04331A04A8', NULL, '4ee974da564c7'),
(109, '04331A04A9', NULL, '4ee974da5b277'),
(110, '04331A04B0', NULL, '4ee974da60d18'),
(111, '04331A04B1', NULL, '4ee974da66814'),
(112, '04331A04B2', NULL, '4ee974da6b7a7'),
(113, '04331A04B3', NULL, '4ee974da70d5a'),
(114, '04331A04B4', NULL, '4ee974da76d6f'),
(115, '04331A04B5', NULL, '4ee974da7bc28'),
(116, '04331A04B6', NULL, '4ee974da7cbe6'),
(117, '04331A04B7', NULL, '4ee974da87202'),
(118, '04331A04B8', NULL, '4ee974da8c07f'),
(119, '04331A04B9', NULL, '4ee974da8d1bc'),
(120, '04331A04C0', NULL, '4ee974da96e80');

-- --------------------------------------------------------

--
-- Table structure for table `06331a04`
--

CREATE TABLE IF NOT EXISTS `06331a04` (
  `index` int(3) NOT NULL AUTO_INCREMENT,
  `id` varchar(10) NOT NULL,
  `uid` int(16) DEFAULT NULL,
  `passKey` varchar(13) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:Az0,o:331' AUTO_INCREMENT=121 ;

--
-- Dumping data for table `06331a04`
--

INSERT INTO `06331a04` (`index`, `id`, `uid`, `passKey`) VALUES
(1, '06331A0401', 8, '4ee8b6415f6fd'),
(2, '06331A0402', 3, '4ee8b64160682'),
(3, '06331A0403', 3, '4ee8b64161426'),
(4, '06331A0404', 3, '4ee8b64162129'),
(5, '06331A0405', 7, '4ee8b64162bb1'),
(6, '06331A0406', 9, '4ee8b64163746'),
(7, '06331A0407', NULL, '4ee8b641640ea'),
(8, '06331A0408', NULL, '4ee8b64164f77'),
(9, '06331A0409', NULL, '4ee8b64165823'),
(10, '06331A0410', NULL, '4ee8b64166137'),
(11, '06331A0411', NULL, '4ee8b64166c9b'),
(12, '06331A0412', NULL, '4ee8b641677a4'),
(13, '06331A0413', NULL, '4ee8b6416801b'),
(14, '06331A0414', NULL, '4ee8b64168a99'),
(15, '06331A0415', NULL, '4ee8b6416920c'),
(16, '06331A0416', NULL, '4ee8b64169af1'),
(17, '06331A0417', NULL, '4ee8b6416a25a'),
(18, '06331A0418', NULL, '4ee8b6416a9a6'),
(19, '06331A0419', NULL, '4ee8b6416b19c'),
(20, '06331A0420', NULL, '4ee8b6416ba46'),
(21, '06331A0421', NULL, '4ee8b6416c4bb'),
(22, '06331A0422', NULL, '4ee8b6416cc41'),
(23, '06331A0423', NULL, '4ee8b6416d3a6'),
(24, '06331A0424', NULL, '4ee8b6416dc68'),
(25, '06331A0425', NULL, '4ee8b6416e3f0'),
(26, '06331A0426', NULL, '4ee8b6416efac'),
(27, '06331A0427', NULL, '4ee8b6417aa2a'),
(28, '06331A0428', NULL, '4ee8b6417b714'),
(29, '06331A0429', NULL, '4ee8b6417c190'),
(30, '06331A0430', NULL, '4ee8b6417cea9'),
(31, '06331A0431', NULL, '4ee8b6417d87a'),
(32, '06331A0432', NULL, '4ee8b6417e918'),
(33, '06331A0433', NULL, '4ee8b641829a7'),
(34, '06331A0434', NULL, '4ee8b64183f19'),
(35, '06331A0435', NULL, '4ee8b64184b77'),
(36, '06331A0436', NULL, '4ee8b641858c3'),
(37, '06331A0437', NULL, '4ee8b64186279'),
(38, '06331A0438', NULL, '4ee8b64186b8a'),
(39, '06331A0439', NULL, '4ee8b6418748e'),
(40, '06331A0440', NULL, '4ee8b641880f1'),
(41, '06331A0441', NULL, '4ee8b64188a49'),
(42, '06331A0442', NULL, '4ee8b641895a2'),
(43, '06331A0443', NULL, '4ee8b64189eff'),
(44, '06331A0444', NULL, '4ee8b6418a88b'),
(45, '06331A0445', NULL, '4ee8b6418b289'),
(46, '06331A0446', NULL, '4ee8b6418bb9c'),
(47, '06331A0447', NULL, '4ee8b6418c4fc'),
(48, '06331A0448', NULL, '4ee8b6418ce83'),
(49, '06331A0449', NULL, '4ee8b6418d860'),
(50, '06331A0450', NULL, '4ee8b6418e603'),
(51, '06331A0451', NULL, '4ee8b6418efdd'),
(52, '06331A0452', NULL, '4ee8b6418fc99'),
(53, '06331A0453', NULL, '4ee8b6419061a'),
(54, '06331A0454', NULL, '4ee8b64190f45'),
(55, '06331A0455', NULL, '4ee8b641918f9'),
(56, '06331A0456', NULL, '4ee8b64192461'),
(57, '06331A0457', NULL, '4ee8b64192d8f'),
(58, '06331A0458', NULL, '4ee8b6419374a'),
(59, '06331A0459', NULL, '4ee8b64193fd1'),
(60, '06331A0460', NULL, '4ee8b64194887'),
(61, '06331A0461', NULL, '4ee8b6419540b'),
(62, '06331A0462', NULL, '4ee8b64195e3e'),
(63, '06331A0463', NULL, '4ee8b64196745'),
(64, '06331A0464', NULL, '4ee8b64196fda'),
(65, '06331A0465', NULL, '4ee8b64197ab7'),
(66, '06331A0466', NULL, '4ee8b64198554'),
(67, '06331A0467', NULL, '4ee8b64198e14'),
(68, '06331A0468', NULL, '4ee8b6419969b'),
(69, '06331A0469', NULL, '4ee8b64199f7d'),
(70, '06331A0470', NULL, '4ee8b6419ad23'),
(71, '06331A0471', NULL, '4ee8b6419b605'),
(72, '06331A0472', NULL, '4ee8b6419d287'),
(73, '06331A0473', NULL, '4ee8b6419de4d'),
(74, '06331A0474', NULL, '4ee8b6429ab53'),
(75, '06331A0475', NULL, '4ee8b6429c03a'),
(76, '06331A0476', 56, 'REGISTERED'),
(77, '06331A0477', 57, 'REGISTERED'),
(78, '06331A0478', NULL, '4ee8b6429e4db'),
(79, '06331A0479', NULL, '4ee8b6429f180'),
(80, '06331A0480', NULL, '4ee8b6429fce7'),
(81, '06331A0481', NULL, '4ee8b642a0900'),
(82, '06331A0482', NULL, '4ee8b642a1378'),
(83, '06331A0483', NULL, '4ee8b642a1d6f'),
(84, '06331A0484', NULL, '4ee8b642a28a6'),
(85, '06331A0485', NULL, '4ee8b642a37c5'),
(86, '06331A0486', NULL, '4ee8b642a414d'),
(87, '06331A0487', NULL, '4ee8b642a4b03'),
(88, '06331A0488', NULL, '4ee8b642a5484'),
(89, '06331A0489', NULL, '4ee8b642a660c'),
(90, '06331A0490', NULL, '4ee8b642a7047'),
(91, '06331A0491', NULL, '4ee8b642a7c35'),
(92, '06331A0492', NULL, '4ee8b642a8714'),
(93, '06331A0493', NULL, '4ee8b642a92c3'),
(94, '06331A0494', NULL, '4ee8b642a9c74'),
(95, '06331A0495', NULL, '4ee8b642aa5e3'),
(96, '06331A0496', NULL, '4ee8b642ab26b'),
(97, '06331A0497', NULL, '4ee8b642ac03f'),
(98, '06331A0498', NULL, '4ee8b642acd70'),
(99, '06331A0499', NULL, '4ee8b642ad8e4'),
(100, '06331A04A0', NULL, '4ee8b642ae628'),
(101, '06331A04A1', NULL, '4ee8b642af836'),
(102, '06331A04A2', NULL, '4ee8b642b0423'),
(103, '06331A04A3', NULL, '4ee8b642b0fc4'),
(104, '06331A04A4', NULL, '4ee8b642b1c5f'),
(105, '06331A04A5', NULL, '4ee8b642b26c4'),
(106, '06331A04A6', NULL, '4ee8b642b315f'),
(107, '06331A04A7', NULL, '4ee8b642b3bfa'),
(108, '06331A04A8', NULL, '4ee8b642b4db9'),
(109, '06331A04A9', NULL, '4ee8b642b5a8d'),
(110, '06331A04B0', NULL, '4ee8b642b65b5'),
(111, '06331A04B1', NULL, '4ee8b642b704c'),
(112, '06331A04B2', NULL, '4ee8b642b7cd5'),
(113, '06331A04B3', NULL, '4ee8b642b8cd4'),
(114, '06331A04B4', NULL, '4ee8b642b9a95'),
(115, '06331A04B5', NULL, '4ee8b642ba768'),
(116, '06331A04B6', NULL, '4ee8b642bb76f'),
(117, '06331A04B7', NULL, '4ee8b642bc250'),
(118, '06331A04B8', NULL, '4ee8b642bcd32'),
(119, '06331A04B9', NULL, '4ee8b642bda88'),
(120, '06331A04C0', NULL, '4ee8b642be711');

-- --------------------------------------------------------

--
-- Table structure for table `abboo`
--

CREATE TABLE IF NOT EXISTS `abboo` (
  `index` int(3) NOT NULL AUTO_INCREMENT,
  `go` varchar(8) NOT NULL,
  `boo` enum('DEVIL','POLTERGEIST') DEFAULT NULL,
  `ooo` int(5) DEFAULT NULL,
  `vammoo` float DEFAULT NULL,
  `bammo` int(8) DEFAULT NULL,
  `kdfh` int(8) DEFAULT NULL,
  PRIMARY KEY (`go`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='{wt2,{*}}' AUTO_INCREMENT=38 ;

--
-- Dumping data for table `abboo`
--

INSERT INTO `abboo` (`index`, `go`, `boo`, `ooo`, `vammoo`, `bammo`, `kdfh`) VALUES
(4, '06ece41', 'POLTERGEIST', NULL, NULL, NULL, NULL),
(5, '0asdfa', 'DEVIL', 9385939, NULL, NULL, NULL),
(14, 'df', 'POLTERGEIST', 34, NULL, NULL, NULL),
(11, 'dfd', 'DEVIL', NULL, NULL, NULL, NULL),
(15, 'dfdd', 'DEVIL', 33, NULL, NULL, NULL),
(17, 'dfdddd', 'POLTERGEIST', 345, NULL, NULL, NULL),
(16, 'dfdf', 'DEVIL', 23, NULL, NULL, NULL),
(26, 'dfdfgh', NULL, NULL, NULL, NULL, NULL),
(28, 'dfdrt', NULL, NULL, NULL, NULL, NULL),
(10, 'dfgd', 'DEVIL', NULL, NULL, NULL, NULL),
(9, 'dflglsdf', 'DEVIL', 4543, NULL, NULL, NULL),
(22, 'dflsadg', 'POLTERGEIST', 234, 23, 45, 234),
(20, 'dfs', 'POLTERGEIST', 98, 98, 97097, NULL),
(25, 'dgre', NULL, 34, NULL, 3253, NULL),
(18, 'dsfasd', 'DEVIL', 456, NULL, NULL, NULL),
(21, 'dsfgs', 'DEVIL', 4545, 34, 455, NULL),
(27, 'dsfsd', NULL, 34, NULL, NULL, NULL),
(12, 'dw', 'DEVIL', NULL, NULL, NULL, NULL),
(7, 'fgdfg', 'DEVIL', 23445, NULL, NULL, NULL),
(8, 'fgdfsd', 'DEVIL', 234, NULL, NULL, NULL),
(32, 'gh', NULL, NULL, NULL, NULL, NULL),
(35, 'ghk', NULL, NULL, NULL, NULL, NULL),
(19, 'jgjgj', 'DEVIL', 678, 897, NULL, NULL),
(33, 'jk', NULL, NULL, NULL, NULL, NULL),
(31, 'md', NULL, NULL, NULL, NULL, NULL),
(29, 'qwe', NULL, NULL, NULL, NULL, NULL),
(23, 'sdagfl', NULL, NULL, NULL, NULL, NULL),
(36, 'sdfl', NULL, NULL, NULL, NULL, NULL),
(24, 'sldfjo', NULL, 34, NULL, NULL, NULL),
(30, 'th', NULL, NULL, NULL, NULL, NULL),
(37, 'wermn', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `abboo3`
--

CREATE TABLE IF NOT EXISTS `abboo3` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `huh` int(8) DEFAULT NULL,
  `hmm` int(8) DEFAULT NULL,
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='al:,o:u36' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bill1`
--

CREATE TABLE IF NOT EXISTS `bill1` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `items` varchar(18) NOT NULL DEFAULT '',
  `qty` int(8) DEFAULT NULL,
  `price` float DEFAULT NULL,
  PRIMARY KEY (`items`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `bill1`
--

INSERT INTO `bill1` (`index`, `items`, `qty`, `price`) VALUES
(2, 'Clean&Clear', 3, 240),
(4, 'P''s', 3, NULL),
(1, 'parachute45ml', 2, 24),
(3, 'POND''S', 2, 40);

-- --------------------------------------------------------

--
-- Table structure for table `bill2`
--

CREATE TABLE IF NOT EXISTS `bill2` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `itemName` varchar(18) NOT NULL DEFAULT '',
  `qty` int(2) DEFAULT NULL,
  `price` float DEFAULT NULL,
  PRIMARY KEY (`itemName`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bill3`
--

CREATE TABLE IF NOT EXISTS `bill3` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `itemName` varchar(18) NOT NULL DEFAULT '',
  `qty` int(2) DEFAULT NULL,
  `price` float DEFAULT NULL,
  PRIMARY KEY (`itemName`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `bill3`
--

INSERT INTO `bill3` (`index`, `itemName`, `qty`, `price`) VALUES
(1, 'parachute45ml', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bill4`
--

CREATE TABLE IF NOT EXISTS `bill4` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `itemName` varchar(18) NOT NULL DEFAULT '',
  `qty` int(2) DEFAULT NULL,
  `price` float DEFAULT NULL,
  PRIMARY KEY (`itemName`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bill5`
--

CREATE TABLE IF NOT EXISTS `bill5` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `itemName` varchar(18) NOT NULL DEFAULT '',
  `qty` int(2) DEFAULT NULL,
  `price` float DEFAULT NULL,
  PRIMARY KEY (`itemName`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=15 ;

--
-- Dumping data for table `bill5`
--

INSERT INTO `bill5` (`index`, `itemName`, `qty`, `price`) VALUES
(10, 'c&c', 2, 24),
(4, 'cl&c', 2, 2),
(11, 'Clean&Clear', 2, 160),
(3, 'cls&cl', 5, 5),
(1, 'parachute45ml', 3, 36),
(13, 'PO''S', 4, 72),
(14, 'PON''S', 5, 95),
(12, 'POND''S', 2, 40),
(2, 'ponds', 4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `cardsscore`
--

CREATE TABLE IF NOT EXISTS `cardsscore` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `sno` varchar(5) NOT NULL,
  `gowtham` int(2) DEFAULT NULL,
  `meher` int(2) DEFAULT NULL,
  `sri` int(2) DEFAULT NULL,
  `sandy` int(2) DEFAULT NULL,
  `vam` int(2) DEFAULT NULL,
  `surya` int(2) DEFAULT NULL,
  PRIMARY KEY (`sno`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `cardsscore`
--

INSERT INTO `cardsscore` (`index`, `sno`, `gowtham`, `meher`, `sri`, `sandy`, `vam`, `surya`) VALUES
(1, 'TOTAL', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cse11jan2012analysis_cse_hod`
--

CREATE TABLE IF NOT EXISTS `cse11jan2012analysis_cse_hod` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wo455,{*}}',
  `resultant` varchar(24) NOT NULL DEFAULT '',
  `value` int(1) DEFAULT NULL,
  PRIMARY KEY (`resultant`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:Bz0,o:455' AUTO_INCREMENT=11 ;

--
-- Dumping data for table `cse11jan2012analysis_cse_hod`
--

INSERT INTO `cse11jan2012analysis_cse_hod` (`index`, `resultant`, `value`) VALUES
(10, 'HighestPercentage', NULL),
(4, 'NoOf1stClasses', NULL),
(3, 'NoOfDistinctions', NULL),
(6, 'NoOfFailures', NULL),
(7, 'NoOfPassCandidates', NULL),
(5, 'NoOfSecondClasses', NULL),
(9, 'OverAllPassPercentage', NULL),
(8, 'TotalNoOfStudents', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cse11jan2012marks_cse_hod`
--

CREATE TABLE IF NOT EXISTS `cse11jan2012marks_cse_hod` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wo455,{*}}',
  `RegdNo` varchar(10) NOT NULL DEFAULT '',
  `ENG1_I` int(2) DEFAULT NULL,
  `ENG1_E` int(3) DEFAULT NULL,
  `ENG1_T` int(2) DEFAULT NULL,
  `ENG1_C` int(1) DEFAULT NULL,
  `M1_I` int(2) DEFAULT NULL,
  `M1_E` int(3) DEFAULT NULL,
  `M1_T` int(1) DEFAULT NULL,
  `M1_C` int(1) DEFAULT NULL,
  `EP1_I` int(2) DEFAULT NULL,
  `EP1_E` int(3) DEFAULT NULL,
  `EP1_T` int(1) DEFAULT NULL,
  `EP1_C` int(1) DEFAULT NULL,
  `EC1_I` int(2) DEFAULT NULL,
  `EC1_E` int(3) DEFAULT NULL,
  `EC1_T` int(1) DEFAULT NULL,
  `EC1_C` int(1) DEFAULT NULL,
  `CP_I` int(2) DEFAULT NULL,
  `CP_E` int(3) DEFAULT NULL,
  `CP_T` int(1) DEFAULT NULL,
  `CP_C` int(1) DEFAULT NULL,
  `MM_I` int(2) DEFAULT NULL,
  `MM_E` int(3) DEFAULT NULL,
  `MM_T` int(1) DEFAULT NULL,
  `MM_C` int(1) DEFAULT NULL,
  `EPECL_I` int(2) DEFAULT NULL,
  `EPECL_E` int(3) DEFAULT NULL,
  `EPECL_T` int(1) DEFAULT NULL,
  `EPECL_C` int(1) DEFAULT NULL,
  `EWSLAB_I` int(2) DEFAULT NULL,
  `EWSLAB_E` int(3) DEFAULT NULL,
  `EWSLAB_T` int(1) DEFAULT NULL,
  `EWSLAB_C` int(1) DEFAULT NULL,
  `CPLAB_I` int(2) DEFAULT NULL,
  `CPLAB_E` int(3) DEFAULT NULL,
  `CPLAB_T` int(1) DEFAULT NULL,
  `CPLAB_C` int(1) DEFAULT NULL,
  `EPLAB_I` int(2) DEFAULT NULL,
  `EPLAB_E` int(3) DEFAULT NULL,
  `EPLAB_T` int(1) DEFAULT NULL,
  `EPLAB_C` int(1) DEFAULT NULL,
  `TOT` int(4) DEFAULT NULL,
  `AVG` float DEFAULT NULL,
  `RANK` int(3) DEFAULT NULL,
  `PorF` enum('PASS','FAIL') DEFAULT NULL,
  PRIMARY KEY (`RegdNo`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:Bz0,o:455' AUTO_INCREMENT=120 ;

--
-- Dumping data for table `cse11jan2012marks_cse_hod`
--

INSERT INTO `cse11jan2012marks_cse_hod` (`index`, `RegdNo`, `ENG1_I`, `ENG1_E`, `ENG1_T`, `ENG1_C`, `M1_I`, `M1_E`, `M1_T`, `M1_C`, `EP1_I`, `EP1_E`, `EP1_T`, `EP1_C`, `EC1_I`, `EC1_E`, `EC1_T`, `EC1_C`, `CP_I`, `CP_E`, `CP_T`, `CP_C`, `MM_I`, `MM_E`, `MM_T`, `MM_C`, `EPECL_I`, `EPECL_E`, `EPECL_T`, `EPECL_C`, `EWSLAB_I`, `EWSLAB_E`, `EWSLAB_T`, `EWSLAB_C`, `CPLAB_I`, `CPLAB_E`, `CPLAB_T`, `CPLAB_C`, `EPLAB_I`, `EPLAB_E`, `EPLAB_T`, `EPLAB_C`, `TOT`, `AVG`, `RANK`, `PorF`) VALUES
(23, '11331A0501', 22, 64, 86, 2, 24, 74, 98, 2, 24, 49, 73, 2, 19, 26, 45, 2, 22, 37, 59, 2, 25, 70, 95, 2, 24, 45, 69, 2, 19, 46, 65, 2, 22, 42, 64, 2, 21, 37, 58, 2, 712, 79.11, 23, 'PASS'),
(87, '11331A0502', 19, 46, 65, 2, 20, 26, 46, NULL, 19, 1, 20, 0, 14, 31, 45, 2, 19, 26, 45, 2, 20, 58, 78, 2, 22, 45, 67, 2, 23, 43, 66, 2, 20, 35, 55, 2, 23, 39, 62, 2, 549, 61, 87, 'FAIL'),
(38, '11331A0503', 23, 40, 63, 2, 23, 57, 80, 2, 24, 50, 74, 2, 23, 33, 56, 2, 21, 48, 69, 2, 23, 46, 69, 2, 23, 49, 72, 2, 23, 47, 70, 2, 23, 45, 68, 2, 22, 41, 63, 2, 684, 76, 39, 'PASS'),
(88, '11331A0504', 9, 40, 49, 2, 18, 42, 60, 2, 15, 28, 43, 2, 11, 42, 53, 2, 16, 50, 66, 2, 15, 29, 44, 2, 24, 44, 68, 2, 19, 40, 59, 2, 15, 30, 45, 2, 20, 38, 58, 2, 545, 60.56, 88, 'PASS'),
(1, '11331A0505', 24, 28, 52, 2, 23, 75, 98, 2, 16, 56, 79, 2, 25, 68, 93, 2, 24, 68, 92, 2, 23, 75, 98, 2, 24, 49, 73, 2, 23, 45, 68, 2, 25, 50, 75, 2, 24, 48, 72, 2, 847, 94.11, 1, 'PASS'),
(44, '11331A0506', 22, 49, 71, 2, 23, 56, 79, 2, 23, 32, 55, 2, 22, 42, 64, 2, 22, 45, 67, 2, 20, 53, 73, 2, 24, 47, 71, 2, 22, 41, 63, 2, 23, 46, 69, 2, 21, 44, 65, 2, 677, 75.22, 45, 'PASS'),
(34, '11331A0507', 20, 42, 62, 2, 22, 75, 97, 2, 22, 60, 82, 2, 17, 40, 57, 2, 20, 48, 68, 2, 23, 49, 72, 2, 25, 43, 68, 2, 22, 43, 65, 2, 20, 35, 55, 2, 21, 41, 62, 2, 688, 76.44, 34, 'PASS'),
(108, '11331A0508', 16, 53, 69, 2, 12, 28, 40, 2, 13, 2, 15, 0, 15, 18, 33, 0, 16, 26, 42, 2, 17, 3, 20, 0, 22, 43, 65, 2, 23, 39, 62, 2, 18, 31, 49, 2, 20, 38, 58, 2, 453, 50.33, 108, 'FAIL'),
(90, '11331A0509', 21, 74, 95, 2, 13, 4, 17, NULL, 18, 33, 51, 2, 17, 31, 48, 2, 19, 31, 50, 2, 19, 30, 49, 2, 24, 44, 68, 2, 23, 40, 63, 2, 19, 14, 33, 0, 20, 44, 64, 2, 538, 59.78, 91, 'FAIL'),
(71, '11331A0510', 22, 49, 71, 2, 23, 51, 74, 2, 22, 31, 53, 2, 20, 34, 54, 2, 22, 33, 55, 2, 20, 40, 60, 2, 23, 47, 70, 2, 21, 40, 61, 2, 18, 40, 58, 2, 24, 43, 67, 2, 623, 69.22, 71, 'PASS'),
(10, '11331A0511', 23, 44, 67, 2, 25, 75, 100, 2, 17, 56, 81, 2, 22, 43, 65, 2, 25, 48, 73, 2, 24, 66, 90, 2, 25, 49, 74, 2, 22, 48, 70, 2, 22, 45, 67, 2, 24, 46, 70, 2, 757, 84.11, 10, 'PASS'),
(22, '11331A0512', 22, 57, 79, 2, 24, 73, 97, 2, 24, 34, 58, 2, 23, 41, 64, 2, 24, 49, 73, 2, 25, 50, 75, 2, 23, 48, 71, 2, 22, 45, 67, 2, 22, 40, 62, 2, 24, 46, 70, 2, 716, 79.56, 22, 'PASS'),
(66, '11331A0513', 19, 58, 77, 2, 18, 36, 54, 2, 20, 37, 57, 2, 19, 47, 66, 2, 22, 31, 53, 2, 20, 47, 67, 2, 24, 46, 70, 2, 23, 44, 67, 2, 18, 38, 56, 2, 20, 42, 62, 2, 629, 69.89, 66, 'PASS'),
(5, '11331A0514', 25, 61, 86, 2, 23, 62, 85, 2, 10, 60, 84, 2, 25, 53, 78, 2, 24, 50, 74, 2, 25, 61, 86, 2, 24, 49, 73, 2, 21, 48, 69, 2, 25, 50, 75, 2, 23, 48, 71, 2, 781, 86.78, 5, 'PASS'),
(17, '11331A0515', 23, 38, 61, 2, 24, 74, 98, 2, 25, 66, 91, 2, 23, 42, 65, 2, 22, 52, 74, 2, 25, 56, 81, 2, 24, 45, 69, 2, 20, 45, 65, 2, 22, 36, 58, 2, 22, 44, 66, 2, 728, 80.89, 17, 'PASS'),
(50, '11331A0516', 21, 66, 87, 2, 22, 69, 91, 2, 19, 29, 48, 2, 20, 39, 59, 2, 19, 44, 63, 2, 19, 34, 53, 2, 25, 47, 72, 2, 21, 44, 65, 2, 19, 33, 52, 2, 24, 46, 70, 2, 660, 73.33, 50, 'PASS'),
(80, '11331A0517', 19, 34, 53, 2, 18, 62, 80, 2, 18, 26, 44, 2, 18, 12, 30, 0, 17, 38, 55, 2, 19, 52, 71, 2, 24, 49, 73, 2, 20, 44, 64, 2, 18, 32, 50, 2, 21, 41, 62, 2, 582, 64.67, 80, 'FAIL'),
(100, '11331A0518', 18, 45, 63, 2, 19, 26, 45, NULL, 22, 13, 35, 0, 15, 28, 43, 2, 19, 14, 33, 0, 21, 29, 50, 2, 24, 45, 69, 2, 15, 41, 56, 2, 16, 30, 46, 2, 20, 41, 61, 2, 501, 55.67, 100, 'FAIL'),
(91, '11331A0519', 21, 40, 61, 2, 17, 26, 43, NULL, 21, 45, 66, 2, 9, 18, 27, 0, 18, 31, 49, 2, 19, 26, 45, 2, 23, 45, 68, 2, 22, 42, 64, 2, 18, 38, 56, 2, 23, 36, 59, 2, 538, 59.78, 91, 'FAIL'),
(109, '11331A0520', 18, 60, 78, 2, 15, 32, 47, 2, 15, 2, 17, 0, 9, 14, 23, 0, 14, 26, 40, 2, 17, 26, 43, 2, 22, 44, 66, 2, 19, 39, 58, 2, 12, 15, 27, 0, 20, 33, 53, 2, 452, 50.22, 109, 'FAIL'),
(55, '11331A0521', 19, 57, 76, 2, 15, 40, 55, 2, 22, 50, 72, 2, 20, 44, 64, 2, 21, 37, 58, 2, 18, 55, 73, 2, 24, 43, 67, 2, 22, 44, 66, 2, 21, 38, 59, 2, 21, 38, 59, 2, 649, 72.11, 55, 'PASS'),
(97, '11331A0522', 16, 46, 62, 2, 19, 37, 56, 2, 21, 26, 47, 2, 16, 17, 33, 0, 20, 26, 46, 2, 17, 27, 44, 2, 23, 41, 64, 2, 22, 39, 61, 2, 19, 30, 49, 2, 19, 36, 55, 2, 517, 57.44, 97, 'FAIL'),
(35, '11331A0523', 22, 42, 64, 2, 23, 57, 80, 2, 22, 55, 77, 2, 24, 39, 63, 2, 22, 48, 70, 2, 24, 50, 74, 2, 23, 47, 70, 2, 22, 47, 69, 2, 21, 38, 59, 2, 21, 38, 59, 2, 685, 76.11, 37, 'PASS'),
(33, '11331A0525', 23, 63, 86, 2, 24, 68, 92, 2, 21, 40, 61, 2, 24, 40, 64, 2, 21, 53, 74, 2, 22, 45, 67, 2, 25, 46, 71, 2, 22, 41, 63, 2, 21, 40, 61, 2, 23, 36, 59, 2, 698, 77.56, 33, 'PASS'),
(45, '11331A0526', 22, 57, 79, 2, 25, 75, 100, 2, 22, 36, 58, 2, 20, 16, 36, 0, 23, 42, 65, 2, 22, 60, 82, 2, 23, 44, 67, 2, 23, 45, 68, 2, 23, 41, 64, 2, 21, 37, 58, 2, 677, 75.22, 45, 'FAIL'),
(16, '11331A0527', 24, 53, 77, 2, 25, 68, 93, 2, 25, 38, 63, 2, 25, 38, 63, 2, 24, 38, 62, 2, 25, 73, 98, 2, 23, 46, 69, 2, 23, 45, 68, 2, 24, 44, 68, 2, 24, 46, 70, 2, 731, 81.22, 16, 'PASS'),
(75, '11331A0528', 19, 43, 62, 2, 14, 36, 50, 2, 24, 43, 67, 2, 19, 33, 52, 2, 23, 42, 65, 2, 19, 36, 55, 2, 25, 44, 69, 2, 20, 44, 64, 2, 23, 34, 57, 2, 20, 40, 60, 2, 601, 66.78, 75, 'PASS'),
(56, '11331A0529', 19, 67, 86, 2, 15, 48, 63, 2, 23, 46, 69, 2, 22, 44, 66, 2, 22, 47, 69, 2, 18, 27, 45, 2, 23, 43, 66, 2, 22, 47, 69, 2, 18, 34, 52, 2, 23, 40, 63, 2, 648, 72, 56, 'PASS'),
(84, '11331A0530', 19, 43, 62, 2, 17, 36, 53, 2, 20, 15, 35, 0, 19, 17, 36, 0, 19, 48, 67, 2, 20, 47, 67, 2, 24, 44, 68, 2, 20, 41, 61, 2, 18, 38, 56, 2, 20, 36, 56, 2, 561, 62.33, 84, 'FAIL'),
(68, '11331A0531', 24, 50, 74, 2, 21, 59, 80, 2, 24, 26, 50, 2, 21, 30, 51, 2, 21, 39, 60, 2, 21, 43, 64, 2, 21, 42, 63, 2, 22, 44, 66, 2, 22, 38, 60, 2, 21, 36, 57, 2, 625, 69.44, 70, 'PASS'),
(53, '11331A0532', 22, 40, 62, 2, 21, 54, 75, 2, 24, 55, 79, 2, 21, 37, 58, 2, 23, 45, 68, 2, 20, 35, 55, 2, 23, 42, 65, 2, 24, 42, 66, 2, 20, 40, 60, 2, 23, 43, 66, 2, 654, 72.67, 53, 'PASS'),
(118, '11331A0533', 7, 33, 40, 2, 9, 0, 9, NULL, 12, 4, 16, 0, 4, 20, 24, 0, 6, 3, 9, 0, 15, 2, 17, 0, 19, 36, 55, 2, 18, 44, 62, 2, 8, 15, 23, 0, 20, 31, 51, 2, 306, 34, 118, 'FAIL'),
(2, '11331A0534', 24, 71, 95, 2, 24, 75, 99, 2, 11, 62, 85, 2, 25, 40, 65, 2, 24, 75, 99, 2, 24, 70, 94, 2, 24, 43, 67, 2, 24, 47, 71, 2, 25, 49, 74, 2, 21, 44, 65, 2, 814, 90.44, 2, 'PASS'),
(106, '11331A0535', 15, 47, 62, 2, 12, 28, 40, 2, 18, 13, 31, 0, 12, 17, 29, 0, 16, 8, 24, 0, 15, 29, 44, 2, 21, 36, 57, 2, 21, 42, 63, 2, 17, 37, 54, 2, 22, 36, 58, 2, 462, 51.33, 106, 'FAIL'),
(9, '11331A0536', 23, 37, 60, 2, 21, 75, 96, 2, 16, 57, 82, 2, 24, 46, 70, 2, 24, 65, 89, 2, 25, 58, 83, 2, 25, 47, 72, 2, 24, 45, 69, 2, 25, 48, 73, 2, 23, 46, 69, 2, 763, 84.78, 9, 'PASS'),
(86, '11331A0537', 22, 46, 68, 2, 15, 19, 34, NULL, 19, 37, 56, 2, 17, 29, 46, 2, 18, 34, 52, 2, 18, 58, 76, 2, 23, 35, 58, 2, 15, 44, 59, 2, 13, 30, 43, 2, 23, 40, 63, 2, 555, 61.67, 86, 'FAIL'),
(3, '11331A0538', 22, 66, 88, 2, 24, 75, 99, 2, 9, 65, 88, 2, 21, 43, 64, 2, 22, 64, 86, 2, 25, 75, 100, 2, 25, 48, 73, 2, 24, 42, 66, 2, 25, 48, 73, 2, 24, 41, 65, 2, 802, 89.11, 3, 'PASS'),
(111, '11331A0539', 16, 48, 64, 2, 11, 29, 40, 2, 18, 5, 23, 0, 14, 29, 43, 2, 12, 11, 23, 0, 11, 29, 40, 2, 22, 39, 61, 2, 16, 40, 56, 2, 13, 20, 33, 2, 22, 38, 60, 2, 443, 49.22, 111, 'FAIL'),
(48, '11331A0540', 20, 36, 56, 2, 22, 74, 96, 2, 24, 48, 72, 2, 20, 42, 62, 2, 20, 35, 55, 2, 21, 56, 77, 2, 23, 44, 67, 2, 21, 43, 64, 2, 21, 40, 61, 2, 23, 38, 61, 2, 671, 74.56, 48, 'PASS'),
(58, '11331A0541', 19, 52, 71, 2, 21, 62, 83, 2, 23, 40, 63, 2, 19, 45, 64, 2, 20, 42, 62, 2, 21, 29, 50, 2, 24, 47, 71, 2, 21, 46, 67, 2, 21, 38, 59, 2, 20, 34, 54, 2, 644, 71.56, 58, 'PASS'),
(76, '11331A0542', 21, 53, 74, 2, 17, 48, 65, 2, 17, 33, 50, 2, 19, 33, 52, 2, 20, 31, 51, 2, 16, 35, 51, 2, 23, 46, 69, 2, 19, 43, 62, 2, 21, 40, 61, 2, 23, 35, 58, 2, 593, 65.89, 76, 'PASS'),
(30, '11331A0543', 24, 57, 81, 2, 24, 63, 87, 2, 25, 50, 75, 2, 23, 35, 58, 2, 24, 26, 50, 2, 24, 58, 82, 2, 24, 48, 72, 2, 22, 41, 63, 2, 24, 44, 68, 2, 24, 40, 64, 2, 700, 77.78, 32, 'PASS'),
(19, '11331A0544', 23, 42, 65, 2, 22, 69, 91, 2, 25, 58, 83, 2, 23, 37, 60, 2, 21, 58, 79, 2, 24, 54, 78, 2, 24, 46, 70, 2, 24, 44, 68, 2, 23, 41, 64, 2, 24, 43, 67, 2, 725, 80.56, 19, 'PASS'),
(114, '11331A0545', 13, 35, 48, 2, 14, 41, 55, 2, 15, 6, 21, 0, 12, 8, 20, 0, 14, 8, 22, 0, 20, 26, 46, 2, 22, 34, 56, 2, 16, 44, 60, 2, 8, 20, 28, 0, 20, 31, 51, 2, 407, 45.22, 114, 'FAIL'),
(94, '11331A0546', 18, 55, 73, 2, 13, 38, 51, 2, 20, 32, 52, 2, 17, 7, 24, 0, 16, 41, 57, 2, 16, 30, 46, 2, 23, 49, 72, 2, 22, 37, 59, 2, 12, 25, 37, 2, 20, 39, 59, 2, 530, 58.89, 94, 'FAIL'),
(93, '11331A0547', 21, 52, 73, 2, 20, 37, 57, 2, 21, 11, 32, 0, 18, 35, 53, 2, 18, 26, 44, 2, 21, 36, 57, 2, 22, 48, 70, 2, 20, 42, 62, 2, 15, 14, 29, 0, 19, 36, 55, 2, 532, 59.11, 93, 'FAIL'),
(18, '11331A0548', 22, 40, 62, 2, 24, 75, 99, 2, 25, 53, 78, 2, 23, 34, 57, 2, 23, 64, 87, 2, 21, 60, 81, 2, 24, 43, 67, 2, 23, 44, 67, 2, 24, 41, 65, 2, 23, 41, 64, 2, 727, 80.78, 18, 'PASS'),
(73, '11331A0549', 20, 41, 61, 2, 21, 58, 79, 2, 23, 43, 66, 2, 17, 36, 53, 2, 19, 57, 76, 2, 19, 14, 33, 0, 23, 45, 68, 2, 18, 43, 61, 2, 17, 35, 52, 2, 23, 37, 60, 2, 609, 67.67, 73, 'FAIL'),
(24, '11331A0550', 22, 57, 79, 2, 21, 75, 96, 2, 24, 38, 62, 2, 20, 35, 55, 2, 23, 52, 75, 2, 21, 61, 82, 2, 24, 44, 68, 2, 21, 47, 68, 2, 23, 40, 63, 2, 23, 40, 63, 2, 711, 79, 25, 'PASS'),
(4, '11331A0551', 23, 55, 78, 2, 25, 65, 90, 2, 18, 53, 78, 2, 25, 63, 88, 2, 23, 46, 69, 2, 25, 71, 96, 2, 25, 47, 72, 2, 21, 46, 67, 2, 25, 50, 75, 2, 24, 42, 66, 2, 779, 86.56, 4, 'PASS'),
(47, '11331A0552', 23, 40, 63, 2, 24, 70, 94, 2, 25, 40, 65, 2, 20, 40, 60, 2, 23, 53, 76, 2, 21, 35, 56, 2, 24, 45, 69, 2, 20, 41, 61, 2, 23, 44, 67, 2, 23, 38, 61, 2, 672, 74.67, 47, 'PASS'),
(103, '11331A0553', 15, 50, 65, 2, 15, 3, 18, NULL, 19, 32, 51, 2, 13, 20, 33, 0, 19, 53, 72, 2, 21, 3, 24, 0, 23, 42, 65, 2, 17, 43, 60, 2, 14, 28, 42, 2, 18, 36, 54, 2, 484, 53.78, 103, 'FAIL'),
(49, '11331A0554', 22, 49, 71, 2, 19, 68, 87, 2, 24, 42, 66, 2, 20, 34, 54, 2, 19, 48, 67, 2, 19, 53, 72, 2, 25, 48, 73, 2, 21, 41, 62, 2, 20, 36, 56, 2, 21, 37, 58, 2, 666, 74, 49, 'PASS'),
(40, '11331A0555', 22, 50, 72, 2, 22, 51, 73, 2, 24, 43, 67, 2, 19, 44, 63, 2, 21, 46, 67, 2, 22, 71, 93, 2, 25, 42, 67, 2, 20, 42, 62, 2, 20, 40, 60, 2, 20, 37, 57, 2, 681, 75.67, 40, 'PASS'),
(39, '11331A0556', 23, 32, 55, 2, 23, 75, 98, 2, 24, 44, 68, 2, 21, 36, 57, 2, 22, 52, 74, 2, 21, 59, 80, 2, 25, 44, 69, 2, 19, 40, 59, 2, 20, 40, 60, 2, 23, 41, 64, 2, 684, 76, 39, 'PASS'),
(57, '11331A0557', 21, 54, 75, 2, 23, 60, 83, 2, 22, 29, 51, 2, 21, 41, 62, 2, 17, 51, 68, 2, 20, 38, 58, 2, 24, 45, 69, 2, 19, 41, 60, 2, 21, 37, 58, 2, 21, 40, 61, 2, 645, 71.67, 57, 'PASS'),
(28, '11331A0558', 22, 70, 92, 2, 20, 69, 89, 2, 25, 48, 73, 2, 22, 43, 65, 2, 19, 47, 66, 2, 21, 35, 56, 2, 25, 45, 70, 2, 21, 45, 66, 2, 21, 36, 57, 2, 23, 45, 68, 2, 702, 78, 28, 'PASS'),
(59, '11331A0559', 20, 41, 61, 2, 21, 60, 81, 2, 21, 41, 62, 2, 22, 41, 63, 2, 17, 45, 62, 2, 19, 56, 75, 2, 24, 43, 67, 2, 23, 39, 62, 2, 20, 37, 57, 2, 20, 33, 53, 2, 643, 71.44, 59, 'PASS'),
(52, '11331A0560', 21, 38, 59, 2, 23, 71, 94, 2, 23, 36, 59, 2, 18, 37, 55, 2, 20, 51, 71, 2, 21, 50, 71, 2, 25, 46, 71, 2, 19, 38, 57, 2, 20, 36, 56, 2, 23, 41, 64, 2, 657, 73, 52, 'PASS'),
(67, '11331A0561', 18, 52, 70, 2, 20, 49, 69, 2, 22, 40, 62, 2, 23, 39, 62, 2, 21, 44, 65, 2, 22, 27, 49, 2, 23, 44, 67, 2, 22, 42, 64, 2, 20, 38, 58, 2, 22, 40, 62, 2, 628, 69.78, 67, 'PASS'),
(64, '11331A0562', 19, 56, 75, 2, 22, 58, 80, 2, 20, 32, 52, 2, 18, 11, 29, 0, 20, 38, 58, 2, 24, 51, 75, 2, 24, 48, 72, 2, 23, 42, 65, 2, 20, 38, 58, 2, 23, 44, 67, 2, 631, 70.11, 64, 'FAIL'),
(72, '11331A0563', 21, 49, 70, 2, 23, 30, 53, 2, 22, 38, 60, 2, 21, 38, 59, 2, 23, 27, 50, 2, 23, 51, 74, 2, 23, 41, 64, 2, 23, 41, 64, 2, 22, 41, 63, 2, 22, 40, 62, 2, 619, 68.78, 72, 'PASS'),
(21, '11331A0564', 19, 41, 60, 2, 24, 75, 99, 2, 22, 46, 68, 2, 24, 44, 68, 2, 22, 63, 85, 2, 21, 54, 75, 2, 25, 46, 71, 2, 23, 43, 66, 2, 22, 40, 62, 2, 21, 43, 64, 2, 718, 79.78, 21, 'PASS'),
(107, '11331A0565', 14, 32, 46, 2, 9, 31, 40, 2, 17, 31, 48, 2, 11, 17, 28, 0, 12, 33, 45, 2, 17, 29, 46, 2, 21, 41, 62, 2, 16, 40, 56, 2, 13, 14, 27, 0, 19, 38, 57, 2, 455, 50.56, 107, 'FAIL'),
(78, '11331A0566', 19, 63, 82, 2, 15, 29, 44, 2, 22, 42, 64, 2, 20, 42, 62, 2, 19, 47, 66, 2, 19, 12, 31, 0, 23, 46, 69, 2, 23, 43, 66, 2, 18, 30, 48, 2, 20, 36, 56, 2, 588, 65.33, 78, 'FAIL'),
(81, '11331A0567', 20, 50, 70, 2, 19, 27, 46, NULL, 24, 26, 50, 2, 21, 33, 54, 2, 18, 28, 46, 2, 21, 42, 63, 2, 23, 43, 66, 2, 22, 41, 63, 2, 19, 35, 54, 2, 23, 44, 67, 2, 579, 64.33, 81, 'FAIL'),
(12, '11331A0568', 20, 41, 61, 2, 22, 67, 89, 2, 24, 61, 85, 2, 25, 44, 69, 2, 22, 70, 92, 2, 24, 64, 88, 2, 25, 42, 67, 2, 24, 38, 62, 2, 22, 40, 62, 2, 21, 43, 64, 2, 739, 82.11, 13, 'PASS'),
(15, '11331A0569', 22, 54, 76, 2, 22, 69, 91, 2, 24, 60, 84, 2, 22, 41, 63, 2, 21, 48, 69, 2, 21, 70, 91, 2, 25, 47, 72, 2, 20, 39, 59, 2, 22, 40, 62, 2, 21, 44, 65, 2, 732, 81.33, 15, 'PASS'),
(20, '11331A0570', 23, 71, 94, 2, 22, 60, 82, 2, 24, 53, 77, 2, 22, 37, 59, 2, 22, 57, 79, 2, 20, 45, 65, 2, 25, 50, 75, 2, 24, 41, 65, 2, 22, 40, 62, 2, 21, 44, 65, 2, 723, 80.33, 20, 'PASS'),
(104, '11331A0571', 16, 45, 61, 2, 20, 39, 59, 2, 14, 4, 18, 0, 16, 26, 42, 2, 17, 14, 31, 0, 16, 33, 49, 2, 21, 42, 63, 2, 19, 40, 59, 2, 14, 15, 29, 0, 22, 40, 62, 2, 473, 52.56, 104, 'FAIL'),
(89, '11331A0572', 19, 35, 54, 2, 16, 34, 50, 2, 21, 27, 48, 2, 17, 26, 43, 2, 19, 26, 45, 2, 20, 40, 60, 2, 24, 44, 68, 2, 19, 39, 58, 2, 21, 38, 59, 2, 20, 36, 56, 2, 541, 60.11, 89, 'PASS'),
(41, '11331A0573', 23, 58, 81, 2, 19, 58, 77, 2, 23, 41, 64, 2, 23, 30, 53, 2, 24, 40, 64, 2, 22, 55, 77, 2, 24, 48, 72, 2, 21, 40, 61, 2, 22, 40, 62, 2, 21, 47, 68, 2, 679, 75.44, 41, 'PASS'),
(36, '11331A0574', 20, 50, 70, 2, 18, 52, 70, 2, 24, 50, 74, 2, 24, 53, 77, 2, 24, 49, 73, 2, 21, 46, 67, 2, 24, 46, 70, 2, 20, 40, 60, 2, 21, 35, 56, 2, 23, 45, 68, 2, 685, 76.11, 37, 'PASS'),
(6, '11331A0575', 25, 52, 77, 2, 25, 62, 87, 2, 18, 52, 76, 2, 24, 57, 81, 2, 25, 50, 75, 2, 25, 75, 100, 2, 25, 49, 74, 2, 18, 42, 60, 2, 23, 48, 71, 2, 25, 46, 71, 2, 772, 85.78, 6, 'PASS'),
(31, '11331A0576', 22, 39, 61, 2, 24, 75, 99, 2, 21, 51, 72, 2, 22, 33, 55, 2, 19, 53, 72, 2, 23, 61, 84, 2, 24, 44, 68, 2, 21, 44, 65, 2, 20, 40, 60, 2, 22, 42, 64, 2, 700, 77.78, 32, 'PASS'),
(96, '11331A0577', 18, 51, 69, 2, 12, 43, 55, 2, 18, 32, 50, 2, 15, 32, 47, 2, 18, 32, 50, 2, 16, 30, 46, 2, 21, 39, 60, 2, 19, 41, 60, 2, 12, 16, 28, 0, 20, 40, 60, 2, 525, 58.33, 96, 'FAIL'),
(102, '11331A0578', 18, 29, 47, 2, 14, 26, 40, NULL, 22, 26, 48, 2, 14, 26, 40, 2, 14, 26, 40, 2, 19, 17, 36, 0, 20, 45, 65, 2, 21, 38, 59, 2, 14, 35, 49, 2, 20, 41, 61, 2, 485, 53.89, 102, 'FAIL'),
(32, '11331A0579', 22, 59, 81, 2, 24, 46, 70, 2, 25, 47, 72, 2, 24, 58, 82, 2, 22, 44, 66, 2, 20, 46, 66, 2, 25, 45, 70, 2, 23, 43, 66, 2, 23, 41, 64, 2, 22, 41, 63, 2, 700, 77.78, 32, 'PASS'),
(61, '11331A0580', 20, 42, 62, 2, 19, 41, 60, 2, 25, 27, 52, 2, 23, 26, 49, 2, 24, 36, 60, 2, 23, 53, 76, 2, 25, 50, 75, 2, 22, 47, 69, 2, 23, 46, 69, 2, 21, 45, 66, 2, 638, 70.89, 61, 'PASS'),
(42, '11331A0581', 23, 54, 77, 2, 20, 57, 77, 2, 23, 47, 70, 2, 22, 51, 73, 2, 21, 50, 71, 2, 20, 17, 37, 0, 24, 46, 70, 2, 22, 41, 63, 2, 23, 50, 73, 2, 22, 45, 67, 2, 678, 75.33, 43, 'FAIL'),
(63, '11331A0582', 20, 45, 65, 2, 22, 75, 97, 2, 22, 45, 67, 2, 21, 29, 50, 2, 18, 40, 58, 2, 18, 32, 50, 2, 23, 47, 70, 2, 21, 40, 61, 2, 20, 30, 50, 2, 22, 44, 66, 2, 634, 70.44, 63, 'PASS'),
(98, '11331A0583', 17, 37, 54, 2, 15, 30, 45, 2, 20, 26, 46, 2, 17, 29, 46, 2, 17, 26, 43, 2, 20, 37, 57, 2, 23, 39, 62, 2, 22, 40, 62, 2, 12, 32, 44, 2, 19, 38, 57, 2, 516, 57.33, 98, 'PASS'),
(43, '11331A0584', 23, 35, 58, 2, 20, 52, 72, 2, 25, 46, 71, 2, 23, 43, 66, 2, 22, 45, 67, 2, 23, 45, 68, 2, 23, 48, 71, 2, 24, 45, 69, 2, 25, 41, 66, 2, 24, 46, 70, 2, 678, 75.33, 43, 'PASS'),
(110, '11331A0585', 16, 50, 66, 2, 15, 43, 58, 2, 16, 6, 22, 0, 13, 17, 30, 0, 9, 9, 18, 0, 16, 34, 50, 2, 20, 44, 64, 2, 16, 42, 58, 2, 10, 14, 24, 0, 19, 42, 61, 2, 451, 50.11, 110, 'FAIL'),
(85, '11331A0586', 17, 69, 86, 2, 13, 35, 48, 2, 17, 26, 43, 2, 16, 16, 32, 0, 19, 32, 51, 2, 13, 39, 52, 2, 22, 45, 67, 2, 23, 41, 64, 2, 20, 38, 58, 2, 20, 38, 58, 2, 559, 62.11, 85, 'FAIL'),
(79, '11331A0587', 19, 44, 63, 2, 24, 50, 74, 2, 23, 15, 38, 0, 20, 35, 55, 2, 21, 33, 54, 2, 20, 44, 64, 2, 24, 43, 67, 2, 19, 40, 59, 2, 19, 35, 54, 2, 19, 37, 56, 2, 584, 64.89, 79, 'FAIL'),
(65, '11331A0588', 23, 40, 63, 2, 22, 44, 66, 2, 22, 47, 69, 2, 18, 26, 44, 2, 20, 32, 52, 2, 22, 61, 83, 2, 23, 45, 68, 2, 22, 41, 63, 2, 19, 40, 59, 2, 20, 43, 63, 2, 630, 70, 65, 'PASS'),
(13, '11331A0589', 21, 60, 81, 2, 25, 72, 97, 2, 24, 54, 78, 2, 20, 39, 59, 2, 22, 43, 65, 2, 22, 71, 93, 2, 24, 48, 72, 2, 22, 43, 65, 2, 24, 42, 66, 2, 21, 42, 63, 2, 739, 82.11, 13, 'PASS'),
(99, '11331A0590', 20, 55, 75, 2, 14, 26, 40, NULL, 19, 26, 45, 2, 19, 8, 27, 0, 18, 35, 53, 2, 18, 16, 34, 0, 23, 46, 69, 2, 16, 41, 57, 2, 19, 30, 49, 2, 19, 42, 61, 2, 510, 56.67, 99, 'FAIL'),
(77, '11331A0591', 20, 45, 65, 2, 19, 43, 62, 2, 22, 12, 34, 0, 22, 29, 51, 2, 22, 34, 56, 2, 19, 50, 69, 2, 24, 45, 69, 2, 21, 42, 63, 2, 21, 35, 56, 2, 22, 44, 66, 2, 591, 65.67, 77, 'FAIL'),
(113, '11331A0592', 18, 40, 58, 2, 15, 12, 27, NULL, 17, 13, 30, 0, 15, 8, 23, 0, 16, 12, 28, 0, 17, 10, 27, 0, 20, 42, 62, 2, 22, 39, 61, 2, 12, 27, 39, 2, 19, 36, 55, 2, 410, 45.56, 113, 'FAIL'),
(27, '11331A0593', 22, 55, 77, 2, 22, 73, 95, 2, 23, 49, 72, 2, 18, 36, 54, 2, 22, 32, 54, 2, 22, 58, 80, 2, 25, 48, 73, 2, 23, 47, 70, 2, 21, 39, 60, 2, 24, 45, 69, 2, 704, 78.22, 27, 'PASS'),
(117, '11331A0594', 13, 38, 51, 2, 10, 11, 21, NULL, 13, 11, 24, 0, 7, 4, 11, 0, 13, 3, 16, 0, 12, 28, 40, 2, 19, 40, 59, 2, 14, 37, 51, 2, 12, 13, 25, 0, 20, 31, 51, 2, 349, 38.78, 117, 'FAIL'),
(119, '11331A0595', 12, 30, 42, 2, 8, 0, 8, NULL, 12, 2, 14, 0, 9, 4, 13, 0, 11, 3, 14, 0, 9, 7, 16, 0, 17, 41, 58, 2, 14, 38, 52, 2, 12, 11, 23, 0, 20, 31, 51, 2, 291, 32.33, 119, 'FAIL'),
(105, '11331A0596', 18, 45, 63, 2, 14, 9, 23, NULL, 18, 28, 46, 2, 20, 15, 35, 0, 18, 9, 27, 0, 18, 26, 44, 2, 20, 40, 60, 2, 22, 43, 65, 2, 14, 30, 44, 2, 22, 37, 59, 2, 466, 51.78, 105, 'FAIL'),
(92, '11331A0597', 19, 40, 59, 2, 17, 33, 50, 2, 19, 31, 50, 2, 16, 34, 50, 2, 18, 26, 44, 2, 20, 33, 53, 2, 24, 42, 66, 2, 20, 41, 61, 2, 18, 28, 46, 2, 19, 39, 58, 2, 537, 59.67, 92, 'PASS'),
(8, '11331A0598', 25, 55, 80, 2, 25, 75, 100, 2, 11, 50, 74, 2, 24, 50, 74, 2, 23, 54, 77, 2, 21, 57, 78, 2, 24, 47, 71, 2, 23, 44, 67, 2, 24, 50, 74, 2, 23, 46, 69, 2, 764, 84.89, 8, 'PASS'),
(101, '11331A0599', 16, 55, 71, 2, 11, 13, 24, NULL, 17, 11, 28, 0, 13, 38, 51, 2, 13, 28, 41, 2, 14, 27, 41, 2, 23, 44, 67, 2, 22, 43, 65, 2, 13, 34, 47, 2, 23, 41, 64, 2, 499, 55.44, 101, 'FAIL'),
(46, '11331A05A0', 22, 34, 56, 2, 23, 75, 98, 2, 24, 51, 75, 2, 21, 38, 59, 2, 22, 36, 58, 2, 22, 45, 67, 2, 24, 46, 70, 2, 22, 44, 66, 2, 22, 40, 62, 2, 23, 42, 65, 2, 676, 75.11, 46, 'PASS'),
(69, '11331A05A1', 16, 37, 53, 2, 18, 52, 70, 2, 23, 53, 76, 2, 20, 40, 60, 2, 20, 36, 56, 2, 22, 48, 70, 2, 24, 36, 60, 2, 22, 42, 64, 2, 20, 40, 60, 2, 22, 34, 56, 2, 625, 69.44, 70, 'PASS'),
(74, '11331A05A2', 21, 40, 61, 2, 22, 42, 64, 2, 23, 32, 55, 2, 21, 36, 57, 2, 20, 41, 61, 2, 21, 37, 58, 2, 24, 44, 68, 2, 23, 44, 67, 2, 20, 37, 57, 2, 22, 35, 57, 2, 605, 67.22, 74, 'PASS'),
(112, '11331A05A3', 18, 48, 66, 2, 11, 15, 26, NULL, 16, 0, 16, 0, 16, 34, 50, 2, 18, 10, 28, 0, 15, 6, 21, 0, 22, 39, 61, 2, 22, 40, 62, 2, 17, 30, 47, 2, 20, 39, 59, 2, 436, 48.44, 112, 'FAIL'),
(26, '11331A05A4', 24, 43, 67, 2, 23, 66, 89, 2, 24, 57, 81, 2, 24, 43, 67, 2, 23, 43, 66, 2, 22, 50, 72, 2, 24, 47, 71, 2, 22, 42, 64, 2, 23, 42, 65, 2, 24, 41, 65, 2, 707, 78.56, 26, 'PASS'),
(70, '11331A05A5', 19, 46, 65, 2, 24, 62, 86, 2, 19, 43, 62, 2, 20, 28, 48, 2, 21, 32, 53, 2, 21, 51, 72, 2, 23, 45, 68, 2, 23, 43, 66, 2, 20, 30, 50, 2, 21, 34, 55, 2, 625, 69.44, 70, 'PASS'),
(25, '11331A05A6', 19, 53, 72, 2, 24, 75, 99, 2, 21, 58, 79, 2, 23, 43, 66, 2, 21, 33, 54, 2, 25, 54, 79, 2, 25, 48, 73, 2, 22, 40, 62, 2, 21, 45, 66, 2, 23, 38, 61, 2, 711, 79, 25, 'PASS'),
(54, '11331A05A7', 21, 60, 81, 2, 22, 40, 62, 2, 22, 32, 54, 2, 19, 36, 55, 2, 20, 54, 74, 2, 21, 46, 67, 2, 24, 44, 68, 2, 23, 38, 61, 2, 23, 47, 70, 2, 23, 38, 61, 2, 653, 72.56, 54, 'PASS'),
(83, '11331A05A8', 21, 41, 62, 2, 17, 29, 46, 2, 24, 34, 58, 2, 20, 32, 52, 2, 21, 26, 47, 2, 22, 33, 55, 2, 24, 41, 65, 2, 23, 40, 63, 2, 20, 41, 61, 2, 21, 38, 59, 2, 568, 63.11, 83, 'PASS'),
(62, '11331A05A9', 22, 56, 78, 2, 19, 36, 55, 2, 22, 37, 59, 2, 19, 28, 47, 2, 20, 43, 63, 2, 24, 50, 74, 2, 23, 43, 66, 2, 22, 42, 64, 2, 22, 44, 66, 2, 24, 41, 65, 2, 637, 70.78, 62, 'PASS'),
(82, '11331A05B0', 22, 52, 74, 2, 22, 35, 57, 2, 22, 26, 48, 2, 19, 29, 48, 2, 19, 33, 52, 2, 22, 28, 50, 2, 21, 44, 65, 2, 22, 44, 66, 2, 19, 30, 49, 2, 23, 39, 62, 2, 571, 63.44, 82, 'PASS'),
(11, '11331A05B1', 23, 58, 81, 2, 24, 58, 82, 2, 18, 47, 71, 2, 25, 45, 70, 2, 24, 51, 75, 2, 25, 67, 92, 2, 25, 48, 73, 2, 23, 45, 68, 2, 25, 50, 75, 2, 22, 47, 69, 2, 756, 84, 11, 'PASS'),
(7, '11331A05B2', 24, 43, 67, 2, 25, 75, 100, 2, 16, 68, 93, 2, 22, 46, 68, 2, 25, 42, 67, 2, 25, 68, 93, 2, 24, 47, 71, 2, 22, 45, 67, 2, 24, 47, 71, 2, 23, 46, 69, 2, 766, 85.11, 7, 'PASS'),
(51, '11331A05B3', 23, 56, 79, 2, 19, 53, 72, 2, 23, 36, 59, 2, 18, 40, 58, 2, 20, 49, 69, 2, 19, 38, 57, 2, 25, 43, 68, 2, 23, 43, 66, 2, 24, 41, 65, 2, 24, 42, 66, 2, 659, 73.22, 51, 'PASS'),
(116, '11331A05B4', 15, 18, 33, NULL, 12, 10, 22, NULL, 17, 4, 21, 0, 15, 13, 28, 0, 15, 4, 19, 0, 14, 28, 42, 2, 19, 36, 55, 2, 14, 38, 52, 2, 12, 14, 26, 0, 19, 38, 57, 2, 355, 39.44, 116, 'FAIL'),
(14, '11331A05B5', 22, 60, 82, 2, 25, 47, 72, 2, 22, 58, 80, 2, 23, 53, 76, 2, 20, 41, 61, 2, 25, 69, 94, 2, 24, 46, 70, 2, 23, 44, 67, 2, 25, 47, 72, 2, 24, 39, 63, 2, 737, 81.89, 14, 'PASS'),
(95, '11331A05B6', 18, 39, 57, 2, 17, 37, 54, 2, 18, 31, 49, 2, 18, 7, 25, 0, 19, 32, 51, 2, 22, 33, 55, 2, 22, 41, 63, 2, 19, 41, 60, 2, 19, 35, 54, 2, 23, 35, 58, 2, 526, 58.44, 95, 'FAIL'),
(37, '11331A05B7', 22, 46, 68, 2, 23, 62, 85, 2, 19, 42, 61, 2, 20, 55, 75, 2, 20, 53, 73, 2, 24, 50, 74, 2, 20, 43, 63, 2, 19, 40, 59, 2, 21, 44, 65, 2, 21, 41, 62, 2, 685, 76.11, 37, 'PASS'),
(115, '11331A05B8', 14, 20, 34, NULL, 19, 1, 20, NULL, 16, 11, 27, 0, 19, 12, 31, 0, 14, 15, 29, 0, 16, 14, 30, 0, 21, 40, 61, 2, 19, 41, 60, 2, 14, 13, 27, 0, 21, 38, 59, 2, 378, 42, 115, 'FAIL'),
(60, '11331A05B9', 22, 59, 81, 2, 21, 30, 51, 2, 24, 26, 50, 2, 20, 35, 55, 2, 23, 46, 69, 2, 23, 40, 63, 2, 23, 48, 71, 2, 23, 43, 66, 2, 23, 42, 65, 2, 23, 46, 69, 2, 640, 71.11, 60, 'PASS'),
(29, '11331A05C0', 24, 40, 64, 2, 22, 74, 96, 2, 24, 63, 87, 2, 22, 26, 48, 2, 21, 39, 60, 2, 21, 56, 77, 2, 24, 50, 74, 2, 22, 41, 63, 2, 21, 40, 61, 2, 25, 46, 71, 2, 701, 77.89, 29, 'PASS');

-- --------------------------------------------------------

--
-- Table structure for table `cse11jan2012results_cse_hod`
--

CREATE TABLE IF NOT EXISTS `cse11jan2012results_cse_hod` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wo455,{*}}',
  `resultants` varchar(20) NOT NULL DEFAULT '',
  `ENG_1` float DEFAULT NULL,
  `M_1` float DEFAULT NULL,
  `EP_1` float DEFAULT NULL,
  `EC_1` float DEFAULT NULL,
  `CP` float DEFAULT NULL,
  `MM` float DEFAULT NULL,
  `EPECL` float DEFAULT NULL,
  `EWSL` float DEFAULT NULL,
  `CPL` float DEFAULT NULL,
  `EPL` float DEFAULT NULL,
  PRIMARY KEY (`resultants`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:Bz0,o:455' AUTO_INCREMENT=12 ;

--
-- Dumping data for table `cse11jan2012results_cse_hod`
--

INSERT INTO `cse11jan2012results_cse_hod` (`index`, `resultants`, `ENG_1`, `M_1`, `EP_1`, `EC_1`, `CP`, `MM`, `EPECL`, `EWSL`, `CPL`, `EPL`) VALUES
(10, '>60', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, '>90', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'averageMarks', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'Between60and70', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Between70and80', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'Between80and90', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'passAverageMarks', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'SubjectWise%', 100, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(1, 'subjectWiseFailed', NULL, 1, 21, 24, 14, 12, NULL, NULL, 13, NULL),
(3, 'subjectWiseMaximum', NULL, NULL, 93, 93, 99, 100, 75, 71, NULL, NULL),
(2, 'subjectWisePassed', 1, NULL, 98, 95, 105, 107, 119, 119, 106, 119);

-- --------------------------------------------------------

--
-- Table structure for table `dow`
--

CREATE TABLE IF NOT EXISTS `dow` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wt5,{*}}',
  `gow` int(8) DEFAULT NULL,
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forhod_ece_lecturer`
--

CREATE TABLE IF NOT EXISTS `forhod_ece_lecturer` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wg52,{*}}',
  `regd_no` varchar(10) NOT NULL DEFAULT '',
  `attendance` int(2) DEFAULT NULL,
  `subs` enum('cn','oc','emi','mpi') DEFAULT NULL,
  `marks` int(2) DEFAULT NULL,
  PRIMARY KEY (`regd_no`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:Ab6,o:121' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `forhod_ece_lecturer`
--

INSERT INTO `forhod_ece_lecturer` (`index`, `regd_no`, `attendance`, `subs`, `marks`) VALUES
(1, '06331A0401', 5, 'emi', 13),
(2, '06331A0402', 4, 'mpi', 12);

-- --------------------------------------------------------

--
-- Table structure for table `go`
--

CREATE TABLE IF NOT EXISTS `go` (
  `index` int(3) NOT NULL AUTO_INCREMENT,
  `qw` int(2) NOT NULL DEFAULT '0',
  `fq` int(2) NOT NULL COMMENT '{rg38,{*}},{wg39,{2-4}}',
  `er` varchar(8) DEFAULT NULL,
  `grr` enum('GURR','BURR') DEFAULT NULL,
  PRIMARY KEY (`qw`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='{wg53,{*}}' AUTO_INCREMENT=7 ;

--
-- Dumping data for table `go`
--

INSERT INTO `go` (`index`, `qw`, `fq`, `er`, `grr`) VALUES
(1, 1, 23, 'hg', 'BURR'),
(2, 2, 23, '<br/>', 'GURR'),
(3, 3, 13, '<br/>', 'GURR'),
(4, 4, 34, '<br/>', 'BURR'),
(5, 5, 12, '<br/>', 'BURR'),
(6, 6, 13, 'hg', 'GURR');

-- --------------------------------------------------------

--
-- Table structure for table `god`
--

CREATE TABLE IF NOT EXISTS `god` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `god` int(8) DEFAULT NULL,
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gom`
--

CREATE TABLE IF NOT EXISTS `gom` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `dom` int(8) NOT NULL DEFAULT '0',
  `com` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`dom`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gop`
--

CREATE TABLE IF NOT EXISTS `gop` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `go` varchar(8) DEFAULT NULL,
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gow`
--

CREATE TABLE IF NOT EXISTS `gow` (
  `index` int(3) NOT NULL AUTO_INCREMENT,
  `gow` int(2) NOT NULL DEFAULT '0',
  `bow` int(2) DEFAULT NULL,
  PRIMARY KEY (`gow`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='{wg59,{*}}' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `gow`
--

INSERT INTO `gow` (`index`, `gow`, `bow`) VALUES
(2, 22, 34),
(3, 33, 23);

-- --------------------------------------------------------

--
-- Table structure for table `gow1`
--

CREATE TABLE IF NOT EXISTS `gow1` (
  `index` int(3) NOT NULL AUTO_INCREMENT,
  `go` int(2) DEFAULT NULL,
  `wt` int(2) DEFAULT NULL,
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='{waZz0{*}}' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gowe`
--

CREATE TABLE IF NOT EXISTS `gowe` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `go` int(8) NOT NULL DEFAULT '54',
  `bo` varchar(8) DEFAULT '432',
  `tu` enum('go','bo') DEFAULT 'go',
  PRIMARY KEY (`go`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `gowe`
--

INSERT INTO `gowe` (`index`, `go`, `bo`, `tu`) VALUES
(3, 4, '432', 'go'),
(5, 5, '432', NULL),
(1, 54, '432', 'go');

-- --------------------------------------------------------

--
-- Table structure for table `gowee`
--

CREATE TABLE IF NOT EXISTS `gowee` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wt5,{*}}',
  `a` int(3) NOT NULL,
  `b` varchar(8) NOT NULL DEFAULT 'gow',
  `c` timestamp NULL DEFAULT NULL,
  `d` enum('g','h','gh') NOT NULL,
  PRIMARY KEY (`a`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `gowee`
--

INSERT INTO `gowee` (`index`, `a`, `b`, `c`, `d`) VALUES
(2, 2, 'gow', '0000-00-00 00:00:00', 'h'),
(4, 3, 'gow', NULL, 'h'),
(7, 4, 'gow', NULL, 'g'),
(8, 5, 'gow', NULL, 'g'),
(9, 6, 'gow', NULL, 'g');

-- --------------------------------------------------------

--
-- Table structure for table `hai_sairamvvs`
--

CREATE TABLE IF NOT EXISTS `hai_sairamvvs` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu88,{*}}',
  `No` int(3) NOT NULL DEFAULT '272',
  `Name` varchar(8) DEFAULT 'satya',
  `Maths` int(3) DEFAULT '84',
  `Physics` int(3) DEFAULT '80',
  `Total` int(3) DEFAULT '164',
  `Average` int(3) DEFAULT '82',
  PRIMARY KEY (`No`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `itemprices`
--

CREATE TABLE IF NOT EXISTS `itemprices` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `itemName` varchar(18) NOT NULL,
  `price` int(2) DEFAULT NULL,
  PRIMARY KEY (`itemName`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=13 ;

--
-- Dumping data for table `itemprices`
--

INSERT INTO `itemprices` (`index`, `itemName`, `price`) VALUES
(10, 'c&c', 12),
(8, 'Clean&Clear', 80),
(1, 'parachute45ml', 12),
(11, 'PO''S', 18),
(12, 'PON''S', 19),
(9, 'POND''S', 20),
(4, 'PONDs', 13),
(2, 'PONDSmagic50g', 24),
(3, 'PONDSs', 12);

-- --------------------------------------------------------

--
-- Table structure for table `jaggu_gowtham`
--

CREATE TABLE IF NOT EXISTS `jaggu_gowtham` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `item` varchar(20) DEFAULT NULL,
  `cost` int(2) DEFAULT NULL,
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `jaggu_gowtham`
--

INSERT INTO `jaggu_gowtham` (`index`, `item`, `cost`) VALUES
(1, 'super', 8);

-- --------------------------------------------------------

--
-- Table structure for table `kuch_cse_hod`
--

CREATE TABLE IF NOT EXISTS `kuch_cse_hod` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wo455,{*}}',
  `sdf` int(2) NOT NULL DEFAULT '0' COMMENT '{rg54,{*}},{wg55,{1}}',
  `ad` int(2) DEFAULT NULL COMMENT '{wg56,{*}}',
  `bd` int(1) DEFAULT NULL,
  `cd` int(1) DEFAULT NULL,
  PRIMARY KEY (`sdf`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:Bz0,o:455' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `kuch_cse_hod`
--

INSERT INTO `kuch_cse_hod` (`index`, `sdf`, `ad`, `bd`, `cd`) VALUES
(1, 4, 3, 7, 14),
(2, 22, 21, 43, 86);

-- --------------------------------------------------------

--
-- Table structure for table `marks_cse_head`
--

CREATE TABLE IF NOT EXISTS `marks_cse_head` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wo455,{*}}',
  `id` varchar(10) NOT NULL,
  `eng` int(2) DEFAULT NULL,
  `tel` int(2) DEFAULT NULL,
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `marks_cse_head`
--

INSERT INTO `marks_cse_head` (`index`, `id`, `eng`, `tel`) VALUES
(1, '06331A0401', 20, 30),
(2, '06331A0402', 34, 43);

-- --------------------------------------------------------

--
-- Table structure for table `marks_ece_head`
--

CREATE TABLE IF NOT EXISTS `marks_ece_head` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wo331,{*}}',
  `id` varchar(16) DEFAULT NULL,
  `maths` int(2) DEFAULT NULL,
  `physics` int(2) DEFAULT NULL,
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `marks_ece_head`
--

INSERT INTO `marks_ece_head` (`index`, `id`, `maths`, `physics`) VALUES
(1, 'venki', 100, 98);

-- --------------------------------------------------------

--
-- Table structure for table `marks_sairamvvs`
--

CREATE TABLE IF NOT EXISTS `marks_sairamvvs` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu88,{*}}',
  `No` int(3) NOT NULL DEFAULT '0',
  `Maths` int(3) DEFAULT NULL,
  `Physics` int(3) DEFAULT NULL,
  `Chemistry` int(3) DEFAULT NULL,
  `Total` int(3) DEFAULT NULL,
  `Average` int(3) DEFAULT NULL,
  PRIMARY KEY (`No`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `mytable_swapnalata`
--

CREATE TABLE IF NOT EXISTS `mytable_swapnalata` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu74,{*}}',
  `friends` varchar(8) NOT NULL DEFAULT '',
  `closeness` int(8) DEFAULT NULL,
  `type` enum('best','good','acquaintance') DEFAULT NULL,
  PRIMARY KEY (`friends`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `mytable_swapnalata`
--

INSERT INTO `mytable_swapnalata` (`index`, `friends`, `closeness`, `type`) VALUES
(3, 'scooby', 10, 'best'),
(1, 'souji', 9, 'best'),
(2, 'venki', 6, 'good');

-- --------------------------------------------------------

--
-- Table structure for table `oct2012balsheet_gowtham`
--

CREATE TABLE IF NOT EXISTS `oct2012balsheet_gowtham` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `day` int(1) NOT NULL,
  `venki` varchar(100) DEFAULT NULL,
  `meher` varchar(100) DEFAULT NULL,
  `vamsi` varchar(100) DEFAULT NULL,
  `me` varchar(100) DEFAULT NULL,
  `raja` varchar(100) DEFAULT NULL,
  `sandy` varchar(100) DEFAULT NULL,
  `srikanth` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`day`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `oct2012balsheet_gowtham`
--

INSERT INTO `oct2012balsheet_gowtham` (`index`, `day`, `venki`, `meher`, `vamsi`, `me`, `raja`, `sandy`, `srikanth`) VALUES
(2, 1, '128|128.57(128.57:ricebag)-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0'),
(3, 2, '78|0-50(50:almondOil)', '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0'),
(4, 3, '100|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0'),
(5, 4, '85|0-15(15:gdayBiks)', '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0');

-- --------------------------------------------------------

--
-- Table structure for table `oct2012roomledger`
--

CREATE TABLE IF NOT EXISTS `oct2012roomledger` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `day` int(1) NOT NULL,
  `venki` varchar(100) DEFAULT NULL,
  `meher` varchar(100) DEFAULT NULL,
  `vamsi` varchar(100) DEFAULT NULL,
  `gowtham` varchar(100) DEFAULT NULL,
  `raja` varchar(100) DEFAULT NULL,
  `sandy` varchar(100) DEFAULT NULL,
  `srikanth` int(200) DEFAULT NULL,
  PRIMARY KEY (`day`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `oct2012roomledger`
--

INSERT INTO `oct2012roomledger` (`index`, `day`, `venki`, `meher`, `vamsi`, `gowtham`, `raja`, `sandy`, `srikanth`) VALUES
(1, 1, '900(r-ricebag)', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2, NULL, NULL, NULL, '50(s-i-venki;r-almondOil)', NULL, NULL, NULL),
(3, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 4, NULL, NULL, NULL, '15(s-i-venki;r-gdayBiks)', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pastetest`
--

CREATE TABLE IF NOT EXISTS `pastetest` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `regNo` varchar(10) NOT NULL DEFAULT '',
  `s_i` int(1) DEFAULT NULL,
  `s_e` int(1) DEFAULT NULL,
  `s_c` int(1) DEFAULT NULL COMMENT '{rg60,{*}}',
  PRIMARY KEY (`regNo`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=14 ;

--
-- Dumping data for table `pastetest`
--

INSERT INTO `pastetest` (`index`, `regNo`, `s_i`, `s_e`, `s_c`) VALUES
(1, '11331A0505', 22, 22, NULL),
(9, '11331A0514', 25, 5, NULL),
(2, '11331A0534', 14, 28, 2),
(3, '11331A0538', 25, 36, 2),
(4, '11331A0539', 24, 55, 2),
(10, '11331A0575', 25, 52, 2),
(12, '11331A0598', 25, 5, NULL),
(11, '11331A05B2', 24, 43, 2),
(13, 'total', 24, 31, 2);

-- --------------------------------------------------------

--
-- Table structure for table `salrytable`
--

CREATE TABLE IF NOT EXISTS `salrytable` (
  `index` int(3) NOT NULL AUTO_INCREMENT,
  `id` varchar(8) NOT NULL,
  `hrs` int(8) DEFAULT NULL,
  `incetive` int(8) DEFAULT NULL,
  `salary` int(8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `salrytable`
--

INSERT INTO `salrytable` (`index`, `id`, `hrs`, `incetive`, `salary`) VALUES
(1, '06331A47', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sept2012balsheet_meher`
--

CREATE TABLE IF NOT EXISTS `sept2012balsheet_meher` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wg62,{*}}',
  `day` int(1) NOT NULL,
  `venki` varchar(100) DEFAULT NULL,
  `gowtham` varchar(100) DEFAULT NULL,
  `vamsi` varchar(100) DEFAULT NULL,
  `me` varchar(100) DEFAULT NULL,
  `raja` varchar(100) DEFAULT NULL,
  `sandy` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`day`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `sept2012balsheet_meher`
--

INSERT INTO `sept2012balsheet_meher` (`index`, `day`, `venki`, `gowtham`, `vamsi`, `me`, `raja`, `sandy`) VALUES
(1, 14, '0|0-0', '25|25(25:Mwater)-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0'),
(2, 15, '0|0-0', '-15|0-15(15:dinner)', '0|0-0', '0|0-0', '0|0-0', '0|0-0'),
(3, 16, '5|5(5)-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0'),
(4, 17, '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0'),
(5, 18, '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0', '0|0-0');

-- --------------------------------------------------------

--
-- Table structure for table `sept2012creditroomaccount`
--

CREATE TABLE IF NOT EXISTS `sept2012creditroomaccount` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `day` int(1) NOT NULL DEFAULT '0',
  `venki` varchar(100) DEFAULT NULL,
  `meher` varchar(100) DEFAULT NULL,
  `vamsi` varchar(100) DEFAULT NULL,
  `me` varchar(100) DEFAULT NULL,
  `raja` varchar(100) DEFAULT NULL,
  `sandy` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`day`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=23 ;

--
-- Dumping data for table `sept2012creditroomaccount`
--

INSERT INTO `sept2012creditroomaccount` (`index`, `day`, `venki`, `meher`, `vamsi`, `me`, `raja`, `sandy`) VALUES
(5, 14, '-11|14(10:maagi+4:1egg)-25(25:Mwater)', '-25|0-25(25:Mwater)', '-25|0-25(25:Mwater)', '25|25(25:Mwater)-0', '-25|0-25(25:Mwater)', '-25|0-25(25:Mwater)'),
(6, 15, '13|25(25)-1.67(1.67)', '-11|15(15:dinner)-1.67(1.67)', '-26|0-1.67(1.67)', '26|1.67(1.67)-0', '-26|0-1.67(1.67)', '-25|0-0'),
(8, 16, '-182|5(5)-200(200:lent)', '-11|0-0', '-26|0-0', '26|0-0', '-26|0-0', '-26|0-0'),
(9, 17, '-182|0-0', '-11|0-0', '-26|0-0', '26|0-0', '-26|0-0', '-26|0-0'),
(10, 18, '-182|0-0', '-11|0-0', '-26|0-0', '26|0-0', '-26|0-0', '-26|0-0'),
(11, 19, '-182|0-0', '-11|0-0', '-26|0-0', '26|0-0', '-26|0-0', '-26|0-0'),
(12, 20, '-182|0-0', '-11|0-0', '-26|0-0', '26|0-0', '-26|0-0', '-26|0-0'),
(13, 21, '-182|0-0', '-11|0-0', '-26|0-0', '26|0-0', '-26|0-0', '-356|0-330(330:party)'),
(14, 22, '-182|0-0', '-11|0-0', '-26|0-0', '26|0-0', '-26|0-0', '-356|0-0'),
(15, 23, '-182|0-0', '-11|0-0', '-26|0-0', '26|0-0', '-26|0-0', '-356|0-0'),
(16, 24, '-182|0-0', '-11|0-0', '-26|0-0', '26|0-0', '-26|0-0', '-356|0-0'),
(17, 25, '-182|0-0', '-11|0-0', '-26|0-0', '26|0-0', '-26|0-0', '-356|0-0'),
(18, 26, '-182|0-0', '15|26(26:curries)-0', '-26|0-0', '26|0-0', '-26|0-0', '-356|0-0'),
(19, 27, '-182|0-0', '15|0-0', '-26|0-0', '26|0-0', '-26|0-0', '-356|0-0'),
(20, 28, '-182|0-0', '15|0-0', '-26|0-0', '26|0-0', '-26|0-0', '-356|0-0'),
(21, 29, '-165|35(35:dinner)-18(18:tablets)', '15|0-0', '-26|0-0', '26|0-0', '-16|10(10:chapathi)-0', '-356|0-0'),
(22, 30, '-165|0-0', '15|0-0', '-26|0-0', '26|0-0', '-16|0-0', '-356|0-0');

-- --------------------------------------------------------

--
-- Table structure for table `sept2012roomledger`
--

CREATE TABLE IF NOT EXISTS `sept2012roomledger` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `day` int(1) NOT NULL DEFAULT '0',
  `venki` varchar(100) DEFAULT NULL,
  `meher` varchar(100) DEFAULT NULL COMMENT '{wg61,{*}}',
  `vamsi` varchar(100) DEFAULT NULL,
  `gowtham` varchar(100) DEFAULT NULL,
  `raja` varchar(100) DEFAULT NULL,
  `sandy` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`day`),
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='al:,o:36' AUTO_INCREMENT=19 ;

--
-- Dumping data for table `sept2012roomledger`
--

INSERT INTO `sept2012roomledger` (`index`, `day`, `venki`, `meher`, `vamsi`, `gowtham`, `raja`, `sandy`) VALUES
(1, 13, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 14, '20(s-i-gowtham,venki;r-maagi)|4(s-i-gowtham;r-1egg)', NULL, NULL, '150(r-Mwater)', NULL, NULL),
(3, 15, '25(s-i-gowtham)', '15(s-i-gowtham;r-dinner)', NULL, NULL, NULL, NULL),
(4, 16, '30', NULL, NULL, '200(s-i-venki;r-lent)', NULL, NULL),
(5, 17, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 18, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 19, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 20, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 21, NULL, NULL, NULL, '330(s-i-sandy;r-party)', NULL, NULL),
(10, 22, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 23, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 24, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 25, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 26, NULL, '52(s-i-meher,gowtham;r-curries)', NULL, NULL, NULL, NULL),
(15, 27, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 28, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 29, '70(s-i-venki,gowtham;r-dinner)', NULL, NULL, '18(s-i-venki;r-tablets)', '10(s-i-gowtham;r-chapathi)', NULL),
(18, 30, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_ids`
--

CREATE TABLE IF NOT EXISTS `student_ids` (
  `index` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_name` varchar(24) NOT NULL,
  `regd_no` varchar(10) NOT NULL DEFAULT '0000000000',
  `UPID` int(32) NOT NULL DEFAULT '0',
  PRIMARY KEY (`index`),
  UNIQUE KEY `regd_no` (`regd_no`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `student_ids`
--

INSERT INTO `student_ids` (`index`, `student_name`, `regd_no`, `UPID`) VALUES
(1, 'satya gowtham kudupudi', '06331A0478', 0),
(3, 'sasidhar gulipalli', '06331A0476', 0),
(4, 'satish kumar k', '06331A0477', 0),
(5, 'gowtham pakki', '06331A0432', 0),
(6, 'harish koppisetty', '06B91A0310', 29),
(7, 'rekha', '09HK1A0229', 0);

-- --------------------------------------------------------

--
-- Table structure for table `swapnatotest`
--

CREATE TABLE IF NOT EXISTS `swapnatotest` (
  `index` int(3) NOT NULL AUTO_INCREMENT COMMENT '{wu36,{*}}',
  `venki` int(8) DEFAULT NULL COMMENT '{wg24,{*}},{rg28,{*}}',
  `swapna` varchar(8) DEFAULT NULL COMMENT '{wg26,{*}},{rg30,{*}}',
  `gowtham` enum('yes','no') DEFAULT NULL COMMENT '{wg27,{*}},{rg29,{*}}',
  UNIQUE KEY `index` (`index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `swapnatotest`
--

INSERT INTO `swapnatotest` (`index`, `venki`, `swapna`, `gowtham`) VALUES
(1, 1234, 'yipee', 'yes'),
(2, 0, 'u', 'no'),
(3, 0, 'can', 'no'),
(4, 0, 'write', 'no'),
(5, 0, 'into', 'no'),
(6, NULL, 'these', NULL),
(7, NULL, 'cells', NULL),
(8, NULL, 'or', NULL),
(9, NULL, 'this', NULL),
(10, NULL, 'column', NULL),
(11, NULL, 'but', NULL),
(12, NULL, 'not', NULL),
(13, NULL, 'in', NULL),
(14, NULL, 'venkis', NULL),
(15, NULL, 'column', NULL),
(16, NULL, 'n', NULL),
(17, NULL, 'u cant', NULL),
(18, NULL, 'read', NULL),
(19, NULL, 'gowtham', NULL),
(20, NULL, NULL, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
