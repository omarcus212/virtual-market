<?php
require_once 'includes/connection.php';

try {
    // Test query
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();

    if ($result['test'] === 1) {
        echo "✅ CONNECTION SUCCESSFUL!\n\n";
        echo "Database: " . Env::get('DB_NAME') . "\n";
        echo "Host: " . Env::get('DB_HOST') . "\n";
        echo "User: " . Env::get('DB_USER') . "\n\n";

        // Count tables
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        echo "Tables found: " . count($tables) . "\n";

        foreach ($tables as $table) {
            echo "  - " . $table[array_key_first($table)] . "\n";
        }

    }

} catch (PDOException $e) {
    echo "❌ CONNECTION FAILED!\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Check:\n";
    echo "1. MySQL is running\n";
    echo "2. .env file exists and has correct credentials\n";
    echo "3. Database 'product_supplier_system' exists\n";
}
?>