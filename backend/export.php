<?php
require_once 'crud.php';

$format = $_GET['format'] ?? 'csv';
$tasks = getTasks($pdo);

if ($format === 'json') {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="tasks_export_' . date('Y-m-d') . '.json"');
    echo json_encode($tasks, JSON_PRETTY_PRINT);
} else {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="tasks_export_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['ID', 'Title', 'Description', 'Due Date', 'Status', 'Priority', 'Category', 'Created At']);

    foreach ($tasks as $task) {
        fputcsv($output, [
            $task['id'],
            $task['title'],
            $task['description'],
            $task['due_date'],
            $task['status'],
            $task['priority'] ?? 'medium',
            $task['category'] ?? '',
            $task['created_at']
        ]);
    }

    fclose($output);
}
exit;
?>
