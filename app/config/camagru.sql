DROP DATABASE IF EXISTS camagru;
CREATE DATABASE camagru;

USE camagru;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `comments` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`userid` int(11) NOT NULL,
	`postid` int(11) NOT NULL,
	`comment` text NOT NULL,
	PRIMARY KEY (`id`),
	KEY `userid` (`userid`,`postid`),
	KEY `postid` (`postid`)
);

CREATE TABLE `images` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`postid` int(11) NOT NULL,
	`image_path` varchar(100) NOT NULL,
	`sticker_path` varchar(100) NOT NULL,
	`userimage_path` varchar(100) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `postid` (`postid`)
);

CREATE TABLE `likes` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`userid` int(11) NOT NULL,
	`postid` int(11) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `userid` (`userid`,`postid`),
	KEY `postid` (`postid`)
);

CREATE TABLE `posts` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`userid` int(11) NOT NULL,
	`title` varchar(255) NOT NULL,
	`body` text NOT NULL,
	`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`imageid` int(11) DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `userid` (`userid`)
);

CREATE TABLE `users` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`uname` varchar(255) NOT NULL UNIQUE,
	`email` varchar(255) NOT NULL UNIQUE,
	`password` varchar(255) NOT NULL,
	`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`reset_Hash` varchar(255) DEFAULT NULL,
	`verify_Hash` varchar(255) DEFAULT NULL,
	`notifications` tinyint(1) DEFAULT '1',
	PRIMARY KEY (`id`)
);


-- RELATIONSHIP Constraints
ALTER TABLE `comments`
	ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`postid`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `images`
	ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`postid`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `likes`
	ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`postid`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `posts`
	ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;