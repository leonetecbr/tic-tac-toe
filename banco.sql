SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Estrutura da tabela  `matches`
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
-- Indíces da tabela `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`hash`),
  ADD UNIQUE KEY `next` (`next`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;