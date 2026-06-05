-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 19, 2026 alle 17:12
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pepperoni_db`
--

CREATE DATABASE pepperoni_db;
USE pepperoni_db;

-- --------------------------------------------------------

--
-- Struttura della tabella `assegnazione_turno`
--

CREATE TABLE `assegnazione_turno` (
  `dipendente_id` int(11) NOT NULL,
  `turno_id` int(11) NOT NULL,
  `veicolo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `assegnazione_turno`
--

INSERT INTO `assegnazione_turno` (`dipendente_id`, `turno_id`, `veicolo_id`) VALUES
(3, 1, NULL),
(3, 2, NULL),
(4, 1, NULL),
(4, 2, NULL),
(2, 1, 1),
(2, 2, 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `consegna`
--

CREATE TABLE `consegna` (
  `id` int(11) NOT NULL,
  `ordine_id` int(11) NOT NULL,
  `fattorino_id` int(11) DEFAULT NULL,
  `via` varchar(255) NOT NULL,
  `n_civico` varchar(20) NOT NULL,
  `citta` varchar(100) NOT NULL,
  `cap` varchar(10) NOT NULL,
  `stato` varchar(50) NOT NULL CHECK (`stato` in ('CREATA','IN_ATTESA','IN_CONSEGNA','CONSEGNATA'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `consegna`
--

INSERT INTO `consegna` (`id`, `ordine_id`, `fattorino_id`, `via`, `n_civico`, `citta`, `cap`, `stato`) VALUES
(1, 1, 2, 'Fausto Coppi', '1', 'Bologna', '40132', 'CONSEGNATA');

-- --------------------------------------------------------

--
-- Struttura della tabella `dettaglio_ordine`
--

CREATE TABLE `dettaglio_ordine` (
  `id` int(11) NOT NULL,
  `ordine_id` int(11) NOT NULL,
  `pizza_id` int(11) NOT NULL,
  `prezzo_finale` decimal(6,2) NOT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `dettaglio_ordine`
--

INSERT INTO `dettaglio_ordine` (`id`, `ordine_id`, `pizza_id`, `prezzo_finale`, `note`) VALUES
(1, 1, 2, 4.50, NULL),
(2, 1, 4, 6.50, NULL),
(3, 1, 6, 6.00, NULL),
(4, 2, 5, 5.50, NULL),
(5, 2, 2, 4.50, NULL),
(6, 2, 4, 6.50, NULL),
(7, 2, 3, 2.50, NULL),
(8, 2, 6, 6.00, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `dipendente`
--

CREATE TABLE `dipendente` (
  `id` int(11) NOT NULL,
  `ruolo` enum('FATTORINO','PIZZAIOLO','CASSIERE') NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `dipendente`
--

INSERT INTO `dipendente` (`id`, `ruolo`, `password`) VALUES
(2, 'FATTORINO', '$2y$10$f0Gg2kSP1JqhYUIKGeJhj.SmZUCwxDFYSdiqixKvr30CyQCbUmvgO'),
(3, 'PIZZAIOLO', '$2y$10$CbOiqJ7Lk5mYpkvbtJxGm.COWecrJYXZ.EJgW6dTdOTSdZRQiwUc2'),
(4, 'CASSIERE', '$2y$10$1/3dwqAane1KKV82C8eAvOhhnRfq1l8F1jWzNgsKESg05STSOlkG2');

-- --------------------------------------------------------

--
-- Struttura della tabella `ingrediente`
--

CREATE TABLE `ingrediente` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `prezzo` decimal(6,2) NOT NULL,
  `descrizione` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `ingrediente`
--

INSERT INTO `ingrediente` (`id`, `nome`, `prezzo`, `descrizione`) VALUES
(2, 'Mozzarella', 0.50, ''),
(3, 'Pomodoro', 0.50, ''),
(4, 'Salsiccia', 2.00, ''),
(5, 'Salame piccante', 1.50, ''),
(6, 'Origano', 0.50, ''),
(7, 'Acciughe', 2.00, ''),
(8, 'Olive nere', 0.50, 'Senza nocciolo');

-- --------------------------------------------------------

--
-- Struttura della tabella `ordine`
--

CREATE TABLE `ordine` (
  `id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `data` datetime NOT NULL,
  `stato` varchar(50) NOT NULL,
  `tipo_acquisizione` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `ordine`
--

INSERT INTO `ordine` (`id`, `utente_id`, `data`, `stato`, `tipo_acquisizione`) VALUES
(1, 1, '2026-05-19 16:58:04', 'COMPLETATO', 'DOMICILIO'),
(2, 1, '2026-05-19 16:58:14', 'COMPLETATO', 'ASPORTO');

-- --------------------------------------------------------

--
-- Struttura della tabella `persona`
--

CREATE TABLE `persona` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `persona`
--

INSERT INTO `persona` (`id`, `nome`, `cognome`) VALUES
(1, 'Mario', 'Rossi'),
(2, 'Luigi', 'Verdi'),
(3, 'Anna', 'Gialli'),
(4, 'Massimo', 'Bianchi');

-- --------------------------------------------------------

--
-- Struttura della tabella `pizza`
--

CREATE TABLE `pizza` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `prezzo` decimal(6,2) NOT NULL,
  `disponibilita` tinyint(1) NOT NULL,
  `dimensione` enum('S','M','L') NOT NULL,
  `impasto` enum('classico','integrale','senza_glutine') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `pizza`
--

INSERT INTO `pizza` (`id`, `nome`, `descrizione`, `prezzo`, `disponibilita`, `dimensione`, `impasto`) VALUES
(2, 'Margherita', '', 4.50, 1, 'S', 'classico'),
(3, 'Piada', 'Con olio e origano', 2.50, 1, 'S', 'classico'),
(4, 'Napoletana', '', 6.50, 1, 'S', 'classico'),
(5, 'Diavola', '', 5.50, 1, 'S', 'classico'),
(6, 'Salsiccia', '', 8.00, 1, 'S', 'classico');

-- --------------------------------------------------------

--
-- Struttura della tabella `recensione`
--

CREATE TABLE `recensione` (
  `id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `ordine_id` int(11) NOT NULL,
  `data_pubblicazione` datetime NOT NULL,
  `voto` int(11) NOT NULL,
  `commento` text DEFAULT NULL,
  `stato` varchar(50) NOT NULL CHECK (`voto` >= 1 and `voto` <= 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `recensione`
--

INSERT INTO `recensione` (`id`, `utente_id`, `ordine_id`, `data_pubblicazione`, `voto`, `commento`, `stato`) VALUES
(1, 1, 2, '2026-05-19 17:06:53', 5, 'Molto buone', 'PUBBLICATA'),
(2, 1, 1, '2026-05-19 17:07:19', 3, 'La margherita era un po\' bruciata, il resto era buono', 'PUBBLICATA');

-- --------------------------------------------------------

--
-- Struttura della tabella `revisione`
--

CREATE TABLE `revisione` (
  `veicolo_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `prezzo` decimal(6,2) NOT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `revisione`
--

INSERT INTO `revisione` (`veicolo_id`, `data`, `prezzo`, `note`) VALUES
(1, '2026-05-07', 70.00, 'Cambio pastiglie freni e olio motore'),
(2, '2026-05-11', 30.00, 'Cambio olio motore');

-- --------------------------------------------------------

--
-- Struttura della tabella `ricetta`
--

CREATE TABLE `ricetta` (
  `pizza_id` int(11) NOT NULL,
  `ingrediente_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `ricetta`
--

INSERT INTO `ricetta` (`pizza_id`, `ingrediente_id`) VALUES
(2, 2),
(2, 3),
(2, 6),
(3, 6),
(4, 2),
(4, 3),
(4, 6),
(4, 7),
(5, 2),
(5, 3),
(5, 5),
(6, 2),
(6, 3),
(6, 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `turno`
--

CREATE TABLE `turno` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `ora_inizio` time NOT NULL,
  `ora_fine` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `turno`
--

INSERT INTO `turno` (`id`, `data`, `ora_inizio`, `ora_fine`) VALUES
(1, '2026-05-20', '11:30:00', '13:30:00'),
(2, '2026-05-20', '18:00:00', '21:30:00');

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `id_persona` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `stato` varchar(50) NOT NULL,
  `data_registrazione` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`id_persona`, `email`, `username`, `password`, `stato`, `data_registrazione`) VALUES
(1, 'mario.rossi@gmail.com', 'mariorossi', '$2y$10$v.qax9rNDubqt9yDB0ephubT0DWsBu6KiQBgZqnMbt.wX4Bbn6lfy', 'ATTIVO', '2026-05-19 15:57:03');

-- --------------------------------------------------------

--
-- Struttura della tabella `veicolo`
--

CREATE TABLE `veicolo` (
  `id` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `stato_operativo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `veicolo`
--

INSERT INTO `veicolo` (`id`, `tipo`, `stato_operativo`) VALUES
(1, 'Automobile', 'OPERATIVO'),
(2, 'Motorino', 'OPERATIVO');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `assegnazione_turno`
--
ALTER TABLE `assegnazione_turno`
  ADD PRIMARY KEY (`dipendente_id`,`turno_id`),
  ADD KEY `turno_id` (`turno_id`),
  ADD KEY `veicolo_id` (`veicolo_id`);

--
-- Indici per le tabelle `consegna`
--
ALTER TABLE `consegna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ordine_id` (`ordine_id`),
  ADD KEY `fattorino_id` (`fattorino_id`);

--
-- Indici per le tabelle `dettaglio_ordine`
--
ALTER TABLE `dettaglio_ordine`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ordine_id` (`ordine_id`),
  ADD KEY `pizza_id` (`pizza_id`);

--
-- Indici per le tabelle `dipendente`
--
ALTER TABLE `dipendente`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `ingrediente`
--
ALTER TABLE `ingrediente`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `ordine`
--
ALTER TABLE `ordine`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utente_id` (`utente_id`);

--
-- Indici per le tabelle `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `pizza`
--
ALTER TABLE `pizza`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `recensione`
--
ALTER TABLE `recensione`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ordine_id` (`ordine_id`),
  ADD KEY `utente_id` (`utente_id`);

--
-- Indici per le tabelle `revisione`
--
ALTER TABLE `revisione`
  ADD PRIMARY KEY (`veicolo_id`,`data`);

--
-- Indici per le tabelle `ricetta`
--
ALTER TABLE `ricetta`
  ADD PRIMARY KEY (`pizza_id`,`ingrediente_id`),
  ADD KEY `ingrediente_id` (`ingrediente_id`);

--
-- Indici per le tabelle `turno`
--
ALTER TABLE `turno`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`id_persona`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indici per le tabelle `veicolo`
--
ALTER TABLE `veicolo`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `consegna`
--
ALTER TABLE `consegna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `dettaglio_ordine`
--
ALTER TABLE `dettaglio_ordine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT per la tabella `ingrediente`
--
ALTER TABLE `ingrediente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `ordine`
--
ALTER TABLE `ordine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `persona`
--
ALTER TABLE `persona`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `pizza`
--
ALTER TABLE `pizza`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `recensione`
--
ALTER TABLE `recensione`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `turno`
--
ALTER TABLE `turno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `veicolo`
--
ALTER TABLE `veicolo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `assegnazione_turno`
--
ALTER TABLE `assegnazione_turno`
  ADD CONSTRAINT `assegnazione_turno_ibfk_1` FOREIGN KEY (`dipendente_id`) REFERENCES `dipendente` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assegnazione_turno_ibfk_2` FOREIGN KEY (`turno_id`) REFERENCES `turno` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assegnazione_turno_ibfk_3` FOREIGN KEY (`veicolo_id`) REFERENCES `veicolo` (`id`) ON DELETE SET NULL;

--
-- Limiti per la tabella `consegna`
--
ALTER TABLE `consegna`
  ADD CONSTRAINT `consegna_ibfk_1` FOREIGN KEY (`ordine_id`) REFERENCES `ordine` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consegna_ibfk_2` FOREIGN KEY (`fattorino_id`) REFERENCES `dipendente` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `dettaglio_ordine`
--
ALTER TABLE `dettaglio_ordine`
  ADD CONSTRAINT `dettaglio_ordine_ibfk_1` FOREIGN KEY (`ordine_id`) REFERENCES `ordine` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dettaglio_ordine_ibfk_2` FOREIGN KEY (`pizza_id`) REFERENCES `pizza` (`id`);

--
-- Limiti per la tabella `dipendente`
--
ALTER TABLE `dipendente`
  ADD CONSTRAINT `dipendente_ibfk_1` FOREIGN KEY (`id`) REFERENCES `persona` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `ordine`
--
ALTER TABLE `ordine`
  ADD CONSTRAINT `ordine_ibfk_1` FOREIGN KEY (`utente_id`) REFERENCES `utente` (`id_persona`) ON DELETE CASCADE;

--
-- Limiti per la tabella `recensione`
--
ALTER TABLE `recensione`
  ADD CONSTRAINT `recensione_ibfk_1` FOREIGN KEY (`utente_id`) REFERENCES `utente` (`id_persona`) ON DELETE CASCADE,
  ADD CONSTRAINT `recensione_ibfk_2` FOREIGN KEY (`ordine_id`) REFERENCES `ordine` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `revisione`
--
ALTER TABLE `revisione`
  ADD CONSTRAINT `revisione_ibfk_1` FOREIGN KEY (`veicolo_id`) REFERENCES `veicolo` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `ricetta`
--
ALTER TABLE `ricetta`
  ADD CONSTRAINT `ricetta_ibfk_1` FOREIGN KEY (`pizza_id`) REFERENCES `pizza` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ricetta_ibfk_2` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingrediente` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `utente`
--
ALTER TABLE `utente`
  ADD CONSTRAINT `utente_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
