<?php
require_once __DIR__ . '/db.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['vloga'] !== 'ucitelj') {
    json_response(['success' => false, 'message' => 'Ni dovoljenja.'], 401);
}

try {
    $pdo = get_db();
    $uciteljId = $_SESSION['user_id'];
    
    // Število predmetov, ki jih poučuje učitelj
    $stmt = $pdo->prepare('SELECT COUNT(DISTINCT predmet_id) as stevilo FROM ucitelji_predmeti WHERE ucitelj_id = ?');
    $stmt->execute([$uciteljId]);
    $predmetiCount = $stmt->fetch()['stevilo'] ?? 0;
    
    // Število učencev v predmetih, ki jih poučuje učitelj
    $sql = "
        SELECT COUNT(DISTINCT up.ucenec_id) as stevilo
        FROM ucitelji_predmeti utp
        INNER JOIN ucenci_predmeti up ON up.predmet_id = utp.predmet_id
        WHERE utp.ucitelj_id = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$uciteljId]);
    $ucenciCount = $stmt->fetch()['stevilo'] ?? 0;
    
    // Preveri strukturo tabele naloge
    try {
        $testStmt = $pdo->query("SHOW COLUMNS FROM naloge LIKE 'naslov_naloge'");
        $hasNaslovNaloge = $testStmt->rowCount() > 0;
    } catch (Exception $e) {
        $hasNaslovNaloge = false;
    }
    $naslovColumn = $hasNaslovNaloge ? 'naslov_naloge' : 'naslov';
    
    // Število zadnjih nalog (v zadnjih 7 dneh) za predmete, ki jih poučuje učitelj
    $sql = "
        SELECT COUNT(*) as stevilo
        FROM naloge n
        INNER JOIN ucitelji_predmeti utp ON utp.predmet_id = n.predmet_id
        WHERE utp.ucitelj_id = ?
        AND n.datum_oddaje >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$uciteljId]);
    $zadnjeNalogeCount = $stmt->fetch()['stevilo'] ?? 0;
    
    json_response([
        'success' => true,
        'predmeti' => $predmetiCount,
        'ucenci' => $ucenciCount,
        'zadnje_naloge' => $zadnjeNalogeCount . ($zadnjeNalogeCount == 1 ? ' nova' : ($zadnjeNalogeCount == 0 ? ' novih' : ' novih'))
    ]);
    
} catch (Exception $e) {
    error_log('UCITELJ_PREGLED_ERROR: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Napaka pri pridobivanju podatkov.'], 500);
}

