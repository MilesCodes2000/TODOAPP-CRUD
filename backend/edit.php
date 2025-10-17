<?php
// Backend handler for updating tasks (called via form or AJAX)
require_once 'crud.php';  // Loads $pdo and functions

header('Content-Type: application/json');  // For AJAX responses

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? $_GET['id'] ?? 0;
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'No task ID provided']);
        exit;
    }

    $task = getTaskById($pdo, $id);
    if (!$task) {
        echo json_encode(['success' => false, 'error' => 'Task not found']);
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? null;
    $status = $_POST['status'] ?? $task['status'];

    if (empty($title)) {
        echo json_encode(['success' => false, 'error' => 'Title is required']);
        exit;
    }

    if (updateTask($pdo, $id, $title, $description, $due_date, $status)) {
        echo json_encode(['success' => true, 'message' => 'Task updated']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Update failed']);
    }
} else {
    // For GET: Redirect to frontend edit UI
    $id = $_GET['id'] ?? 0;
    if ($id) {
        header('Location: ../frontend/edit.php?id=' . $id);
    } else {
        header('Location: ../frontend/index.php?error=No task selected');
    }
    exit;
}
?>
