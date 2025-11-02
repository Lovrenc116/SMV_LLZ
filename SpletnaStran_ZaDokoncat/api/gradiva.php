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
        $stmt = $pdo->prepare('SELECT 1 FROM ucitelji_predmeti WHERE ucitelj_id = ? AND predmet_id = ?');
        $stmt->execute([$_SESSION['user_id'], $predmetId]);
        if (!$stmt->fetch()) {
            json_response(['success' => false, 'message' => 'Ni dostopa do predmeta.'], 403);
        }
    }
    
    // Pridobi gradiva za predmet
    $sql = "
        SELECT g.id, COALESCE(g.naziv_gradiva, g.ime) as naziv_gradiva, g.datoteka, g.datum_nalaganja, p.naziv_predmeta as predmet
        FROM gradiva g
        INNER JOIN predmeti p ON p.id = g.predmet_id
        WHERE g.predmet_id = ?
        ORDER BY g.datum_nalaganja DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$predmetId]);
    $gradiva = $stmt->fetchAll();
    
    json_response(['success' => true, 'gradiva' => $gradiva]);
    
} catch (Exception $e) {
    error_log('GRADIVA_ERROR: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Napaka pri pridobivanju gradiv.'], 500);
}










