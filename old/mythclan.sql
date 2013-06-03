-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Апр 14 2013 г., 20:35
-- Версия сервера: 5.5.27
-- Версия PHP: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `mythclan`
--

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `author` varchar(15) NOT NULL DEFAULT '',
  `poluchatel` varchar(15) NOT NULL DEFAULT '',
  `date` datetime NOT NULL,
  `text` text NOT NULL,
  `read` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `oshibka`
--

CREATE TABLE IF NOT EXISTS `oshibka` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(12) NOT NULL,
  `date` datetime NOT NULL,
  `col` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `oshibka`
--

INSERT INTO `oshibka` (`id`, `ip`, `date`, `col`) VALUES
(2, '::1', '2013-04-14 12:05:02', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id_post` int(11) NOT NULL AUTO_INCREMENT,
  `name` int(11) NOT NULL,
  `id_author` int(11) NOT NULL,
  `hide` int(1) NOT NULL DEFAULT '0',
  `time` datetime NOT NULL,
  `parent_post` int(11) NOT NULL,
  `id_theme` int(11) NOT NULL,
  PRIMARY KEY (`id_post`),
  KEY `fk_id_theme` (`id_theme`),
  KEY `fk_id_author` (`id_author`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `soobs`
--

CREATE TABLE IF NOT EXISTS `soobs` (
  `id_soob` int(10) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `rule` text NOT NULL,
  `logo` text NOT NULL,
  `hide` int(1) NOT NULL DEFAULT '1',
  `id_author` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_soob`),
  KEY `id_author` (`id_author`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='soobshestva polzovatelei' AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `soobs`
--

INSERT INTO `soobs` (`id_soob`, `name`, `rule`, `logo`, `hide`, `id_author`) VALUES
(2, '123', '123', 'avatars/lfooto.png', 1, 70),
(10, 'twhtrhwrth', 'wrthwerthwrth', 'avatars/lfooto.png', 0, 74);

-- --------------------------------------------------------

--
-- Структура таблицы `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `id_theme` int(11) NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET utf8 NOT NULL,
  `id_author` int(11) NOT NULL,
  `hide` int(1) NOT NULL DEFAULT '1',
  `time` datetime NOT NULL,
  `id_soob` int(11) NOT NULL,
  PRIMARY KEY (`id_theme`),
  KEY `id_author` (`id_author`),
  KEY `fk_id_soob` (`id_soob`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='список тем в сообществах' AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `themes`
--

INSERT INTO `themes` (`id_theme`, `name`, `id_author`, `hide`, `time`, `id_soob`) VALUES
(1, '????????', 70, 0, '2013-04-14 14:17:21', 2),
(2, '12356', 70, 0, '2013-04-14 14:50:17', 2),
(3, 'ÐŸÑ€Ð¸Ð²ÐµÑ‚! ÐºÐ°Ðº Ð´ÐµÐ»Ð°?', 74, 0, '2013-04-14 14:50:53', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id_u` int(11) NOT NULL AUTO_INCREMENT,
  `login` char(15) NOT NULL,
  `password` char(255) NOT NULL,
  `text` mediumblob NOT NULL,
  `avatar` char(255) NOT NULL,
  `email` char(250) NOT NULL,
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id_u`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=69 ;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` char(15) NOT NULL,
  `password` char(255) NOT NULL,
  `avatar` char(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `icq` text NOT NULL,
  `url` text NOT NULL,
  `about` text NOT NULL,
  `activation` int(1) NOT NULL DEFAULT '0',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `statususer` int(2) NOT NULL,
  `last_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `login` (`login`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=75 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `avatar`, `email`, `icq`, `url`, `about`, `activation`, `date`, `statususer`, `last_time`) VALUES
(66, 'esa', '07b432d25170b469b57095ca269bc202b3p6f', 'avatars/lfooto.png', 'mythclan@mail.ru', '', '', '', 1, '2012-12-19', 0, '2013-04-06 22:04:14'),
(68, 'sem', '617b8d3c6175cfae3eba798384be56eab3p6f', 'avatars/lfooto.png', 'mythclan@mail.ru', '', '', '', 1, '2012-12-21', 0, '0000-00-00 00:00:00'),
(69, 'myth', '07b432d25170b469b57095ca269bc202b3p6f', 'avatars/1356329688.jpg', 'mythclan@mail.ru', '', '', '', 1, '2012-12-24', 0, '0000-00-00 00:00:00'),
(70, '123', '07b432d25170b469b57095ca269bc202b3p6f', 'avatars/lfooto.png', 'semseriou@gmail.com', '', '', '', 1, '2012-12-24', 0, '2013-04-14 10:03:41'),
(74, 'DjSem', '07b432d25170b469b57095ca269bc202b3p6f', 'avatars/20130414095451314.jpg', 'semseriou@gmail.com', '', '', 'Ð§Ñ‚Ð¾ Ð²Ñ‹ Ð¼Ð¾Ð¶ÐµÑ‚Ðµ ÑÐ¾Ð¾Ð±Ñ‰Ð¸Ñ‚ÑŒ Ð¾ ÑÐµÐ±Ðµ?', 1, '2013-04-14', 2, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `video`
--

CREATE TABLE IF NOT EXISTS `video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `name` varchar(11) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_id_author` FOREIGN KEY (`id_author`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_id_theme` FOREIGN KEY (`id_theme`) REFERENCES `themes` (`id_theme`);

--
-- Ограничения внешнего ключа таблицы `soobs`
--
ALTER TABLE `soobs`
  ADD CONSTRAINT `id_author` FOREIGN KEY (`id_author`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `themes`
--
ALTER TABLE `themes`
  ADD CONSTRAINT `fk_id_soob` FOREIGN KEY (`id_soob`) REFERENCES `soobs` (`id_soob`),
  ADD CONSTRAINT `fk_id_suthor` FOREIGN KEY (`id_author`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
