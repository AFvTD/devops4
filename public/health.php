<?php
require __DIR__ . '/src/db.php';

try {
    $pdo = db();
    $stmt = $pdo->query('SELECT 1');
    $stmt->fetch();
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'time' => date('c')]);
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
