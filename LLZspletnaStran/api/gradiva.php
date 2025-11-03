<?php
require_once __DIR__ . '/db.php';

$pdo = get_db();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        session_start();
        if (!isset($_SESSION['user_id'])) {
            json_response(['success' => false, 'message' => 'Ni dovoljenja.'], 401);
        }
        
        try {
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
            // Poskusi uporabiti datum_nalaganja, če ne obstaja, uporabi id za sortiranje
            try {
                $testStmt = $pdo->query("SHOW COLUMNS FROM gradiva LIKE 'datum_nalaganja'");
                $hasDatumNalaganja = $testStmt->rowCount() > 0;
            } catch (Exception $e) {
                $hasDatumNalaganja = false;
            }
            
            if ($hasDatumNalaganja) {
                $sql = "
                    SELECT g.id, g.ime as naziv_gradiva, g.datoteka, g.datum_nalaganja, p.naziv_predmeta
                    FROM gradiva g
                    INNER JOIN predmeti p ON p.id = g.predmet_id
                    WHERE g.predmet_id = ?
                    ORDER BY g.datum_nalaganja DESC
                ";
            } else {
                $sql = "
                    SELECT g.id, g.ime as naziv_gradiva, g.datoteka, NULL as datum_nalaganja, p.naziv_predmeta
                    FROM gradiva g
                    INNER JOIN predmeti p ON p.id = g.predmet_id
                    WHERE g.predmet_id = ?
                    ORDER BY g.id DESC
                ";
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$predmetId]);
            $gradiva = $stmt->fetchAll();
            
            json_response(['success' => true, 'gradiva' => $gradiva]);
            
        } catch (Exception $e) {
            error_log('GRADIVA_GET_ERROR: ' . $e->getMessage());
            json_response(['success' => false, 'message' => 'Napaka pri pridobivanju gradiv.'], 500);
        }
        break;
        
    case 'DELETE':
        require_role(['ucitelj']);
        try {
            $data = read_json_body();
            $gradivoId = (int)($data['id'] ?? 0);
            
            if ($gradivoId <= 0) {
                json_response(['success' => false, 'message' => 'Neveljaven ID'], 400);
            }
            
            // Preveri, ali je gradivo pripada predmetu, ki ga poučuje učitelj
            $stmt = $pdo->prepare('
                SELECT g.id, g.datoteka 
                FROM gradiva g
                INNER JOIN ucitelji_predmeti up ON up.predmet_id = g.predmet_id
                WHERE g.id = ? AND up.ucitelj_id = ?
            ');
            $stmt->execute([$gradivoId, $_SESSION['user_id']]);
            $gradivo = $stmt->fetch();
            
            if (!$gradivo) {
                json_response(['success' => false, 'message' => 'Nimate dovoljenja za brisanje tega gradiva.'], 403);
            }
            
            // Izbriši datoteko
            if ($gradivo['datoteka']) {
                $filePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . $gradivo['datoteka'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            
            // Izbriši iz baze
            $stmt = $pdo->prepare('DELETE FROM gradiva WHERE id = ?');
            $stmt->execute([$gradivoId]);
            
            json_response(['success' => true, 'message' => 'Gradivo uspešno izbrisano.']);
            
        } catch (Exception $e) {
            error_log('GRADIVA_DELETE_ERROR: ' . $e->getMessage());
            json_response(['success' => false, 'message' => 'Napaka pri brisanju gradiva.'], 500);
        }
        break;
        
    default:
        json_response(['success' => false, 'message' => 'Metoda ni podprta.'], 405);
}













