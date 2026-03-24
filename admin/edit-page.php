<?php
require_once __DIR__ . '/includes/header.php';

if (!is_logged_in() || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    redirect('index.php');
}

if (!isset($_GET['id'])) {
    redirect('pages.php');
}

$id = (int)$_GET['id'];

// Fetch the existing page
$stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
$stmt->execute([$id]);
$page = $stmt->fetch();

if (!$page) {
    set_flash_message('danger', 'Page not found.');
    redirect('pages.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = clean_input($_POST['title']);
    $content = $_POST['content'];
    $status = clean_input($_POST['status']);
    $meta_description = clean_input($_POST['meta_description']);
    $slug = !empty($_POST['slug']) ? slugify($_POST['slug']) : slugify($title);
    
    // Check if slug is taken by another page
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $id]);
    if ($stmt->fetchColumn() > 0) {
        $slug = $slug . '-' . time();
    }
    
    $image = $page['featured_image'];
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $image = slugify($title) . '-hero-' . time() . '.' . $ext;
            if (!file_exists('../assets/images/uploads/')) mkdir('../assets/images/uploads/', 0777, true);
            move_uploaded_file($_FILES['featured_image']['tmp_name'], '../assets/images/uploads/' . $image);
            
            // Delete old image
            if (!empty($page['featured_image']) && file_exists('../assets/images/uploads/' . $page['featured_image'])) {
                @unlink('../assets/images/uploads/' . $page['featured_image']);
            }
        }
    }
    
    if (empty($title) || empty($content)) {
        set_flash_message('danger', 'Title and Content are required.');
    } else {
        $stmt = $pdo->prepare("UPDATE pages SET title = ?, slug = ?, content = ?, status = ?, featured_image = ?, meta_description = ? WHERE id = ?");
        if ($stmt->execute([$title, $slug, $content, $status, $image, $meta_description, $id])) {
            set_flash_message('success', 'Page updated successfully.');
            redirect("edit-page.php?id=$id");
        } else {
            set_flash_message('danger', 'Error updating page.');
        }
    }
}
?>

<div class="dashboard-header card-header" style="margin-bottom: 2rem; background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between;">
    <div>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Edit Page</h1>
        <p style="color: var(--text-muted);">Make advanced changes to your custom page.</p>
    </div>
    <a href="pages.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Pages List</a>
</div>

<div class="card" style="border: none; background: transparent; box-shadow: none;">
    <?php echo get_flash_message(); ?>
    <form action="edit-page.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
            
            <!-- Left Column: Main Content -->
            <div class="card" style="flex: 2; min-width: 400px; padding: 2rem; margin-bottom: 0;">
                <div class="form-group">
                    <label for="title" style="font-size: 1.1rem;">Page Title *</label>
                    <input type="text" name="title" id="title" class="form-control" required value="<?php echo htmlspecialchars($page['title']); ?>" style="font-size: 1.25rem; padding: 0.75rem 1rem;">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="content" style="font-size: 1.1rem;">Content *</label>
                    <textarea name="content" class="rich-editor"><?php echo htmlspecialchars($page['content']); ?></textarea>
                </div>
            </div>
            
            <!-- Right Column: Meta & Settings -->
            <div style="flex: 1; min-width: 300px;">
                
                <!-- Publish Settings -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-header" style="background: #F9FAFB; border-bottom: 1px solid var(--border);">
                        <h3><i class="fas fa-cog text-muted"></i> Publish Settings</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" style="background: white;">
                                <option value="draft" <?php echo $page['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo $page['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Custom Slug</label>
                            <input type="text" name="slug" id="slug" class="form-control" required value="<?php echo htmlspecialchars($page['slug']); ?>" style="background: white;">
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>SEO Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3" style="background: white;"><?php echo htmlspecialchars($page['meta_description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Featured Image -->
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-header" style="background: #F9FAFB; border-bottom: 1px solid var(--border);">
                        <h3><i class="fas fa-image text-muted"></i> Featured Hero Image</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group" style="margin-bottom: 0;">
                            <?php if (!empty($page['featured_image'])): ?>
                                <div style="margin-bottom: 1rem; text-align: center;">
                                    <img src="../assets/images/uploads/<?php echo htmlspecialchars($page['featured_image']); ?>" alt="Current Hero Image" style="max-height: 150px; border-radius: var(--radius-sm); border: 1px solid var(--border); max-width: 100%;">
                                </div>
                            <?php endif; ?>
                            
                            <div style="border: 2px dashed var(--border); border-radius: var(--radius); padding: 1.5rem; text-align: center; background: #F9FAFB;">
                                <i class="fas fa-upload text-muted" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>
                                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">Upload to replace image.</p>
                                <input type="file" name="featured_image" accept="image/*" class="form-control" style="padding: 0.4rem; background: white; border: 1px solid var(--border);">
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" style="padding: 1rem; font-size: 1.1rem; box-shadow: var(--shadow-md);">
                    <i class="fas fa-save"></i> Update Page
                </button>
                
            </div>
            
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
