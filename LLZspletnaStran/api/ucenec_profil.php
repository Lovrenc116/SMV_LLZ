<?php
require_once __DIR__ . '/db.php';

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['vloga'] !== 'ucenec') {
    json_response(['success' => false, 'message' => 'Ni dovoljenja.'], 401);
}

try {
    $pdo = get_db();
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // Pridobi podatke o trenutnem učencu
            $stmt = $pdo->prepare('SELECT ime, priimek, email, razred FROM ucenci WHERE id = ?');
            $stmt->execute([$_SESSION['user_id']]);
            $ucenec = $stmt->fetch();
            
            if (!$ucenec) {
                json_response(['success' => false, 'message' => 'Učenec ne obstaja.'], 404);
            }
            
            json_response(['success' => true] + $ucenec);
            
        case 'POST':
            // Posodobi podatke učenca
            $data = read_json_body();
            $ime = trim($data['ime'] ?? '');
            $priimek = trim($data['priimek'] ?? '');
            $email = trim($data['email'] ?? '');
            $razred = trim($data['razred'] ?? '');
            
            if ($ime === '' || $priimek === '' || $email === '' || $razred === '') {
                json_response(['success' => false, 'message' => 'Manjkajoča polja.'], 400);
            }
            
            // Preveri, ali email že obstaja pri drugem učencu
            $stmt = $pdo->prepare('SELECT id FROM ucenci WHERE email = ? AND id != ?');
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                json_response(['success' => false, 'message' => 'Email je že uporabljen.'], 409);
            }
            
            $stmt = $pdo->prepare('UPDATE ucenci SET ime = ?, priimek = ?, email = ?, razred = ? WHERE id = ?');
            $stmt->execute([$ime, $priimek, $email, $razred, $_SESSION['user_id']]);
            
            json_response(['success' => true]);
            
        default:
            json_response(['success' => false, 'message' => 'Metoda ni podprta.'], 405);
    }
} catch (Exception $e) {
    error_log('UCENEC_PROFIL_ERROR: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Napaka pri obdelavi zahteve.'], 500);
}













