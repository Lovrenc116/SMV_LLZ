<?php
require_once __DIR__ . '/db.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['vloga'] !== 'ucenec') {
    json_response(['success' => false, 'message' => 'Ni dovoljenja.'], 401);
}

try {
    $pdo = get_db();
    $ucenecId = $_SESSION['user_id'];
    
    // Najprej pridobi razred učenca
    $razredStmt = $pdo->prepare('SELECT razred FROM ucenci WHERE id = ?');
    $razredStmt->execute([$ucenecId]);
    $ucenecData = $razredStmt->fetch();
    $ucenecRazred = $ucenecData['razred'] ?? null;
    
    // Pridobi predmete, ki jih obiskuje učenec
    $sql = "
        SELECT DISTINCT p.id, p.naziv_predmeta, p.kratica
        FROM predmeti p
        INNER JOIN ucenci_predmeti up ON up.predmet_id = p.id
        WHERE up.ucenec_id = ?
        ORDER BY p.naziv_predmeta
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ucenecId]);
    $predmeti = $stmt->fetchAll();
    
    // Za vsak predmet pridobi učitelje
    foreach ($predmeti as &$predmet) {
        $predmetId = $predmet['id'];
        
        // Pridobi učitelje, ki so dodeljeni predmetu
        $uciteljiSql = "
            SELECT DISTINCT CONCAT(u.ime, ' ', u.priimek) AS ucitelj_ime
            FROM ucitelji u
            INNER JOIN ucitelji_predmeti utp ON utp.ucitelj_id = u.id
            WHERE utp.predmet_id = ?
            ORDER BY u.ime, u.priimek
        ";
        
        $uciteljiStmt = $pdo->prepare($uciteljiSql);
        $uciteljiStmt->execute([$predmetId]);
        $ucitelji = $uciteljiStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Če ima učenec razred, dodaj tudi učitelje, ki so dodeljeni temu razredu in so dodeljeni predmetu
        if ($ucenecRazred && !empty($ucitelji)) {
            // Preveri, če so kateri učitelji že v seznamu, ker so dodeljeni razredu
            $razredUciteljiSql = "
                SELECT DISTINCT CONCAT(u.ime, ' ', u.priimek) AS ucitelj_ime
                FROM ucitelji u
                INNER JOIN ucitelji_predmeti utp ON utp.ucitelj_id = u.id
                WHERE utp.predmet_id = ? AND u.razred = ?
                ORDER BY u.ime, u.priimek
            ";
            $razredUciteljiStmt = $pdo->prepare($razredUciteljiSql);
            $razredUciteljiStmt->execute([$predmetId, $ucenecRazred]);
            $razredUcitelji = $razredUciteljiStmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Združi seznama in odstrani duplikate
            $ucitelji = array_unique(array_merge($ucitelji, $razredUcitelji));
        }
        
        $predmet['ucitelji'] = !empty($ucitelji) ? implode(', ', $ucitelji) : null;
    }
    unset($predmet);
    
    json_response(['success' => true, 'predmeti' => $predmeti]);
    
} catch (Exception $e) {
    error_log('UCENEC_PREDMETI_ERROR: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Napaka pri pridobivanju predmetov.'], 500);
}










