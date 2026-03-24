<?php
require_once __DIR__ . '/includes/header.php';

// Fetch some quick stats
$stats = [
    'posts' => $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn(),
    'categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
];

// Fetch recent posts
$stmt = $pdo->query("SELECT p.id, p.title, p.status, p.created_at, c.name as category 
                     FROM posts p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     ORDER BY p.created_at DESC LIMIT 5");
$recent_posts = $stmt->fetchAll();
?>

<div class="dashboard-header">
    <h1>Dashboard</h1>
    <p>Welcome to the DevBlog CMS Admin Panel.</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-details">
            <h3>Total Posts</h3>
            <span class="stat-number"><?php echo $stats['posts']; ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-tags"></i>
        </div>
        <div class="stat-details">
            <h3>Categories</h3>
            <span class="stat-number"><?php echo $stats['categories']; ?></span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-details">
            <h3>Users</h3>
            <span class="stat-number"><?php echo $stats['users']; ?></span>
        </div>
    </div>
</div>

<div class="recent-posts">
    <div class="card">
        <div class="card-header">
            <h3>Recent Posts</h3>
            <a href="posts.php" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="card-body">
            <?php if(empty($recent_posts)): ?>
                <p>No posts found. <a href="add-post.php">Create one now</a>.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_posts as $post): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><?php echo htmlspecialchars($post['category'] ?? 'Uncategorized'); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($post['created_at'])); ?></td>
                            <td>
                                <a href="edit-post.php?id=<?php echo $post['id']; ?>" class="action-btn text-info" title="Edit"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
