-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 03 sep. 2025 à 16:51
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
  `id_cam` int(11) NOT NULL AUTO_INCREMENT,
  `immatriculation` varchar(20) NOT NULL,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id_cam`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `camion`
--

INSERT INTO `camion` (`id_cam`, `immatriculation`, `id_user`) VALUES
(1, 'LTD4569213PR', 15),
(2, 'LTD4569213PR', 15),
(7, 'LTD4569213PR', 16);

-- --------------------------------------------------------

--
-- Structure de la table `point_collecte`
--

DROP TABLE IF EXISTS `point_collecte`;
CREATE TABLE IF NOT EXISTS `point_collecte` (
  `id_pt` int(11) NOT NULL AUTO_INCREMENT,
  `nom_pt` varchar(20) NOT NULL,
  `lieu` varchar(20) NOT NULL,
  `capacite` float NOT NULL,
  `Etat` varchar(20) NOT NULL,
  `date_vidange` datetime(2) NOT NULL,
  PRIMARY KEY (`id_pt`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `point_collecte`
--

INSERT INTO `point_collecte` (`id_pt`, `nom_pt`, `lieu`, `capacite`, `Etat`, `date_vidange`) VALUES
(11, 'point ndokoti', 'yaounde', 255, 'rempli', '2025-08-24 12:03:00.00'),
(12, 'point Saker', 'douala', 880, 'rempli', '2025-08-27 04:12:00.00');

-- --------------------------------------------------------

--
-- Structure de la table `ramassage`
--

DROP TABLE IF EXISTS `ramassage`;
CREATE TABLE IF NOT EXISTS `ramassage` (
  `id_tour` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_pt` int(11) NOT NULL,
  `date_tour` date NOT NULL,
  PRIMARY KEY (`id_tour`),
  KEY `id_user` (`id_user`),
  KEY `id_pt` (`id_pt`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `ramassage`
--

INSERT INTO `ramassage` (`id_tour`, `id_user`, `id_pt`, `date_tour`) VALUES
(1, 16, 12, '2025-08-31');

-- --------------------------------------------------------

--
-- Structure de la table `signalement`
--

DROP TABLE IF EXISTS `signalement`;
CREATE TABLE IF NOT EXISTS `signalement` (
  `id_sign` int(11) NOT NULL AUTO_INCREMENT,
  `motif` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `date_signal` datetime NOT NULL,
  `adresse` varchar(256) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_pt` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_sign`),
  KEY `id_user` (`id_user`),
  KEY `id_pt` (`id_pt`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `signalement`
--

INSERT INTO `signalement` (`id_sign`, `motif`, `description`, `date_signal`, `adresse`, `id_user`, `id_pt`) VALUES
(1, 'plein', 'test', '2025-08-23 19:13:00', 'douala', 11, NULL),
(2, 'plein', 'test', '2025-08-23 19:13:00', 'douala', 12, NULL),
(3, 'plein', 'test', '2025-08-23 19:13:00', 'douala', NULL, NULL),
(4, 'plein', 'test', '2025-08-23 19:13:00', 'douala', NULL, NULL),
(5, 'plein', 'test', '2025-08-23 19:13:00', 'douala', NULL, NULL),
(6, 'plein', 'test', '2025-08-23 19:13:00', 'douala', NULL, NULL),
(7, 'plein', 'AUCUN', '2025-08-14 13:18:00', 'PICASO', NULL, NULL),
(8, 'plein', 'AUCUN', '2025-08-14 13:18:00', 'PICASO', NULL, NULL),
(9, 'plein', 'AUCUN', '2025-08-14 13:18:00', 'PICASO', NULL, NULL),
(10, 'absent', 'aucun', '2025-08-25 13:39:00', 'PICASO', NULL, NULL),
(11, 'absent', 'aucun', '2025-08-25 13:39:00', 'PICASO', NULL, NULL),
(12, 'cassÃ©', 'tesfg', '2025-08-25 13:43:00', 'douala', NULL, NULL),
(13, 'cassÃ©', 'aucun', '2025-08-25 13:43:00', 'douala', NULL, NULL),
(14, 'cassÃ©', 'aucun', '2025-08-25 13:43:00', 'douala', NULL, NULL),
(15, 'cassÃ©', 'aucun', '2025-08-25 13:43:00', 'douala', NULL, NULL),
(16, 'cassÃ©', 'aucun', '2025-08-25 13:43:00', 'douala', 12, NULL),
(17, 'cassÃ©', 'qsdfghjk', '2025-08-16 14:05:00', 'douala', 12, NULL),
(18, 'cassÃ©', 'aucune description', '2000-02-14 12:00:00', 'rail', 12, NULL),
(19, 'plein', 'point franchement rempli', '2025-09-03 18:03:00', 'douala', 12, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nom_user` varchar(20) NOT NULL,
  `email` varchar(60) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `lieu` varchar(20) NOT NULL,
  `role` varchar(20) NOT NULL,
  `permis` varchar(20) DEFAULT NULL,
  `motdepasse` varchar(256) NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_user`, `nom_user`, `email`, `telephone`, `lieu`, `role`, `permis`, `motdepasse`) VALUES
(11, 'prunelle schemima', 'prunellendonkeu@gmail.com', '698916646', 'yaounde', 'chauffeur', NULL, '$2y$10$WZy96.Q3eTw6VERXnaHx..P8LFgjfmOrK5rhUPRhShhhbo5xLCyrS'),
(12, 'evans', 'evanstech@gmail.com', '678840296', 'yaounde', 'administrateur', NULL, '$2y$10$TZnxQhsaSIbV1n7CpRG1re8DI4oaVAVtzL8pyTQZOoUWeZa4knqKu'),
(13, 'marvel', 'marvelinner@gmail.com', '390420954', 'yaounde', 'citoyen', NULL, '$2y$10$kFdJkaIYA.1sBlQdsiwAEe4oUMLJq1BCLihvfNHrPq1pzeKi12SIm'),
(14, 'MELIA', 'melia@gmail.com', '648585623', 'douala', 'citoyen', NULL, '$2y$10$pgEapNwgT9uUPkEmB3qNQeJwOw2TG22CQlALkLukkILV8Ono0fcsa'),
(15, 'SteveNGANGUE', 'stevengangue405@gmail.com', '656272729', 'Douala', 'chauffeur', NULL, '$2y$10$kDtP1xMDTWHZwMruNF02a.mnw0076WRpxcPi4Kw30/GzfytC0YsUC'),
(16, 'SALOMON FRITZ', 'fritzalom@gmail.com', '645252028', 'Douala', 'chauffeur', NULL, '$2y$10$o52tGNt7rg6On35krEEEN.GfU/vPMxuoPPFOeuyChjxWIqgttUJZG');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
