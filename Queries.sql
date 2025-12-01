CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO users
(id, username, password)
VALUES(1, 'admin', '$2y$10$1oJoW2QjLU8hr0RYYajcWO4YKaHqYunPv1TCSLZphsDlOC0rr8OBK');

CREATE TABLE `variables` (
  `key` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO variables
(`key`, value)
VALUES('theme', 'default');

INSERT INTO variables
(`key`, value)
VALUES('version', '1');

CREATE TABLE `expense_balance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `expense_name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `person` int NOT NULL,
  `date` date DEFAULT NULL,
  `creation_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expense_balance_book_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `expense_balance_book` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `persons` varchar(100) NOT NULL,
  `creation_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `app_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO app_users
(id, username, password)
VALUES(1, 'jrv', '$2y$10$7Jcr0kj23BGMHZRhFdLd3e9XSvL82pmIFfl3/u3LcmyB9ZOtiervy');

CREATE TABLE `mileage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `date` datetime NOT NULL,
  `fuel_state` varchar(255) DEFAULT NULL,
  `comments` text,
  `odometer_reading` decimal(10,2) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `odometer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `date` date NOT NULL,
  `start_distance` decimal(10,1) NOT NULL,
  `end_distance` decimal(10,1) NOT NULL DEFAULT '0.0',
  `comment` text,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=225 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `vehicles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `user_id` int NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `vt_maintenance ` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `date` date NOT NULL,
  `odometer_start` decimal(10,2) NOT NULL,
  `odometer_due` decimal(10,2) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `vt_notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_id` int NOT NULL,
  `date` date NOT NULL,
  `note` text NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

RENAME TABLE vehicles TO vt_vehicles;
RENAME TABLE odometer TO vt_odometer;
RENAME TABLE mileage TO vt_mileage;
-- Bill Split Tracker
-- Bill Split Book Table
CREATE TABLE IF NOT EXISTS `bill_split_book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `persons` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
);

-- Bill Split Table
CREATE TABLE IF NOT EXISTS `bill_split` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_name` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `paid_by` varchar(100) NOT NULL,
  `split_type` enum('equal','custom') NOT NULL DEFAULT 'equal',
  `splits` json NOT NULL,
  `date` date NOT NULL,
  `bill_split_book_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `bill_split_book_id` (`bill_split_book_id`),
  FOREIGN KEY (`bill_split_book_id`) REFERENCES `bill_split_book` (`id`) ON DELETE CASCADE
);

-- Event Tracker Tables
CREATE TABLE `events_anniversary` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `original_date` date NOT NULL,
  `added_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_events_user` (`user_id`)
);

CREATE TABLE `events_anniversary_celebration` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int unsigned NOT NULL,
  `date` date NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_celebrations_event` (`event_id`),
  KEY `idx_celebrations_date` (`date`),
  CONSTRAINT `fk_celebration_event` FOREIGN KEY (`event_id`) REFERENCES `events_anniversary` (`id`) ON DELETE CASCADE
);


-- Notebook tables

CREATE TABLE `notebook` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notebook_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE `notebook_page` (
  `id` int NOT NULL AUTO_INCREMENT,
  `notebook_id` int NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `markdown_content` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `notebook_id` (`notebook_id`),
  CONSTRAINT `notebook_page_ibfk_1` FOREIGN KEY (`notebook_id`) REFERENCES `notebook` (`id`) ON DELETE CASCADE
);

ALTER TABLE notebook_page CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE events_anniversary ADD `type` varchar(100) NULL;

-- Cashbook tables

CREATE TABLE IF NOT EXISTS `cashbook_book` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `creation_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `cashbook_bank_account` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `initial_balance` decimal(14,2) NOT NULL DEFAULT '0.00',
  `cashbook_book_id` int NOT NULL DEFAULT 0,
  `creation_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `cashbook_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `cashbook_book_id` int NOT NULL DEFAULT 0,
  `creation_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `cashbook_entry` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `type` enum('expense','income') NOT NULL DEFAULT 'expense',
  `category_id` int DEFAULT NULL,
  `bank_account_id` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `cashbook_book_id` int NOT NULL DEFAULT 0,
  `creation_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Food', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Snacks', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Groceries', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Fuel', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Health & Medical', 0);

INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Fashions', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Rent', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Home Maintenance', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Personal Care', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Entertainment', 0);

INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Education', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Gifts & Donations', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Travel', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Outing', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Recreation', 0);

INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Salary', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Business Income', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Investment Income', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Other Income', 0);
INSERT INTO cashbook_category (name, cashbook_book_id) VALUES ('Lending', 0);
