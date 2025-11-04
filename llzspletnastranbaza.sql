-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gostitelj: 127.0.0.1
-- Čas nastanka: 03. nov 2025 ob 21.44
-- Različica strežnika: 10.4.32-MariaDB
-- Različica PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Zbirka podatkov: `llzspletnastranbaza`
--

-- --------------------------------------------------------

--
-- Struktura tabele `gradiva`
--

CREATE TABLE `gradiva` (
  `id` int(11) NOT NULL,
  `predmet_id` int(11) DEFAULT NULL,
  `ime` varchar(100) NOT NULL,
  `datoteka` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `gradiva`
--

INSERT INTO `gradiva` (`id`, `predmet_id`, `ime`, `datoteka`) VALUES
(1, 1, 'Uvod v algebro', 'uploads/matematika_algebra.pdf'),
(2, 2, 'Esej pisanje', 'uploads/slovenscina_esej.pdf'),
(3, 3, 'Newtonovi zakoni', 'uploads/fizika_newton.pdf'),
(4, 1, 'Mat', 'uploads/gradivo_1_1761029986_Mat.docx'),
(5, 1, 'Mat', 'uploads/gradivo_1_1761031240_Mat.docx'),
(7, 3, 'sonce', 'uploads/gradivo_3_1762110467_sonce.docx'),
(8, 1, 'Polinomi', 'uploads/gradivo_1_1762176169_Polinomi.docx'),
(9, 12, 'enačbe', 'uploads/gradivo_12_1762196680_enabe.docx'),
(10, 5, 'enačbe', 'uploads/gradivo_5_1762197266_enabe.docx');

-- --------------------------------------------------------

--
-- Struktura tabele `naloge`
--

CREATE TABLE `naloge` (
  `id` int(11) NOT NULL,
  `ucenec_id` int(11) DEFAULT NULL,
  `predmet_id` int(11) DEFAULT NULL,
  `naslov` varchar(100) NOT NULL,
  `datoteka` varchar(255) NOT NULL,
  `datum_oddaje` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `naloge`
--

INSERT INTO `naloge` (`id`, `ucenec_id`, `predmet_id`, `naslov`, `datoteka`, `datum_oddaje`) VALUES
(1, 1, 1, 'Algebra', 'uploads/Kovacic Janez - Algebra.pdf', '2025-09-28 10:00:00'),
(2, 2, 2, 'Esej', 'uploads/Novak Maja - Esej.pdf', '2025-09-27 14:30:00'),
(3, 12, 3, 'nekaj', 'Milher iga - nekaj.docx', '2025-11-02 19:43:56'),
(4, 12, 3, 'zakoni', 'Milher iga - zakoni.png', '2025-11-03 15:48:33'),
(5, 12, 3, 'neznana slovenija', 'Milher iga - neznana slovenija.docx', '2025-11-03 15:48:51'),
(6, 14, 5, 'enačbe', 'Jurgl Babrijela - enabe.docx', '2025-11-03 20:16:18'),
(7, 14, 5, 'okolje', 'Jurgl Babrijela - okolje.docx', '2025-11-03 20:25:09');

-- --------------------------------------------------------

--
-- Struktura tabele `predmeti`
--

CREATE TABLE `predmeti` (
  `id` int(11) NOT NULL,
  `naziv_predmeta` varchar(100) NOT NULL,
  `kratica` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `predmeti`
--

INSERT INTO `predmeti` (`id`, `naziv_predmeta`, `kratica`) VALUES
(1, 'Matematika', 'MAT'),
(2, 'Slovenščina', 'SLO'),
(3, 'Fizika', 'FIZ'),
(4, 'Kemija', 'KEM'),
(5, 'Biologija', 'BIO'),
(6, 'Zgodovina', 'ZGO'),
(7, 'Geografija', 'GEO'),
(8, 'Angleščina', 'ANG'),
(9, 'Nemščina', 'NEM'),
(10, 'Šport', 'ŠPO'),
(12, 'matematika', 'kem'),
(13, 'spoznavanje okolja', 'SPO'),
(15, 'računalniški praktikum', 'RPR');

-- --------------------------------------------------------

--
-- Struktura tabele `ucenci`
--

CREATE TABLE `ucenci` (
  `id` int(11) NOT NULL,
  `ime` varchar(50) NOT NULL,
  `priimek` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `geslo` varchar(255) NOT NULL,
  `razred` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `ucenci`
--

INSERT INTO `ucenci` (`id`, `ime`, `priimek`, `email`, `geslo`, `razred`) VALUES
(1, 'Janez', 'Kovačič', 'janez.kovacic@ucilnica.si', '$2y$10$examplehash101', '1A'),
(2, 'Maja', 'Novak', 'maja.novak@ucilnica.si', '$2y$10$examplehash102', '1A'),
(3, 'Luka', 'Horvat', 'luka.horvat@ucilnica.si', '$2y$10$examplehash103', '1B'),
(4, 'Ana', 'Zupan', 'ana.zupan@ucilnica.si', '$2y$10$examplehash104', '1B'),
(5, 'Tilen', 'Kolar', 'tilen.kolar@ucilnica.si', '$2y$10$examplehash105', '2A'),
(6, 'Sara', 'Pavlič', 'sara.pavlic@ucilnica.si', '$2y$10$examplehash106', '2A'),
(7, 'Mark', 'Mlakar', 'mark.mlakar@ucilnica.si', '$2y$10$examplehash107', '2B'),
(9, 'Rok', 'Vidmar', 'rok.vidmar@ucilnica.si', '$2y$10$examplehash109', '3A'),
(10, 'Lea', 'Žagar', 'lea.zagar@ucilnica.si', '$2y$10$examplehash110', '3A'),
(11, 'Luka', 'Šuši', 'luka.susi@gmail.com', 'Luka2007', 'r.4-a'),
(12, 'Žiga', 'Milher', 'zigamilher7@gmail.com', 'Milhi2\'\'7', '2A'),
(13, 'Kokos', 'učitelj', 'kokosucitelje@gmail.com', '123456789', '3B'),
(14, 'Babrijela', 'Jurgl', 'gabrijela.jurgl@gmail.com', 'gabi1977', '4A');

-- --------------------------------------------------------

--
-- Struktura tabele `ucenci_predmeti`
--

CREATE TABLE `ucenci_predmeti` (
  `ucenec_id` int(11) NOT NULL,
  `predmet_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `ucenci_predmeti`
--

INSERT INTO `ucenci_predmeti` (`ucenec_id`, `predmet_id`) VALUES
(1, 1),
(1, 2),
(1, 8),
(2, 2),
(2, 9),
(3, 1),
(3, 3),
(4, 4),
(4, 5),
(5, 6),
(5, 7),
(6, 8),
(6, 10),
(7, 1),
(7, 4),
(9, 3),
(9, 5),
(10, 7),
(10, 9),
(11, 1),
(12, 3),
(14, 1),
(14, 2),
(14, 3),
(14, 4),
(14, 5),
(14, 6),
(14, 7);

-- --------------------------------------------------------

--
-- Struktura tabele `ucitelji`
--

CREATE TABLE `ucitelji` (
  `id` int(11) NOT NULL,
  `ime` varchar(50) NOT NULL,
  `priimek` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `geslo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `ucitelji`
--

INSERT INTO `ucitelji` (`id`, `ime`, `priimek`, `email`, `geslo`) VALUES
(1, 'Ana', 'Novak', 'ana.novak@ucilnica.si', '$2y$10$examplehash1'),
(2, 'Bojan', 'Kovač', 'bojan.kovac@ucilnica.si', '$2y$10$examplehash2'),
(3, 'Cvetka', 'Horvat', 'cvetka.horvat@ucilnica.si', '$2y$10$examplehash3'),
(4, 'David', 'Zupan', 'david.zupan@ucilnica.si', '$2y$10$examplehash4'),
(5, 'Ema', 'Kolar', 'ema.kolar@ucilnica.si', '$2y$10$examplehash5'),
(6, 'Fran', 'Pavlič', 'fran.pavlic@ucilnica.si', '$2y$10$examplehash6'),
(7, 'Greta', 'Mlakar', 'greta.mlakar@ucilnica.si', '$2y$10$examplehash7'),
(8, 'Hinko', 'Kos', 'hinko.kos@ucilnica.si', '$2y$10$examplehash8'),
(9, 'Irena', 'Vidmar', 'irena.vidmar@ucilnica.si', '$2y$10$examplehash9'),
(10, 'Janko', 'Žagar', 'janko.zagar@ucilnica.si', '$2y$10$examplehash10'),
(11, 'Klara', 'Rozman', 'klara.rozman@ucilnica.si', '$2y$10$examplehash11'),
(12, 'Lovro', 'Kranjc', 'lovro.kranjc@ucilnica.si', '$2y$10$examplehash12'),
(13, 'Maja', 'Hočevar', 'maja.hocevar@ucilnica.si', '$2y$10$examplehash13'),
(14, 'Nejc', 'Pirc', 'nejc.pirc@ucilnica.si', '$2y$10$examplehash14'),
(15, 'Olga', 'Grm', 'olga.grm@ucilnica.si', '$2y$10$examplehash15'),
(16, 'Peter', 'Furlan', 'peter.furlan@ucilnica.si', '$2y$10$examplehash16'),
(17, 'Rebeka', 'Turk', 'rebeka.turk@ucilnica.si', '$2y$10$examplehash17'),
(18, 'Simon', 'Dolenc', 'simon.dolenc@ucilnica.si', '$2y$10$examplehash18'),
(19, 'Tina', 'Pavlin', 'tina.pavlin@ucilnica.si', '$2y$10$examplehash19'),
(20, 'Uroš', 'Hribar', 'uros.hribar@ucilnica.si', '$2y$10$examplehash20'),
(21, 'Helena', 'Viher', 'helena.viher@gamil.com', '12345678'),
(22, 'Boštjan', 'Resinovič', 'bostjan@gmail.com', 'bostjan2007'),
(23, 'Gregor', 'Nudel', 'gregor@gmail.com', 'gregor2007'),
(24, 'Lovro', 'Pernovšek', 'lovro.pernovsek@gmail.com', 'Lovro2007'),
(25, 'Nino', 'Kranjec', 'nino@gmail.com', 'nino2007');

-- --------------------------------------------------------

--
-- Struktura tabele `ucitelji_predmeti`
--

CREATE TABLE `ucitelji_predmeti` (
  `ucitelj_id` int(11) NOT NULL,
  `predmet_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `ucitelji_predmeti`
--

INSERT INTO `ucitelji_predmeti` (`ucitelj_id`, `predmet_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 3),
(3, 4),
(3, 5),
(4, 6),
(4, 7),
(5, 8),
(5, 9),
(6, 10),
(7, 1),
(7, 4),
(8, 2),
(8, 6),
(9, 8),
(10, 9),
(11, 5),
(11, 7),
(12, 3),
(12, 4),
(13, 1),
(13, 8),
(14, 2),
(14, 9),
(15, 6),
(15, 10),
(16, 5),
(17, 7),
(18, 3),
(19, 1),
(19, 2),
(20, 8),
(20, 10),
(21, 1),
(22, 1),
(23, 3),
(24, 1),
(24, 2),
(24, 3),
(24, 4),
(25, 2),
(25, 3),
(25, 4),
(25, 5),
(25, 6),
(25, 7),
(25, 8),
(25, 9),
(25, 10),
(25, 12),
(25, 13),
(25, 15);

--
-- Indeksi zavrženih tabel
--

--
-- Indeksi tabele `gradiva`
--
ALTER TABLE `gradiva`
  ADD PRIMARY KEY (`id`),
  ADD KEY `predmet_id` (`predmet_id`);

--
-- Indeksi tabele `naloge`
--
ALTER TABLE `naloge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ucenec_id` (`ucenec_id`),
  ADD KEY `predmet_id` (`predmet_id`);

--
-- Indeksi tabele `predmeti`
--
ALTER TABLE `predmeti`
  ADD PRIMARY KEY (`id`);

--
-- Indeksi tabele `ucenci`
--
ALTER TABLE `ucenci`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksi tabele `ucenci_predmeti`
--
ALTER TABLE `ucenci_predmeti`
  ADD PRIMARY KEY (`ucenec_id`,`predmet_id`),
  ADD KEY `predmet_id` (`predmet_id`);

--
-- Indeksi tabele `ucitelji`
--
ALTER TABLE `ucitelji`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksi tabele `ucitelji_predmeti`
--
ALTER TABLE `ucitelji_predmeti`
  ADD PRIMARY KEY (`ucitelj_id`,`predmet_id`),
  ADD KEY `predmet_id` (`predmet_id`);

--
-- AUTO_INCREMENT zavrženih tabel
--

--
-- AUTO_INCREMENT tabele `gradiva`
--
ALTER TABLE `gradiva`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT tabele `naloge`
--
ALTER TABLE `naloge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT tabele `predmeti`
--
ALTER TABLE `predmeti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT tabele `ucenci`
--
ALTER TABLE `ucenci`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT tabele `ucitelji`
--
ALTER TABLE `ucitelji`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Omejitve tabel za povzetek stanja
--

--
-- Omejitve za tabelo `gradiva`
--
ALTER TABLE `gradiva`
  ADD CONSTRAINT `gradiva_ibfk_1` FOREIGN KEY (`predmet_id`) REFERENCES `predmeti` (`id`) ON DELETE CASCADE;

--
-- Omejitve za tabelo `naloge`
--
ALTER TABLE `naloge`
  ADD CONSTRAINT `naloge_ibfk_1` FOREIGN KEY (`ucenec_id`) REFERENCES `ucenci` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `naloge_ibfk_2` FOREIGN KEY (`predmet_id`) REFERENCES `predmeti` (`id`) ON DELETE CASCADE;

--
-- Omejitve za tabelo `ucenci_predmeti`
--
ALTER TABLE `ucenci_predmeti`
  ADD CONSTRAINT `ucenci_predmeti_ibfk_1` FOREIGN KEY (`ucenec_id`) REFERENCES `ucenci` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ucenci_predmeti_ibfk_2` FOREIGN KEY (`predmet_id`) REFERENCES `predmeti` (`id`) ON DELETE CASCADE;

--
-- Omejitve za tabelo `ucitelji_predmeti`
--
ALTER TABLE `ucitelji_predmeti`
  ADD CONSTRAINT `ucitelji_predmeti_ibfk_1` FOREIGN KEY (`ucitelj_id`) REFERENCES `ucitelji` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ucitelji_predmeti_ibfk_2` FOREIGN KEY (`predmet_id`) REFERENCES `predmeti` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
