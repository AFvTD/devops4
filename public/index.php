<?php
require __DIR__ . '/src/tasks.php';

$action = $_POST['action'] ?? $_GET['action'] ?? null;

try {
    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        if ($title !== '') {
            add_task($title);
        }
        redirect_home();
    }

    if ($action === 'move') {
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'todo';
        if ($id > 0) {
            move_task($id, $status);
        }
        redirect_home();
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            delete_task($id);
        }
        redirect_home();
    }

    $tasks = get_tasks_grouped();
} catch (Throwable $e) {
    http_response_code(500);
    $error = $e->getMessage();
    $tasks = ['todo'=>[], 'doing'=>[], 'done'=>[]];
}

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function redirect_home() { header('Location: /'); exit; }
?>
<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Minimalny Kanban (PHP)</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .col-board { min-height: 60vh; }
    .task { border: 1px solid #e9ecef; border-radius: 12px; padding: 10px; margin-bottom: 10px; background: #fff; }
    .task-title { font-weight: 600; }
    .small-btn { font-size: 12px; padding: 2px 8px; }
  </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <span class="navbar-brand">TodoList / Mini Jira / Mini Trello (minimal)</span>
    <a class="text-white-50 small" href="/health.php">health</a>
  </div>
</nav>

<div class="container py-4">
  <?php if (isset($error)): ?>
    <div class="alert alert-danger">Błąd: <?= h($error) ?></div>
  <?php endif; ?>

  <div class="card mb-3">
    <div class="card-body">
      <form method="post" class="row g-2 align-items-center">
        <input type="hidden" name="action" value="add" />
        <div class="col-12 col-md-10">
          <input name="title" class="form-control" placeholder="Dodaj nowe zadanie…" maxlength="200" required />
        </div>
        <div class="col-12 col-md-2 d-grid">
          <button class="btn btn-primary" type="submit">Dodaj</button>
        </div>
      </form>
    </div>
  </div>

  <div class="row g-3">
    <?php
      $cols = [
        'todo'  => ['TODO', 'secondary'],
        'doing' => ['DOING', 'warning'],
        'done'  => ['DONE', 'success'],
      ];
      foreach ($cols as $key => [$label, $badge]) :
    ?>
      <div class="col-12 col-lg-4">
        <div class="card shadow-sm col-board">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold"><?= h($label) ?></span>
            <span class="badge text-bg-<?= h($badge) ?>"><?= count($tasks[$key] ?? []) ?></span>
          </div>
          <div class="card-body">
            <?php foreach (($tasks[$key] ?? []) as $t): ?>
              <div class="task">
                <div class="task-title"><?= h($t['title']) ?></div>
                <div class="text-muted small">#<?= (int)$t['id'] ?> · <?= h($t['created_at']) ?></div>

                <div class="mt-2 d-flex gap-2 flex-wrap">
                  <form method="post">
                    <input type="hidden" name="action" value="move" />
                    <input type="hidden" name="id" value="<?= (int)$t['id'] ?>" />
                    <input type="hidden" name="status" value="todo" />
                    <button class="btn btn-outline-secondary small-btn" <?= $key==='todo'?'disabled':'' ?>>TODO</button>
                  </form>
                  <form method="post">
                    <input type="hidden" name="action" value="move" />
                    <input type="hidden" name="id" value="<?= (int)$t['id'] ?>" />
                    <input type="hidden" name="status" value="doing" />
                    <button class="btn btn-outline-warning small-btn" <?= $key==='doing'?'disabled':'' ?>>DOING</button>
                  </form>
                  <form method="post">
                    <input type="hidden" name="action" value="move" />
                    <input type="hidden" name="id" value="<?= (int)$t['id'] ?>" />
                    <input type="hidden" name="status" value="done" />
                    <button class="btn btn-outline-success small-btn" <?= $key==='done'?'disabled':'' ?>>DONE</button>
                  </form>

                  <form method="post" onsubmit="return confirm('Usunąć zadanie?');">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id" value="<?= (int)$t['id'] ?>" />
                    <button class="btn btn-outline-danger small-btn">Usuń</button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>

            <?php if (empty($tasks[$key])): ?>
              <div class="text-muted">Brak zadań.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="text-muted small mt-3">
    Środowisko: <?= h(getenv('APP_ENV') ?: 'local') ?>
  </div>
</div>
</body>
</html>
