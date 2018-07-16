CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT (11) NOT NULL DEFAUlT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(11) DEFAULT NULL,
  `exclude_from_cashflow` tinyint(1) NOT NULL DEFAULT '0',
  `type` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  key `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `user_id` INT (11) NOT NULL DEFAUlT 0,
  PRIMARY KEY (`id`),
  key `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT (11) NOT NULL DEFAUlT 0,
  `date` date DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `account_id` (`account_id`),
  KEY `category_id` (`category_id`),
  key `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `keyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT (11) NOT NULL DEFAUlT 0,
  `keyword` varchar(255) NOT NULL DEFAULT '',
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),  
  key `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`first_name` VARCHAR(50) NOT NULL DEFAULT '',
	`last_name` VARCHAR(50) NOT NULL DEFAULT '',
	`login` VARCHAR(100) NOT NULL DEFAULT '',
	`password` VARCHAR(100) NOT NULL DEFAULT '',
	`email` VARCHAR(100) NOT NULL DEFAULT '',
	`currency` INT(3),
	PRIMARY KEY (`id`),
	INDEX `auth` (login, password)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8;