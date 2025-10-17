<?php
require_once '../backend/crud.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? null;
    $priority = $_POST['priority'] ?? 'medium';
    $category = trim($_POST['category'] ?? '');

    if (!empty($title)) {
        if (createTask($pdo, $title, $description, $due_date, $priority, $category)) {
            header('Location: index.php?success=Task added successfully!');
            exit;
        } else {
            $error = 'Failed to add task. Please try again.';
        }
    } else {
        $error = 'Title is required!';
    }
}

$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$priorityFilter = $_GET['priority'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
$sortBy = $_GET['sort'] ?? 'created_at';
$sortOrder = $_GET['order'] ?? 'DESC';

$tasks = getFilteredTasks($pdo, $search, $statusFilter, $priorityFilter, $categoryFilter, $sortBy, $sortOrder);
$stats = getTaskStats($pdo);
$categories = getCategories($pdo);

$hasFilters = !empty($search) || !empty($statusFilter) || !empty($priorityFilter) || !empty($categoryFilter);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Todo App - Task Manager</title>
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
        <div class="main-header text-center">
            <h1 class="main-title">Advanced Todo Manager</h1>
            <p class="text-muted mb-0">Organize your tasks with powerful features</p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value text-primary"><?= $stats['total'] ?></div>
                <div class="stat-label">Total Tasks</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-success"><?= $stats['completed'] ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-warning"><?= $stats['pending'] ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-danger"><?= $stats['overdue'] ?></div>
                <div class="stat-label">Overdue</div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add New Task</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="taskForm">
                            <input type="hidden" name="action" value="create">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="title" class="form-label">Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" required maxlength="100">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select class="form-select" id="priority" name="priority">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="date" class="form-control" id="due_date" name="due_date">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="category" name="category" list="categoryList" placeholder="e.g., Work, Personal">
                                    <datalist id="categoryList">
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= htmlspecialchars($cat) ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <svg width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg>
                                Add Task
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <h6>By Priority</h6>
                        <?php foreach ($stats['by_priority'] as $priority): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="priority-badge priority-<?= strtolower($priority['priority']) ?>">
                                    <?= ucfirst($priority['priority']) ?>
                                </span>
                                <strong><?= $priority['count'] ?></strong>
                            </div>
                        <?php endforeach; ?>

                        <?php if (!empty($stats['by_category'])): ?>
                            <h6 class="mt-3">By Category</h6>
                            <?php foreach ($stats['by_category'] as $cat): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="category-badge"><?= htmlspecialchars($cat['category']) ?></span>
                                    <strong><?= $cat['count'] ?></strong>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="mt-3">
                            <a href="../backend/export.php?format=csv" class="btn btn-sm btn-outline-primary w-100 mb-2">
                                Export CSV
                            </a>
                            <a href="../backend/export.php?format=json" class="btn btn-sm btn-outline-primary w-100 mb-2">
                                Export JSON
                            </a>
                            <a href="archived.php" class="btn btn-sm btn-outline-secondary w-100">
                                View Archived
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" id="filterForm" class="mb-0">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search tasks..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="">All</option>
                            <option value="urgent" <?= $priorityFilter === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                            <option value="high" <?= $priorityFilter === 'high' ? 'selected' : '' ?>>High</option>
                            <option value="medium" <?= $priorityFilter === 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="low" <?= $priorityFilter === 'low' ? 'selected' : '' ?>>Low</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" <?= $categoryFilter === $cat ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sort By</label>
                        <select name="sort" class="form-select">
                            <option value="created_at" <?= $sortBy === 'created_at' ? 'selected' : '' ?>>Date Created</option>
                            <option value="due_date" <?= $sortBy === 'due_date' ? 'selected' : '' ?>>Due Date</option>
                            <option value="priority" <?= $sortBy === 'priority' ? 'selected' : '' ?>>Priority</option>
                            <option value="title" <?= $sortBy === 'title' ? 'selected' : '' ?>>Title</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <?php if ($hasFilters): ?>
                            <button type="button" id="clearFilters" class="btn btn-outline-secondary w-100">Clear</button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Task List (<?= count($tasks) ?> tasks)
                    <?php if ($hasFilters): ?>
                        <small class="text-muted">- Filtered</small>
                    <?php endif; ?>
                </h5>
                <?php if (!empty($tasks)): ?>
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                        <label for="selectAll" class="form-check-label ms-1">Select All</label>
                    </div>
                <?php endif; ?>
            </div>
            <ul class="list-group list-group-flush">
                <?php if (empty($tasks)): ?>
                    <li class="list-group-item text-center text-muted py-5">
                        <svg width="64" height="64" fill="currentColor" class="mb-3 opacity-25" viewBox="0 0 16 16">
                            <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                            <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319z"/>
                        </svg>
                        <p class="mb-0">
                            <?= $hasFilters ? 'No tasks match your filters.' : 'No tasks yet. Add one above!' ?>
                        </p>
                    </li>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                        <?php
                        $isOverdue = $task['due_date'] && strtotime($task['due_date']) < time() && $task['status'] === 'pending';
                        ?>
                        <li class="list-group-item task-item <?= $isOverdue ? 'overdue' : '' ?>">
                            <div class="d-flex">
                                <div class="checkbox-wrapper">
                                    <input type="checkbox" class="form-check-input task-checkbox" value="<?= $task['id'] ?>">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-1">
                                            <?= htmlspecialchars($task['title']) ?>
                                            <?php if ($task['category']): ?>
                                                <span class="category-badge ms-2"><?= htmlspecialchars($task['category']) ?></span>
                                            <?php endif; ?>
                                        </h6>
                                        <div class="d-flex gap-2">
                                            <span class="priority-badge priority-<?= strtolower($task['priority'] ?? 'medium') ?>">
                                                <?= ucfirst($task['priority'] ?? 'medium') ?>
                                            </span>
                                            <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' : 'secondary' ?>">
                                                <?= ucfirst($task['status']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <p class="mb-2 text-muted"><?= htmlspecialchars($task['description'] ?: 'No description') ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <?php if ($task['due_date']): ?>
                                                <strong class="<?= $isOverdue ? 'text-danger' : '' ?>">
                                                    Due: <?= date('M j, Y', strtotime($task['due_date'])) ?>
                                                    <?= $isOverdue ? '(Overdue)' : '' ?>
                                                </strong> |
                                            <?php endif; ?>
                                            Created: <?= date('M j, Y', strtotime($task['created_at'])) ?>
                                        </small>
                                        <div class="btn-group" role="group">
                                            <?php if ($task['status'] === 'pending'): ?>
                                                <button onclick="quickComplete(<?= $task['id'] ?>)" class="btn btn-sm btn-success">
                                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                                                    </svg>
                                                </button>
                                            <?php endif; ?>
                                            <a href="edit.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="../backend/archive.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-warning">Archive</a>
                                            <a href="../backend/delete.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this task?')">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div id="bulkActions" class="bulk-actions">
        <div class="mb-2">
            <strong><span id="selectedCount">0</span> tasks selected</strong>
        </div>
        <div class="btn-group-vertical w-100">
            <button onclick="executeBulkAction('complete')" class="btn btn-sm btn-success">Mark as Complete</button>
            <button onclick="executeBulkAction('pending')" class="btn btn-sm btn-warning">Mark as Pending</button>
            <button onclick="executeBulkAction('archive')" class="btn btn-sm btn-secondary">Archive</button>
            <button onclick="executeBulkAction('delete')" class="btn btn-sm btn-danger">Delete</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
