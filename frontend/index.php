<?php
// Frontend main page: Handles Create and Read, uses backend for DB logic
require_once '../backend/crud.php';  // Path to backend CRUD functions (loads $pdo)

// Handle Create (Add Task)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? null;

    if (!empty($title)) {
        if (createTask($pdo, $title, $description, $due_date)) {
            header('Location: index.php?success=Task added successfully!');
            exit;
        } else {
            $error = 'Failed to add task. Please try again.';
        }
    } else {
        $error = 'Title is required!';
    }
}

// Read: Fetch all tasks
$tasks = getTasks($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo App - Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">  <!-- Path to assets -->
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">My Todo Tasks</h1>
        
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

        <!-- Create Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Add New Task</h5>
            </div>
            <div class="card-body">
                <form method="POST" id="taskForm">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </form>
            </div>
        </div>

        <!-- Read: Task List -->
        <div class="card">
            <div class="card-header">
                <h5>Task List (<?= count($tasks) ?> tasks)</h5>
            </div>
            <ul class="list-group list-group-flush">
                <?php if (empty($tasks)): ?>
                    <li class="list-group-item text-center text-muted">No tasks yet. Add one above!</li>
                <?php else: ?>
                    <?php foreach ($tasks as $task): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center task-item">
                            <div>
                                <h6 class="mb-1"><?= htmlspecialchars($task['title']) ?></h6>
                                <p class="mb-1 text-muted"><?= htmlspecialchars($task['description'] ?: 'No description') ?></p>
                                <small class="text-muted">
                                    Due: <?= $task['due_date'] ? date('M j, Y', strtotime($task['due_date'])) : 'No due date' ?> | 
                                    Status: <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' : 'secondary' ?>"><?= ucfirst($task['status']) ?></span> | 
                                    Created: <?= date('M j, Y', strtotime($task['created_at'])) ?>
                                </small>
                            </div>
                            <div class="btn-group" role="group">
                                <a href="edit.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="../backend/delete.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this task?')">Delete</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>  <!-- Path to assets -->
</body>
</html>
