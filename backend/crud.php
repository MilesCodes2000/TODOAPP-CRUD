<?php
// Shared CRUD functions to avoid code duplication. Include this in other PHP files.
require_once 'config.php';  // Loads $pdo

// Read: Fetch all tasks
function getTasks($pdo) {
    $stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Read: Fetch a single task by ID
function getTaskById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Create: Add a new task
function createTask($pdo, $title, $description = '', $due_date = null) {
    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date) VALUES (?, ?, ?)");
    return $stmt->execute([$title, $description, $due_date]);
}

// Update: Modify a task
function updateTask($pdo, $id, $title, $description = '', $due_date = null, $status = 'pending') {
    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ?, status = ? WHERE id = ?");
    return $stmt->execute([$title, $description, $due_date, $status, $id]);
}

// Delete: Remove a task
function deleteTask($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    return $stmt->execute([$id]);
}
?>
