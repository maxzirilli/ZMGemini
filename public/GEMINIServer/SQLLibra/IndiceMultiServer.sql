-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 19, 2025 alle 10:57
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
-- Database: `main_gemini`
--

DELIMITER $$
--
-- Funzioni
--
CREATE FUNCTION `GET_NEW_KEY` () RETURNS INT(11)  BEGIN
 DECLARE Result INT DEFAULT 0;

 UPDATE gen_key
   SET CHIAVE = CHIAVE + 1;

SELECT MAX(CHIAVE) FROM gen_key INTO Result;
 RETURN Result;  

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `articoli`
--

CREATE TABLE `articoli` (
  `CHIAVE` int(11) NOT NULL,
  `DATA` date DEFAULT NULL,
  `TITOLO` varchar(255) DEFAULT NULL,
  `OGGETTO` longtext DEFAULT NULL,
  `IMMAGINE` mediumblob DEFAULT NULL,
  `TESTO` longtext DEFAULT NULL,
  `RIASSUNTO` longtext DEFAULT NULL,
  `ORA` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Struttura della tabella `customers`
--

CREATE TABLE `customers` (
  `PARTITA_IVA` varchar(20) NOT NULL,
  `DATABASE` varchar(255) NOT NULL,
  `SERVER` varchar(255) NOT NULL,
  `ACCOUNT` varchar(255) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `customers`
--

INSERT INTO `customers` (`PARTITA_IVA`, `DATABASE`, `SERVER`, `ACCOUNT`, `PASSWORD`) VALUES
('02017301009', 'gemini', 'localhost', 'root', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `gen_key`
--

CREATE TABLE `gen_key` (
  `CHIAVE` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dump dei dati per la tabella `gen_key`
--

INSERT INTO `gen_key` (`CHIAVE`) VALUES
(10000);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `articoli`
--
ALTER TABLE `articoli`
  ADD PRIMARY KEY (`CHIAVE`) USING BTREE;

--
-- Indici per le tabelle `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`PARTITA_IVA`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
