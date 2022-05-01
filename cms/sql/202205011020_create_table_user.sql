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
(1, 'Jan', 'Purchart', 'jprace@icloud.com', '$2y$10$arJIJie/xGoqZayCro4yZ.pPEkt9Ps4DJBNZAHSZ/rvbOkj//K/tq', 'admin')