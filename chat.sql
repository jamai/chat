CREATE TABLE `chat` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sender` INT(10) UNSIGNED NOT NULL,
	`message` TEXT NOT NULL,
	`created` INT(10) UNSIGNED NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE `user` (
	`user_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(50) NOT NULL COMMENT '用户名',
	`created` INT(10) UNSIGNED NOT NULL COMMENT '用户生成时间',
	PRIMARY KEY (`user_id`),
	INDEX `username` (`username`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;
