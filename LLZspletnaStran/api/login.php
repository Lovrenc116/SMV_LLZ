<?php
require_once __DIR__ . '/db.php';

// Accept form-data or JSON
$email = $_POST['email'] ?? null;
$geslo = $_POST['geslo'] ?? null;
$vloga = $_POST['vloga'] ?? null;
if ($email === null || $geslo === null || $vloga === null) {
    $body = read_json_body();
    $email = $email ?? ($body['email'] ?? null);
    $geslo = $geslo ?? ($body['geslo'] ?? null);
    $vloga = $vloga ?? ($body['vloga'] ?? null);
}

if (!$email || !$geslo || !$vloga) {
    json_response(['success' => false, 'message' => 'Manjkajoči podatki.'], 400);
}

try {
    if ($vloga === 'administrator') {
        // Simple admin: fixed credentials
        if ($email === 'llzspletnastran@gmail.com' && $geslo === 'LLZ2007') {
            $_SESSION['user_id'] = 0;
            $_SESSION['ime'] = 'Admin';
            $_SESSION['priimek'] = 'Uporabnik';
            $_SESSION['vloga'] = 'administrator';
            json_response(['success' => true]);
        } else {
            json_response(['success' => false, 'message' => 'Napačen admin email ali geslo.'], 401);
        }
    }

    $pdo = get_db();

    if ($vloga === 'ucenec') {
        $stmt = $pdo->prepare('SELECT id, ime, priimek, geslo FROM ucenci WHERE email = ?');
    } elseif ($vloga === 'ucitelj') {
        $stmt = $pdo->prepare('SELECT id, ime, priimek, geslo FROM ucitelji WHERE email = ?');
    } else {
        json_response(['success' => false, 'message' => 'Neveljavna vloga.'], 400);
    }

    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) {
        json_response(['success' => false, 'message' => 'Uporabnik ne obstaja.'], 404);
    }

    // Plain text comparison (insecure; for demo only)
    $ok = hash_equals($user['geslo'], $geslo) || password_verify($geslo, $user['geslo']);
    if (!$ok) {
        json_response(['success' => false, 'message' => 'Napačno geslo.'], 401);
    }

    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['ime'] = $user['ime'];
    $_SESSION['priimek'] = $user['priimek'];
    $_SESSION['vloga'] = $vloga;

    json_response(['success' => true]);
} catch (Throwable $e) {
    error_log('LOGIN_ERROR: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Napaka pri prijavi: ' . $e->getMessage()], 500);
}


