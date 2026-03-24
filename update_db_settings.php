<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT DEFAULT NULL
    )");
    
    // Seed default settings
    $settings = [
        'site_title' => 'DevBlog',
        'site_description' => 'A modern, premium blog management system with dynamic animations and fluid typography.',
        'footer_text' => 'DevBlog CMS. All rights reserved.',
        'contact_email' => 'contact@example.com'
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($settings as $key => $val) {
        $stmt->execute([$key, $val]);
    }
    
    echo "<h3>&check; Settings table and default data initialized successfully. Go back to Admin Panel.</h3>";
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>
