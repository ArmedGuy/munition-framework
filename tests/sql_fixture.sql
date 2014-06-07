CREATE DATABASE munition_test;
USE munition_test;

CREATE TABLE users (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `type` int(11) NOT NULL,
  `login_count` int(11) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;

INSERT INTO users (`name`, `password`, `type`,`login_count`) VALUES ('ArmedGuy', 'datpass', 1, 300), ('EmiiilK', 'hammas', 2, 2), ('Hannzas', ':3', 1, 1337);

CREATE TABLE user_profiles (
  `user_id` int(11) NOT NULL,
  `avatar_url` varchar(255) NOT NULL,
  `bio_raw` TEXT NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;

INSERT INTO user_profiles (`user_id`, `avatar_url`, `bio_raw`) VALUES (1, 'http://localhost.png', ''), (2, 'https://example.com/.png', '');

CREATE TABLE posts (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `text` varchar(160) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;

INSERT INTO posts (`user_id`, `title`, `text` ) VALUES (1, 'hi', 'hello'), (1, 'hiya', 'hellow'), (2, 'hibla', 'helluw'), (1, 'hiyadadadada', 'hellowowowow');


CREATE TABLE groups (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;

INSERT INTO groups (`name`) VALUES ('Awesome Group'), ('Less Awesome Group');

CREATE TABLE user_groups (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `is_admin` boolean NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;

INSERT INTO user_groups (`user_id`, `group_id`, `is_admin`) VALUES (1, 1, true), (2, 2, true), (3, 1, false);