<?php
require_once __DIR__ . '/db.php';
$pdo = get_db();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // List all teachers with aggregated subjects and razred
        $sql = "
            SELECT u.id, u.ime, u.priimek, u.email, u.razred,
                   GROUP_CONCAT(p.naziv_predmeta ORDER BY p.naziv_predmeta SEPARATOR ', ') AS predmeti
            FROM ucitelji u
            LEFT JOIN ucitelji_predmeti up ON up.ucitelj_id = u.id
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
            $ime = trim($data['ime_ucitelja'] ?? '');
            $priimek = trim($data['priimek_ucitelja'] ?? '');
            $email = trim($data['email_ucitelja'] ?? '');
            $geslo = $data['geslo_ucitelja'] ?? '';
            $razred = trim($data['razred_ucitelja'] ?? '');
            $predmeti = isset($data['predmeti']) && is_array($data['predmeti']) ? array_map('intval', $data['predmeti']) : [];
            if ($ime === '' || $priimek === '' || $email === '') {
                json_response(['success' => false, 'message' => 'Manjkajoča polja.'], 400);
            }
            if ($id) {
                if ($geslo !== '') {
                    $stmt = $pdo->prepare('UPDATE ucitelji SET ime = ?, priimek = ?, email = ?, geslo = ?, razred = ? WHERE id = ?');
                    $stmt->execute([$ime, $priimek, $email, $geslo, $razred ?: null, $id]);
                } else {
                    $stmt = $pdo->prepare('UPDATE ucitelji SET ime = ?, priimek = ?, email = ?, razred = ? WHERE id = ?');
                    $stmt->execute([$ime, $priimek, $email, $razred ?: null, $id]);
                }
                $pdo->prepare('DELETE FROM ucitelji_predmeti WHERE ucitelj_id = ?')->execute([$id]);
            } else {
                if ($geslo === '') { json_response(['success' => false, 'message' => 'Geslo je obvezno za novega učitelja.'], 400); }
                $stmt = $pdo->prepare('INSERT INTO ucitelji (ime, priimek, email, geslo, razred) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$ime, $priimek, $email, $geslo, $razred ?: null]);
                $id = (int)$pdo->lastInsertId();
            }
            if (!empty($predmeti)) {
                $ins = $pdo->prepare('INSERT INTO ucitelji_predmeti (ucitelj_id, predmet_id) VALUES (?, ?)');
                foreach ($predmeti as $pid) { $ins->execute([$id, $pid]); }
            }
            json_response(['success' => true]);
        } catch (PDOException $e) {
            error_log('UCITELJ_POST_ERROR: ' . $e->getMessage());
            $msg = $e->getCode() === '23000' ? 'Email je že uporabljen.' : 'Napaka pri shranjevanju učitelja.';
            json_response(['success' => false, 'message' => $msg], 500);
        }

    case 'DELETE':
        require_role(['administrator']);
        try {
            $data = read_json_body();
            $id = (int)($data['id'] ?? 0);
            if ($id <= 0) { json_response(['success' => false, 'message' => 'Neveljaven ID'], 400); }
            $pdo->prepare('DELETE FROM ucitelji_predmeti WHERE ucitelj_id = ?')->execute([$id]);
            $pdo->prepare('DELETE FROM ucitelji WHERE id = ?')->execute([$id]);
            json_response(['success' => true]);
        } catch (PDOException $e) {
            error_log('UCITELJ_DELETE_ERROR: ' . $e->getMessage());
            json_response(['success' => false, 'message' => 'Napaka pri brisanju učitelja.'], 500);
        }

    default:
        json_response(['success' => false, 'message' => 'Metoda ni podprta.'], 405);
}


