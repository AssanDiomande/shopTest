
CREATE DATABASE IF NOT EXISTS `wsTest`;

USE `wsTest`;

DROP TABLE IF EXISTS `store`;

CREATE TABLE `store` (
  `store_id` INTEGER NOT NULL AUTO_INCREMENT,
  `store_name` varchar(100) CHARACTER SET utf8,
  `store_adress` varchar(100) CHARACTER SET utf8,
  `store_owner` varchar(100) CHARACTER SET utf8,
  `store_created_at` DATE,
  PRIMARY KEY (`store_id`),
  UNIQUE KEY `store_name` (`store_name`)
);


INSERT INTO `store` (`store_name`,`store_adress`,`store_owner`,`store_created_at`) VALUES 
('store 1', 'adress 1', 'owner 1', '2024-07-17'),
('store 2', 'adress 2', 'owner 1', '2024-07-17'),
('store 3', 'adress 3', 'owner 2', '2024-07-17');


DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `email` varchar(100) CHARACTER SET utf8,
  `password` varchar(100) CHARACTER SET utf8,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
);


INSERT INTO `user` (`email`,`password`) VALUES ('user@test.com', '$2y$10$qhBTNejPHuHisEooQCwZJORu4PeNzLcjOCmPmxIrvm7Z2d7TlRxge');


DROP TABLE IF EXISTS `log`;

CREATE TABLE `log` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `message` varchar(100) CHARACTER SET utf8,
  `type` varchar(100) CHARACTER SET utf8,
  `store_created_at` DATE,
  PRIMARY KEY (`id`)
);