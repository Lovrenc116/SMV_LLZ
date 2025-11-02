-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2025 at 06:57 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spletna_ucilnica`
--

-- --------------------------------------------------------

--
-- Table structure for table `gradiva`
--

CREATE TABLE `gradiva` (
  `id` int(11) NOT NULL,
  `predmet_id` int(11) NOT NULL,
  `naziv_gradiva` varchar(255) DEFAULT NULL,
  `ime` varchar(255) DEFAULT NULL,
  `datoteka` varchar(500) NOT NULL,
  `datum_nalaganja` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gradiva`
--

INSERT INTO `gradiva` (`id`, `predmet_id`, `naziv_gradiva`, `ime`, `datoteka`, `datum_nalaganja`) VALUES
(1, 2, '12', '12', 'uploads/gradivo_2_1762104661_12.docx', '2025-11-02 17:31:01'),
(2, 4, '123123', '123123', 'uploads/gradivo_4_1762105309_123123.docx', '2025-11-02 17:41:49');

-- --------------------------------------------------------

--
-- Table structure for table `naloge`
--

CREATE TABLE `naloge` (
  `id` int(11) NOT NULL,
  `ucenec_id` int(11) NOT NULL,
  `predmet_id` int(11) NOT NULL,
  `naslov_naloge` varchar(255) NOT NULL,
  `naslov` varchar(255) DEFAULT NULL,
  `opis_naloge` text DEFAULT NULL,
  `datoteka` varchar(500) NOT NULL,
  `datum_oddaje` timestamp NULL DEFAULT NULL,
  `datum_kreiranja` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `predmeti`
--

CREATE TABLE `predmeti` (
  `id` int(11) NOT NULL,
  `naziv_predmeta` varchar(255) NOT NULL,
  `kratica` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `predmeti`
--

INSERT INTO `predmeti` (`id`, `naziv_predmeta`, `kratica`) VALUES
(2, 'Naravoslovje', 'NAR'),
(3, 'Matematika', 'MAT'),
(4, 'Športna', 'ŠPO');

-- --------------------------------------------------------

--
-- Table structure for table `ucenci`
--

CREATE TABLE `ucenci` (
  `id` int(11) NOT NULL,
  `ime` varchar(100) NOT NULL,
  `priimek` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `geslo` varchar(255) NOT NULL,
  `razred` varchar(20) NOT NULL,
  `datum_registracije` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ucenci`
--

INSERT INTO `ucenci` (`id`, `ime`, `priimek`, `email`, `geslo`, `razred`, `datum_registracije`) VALUES
(1, 'luka', 'susteric', 'luka.susteric@gmail.com', '123123', '1A', '2025-11-02 17:13:09'),
(2, 'janez', 'mike', 'neki1@gmail.com', '1213123', '1A', '2025-11-02 17:18:28'),
(3, 'jan', '1', 'jan@gmail.com', '123123', 'R4A', '2025-11-02 17:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `ucenci_predmeti`
--

CREATE TABLE `ucenci_predmeti` (
  `ucenec_id` int(11) NOT NULL,
  `predmet_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ucenci_predmeti`
--

INSERT INTO `ucenci_predmeti` (`ucenec_id`, `predmet_id`) VALUES
(2, 4),
(3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `ucitelji`
--

CREATE TABLE `ucitelji` (
  `id` int(11) NOT NULL,
  `ime` varchar(100) NOT NULL,
  `priimek` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `geslo` varchar(255) NOT NULL,
  `razred` varchar(20) DEFAULT NULL,
  `datum_registracije` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ucitelji`
--

INSERT INTO `ucitelji` (`id`, `ime`, `priimek`, `email`, `geslo`, `razred`, `datum_registracije`) VALUES
(1, 'Herold', 'Mike', 'neki@gmail.com', '123123', NULL, '2025-11-02 17:18:03'),
(3, 'Nel', 'AS', 'nel@gmail.com', '123123', 'R4A', '2025-11-02 17:39:48');

-- --------------------------------------------------------

--
-- Table structure for table `ucitelji_predmeti`
--

CREATE TABLE `ucitelji_predmeti` (
  `ucitelj_id` int(11) NOT NULL,
  `predmet_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ucitelji_predmeti`
--

INSERT INTO `ucitelji_predmeti` (`ucitelj_id`, `predmet_id`) VALUES
(3, 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gradiva`
--
ALTER TABLE `gradiva`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_predmet` (`predmet_id`);

--
-- Indexes for table `naloge`
--
ALTER TABLE `naloge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `predmet_id` (`predmet_id`),
  ADD KEY `idx_ucenec_predmet` (`ucenec_id`,`predmet_id`);

--
-- Indexes for table `predmeti`
--
ALTER TABLE `predmeti`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ucenci`
--
ALTER TABLE `ucenci`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `ucenci_predmeti`
--
ALTER TABLE `ucenci_predmeti`
  ADD PRIMARY KEY (`ucenec_id`,`predmet_id`),
  ADD KEY `predmet_id` (`predmet_id`);

--
-- Indexes for table `ucitelji`
--
ALTER TABLE `ucitelji`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `ucitelji_predmeti`
--
ALTER TABLE `ucitelji_predmeti`
  ADD PRIMARY KEY (`ucitelj_id`,`predmet_id`),
  ADD KEY `predmet_id` (`predmet_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gradiva`
--
ALTER TABLE `gradiva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `naloge`
--
ALTER TABLE `naloge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `predmeti`
--
ALTER TABLE `predmeti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ucenci`
--
ALTER TABLE `ucenci`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ucitelji`
--
ALTER TABLE `ucitelji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gradiva`
--
ALTER TABLE `gradiva`
  ADD CONSTRAINT `gradiva_ibfk_1` FOREIGN KEY (`predmet_id`) REFERENCES `predmeti` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `naloge`
--
ALTER TABLE `naloge`
  ADD CONSTRAINT `naloge_ibfk_1` FOREIGN KEY (`ucenec_id`) REFERENCES `ucenci` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `naloge_ibfk_2` FOREIGN KEY (`predmet_id`) REFERENCES `predmeti` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ucenci_predmeti`
--
ALTER TABLE `ucenci_predmeti`
  ADD CONSTRAINT `ucenci_predmeti_ibfk_1` FOREIGN KEY (`ucenec_id`) REFERENCES `ucenci` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ucenci_predmeti_ibfk_2` FOREIGN KEY (`predmet_id`) REFERENCES `predmeti` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ucitelji_predmeti`
--
ALTER TABLE `ucitelji_predmeti`
  ADD CONSTRAINT `ucitelji_predmeti_ibfk_1` FOREIGN KEY (`ucitelj_id`) REFERENCES `ucitelji` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ucitelji_predmeti_ibfk_2` FOREIGN KEY (`predmet_id`) REFERENCES `predmeti` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
