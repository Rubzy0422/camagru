DROP DATABASE camagru;
CREATE DATABASE camagru;

USE camagru;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `image_path` varchar(100) NOT NULL,
  `sticker_path` varchar(100) NOT NULL,
  `userimage_path` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
);

CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `imageid` int(11) DEFAULT NULL,

  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
);


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reset_Hash` varchar(255) DEFAULT NULL,
  `verify_Hash` varchar(255) DEFAULT NULL,
  `notifications` tinyint(1) DEFAULT '1',

  PRIMARY KEY (`id`)
);

ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`post_id`)
   REFERENCES `posts` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
 
 ALTER TABLE `posts`
   ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
 COMMIT; 