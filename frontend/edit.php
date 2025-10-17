<?php
require_once '../backend/crud.php';

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: index.php?error=No task selected');
    exit;
}

$task = getTaskById($pdo, $id);
if (!$task) {
    header('Location: index.php?error=Task not found');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? null;
    $status = $_POST['status'] ?? $task['status'];
    $priority = $_POST['priority'] ?? $task['priority'];
    $category = trim($_POST['category'] ?? '');

    if (!empty($title)) {
        if (updateTask($pdo, $id, $title, $description, $due_date, $status, $priority, $category)) {
            header('Location: index.php?success=Task updated successfully!');
            exit;
        } else {
            $error = 'Failed to update task. Please try again.';
        }
    } else {
        $error = 'Title is required!';
    }
}

$notes = getTaskNotes($pdo, $id);
$categories = getCategories($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - Advanced Todo App</title>
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
            <h1 class="main-title">Edit Task</h1>
            <a href="index.php" class="btn btn-secondary mt-3">
                <svg width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                </svg>
                Back to Tasks
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Task Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="editForm">
                            <input type="hidden" name="id" value="<?= $task['id'] ?>">

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="title" class="form-label">Title *</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required maxlength="100">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="pending" <?= $task['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="completed" <?= $task['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($task['description']) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select class="form-select" id="priority" name="priority">
                                        <option value="low" <?= ($task['priority'] ?? 'medium') === 'low' ? 'selected' : '' ?>>Low</option>
                                        <option value="medium" <?= ($task['priority'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Medium</option>
                                        <option value="high" <?= ($task['priority'] ?? 'medium') === 'high' ? 'selected' : '' ?>>High</option>
                                        <option value="urgent" <?= ($task['priority'] ?? 'medium') === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?= $task['due_date'] ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="category" name="category" value="<?= htmlspecialchars($task['category'] ?? '') ?>" list="categoryList" placeholder="e.g., Work, Personal">
                                    <datalist id="categoryList">
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= htmlspecialchars($cat) ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <svg width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                    </svg>
                                    Update Task
                                </button>
                                <a href="index.php" class="btn btn-secondary">Cancel</a>
                                <a href="../backend/archive.php?id=<?= $task['id'] ?>" class="btn btn-warning ms-auto">
                                    <svg width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                        <path d="M0 2a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 12.5V5a1 1 0 0 1-1-1V2zm2 3v7.5A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5V5H2zm13-3H1v2h14V2zM5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                                    </svg>
                                    Archive
                                </a>
                                <a href="../backend/delete.php?id=<?= $task['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this task?')">
                                    <svg width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                    </svg>
                                    Delete
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Task Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Current Priority:</strong>
                            <span class="priority-badge priority-<?= strtolower($task['priority'] ?? 'medium') ?> d-block mt-1">
                                <?= ucfirst($task['priority'] ?? 'medium') ?>
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Current Status:</strong>
                            <span class="badge bg-<?= $task['status'] === 'completed' ? 'success' : 'secondary' ?> d-block mt-1">
                                <?= ucfirst($task['status']) ?>
                            </span>
                        </div>
                        <?php if ($task['category']): ?>
                            <div class="mb-3">
                                <strong>Category:</strong>
                                <span class="category-badge d-block mt-1"><?= htmlspecialchars($task['category']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <strong>Created:</strong>
                            <p class="mb-0 text-muted"><?= date('M j, Y \a\t g:i A', strtotime($task['created_at'])) ?></p>
                        </div>
                        <?php if ($task['due_date']): ?>
                            <div class="mb-3">
                                <strong>Due Date:</strong>
                                <p class="mb-0 text-muted"><?= date('M j, Y', strtotime($task['due_date'])) ?></p>
                                <?php if (strtotime($task['due_date']) < time() && $task['status'] === 'pending'): ?>
                                    <small class="text-danger">Overdue</small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Notes</h5>
                    </div>
                    <div class="card-body">
                        <form id="noteForm" class="mb-3">
                            <input type="hidden" name="task_id" id="task_id_for_notes" value="<?= $task['id'] ?>">
                            <div class="mb-2">
                                <textarea name="note_text" class="form-control" rows="2" placeholder="Add a note..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100">Add Note</button>
                        </form>

                        <div id="notesContainer">
                            <?php if (empty($notes)): ?>
                                <p class="text-muted">No notes yet.</p>
                            <?php else: ?>
                                <?php foreach ($notes as $note): ?>
                                    <div class="note-item">
                                        <p class="mb-1"><?= htmlspecialchars($note['note_text']) ?></p>
                                        <small class="text-muted"><?= date('M j, Y g:i A', strtotime($note['created_at'])) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
