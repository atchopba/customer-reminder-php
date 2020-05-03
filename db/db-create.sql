-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 25, 2020 at 02:33 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dx_webhostreminder`
--

use dx_webhostreminder;

-- --------------------------------------------------------

--
-- Table structure for table `portolio`
--

DROP TABLE IF EXISTS `portolio`;
CREATE TABLE IF NOT EXISTS `portolio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_entreprise` varchar(300) DEFAULT NULL,
  `nom_client` varchar(300) NOT NULL,
  `numero_tel` varchar(21) NOT NULL,
  `contact_2` varchar(300) DEFAULT NULL,
  `numero_contact_2` varchar(21) DEFAULT NULL,
  `nom_domaine` varchar(100) NOT NULL,
  `date_expiration` date NOT NULL,
  `email_contact` varchar(200) NOT NULL,
  `sms_relance` set('oui','non') NOT NULL DEFAULT 'oui',
  `payement_effectue` set('oui','non') NOT NULL DEFAULT 'oui',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `relance_historique`
--

DROP TABLE IF EXISTS `relance_historique`;
CREATE TABLE IF NOT EXISTS `relance_historique` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_portfolio` int(11) NOT NULL,
  `email` set('non','oui') NOT NULL,
  `sms` set('non','oui') NOT NULL,
  `sms_report` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
