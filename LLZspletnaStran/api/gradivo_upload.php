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
    json_response(['success' => false, 'message' => 'Nalaganje datoteke ni uspelo.'], 500);
}

$relPath = 'uploads/' . $filename;
// Poskusi vstaviti z datum_nalaganja, če stolpec obstaja
try {
    $ins = $pdo->prepare('INSERT INTO gradiva (predmet_id, ime, datoteka, datum_nalaganja) VALUES (?, ?, ?, NOW())');
    $ins->execute([$predmetId, $naziv, $relPath]);
} catch (PDOException $e) {
    // Če stolpec datum_nalaganja ne obstaja, vstavi brez njega
    $ins = $pdo->prepare('INSERT INTO gradiva (predmet_id, ime, datoteka) VALUES (?, ?, ?)');
    $ins->execute([$predmetId, $naziv, $relPath]);
}

json_response(['success' => true, 'path' => $relPath]);


















