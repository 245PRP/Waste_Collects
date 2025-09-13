-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 13 sep. 2025 à 15:46
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
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `point_collecte`
--

INSERT INTO `point_collecte` (`id_pt`, `nom_pt`, `lieu`, `capacite`, `Etat`, `date_vidange`) VALUES
(15, 'point akwa', 'bafoussam', 450, 'vide', '2025-09-04 10:45:00.00'),
(11, 'point ndokoti', 'yaounde', 255, 'rempli', '2025-08-24 12:03:00.00'),
(12, 'point Saker', 'douala', 880, 'rempli', '2025-08-27 04:12:00.00'),
(14, 'point yassa', 'douala', 228, 'rempli', '2025-09-04 09:25:00.00');

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
  `statut` varchar(50) NOT NULL DEFAULT 'En attente',
  PRIMARY KEY (`id_tour`),
  KEY `id_user` (`id_user`),
  KEY `id_pt` (`id_pt`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `ramassage`
--

INSERT INTO `ramassage` (`id_tour`, `id_user`, `id_pt`, `date_tour`, `statut`) VALUES
(1, 16, 12, '2025-08-31', ''),
(2, 20, 12, '2025-10-12', ''),
(3, 20, 11, '2025-09-12', ''),
(4, 20, 14, '2025-10-22', ''),
(5, 20, 12, '2025-10-26', 'En attente'),
(6, 20, 11, '2025-10-31', 'En attente'),
(7, 20, 14, '2025-09-09', 'En attente');

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
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `signalement`
--

INSERT INTO `signalement` (`id_sign`, `motif`, `description`, `date_signal`, `adresse`, `id_user`, `id_pt`) VALUES
(1, 'plein', 'test', '2025-03-23 19:13:00', 'douala', 11, 11),
(2, 'plein', 'test', '2025-03-23 19:13:00', 'douala', 12, 12),
(3, 'plein', 'test', '2025-03-23 19:13:00', 'douala', 11, 14),
(4, 'plein', 'test', '2025-01-23 19:13:00', 'douala', NULL, 14),
(5, 'plein', 'test', '2025-01-23 19:13:00', 'douala', NULL, 14),
(6, 'plein', 'test', '2025-08-23 19:13:00', 'douala', NULL, NULL),
(7, 'plein', 'AUCUN', '2025-08-14 13:18:00', 'PICASO', NULL, NULL),
(8, 'plein', 'AUCUN', '2025-08-14 13:18:00', 'PICASO', NULL, NULL),
(9, 'plein', 'AUCUN', '2025-08-14 13:18:00', 'PICASO', NULL, NULL),
(10, 'absent', 'aucun', '2025-08-25 13:39:00', 'PICASO', NULL, NULL),
(11, 'absent', 'aucun', '2025-08-25 13:39:00', 'PICASO', NULL, NULL),
(12, 'cassÃ©', 'tesfg', '2025-08-25 13:43:00', 'douala', NULL, NULL),
(13, 'cassÃ©', 'aucun', '2025-08-25 13:43:00', 'douala', NULL, NULL),
(14, 'cassÃ©', 'aucun', '2025-08-25 13:43:00', 'douala', NULL, NULL),
(15, 'cassÃ©', 'aucun', '2025-01-25 13:43:00', 'douala', NULL, 15),
(16, 'cassÃ©', 'aucun', '2025-02-25 13:43:00', 'douala', 12, 14),
(17, 'cassÃ©', 'qsdfghjk', '2025-02-16 14:05:00', 'douala', 12, 14),
(18, 'cassÃ©', 'aucune description', '2000-02-14 12:00:00', 'rail', 12, 14),
(19, 'plein', 'point franchement rempli', '2025-01-03 18:03:00', 'douala', 12, 15),
(20, 'cassÃ©', 'point endomagÃ©', '2025-01-04 19:04:00', 'douala', 12, 12),
(21, 'cassÃ©', 'aucun', '2025-01-04 10:36:00', 'douala', 12, 12),
(22, 'cassÃ©', 'aucun', '2025-01-04 10:43:00', 'douala', 12, 15),
(23, 'absent', 'zertyuiop', '2025-02-04 11:19:00', 'PICASO', 12, 14),
(24, 'cassÃ©', 'sdfghjkl', '2025-01-04 11:49:00', 'rail', 12, 14),
(25, 'absent', 'sdfghjkl', '2025-09-04 11:49:00', 'rail', 12, 15),
(26, 'plein', 'aucun', '2025-09-08 15:38:00', 'douala', 12, 12),
(27, 'cassÃ©', 'aucun', '2025-09-08 15:38:00', 'rail', 12, 14),
(28, 'plein', 'point de collecte très plein', '2025-01-22 16:52:42', 'douala dakar', NULL, NULL),
(29, 'plein', 'point de collecte très plein', '2025-01-22 16:52:42', 'douala dakar', NULL, NULL),
(30, 'plein', 'aucun', '2025-07-07 15:38:00', 'rail', 12, 15),
(31, 'plein', 'aucun', '2025-07-27 15:38:00', 'rail', 12, 15),
(32, 'plein', 'aucun', '2025-05-14 15:38:00', 'newbell', 12, 11),
(33, 'plein', 'aucun', '2025-05-29 15:38:00', 'dernier poteau', 12, 11),
(34, 'plein', 'aucun', '2025-04-22 19:42:00', 'bonamoussadi', 12, 11),
(35, 'plein', 'aucun', '2025-04-24 19:42:00', 'bonamoussadi', 12, 12),
(36, 'plein', 'aucun', '2025-06-06 09:44:00', 'bonandjo', 12, 15),
(37, 'plein', 'aucun', '2025-06-19 12:47:00', 'bonandjo', 12, 15),
(38, 'plein', 'none', '2025-09-12 03:31:00', 'douala', NULL, 11);

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
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_user`, `nom_user`, `email`, `telephone`, `lieu`, `role`, `permis`, `motdepasse`) VALUES
(11, 'prunelle schemima', 'prunellendonkeu@gmail.com', '698916646', 'yaounde', 'chauffeur', NULL, '$2y$10$WZy96.Q3eTw6VERXnaHx..P8LFgjfmOrK5rhUPRhShhhbo5xLCyrS'),
(12, 'evans', 'evanstech@gmail.com', '678840296', 'yaounde', 'administrateur', NULL, '$2y$10$TZnxQhsaSIbV1n7CpRG1re8DI4oaVAVtzL8pyTQZOoUWeZa4knqKu'),
(13, 'marvel', 'marvelinner@gmail.com', '390420954', 'yaounde', 'citoyen', NULL, '$2y$10$kFdJkaIYA.1sBlQdsiwAEe4oUMLJq1BCLihvfNHrPq1pzeKi12SIm'),
(14, 'MELIA', 'melia@gmail.com', '648585623', 'douala', 'citoyen', NULL, '$2y$10$pgEapNwgT9uUPkEmB3qNQeJwOw2TG22CQlALkLukkILV8Ono0fcsa'),
(15, 'SteveNGANGUE', 'stevengangue405@gmail.com', '656272729', 'Douala', 'chauffeur', 'B', '$2y$10$kDtP1xMDTWHZwMruNF02a.mnw0076WRpxcPi4Kw30/GzfytC0YsUC'),
(16, 'SALOMON FRITZ', 'fritzalom@gmail.com', '645252028', 'Douala', 'chauffeur', NULL, '$2y$10$o52tGNt7rg6On35krEEEN.GfU/vPMxuoPPFOeuyChjxWIqgttUJZG'),
(17, 'Manuella', 'manuellavanelle@gmail.com', '6987654332', 'cite sic', 'citoyen', 'B', '$2y$10$WaLX6R2WJVpag49ZkuMNiOGtlTmSAKE2CPFQRT2Q0tGzigsWw7hOm'),
(18, 'vanelle Manuella', 'vanellemanuella@gmail.com', '698961752', 'douala', 'citoyen', NULL, '$2y$10$elJiL..6FjlH/b8IJVavsODf4tAJkTRbLdBZC7OYgT9RdFwEz8X1e'),
(19, 'victoire', 'victoirealexis@gamil.com', '6987654332', 'Yaounde', 'chauffeur', 'A', '$2y$10$IIiKmBhqIgr61GnemFAwEOhiMwceijZVp/Gkal5ovhJ/0Txxkqa8a'),
(20, 'kareyce', 'kareycelae@gmail.com', '656545251', 'bafoussam', 'chauffeur', 'A', '$2y$10$s2laB9KlDiJq.tfJNUqd5.YIL9hvUjwMyOb5UL8dXBDOSAFOywc96');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
