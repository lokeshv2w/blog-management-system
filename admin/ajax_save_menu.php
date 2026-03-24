<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Requires admin login
if (!is_logged_in() || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hierarchy'])) {
    $hierarchy = json_decode($_POST['hierarchy'], true);
    
    if (is_array($hierarchy)) {
        try {
            $pdo->beginTransaction();
            save_hierarchy($hierarchy, 0, null);
            $pdo->commit();
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
}

function save_hierarchy($items, $sort_order, $parent_id) {
    global $pdo;
    foreach ($items as $item) {
        $id = (int)$item['id'];
        
        $stmt = $pdo->prepare("UPDATE menus SET parent_id = ?, sort_order = ? WHERE id = ?");
        $stmt->execute([$parent_id, $sort_order, $id]);
        
        $sort_order++;
        
        if (isset($item['children']) && is_array($item['children'])) {
            save_hierarchy($item['children'], 0, $id);
        }
    }
}
?>
