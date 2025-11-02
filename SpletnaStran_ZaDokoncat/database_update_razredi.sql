-- SQL skripta za dodajanje polja razred v tabelo učiteljev
-- Zaženite to skripto v phpMyAdmin ali MySQL konzoli

USE `spletna_ucilnica`;

-- Dodaj polje razred v tabelo učiteljev
ALTER TABLE `ucitelji` 
ADD COLUMN IF NOT EXISTS `razred` VARCHAR(20) NULL AFTER `geslo`;

-- Če IF NOT EXISTS ni podprt (starejše različice MySQL), uporabite:
-- ALTER TABLE `ucitelji` ADD COLUMN `razred` VARCHAR(20) NULL;

