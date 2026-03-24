<?php
require_once __DIR__ . '/includes/db.php';

try {
    // Create menus table mapping for nested mega menus
    $pdo->exec("CREATE TABLE IF NOT EXISTS menus (
        id INT AUTO_INCREMENT PRIMARY KEY,
        label VARCHAR(255) NOT NULL,
        url VARCHAR(255) NOT NULL,
        parent_id INT DEFAULT NULL,
        sort_order INT DEFAULT 0,
        is_mega_menu TINYINT(1) DEFAULT 0,
        FOREIGN KEY (parent_id) REFERENCES menus(id) ON DELETE CASCADE
    )");
    
    // Seed basic menu items if the table is freshly generated
    $count = $pdo->query("SELECT COUNT(*) FROM menus")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO menus (label, url, sort_order) VALUES ('Home', 'index.php', 1)");
        $pdo->exec("INSERT INTO menus (label, url, sort_order) VALUES ('All Posts', 'index.php', 2)");
        $pdo->exec("INSERT INTO menus (label, url, sort_order, is_mega_menu) VALUES ('Categories', '#', 3, 1)");
    }
    
    echo "<h3>&check; Table 'menus' checked/created successfully! Go back to the Admin Panel.</h3>";
} catch (PDOException $e) {
    die("Database update failed: " . $e->getMessage());
}
?>
