<?php
function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host = getenv('DB_HOST') ?: 'db';
    $port = getenv('DB_PORT') ?: '5432';
    $name = getenv('DB_NAME') ?: 'todolist';
    $user = getenv('DB_USER') ?: 'todolist';
    $pass = getenv('DB_PASS') ?: 'todolist';

    $dsn = "pgsql:host={$host};port={$port};dbname={$name}";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Auto-migration (minimalnie)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tasks (
            id SERIAL PRIMARY KEY,
            title TEXT NOT NULL,
            status VARCHAR(10) NOT NULL DEFAULT 'todo',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
    ");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_tasks_status ON tasks(status);");

    return $pdo;
}
