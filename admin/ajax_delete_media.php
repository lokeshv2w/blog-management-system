<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_logged_in() || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filename'])) {
    // Basic sanitization
    $filename = basename($_POST['filename']);
    $filepath = __DIR__ . '/../assets/images/uploads/' . $filename;
    
    if (file_exists($filepath) && is_file($filepath)) {
        if (unlink($filepath)) {
            echo json_encode(['status' => 'success', 'message' => 'File deleted permanently.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete file from the server.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File not found on system.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request parameters.']);
}
?>
