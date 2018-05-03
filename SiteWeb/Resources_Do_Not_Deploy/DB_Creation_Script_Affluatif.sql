-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 02, 2018 at 07:10 AM
-- Server version: 5.7.22-0ubuntu0.16.04.1
-- PHP Version: 7.0.28-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Affluatif`
--

-- --------------------------------------------------------

--
-- Table structure for table `affluence_flux`
--

CREATE TABLE `affluence_flux` (
  `id` int(11) NOT NULL,
  `flux_id` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` int(11) NOT NULL,
  `nombre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `alertes`
--

CREATE TABLE `alertes` (
  `id` int(11) NOT NULL,
  `flux_id` int(11) NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `derniere_alerte` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `classe_flux`
--

CREATE TABLE `classe_flux` (
  `id` int(11) NOT NULL,
  `flux_id` int(11) NOT NULL,
  `classe` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `flux_utilisateur`
--

CREATE TABLE `flux_utilisateur` (
  `id` int(11) NOT NULL,
  `flux_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `flux_video`
--

CREATE TABLE `flux_video` (
  `id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `description` text,
  `loc_lat` float DEFAULT NULL,
  `loc_lon` float DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `image_max_affluence` longblob,
  `waiting_interpret` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `password` varchar(64) NOT NULL,
  `statut` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `affluence_flux`
--
ALTER TABLE `affluence_flux`
  ADD PRIMARY KEY (`id`),
  ADD KEY `flux_id` (`flux_id`);

--
-- Indexes for table `alertes`
--
ALTER TABLE `alertes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idFlux` (`flux_id`);

--
-- Indexes for table `classe_flux`
--
ALTER TABLE `classe_flux`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `flux_id_2` (`flux_id`,`classe`),
  ADD KEY `flux_id` (`flux_id`);

--
-- Indexes for table `flux_utilisateur`
--
ALTER TABLE `flux_utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `flux_id_2` (`flux_id`,`utilisateur_id`),
  ADD KEY `flux_id` (`flux_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Indexes for table `flux_video`
--
ALTER TABLE `flux_video`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `utilisateurs_mail_uindex` (`mail`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `affluence_flux`
--
ALTER TABLE `affluence_flux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `alertes`
--
ALTER TABLE `alertes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `classe_flux`
--
ALTER TABLE `classe_flux`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `flux_utilisateur`
--
ALTER TABLE `flux_utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `flux_video`
--
ALTER TABLE `flux_video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `affluence_flux`
--
ALTER TABLE `affluence_flux`
  ADD CONSTRAINT `fk_flux_video_affluence_flux` FOREIGN KEY (`flux_id`) REFERENCES `flux_video` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `alertes`
--
ALTER TABLE `alertes`
  ADD CONSTRAINT `alertes_ibfk_1` FOREIGN KEY (`flux_id`) REFERENCES `flux_video` (`id`);

--
-- Constraints for table `classe_flux`
--
ALTER TABLE `classe_flux`
  ADD CONSTRAINT `fk_flux_classe` FOREIGN KEY (`flux_id`) REFERENCES `flux_video` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `flux_utilisateur`
--
ALTER TABLE `flux_utilisateur`
  ADD CONSTRAINT `fk_flux_flux_utilisateur` FOREIGN KEY (`flux_id`) REFERENCES `flux_video` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_utilisateur_flux_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
