-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 15, 2025 at 10:28 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos7`
--

-- --------------------------------------------------------

--
-- Table structure for table `pos_category`
--

DROP TABLE IF EXISTS `pos_category`;
CREATE TABLE IF NOT EXISTS `pos_category` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `category_status` enum('Active','Inactive') NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pos_category`
--

INSERT INTO `pos_category` (`category_id`, `category_name`, `category_status`) VALUES
(1, 'Fast Food', 'Active'),
(2, 'Meetha', 'Active'),
(3, 'Methai', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `pos_configuration`
--

DROP TABLE IF EXISTS `pos_configuration`;
CREATE TABLE IF NOT EXISTS `pos_configuration` (
  `config_id` int NOT NULL AUTO_INCREMENT,
  `restaurant_name` varchar(255) NOT NULL,
  `restaurant_address` varchar(255) NOT NULL,
  `restaurant_phone` varchar(20) NOT NULL,
  `restaurant_email` varchar(255) DEFAULT NULL,
  `opening_hours` varchar(255) DEFAULT NULL,
  `closing_hours` varchar(255) DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `logo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pos_configuration`
--

INSERT INTO `pos_configuration` (`config_id`, `restaurant_name`, `restaurant_address`, `restaurant_phone`, `restaurant_email`, `opening_hours`, `closing_hours`, `tax_rate`, `currency`, `logo`) VALUES
(1, 'Sample Restaurant', '123 Main Street, City', '123-456-7890', 'info@restaurant.com', '08:00 AM', '10:00 PM', 10.00, 'PKR', 'logo.png');

-- --------------------------------------------------------

--
-- Table structure for table `pos_order`
--

DROP TABLE IF EXISTS `pos_order`;
CREATE TABLE IF NOT EXISTS `pos_order` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `order_total` decimal(10,2) NOT NULL,
  `order_datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `order_created_by` int DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `order_created_by` (`order_created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pos_order`
--

INSERT INTO `pos_order` (`order_id`, `order_number`, `order_total`, `order_datetime`, `order_created_by`) VALUES
(1, 'ORD1742026928579', 2200.00, '2025-03-15 08:22:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pos_order_item`
--

DROP TABLE IF EXISTS `pos_order_item`;
CREATE TABLE IF NOT EXISTS `pos_order_item` (
  `order_item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_qty` int NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pos_order_item`
--

INSERT INTO `pos_order_item` (`order_item_id`, `order_id`, `product_name`, `product_qty`, `product_price`) VALUES
(1, 1, 'Chicken Broast', 2, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `pos_product`
--

DROP TABLE IF EXISTS `pos_product`;
CREATE TABLE IF NOT EXISTS `pos_product` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `category_id` int DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(100) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_status` enum('Available','Out of Stock') NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pos_product`
--

INSERT INTO `pos_product` (`product_id`, `category_id`, `product_name`, `product_image`, `product_price`, `product_status`) VALUES
(1, 1, 'Chicken Broast', '', 0.00, 'Available'),
(2, 1, 'Leg Piece', '', 0.00, 'Available'),
(3, 1, 'Shashlik Stick', '', 0.00, 'Available'),
(4, 1, 'Drum Stick', '', 0.00, 'Available'),
(5, 1, 'Chapal Kabab', '', 0.00, 'Available'),
(6, 1, 'Wings', '', 0.00, 'Available'),
(7, 1, 'Chicken Finger', '', 0.00, 'Available'),
(8, 1, 'Leg Roll', '', 0.00, 'Available'),
(9, 1, 'Chicken Pai', '', 0.00, 'Available'),
(10, 1, 'Deal Cheese Sandwich', '', 0.00, 'Available'),
(11, 1, 'Tikka Farai Sandwich', '', 0.00, 'Available'),
(12, 1, 'Single Farai Sandwich', '', 0.00, 'Available'),
(13, 1, 'Smoli Farai Burger', '', 0.00, 'Available'),
(14, 1, 'Single Sandwich', '', 0.00, 'Available'),
(15, 1, 'Tikka Sandwich Double', '', 0.00, 'Available'),
(16, 1, 'Cheese Sandwich', '', 0.00, 'Available'),
(17, 1, 'Club Sandwich', '', 0.00, 'Available'),
(18, 1, 'Chicken Pastry', '', 0.00, 'Available'),
(19, 1, 'Chicken Kundi', '', 0.00, 'Available'),
(20, 1, 'Pizza Chicken Roll', '', 0.00, 'Available'),
(21, 1, 'Pizza 1 Pound', '', 0.00, 'Available'),
(22, 1, 'Pizza Medium', '', 0.00, 'Available'),
(23, 1, 'Pizza Small', '', 0.00, 'Available'),
(24, 1, 'Pizza Takoon', '', 0.00, 'Available'),
(25, 1, 'Pizza Patti', '', 0.00, 'Available'),
(26, 1, 'Slice Pizza', '', 0.00, 'Available'),
(27, 1, 'Donut', '', 0.00, 'Available'),
(28, 1, 'Dinner Roll', '', 0.00, 'Available'),
(29, 1, 'Variety Pizza', '', 0.00, 'Available'),
(30, 1, 'Shami Kabab', '', 0.00, 'Available'),
(31, 1, 'Chicken Pokora', '', 0.00, 'Available'),
(32, 1, 'Salad', '', 0.00, 'Available'),
(33, 1, 'Namkeen Salad', '', 0.00, 'Available'),
(34, 1, 'Full Bread Pizza', '', 0.00, 'Available'),
(35, 1, 'Half Bread Pizza', '', 0.00, 'Available'),
(36, 1, 'Zinger Burger', '', 0.00, 'Available'),
(37, 1, 'Half Burger', '', 0.00, 'Available'),
(38, 1, 'Frozen Chicken Roll', '', 0.00, 'Available'),
(39, 1, 'Frozen Chicken Smosa', '', 0.00, 'Available'),
(40, 1, 'Frozen Sabzi Roll', '', 0.00, 'Available'),
(41, 1, 'Frozen Sabzi Smosa', '', 0.00, 'Available'),
(42, 1, 'Frozen Alu Roll', '', 0.00, 'Available'),
(43, 1, 'Frozen Alu Smosa', '', 0.00, 'Available'),
(44, 1, 'Frozen Shami Kabab Chicken', '', 0.00, 'Available'),
(45, 2, 'Chocolate Cake 1 Pound', '', 0.00, 'Available'),
(46, 2, 'Chocolate Cake 2 Pound', '', 0.00, 'Available'),
(47, 2, 'Variety Cake 1 Pound', '', 0.00, 'Available'),
(48, 2, 'Variety Cake 2 Pound', '', 0.00, 'Available'),
(49, 2, 'Cheese Cake 1 Pound', '', 0.00, 'Available'),
(50, 2, 'Cheese Cake 2 Pound', '', 0.00, 'Available'),
(51, 2, 'Dry Cake 1 Pound', '', 0.00, 'Available'),
(52, 2, 'Dry Cake 2 Pound', '', 0.00, 'Available'),
(53, 2, 'Almond Cake 1 Pound', '', 0.00, 'Available'),
(54, 2, 'Almond Cake 2 Pound', '', 0.00, 'Available'),
(55, 2, 'Fig Cake 1 Pound', '', 0.00, 'Available'),
(56, 2, 'Fig Cake 2 Pound', '', 0.00, 'Available'),
(57, 2, 'Fruit Cake Bara', '', 0.00, 'Available'),
(58, 2, 'Fruit Cake Chota', '', 0.00, 'Available'),
(59, 2, 'Saada Cake Bara', '', 0.00, 'Available'),
(60, 2, 'Saada Cake Chota', '', 0.00, 'Available'),
(61, 2, 'Fresh Careme Pastry', '', 0.00, 'Available'),
(62, 2, 'Black Faras Pastry', '', 0.00, 'Available'),
(63, 2, 'Chacolate Pastry', '', 0.00, 'Available'),
(64, 2, 'Mafan', '', 0.00, 'Available'),
(65, 2, 'Brownie Pastry', '', 0.00, 'Available'),
(66, 2, 'Chocolate Dry Pastry', '', 0.00, 'Available'),
(67, 2, 'Cheese Pastry', '', 0.00, 'Available'),
(68, 2, 'Almond Pastry', '', 0.00, 'Available'),
(69, 2, 'Namkeen Buscuit', '', 0.00, 'Available'),
(70, 2, 'Pista Buscuit', '', 0.00, 'Available'),
(71, 2, 'Chocolate Buscuit', '', 0.00, 'Available'),
(72, 2, 'Dry Buscuit', '', 0.00, 'Available'),
(73, 2, 'Hatai Buscuit', '', 0.00, 'Available'),
(74, 2, 'Coconut Buscuit', '', 0.00, 'Available'),
(75, 2, 'Cake Rus', '', 0.00, 'Available'),
(76, 2, 'Paint Sweet', '', 0.00, 'Available'),
(77, 2, 'Til Sweet', '', 0.00, 'Available'),
(78, 2, 'Koki Sweet', '', 0.00, 'Available'),
(79, 2, 'Bakarkhani Bari', '', 0.00, 'Available'),
(80, 2, 'Bakarkhani Choti', '', 0.00, 'Available'),
(81, 2, 'Butter Puff', '', 0.00, 'Available'),
(82, 2, 'Cheese Tai', '', 0.00, 'Available'),
(83, 2, 'Zeera Tai', '', 0.00, 'Available'),
(84, 2, 'Cheeni Stick', '', 0.00, 'Available'),
(85, 2, 'Careem Roll', '', 0.00, 'Available'),
(86, 2, 'Careem Puff', '', 0.00, 'Available'),
(87, 2, 'Chicken Patties', '', 0.00, 'Available'),
(88, 2, 'Alu Patties', '', 0.00, 'Available'),
(89, 2, 'Icing Stick', '', 0.00, 'Available'),
(90, 2, 'Paint Butter Puff', '', 0.00, 'Available'),
(91, 2, 'Jam Stick', '', 0.00, 'Available'),
(92, 2, 'Namkeen Phal Tai', '', 0.00, 'Available'),
(93, 2, 'Dalda Buscuit', '', 0.00, 'Available'),
(94, 2, 'Dalda Cross', '', 0.00, 'Available'),
(95, 3, 'Jaman Gol', '', 0.00, 'Available'),
(96, 3, 'Jaman Lambay', '', 0.00, 'Available'),
(97, 3, 'Jaman Katlas', '', 0.00, 'Available'),
(98, 3, 'Jaman Malai', '', 0.00, 'Available'),
(99, 3, 'Jaman Chotay', '', 0.00, 'Available'),
(100, 3, 'Rasgullay Safaid', '', 0.00, 'Available'),
(101, 3, 'Rasgullay Peelay', '', 0.00, 'Available'),
(102, 3, 'Rasgullay Chapti', '', 0.00, 'Available'),
(103, 3, 'Falsa', '', 0.00, 'Available'),
(104, 3, 'Barfi Saada', '', 0.00, 'Available'),
(105, 3, 'Barfi Badam', '', 0.00, 'Available'),
(106, 3, 'Barfi Akhroot', '', 0.00, 'Available'),
(107, 3, 'Barfi Special', '', 0.00, 'Available'),
(108, 3, 'Barfi Gajar', '', 0.00, 'Available'),
(109, 3, 'Pista Roll', '', 0.00, 'Available'),
(110, 3, 'Kalakand', '', 0.00, 'Available'),
(111, 3, 'Gajar ka Halwa', '', 0.00, 'Available'),
(112, 3, 'Habshi Halwa', '', 0.00, 'Available'),
(113, 3, 'Cheeri Halwa', '', 0.00, 'Available'),
(114, 3, 'Anjeer Halwa', '', 0.00, 'Available'),
(115, 3, 'Manpasand Halwa', '', 0.00, 'Available'),
(116, 3, 'Barfi Paira', '', 0.00, 'Available'),
(117, 3, 'Pista Barfi', '', 0.00, 'Available'),
(118, 3, 'Khajoor Halwa', '', 0.00, 'Available'),
(119, 3, 'Cheeri Barfi', '', 0.00, 'Available'),
(120, 3, 'Ras Malai', '', 0.00, 'Available'),
(121, 3, 'Kheer', '', 0.00, 'Available'),
(122, 3, 'Dil Pateesa', '', 0.00, 'Available'),
(123, 3, 'Malki Pateesa', '', 0.00, 'Available'),
(124, 3, 'Roll Pateesa', '', 0.00, 'Available'),
(125, 3, 'Balushahi', '', 0.00, 'Available'),
(126, 3, 'Imarti', '', 0.00, 'Available'),
(127, 3, 'Ladu Motichoor', '', 0.00, 'Available'),
(128, 3, 'Sohan Halwa Tikki', '', 0.00, 'Available'),
(129, 3, 'Pateesa Gol', '', 0.00, 'Available'),
(130, 3, 'Maysore', '', 0.00, 'Available'),
(131, 3, 'Shakarparay', '', 0.00, 'Available'),
(132, 3, 'Namakparay', '', 0.00, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `pos_user`
--

DROP TABLE IF EXISTS `pos_user`;
CREATE TABLE IF NOT EXISTS `pos_user` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(191) NOT NULL,
  `user_password` varchar(50) NOT NULL,
  `user_type` enum('Admin','User') NOT NULL,
  `user_status` enum('Active','Inactive') NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pos_user`
--

INSERT INTO `pos_user` (`user_id`, `user_name`, `user_email`, `user_password`, `user_type`, `user_status`) VALUES
(1, 'Admin', 'info@softcomputech.com', 'admin2', 'Admin', 'Active'),
(2, 'natiq', 'natiq@softcomputech.com', 'natiq', 'Admin', 'Active'),
(3, 'irfan', 'irfan@softcomputech.com', 'irfan', 'User', 'Active');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
