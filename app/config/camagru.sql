DROP database IF EXISTS camagru;
CREATE database camagru;

USE camagru;
CREATE TABLE `posts` (
	`id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`title` varchar(255) NOT NULL,
	`body` text NOT NULL,
	`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `users` (
	`id` int(11) NOT NULL,
	`uname` varchar(255) NOT NULL,
	`email` varchar(255) NOT NULL,
	`password` varchar(255) NOT NULL,
	`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`reset_Hash` varchar(255),
	`verify_Hash` varchar(255)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `posts`
	ADD PRIMARY KEY (`id`);


ALTER TABLE `users`
	ADD PRIMARY KEY (`id`);


ALTER TABLE `posts`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `users`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;