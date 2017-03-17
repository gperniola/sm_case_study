-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mar 17, 2017 alle 13:54
-- Versione del server: 5.7.14-log
-- Versione PHP: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sql855376_1`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `indagini`
--

DROP TABLE IF EXISTS `indagini`;
CREATE TABLE `indagini` (
  `id` int(10) NOT NULL,
  `dataInserimento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dataAggiornamento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `idpaziente` int(5) DEFAULT NULL,
  `idcpp` int(5) DEFAULT NULL,
  `careprovider` varchar(50) DEFAULT NULL,
  `idDiagnosi` int(10) DEFAULT NULL,
  `idStudioIndagini` int(11) DEFAULT NULL,
  `stato` int(12) DEFAULT NULL,
  `tipoIndagine` text,
  `dataIndagine` datetime DEFAULT NULL,
  `motivo` text,
  `referto` int(11) DEFAULT NULL,
  `allegato` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `indaginieliminate`
--

DROP TABLE IF EXISTS `indaginieliminate`;
CREATE TABLE `indaginieliminate` (
  `id` int(11) NOT NULL,
  `idutente` int(11) NOT NULL,
  `indagine_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `indagini`
--
ALTER TABLE `indagini`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_indagini` (`idpaziente`),
  ADD KEY `idDiagnosi` (`idDiagnosi`),
  ADD KEY `idcpp` (`idcpp`),
  ADD KEY `referto` (`referto`),
  ADD KEY `allegato` (`allegato`),
  ADD KEY `idStudioIndagini` (`idStudioIndagini`);

--
-- Indici per le tabelle `indaginieliminate`
--
ALTER TABLE `indaginieliminate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idutente` (`idutente`),
  ADD KEY `indagine_id` (`indagine_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `indagini`
--
ALTER TABLE `indagini`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;
--
-- AUTO_INCREMENT per la tabella `indaginieliminate`
--
ALTER TABLE `indaginieliminate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `indagini`
--
ALTER TABLE `indagini`
  ADD CONSTRAINT `FK_allegato` FOREIGN KEY (`allegato`) REFERENCES `files` (`idFiles`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_careprovider` FOREIGN KEY (`idcpp`) REFERENCES `careproviderpersona` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_centro` FOREIGN KEY (`idStudioIndagini`) REFERENCES `centriindagini` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_diagnosi` FOREIGN KEY (`idDiagnosi`) REFERENCES `diagnosi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_paziente` FOREIGN KEY (`idpaziente`) REFERENCES `pazienti` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_referto` FOREIGN KEY (`referto`) REFERENCES `files` (`idFiles`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `indaginieliminate`
--
ALTER TABLE `indaginieliminate`
  ADD CONSTRAINT `FK_indagine` FOREIGN KEY (`indagine_id`) REFERENCES `indagini` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_utente` FOREIGN KEY (`idutente`) REFERENCES `utenti` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
