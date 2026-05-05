<?php
require 'db.php';

header('Content-Type: text/css; charset=UTF-8');

$rows = $pdo->query('SELECT id, hex_value FROM colors ORDER BY id ASC')->fetchAll();

foreach ($rows as $row) {
    $id = (int)$row['id'];
    $hex = strtoupper(trim($row['hex_value']));
    if (!preg_match('/^#[0-9A-F]{6}$/', $hex)) {
        continue;
    }
    echo '.paint-color-' . $id . ' { background-color: ' . $hex . '; }' . PHP_EOL;
}
