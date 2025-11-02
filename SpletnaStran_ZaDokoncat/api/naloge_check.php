<?php
require_once __DIR__ . '/db.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['vloga'] !== 'ucenec') {
    json_response(['success' => false, 'message' => 'Ni dovoljenja.'], 401);
}

try {
    $pdo = get_db();
    $predmetId = $_GET['predmet'] ?? null;
    $naslov = $_GET['naslov'] ?? null;
    
    if (!$predmetId || !$naslov) {
        json_response(['success' => false, 'message' => 'Manjkajoči parametri.'], 400);
    }
    
    // Preveri, ali učenec že ima nalogo s tem naslovom za ta predmet
    $stmt = $pdo->prepare('SELECT id FROM naloge WHERE ucenec_id = ? AND predmet_id = ? AND naslov_naloge = ?');
    $stmt->execute([$_SESSION['user_id'], $predmetId, $naslov]);
    $existing = $stmt->fetch();
    
    json_response(['success' => true, 'exists' => $existing !== false]);
    
} catch (Exception $e) {
    error_log('NALOGE_CHECK_ERROR: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Napaka pri preverjanju naloge.'], 500);
}










