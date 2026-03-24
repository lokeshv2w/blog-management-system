<?php
require_once __DIR__ . '/includes/db.php';

try {
    // Drop existing tables for fresh setup (optional, comment out for production)
    $pdo->exec("DROP TABLE IF EXISTS posts");
    $pdo->exec("DROP TABLE IF EXISTS categories");
    $pdo->exec("DROP TABLE IF EXISTS users");

    // Create users table
    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'author') DEFAULT 'author',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'users' created successfully.<br>\n";

    // Create categories table
    $pdo->exec("CREATE TABLE categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'categories' created successfully.<br>\n";

    // Create posts table
    $pdo->exec("CREATE TABLE posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        content TEXT NOT NULL,
        image VARCHAR(255) DEFAULT NULL,
        category_id INT DEFAULT NULL,
        user_id INT NOT NULL,
        status ENUM('draft', 'published') DEFAULT 'draft',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "Table 'posts' created successfully.<br>\n";

    // Insert default admin user
    $adminPassword = password_hash('password', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
    $stmt->execute(['admin', 'admin@example.com', $adminPassword]);
    echo "Default admin user created: admin / password<br>\n";

    echo "Database setup complete! You can now <a href='admin/login.php'>login</a>.";

} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>
