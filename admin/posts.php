<?php
require_once __DIR__ . '/includes/header.php';

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // Optional: Get image path to delete it
    $stmt = $pdo->prepare("SELECT image FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    if ($stmt->execute([$id])) {
        if ($post && $post['image'] && file_exists(__DIR__ . '/../' . $post['image'])) {
            unlink(__DIR__ . '/../' . $post['image']);
        }
        set_flash_message('success', 'Post deleted successfully.');
    } else {
        set_flash_message('danger', 'Error deleting post.');
    }
    redirect('posts.php');
}

// Fetch total count for pagination
$total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$pagination = get_pagination_data($total_posts, 10);

// Fetch posts with limit and offset
$stmt = $pdo->prepare("SELECT p.id, p.title, p.status, p.created_at, c.name as category, u.username as author 
                     FROM posts p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     LEFT JOIN users u ON p.user_id = u.id 
                     ORDER BY p.created_at DESC 
                     LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', (int)$pagination['limit'], PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$pagination['offset'], PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();
?>

<div class="dashboard-header card-header" style="margin-bottom: 2rem; background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between;">
    <div>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Posts</h1>
        <p style="color: var(--text-muted);">Manage your blog posts.</p>
    </div>
    <a href="add-post.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Post</a>
</div>

<div class="card">
    <div class="card-body" style="overflow-x: auto;">
        <?php if(empty($posts)): ?>
            <p>No posts found. <a href="add-post.php" class="text-primary">Create your first post</a>.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($posts as $post): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($post['title']); ?></strong></td>
                        <td><div style="display: flex; align-items: center; gap: 0.5rem;"><div style="width: 24px; height: 24px; background: #E5E7EB; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem;"><i class="fas fa-user text-muted"></i></div> <?php echo htmlspecialchars($post['author']); ?></div></td>
                        <td><span class="badge badge-info" style="background:#DBEAFE; color:#1E40AF;"><?php echo htmlspecialchars($post['category'] ?? 'Uncategorized'); ?></span></td>
                        <td>
                            <span class="badge badge-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                <i class="fas fa-circle" style="font-size: 0.5rem; margin-right: 0.25rem;"></i> <?php echo ucfirst($post['status']); ?>
                            </span>
                        </td>
                        <td><span style="color: var(--text-muted); font-size: 0.85rem;"><i class="far fa-calendar-alt"></i> <?php echo date('M j, Y', strtotime($post['created_at'])); ?></span></td>
                        <td>
                            <a href="../post.php?id=<?php echo $post['id']; ?>" class="action-btn text-success" title="View" target="_blank"><i class="fas fa-eye"></i></a>
                            <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="action-btn text-info" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="posts.php?action=delete&id=<?php echo $post['id']; ?>" class="action-btn text-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this post?');"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'posts.php'); ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
