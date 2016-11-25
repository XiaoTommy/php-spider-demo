DROP TABLE IF EXISTS `kankandou`;

CREATE TABLE `kankandou` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `book_name` varchar(20) DEFAULT NULL,
  `book_author` varchar(20) DEFAULT NULL,
  `book_format` varchar(20) NOT NULL DEFAULT '',
  `book_class` varchar(10) NOT NULL DEFAULT '',
  `click_num` int(11) NOT NULL DEFAULT '0',
  `download_num` int(11) NOT NULL DEFAULT '0',
  `book_content` text,
  `book_img` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8