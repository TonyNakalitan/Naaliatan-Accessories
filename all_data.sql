-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: Cartlify_db
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
REPLACE INTO `user` VALUES
(1,'Antony','antony@naaliatan.com','$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS','["ROLE_ADMIN"]','2026-03-01 00:00:00',NULL,'Antony',NULL,NULL,1,0,'local',1,NULL,NULL,NULL),
(2,'Samson','samson@naaliatan.com','$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS','["ROLE_ADMIN"]','2026-03-01 00:00:00',NULL,'Samson',NULL,NULL,1,0,'local',1,NULL,NULL,NULL),
(8,'Antonio','antonio@naaliatan.com','$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS','["ROLE_STAFF"]','2026-03-05 00:00:00',NULL,'Antonio',NULL,NULL,1,0,'local',1,NULL,NULL,NULL),
(11,'amil','amil@naaliatan.com','$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS','["ROLE_STAFF"]','2026-03-05 00:00:00',NULL,'Amil',NULL,NULL,1,0,'local',1,NULL,NULL,NULL),
(14,'admin','admin@naaliatan.com','$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS','["ROLE_ADMIN"]','2026-03-09 00:00:00',NULL,'Administrator',NULL,NULL,1,0,'local',1,NULL,NULL,NULL),
(15,'staff','staff@naaliatan.com','$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS','["ROLE_STAFF"]','2026-03-09 00:00:00',NULL,'Staff',NULL,NULL,1,0,'local',1,NULL,NULL,NULL),
(17,'luna','luna@naaliatan.com','$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS','["ROLE_USER"]','2026-03-10 00:00:00',NULL,'Luna',NULL,NULL,1,0,'local',1,NULL,NULL,NULL),
(21,'Dodot','dodot@naaliatan.com','$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS','["ROLE_ADMIN"]','2026-03-15 00:00:00',NULL,'Dodot',NULL,NULL,1,0,'local',1,NULL,NULL,NULL),
(24,'Venom','venom@naaliatan.com','$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS','["ROLE_ADMIN"]','2026-03-15 00:00:00',NULL,'Venom',NULL,NULL,1,0,'local',1,NULL,NULL,NULL),
(25,'Anthony','anthony@naaliatan.com','$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS','["ROLE_USER"]','2026-03-20 00:00:00',NULL,'Anthony',NULL,NULL,1,0,'local',1,NULL,NULL,NULL),
(28,'Arjay','arjay@naaliatan.com','$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS','["ROLE_USER"]','2026-03-22 00:00:00',NULL,'Arjay',NULL,NULL,1,0,'local',1,NULL,NULL,NULL),
(31,'weaver','weaver@naaliatan.com','$2y$13$m7QtT9NTybSteCX0VAVBs.3zGGtK80YTjv6rtGOPeEieQtzw6XDlS','["ROLE_USER"]','2026-03-10 00:00:00',NULL,'Weaver',NULL,NULL,1,0,'local',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `game_character`
--

LOCK TABLES `game_character` WRITE;
/*!40000 ALTER TABLE `game_character` DISABLE KEYS */;
REPLACE INTO `game_character` VALUES
(1,'Naruto Uzumaki','Masashi Kishimoto','The main protagonist of the Naruto series, a young ninja who seeks recognition and dreams of becoming Hokage.','Good','naruto.jpg','#FF6B00','2026-03-09 00:00:00',1),
(2,'Sasuke Uchiha','Masashi Kishimoto','A rival and friend of Naruto, driven by revenge and later redemption.','Neutral','sasuke.jpg','#1A1A2E','2026-03-09 00:00:00',1),
(3,'Goku','Akira Toriyama','The main protagonist of Dragon Ball Z, a Saiyan warrior who protects Earth.','Good','goku.jpg','#FF8C00','2026-03-09 00:00:00',1),
(4,'Vegeta','Akira Toriyama','The prince of the Saiyan race and rival of Goku, who eventually becomes a hero.','Neutral','vegeta.jpg','#4B0082','2026-03-09 00:00:00',1),
(5,'Luffy','Eiichiro Oda','The captain of the Straw Hat Pirates who dreams of becoming King of the Pirates.','Good','luffy.jpg','#FF0000','2026-03-09 00:00:00',1),
(6,'Zoro','Eiichiro Oda','The swordsman of the Straw Hat Pirates who aims to become the world\'s greatest swordsman.','Good','zoro.jpg','#228B22','2026-03-09 00:00:00',1);
/*!40000 ALTER TABLE `game_character` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
REPLACE INTO `product` VALUES
(1,'Naruto Headband','PROD-001','Official Naruto Konoha headband replica, high quality metal plate with adjustable cloth band.',NULL,350.00,1,'FF6B00',50,'2026-03-09 00:00:00',14),
(2,'Naruto Kunai Set','PROD-002','Set of 3 metal kunai replicas from the Naruto series, great for cosplay.',NULL,500.00,1,'FF6B00',30,'2026-03-09 00:00:00',14),
(3,'Sasuke Sharingan Shirt','PROD-003','Black t-shirt featuring the iconic Sharingan eye design from Naruto.',NULL,450.00,2,'1A1A2E',40,'2026-03-09 00:00:00',14),
(4,'Goku Action Figure','PROD-004','Detailed 15cm action figure of Goku in Super Saiyan form with interchangeable hands.',NULL,1200.00,3,'FF8C00',25,'2026-03-09 00:00:00',14),
(5,'Dragon Ball Z Poster Set','PROD-005','Set of 5 high-quality posters featuring iconic Dragon Ball Z scenes.',NULL,300.00,3,'FF8C00',60,'2026-03-09 00:00:00',14),
(6,'Vegeta Pride Hoodie','PROD-006','Premium hoodie with Vegeta\'s Saiyan crest embroidered on the chest.',NULL,1500.00,4,'4B0082',20,'2026-03-09 00:00:00',14),
(7,'Luffy Straw Hat','PROD-007','Replica of Luffy\'s iconic straw hat from One Piece, made from natural straw.',NULL,800.00,5,'FF0000',35,'2026-03-09 00:00:00',14),
(8,'One Piece Wanted Poster','PROD-008','Set of 6 wanted posters featuring the Straw Hat crew, printed on aged paper.',NULL,250.00,5,'FF0000',70,'2026-03-09 00:00:00',14),
(9,'Zoro Three Sword Set','PROD-009','Decorative replica of Zoro\'s three swords: Wado Ichimonji, Sandai Kitetsu, and Shusui.',NULL,3500.00,6,'228B22',10,'2026-03-09 00:00:00',14),
(10,'Queen Bed','PROD-010','Luxury queen-sized bed frame with anime-themed headboard design.',NULL,4500.00,1,'FF6B00',5,'2026-03-10 00:00:00',14),
(11,'Headphone','PROD-011','Anime-themed over-ear headphones with high-fidelity sound and character artwork.',NULL,2500.00,3,'FF8C00',50,'2026-03-10 00:00:00',14),
(12,'Blender','PROD-012','High-powered blender with anime character decals, 1000W motor.',NULL,4000.00,1,'FF6B00',55,'2026-03-10 00:00:00',14),
(13,'Vacuum Cleaner','PROD-013','Compact vacuum cleaner with anime-themed design, 2000W suction power.',NULL,3500.00,3,'FF8C00',55,'2026-03-10 00:00:00',14),
(14,'Pliers','PROD-014','Heavy-duty pliers with anime character grip handles.',NULL,350.00,2,'1A1A2E',70,'2026-03-10 00:00:00',14),
(15,'Rope','PROD-015','10-meter nylon rope with anime character packaging.',NULL,200.00,4,'4B0082',70,'2026-03-10 00:00:00',14),
(16,'MacBook','PROD-016','Laptop sleeve with anime artwork, compatible with MacBook 13-15 inch.',NULL,1800.00,5,'FF0000',6,'2026-03-10 00:00:00',14),
(17,'Lamp','PROD-017','LED desk lamp with anime character base design, adjustable brightness.',NULL,200.00,6,'228B22',30,'2026-03-10 00:00:00',14),
(18,'Christmas Ball','PROD-018','Set of 12 anime-themed Christmas ornament balls.',NULL,800.00,1,'FF6B00',40,'2026-03-10 00:00:00',14),
(19,'Frying Pan','PROD-019','Non-stick frying pan with anime character handle design, 28cm diameter.',NULL,40.00,3,'FF8C00',20,'2026-03-10 00:00:00',14),
(20,'Bowl','PROD-020','Ceramic bowl set with anime character illustrations, set of 4.',NULL,800.00,5,'FF0000',15,'2026-03-10 00:00:00',14),
(21,'Towel','PROD-021','Soft cotton towel with anime character print, 70x140cm.',NULL,350.00,2,'1A1A2E',25,'2026-03-10 00:00:00',14),
(22,'Light Bulb','PROD-022','LED light bulb with anime character packaging, 9W warm white.',NULL,150.00,4,'4B0082',100,'2026-03-10 00:00:00',14),
(23,'airpod','PROD-023','Wireless earbuds case with anime character artwork.',NULL,2200.00,6,'228B22',44,'2026-03-10 00:00:00',14);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `stock`
--

LOCK TABLES `stock` WRITE;
/*!40000 ALTER TABLE `stock` DISABLE KEYS */;
REPLACE INTO `stock` VALUES
(1,11,50,NULL,'2026-03-09 00:00:00',14),
(2,12,55,'Initial stock','2026-03-09 00:00:00',14),
(3,13,50,'Initial stock','2026-03-09 00:00:00',14),
(4,14,70,'Initial stock','2026-03-09 00:00:00',14),
(5,15,70,'Initial stock','2026-03-09 00:00:00',14),
(6,16,6,'Initial stock','2026-03-09 00:00:00',14),
(7,23,44,'Initial stock','2026-03-22 08:27:59',14);
/*!40000 ALTER TABLE `stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `stock_transaction`
--

LOCK TABLES `stock_transaction` WRITE;
/*!40000 ALTER TABLE `stock_transaction` DISABLE KEYS */;
REPLACE INTO `stock_transaction` VALUES
(1,12,14,'RESTOCK',55,'Initial restock','2026-03-09 00:00:00'),
(2,13,14,'RESTOCK',50,'Initial restock','2026-03-09 00:00:00'),
(3,11,14,'RESTOCK',50,'Initial restock','2026-03-09 00:00:00'),
(4,14,14,'RESTOCK',70,'Initial restock','2026-03-09 00:00:00'),
(5,15,14,'RESTOCK',70,'Initial restock','2026-03-09 00:00:00'),
(6,16,14,'RESTOCK',6,'Initial restock','2026-03-09 00:00:00'),
(7,23,14,'RESTOCK',44,'Initial restock','2026-03-22 08:27:59');
/*!40000 ALTER TABLE `stock_transaction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
REPLACE INTO `cart` VALUES
(1,17,'2026-03-10 00:00:00','2026-03-10 00:00:00'),
(2,25,'2026-03-20 00:00:00','2026-03-20 00:00:00'),
(3,28,'2026-03-22 00:00:00','2026-03-22 00:00:00'),
(4,31,'2026-03-10 00:00:00','2026-03-10 00:00:00');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `cart_item`
--

LOCK TABLES `cart_item` WRITE;
/*!40000 ALTER TABLE `cart_item` DISABLE KEYS */;
REPLACE INTO `cart_item` VALUES
(1,1,7,1,'2026-03-22 00:00:00'),
(2,1,1,2,'2026-03-22 00:00:00'),
(3,2,4,1,'2026-03-23 00:00:00'),
(4,3,9,1,'2026-03-23 00:00:00');
/*!40000 ALTER TABLE `cart_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
REPLACE INTO `order` VALUES
(1,'ORD-A1B2C3D4',17,4500.00,'completed','2026-03-21 04:05:55','2026-03-22 00:00:00','Luna Santos','123 Rizal Street','Cebu City','Cebu','standard','09171234567','gcash','2026-03-21 04:10:00'),
(2,'ORD-E5F6G7H8',25,1200.00,'pending','2026-03-23 14:15:00',NULL,'Anthony Reyes','456 Mabini Avenue','Davao City','Davao del Sur','express','09281234567','cash',NULL),
(3,'ORD-I9J0K1L2',28,3500.00,'processing','2026-03-23 17:35:00',NULL,'Arjay Cruz','789 Bonifacio Road','Manila','Metro Manila','standard','09391234567','bank_transfer',NULL),
(4,'ORD-M3N4O5P6',17,800.00,'completed','2026-03-25 10:00:00','2026-03-26 00:00:00','Luna Santos','123 Rizal Street','Cebu City','Cebu','standard','09171234567','gcash','2026-03-25 10:30:00'),
(5,'ORD-Q7R8S9T0',31,700.00,'cancelled','2026-03-28 09:00:00',NULL,'Weaver Dela Cruz','321 Aguinaldo Blvd','Quezon City','Metro Manila','standard','09451234567','cash',NULL);
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `order_item`
--

LOCK TABLES `order_item` WRITE;
/*!40000 ALTER TABLE `order_item` DISABLE KEYS */;
REPLACE INTO `order_item` VALUES
(1,1,10,1,4500.00,4500.00),
(2,2,4,1,1200.00,1200.00),
(3,3,9,1,3500.00,3500.00),
(4,4,18,1,800.00,800.00),
(5,5,17,1,200.00,200.00),
(6,5,22,3,150.00,450.00);
/*!40000 ALTER TABLE `order_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `payment`
--

LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
REPLACE INTO `payment` VALUES
(1,'PAY-A1B2C3D4',1,17,'gcash',4500.00,'completed',NULL,14,'2026-03-21 04:12:00','2026-03-21 04:10:00'),
(2,'PAY-E5F6G7H8',4,17,'gcash',800.00,'completed',NULL,14,'2026-03-25 10:35:00','2026-03-25 10:30:00'),
(3,'PAY-I9J0K1L2',2,25,'cash',1200.00,'pending','Awaiting cash payment on delivery',NULL,NULL,'2026-03-23 14:20:00'),
(4,'PAY-M3N4O5P6',3,28,'bank_transfer',3500.00,'pending','Bank transfer initiated, awaiting confirmation',NULL,NULL,'2026-03-23 17:40:00');
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
REPLACE INTO `activity_log` VALUES
(622,1,'Antony','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product: Griller (Price: $2000)','2026-03-21 03:15:50'),
(623,1,'Antony','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product: Blender (Price: $5000)','2026-03-21 03:24:20'),
(624,1,'Antony','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited product: Queen Bed (Price: $5400.00)','2026-03-21 03:24:38'),
(625,1,'Antony','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created stock: Griller x 88 units','2026-03-21 03:25:38'),
(626,1,'Antony','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited stock: Headphone → 6 units','2026-03-21 03:25:54'),
(627,1,'Antony','ROLE_ADMIN','LOGOUT','User logout: Antony (ID: 1)','2026-03-21 03:34:07'),
(628,8,'Antonio','ROLE_STAFF','LOGIN','User login: Antonio (ID: 8)','2026-03-21 03:34:22'),
(629,8,'Antonio','ROLE_STAFF','LOGOUT','User logout: Antonio (ID: 8)','2026-03-21 03:36:25'),
(630,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-21 03:38:14'),
(631,14,'admin','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product: Whisk (Price: $555)','2026-03-21 03:40:23'),
(632,14,'admin','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product: Bowl (Price: $95)','2026-03-21 03:41:55'),
(633,14,'admin','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product: Bowl (Price: $95)','2026-03-21 03:42:57'),
(634,14,'admin','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited product: Whisker (Price: $555.00)','2026-03-21 03:44:10'),
(635,14,'admin','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created stock: Blender x 55 units','2026-03-21 03:44:38'),
(636,14,'admin','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited stock: Blender → 51 units','2026-03-21 03:45:02'),
(637,14,'admin','ROLE_ADMIN','ADMIN_CREATES_USER','Admin created user: Sunny (Role: Admin) (Password set)','2026-03-21 03:57:34'),
(638,14,'admin','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: Sunny (Password changed only)','2026-03-21 03:57:50'),
(639,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-21 04:02:39'),
(640,1,'Antony','ROLE_ADMIN','LOGIN','User login: Antony (ID: 1)','2026-03-21 04:03:02'),
(641,1,'Antony','ROLE_ADMIN','USER_UPDATES_PROFILE','User updated their profile: Antony (Password changed)','2026-03-21 04:03:31'),
(642,1,'Antony','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created order: Queen Bed x 1 units','2026-03-21 04:05:55'),
(643,1,'Antony','ROLE_ADMIN','ADMIN_CREATES_USER','Admin created user: Dodot (Role: Admin) (Password set)','2026-03-21 04:14:36'),
(644,1,'Antony','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: Dodot (Password changed only)','2026-03-21 04:14:52'),
(645,1,'Antony','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: weaver (No changes)','2026-03-21 04:15:09'),
(646,1,'Antony','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product: Glass (Price: $2000)','2026-03-21 04:16:58'),
(647,1,'Antony','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited product: Queen Bed (Price: $5400.00)','2026-03-21 04:17:29'),
(648,1,'Antony','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: weaver (No changes)','2026-03-21 04:19:05'),
(649,1,'Antony','ROLE_ADMIN','ADMIN_DELETES_RECORD','Admin deleted product (ID: 45)','2026-03-21 04:21:22'),
(650,1,'Antony','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product: Glass (Price: $200)','2026-03-21 04:22:38'),
(651,1,'Antony','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited product: Glass (Price: $100.00)','2026-03-21 04:23:04'),
(652,1,'Antony','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created stock: Glass x 2 units','2026-03-21 04:23:29'),
(653,1,'Antony','ROLE_ADMIN','ADMIN_DELETES_RECORD','Admin deleted stock (ID: 36)','2026-03-21 04:23:37'),
(654,1,'Antony','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited stock: Headphone → 3 units','2026-03-21 04:23:48'),
(655,1,'Antony','ROLE_ADMIN','ADMIN_DELETES_RECORD','Admin deleted product (ID: 46)','2026-03-21 04:24:14'),
(656,1,'Antony','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created stock: Vacuum Cleaner x 3 units','2026-03-21 04:25:03'),
(657,1,'Antony','ROLE_ADMIN','ADMIN_DELETES_RECORD','Admin deleted stock (ID: 37)','2026-03-21 04:25:11'),
(658,1,'Antony','ROLE_ADMIN','ADMIN_CREATES_USER','Admin created user: add (Role: Admin)','2026-03-21 04:25:46'),
(659,1,'Antony','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: add → Role changed from Staff to Staff','2026-03-21 04:25:57'),
(660,1,'Antony','ROLE_ADMIN','ADMIN_DELETES_USER','Admin deleted user (ID: 22)','2026-03-21 04:26:03'),
(661,1,'Antony','ROLE_ADMIN','ADMIN_CREATES_USER','Admin created user: adwdwaa (Role: Admin)','2026-03-21 04:26:51'),
(662,1,'Antony','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: adwdwaa → Role changed from User to User (Password changed)','2026-03-21 04:27:07'),
(663,1,'Antony','ROLE_ADMIN','ADMIN_DELETES_USER','Admin deleted user (ID: 23)','2026-03-21 04:27:15'),
(664,1,'Antony','ROLE_ADMIN','USER_UPDATES_PROFILE','User updated their profile: Antony (Password changed)','2026-03-21 04:27:36'),
(665,1,'Antony','ROLE_ADMIN','LOGOUT','User logout: Antony (ID: 1)','2026-03-21 04:29:34'),
(666,21,'Dodot','ROLE_ADMIN','LOGIN','User login: Dodot (ID: 21)','2026-03-21 04:30:03'),
(667,21,'Dodot','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product: Bowl (Price: $800)','2026-03-21 05:02:38'),
(668,21,'Dodot','ROLE_ADMIN','ADMIN_DELETES_RECORD','Admin deleted product (ID: 47)','2026-03-21 05:34:46'),
(669,21,'Dodot','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product: Bowl (Price: $800)','2026-03-21 05:52:35'),
(670,21,'Dodot','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product:  (Price: $)','2026-03-21 05:54:56'),
(671,21,'Dodot','ROLE_ADMIN','USER_UPDATES_PROFILE','User updated their profile: Dodot (Password changed)','2026-03-21 05:58:10'),
(672,21,'Dodot','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: weaver → Role changed from User to User','2026-03-21 05:58:51'),
(673,21,'Dodot','ROLE_ADMIN','LOGOUT','User logout: Dodot (ID: 21)','2026-03-21 05:59:15'),
(674,2,'Samson','ROLE_ADMIN','LOGIN','User login: Samson (ID: 2)','2026-03-21 05:59:31'),
(675,2,'Samson','ROLE_ADMIN','LOGOUT','User logout: Samson (ID: 2)','2026-03-21 06:00:46'),
(676,2,'Samson','ROLE_ADMIN','LOGIN','User login: Samson (ID: 2)','2026-03-21 06:01:09'),
(677,2,'Samson','ROLE_ADMIN','ADMIN_DELETES_RECORD','Admin deleted product (ID: 48)','2026-03-21 06:12:42'),
(678,2,'Samson','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product: Bowl (Price: $800)','2026-03-21 06:13:15'),
(679,2,'Samson','ROLE_ADMIN','LOGOUT','User logout: Samson (ID: 2)','2026-03-21 06:22:45'),
(680,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-21 06:22:54'),
(681,14,'admin','ROLE_ADMIN','ADMIN_CREATES_USER','Admin created user: Amil (Role: Admin)','2026-03-21 06:23:24'),
(682,14,'admin','ROLE_ADMIN','ADMIN_CREATES_USER','Admin created user: Amil (Role: Admin)','2026-03-21 06:23:29'),
(683,14,'admin','ROLE_ADMIN','ADMIN_CREATES_USER','Admin created user: Venom (Role: Admin)','2026-03-21 06:23:57'),
(684,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-21 06:24:13'),
(685,24,'Venom','ROLE_ADMIN','LOGIN','User login: Venom (ID: 24)','2026-03-21 06:24:28'),
(686,24,'Venom','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product:  (Price: $)','2026-03-21 06:32:59'),
(687,24,'Venom','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product:  (Price: $)','2026-03-21 06:33:03'),
(688,24,'Venom','ROLE_ADMIN','ADMIN_DELETES_RECORD','Admin deleted product (ID: 49)','2026-03-21 06:33:34'),
(689,24,'Venom','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited product: Queen Bed (Price: $4400.00)','2026-03-21 06:38:15'),
(690,24,'Venom','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created product: Bowl (Price: $800)','2026-03-21 06:40:22'),
(691,24,'Venom','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created stock: Blender x 33 units','2026-03-21 07:04:42'),
(692,24,'Venom','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: amil → Role changed from Staff to Staff (Password changed)','2026-03-21 07:59:30'),
(693,24,'Venom','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: weaver → Role changed from User to User (Password changed)','2026-03-21 07:59:48'),
(694,24,'Venom','ROLE_ADMIN','LOGOUT','User logout: Venom (ID: 24)','2026-03-21 08:00:10'),
(695,15,'staff','ROLE_STAFF','LOGIN','User login: staff (ID: 15)','2026-03-21 08:01:33'),
(696,15,'staff','ROLE_STAFF','STAFF_CREATES_RECORD','Staff created stock: Bowl x 5 units','2026-03-21 08:07:23'),
(697,15,'staff','ROLE_STAFF','LOGOUT','User logout: staff (ID: 15)','2026-03-21 08:07:54'),
(698,11,'amil','ROLE_STAFF','LOGIN','User login: amil (ID: 11)','2026-03-21 08:08:05'),
(699,11,'amil','ROLE_STAFF','LOGOUT','User logout: amil (ID: 11)','2026-03-21 08:09:13'),
(700,17,'luna','ROLE_USER','LOGIN','User login: luna (ID: 17)','2026-03-21 08:09:26'),
(701,17,'luna','ROLE_USER','LOGOUT','User logout: luna (ID: 17)','2026-03-21 08:51:20'),
(702,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-21 08:51:27'),
(703,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-21 08:53:52'),
(704,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-22 03:34:49'),
(705,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-22 03:38:52'),
(706,15,'staff','ROLE_STAFF','LOGIN','User login: staff (ID: 15)','2026-03-22 03:39:04'),
(707,15,'staff','ROLE_STAFF','LOGOUT','User logout: staff (ID: 15)','2026-03-22 03:40:04'),
(708,17,'luna','ROLE_USER','LOGIN','User login: luna (ID: 17)','2026-03-22 03:40:16'),
(709,17,'luna','ROLE_USER','LOGOUT','User logout: luna (ID: 17)','2026-03-22 03:42:05'),
(710,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-22 06:08:36'),
(711,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-22 06:09:30'),
(712,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-22 06:37:28'),
(713,14,'admin','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited stock: Vacuum Cleaner → 50 units','2026-03-22 08:25:58'),
(714,14,'admin','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited stock: Pliers → 40 units','2026-03-22 08:26:16'),
(715,14,'admin','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited stock: Rope → 44 units','2026-03-22 08:26:32'),
(716,14,'admin','ROLE_ADMIN','ADMIN_DELETES_RECORD','Admin deleted stock (ID: 18)','2026-03-22 08:27:18'),
(717,14,'admin','ROLE_ADMIN','ADMIN_DELETES_RECORD','Admin deleted stock (ID: 22)','2026-03-22 08:27:27'),
(718,14,'admin','ROLE_ADMIN','ADMIN_DELETES_RECORD','Admin deleted stock (ID: 23)','2026-03-22 08:27:35'),
(719,14,'admin','ROLE_ADMIN','ADMIN_DELETES_RECORD','Admin deleted stock (ID: 25)','2026-03-22 08:27:44'),
(720,14,'admin','ROLE_ADMIN','ADMIN_CREATES_RECORD','Admin created stock: airpod x 44 units','2026-03-22 08:27:59'),
(721,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-22 18:56:07'),
(722,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-22 18:59:16'),
(723,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-23 14:05:45'),
(724,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-23 14:09:14'),
(725,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-23 14:10:59'),
(726,25,'Anthony','ROLE_USER','LOGIN','User login: Anthony (ID: 25)','2026-03-23 14:11:54'),
(727,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-23 14:59:44'),
(728,17,'luna','ROLE_USER','LOGIN','User login: luna (ID: 17)','2026-03-23 14:59:56'),
(729,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-23 15:05:28'),
(730,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-23 15:11:56'),
(731,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-23 15:22:03'),
(732,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-23 15:47:28'),
(733,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-23 15:50:10'),
(734,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-23 16:06:45'),
(735,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-23 16:11:01'),
(736,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-23 16:22:16'),
(737,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-23 16:22:54'),
(738,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-23 16:25:26'),
(739,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-23 16:40:40'),
(740,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-23 16:40:48'),
(741,17,'luna','ROLE_USER','LOGOUT','User logout: luna (ID: 17)','2026-03-23 16:43:15'),
(742,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-23 16:49:18'),
(743,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-23 16:49:31'),
(744,NULL,'Jajton','ROLE_USER','LOGIN','User login: Jajton (ID: 26)','2026-03-23 17:02:01'),
(745,14,'admin','ROLE_ADMIN','LOGOUT','User logout: admin (ID: 14)','2026-03-23 17:15:19'),
(746,NULL,'Jajton','ROLE_USER','LOGOUT','User logout: Jajton (ID: 26)','2026-03-23 17:16:45'),
(747,NULL,'loloy','ROLE_USER','LOGIN','User login: loloy (ID: 27)','2026-03-23 17:17:20'),
(748,NULL,'loloy','ROLE_USER','LOGOUT','User logout: loloy (ID: 27)','2026-03-23 17:30:17'),
(749,28,'Arjay','ROLE_USER','LOGIN','User login: Arjay (ID: 28)','2026-03-23 17:30:55'),
(750,28,'Arjay','ROLE_USER','LOGOUT','User logout: Arjay (ID: 28)','2026-03-23 17:31:52'),
(751,14,'admin','ROLE_ADMIN','LOGIN','User login: admin (ID: 14)','2026-03-29 05:10:16'),
(860,15,'staff','ROLE_STAFF','LOGIN','User login: staff (ID: 15)','2026-03-29 12:40:38'),
(861,15,'staff','ROLE_STAFF','LOGOUT','User logout: staff (ID: 15)','2026-03-29 12:40:55'),
(862,1,'Antony','ROLE_ADMIN','LOGIN','User login: Antony (ID: 1)','2026-03-29 12:42:18'),
(863,1,'Antony','ROLE_ADMIN','LOGOUT','User logout: Antony (ID: 1)','2026-03-29 12:42:42'),
(864,2,'Samson','ROLE_ADMIN','LOGIN','User login: Samson (ID: 2)','2026-03-29 13:08:35'),
(865,2,'Samson','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited product: Christmas Ball (Price: $800.00)','2026-03-29 13:09:32'),
(866,2,'Samson','ROLE_ADMIN','ADMIN_UPDATES_RECORD','Admin edited product: Frying Pan (Price: $40.00)','2026-03-29 13:10:05'),
(867,2,'Samson','ROLE_ADMIN','LOGOUT','User logout: Samson (ID: 2)','2026-03-29 13:10:10'),
(868,2,'Samson','ROLE_ADMIN','LOGIN','User login: Samson (ID: 2)','2026-03-29 13:22:01'),
(869,2,'Samson','ROLE_ADMIN','LOGOUT','User logout: Samson (ID: 2)','2026-03-29 13:22:36'),
(870,14,'admin','ROLE_ADMIN','ADMIN_DELETES_USER','Admin deleted user (ID: 27)','2026-03-29 13:23:30'),
(871,14,'admin','ROLE_ADMIN','ADMIN_CREATES_USER','Admin created user: loloy (Role: User)','2026-03-29 13:27:01'),
(872,14,'admin','ROLE_ADMIN','ADMIN_DELETES_USER','Admin deleted user (ID: 29)','2026-03-29 13:29:40'),
(873,14,'admin','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: Arjay → Role changed from User to User (Password changed)','2026-03-29 13:29:56'),
(874,14,'admin','ROLE_ADMIN','ADMIN_CREATES_USER','Admin created user: loloy (Role: Admin)','2026-03-29 13:30:16'),
(875,14,'admin','ROLE_ADMIN','ADMIN_DELETES_USER','Admin deleted user (ID: 30)','2026-03-29 13:31:07'),
(876,14,'admin','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: Dodot → Role changed from Admin to Admin (Password changed)','2026-03-29 13:34:17'),
(877,14,'admin','ROLE_ADMIN','ADMIN_DELETES_USER','Admin deleted user (ID: 26)','2026-03-29 13:34:34'),
(878,14,'admin','ROLE_ADMIN','ADMIN_CREATES_USER','Admin created user: loloy (Role: Admin)','2026-03-29 13:42:53'),
(879,14,'admin','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: loloy → Role changed from Admin to Admin (Password changed)','2026-03-29 13:46:21'),
(880,14,'admin','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: Antony → Role changed from Admin to Admin (Password changed)','2026-03-29 13:53:36'),
(881,14,'admin','ROLE_ADMIN','ADMIN_UPDATES_USER','Admin updated user: loloy → Role changed from Admin to Admin (Password changed)','2026-03-29 13:54:57');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
