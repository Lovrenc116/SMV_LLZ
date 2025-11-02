<?php
session_start();
// Uniči vse podatke v seji
$_SESSION = array();

// Če se uporabljajo piškotki, jih uniči
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Uniči sejo
session_destroy();

// Določi pot do login.php glede na to, kje se nahaja api/logout.php
// api/logout.php je v api/ mapi, login.php je v root mapi
$loginPath = dirname(__DIR__) . '/login.php';
$relativePath = '../login.php';

// Preusmeri na stran za prijavo
header('Location: ' . $relativePath);
exit;



