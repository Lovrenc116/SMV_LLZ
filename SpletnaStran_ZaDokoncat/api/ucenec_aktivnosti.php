<?php
require_once __DIR__ . '/db.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['vloga'] !== 'ucenec') {
    json_response(['success' => false, 'message' => 'Ni dovoljenja.'], 401);
}

try {
    $pdo = get_db();
    
    // Pridobi zadnje aktivnosti učenca (oddane naloge)
    $sql = "
        SELECT 'naloga' as tip, n.naslov_naloge as naslov, p.naziv_predmeta as predmet, n.datum_oddaje as datum
        FROM naloge n
        INNER JOIN predmeti p ON p.id = n.predmet_id
        WHERE n.ucenec_id = ?
        ORDER BY n.datum_oddaje DESC
        LIMIT 10
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $aktivnosti = $stmt->fetchAll();
    
    json_response($aktivnosti);
    
} catch (Exception $e) {
    error_log('UCENEC_AKTIVNOSTI_ERROR: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Napaka pri pridobivanju aktivnosti.'], 500);
}










