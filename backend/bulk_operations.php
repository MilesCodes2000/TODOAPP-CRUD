<?php
require_once 'crud.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $ids = $_POST['ids'] ?? [];

    if (empty($ids) || !is_array($ids)) {
        echo json_encode(['success' => false, 'error' => 'No tasks selected']);
        exit;
    }

    $ids = array_map('intval', $ids);

    switch ($action) {
        case 'complete':
            $result = bulkUpdateStatus($pdo, $ids, 'completed');
            echo json_encode(['success' => $result, 'message' => 'Tasks marked as completed']);
            break;

        case 'pending':
            $result = bulkUpdateStatus($pdo, $ids, 'pending');
            echo json_encode(['success' => $result, 'message' => 'Tasks marked as pending']);
            break;

        case 'delete':
            $result = bulkDelete($pdo, $ids);
            echo json_encode(['success' => $result, 'message' => 'Tasks deleted']);
            break;

        case 'archive':
            $result = bulkArchive($pdo, $ids);
            echo json_encode(['success' => $result, 'message' => 'Tasks archived']);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
