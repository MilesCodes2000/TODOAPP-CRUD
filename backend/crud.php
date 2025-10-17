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
function createTask($pdo, $title, $description = '', $due_date = null, $priority = 'medium', $category = null) {
    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, priority, category) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $description, $due_date, $priority, $category]);
}

// Update: Modify a task
function updateTask($pdo, $id, $title, $description = '', $due_date = null, $status = 'pending', $priority = 'medium', $category = null) {
    $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ?, status = ?, priority = ?, category = ? WHERE id = ?");
    return $stmt->execute([$title, $description, $due_date, $status, $priority, $category, $id]);
}

// Delete: Remove a task
function deleteTask($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    return $stmt->execute([$id]);
}

// Archive: Move task to archive
function archiveTask($pdo, $id) {
    $stmt = $pdo->prepare("UPDATE tasks SET archived = 1 WHERE id = ?");
    return $stmt->execute([$id]);
}

// Get archived tasks
function getArchivedTasks($pdo) {
    $stmt = $pdo->query("SELECT * FROM tasks WHERE archived = 1 ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get active tasks with filters
function getFilteredTasks($pdo, $search = '', $status = '', $priority = '', $category = '', $sortBy = 'created_at', $sortOrder = 'DESC') {
    $sql = "SELECT * FROM tasks WHERE archived = 0";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (title LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if (!empty($status)) {
        $sql .= " AND status = ?";
        $params[] = $status;
    }

    if (!empty($priority)) {
        $sql .= " AND priority = ?";
        $params[] = $priority;
    }

    if (!empty($category)) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }

    $allowedSorts = ['created_at', 'due_date', 'priority', 'title', 'status'];
    $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'created_at';
    $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

    if ($sortBy === 'priority') {
        $sql .= " ORDER BY FIELD(priority, 'urgent', 'high', 'medium', 'low'), created_at DESC";
    } else {
        $sql .= " ORDER BY $sortBy $sortOrder";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get task statistics
function getTaskStats($pdo) {
    $stats = [];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tasks WHERE archived = 0");
    $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as completed FROM tasks WHERE status = 'completed' AND archived = 0");
    $stats['completed'] = $stmt->fetch(PDO::FETCH_ASSOC)['completed'];

    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM tasks WHERE status = 'pending' AND archived = 0");
    $stats['pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];

    $stmt = $pdo->query("SELECT COUNT(*) as overdue FROM tasks WHERE due_date < CURDATE() AND status = 'pending' AND archived = 0");
    $stats['overdue'] = $stmt->fetch(PDO::FETCH_ASSOC)['overdue'];

    $stmt = $pdo->query("SELECT priority, COUNT(*) as count FROM tasks WHERE archived = 0 GROUP BY priority");
    $stats['by_priority'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM tasks WHERE archived = 0 AND category IS NOT NULL GROUP BY category");
    $stats['by_category'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $stats;
}

// Get all unique categories
function getCategories($pdo) {
    $stmt = $pdo->query("SELECT DISTINCT category FROM tasks WHERE category IS NOT NULL AND category != '' AND archived = 0 ORDER BY category");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Add note to task
function addTaskNote($pdo, $task_id, $note_text) {
    $stmt = $pdo->prepare("INSERT INTO task_notes (task_id, note_text) VALUES (?, ?)");
    return $stmt->execute([$task_id, $note_text]);
}

// Get notes for a task
function getTaskNotes($pdo, $task_id) {
    $stmt = $pdo->prepare("SELECT * FROM task_notes WHERE task_id = ? ORDER BY created_at DESC");
    $stmt->execute([$task_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Bulk update status
function bulkUpdateStatus($pdo, $ids, $status) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "UPDATE tasks SET status = ? WHERE id IN ($placeholders)";
    $params = array_merge([$status], $ids);
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

// Bulk delete
function bulkDelete($pdo, $ids) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "DELETE FROM tasks WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($ids);
}

// Bulk archive
function bulkArchive($pdo, $ids) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "UPDATE tasks SET archived = 1 WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($ids);
}
?>
