<?php
/**
 * Konfiguracija baze podatkov
 * 
 * NAVODILA:
 * 1. Preimenuj to datoteko v db_config.php
 * 2. Spremeni vrednosti za produkcijski strežnik
 * 3. NE committaj db_config.php v Git (dodaj v .gitignore)
 */

// LOKALNA RAZVOJNA OKOLJE (XAMPP)
$dbConfig = [
    'host' => 'localhost',
    'database' => 'llzspletnastranbaza', // ali 'spletna_ucilnica'
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

// PRODUKCIJSKA OKOLJE (Linux strežnik)
// Odkomentiraj in uporabi te nastavitve za produkcijo:
/*
$dbConfig = [
    'host' => 'localhost',
    'database' => 'llzspletnastranbaza',
    'username' => 'llz_user',
    'password' => 'MOCNO_GESLO_TUJ',
    'charset' => 'utf8mb4'
];
*/

// Avtomatska detekcija okolja (ne spreminjaj)
$isProduction = ($_SERVER['SERVER_NAME'] !== 'localhost' && 
                 $_SERVER['SERVER_NAME'] !== '127.0.0.1' &&
                 strpos($_SERVER['SERVER_NAME'], '.local') === false);

// Če je produkcija, uporabi produkcijske nastavitve
if ($isProduction && isset($productionConfig)) {
    $dbConfig = $productionConfig;
}

