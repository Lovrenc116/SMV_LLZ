-- SQL skripta za ustvarjanje baze podatkov za LLZ spletno učilnico
-- Zaženite to skripto v phpMyAdmin ali MySQL konzoli

-- Ustvari bazo podatkov (če ne obstaja)
CREATE DATABASE IF NOT EXISTS `spletna_ucilnica` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `spletna_ucilnica`;

-- Tabela za učence
CREATE TABLE IF NOT EXISTS `ucenci` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ime` VARCHAR(100) NOT NULL,
  `priimek` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `geslo` VARCHAR(255) NOT NULL,
  `razred` VARCHAR(20) NOT NULL,
  `datum_registracije` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela za učitelje
CREATE TABLE IF NOT EXISTS `ucitelji` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ime` VARCHAR(100) NOT NULL,
  `priimek` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `geslo` VARCHAR(255) NOT NULL,
  `datum_registracije` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela za predmete
CREATE TABLE IF NOT EXISTS `predmeti` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `naziv_predmeta` VARCHAR(255) NOT NULL,
  `kratica` VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Povezovalna tabela: Učenci - Predmeti (many-to-many)
CREATE TABLE IF NOT EXISTS `ucenci_predmeti` (
  `ucenec_id` INT NOT NULL,
  `predmet_id` INT NOT NULL,
  PRIMARY KEY (`ucenec_id`, `predmet_id`),
  FOREIGN KEY (`ucenec_id`) REFERENCES `ucenci`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`predmet_id`) REFERENCES `predmeti`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Povezovalna tabela: Učitelji - Predmeti (many-to-many)
CREATE TABLE IF NOT EXISTS `ucitelji_predmeti` (
  `ucitelj_id` INT NOT NULL,
  `predmet_id` INT NOT NULL,
  PRIMARY KEY (`ucitelj_id`, `predmet_id`),
  FOREIGN KEY (`ucitelj_id`) REFERENCES `ucitelji`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`predmet_id`) REFERENCES `predmeti`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela za naloge (oddane naloge učencev)
CREATE TABLE IF NOT EXISTS `naloge` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ucenec_id` INT NOT NULL,
  `predmet_id` INT NOT NULL,
  `naslov_naloge` VARCHAR(255) NOT NULL,
  `naslov` VARCHAR(255) NULL,  -- Alternativno polje za kompatibilnost
  `opis_naloge` TEXT,
  `datoteka` VARCHAR(500) NOT NULL,
  `datum_oddaje` TIMESTAMP NULL,
  `datum_kreiranja` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`ucenec_id`) REFERENCES `ucenci`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`predmet_id`) REFERENCES `predmeti`(`id`) ON DELETE CASCADE,
  INDEX `idx_ucenec_predmet` (`ucenec_id`, `predmet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela za gradiva (gradiva, ki jih naložijo učitelji)
CREATE TABLE IF NOT EXISTS `gradiva` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `predmet_id` INT NOT NULL,
  `naziv_gradiva` VARCHAR(255) NULL,  -- Za kompatibilnost z gradiva.php
  `ime` VARCHAR(255) NULL,  -- Za kompatibilnost z gradivo_upload.php
  `datoteka` VARCHAR(500) NOT NULL,
  `datum_nalaganja` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`predmet_id`) REFERENCES `predmeti`(`id`) ON DELETE CASCADE,
  INDEX `idx_predmet` (`predmet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

