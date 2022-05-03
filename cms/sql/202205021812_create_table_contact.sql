--
-- Struktura tabulky `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `house_number` varchar(255) DEFAULT NULL,
  `zip` int(11) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `contact`
--

INSERT INTO `contact` (`id`, `company`, `street`, `house_number`, `zip`, `city`, `state`, `email`, `phone`) VALUES
(1, 'Jan Purchart', 'Ulice', '123', 73101, 'Město', 'Česká republika', 'jprace@icloud.com', '+420 605 123 456');