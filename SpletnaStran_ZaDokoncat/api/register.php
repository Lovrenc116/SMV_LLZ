<?php
require_once __DIR__ . '/db.php';

// Registration is for students (ucenci)
$ime = $_POST['ime'] ?? null;
$priimek = $_POST['priimek'] ?? null;
$email = $_POST['email'] ?? null;
$geslo = $_POST['geslo'] ?? null;
$razred = $_POST['razred'] ?? null;
if (!$ime || !$priimek || !$email || !$geslo || !$razred) {
    $body = read_json_body();
    $ime = $ime ?? ($body['ime'] ?? null);
    $priimek = $priimek ?? ($body['priimek'] ?? null);
    $email = $email ?? ($body['email'] ?? null);
    $geslo = $geslo ?? ($body['geslo'] ?? null);
    $razred = $razred ?? ($body['razred'] ?? null);
}

if (!$ime || !$priimek || !$email || !$geslo || !$razred) {
    json_response(['success' => false, 'message' => 'Manjkajoči podatki.'], 400);
}

try {
    $pdo = get_db();
    // Store password as plain text (insecure; for demo only)
    $stmt = $pdo->prepare('INSERT INTO ucenci (ime, priimek, email, geslo, razred) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$ime, $priimek, $email, $geslo, $razred]);
    json_response(['success' => true]);
} catch (PDOException $e) {
    error_log('REGISTER_ERROR: ' . $e->getMessage());
    if ($e->getCode() === '23000') {
        json_response(['success' => false, 'message' => 'Email je že uporabljen.'], 409);
    }
    // Preveri če gre za problem s povezavo ali bazo
    if ($e->getCode() === '1049' || strpos($e->getMessage(), 'Unknown database') !== false) {
        json_response(['success' => false, 'message' => 'Baza podatkov ne obstaja. Prosimo preverite, ali je MySQL zagnan in baza ustvarjena.'], 500);
    } elseif ($e->getCode() === '2002' || strpos($e->getMessage(), 'refused') !== false) {
        json_response(['success' => false, 'message' => 'Ni mogoče se povezati z MySQL strežnikom. Prosimo preverite, ali je MySQL zagnan v XAMPP.'], 500);
    } else {
        json_response(['success' => false, 'message' => 'Napaka pri registraciji: ' . $e->getMessage()], 500);
    }
}


