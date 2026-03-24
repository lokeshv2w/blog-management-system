<?php
require_once __DIR__ . '/includes/header.php';

if (!isset($_GET['id'])) {
    redirect('posts.php');
}
$id = (int)$_GET['id'];

// Fetch the post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    set_flash_message('danger', 'Post not found.');
    redirect('posts.php');
}

// Fetch categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title']);
    $slug = slugify($title);
    $content = $_POST['content']; 
    $category_id = empty($_POST['category_id']) ? null : (int)$_POST['category_id'];
    $status = $_POST['status'] === 'published' ? 'published' : 'draft';
    
    $image_path = $post['image']; // Keep old image by default

    if (empty($title) || empty($content)) {
        set_flash_message('danger', 'Title and content are required.');
    } else {
        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_ext, $allowed_exts)) {
                $new_filename = uniqid('post_') . '.' . $file_ext;
                $destination = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    // Delete old image if exists
                    if ($image_path && file_exists(__DIR__ . '/../' . $image_path)) {
                        unlink(__DIR__ . '/../' . $image_path);
                    }
                    $image_path = 'uploads/' . $new_filename;
                } else {
                    set_flash_message('danger', 'Failed to move uploaded image.');
                }
            } else {
                set_flash_message('danger', 'Invalid image format. Allowed: JPG, PNG, GIF, WEBP.');
            }
        }
        
        // Update Post
        if (!isset($_SESSION['flash_message']) || $_SESSION['flash_message']['type'] !== 'danger') {
            $stmt = $pdo->prepare("UPDATE posts SET title = ?, slug = ?, content = ?, image = ?, category_id = ?, status = ? WHERE id = ?");
            try {
                if ($stmt->execute([$title, $slug, $content, $image_path, $category_id, $status, $id])) {
                    set_flash_message('success', 'Post updated successfully.');
                    redirect('posts.php');
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    set_flash_message('danger', 'A post with this slug already exists. Please change the title.');
                } else {
                    set_flash_message('danger', 'Error updating post.');
                }
            }
        }
    }
}
?>

<div class="dashboard-header card-header" style="margin-bottom: 2rem; background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between;">
    <div>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Edit Post</h1>
    </div>
    <a href="posts.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Posts</a>
</div>

<div class="card" style="border: none; background: transparent; box-shadow: none;">
    <form method="POST" action="edit-post.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
        <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
            
            <!-- Left Column: Main Content -->
            <div class="card" style="flex: 2; min-width: 400px; padding: 2rem; margin-bottom: 0;">
                <div class="form-group">
                    <label for="title" style="font-size: 1.1rem;">Post Title</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="Enter a descriptive title..." required value="<?php echo htmlspecialchars($_POST['title'] ?? $post['title']); ?>" style="font-size: 1.25rem; padding: 0.75rem 1rem;">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="content" style="font-size: 1.1rem;">Content</label>
                    <textarea id="content" name="content" class="rich-editor"><?php echo htmlspecialchars($_POST['content'] ?? $post['content']); ?></textarea>
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
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control" style="background: white;">
                                <option value="draft" <?php echo ($post['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo ($post['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id" class="form-control" style="background: white;">
                                <option value="">-- No Category --</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($post['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="card">
                    <div class="card-header" style="background: #F9FAFB; border-bottom: 1px solid var(--border);">
                        <h3><i class="fas fa-image text-muted"></i> Featured Image</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($post['image']): ?>
                            <div style="margin-bottom: 1rem; text-align: center;">
                                <img src="../<?php echo htmlspecialchars($post['image']); ?>" alt="Current Image" style="max-width: 100%; border-radius: var(--radius); border: 1px solid var(--border);">
                                <small style="display: block; margin-top: 0.5rem; color: var(--text-muted);">Current Image</small>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Replace Image</label>
                            <input type="file" id="image" name="image" class="form-control" accept="image/*" style="padding: 0.4rem; background: white; border: 1px solid var(--border);">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.5rem; padding: 1rem; font-size: 1.1rem; box-shadow: var(--shadow-md);">
                    <i class="fas fa-save"></i> Update Post
                </button>
            </div>
            
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
