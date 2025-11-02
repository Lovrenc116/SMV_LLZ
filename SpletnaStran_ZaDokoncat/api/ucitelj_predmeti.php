<?php
require_once __DIR__ . '/db.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['vloga'] !== 'ucitelj') {
    json_response(['success' => false, 'message' => 'Ni dovoljenja.'], 401);
}

try {
    $pdo = get_db();
    
    // Pridobi predmete, ki jih poučuje učitelj
    $sql = "
        SELECT p.id, p.naziv_predmeta, p.kratica
        FROM predmeti p
        INNER JOIN ucitelji_predmeti utp ON utp.predmet_id = p.id
        WHERE utp.ucitelj_id = ?
        ORDER BY p.naziv_predmeta
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $predmeti = $stmt->fetchAll();
    
    json_response(['success' => true, 'predmeti' => $predmeti]);
    
} catch (Exception $e) {
    error_log('UCITELJ_PREDMETI_ERROR: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Napaka pri pridobivanju predmetov.'], 500);
}










