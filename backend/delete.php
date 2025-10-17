<?php
// Backend handler for deleting tasks
require_once 'config.php';  // Loads $pdo

$id = $_GET['id'] ?? 0;
if (!$id) {
    header('Location: ../frontend/index.php?error=No task selected');
    exit;
}

// Delete the task
$stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
$stmt->execute([$id]);

if ($stmt->rowCount() > 0) {
    header('Location: ../frontend/index.php?success=Task deleted!');
} else {
    header('Location: ../frontend/index.php?error=Task not found or already deleted');
}
exit;
?>
