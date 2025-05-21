USE demo_shop;

CREATE TABLE `admins` (
                          `id` int NOT NULL AUTO_INCREMENT,
                          `username` varchar(255) NOT NULL,
                          `password` varchar(255) NOT NULL,
                          PRIMARY KEY (`id`),
                          UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
