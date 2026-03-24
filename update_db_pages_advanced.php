<?php
require_once __DIR__ . '/includes/db.php';

try {
    // Add columns if they don't exist
    $pdo->exec("ALTER TABLE pages ADD COLUMN featured_image VARCHAR(255) DEFAULT NULL");
    echo "<h3>&check; Added featured_image column.</h3>";
} catch (PDOException $e) {
    echo "<h3>&check; Column featured_image already exists.</h3>";
}

try {
    $pdo->exec("ALTER TABLE pages ADD COLUMN meta_description TEXT DEFAULT NULL");
    echo "<h3>&check; Added meta_description column.</h3>";
} catch (PDOException $e) {
    echo "<h3>&check; Column meta_description already exists.</h3>";
}

echo "<br><a href='admin/pages.php'>Go back to Pages Management</a>";
?>
