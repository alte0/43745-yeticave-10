CREATE DATABASE `yeticave` 
  DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `yeticave`;

CREATE TABLE `users` ( 
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    `date_reg` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `email` VARCHAR(255) NOT NULL UNIQUE, 
    `name` VARCHAR(255) NOT NULL , 
    `password` VARCHAR(255) NOT NULL , 
    `contacts` VARCHAR(255) NOT NULL
  ) 
  ENGINE = InnoDB 
  COMMENT = 'Таблица пользователей';
  
CREATE TABLE `сategories` ( 
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    `name` VARCHAR(255) NOT NULL UNIQUE, 
    `character_code` VARCHAR(255) NOT NULL UNIQUE
  ) 
  ENGINE = InnoDB 
  COMMENT = 'Таблица категорий';

CREATE TABLE `lots` ( 
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    `name` VARCHAR(255) NOT NULL, 
    `description` VARCHAR(1000) NOT NULL, 
    `image` VARCHAR(255) NOT NULL , 
    `start_price` INT UNSIGNED NOT NULL , 
    `date_completion` TIMESTAMP NOT NULL , 
    `date_create` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `step` INT UNSIGNED NOT NULL , 
    `user_id` INT NOT NULL , 
    `user_id_winner` INT, 
    `category_id` INT NOT NULL ,
    INDEX(`name`),
    INDEX(`description`),
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (user_id_winner) REFERENCES users (id),
    FOREIGN KEY (category_id) REFERENCES сategories (id),
    FULLTEXT (name,description)
  ) 
  ENGINE = InnoDB 
  COMMENT = 'Таблица лотов';

CREATE TABLE `bets` ( 
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    `date_create` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `price` INT UNSIGNED NOT NULL , 
    `user_id` INT NOT NULL , 
    `lot_id` INT NOT NULL ,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (lot_id) REFERENCES lots (id)
  ) 
  ENGINE = InnoDB 
  COMMENT = 'Таблица ставок';
