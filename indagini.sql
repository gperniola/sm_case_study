-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 06, 2017 alle 15:16
-- Versione del server: 5.7.14
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
  `id` int(10) UNSIGNED NOT NULL,
  `idpaziente` int(5) DEFAULT NULL,
  `idcpp` int(10) UNSIGNED DEFAULT NULL,
  `idDiagnosi` int(10) DEFAULT NULL,
  `idStudioIndagini` int(11) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `dataAggiornamento` date DEFAULT NULL,
  `stato` varchar(12) DEFAULT NULL,
  `tipoIndagine` text,
  `motivo` text,
  `referto` text,
  `allegato` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `indagini`
--

INSERT INTO `indagini` (`id`, `idpaziente`, `idcpp`, `idDiagnosi`, `idStudioIndagini`, `data`, `dataAggiornamento`, `stato`, `tipoIndagine`, `motivo`, `referto`, `allegato`) VALUES
(20, 2, NULL, 0, 0, '0000-00-00', NULL, 'conclusa', 'tipo-otite', 'verifica diagnosi di otite', 'file.doc', 'all.jpg'),
(21, 2, NULL, 0, 0, '2016-11-11', NULL, 'conclusa', 'ot', 'verifica diagnosi di otite', 're', 'a'),
(22, 2, NULL, 0, 0, '2016-04-03', NULL, 'conclusa', 'prova', 'verifica diagnosi di test', 'ref.pdf', 'photo.jpg'),
(27, 2, NULL, 0, 0, '2016-04-19', NULL, 'conclusa', 'Verifica', 'verifica diagnosi di Prima', 'ref.pdf', 'img.jpg'),
(28, 2, NULL, 0, 0, '2016-04-19', NULL, 'conclusa', 'rx', 'verifica diagnosi di Terza', 'docu.doc', 'audio.mp3'),
(29, 2, NULL, 0, 0, '2016-04-19', NULL, 'conclusa', 'eco', 'verifica diagnosi di Terza', 'doc.pdf', 'imm.jpg'),
(44, 1, 192, 0, 1, '2016-12-29', NULL, 'richiesta', 'tipoIndagine1', 'motivo1', NULL, NULL),
(45, 1, 190, 0, 1, '2016-12-31', NULL, 'richiesta', 'tipoIndagine2', 'motivo2', NULL, NULL),
(46, 1, 192, 0, 1, '2017-01-04', NULL, 'programmata', 'tipoIndagine3', 'motivo3', NULL, NULL),
(47, 1, 190, 0, 2, '2017-01-06', NULL, 'programmata', 'tipoIndagine4', 'motivo4', NULL, NULL),
(50, 1, 190, 0, 1, '2016-12-01', NULL, 'conclusa', 'tipoIndagine5', 'motivo5', 'referto5.doc', 'allegato5.jpg'),
(51, 1, 192, 0, 2, '2016-12-02', NULL, 'conclusa', 'tipoIndagine6', 'motivo6', 'referto6.pdf', NULL),
(52, 1, 190, 43, NULL, NULL, NULL, 'richiesta', 'Indagine dem.', 'Indagine: demenza (10-09-2016)', NULL, NULL),
(54, 1, 192, 35, 2, '2017-02-08', NULL, 'programmata', 'Indagini urinarie', 'Diagnosi: infezione vie urinarie (23-04-2016)', NULL, NULL);

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
  ADD KEY `idcpp` (`idcpp`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `indagini`
--
ALTER TABLE `indagini`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `indagini`
--
ALTER TABLE `indagini`
  ADD CONSTRAINT `FK_indagini` FOREIGN KEY (`idpaziente`) REFERENCES `pazienti` (`idutente`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
