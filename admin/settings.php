<?php
require_once __DIR__ . '/includes/header.php';

// Only admins can see this
if (!is_logged_in() || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_title = clean_input($_POST['site_title']);
    $site_description = clean_input($_POST['site_description']);
    $footer_text = clean_input($_POST['footer_text']);
    $contact_email = clean_input($_POST['contact_email']);
    
    $settings_data = [
        'site_title' => $site_title,
        'site_description' => $site_description,
        'footer_text' => $footer_text,
        'contact_email' => $contact_email
    ];
    
    try {
        $pdo->beginTransaction();
        
        foreach ($settings_data as $key => $val) {
            // Upsert technique
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$val, $key]);
            
            if ($stmt->rowCount() == 0) {
                // If it didn't exist/was zero affected (same value), ensure it is inserted if not present
                $insertStmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
                $insertStmt->execute([$key, $val]);
            }
        }
        
        $pdo->commit();
        set_flash_message('success', 'Global settings updated successfully.');
    } catch (PDOException $e) {
        $pdo->rollBack();
        set_flash_message('danger', 'Error updating settings.');
    }
    
    redirect('settings.php');
}

// Fetch current
$stmt = $pdo->query("SELECT * FROM settings");
$current_settings = [];
if ($stmt) {
    while ($row = $stmt->fetch()) {
        $current_settings[$row['setting_key']] = $row['setting_value'];
    }
}
?>

<div class="dashboard-header card-header" style="margin-bottom: 2rem; background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow-sm);">
    <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Global Settings</h1>
    <p style="color: var(--text-muted);">Manage your website's main configurations.</p>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <?php echo get_flash_message(); ?>
        
        <form method="POST" action="settings.php">
            <div class="form-group">
                <label>Site Title</label>
                <input type="text" name="site_title" class="form-control" required value="<?php echo htmlspecialchars($current_settings['site_title'] ?? 'DevBlog'); ?>">
            </div>
            
            <div class="form-group">
                <label>Contact Email</label>
                <input type="email" name="contact_email" class="form-control" required value="<?php echo htmlspecialchars($current_settings['contact_email'] ?? 'contact@example.com'); ?>">
            </div>
            
            <div class="form-group">
                <label>Site Description (Used in Footer & Hero)</label>
                <textarea name="site_description" class="form-control" rows="3" required><?php echo htmlspecialchars($current_settings['site_description'] ?? 'A modern, premium blog management system.'); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Footer Copyright Text</label>
                <input type="text" name="footer_text" class="form-control" required value="<?php echo htmlspecialchars($current_settings['footer_text'] ?? 'DevBlog CMS. All rights reserved.'); ?>">
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
