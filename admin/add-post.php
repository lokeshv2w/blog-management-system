<?php
require_once __DIR__ . '/includes/header.php';

// Fetch categories for the dropdown
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title']);
    $slug = slugify($title);
    $content = $_POST['content']; 
    $category_id = empty($_POST['category_id']) ? null : (int)$_POST['category_id'];
    $status = $_POST['status'] === 'published' ? 'published' : 'draft';
    $user_id = $_SESSION['user_id'];
    
    $image_path = null;

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
                    $image_path = 'uploads/' . $new_filename;
                } else {
                    set_flash_message('danger', 'Failed to move uploaded image.');
                }
            } else {
                set_flash_message('danger', 'Invalid image format. Allowed: JPG, PNG, GIF, WEBP.');
            }
        }
        
        // If there's no error set by image upload
        if (!isset($_SESSION['flash_message']) || $_SESSION['flash_message']['type'] !== 'danger') {
            $stmt = $pdo->prepare("INSERT INTO posts (title, slug, content, image, category_id, user_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            try {
                if ($stmt->execute([$title, $slug, $content, $image_path, $category_id, $user_id, $status])) {
                    set_flash_message('success', 'Post created successfully.');
                    redirect('posts.php');
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    set_flash_message('danger', 'A post with this slug already exists. Please change the title.');
                } else {
                    set_flash_message('danger', 'Error creating post.');
                }
            }
        }
    }
}
?>

<div class="dashboard-header card-header" style="margin-bottom: 2rem; background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between;">
    <div>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Add New Post</h1>
    </div>
    <a href="posts.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Posts</a>
</div>

<div class="card" style="border: none; background: transparent; box-shadow: none;">
    <form method="POST" action="add-post.php" enctype="multipart/form-data">
        <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
            
            <!-- Left Column: Main Content -->
            <div class="card" style="flex: 2; min-width: 400px; padding: 2rem; margin-bottom: 0;">
                <div class="form-group">
                    <label for="title" style="font-size: 1.1rem;">Post Title</label>
                    <input type="text" id="title" name="title" class="form-control" placeholder="Enter a descriptive title..." required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" style="font-size: 1.25rem; padding: 0.75rem 1rem;">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="content" style="font-size: 1.1rem;">Content</label>
                    <textarea id="content" name="content" class="rich-editor"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
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
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id" class="form-control" style="background: white;">
                                <option value="">-- No Category --</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
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
                        <div class="form-group" style="margin-bottom: 0;">
                            <div style="border: 2px dashed var(--border); border-radius: var(--radius); padding: 1.5rem; text-align: center; background: #F9FAFB;">
                                <i class="fas fa-cloud-upload-alt text-muted" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">Upload a feature image for your post.</p>
                                <input type="file" id="image" name="image" class="form-control" accept="image/*" style="padding: 0.4rem; background: white; border: 1px solid var(--border);">
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.5rem; padding: 1rem; font-size: 1.1rem; box-shadow: var(--shadow-md);">
                    <i class="fas fa-paper-plane"></i> Save Post
                </button>
            </div>
            
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
