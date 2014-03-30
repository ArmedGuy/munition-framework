CREATE DATABASE munition_test;
USE munition_test;

CREATE TABLE users (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `group_id` int(11) NOT NULL,
  `num_posts` int(11) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;

INSERT INTO users (`name`, `password`, `group_id`,`num_posts`) VALUES ('ArmedGuy', 'datpass', 1, 300), ('EmiiilK', 'hammas', 2, 2), ('Hannzas', ':3', 1, 1337);