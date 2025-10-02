-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 02 oct. 2025 à 16:09
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
  KEY `id_user` (`id_user`),
  KEY `id_cam` (`id_cam`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `camion`
--

INSERT INTO `camion` (`id_cam`, `immatriculation`, `id_user`) VALUES
(8, 'LTD4569213PR', 27),
(9, 'LT 332 45 NO', 27);

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
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  PRIMARY KEY (`id_pt`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `point_collecte`
--

INSERT INTO `point_collecte` (`id_pt`, `nom_pt`, `lieu`, `capacite`, `Etat`, `date_vidange`, `latitude`, `longitude`) VALUES
(45, 'POINT CITE SIC', 'Douala', 800, 'vide', '2025-10-02 14:50:00.00', 4.05192, 9.73309),
(46, 'POINT BESSENGUE', 'Douala', 500, 'vide', '2025-10-02 14:53:00.00', 4.05586, 9.70922),
(47, 'POINT BONANDJO', 'Douala', 800, 'vide', '2025-10-02 14:55:00.00', 4.04079, 9.69103),
(48, 'POINT DEIDO', 'Douala', 800, 'vide', '2025-10-02 14:56:00.00', 4.06203, 9.71506),
(49, 'POINT NDOKOTI', 'Douala', 900, 'vide', '2025-10-02 14:57:00.00', 4.04182, 9.7439);

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
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `ramassage`
--

INSERT INTO `ramassage` (`id_tour`, `id_user`, `id_pt`, `date_tour`, `statut`) VALUES
(13, 28, 48, '2025-10-19', 'En attente'),
(14, 27, 47, '2025-10-19', 'En attente'),
(15, 27, 46, '2025-10-31', 'En attente'),
(16, 28, 49, '2025-10-26', 'En attente'),
(17, 27, 45, '2025-10-23', 'En attente'),
(18, 27, 48, '2025-11-14', 'En attente'),
(19, 27, 47, '2025-11-30', 'En attente');

-- --------------------------------------------------------

--
-- Structure de la table `signalement`
--

DROP TABLE IF EXISTS `signalement`;
CREATE TABLE IF NOT EXISTS `signalement` (
  `id_sign` int(11) NOT NULL AUTO_INCREMENT,
  `motif` varchar(256) NOT NULL,
  `description` mediumtext NOT NULL,
  `date_signal` datetime NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_pt` int(11) DEFAULT NULL,
  `lu` tinyint(1) DEFAULT '0',
  `statut` varchar(10) NOT NULL,
  PRIMARY KEY (`id_sign`),
  KEY `id_user` (`id_user`),
  KEY `id_pt` (`id_pt`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `signalement`
--

INSERT INTO `signalement` (`id_sign`, `motif`, `description`, `date_signal`, `id_user`, `id_pt`, `lu`, `statut`) VALUES
(75, 'plein', 'le point est plein Ã  craquer', '2025-10-02 15:03:00', 25, 46, 0, 'non-traite'),
(76, 'plein', 'le point est plein Ã  craquer', '2025-10-02 15:03:00', 25, 46, 0, 'non-traite'),
(77, 'absent', 'none', '2025-10-02 15:45:00', 25, 47, 0, 'non-traite'),
(78, 'plein', 'aucun', '2025-10-02 15:46:00', 25, 49, 0, 'non-traite'),
(79, 'plein', 'aucun', '2025-10-02 15:46:00', 25, 49, 0, 'non-traite'),
(80, 'absent', 'none', '2025-10-02 15:59:00', 25, 46, 0, 'non-traite');

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
  UNIQUE KEY `email` (`email`),
  KEY `id_user` (`id_user`),
  KEY `id_user_2` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_user`, `nom_user`, `email`, `telephone`, `lieu`, `role`, `permis`, `motdepasse`) VALUES
(25, 'Ndonkeu prunelle', 'prunellendonkeu@gmail.com', '690420953', 'Douala', 'administrateur', NULL, '$2y$10$xHAWSYVjJwjbjUUxoPM.ougHpvQUJqTNpgt9gwcEqiiMmKF/MW/O2'),
(26, 'MBOUTOU CATHERINE', 'mboutouetemecatherine@gmail.com', '677622401', 'Douala', 'citoyen', NULL, '$2y$10$sjUjBjDhmdKLg2hOfLF9Q.AdDih5d5zIDcjLaC4kzo2ha/zTkEa0O'),
(27, 'PEGUY NKOUEBO', 'peguynk10@gmail.com', '678 56 37 71', 'Douala', 'chauffeur', NULL, '$2y$10$gRhEbOsyMUE3OVHmU19S7OC0hPRUx91BZUcBCjh4BD3dpRW4END/e'),
(28, 'KEMA DIVINE', 'schemiprp@gamil.com', '683 56 80 80', 'Douala', 'chauffeur', NULL, '$2y$10$cXw8u/UAwmhKNq.ozFT7auzFWEz7gcM10kpyO3ASnlHgsEeBhudzy');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `ramassage`
--
ALTER TABLE `ramassage`
  ADD CONSTRAINT `ramassage_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
