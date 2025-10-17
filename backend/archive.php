<?php
require_once 'crud.php';

$id = intval($_GET['id'] ?? 0);

if (!$id) {
    header('Location: ../frontend/index.php?error=No task selected');
    exit;
}

if (archiveTask($pdo, $id)) {
    header('Location: ../frontend/index.php?success=Task archived successfully!');
} else {
    header('Location: ../frontend/index.php?error=Failed to archive task');
}
exit;
?>
