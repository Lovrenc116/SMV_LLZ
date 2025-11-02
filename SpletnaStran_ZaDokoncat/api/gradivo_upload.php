<?php
require_once __DIR__ . '/db.php';
require_role(['ucitelj']);

$pdo = get_db();

// Validate teacher teaches this subject
$uciteljId = (int)($_SESSION['user_id'] ?? 0);
$predmetId = (int)($_POST['predmet_id'] ?? 0);
$naziv = trim($_POST['naziv_gradiva'] ?? '');
if ($uciteljId <= 0 || $predmetId <= 0 || $naziv === '' || !isset($_FILES['datoteka_gradiva'])) {
    json_response(['success' => false, 'message' => 'Manjkajoči podatki.'], 400);
}

$stmt = $pdo->prepare('SELECT 1 FROM ucitelji_predmeti WHERE ucitelj_id = ? AND predmet_id = ?');
$stmt->execute([$uciteljId, $predmetId]);
if (!$stmt->fetch()) {
    json_response(['success' => false, 'message' => 'Nimate dostopa do tega predmeta.'], 403);
}

$uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';
if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }

$file = $_FILES['datoteka_gradiva'];
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$safeName = preg_replace('/[^A-Za-z0-9 _-]+/u', '', $naziv);
$filename = 'gradivo_' . $predmetId . '_' . time() . '_' . $safeName . ($ext ? ('.' . $ext) : '');
$destPath = $uploadDir . DIRECTORY_SEPARATOR . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    error_log('GRADIVO_UPLOAD_ERROR: Neuspešno premikanje datoteke iz ' . $file['tmp_name'] . ' v ' . $destPath);
    json_response(['success' => false, 'message' => 'Nalaganje datoteke ni uspelo. Prosimo preverite dovoljenja za mapo uploads.'], 500);
}

$relPath = 'uploads/' . $filename;
try {
    // Shrani v naziv_gradiva (primarno) in ime (za kompatibilnost)
    $ins = $pdo->prepare('INSERT INTO gradiva (predmet_id, naziv_gradiva, ime, datoteka) VALUES (?, ?, ?, ?)');
    $ins->execute([$predmetId, $naziv, $naziv, $relPath]);
    json_response(['success' => true, 'path' => $relPath, 'message' => 'Gradivo uspešno naloženo.']);
} catch (PDOException $e) {
    error_log('GRADIVO_UPLOAD_DB_ERROR: ' . $e->getMessage());
    // Izbriši datoteko, če shranjevanje v bazo ni uspelo
    if (file_exists($destPath)) {
        @unlink($destPath);
    }
    json_response(['success' => false, 'message' => 'Napaka pri shranjevanju v bazo: ' . $e->getMessage()], 500);
}















