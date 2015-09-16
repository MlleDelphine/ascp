-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Client: 1.253.241.12
-- Généré le: Jeu 27 Août 2015 à 16:59
-- Version du serveur: 5.5.37-35.0
-- Version de PHP: 5.5.9-1ubuntu4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `servicecivique`
--

INSERT INTO `taxonomy` (`id`, `root_id`, `name`) VALUES
(1, 1, 'thématique');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Contenu de la table `taxon`
--

INSERT INTO `taxon` (`id`, `taxonomy_id`, `parent_id`, `name`, `slug`, `permalink`, `description`, `tree_left`, `tree_right`, `tree_level`, `deleted_at`) VALUES
(1, 1, NULL, 'thématique', 'thematique', 'thematique', NULL, 1, 20, 0, NULL),
(2, 1, 1, 'Culture et loisirs', 'culture-et-loisirs', 'thematique/culture-et-loisirs', NULL, 6, 7, 1, NULL),
(3, 1, 1, 'Développement international et aide humanitaire', 'developpement-international-et-aide-humanitaire', 'thematique/developpement-international-et-aide-humanitaire', NULL, 18, 19, 1, NULL),
(4, 1, 1, 'Éducation pour tous', 'education-pour-tous', 'thematique/education-pour-tous', NULL, 10, 11, 1, NULL),
(5, 1, 1, 'Environnement', 'environnement', 'thematique/environnement', NULL, 4, 5, 1, NULL),
(6, 1, 1, 'Interventions d''urgence en cas de crise', 'interventions-d-urgence-en-cas-de-crise', 'thematique/interventions-d-urgence-en-cas-de-crise', NULL, 14, 15, 1, NULL),
(7, 1, 1, 'Mémoire et citoyenneté', 'memoire-et-citoyennete', 'thematique/memoire-et-citoyennete', NULL, 8, 9, 1, NULL),
(8, 1, 1, 'Santé', 'sante', 'thematique/sante', NULL, 12, 13, 1, NULL),
(9, 1, 1, 'Solidarité', 'solidarite', 'thematique/solidarite', NULL, 2, 3, 1, NULL),
(10, 1, 1, 'Sport', 'sport', 'thematique/sport', NULL, 16, 17, 1, NULL);

--
-- Contenu de la table `taxonomy`
--

SET FOREIGN_KEY_CHECKS = 1;