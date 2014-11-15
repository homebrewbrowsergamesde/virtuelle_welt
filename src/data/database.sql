-- Copyright (C) 2011-2012  Stephan Kreutzer
--
-- This file is part of Welt.
--
-- Welt is free software: you can redistribute it and/or modify
-- it under the terms of the GNU Affero General Public License as published by
-- the Free Software Foundation, version 3 of the License.
--
-- Welt is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU Affero General Public License for more details.
--
-- You should have received a copy of the GNU Affero General Public License
-- along with Welt.  If not, see <http://www.gnu.org/licenses/>.



CREATE DATABASE `welt` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE welt;

CREATE USER 'weltuser'@'localhost' IDENTIFIED BY 'password';
GRANT USAGE ON *.* TO 'weltuser'@'localhost' IDENTIFIED BY 'password' WITH MAX_QUERIES_PER_HOUR 0
    MAX_CONNECTIONS_PER_HOUR 0
    MAX_UPDATES_PER_HOUR 0
    MAX_USER_CONNECTIONS 0;
GRANT ALL PRIVILEGES ON `welt`.* TO 'weltuser'@'localhost';

CREATE TABLE IF NOT EXISTS `objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`file`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_x` int(11) NOT NULL,
  `map_y` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `position_x` int(11) NOT NULL,
  `position_y` int(11) NOT NULL,
  `order_z` int(11) NOT NULL,
  `from` time NOT NULL,
  `to` time NOT NULL,
  `visible` time DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `map_coords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_x` int(11) NOT NULL,
  `map_y` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `order_z` int(11) NOT NULL,
  `from` time NOT NULL,
  `to` time NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `coords` text COLLATE utf8_bin NOT NULL,
  `href` varchar(255) COLLATE utf8_bin NOT NULL,
  `alt` varchar(255) COLLATE utf8_bin NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `object_id` int(11) NOT NULL,
  `position_x` int(11) NOT NULL,
  `position_y` int(11) NOT NULL,
  `order_z` int(11) NOT NULL,
  `from` time NOT NULL,
  `to` time NOT NULL,
  `visible` time DEFAULT NULL,
  `night` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `detail_coords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `detail_id` int(11) NOT NULL,
  `order_z` int(11) NOT NULL,
  `from` time NOT NULL,
  `to` time NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `coords` text COLLATE utf8_bin NOT NULL,
  `href` varchar(255) COLLATE utf8_bin NOT NULL,
  `alt` varchar(255) COLLATE utf8_bin NOT NULL,
  `title` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'background_0_0.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 1, 0, 599, 0, '00:00:00', '24:00:00', NULL);
INSERT INTO `map_coords` (`id`, `map_x`, `map_y`, `map_id`, `order_z`, `from`, `to`, `visible`, `coords`, `href`, `alt`, `title`) VALUES (NULL, 0, 0, 1, 0, '07:00:00', '21:30:00', 0, '322,460,322,408,328,399,338,395,350,396,360,407,366,433,366,459', 'detail.php?name=rathaus_innen', 'Ins Rathaus', 'Ins Rathaus');
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'rathaus_innen.png');
INSERT INTO `detail` (`id`, `name`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`, `night`) VALUES (NULL, 'rathaus_innen', 2, 0, 599, 0, '07:00:00', '21:30:00', NULL, 0);
INSERT INTO `detail_coords` (`id`, `name`, `detail_id`, `order_z`, `from`, `to`, `visible`, `coords`, `href`, `alt`, `title`) VALUES (NULL, 'rathaus_innen', 1, 0, '07:00:00', '21:30:00', 0, '796,599,780,581,606,585,587,551,47,551,4,599', 'index.php?x=0&y=0', 'Verlassen', 'Verlassen');
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'uhr1_0.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 3, 244, 271, 1, '00:00:00', '01:00:00', NULL);
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 3, 244, 271, 1, '12:00:00', '13:00:00', NULL);
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'uhr1_1.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 4, 244, 271, 1, '01:00:00', '02:00:00', NULL);
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 4, 244, 271, 1, '13:00:00', '14:00:00', NULL);
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'uhr1_2.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 5, 244, 271, 1, '02:00:00', '03:00:00', NULL);
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 5, 244, 271, 1, '14:00:00', '15:00:00', NULL);
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'uhr1_3.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 6, 244, 271, 1, '03:00:00', '04:00:00', NULL);
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 6, 244, 271, 1, '15:00:00', '16:00:00', NULL);
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'uhr1_4.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 7, 244, 271, 1, '04:00:00', '05:00:00', NULL);
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 7, 244, 271, 1, '16:00:00', '17:00:00', NULL);
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'uhr1_5.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 8, 244, 271, 1, '05:00:00', '06:00:00', NULL);
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 8, 244, 271, 1, '17:00:00', '18:00:00', NULL);
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'uhr1_6.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 9, 244, 271, 1, '06:00:00', '07:00:00', NULL);
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 9, 244, 271, 1, '18:00:00', '19:00:00', NULL);
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'uhr1_7.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 10, 244, 271, 1, '07:00:00', '08:00:00', NULL);
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 10, 244, 271, 1, '19:00:00', '20:00:00', NULL);
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'uhr1_8.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 11, 244, 271, 1, '08:00:00', '09:00:00', NULL);
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 11, 244, 271, 1, '20:00:00', '21:00:00', NULL);
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'uhr1_9.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 12, 244, 271, 1, '09:00:00', '10:00:00', NULL);
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 12, 244, 271, 1, '21:00:00', '22:00:00', NULL);
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'uhr1_10.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 13, 244, 271, 1, '10:00:00', '11:00:00', NULL);
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 13, 244, 271, 1, '22:00:00', '23:00:00', NULL);
INSERT INTO `objects` (`id`, `file`) VALUES (NULL, 'uhr1_11.png');
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 14, 244, 271, 1, '11:00:00', '12:00:00', NULL);
INSERT INTO `map` (`id`, `map_x`, `map_y`, `object_id`, `position_x`, `position_y`, `order_z`, `from`, `to`, `visible`) VALUES (NULL, 0, 0, 14, 244, 271, 1, '23:00:00', '24:00:00', NULL);
