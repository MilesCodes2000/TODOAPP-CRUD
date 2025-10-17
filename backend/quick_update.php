<?php
require_once 'crud.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'completed';

    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'Task ID is required']);
        exit;
    }

    $task = getTaskById($pdo, $id);
    if (!$task) {
        echo json_encode(['success' => false, 'error' => 'Task not found']);
        exit;
    }

    if (updateTask($pdo, $id, $task['title'], $task['description'], $task['due_date'], $status, $task['priority'] ?? 'medium', $task['category'])) {
        echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update task']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
