SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  `content` mediumtext NOT NULL,
  `time` int(11) NOT NULL,
  `form` text NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

CREATE TABLE IF NOT EXISTS `item_user` (
  `item_id` int(11) NOT NULL,
  `user_id` varchar(16) NOT NULL,
  `user_form` text NOT NULL,
  `user_time` int(11) NOT NULL,
  PRIMARY KEY (`item_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` varchar(16) NOT NULL,
  `display_name` varchar(64) NOT NULL,
  `password` varchar(256) NOT NULL,
  `type` enum('common','admin','system') NOT NULL DEFAULT 'common',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `user` (`user_id`, `display_name`, `password`, `type`) VALUES
('root', 'root', '$2y$07$dYmalBt0uJNWyq7oT94xsu61qhmcisTdwmc1pPDClGiwvdM04keSq', 'system');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
