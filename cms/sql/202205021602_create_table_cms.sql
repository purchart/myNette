--
-- Struktura tabulky `cms`
--

DROP TABLE IF EXISTS `cms`;
CREATE TABLE IF NOT EXISTS `cms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `on_homepage` int(11) DEFAULT '0',
  `has_picture` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `cms`
--

INSERT INTO `cms` (`id`, `date_add`, `on_homepage`, `has_picture`, `title`, `url`, `description`) VALUES
(1, '2020-03-15 10:54:29', 1, 1, 'Vítejte', 'vitejte', 'Toto je úvodní uvítací článek systému ArgoMi - RS.'),
(3, '2020-03-16 14:25:52', 0, 0, 'Servis', 'service', 'Obsah cms stránky servis.'),
(5, '2020-03-22 09:16:08', 0, 0, 'O Nás', 'o-nas', '<p>Stránka o systému ArgoMi - RS.</p>');