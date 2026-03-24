<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $upload_dir = __DIR__ . '/../assets/images/uploads/';
    
    if (!file_exists($upload_dir)) {
        if(!mkdir($upload_dir, 0777, true)) {
           echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory.']);
           exit;
        }
    }
    
    $file = $_FILES['file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'Upload failed with error code ' . $file['error']]);
        exit;
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Allowed image formats
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    if (!in_array($ext, $allowed)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, GIF, WEBP, and SVG are allowed.']);
        exit;
    }
    
    // Create safe and unique filename
    $base = pathinfo($file['name'], PATHINFO_FILENAME);
    $safe_name = slugify($base) . '-' . uniqid() . '.' . $ext;
    $target_file = $upload_dir . $safe_name;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        echo json_encode([
            'status' => 'success', 
            'file' => [
                'name' => $safe_name,
                'url' => '../assets/images/uploads/' . $safe_name,
                'size' => filesize($target_file),
                'time_formatted' => date('M j, Y')
            ]
        ]);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file correctly to directory.']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file received by the server.']);
    exit;
}
?>
