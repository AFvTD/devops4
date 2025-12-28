<?php
require __DIR__ . '/db.php';

function add_task(string $title): void {
    $pdo = db();
    $stmt = $pdo->prepare("INSERT INTO tasks(title, status) VALUES(:title, 'todo')");
    $stmt->execute([':title' => $title]);
}

function move_task(int $id, string $status): void {
    $allowed = ['todo', 'doing', 'done'];
    if (!in_array($status, $allowed, true)) {
        $status = 'todo';
    }
    $pdo = db();
    $stmt = $pdo->prepare("UPDATE tasks SET status=:status WHERE id=:id");
    $stmt->execute([':status' => $status, ':id' => $id]);
}

function delete_task(int $id): void {
    $pdo = db();
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id=:id");
    $stmt->execute([':id' => $id]);
}

function get_tasks_grouped(): array {
    $pdo = db();
    $rows = $pdo->query("SELECT id, title, status, created_at FROM tasks ORDER BY id DESC")->fetchAll();

    $out = ['todo' => [], 'doing' => [], 'done' => []];
    foreach ($rows as $r) {
        $s = $r['status'] ?? 'todo';
        if (!isset($out[$s])) $s = 'todo';
        $out[$s][] = $r;
    }
    return $out;
}
