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

CREATE TABLE posts (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL,
  `text` varchar(160) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB AUTO_INCEREMENT=1;

INSERT INTO posts (`user_id`, `title`, `text` ) VALUES (1, 'hi', 'hello'), (1, 'hiya', 'hellow'), (2, 'hibla', 'helluw'), (1, 'hiyadadadada', 'hellowowowow');