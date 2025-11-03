<?php
require_once __DIR__ . '/db.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['vloga'] !== 'ucenec') {
    json_response(['success' => false, 'message' => 'Ni dovoljenja.'], 401);
}

try {
    $pdo = get_db();
    
    // Pridobi predmete, ki jih obiskuje učenec
    $sql = "
        SELECT p.id, p.naziv_predmeta, p.kratica,
               GROUP_CONCAT(CONCAT(u.ime, ' ', u.priimek) ORDER BY u.ime SEPARATOR ', ') AS ucitelji
        FROM predmeti p
        INNER JOIN ucenci_predmeti up ON up.predmet_id = p.id
        LEFT JOIN ucitelji_predmeti utp ON utp.predmet_id = p.id
        LEFT JOIN ucitelji u ON u.id = utp.ucitelj_id
        WHERE up.ucenec_id = ?
        GROUP BY p.id, p.naziv_predmeta, p.kratica
        ORDER BY p.naziv_predmeta
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $predmeti = $stmt->fetchAll();
    
    json_response(['success' => true, 'predmeti' => $predmeti]);
    
} catch (Exception $e) {
    error_log('UCENEC_PREDMETI_ERROR: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Napaka pri pridobivanju predmetov.'], 500);
}













