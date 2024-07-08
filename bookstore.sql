-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2024 at 05:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

CREATE TABLE `area` (
  `Area_ID` int(11) NOT NULL,
  `Area_Name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `area`
--

INSERT INTO `area` (`Area_ID`, `Area_Name`) VALUES
(21, 'ภาคเหนือ'),
(22, 'ภาคตะวันตก'),
(23, 'ภาคกลาง'),
(24, 'ภาคตะวันออกเฉียงเหนือ'),
(25, 'ภาคตะวันออก'),
(26, 'ภาคใต้');

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

CREATE TABLE `author` (
  `Auth_ID` int(11) NOT NULL,
  `Auth_Name` varchar(255) DEFAULT NULL,
  `Gend_ID` int(11) DEFAULT NULL,
  `Prov_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `author`
--

INSERT INTO `author` (`Auth_ID`, `Auth_Name`, `Gend_ID`, `Prov_ID`) VALUES
(1410001, 'คิมรันโด', 1, NULL),
(1410003, 'โยชิโนริ โนงุจิ', 1, NULL),
(1410006, 'จอร์จ ออร์เวลล์', 1, NULL),
(1410007, 'Keith Payne', 1, NULL),
(1410008, 'ยาซุดะ ทาดาชิ', 1, NULL),
(1410009, 'เสรี พงศ์พิศ', 1, NULL),
(1410010, 'สุทัศน์ เยี่ยมวัฒนา', 1, NULL),
(1420004, 'เมริษา ยอดมณฑป', 2, NULL),
(1420005, 'กีรติญา สอนเนย', 2, NULL),
(1420011, 'เมริษา ยอดมณฑป', 2, NULL),
(1430002, 'ปู๋ฮุ่ยเซี่ยฉี่', 3, NULL),
(1430005, 'บุชิกะ เอ็ตสึโกะ (Etsuko Bushika)', 3, NULL),
(1430006, 'กฤตานนท์', 3, NULL),
(1430007, 'Silvia Moreno-Garcia', 3, NULL),
(1430008, 'N.K. Jemisin', 3, NULL),
(1430009, 'กองบรรณาธิการ', 3, NULL),
(1430010, 'Xiong Liang', 3, NULL),
(1430011, 'แบรนดอน แซนเดอร์สัน', 3, NULL),
(1430012, 'พรหมพร พิชชานนท์', 3, NULL),
(1430013, 'กรมพระยาดำรงราชานุภาพ', 3, NULL),
(1430014, 'Let\'s improve', 3, NULL),
(1430015, 'นิพัทธ์ ไพบูลย์พรพงศ์', 3, NULL),
(1430016, 'มูลนิธิแม่ฟ้าหลวง', 3, NULL),
(1430017, 'ณภัค เสรีรักษ์', 3, NULL),
(1430018, 'คลาวดิโอ โซปรันเซ็ตติ', 3, NULL),
(1430019, 'ปรานี วงษ์เทศ', 3, NULL),
(1430020, 'ปริพนธ์ นำพบสันติ', 3, NULL),
(1430021, 'ชาครีย์นรทิพย์ เสวิกุล', 3, NULL),
(1430022, 'พีระสิทธิ์ ภู่สาระ (ครูแมม)', 3, NULL),
(1430023, 'จารุวรรณ กะวิเศษ', 3, NULL),
(1430024, 'ขวัญชาย ดำรงค์ขวัญ', 3, NULL),
(1430026, 'ปู๋ฮุ่ยเซี่ยฉี่', 3, NULL),
(1430027, 'บุชิกะ เอ็ตสึโกะ (Etsuko Bushika)', 3, NULL),
(1430028, 'ปกรณ์', 1, 2316),
(14300025, 'ยูดาอึน', 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `ISBN` varchar(13) NOT NULL,
  `Book_Name` varchar(255) DEFAULT NULL,
  `Book_Price` float NOT NULL,
  `Add_Date` date NOT NULL,
  `Book_Remain` int(11) NOT NULL,
  `Publ_ID` int(11) DEFAULT NULL,
  `Auth_ID` int(11) DEFAULT NULL,
  `Type_ID` int(11) DEFAULT NULL,
  `Cate_ID` int(11) DEFAULT NULL,
  `SCat_ID` int(11) DEFAULT NULL,
  `View_Count` int(11) NOT NULL,
  `Book_Image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`ISBN`, `Book_Name`, `Book_Price`, `Add_Date`, `Book_Remain`, `Publ_ID`, `Auth_ID`, `Type_ID`, `Cate_ID`, `SCat_ID`, `View_Count`, `Book_Image`) VALUES
('1234567894564', 'อกเกือบหักแอบรักคุณสามี', 499, '2024-01-30', 3, 3236901, 1430028, 1, 1501, 160110, 0, '1000208067_front_XXL.jpg'),
('4567894523684', 'Jujutsu Kaisen ', 115, '2024-01-26', 6, 3236901, 1410001, 1, 1501, 160110, 0, '1000219403_front_XXL.jpg'),
('9000108511', 'ชีวิตนักลงทุนแบบนี้สิโคตรดี Richer, Wiser, Happier', 376.2, '2024-01-01', 2, 3236918, 1420004, 2, 1504, 160420, 0, '1000266013_front_XXL.jpg'),
('9000110043', 'อาถรรพ์ไพร', 539.1, '2023-11-07', 9, 3236919, 1430004, 2, 1501, 160120, 0, '1000195003_front_XXL.jpg'),
('9780679749868', 'เมื่อบันไดหัก : มองสังคมเหลื่อมล้ำผ่านแว่นจิตวิทยา', 293.25, '2024-01-03', 7, 3236914, 1410007, 1, 1503, 160322, 0, '716Z1hD8s+L._SL1197_.jpg'),
('9786160044146', 'มามุดโลก', 123.25, '2023-12-24', 7, 3236919, 1410010, 2, 1506, 160620, 0, '6000053989_front_XXXL.jpg'),
('9786160049769', 'บันทึกสุดท้ายก่อนเธอจะหายไป', 255, '2024-01-31', 12, 3236904, 1430005, 1, 1501, 160110, 0, '1000268465_front_XXL.jpg'),
('9786160458653', 'แฮร์รี่ พอตเตอร์ พลิกปูมโลกเวทมนตร์', 1377.5, '2024-01-09', 12, 3236916, 1410005, 1, 1501, 160130, 0, '1000260793_front_XXL.jpg'),
('9786160806430', 'การจัดการสีเพื่องานกราฟิก (Color Management System)', 220, '2024-01-27', 13, 3236901, 1430015, 2, 1505, 160521, 0, '6000062118_front_XXXL.jpg'),
('9786161840006', 'นิทานอีสปและนิทานคลาสสิก เรื่องแรกของโลก (เสริมทักษะทางภาษา ไทย-อังกฤษ)', 169.15, '2024-01-26', 7, 3236908, 1430009, 1, 1502, 160211, 0, '1000238040_front_XXL.jpg'),
('9786161858032', 'เป็นตัวร้ายก็ต้องตายเท่านั้น เล่ม 4', 391, '2023-08-01', 7, 3236915, 1430001, 2, 1501, 160110, 0, '6000076204_front_XXL.jpg'),
('9786161860769', 'เกิดใหม่ชาตินี้ ฉันจะเป็นเจ้าตระกูล เล่ม 7 จบ', 446, '2023-11-01', 24, 3236915, 1430028, 1, 1501, 160420, 0, '1000267282_front_XXL.jpg'),
('9786161861971', 'ธี่หยด...สิ้นเสียงครวญคลั่ง', 310.25, '2024-01-01', 2, 3236905, 1430006, 1, 1501, 160120, 0, '1000266773_front_XXL.jpg'),
('9786162780752', 'ธรรมชาติสถาปนา การอนุรักษ์สิ่งแวดล้อมในสมัยของทุน', 200, '2023-12-01', 29, 3236921, 1430017, 1, 1506, 160620, 0, 'pid-188577.jpg\r\n'),
('9786162852503', 'จอมยุทธ์น้อยเสี่ยวมู่เค่อ ผจญภัยสู่ดินแดนดอกท้อ', 254.15, '2023-04-05', 10, 3236909, 1430010, 1, 1502, 160212, 0, '1000248961_front_XXL.jpg'),
('9786162873829', 'อย่าเป็นคนเก่งที่คุยไม่เป็น', 187, '2023-07-10', 3, 3236916, 1410008, 1, 1504, 160420, 0, '1000232888_front_XXXL.jpg'),
('9786162985621', 'มนุษย์ 6 ตุลา', 395, '2023-01-26', 14, 3236928, 1430024, 1, 1510, 161010, 0, '1000255449_front_XXXL.jpg'),
('9786163452122', 'ธุรกิจโรงแรม', 164, '2023-12-08', 17, 3236927, 1430023, 2, 1509, 160920, 0, '3407229100_cover_1.jpg'),
('9786163883278', 'เหตุการณ์พลิกโลกศตวรรษที่ 20 เล่ม1 : 1901-1920', 221, '2023-11-11', 12, 3236911, 1430012, 2, 1502, 160222, 0, '1000217109_front_XXL.jpg'),
('9786163887139', 'สีสันแห่งตุรกี : เทศกาล อาหาร และวัฒนธรรมแห่งดินแดนสองทวีป', 300, '2024-01-31', 20, 3236925, 1430021, 1, 1508, 160820, 0, '1000266334_front_XXXL.jpg'),
('9786164283756', 'การเขียนภาพแฟชั่นและการออกแบบเสื้อผ้า (ฉบับสุดคุ้ม)', 266, '2023-05-29', 4, 3236918, 1420005, 1, 1505, 160522, 0, 'images.jpg'),
('9786164343283', '1984 มหานครแห่งความคับแค้น พ.7', 246.5, '2024-01-25', 11, 3236913, 1410006, 1, 1503, 160321, 0, '1000259687_front_XXXL.jpg'),
('9786164343337', 'ปรัชญา 101 (PHILOSOPHY 101)', 336, '2023-12-05', 37, 3236917, 1430028, 1, 1510, 161020, 0, '1000260006_front_XXL.jpg'),
('9786164940130', 'LecTURE สรุปเข้มสังคมประถม (ฉบับปรับปรุง)', 199, '2024-01-01', 50, 3236926, 1430022, 1, 1509, 160910, 0, '1000245241_front_XXXL.jpg'),
('9786165147118', 'นิทานโบราณคดี', 323, '2023-07-12', 2, 3236912, 1430013, 2, 1503, 160310, 0, '1000263928_front_XXL.jpg'),
('9786165721462', 'สังคมและวัฒนธรรมในอุษาคเนย์ Ethnology of Mainland Southeast Asia', 320, '2022-12-28', 0, 3236923, 1430019, 1, 1507, 160810, 0, '1000223424_front_XXXL.jpg'),
('9786167681658', 'I CHOOSE TO ACT GREEN : low carbon lifestyle for everyone', 109.65, '2024-01-01', 9, 3236920, 1430016, 1, 1506, 160610, 0, '1000243626_front_XXXL.jpg'),
('9786168175224', 'อัลคาแทรซผจญภัยบรรณารักษ์จอมโฉด', 212.5, '2024-01-01', 8, 3236910, 1430011, 1, 1502, 160221, 0, '1000255352_front_XXL.jpg'),
('9786168175330', 'Gods of Jade and Shadowหยกเงาและเถ้าควัน', 335.75, '2023-12-25', 5, 3236906, 1430007, 1, 1501, 160130, 0, '1000267546_front_XXL.jpg'),
('9786168266274', 'ประตูจันทรา', 399, '2023-06-12', 1, 3236907, 1430008, 1, 1501, 160140, 0, '1000248058_front_XXXL.jpg'),
('9786169348702', 'ตาสว่าง', 395, '2021-01-01', 6, 3236922, 1430018, 1, 1507, 160710, 0, '1000231010_front_XXXL.jpg'),
('9789740217336', 'Liveable Japan ใส่ใจไว้ในเมือง', 299, '2023-01-15', 12, 3236924, 1430020, 1, 1508, 160810, 0, '6000051587_front_XXXL.jpg'),
('9789749747766', 'ศิลปะวรรณกรรม ตำนานคนเปลี่ยนโลก', 297.5, '2023-12-30', 9, 3236917, 1410009, 1, 1505, 160510, 0, '1000256078_front_XXXL.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `book_buyer`
--

CREATE TABLE `book_buyer` (
  `BBuy_ID` int(11) NOT NULL,
  `BBuy_Name` varchar(255) DEFAULT NULL,
  `BBuy_MName` varchar(255) DEFAULT NULL,
  `BBuy_LName` varchar(255) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `BBuy_Point` int(11) DEFAULT NULL,
  `BBuy_BirthDate` date DEFAULT NULL,
  `BBuy_Tel` varchar(20) DEFAULT NULL,
  `Gender_ID` int(11) DEFAULT NULL,
  `Prov_ID` int(11) DEFAULT NULL,
  `BBuy_Age` int(11) DEFAULT NULL,
  `BBuy_Address` text NOT NULL,
  `BBuy_Password` varchar(255) NOT NULL,
  `User_Type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_buyer`
--

INSERT INTO `book_buyer` (`BBuy_ID`, `BBuy_Name`, `BBuy_MName`, `BBuy_LName`, `Email`, `BBuy_Point`, `BBuy_BirthDate`, `BBuy_Tel`, `Gender_ID`, `Prov_ID`, `BBuy_Age`, `BBuy_Address`, `BBuy_Password`, `User_Type`) VALUES
(1, 'Thanaporn', '', 'Chaichalermsak', 'beelovefame567@gmail.com', NULL, '2003-02-24', '0643234912', 1, 2415, 20, 'ดาวอังคาร', '$2y$10$zNJGKZIPuv36b5XLNaBvjeHJN1m2iM8acY2Y2WKVvDp7DCox/5uDq', 'admin'),
(13100001, 'แดง', '', 'แผลงฤทธิ์', 'Hot_red@gmail.com', 450, '1988-02-13', '0954032639', 1, 2125, 36, '', '0', ''),
(13100003, 'โกล', 'D', 'โรเจอร์', 'Glod_roger@gmail.com', 600, '2010-01-13', '0854789625', 1, 2309, 14, '', '0', ''),
(13100004, 'เนติภูมิ', '', 'สอนถา', 'natiphorms@gmail.com', 20, '2002-12-22', '0954032036', 1, 2316, 22, '', '0', ''),
(13100005, 'มังกี้', 'D', 'ลูฟี้', 'monkey@gmail.com', 30, '2004-01-25', '0875287964', 1, 2455, 20, '', '0', ''),
(13100009, 'พิชัย', '', 'ดาบหัก', 'phichai@gmail.com', 80, '1957-06-01', '0885237946', 1, 2649, 67, '', '0', ''),
(13200002, 'มายมิ้น', '', 'ช็อกโก', 'choco@gmail.com', 50, '1996-07-08', '0875617428', 2, 2421, 28, '', '0', ''),
(13200006, 'รักษา', '', 'เมืองแปลก', 'strcity@gmail.com', 225, '2002-04-18', '0986525473', 2, 2232, 22, '', '0', ''),
(13200007, 'สิริพร', '', 'มั่งมี', 'siripoon@gmail.com', 150, '1966-09-30', '0887562148', 2, 2562, 58, '', '0', ''),
(13200008, 'กาญจนา', '', 'พิเศษ', 'kanjana@gmail.com', 325, '1990-05-09', '0995487261', 2, 2676, 34, '', '0', ''),
(13300010, 'สวย', '', 'มากมาย', 'berry@gmail.com', 75, '1966-03-06', '0554789526', 3, 2647, 58, '', '0', ''),
(13300011, 'ข้าว', '-', 'ฟ่าง', 'thanapornkhaww@gmail.com', NULL, '2003-01-06', '887899996133', 1, 2645, 21, 'sdfgsgs', '$2y$10$snDVloLp7co0cQhrGaI90ORu8CwUSQqfykSstYn2fwd/a8XWYXrEO', 'regular'),
(13300012, 'นาธาน', '', 'สิทธิเวช', 'nathan@gmail.com', NULL, '2024-02-01', '0877784529', 1, 2645, 0, 'ที่ไหนก็ได้', '$2y$10$w7P4aBRzue855oU.7beZVeRXxNmqdfrxCE16PEUbUW984J1TFaQQ2', ''),
(13300013, 'Yotakaa', '-', 'zhou', 'yoya@gmail.com', NULL, '2002-06-16', '95346784', 2, 2363, 21, '-', '$2y$10$x6cBq3ClSnA6P4L8JOUHAu4mTS3JObREQh3sWN/ZYUGynXL5Qq0re', ''),
(13300014, 'Oraoraoraora', '-', 'สร้อยสุวรรณ์', 'oum00oum@gmail.com', NULL, '2003-01-04', '0991110909', 3, 2329, 21, 'กทมมมมม', '$2y$10$DG480ccGuiS9QUnkt/TXgeNbRYGbGEgg5.KiAqA.O.84VI2sJl/rq', '');

-- --------------------------------------------------------

--
-- Table structure for table `book_type`
--

CREATE TABLE `book_type` (
  `Type_ID` int(11) NOT NULL,
  `Type_Name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_type`
--

INSERT INTO `book_type` (`Type_ID`, `Type_Name`) VALUES
(1, 'ปกอ่อน'),
(2, 'E-Book');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `Cart_ID` int(20) NOT NULL COMMENT 'IDตะกร้า นี้จะเป็นตัวเดียวกับที่เอาข้อมูลไปทำใบเสร็จนะจ๊ะ',
  `Mana_ID` int(11) DEFAULT NULL,
  `BBuy_ID` int(11) NOT NULL,
  `Total_Price` decimal(10,2) DEFAULT 0.00,
  `Buy_Date` datetime DEFAULT NULL,
  `Order_Score` int(11) DEFAULT NULL,
  `Order_Review` text NOT NULL,
  `Order_Status` text NOT NULL COMMENT 'สถานะที่ถูกตรวจสอบโดยผู้จัดการว่าออเดอร์นี้จ่ายเงินเรียบร้อยหรือยัง หรือยกเลิกออเดอรืไปแล้วมีดังนี้\r\nยืนยันคำสั่งซื้อ\r\nรอตรวจสอบ\r\nยืนยันการชำระเงิน\r\nยกเลิก',
  `Confirmation` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`Cart_ID`, `Mana_ID`, `BBuy_ID`, `Total_Price`, `Buy_Date`, `Order_Score`, `Order_Review`, `Order_Status`, `Confirmation`) VALUES
(2026102314, NULL, 13300011, 499.00, '2024-01-30 22:57:43', NULL, '', 'รอตรวจสอบ', 'payment/65b91c775506e_1000219403_front_XXL.jpg'),
(2026102315, NULL, 13300011, 499.00, '2024-01-30 23:00:01', NULL, '', 'รอตรวจสอบ', 'payment/65b91d01d468e_banki.png'),
(2026102316, NULL, 13300011, 376.00, '2024-01-30 23:04:36', NULL, '', 'รอตรวจสอบ', 'payment/65b91e14c520f_banki.png'),
(2026102317, NULL, 13300011, 499.00, '2024-01-30 23:07:47', NULL, '', 'รอตรวจสอบ', 'payment/65b91ed3a7d63_xxx.png'),
(2026102318, NULL, 13300011, 115.00, '2024-01-30 23:09:01', NULL, '', 'รอตรวจสอบ', 'payment/65b91f1dbd561_xxx.png'),
(2026102322, NULL, 13300011, 1005.00, '2024-01-30 23:17:13', NULL, '', 'รอตรวจสอบ', 'payment/65b9210922793_banki.png'),
(2026102323, NULL, 13300011, 230.00, '2024-01-30 23:22:28', NULL, '', 'รอตรวจสอบ', 'payment/65b92244e09fa_xxx.png'),
(2026102324, NULL, 13300011, 376.20, '2024-01-30 23:23:25', NULL, '', 'รอตรวจสอบ', 'payment/65b9227d95e23_6000076204_front_XXL.jpg'),
(2026102325, NULL, 13300011, 721.20, '2024-01-31 21:19:47', NULL, '  ', 'รอตรวจสอบ', 'payment/65ba5703f2d5f_1000219403_front_XXL.jpg'),
(2026102326, NULL, 13300011, 446.00, '2024-01-31 21:20:32', NULL, '  ', 'รอตรวจสอบ', 'payment/65ba5730e531c_6000076204_front_XXL.jpg'),
(2026102327, NULL, 13300011, 1876.50, '2024-01-31 21:23:53', NULL, '  ', 'Disapproved', 'payment/65ba57f93f97a_422902277_3654174261570326_5841919488761254760_n.jpg'),
(2026102328, NULL, 13300011, 654.10, '2024-01-31 23:45:24', NULL, '  ', 'Approved', 'payment/65ba792477278_422902277_3654174261570326_5841919488761254760_n.jpg'),
(2026102329, NULL, 13300013, 499.00, '2024-02-01 17:50:51', NULL, '  ', 'Approved', 'payment/65bb778bc34d3_1000255449_front_XXXL.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `Cate_ID` int(11) NOT NULL,
  `Cate_Name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`Cate_ID`, `Cate_Name`) VALUES
(1501, 'นวนิยาย'),
(1502, 'หนังสือเด็กและเยาวชน'),
(1503, 'วรรณกรรม'),
(1504, 'การเรียนรู้และพัฒนาตัวเอง'),
(1505, 'ศิลปะและการออกแบบ'),
(1506, 'การอนุรักษ์และสิ่งแวดล้อม'),
(1507, 'การเมืองและสังคม'),
(1508, 'การท่องเที่ยวและวัฒนธรรม'),
(1509, 'การเรียนการสอน'),
(1510, 'ความรู้ทั่วไป');

-- --------------------------------------------------------

--
-- Table structure for table `gender`
--

CREATE TABLE `gender` (
  `Gender_ID` int(11) NOT NULL,
  `Gender_Name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gender`
--

INSERT INTO `gender` (`Gender_ID`, `Gender_Name`) VALUES
(1, 'ชาย'),
(2, 'หญิง'),
(3, 'ไม่ระบุ');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `Order_ID` int(12) NOT NULL COMMENT 'แสดงรายละเรียดของตะกร้าว่ามีหนังสืออะไรถูกเพิ่มเข้ามาบ้าง',
  `Cart_ID` int(11) NOT NULL,
  `ISBN` varchar(13) NOT NULL,
  `Book_Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`Order_ID`, `Cart_ID`, `ISBN`, `Book_Quantity`) VALUES
(2026102317, 2026102317, '1234567894564', 1),
(2026102318, 2026102318, '4567894523684', 1),
(2026102322, 2026102322, '1234567894564', 1),
(2026102323, 2026102322, '4567894523684', 1),
(2026102324, 2026102322, '9786161858032', 1),
(2026102325, 2026102323, '4567894523684', 2),
(2026102326, 2026102324, '9000108511', 1),
(2026102327, 2026102325, '4567894523684', 3),
(2026102328, 2026102325, '9000108511', 1),
(2026102329, 2026102326, '9786161860769', 1),
(2026102330, 2026102327, '1234567894564', 1),
(2026102331, 2026102327, '9786160458653', 1),
(2026102332, 2026102328, '4567894523684', 1),
(2026102333, 2026102328, '9000110043', 1),
(2026102334, 2026102329, '1234567894564', 1);

-- --------------------------------------------------------

--
-- Table structure for table `own`
--

CREATE TABLE `own` (
  `BBuy_ID` int(11) NOT NULL,
  `ISBN` varchar(13) NOT NULL,
  `Book_Score` int(11) DEFAULT NULL,
  `Book_Review` text NOT NULL,
  `Read_Count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `own`
--

INSERT INTO `own` (`BBuy_ID`, `ISBN`, `Book_Score`, `Book_Review`, `Read_Count`) VALUES
(13300011, '4567894523684', NULL, '', 0),
(13300011, '4567894523684', NULL, '', 0),
(13300011, '9000110043', NULL, '', 0),
(13300011, '4567894523684', NULL, '', 0),
(13300011, '9000110043', NULL, '', 0),
(13300013, '1234567894564', NULL, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `province`
--

CREATE TABLE `province` (
  `Prov_ID` int(11) NOT NULL,
  `Prov_Name` varchar(255) DEFAULT NULL,
  `Area_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `province`
--

INSERT INTO `province` (`Prov_ID`, `Prov_Name`, `Area_ID`) VALUES
(2102, 'เชียงใหม่', 21),
(2108, 'แม่ฮ่องสอน', 21),
(2110, 'ลำปาง', 21),
(2112, 'เชียงราย', 21),
(2113, 'น่าน', 21),
(2125, 'อุตรดิตถ์', 21),
(2131, 'แพร่', 21),
(2134, 'พะเยา', 21),
(2148, 'ลำพูน', 21),
(2203, 'กาญจนบุรี', 22),
(2204, 'ตาก', 22),
(2232, 'ประจวบคีรีขันธ์', 22),
(2235, 'เพชรบุรี', 22),
(2242, 'ราชบุรี', 22),
(2309, 'เพชรบูรณ์', 23),
(2316, 'พิษณุโลก', 23),
(2320, 'นครสวรรค์', 23),
(2322, 'กำแพงเพชร', 23),
(2329, 'อุทัยธานี', 23),
(2330, 'สุโขทัย', 23),
(2336, 'ลพบุรี', 23),
(2339, 'สุพรรณบุรี', 22),
(2346, 'พิจิตร', 23),
(2356, 'สระบุรี', 23),
(2363, 'พระนครศรีอยุธยา', 23),
(2365, 'ชัยนาท', 23),
(2366, 'นครปฐม', 23),
(2367, 'นครนายก', 23),
(2369, 'กรุงเทพมหานคร', 23),
(2370, 'ปทุมธานี', 23),
(2371, 'สมุทรปราการ', 23),
(2372, 'อ่างทอง', 23),
(2373, 'สมุทรสาคร', 23),
(2374, 'สิงห์บุรี', 23),
(2375, 'นนทบุรี', 23),
(2377, 'สมุทรสงคราม', 23),
(2401, 'นครราชสีมา', 24),
(2405, 'อุบลราชธานี', 24),
(2407, 'ชัยภูมิ', 24),
(2411, 'อุดรธานี', 24),
(2414, 'เลย', 24),
(2415, 'ขอนแก่น', 24),
(2417, 'บุรีรัมย์', 24),
(2419, 'สกลนคร', 24),
(2421, 'ศรีสะเกษ', 24),
(2423, 'ร้อยเอ็ด', 24),
(2424, 'สุรินทร์', 24),
(2428, 'กาฬสินธุ์', 24),
(2438, 'นครพนม', 24),
(2441, 'มหาสารคาม', 24),
(2451, 'มุกดาหาร', 24),
(2452, 'บึงกาฬ', 24),
(2454, 'ยโสธร', 24),
(2455, 'หนองบัวลำภู', 24),
(2460, 'อำนาจเจริญ', 24),
(2461, 'หนองคาย', 24),
(2527, 'สระแก้ว', 25),
(2533, 'จันทบุรี', 25),
(2540, 'ฉะเชิงเทรา', 25),
(2544, 'ปราจีนบุรี', 25),
(2550, 'ชลบุรี', 25),
(2557, 'ระยอง', 25),
(2562, 'ตราด', 25),
(2606, 'สุราษฎร์ธานี', 26),
(2618, 'นครศรีธรรมราช', 26),
(2626, 'สงขลา', 26),
(2637, 'ชุมพร', 26),
(2643, 'ตรัง', 26),
(2645, 'กระบี่', 26),
(2647, 'ยะลา', 26),
(2649, 'นราธิวาส', 26),
(2653, 'พังงา', 26),
(2658, 'พัทลุง', 26),
(2659, 'ระนอง', 26),
(2664, 'สตูล', 26),
(2668, 'ปัตตานี', 26),
(2676, 'ภูเก็ต', 26);

-- --------------------------------------------------------

--
-- Table structure for table `publisher`
--

CREATE TABLE `publisher` (
  `Publ_ID` int(11) NOT NULL,
  `Publ_Name` varchar(255) DEFAULT NULL,
  `Prov_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publisher`
--

INSERT INTO `publisher` (`Publ_ID`, `Publ_Name`, `Prov_ID`) VALUES
(3236901, 'SE-ED', 2369),
(3236902, 'Amarin Kids', 2369),
(3236903, 'แจ่มใส', 2369),
(3236904, 'อะโวคาโด บุ๊กส์', 2369),
(3236905, 'สำนักพิมพ์โลกหนังสือ', 2369),
(3236906, 'เวิร์ด วอนเดอร์', 2369),
(3236907, 'The Fielding Agency, LLC', 2369),
(3236908, 'Amarin Kids', 2369),
(3236909, 'ทองเกษม', 2369),
(3236910, 'เวิร์ด วอนเดอร์', 2369),
(3236911, 'โนเบิ้ลบุ๊คส์', 2369),
(3236912, 'ไทยควอลิตี้บุ๊คส์', 2369),
(3236913, 'แอร์โรว์ คลาสสิกบุ๊ค', 2369),
(3236914, 'บุ๊คสเคป/BOOKSCAPE', 2369),
(3236915, 'Let\'s improve', 2369),
(3236916, 'วีเลิร์น (WeLearn)', 2369),
(3236917, 'มิ่งมิตร', 2369),
(3236918, 'วาดศิลป์, บจก.', 2369),
(3236919, 'สถาพรบุ๊คส์, สนพ.', 2369),
(3236920, 'มูลนิธิแม่ฟ้าหลวง', 2112),
(3236921, 'สำนักพิมพ์ศูนย์มานุษยวิทยาสิรินธร', 2369),
(3236922, 'อ่านอิตาลี', 2369),
(3236923, 'Ituibooks', 2369),
(3236924, 'มติชน', 2369),
(3236925, 'สำนักพิมพ์แสงดาว', 2369),
(3236926, 'GANBATTE', 2369),
(3236927, 'MACEDUCATION', 2369),
(3236928, 'แซลมอน', 2369),
(3236929, 'Be Bright', 2369),
(3236930, 'แพรวสำนักพิมพ์', 2369);

-- --------------------------------------------------------

--
-- Table structure for table `store_manager`
--

CREATE TABLE `store_manager` (
  `Mana_ID` int(11) NOT NULL,
  `Mana_Name` varchar(255) DEFAULT NULL,
  `Mana_Email` varchar(255) NOT NULL,
  `Mana_Password` varchar(255) NOT NULL,
  `User_Type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_manager`
--

INSERT INTO `store_manager` (`Mana_ID`, `Mana_Name`, `Mana_Email`, `Mana_Password`, `User_Type`) VALUES
(121001, 'จักริน ลายศรี', '', '', ''),
(122002, 'พาฝัน จันทรสมบูรณ์', '', '', ''),
(12345678, 'OUM', 'oumoum@gmail.com', '$2y$10$wEid6aLuYA6jKog44MW2v.PvhUPv1txqMM7aDrLVlLxd/x8hKPElu', 'admin'),
(64313069, 'Nack', 'nacknaix@gmail.com', '$2y$10$QiKbBj.V/i4WOHEdTCQiSO8glC9LRF/9ebNLOOC310Jiau3twGJ4a', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `store_owner`
--

CREATE TABLE `store_owner` (
  `Owner_ID` int(11) NOT NULL,
  `Owner_Name` varchar(255) DEFAULT NULL,
  `Owner_Email` varchar(255) NOT NULL,
  `Owner_Password` varchar(255) NOT NULL,
  `User_Type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `store_owner`
--

INSERT INTO `store_owner` (`Owner_ID`, `Owner_Name`, `Owner_Email`, `Owner_Password`, `User_Type`) VALUES
(111002, 'กรภัทร ยิ่งงาม', '', '', ''),
(112001, 'กัญพัชญ์ ยิ่งงาม', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `sub_category`
--

CREATE TABLE `sub_category` (
  `SCat_ID` int(11) NOT NULL,
  `SCat_Name` varchar(255) DEFAULT NULL,
  `Cate_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_category`
--

INSERT INTO `sub_category` (`SCat_ID`, `SCat_Name`, `Cate_ID`) VALUES
(160110, 'นิยายโรแมนติก (Romance)', 1501),
(160120, 'นิยายสืบสวน (Mystery)', 1501),
(160130, 'นิยายแฟนตาซี (Fantasy)', 1501),
(160140, 'นิยายวิทยาศาสตร์ (Science Fiction)', 1501),
(160211, 'นิทานคลาสสิก', 1502),
(160212, 'นิทานแฟนตาซี', 1502),
(160221, 'นวนิยายผจญภัย', 1502),
(160222, 'นวนิยายเหตุการณ์ประวัติศาสตร์เสมือน', 1502),
(160310, 'วรรณกรรมโบราณ (Classic Literature)', 1503),
(160321, 'วรรณกรรมวิทยาศาสตร์ (Scientific Literature)', 1503),
(160322, 'วรรณกรรมสังคม (Social Literature)', 1503),
(160410, 'หนังสือการพัฒนาบุคลิกภาพ (Personal Development Books)', 1504),
(160420, 'หนังสือเกี่ยวกับการเรียนรู้และการพัฒนาทักษะ (Learning and Skill Development)', 1504),
(160510, 'วรรณกรรมศิลปะ (Art Literature)', 1505),
(160521, 'หนังสือออกแบบกราฟิก (Graphic Design)', 1505),
(160522, 'หนังสือออกแบบแฟชั่น (Fashion Design)', 1505),
(160610, 'หนังสือการอนุรักษ์ (Conservation Books)', 1506),
(160620, 'หนังสือเรื่องสิ่งแวดล้อม (Environmental Books)', 1506),
(160710, 'หนังสือเรื่องการเมือง (Political Books)', 1507),
(160720, 'หนังสือเรื่องสังคม (Social Books)', 1507),
(160810, 'หนังสือเรื่องการท่องเที่ยว (Travel Guides)', 1508),
(160820, 'หนังสือเรื่องวัฒนธรรม (Cultural Books)', 1508),
(160910, 'หนังสือเรื่องการเรียนการสอนทั่วไป', 1509),
(160920, 'หนังสือเรื่องการเรียนการสอนในสาขาพิเศษ', 1509),
(161010, 'สารคดีทั่วไป (General Non-Fiction)', 1510),
(161020, 'ปรัชญาและศาสนาทั่วไป (Philosophy and Religion General)', 1510);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`Area_ID`);

--
-- Indexes for table `author`
--
ALTER TABLE `author`
  ADD PRIMARY KEY (`Auth_ID`),
  ADD KEY `IDเพศสภาพ` (`Gend_ID`),
  ADD KEY `IDจังหวัด` (`Prov_ID`);

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`ISBN`),
  ADD KEY `IDสำนักพิมพ์` (`Publ_ID`),
  ADD KEY `IDผู้แต่ง` (`Auth_ID`),
  ADD KEY `IDประเภทหนังสือ` (`Type_ID`),
  ADD KEY `IDหมวดหมู่` (`Cate_ID`),
  ADD KEY `หนังสือ_ibfk_5` (`SCat_ID`);

--
-- Indexes for table `book_buyer`
--
ALTER TABLE `book_buyer`
  ADD PRIMARY KEY (`BBuy_ID`),
  ADD KEY `ผู้ซื้อ_fk_เพศสภาพ` (`Gender_ID`),
  ADD KEY `ผู้ซื้อ_fk_จังหวัด` (`Prov_ID`);

--
-- Indexes for table `book_type`
--
ALTER TABLE `book_type`
  ADD PRIMARY KEY (`Type_ID`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`Cart_ID`),
  ADD KEY `IDผู้จัดการร้าน` (`Mana_ID`),
  ADD KEY `การสั่งซื้อ_fk_ผู้ซื้อ` (`BBuy_ID`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`Cate_ID`);

--
-- Indexes for table `gender`
--
ALTER TABLE `gender`
  ADD PRIMARY KEY (`Gender_ID`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`Order_ID`),
  ADD KEY `IDการสั่งซื้อ` (`Cart_ID`),
  ADD KEY `ISBN` (`ISBN`);

--
-- Indexes for table `own`
--
ALTER TABLE `own`
  ADD KEY `IDผู้ซื้อ` (`BBuy_ID`),
  ADD KEY `ISBN` (`ISBN`);

--
-- Indexes for table `province`
--
ALTER TABLE `province`
  ADD PRIMARY KEY (`Prov_ID`),
  ADD KEY `จังหวัด_ibfk_ภาค` (`Area_ID`);

--
-- Indexes for table `publisher`
--
ALTER TABLE `publisher`
  ADD PRIMARY KEY (`Publ_ID`),
  ADD KEY `IDจังหวัด` (`Prov_ID`);

--
-- Indexes for table `store_manager`
--
ALTER TABLE `store_manager`
  ADD PRIMARY KEY (`Mana_ID`);

--
-- Indexes for table `store_owner`
--
ALTER TABLE `store_owner`
  ADD PRIMARY KEY (`Owner_ID`);

--
-- Indexes for table `sub_category`
--
ALTER TABLE `sub_category`
  ADD PRIMARY KEY (`SCat_ID`),
  ADD KEY `sub_category_fk_category` (`Cate_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book_buyer`
--
ALTER TABLE `book_buyer`
  MODIFY `BBuy_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13300015;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `Cart_ID` int(20) NOT NULL AUTO_INCREMENT COMMENT 'IDตะกร้า นี้จะเป็นตัวเดียวกับที่เอาข้อมูลไปทำใบเสร็จนะจ๊ะ', AUTO_INCREMENT=2026102330;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `Order_ID` int(12) NOT NULL AUTO_INCREMENT COMMENT 'แสดงรายละเรียดของตะกร้าว่ามีหนังสืออะไรถูกเพิ่มเข้ามาบ้าง', AUTO_INCREMENT=2026102335;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `author`
--
ALTER TABLE `author`
  ADD CONSTRAINT `author_fk_gender` FOREIGN KEY (`Gend_ID`) REFERENCES `gender` (`Gender_ID`),
  ADD CONSTRAINT `author_fk_province` FOREIGN KEY (`Prov_ID`) REFERENCES `province` (`Prov_ID`);

--
-- Constraints for table `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `book_fk_sub_category` FOREIGN KEY (`SCat_ID`) REFERENCES `sub_category` (`SCat_ID`);

--
-- Constraints for table `book_buyer`
--
ALTER TABLE `book_buyer`
  ADD CONSTRAINT `book_buyer_fk_gender` FOREIGN KEY (`Gender_ID`) REFERENCES `gender` (`Gender_ID`),
  ADD CONSTRAINT `book_buyer_fk_province` FOREIGN KEY (`Prov_ID`) REFERENCES `province` (`Prov_ID`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_fk_book_buyer` FOREIGN KEY (`BBuy_ID`) REFERENCES `book_buyer` (`BBuy_ID`),
  ADD CONSTRAINT `cart_fk_store_manager` FOREIGN KEY (`Mana_ID`) REFERENCES `store_manager` (`Mana_ID`);

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_fk_book` FOREIGN KEY (`ISBN`) REFERENCES `book` (`ISBN`),
  ADD CONSTRAINT `order_detail_fk_cart` FOREIGN KEY (`Cart_ID`) REFERENCES `cart` (`Cart_ID`);

--
-- Constraints for table `own`
--
ALTER TABLE `own`
  ADD CONSTRAINT `own_fk_book` FOREIGN KEY (`ISBN`) REFERENCES `book` (`ISBN`),
  ADD CONSTRAINT `own_fk_book_buyer` FOREIGN KEY (`BBuy_ID`) REFERENCES `book_buyer` (`BBuy_ID`);

--
-- Constraints for table `province`
--
ALTER TABLE `province`
  ADD CONSTRAINT `province_fk_area` FOREIGN KEY (`Area_ID`) REFERENCES `area` (`Area_ID`);

--
-- Constraints for table `publisher`
--
ALTER TABLE `publisher`
  ADD CONSTRAINT `publisher_fk_province` FOREIGN KEY (`Prov_ID`) REFERENCES `province` (`Prov_ID`);

--
-- Constraints for table `sub_category`
--
ALTER TABLE `sub_category`
  ADD CONSTRAINT `sub_category_fk_category` FOREIGN KEY (`Cate_ID`) REFERENCES `category` (`Cate_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
