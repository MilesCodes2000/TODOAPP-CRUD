<?php
require_once '../backend/crud.php';

$archivedTasks = getArchivedTasks($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Tasks - Advanced Todo App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <button id="themeToggle" class="theme-toggle">
        <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
        </svg>
    </button>

    <div class="container mt-5">
        <div class="main-header">
            <h1 class="main-title">Archived Tasks</h1>
            <a href="index.php" class="btn btn-secondary mt-3">
                <svg width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Back to Tasks
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Archived Tasks (<?= count($archivedTasks) ?>)</h5>
            </div>
            <ul class="list-group list-group-flush">
                <?php if (empty($archivedTasks)): ?>
                    <li class="list-group-item text-center text-muted py-5">
                        <svg width="64" height="64" fill="currentColor" class="mb-3 opacity-25" viewBox="0 0 16 16">
                            <path d="M0 2a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 12.5V5a1 1 0 0 1-1-1V2zm2 3v7.5A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5V5H2zm13-3H1v2h14V2zM5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                        <p class="mb-0">No archived tasks.</p>
                    </li>
                <?php else: ?>
                    <?php foreach ($archivedTasks as $task): ?>
                        <li class="list-group-item task-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <?= htmlspecialchars($task['title']) ?>
                                        <?php if ($task['category']): ?>
                                            <span class="category-badge ms-2"><?= htmlspecialchars($task['category']) ?></span>
                                        <?php endif; ?>
                                    </h6>
                                    <p class="mb-2 text-muted"><?= htmlspecialchars($task['description'] ?: 'No description') ?></p>
                                    <div class="d-flex gap-2 mb-2">
                                        <span class="priority-badge priority-<?= strtolower($task['priority'] ?? 'medium') ?>">
                                            <?= ucfirst($task['priority'] ?? 'medium') ?>
                                        </span>
                                        <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst($task['status']) ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        Archived: <?= date('M j, Y', strtotime($task['created_at'])) ?>
                                        <?php if ($task['due_date']): ?>
                                            | Due: <?= date('M j, Y', strtotime($task['due_date'])) ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="../backend/delete.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to permanently delete this task?')">Delete</a>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
