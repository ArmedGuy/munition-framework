CREATE DATABASE munition_test;
USE munition_test;

CREATE TABLE users (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1;

INSERT INTO users (`name`, `password`) VALUES ('ArmedGuy', 'datpass'), ('EmiiilK', 'hammas'), ('Hannzas', ':3');