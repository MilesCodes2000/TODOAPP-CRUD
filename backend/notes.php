<?php
require_once 'crud.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = intval($_POST['task_id'] ?? 0);
    $note_text = trim($_POST['note_text'] ?? '');

    if (!$task_id || empty($note_text)) {
        echo json_encode(['success' => false, 'error' => 'Task ID and note text are required']);
        exit;
    }

    if (addTaskNote($pdo, $task_id, $note_text)) {
        echo json_encode(['success' => true, 'message' => 'Note added successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add note']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $task_id = intval($_GET['task_id'] ?? 0);

    if (!$task_id) {
        echo json_encode(['success' => false, 'error' => 'Task ID is required']);
        exit;
    }

    $notes = getTaskNotes($pdo, $task_id);
    echo json_encode(['success' => true, 'notes' => $notes]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
