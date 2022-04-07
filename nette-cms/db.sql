
DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_add` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `has_picture` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `article` (`id`, `date_add`, `has_picture`, `title`, `url`, `short_description`, `description`) VALUES
(1, '2020-03-15 11:54:20', 1, 'odpoved 1', '1-odpoved', 'Tohle je odpoved 1', 'Tohle je odpoved 1'),
(2, '2020-03-16 11:05:31', 1, 'odpoved 2', '2-odpoved', 'Tohle je odpoved 2', 'Tohle je odpoved 2');


CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `role` enum('member','admin') COLLATE utf8_czech_ci NOT NULL DEFAULT 'member',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `user` (`id`, `firstname`, `lastname`, `email`, `password`, `role`) VALUES
(1, 'Karel', 'Káš', 'karel@newlogic.cz', '$2y$10$d2/375NlEfkG08GqmiWbl.B/tdXa8E40kt.Op3EFiJBxfJlX0CgD2', 'admin');


DROP TABLE IF EXISTS `article_category`;
CREATE TABLE IF NOT EXISTS `article_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `article_category_unique` (`article_id`,`category_id`),
  KEY `category_id` (`category_id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


INSERT INTO `article_category` (`id`, `article_id`, `category_id`) VALUES
(6, 1, 1),
(3, 2, 2);


DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


INSERT INTO `category` (`id`, `name`, `url`) VALUES
(1, 'Anketa 1', 'anketa-1'),
(2, 'Anketa 2', 'anketa-2'),
(3, 'Anketa 3', 'anketa-3');

ALTER TABLE `article_category`
  ADD CONSTRAINT `article_category_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `article_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;