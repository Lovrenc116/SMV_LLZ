<?php
require_once __DIR__ . '/db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    json_response(['success' => false, 'message' => 'Ni dovoljenja.'], 401);
}

try {
    $pdo = get_db();
    $predmetId = $_GET['predmet'] ?? null;
    
    if (!$predmetId) {
        json_response(['success' => false, 'message' => 'Manjka ID predmeta.'], 400);
    }
    
    // Preveri, ali ima uporabnik dostop do predmeta
    if ($_SESSION['vloga'] === 'ucenec') {
        $stmt = $pdo->prepare('SELECT 1 FROM ucenci_predmeti WHERE ucenec_id = ? AND predmet_id = ?');
        $stmt->execute([$_SESSION['user_id'], $predmetId]);
        if (!$stmt->fetch()) {
            json_response(['success' => false, 'message' => 'Ni dostopa do predmeta.'], 403);
        }
    } elseif ($_SESSION['vloga'] === 'ucitelj') {
        // Učitelj vidi naloge samo za predmete, ki jih poučuje
        $stmt = $pdo->prepare('SELECT 1 FROM ucitelji_predmeti WHERE ucitelj_id = ? AND predmet_id = ?');
        $stmt->execute([$_SESSION['user_id'], $predmetId]);
        if (!$stmt->fetch()) {
            json_response(['success' => false, 'message' => 'Ni dostopa do predmeta.'], 403);
        }
    } elseif ($_SESSION['vloga'] === 'administrator') {
        // Administrator ima dostop do vseh predmetov
    }
    
    // Preveri strukturo tabele - ali ima naslov ali naslov_naloge, in opis
    try {
        $testStmt = $pdo->query("SHOW COLUMNS FROM naloge LIKE 'naslov_naloge'");
        $hasNaslovNaloge = $testStmt->rowCount() > 0;
    } catch (Exception $e) {
        $hasNaslovNaloge = false;
    }
    
    try {
        $testStmt = $pdo->query("SHOW COLUMNS FROM naloge LIKE 'opis_naloge'");
        $hasOpisNaloge = $testStmt->rowCount() > 0;
    } catch (Exception $e) {
        $hasOpisNaloge = false;
    }
    
    try {
        $testStmt = $pdo->query("SHOW COLUMNS FROM naloge LIKE 'opis'");
        $hasOpis = $testStmt->rowCount() > 0;
    } catch (Exception $e) {
        $hasOpis = false;
    }
    
    $naslovColumn = $hasNaslovNaloge ? 'naslov_naloge' : 'naslov';
    $hasOpisColumn = $hasOpisNaloge || $hasOpis;
    
    // Pridobi naloge za predmet
    if ($hasOpisColumn) {
        $opisColumn = $hasOpisNaloge ? 'opis_naloge' : 'opis';
        $sql = "
            SELECT n.id, n.$naslovColumn as naslov_naloge, n.$opisColumn as opis_naloge, n.datum_oddaje, n.datoteka,
                   p.naziv_predmeta, CONCAT(u.ime, ' ', u.priimek) as ucenec_ime
            FROM naloge n
            INNER JOIN predmeti p ON p.id = n.predmet_id
            LEFT JOIN ucenci u ON u.id = n.ucenec_id
            WHERE n.predmet_id = ?
            ORDER BY n.datum_oddaje DESC
        ";
    } else {
        $sql = "
            SELECT n.id, n.$naslovColumn as naslov_naloge, NULL as opis_naloge, n.datum_oddaje, n.datoteka,
                   p.naziv_predmeta, CONCAT(u.ime, ' ', u.priimek) as ucenec_ime
            FROM naloge n
            INNER JOIN predmeti p ON p.id = n.predmet_id
            LEFT JOIN ucenci u ON u.id = n.ucenec_id
            WHERE n.predmet_id = ?
            ORDER BY n.datum_oddaje DESC
        ";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$predmetId]);
    $naloge = $stmt->fetchAll();
    
    json_response(['success' => true, 'naloge' => $naloge]);
    
} catch (Exception $e) {
    error_log('NALOGE_ERROR: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Napaka pri pridobivanju nalog.'], 500);
}













