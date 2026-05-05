<?php

$host   = 'localhost';
$dbname = 'your_database_name';
$user   = 'your_username';
$pass   = 'your_password';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('<p style="color:#991B1B;padding:1rem;font-family:sans-serif;">
        <strong>Database connection failed:</strong> ' .
        htmlspecialchars($e->getMessage()) .
    '</p>');
}

$pdo->exec("
    CREATE TABLE IF NOT EXISTS colors (
        id        INT          NOT NULL AUTO_INCREMENT,
        name      VARCHAR(100) NOT NULL,
        hex_value VARCHAR(7)   NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uq_name      (name),
        UNIQUE KEY uq_hex_value (hex_value)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

$rowCount = (int) $pdo->query("SELECT COUNT(*) FROM colors")->fetchColumn();
if ($rowCount === 0) {
    $defaults = [
        ['Red',    '#DC2626'],
        ['Orange', '#EA580C'],
        ['Yellow', '#CA8A04'],
        ['Green',  '#16A34A'],
        ['Blue',   '#2563EB'],
        ['Purple', '#7C3AED'],
        ['Grey',   '#6B7280'],
        ['Brown',  '#92400E'],
        ['Black',  '#111827'],
        ['Teal',   '#0D9488'],
    ];
    $stmt = $pdo->prepare("INSERT INTO colors (name, hex_value) VALUES (?, ?)");
    foreach ($defaults as [$name, $hex]) {
        $stmt->execute([$name, $hex]);
    }
}
