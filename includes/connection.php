<?php
require_once __DIR__ . '/Env.php';
Env::load(__DIR__ . '/../.env');

$config = [
    'host' => Env::get('DB_HOST', 'localhost'),
    'name' => Env::get('DB_NAME', 'product_supplier_system'),
    'user' => Env::get('DB_USER', 'root'),
    'pass' => Env::get('DB_PASS', ''),
    'charset' => Env::get('DB_CHARSET', 'utf8mb4')
];

try {
    $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";

    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());

    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Database connection error: ' . $e->getMessage()
    ]));
}
?>