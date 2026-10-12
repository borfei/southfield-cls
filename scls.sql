-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               12.3.3-MariaDB - MariaDB Server
-- Server OS:                    Win64
-- HeidiSQL Version:             12.20.0.7320
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for scls
CREATE DATABASE IF NOT EXISTS `scls` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `scls`;

-- Dumping structure for table scls.books
CREATE TABLE IF NOT EXISTS `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(60) NOT NULL,
  `publisher` varchar(50) NOT NULL,
  `year` year(4) NOT NULL COMMENT 'also known as Publication Year',
  `category` set('Unsorted','CIR SHS','FIL SHS') NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table scls.books: ~32 rows (approximately)
DELETE FROM `books`;
INSERT INTO `books` (`id`, `title`, `author`, `publisher`, `year`, `category`, `quantity`) VALUES
	(1, 'How I Went From Being Lost To Getting My Life Into Shape: Be A Triangle', 'Lilly Sin', 'Ballantine Books', '2022', 'Unsorted', 1),
	(2, 'Be Period Positive: Reframe Your Thinking And Reshape The Future Of Menstruation', 'Chella Quint', 'Penguin Random House', '2021', 'Unsorted', 1),
	(3, 'Ham On Rye', 'Charles Bukowski', 'Canongate Books Ltd.', '2015', 'Unsorted', 2),
	(4, 'The Compassionate Mind Approach To Difficult Emotions Using Compassion Focused Therapy', 'Chris Irons', 'Hachette UK Company', '2019', 'Unsorted', 1),
	(5, 'Finding Your Element: How To Discover Your Talents And Passions And Transform Your Life', 'Ken Robinson & Lou Aronica', 'Penguin Books', '2013', 'Unsorted', 1),
	(6, 'How To Find Your Happy Place: Quite Spaces And Journal Pages For Your Busy Minds', 'Alison Davies', 'Octopus Publishing Group', '2022', 'Unsorted', 1),
	(7, 'The Little Book Of Positivity', 'Lucy Lane', 'Summers Dale', '2015', 'Unsorted', 1),
	(8, 'One For Me, One For You: Little Ideas For Caring For Yourself And The World', 'Eva Olsen', 'St. Martins Press', '2021', 'Unsorted', 1),
	(9, 'Unplugged: How To Live Mindfully In A Digital World', 'Orianna Fielding Banks', 'Carlton Books', '2014', 'Unsorted', 1),
	(10, 'Business Ethics: Fourth Edition', 'John R. Boatright', 'Pearson Education', '2005', 'CIR SHS', 1),
	(11, 'Business Information Technology: System, Theory And Practice', 'Geoffrey Elliott', 'Addison-Wesley Publishing Company', '1998', 'CIR SHS', 1),
	(12, 'Modern College Algebra And Trigonometry: Third Edition', 'Vance, Elbridge P.', 'Cacho Hermano', '2008', 'CIR SHS', 1),
	(13, 'New Perspectives On Computer Concepts', 'June Jamrich Parsons and Dan Oja', 'Thomson Learning Asia', '2007', 'CIR SHS', 1),
	(14, 'Effective Communication For Colleges: Tenth Edition', 'Brantley, Clarice', 'South Western Pub. Co.', '2007', 'CIR SHS', 1),
	(15, 'Ethics In Information Technology', 'George W. Reynolds', 'Thomson Course Technology', '2007', 'CIR SHS', 2),
	(16, 'Fundamentals Of Digital Logic With Verilog Design', 'Stephen Brown', 'McGraw Hill Companies, Inc.', '2004', 'CIR SHS', 1),
	(17, 'Introduction To Discrete Mathematics: Sixth Edition', 'Johnsonbaugh, Richard', 'Upper Saddle River', '2008', 'CIR SHS', 4),
	(18, 'Introduction To Probability And Statistics', 'Arnold, Jesse C. and J. Sussan Milton', 'McGraw Hill Companies, Inc.', '2008', 'CIR SHS', 1),
	(19, 'Introduction To Training', 'Penny Hackett', 'CIPD', '2004', 'CIR SHS', 1),
	(20, 'Learning To Use Statistical Tests In Psychology', 'Judith Green and Manuela D\'Oliveira', 'McGraw Hill Companies, Inc.', '1999', 'CIR SHS', 1),
	(21, 'Mosaic 1 Grammar: 4th Edition', 'Patrick K. Werner and Lou Spaventa', 'McGraw Hill Companies, Inc.', '2002', 'CIR SHS', 1),
	(22, 'The Teaching Profession', 'Bilbao, Purita P. and other contributors', 'Lorimar Publishing Inc.', '2006', 'CIR SHS', 1),
	(23, 'Advanced Typewriting', 'Dolores C. Pineda', 'National Book Store', '1983', 'FIL SHS', 1),
	(24, 'Earth Science', 'Mylene D.G Olivia', 'Diwa Learning System Inc.', '2019', 'FIL SHS', 6),
	(25, 'Worktext In General Mathematics', 'Maricar Flores', 'C&E Publishing Inc.', '2016', '', 7),
	(26, 'My Practice Teaching Handbook And Portfolio', 'Borabo, Milagros Lim', 'Lorimar Publishing Inc.', '2010', 'FIL SHS', 1),
	(27, 'Thinking In The 21st Century Culture', 'Elizabeth T. Urgel', 'Diwa Learning System Inc.', '2017', 'FIL SHS', 7),
	(28, 'Disciplines And Ideas In The Social Sciences', 'Maria Carinnes P. Alejandria Gonzales', 'Diwa Learning System Inc.', '2016', 'FIL SHS', 8),
	(29, 'Disaster Readiness and Risk Reduction', 'Mylene D.G Olivia', 'Diwa Learning System Inc.', '2019', 'FIL SHS', 5),
	(30, 'Introduction to Philosophy of the Human Person', 'Roberto D. Abella', 'C& E Publishing, Inc.', '2016', 'FIL SHS', 1),
	(31, 'Personal Development', 'Myreen P. Cleofe', 'Diwa Learning System Inc.', '2019', 'FIL SHS', 8),
	(32, 'Practical Research 2', 'Amadeo Pangilinan Cristobal Jr.', 'C& E Publishing, Inc.', '2017', 'FIL SHS', 4);

-- Dumping structure for table scls.students
CREATE TABLE IF NOT EXISTS `students` (
  `reference` varchar(12) NOT NULL COMMENT 'Learner Reference Number',
  `user` varchar(60) DEFAULT NULL COMMENT 'Associated User',
  `given_name` varchar(60) NOT NULL COMMENT 'First Name',
  `initial_name` varchar(30) DEFAULT NULL COMMENT 'Middle Initial Name',
  `family_name` varchar(60) NOT NULL COMMENT 'Last Name',
  `grade_level` set('12','11','10','9','8','7','6','5','4','3','2','1') NOT NULL COMMENT 'Grade Level',
  `grade_section` varchar(15) NOT NULL DEFAULT 'N/A' COMMENT 'Section',
  PRIMARY KEY (`reference`),
  UNIQUE KEY `user` (`user`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table scls.students: ~1 rows (approximately)
DELETE FROM `students`;
INSERT INTO `students` (`reference`, `user`, `given_name`, `initial_name`, `family_name`, `grade_level`, `grade_section`) VALUES
	('405263150024', NULL, 'Feive Gian', 'Jason', 'Borcillo', '12', 'IT-4');

-- Dumping structure for table scls.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book` int(11) NOT NULL COMMENT 'Book ID',
  `user` varchar(60) NOT NULL,
  `borrow_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `status` set('Pending','Accepted','Declined','Borrowed','Returned') NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`id`),
  KEY `book` (`book`),
  KEY `member` (`user`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user`) REFERENCES `users` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`book`) REFERENCES `books` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='List of transactions made by students';

-- Dumping data for table scls.transactions: ~0 rows (approximately)
DELETE FROM `transactions`;

-- Dumping structure for table scls.users
CREATE TABLE IF NOT EXISTS `users` (
  `name` varchar(60) NOT NULL COMMENT 'User name',
  `password_hash` varchar(255) NOT NULL COMMENT 'Password hash',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'User creation date',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Last user activity',
  `role` set('admin','student') NOT NULL DEFAULT 'student' COMMENT 'User role',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table scls.users: ~1 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`name`, `password_hash`, `created_at`, `updated_at`, `role`) VALUES
	('pss_admin', '$2y$10$Kl6XILP90nKSUc0yxWi/eeaWAQEd8d5uPw.x8BqSKJDg34j/RAI9O', '2025-03-19 05:19:49', '2025-03-19 05:19:49', 'admin');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
