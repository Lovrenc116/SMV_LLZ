<?php
require_once __DIR__ . '/db.php';
require_role(['ucenec']);

$pdo = get_db();

$ucenecId = (int)($_SESSION['user_id'] ?? 0);
$predmetId = (int)($_POST['predmet_id'] ?? 0);
$naslov = trim($_POST['naslov'] ?? '');
if ($ucenecId <= 0 || $predmetId <= 0 || $naslov === '' || !isset($_FILES['datoteka'])) {
    json_response(['success' => false, 'message' => 'Manjkajoči podatki.'], 400);
}

// Check student attends subject
$stmt = $pdo->prepare('SELECT 1 FROM ucenci_predmeti WHERE ucenec_id = ? AND predmet_id = ?');
$stmt->execute([$ucenecId, $predmetId]);
if (!$stmt->fetch()) {
    json_response(['success' => false, 'message' => 'Nimate predmet vpisan.'], 403);
}

// Build filename: Priimek Ime – Naslov naloge.ext
$user = $pdo->prepare('SELECT ime, priimek FROM ucenci WHERE id = ?');
$user->execute([$ucenecId]);
$u = $user->fetch();
$ext = pathinfo($_FILES['datoteka']['name'], PATHINFO_EXTENSION);
$safeTitle = preg_replace('/[^A-Za-z0-9 _-]+/u', '', $naslov);
$filename = sprintf('%s %s – %s%s', $u['priimek'], $u['ime'], $safeTitle, $ext ? ('.' . $ext) : '');

$uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';
if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
$destPath = $uploadDir . DIRECTORY_SEPARATOR . $filename;

// If exists, overwrite only with confirmation flag
$overwrite = ($_POST['overwrite'] ?? 'false') === 'true';
if (file_exists($destPath) && !$overwrite) {
    json_response(['success' => false, 'message' => 'Datoteka že obstaja. Potrdite prepis.', 'needsConfirm' => true]);
}

if (!move_uploaded_file($_FILES['datoteka']['tmp_name'], $destPath)) {
    json_response(['success' => false, 'message' => 'Nalaganje datoteke ni uspelo.'], 500);
}

$relPath = 'uploads/' . $filename;

// Upsert submission
$sel = $pdo->prepare('SELECT id FROM naloge WHERE ucenec_id = ? AND predmet_id = ? AND naslov = ?');
$sel->execute([$ucenecId, $predmetId, $naslov]);
if ($row = $sel->fetch()) {
    $upd = $pdo->prepare('UPDATE naloge SET datoteka = ?, datum_oddaje = NOW() WHERE id = ?');
    $upd->execute([$relPath, $row['id']]);
} else {
    $ins = $pdo->prepare('INSERT INTO naloge (ucenec_id, predmet_id, naslov, datoteka, datum_oddaje) VALUES (?, ?, ?, ?, NOW())');
    $ins->execute([$ucenecId, $predmetId, $naslov, $relPath]);
}

json_response(['success' => true, 'path' => $relPath]);















