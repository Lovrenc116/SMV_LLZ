<?php
require_once __DIR__ . '/db.php';
$pdo = get_db();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Če je podan ID učenca, vrni njegove predmete
        if (isset($_GET['id']) && $_GET['id']) {
            $ucenecId = (int)$_GET['id'];
            $stmt = $pdo->prepare('SELECT predmet_id FROM ucenci_predmeti WHERE ucenec_id = ?');
            $stmt->execute([$ucenecId]);
            $predmetiIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
            json_response(['success' => true, 'predmeti' => $predmetiIds]);
            break;
        }
        
        // List students with classes and subjects
        $sql = "
            SELECT u.id, u.ime, u.priimek, u.email, u.razred,
                   GROUP_CONCAT(p.naziv_predmeta ORDER BY p.naziv_predmeta SEPARATOR ', ') AS predmeti
            FROM ucenci u
            LEFT JOIN ucenci_predmeti up ON up.ucenec_id = u.id
            LEFT JOIN predmeti p ON p.id = up.predmet_id
            GROUP BY u.id, u.ime, u.priimek, u.email, u.razred
            ORDER BY u.id
        ";
        $rows = $pdo->query($sql)->fetchAll();
        json_response($rows);

    case 'POST':
        require_role(['administrator']);
        try {
            $data = read_json_body();
            $id = isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null;
            $ime = trim($data['ime_ucenca'] ?? '');
            $priimek = trim($data['priimek_ucenca'] ?? '');
            $email = trim($data['email_ucenca'] ?? '');
            $geslo = $data['geslo_ucenca'] ?? '';
            $razred = trim($data['razred_ucenca'] ?? '');
            $predmeti = isset($data['predmeti']) && is_array($data['predmeti']) ? array_map('intval', $data['predmeti']) : [];
            if ($ime === '' || $priimek === '' || $email === '' || $razred === '') {
                json_response(['success' => false, 'message' => 'Manjkajoča polja.'], 400);
            }
            if ($id) {
                if ($geslo !== '') {
                    $stmt = $pdo->prepare('UPDATE ucenci SET ime = ?, priimek = ?, email = ?, geslo = ?, razred = ? WHERE id = ?');
                    $stmt->execute([$ime, $priimek, $email, $geslo, $razred, $id]);
                } else {
                    $stmt = $pdo->prepare('UPDATE ucenci SET ime = ?, priimek = ?, email = ?, razred = ? WHERE id = ?');
                    $stmt->execute([$ime, $priimek, $email, $razred, $id]);
                }
                $pdo->prepare('DELETE FROM ucenci_predmeti WHERE ucenec_id = ?')->execute([$id]);
            } else {
                if ($geslo === '') { json_response(['success' => false, 'message' => 'Geslo je obvezno za novega učenca.'], 400); }
                $stmt = $pdo->prepare('INSERT INTO ucenci (ime, priimek, email, geslo, razred) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$ime, $priimek, $email, $geslo, $razred]);
                $id = (int)$pdo->lastInsertId();
            }
            if (!empty($predmeti)) {
                $ins = $pdo->prepare('INSERT INTO ucenci_predmeti (ucenec_id, predmet_id) VALUES (?, ?)');
                foreach ($predmeti as $pid) { $ins->execute([$id, $pid]); }
            }
            json_response(['success' => true]);
        } catch (PDOException $e) {
            error_log('UCENEC_POST_ERROR: ' . $e->getMessage());
            $msg = $e->getCode() === '23000' ? 'Email je že uporabljen.' : 'Napaka pri shranjevanju učenca.';
            json_response(['success' => false, 'message' => $msg], 500);
        }

    case 'DELETE':
        require_role(['administrator']);
        try {
            $data = read_json_body();
            $id = (int)($data['id'] ?? 0);
            if ($id <= 0) { json_response(['success' => false, 'message' => 'Neveljaven ID'], 400); }
            $pdo->prepare('DELETE FROM ucenci_predmeti WHERE ucenec_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM ucenci WHERE id = ?')->execute([$id]);
            json_response(['success' => true]);
        } catch (PDOException $e) {
            error_log('UCENEC_DELETE_ERROR: ' . $e->getMessage());
            json_response(['success' => false, 'message' => 'Napaka pri brisanju učenca.'], 500);
        }

    default:
        json_response(['success' => false, 'message' => 'Metoda ni podprta.'], 405);
}


