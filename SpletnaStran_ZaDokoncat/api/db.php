<?php
// Shared DB connection and helpers
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . DIRECTORY_SEPARATOR . 'php-error.log');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

if (session_status() === PHP_SESSION_NONE) { session_start(); }

function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $host = 'localhost';
        $dbCandidates = [
            'spletna_ucilnica',
            'llzspletnastranbaza',
        ];
        $user = 'root';
        $pass = '';
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $lastError = null;
        foreach ($dbCandidates as $db) {
            try {
                $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
                $pdo = new PDO($dsn, $user, $pass, $options);
                break;
            } catch (PDOException $e) {
                $lastError = $e;
                continue;
            }
        }
        if ($pdo === null) {
            throw $lastError ?? new RuntimeException('Database connection failed');
        }
    }
    return $pdo;
}

function json_response($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function read_json_body(): array {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    return is_array($data) ? $data : [];
}

function require_role(array $allowedRoles): void {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['vloga'] ?? '', $allowedRoles, true)) {
        json_response(['success' => false, 'message' => 'Nimate dovoljenja.'], 403);
    }
}


