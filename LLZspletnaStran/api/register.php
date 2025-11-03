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
    if ($e->getCode() === '23000') {
        json_response(['success' => false, 'message' => 'Email je že uporabljen.'], 409);
    }
    json_response(['success' => false, 'message' => 'Napaka pri registraciji.'], 500);
}


