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
    $naslov = trim($_POST['naslov_naloge'] ?? $_POST['naslov'] ?? '');
    $opis = trim($_POST['opis_naloge'] ?? $_POST['opis'] ?? '');
    
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
    if (!isset($_FILES['datoteka'])) {
        json_response(['success' => false, 'message' => 'Datoteka ni bila naložena.'], 400);
    }
    
    if ($_FILES['datoteka']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'Datoteka je prevelika (presega upload_max_filesize).',
            UPLOAD_ERR_FORM_SIZE => 'Datoteka je prevelika (presega MAX_FILE_SIZE).',
            UPLOAD_ERR_PARTIAL => 'Datoteka je bila delno naložena.',
            UPLOAD_ERR_NO_FILE => 'Nobena datoteka ni bila naložena.',
            UPLOAD_ERR_NO_TMP_DIR => 'Manjka začasna mapa.',
            UPLOAD_ERR_CANT_WRITE => 'Ni možno zapisati datoteke na disk.',
            UPLOAD_ERR_EXTENSION => 'Nalaganje datoteke je ustavila razširitev.',
        ];
        $errorMsg = $errorMessages[$_FILES['datoteka']['error']] ?? 'Neznana napaka pri nalaganju datoteke (koda: ' . $_FILES['datoteka']['error'] . ').';
        json_response(['success' => false, 'message' => $errorMsg], 400);
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
    
    if (!$ucenec) {
        json_response(['success' => false, 'message' => 'Napaka: Učenec ne obstaja v bazi.'], 500);
    }
    
    // Sanitiziraj ime datoteke - odstrani nevarné znake
    $safePriimek = preg_replace('/[^A-Za-z0-9 _-]+/u', '', $ucenec['priimek']);
    $safeIme = preg_replace('/[^A-Za-z0-9 _-]+/u', '', $ucenec['ime']);
    $safeNaslov = preg_replace('/[^A-Za-z0-9 _-]+/u', '', $naslov);
    
    $newFileName = $safePriimek . ' ' . $safeIme . ' - ' . $safeNaslov . '.' . $fileExtension;
    $uploadPath = __DIR__ . '/../uploads/' . $newFileName;
    
    // Ustvari uploads mapo, če ne obstaja
    if (!is_dir(__DIR__ . '/../uploads')) {
        mkdir(__DIR__ . '/../uploads', 0755, true);
    }
    
    // Premakni datoteko
    if (!move_uploaded_file($fileTmpName, $uploadPath)) {
        error_log('NALOGE_ODDAJ_FILE_ERROR: Cannot move file from ' . $fileTmpName . ' to ' . $uploadPath);
        json_response(['success' => false, 'message' => 'Napaka pri shranjevanju datoteke na strežnik. Preverite dovoljenja za mapo uploads.'], 500);
    }
    
    // Preveri, ali datoteka res obstaja po premiku
    if (!file_exists($uploadPath)) {
        error_log('NALOGE_ODDAJ_FILE_ERROR: File does not exist after move: ' . $uploadPath);
        json_response(['success' => false, 'message' => 'Datoteka ni bila shranjena pravilno.'], 500);
    }
    
    // Preveri, ali naloga že obstaja in jo posodobi ali ustvari novo
    try {
        // Preveri strukturo tabele - ali ima naslov ali naslov_naloge, in opis
        try {
            $testStmt = $pdo->query("SHOW COLUMNS FROM naloge LIKE 'naslov_naloge'");
            $hasNaslovNaloge = $testStmt->rowCount() > 0;
        } catch (Exception $e) {
            $hasNaslovNaloge = false;
        }
        
        try {
            $testStmt = $pdo->query("SHOW COLUMNS FROM naloge LIKE 'opis_naloge'");
            $hasOpisNaloge = $testStmt->rowCount() > 0;
        } catch (Exception $e) {
            $hasOpisNaloge = false;
        }
        
        try {
            $testStmt = $pdo->query("SHOW COLUMNS FROM naloge LIKE 'opis'");
            $hasOpis = $testStmt->rowCount() > 0;
        } catch (Exception $e) {
            $hasOpis = false;
        }
        
        $naslovColumn = $hasNaslovNaloge ? 'naslov_naloge' : 'naslov';
        $hasOpisColumn = $hasOpisNaloge || $hasOpis;
        
        $stmt = $pdo->prepare("SELECT id FROM naloge WHERE ucenec_id = ? AND predmet_id = ? AND $naslovColumn = ?");
        $stmt->execute([$_SESSION['user_id'], $predmetId, $naslov]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Posodobi obstoječo nalogo
            if ($hasOpisColumn) {
                $opisColumn = $hasOpisNaloge ? 'opis_naloge' : 'opis';
                $stmt = $pdo->prepare("UPDATE naloge SET $opisColumn = ?, datoteka = ?, datum_oddaje = NOW() WHERE id = ?");
                $stmt->execute([$opis, $newFileName, $existing['id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE naloge SET datoteka = ?, datum_oddaje = NOW() WHERE id = ?");
                $stmt->execute([$newFileName, $existing['id']]);
            }
        } else {
            // Ustvari novo nalogo
            if ($hasOpisColumn) {
                $opisColumn = $hasOpisNaloge ? 'opis_naloge' : 'opis';
                $stmt = $pdo->prepare("INSERT INTO naloge (ucenec_id, predmet_id, $naslovColumn, $opisColumn, datoteka, datum_oddaje) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$_SESSION['user_id'], $predmetId, $naslov, $opis, $newFileName]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO naloge (ucenec_id, predmet_id, $naslovColumn, datoteka, datum_oddaje) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$_SESSION['user_id'], $predmetId, $naslov, $newFileName]);
            }
        }
        
        json_response(['success' => true, 'message' => 'Naloga uspešno oddana.']);
        
    } catch (PDOException $dbError) {
        // Če je napaka pri bazi, še vedno poskusi izbrisati datoteko
        if (file_exists($uploadPath)) {
            @unlink($uploadPath);
        }
        error_log('NALOGE_ODDAJ_DB_ERROR: ' . $dbError->getMessage());
        json_response(['success' => false, 'message' => 'Napaka pri shranjevanju v bazo: ' . $dbError->getMessage()], 500);
    }
    
} catch (Exception $e) {
    error_log('NALOGE_ODDAJ_ERROR: ' . $e->getMessage());
    error_log('NALOGE_ODDAJ_STACK: ' . $e->getTraceAsString());
    json_response(['success' => false, 'message' => 'Napaka pri oddaji naloge: ' . $e->getMessage()], 500);
}













