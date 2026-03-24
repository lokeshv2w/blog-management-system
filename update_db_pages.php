<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS pages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        content LONGTEXT NOT NULL,
        status ENUM('draft', 'published') DEFAULT 'draft',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    // Seed an initial "About" and "Contact" page if table is empty
    $count = $pdo->query("SELECT COUNT(*) FROM pages")->fetchColumn();
    if ($count == 0) {
        $about_content = "<h2>Welcome to DevBlog</h2><p>This is your new default About page. You can edit this from the Admin Panel.</p>";
        $contact_content = "<h2>Get in Touch</h2><p>Feel free to reach out to us for any business inquiries.</p>";
        
        $stmt = $pdo->prepare("INSERT INTO pages (title, slug, content, status) VALUES (?, ?, ?, ?)");
        $stmt->execute(['About Us', 'about-us', $about_content, 'published']);
        $stmt->execute(['Contact', 'contact', $contact_content, 'published']);
    }
    
    echo "<h3>&check; Table 'pages' created and seeded successfully! Go back to the Admin Panel.</h3>";

} catch (PDOException $e) {
    die("Database update failed: " . $e->getMessage());
}
?>
