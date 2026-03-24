<?php
require_once __DIR__ . '/includes/db.php';

try {
    // Create comments table
    $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        content TEXT NOT NULL,
        status ENUM('pending', 'approved') DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
    )");
    
    echo "<h3>&check; Table 'comments' created successfully!</h3>";
    echo "<a href='index.php'>Go to Homepage</a>";

} catch (PDOException $e) {
    die("Database update failed: " . $e->getMessage());
}
?>
