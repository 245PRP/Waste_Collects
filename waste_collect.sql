-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 18 août 2025 à 15:20
-- Version du serveur : 5.7.36
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `waste_collect`
--

-- --------------------------------------------------------

--
-- Structure de la table `camion`
--

DROP TABLE IF EXISTS `camion`;
CREATE TABLE IF NOT EXISTS `camion` (
  `id-cam` int(11) NOT NULL AUTO_INCREMENT,
  `id-user` int(11) NOT NULL,
  `imt` varchar(20) NOT NULL,
  PRIMARY KEY (`id-cam`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `pointdecollecte`
--

DROP TABLE IF EXISTS `pointdecollecte`;
CREATE TABLE IF NOT EXISTS `pointdecollecte` (
  `id-pt` int(11) NOT NULL AUTO_INCREMENT,
  `id-user` int(11) NOT NULL,
  `lieux` varchar(20) NOT NULL,
  PRIMARY KEY (`id-pt`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `signalement`
--

DROP TABLE IF EXISTS `signalement`;
CREATE TABLE IF NOT EXISTS `signalement` (
  `id-signal` int(11) NOT NULL AUTO_INCREMENT,
  `id-user` int(11) NOT NULL,
  `id-pt` int(11) NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id-signal`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id-user` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(20) NOT NULL,
  `admin` varchar(20) NOT NULL,
  `citoyen` varchar(20) NOT NULL,
  `chauffeur` varchar(20) NOT NULL,
  `motdepasse` varchar(20) NOT NULL,
  PRIMARY KEY (`id-user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
