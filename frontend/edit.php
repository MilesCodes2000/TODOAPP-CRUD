<?php
// Frontend edit page: Handles Update (processing + UI), uses backend for DB
require_once '../backend/crud.php';  // Path to backend CRUD functions (loads $pdo)

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: index.php?error=No task selected');
    exit;
}

// Read: Fetch the task to edit
$task = getTaskById($pdo, $id);
if (!$task) {
    header('Location: index.php?error=Task not found');
    exit;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? null;
    $status = $_POST['status'] ?? $task['status'];

    if (!empty($title)) {
        if (updateTask($pdo, $id, $title, $description, $due_date, $status)) {
            header('Location: index.php?success=Task updated successfully!');
            exit;
        } else {
            $error = 'Failed to update task. Please try again.';
        }
    } else {
        $error = 'Title is required!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - Todo App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">  <!-- Path to assets -->
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Task</h1>
        <a href="index.php" class="btn btn-secondary mb-3">Back to Tasks</a>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" id="editForm">
            <input type="hidden" name="id" value="<?= $task['id'] ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Title *</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($task['title']) ?>" required maxlength="100">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($task['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" value="<?= $task['due_date'] ?>">
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="pending" <?= $task['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="completed" <?= $task['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Task</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>  <!-- Path to assets -->
</body>
</html>
