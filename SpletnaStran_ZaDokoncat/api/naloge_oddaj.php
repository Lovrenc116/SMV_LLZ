<?php
require_once __DIR__ . '/db.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['vloga'] !== 'ucenec') {
    json_response(['success' => false, 'message' => 'Ni dovoljenja.'], 401);
}

try {
    $pdo = get_db();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response(['success' => false, 'message' => 'Samo POST metoda je dovoljena.'], 405);
    }
    
    $predmetId = $_POST['predmet_id'] ?? null;
    $naslov = trim($_POST['naslov_naloge'] ?? '');
    $opis = trim($_POST['opis_naloge'] ?? '');
    
    if (!$predmetId || $naslov === '') {
        json_response(['success' => false, 'message' => 'Manjkajoči podatki.'], 400);
    }
    
    // Preveri, ali ima učenec dostop do predmeta
    $stmt = $pdo->prepare('SELECT 1 FROM ucenci_predmeti WHERE ucenec_id = ? AND predmet_id = ?');
    $stmt->execute([$_SESSION['user_id'], $predmetId]);
    if (!$stmt->fetch()) {
        json_response(['success' => false, 'message' => 'Ni dostopa do predmeta.'], 403);
    }
    
    // Preveri, ali datoteka obstaja
    if (!isset($_FILES['datoteka']) || $_FILES['datoteka']['error'] !== UPLOAD_ERR_OK) {
        json_response(['success' => false, 'message' => 'Napaka pri nalaganju datoteke.'], 400);
    }
    
    $file = $_FILES['datoteka'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    
    // Preveri velikost datoteke (max 10MB)
    if ($fileSize > 10 * 1024 * 1024) {
        json_response(['success' => false, 'message' => 'Datoteka je prevelika (max 10MB).'], 400);
    }
    
    // Preveri tip datoteke
    $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedTypes)) {
        json_response(['success' => false, 'message' => 'Nedovoljen tip datoteke.'], 400);
    }
    
    // Ustvari novo ime datoteke: Priimek Ime - Naslov naloge.extension
    $stmt = $pdo->prepare('SELECT ime, priimek FROM ucenci WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $ucenec = $stmt->fetch();
    
    $newFileName = $ucenec['priimek'] . ' ' . $ucenec['ime'] . ' - ' . $naslov . '.' . $fileExtension;
    $uploadPath = __DIR__ . '/../uploads/' . $newFileName;
    
    // Ustvari uploads mapo, če ne obstaja
    if (!is_dir(__DIR__ . '/../uploads')) {
        mkdir(__DIR__ . '/../uploads', 0755, true);
    }
    
    // Premakni datoteko
    if (!move_uploaded_file($fileTmpName, $uploadPath)) {
        json_response(['success' => false, 'message' => 'Napaka pri shranjevanju datoteke.'], 500);
    }
    
    // Preveri, ali naloga že obstaja in jo posodobi ali ustvari novo
    $stmt = $pdo->prepare('SELECT id FROM naloge WHERE ucenec_id = ? AND predmet_id = ? AND naslov_naloge = ?');
    $stmt->execute([$_SESSION['user_id'], $predmetId, $naslov]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Posodobi obstoječo nalogo
        $stmt = $pdo->prepare('UPDATE naloge SET opis_naloge = ?, datoteka = ?, datum_oddaje = NOW() WHERE id = ?');
        $stmt->execute([$opis, $newFileName, $existing['id']]);
    } else {
        // Ustvari novo nalogo
        $stmt = $pdo->prepare('INSERT INTO naloge (ucenec_id, predmet_id, naslov_naloge, opis_naloge, datoteka, datum_oddaje) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$_SESSION['user_id'], $predmetId, $naslov, $opis, $newFileName]);
    }
    
    json_response(['success' => true, 'message' => 'Naloga uspešno oddana.']);
    
} catch (Exception $e) {
    error_log('NALOGE_ODDAJ_ERROR: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Napaka pri oddaji naloge.'], 500);
}










