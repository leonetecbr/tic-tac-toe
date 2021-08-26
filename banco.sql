-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 25-Ago-2021 às 13:22
-- Versão do servidor: 10.5.8-MariaDB
-- versão do PHP: 8.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+03:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `tictactoe`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `matches`
--

CREATE TABLE `matches` (
  `hash` varchar(42) NOT NULL,
  `next` varchar(42) NOT NULL,
  `x` varchar(64) NOT NULL,
  `o` varchar(64) DEFAULT NULL,
  `1` tinyint(1) DEFAULT NULL,
  `2` tinyint(1) DEFAULT NULL,
  `3` tinyint(1) DEFAULT NULL,
  `4` tinyint(1) DEFAULT NULL,
  `5` tinyint(1) DEFAULT NULL,
  `6` tinyint(1) DEFAULT NULL,
  `7` tinyint(1) DEFAULT NULL,
  `8` tinyint(1) DEFAULT NULL,
  `9` tinyint(1) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`hash`);
  ADD UNIQUE KEY `next` (`next`);
DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=``@`localhost` EVENT `expiress` ON SCHEDULE EVERY 30 MINUTE STARTS '2021-08-25 10:22:07' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM matches WHERE (CURRENT_TIMESTAMP-matches.created)>600$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
