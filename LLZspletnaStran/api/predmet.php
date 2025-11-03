<?php
require_once __DIR__ . '/db.php';

$pdo = get_db();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $rows = $pdo->query('SELECT id, naziv_predmeta, kratica FROM predmeti ORDER BY id')->fetchAll();
        json_response($rows);
    case 'POST':
        require_role(['administrator']);
        try {
            $data = read_json_body();
            $id = isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null;
            $naziv = trim($data['naziv_predmeta'] ?? '');
            $kratica = trim($data['kratica'] ?? '');
            if ($naziv === '' || $kratica === '') {
                json_response(['success' => false, 'message' => 'Manjkajoča polja.'], 400);
            }
            if ($id) {
                $stmt = $pdo->prepare('UPDATE predmeti SET naziv_predmeta = ?, kratica = ? WHERE id = ?');
                $stmt->execute([$naziv, $kratica, $id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO predmeti (naziv_predmeta, kratica) VALUES (?, ?)');
                $stmt->execute([$naziv, $kratica]);
            }
            json_response(['success' => true]);
        } catch (PDOException $e) {
            error_log('PREDMET_POST_ERROR: ' . $e->getMessage());
            json_response(['success' => false, 'message' => 'Napaka pri shranjevanju predmeta.'], 500);
        }
    case 'DELETE':
        require_role(['administrator']);
        try {
            $data = read_json_body();
            $id = (int)($data['id'] ?? 0);
            if ($id <= 0) { json_response(['success' => false, 'message' => 'Neveljaven ID'], 400); }
            $stmt = $pdo->prepare('DELETE FROM predmeti WHERE id = ?');
            $stmt->execute([$id]);
            json_response(['success' => true]);
        } catch (PDOException $e) {
            error_log('PREDMET_DELETE_ERROR: ' . $e->getMessage());
            json_response(['success' => false, 'message' => 'Napaka pri brisanju predmeta.'], 500);
        }
    default:
        json_response(['success' => false, 'message' => 'Metoda ni podprta.'], 405);
}


