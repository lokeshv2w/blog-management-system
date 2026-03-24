<?php
require_once __DIR__ . '/includes/header.php';

if (!is_logged_in() || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    redirect('index.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM pages WHERE id = ?");
    if ($stmt->execute([$id])) {
        set_flash_message('success', 'Page deleted successfully.');
    } else {
        set_flash_message('danger', 'Error deleting page.');
    }
    redirect('pages.php');
}

// Fetch all pages
$stmt = $pdo->query("SELECT * FROM pages ORDER BY created_at DESC");
$pages = $stmt->fetchAll();
?>

<div class="dashboard-header card-header" style="margin-bottom: 2rem; background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between;">
    <div>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Custom Pages</h1>
        <p style="color: var(--text-muted);">Manage your standalone website pages like About Us or Contact.</p>
    </div>
    <a href="add-page.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Page</a>
</div>

<div class="card">
    <div class="card-body" style="overflow-x: auto;">
        <?php echo get_flash_message(); ?>
        
        <?php if(empty($pages)): ?>
            <p>No pages found. <a href="add-page.php">Create one now</a>.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Slug / URL</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pages as $page): ?>
                    <tr>
                        <td><?php echo $page['id']; ?></td>
                        <td style="font-weight: 500;"><?php echo htmlspecialchars($page['title']); ?></td>
                        <td><span style="background: #F3F4F6; padding: 0.25rem 0.5rem; border-radius: 4px; font-family: monospace; font-size: 0.85rem;"><?php echo htmlspecialchars($page['slug']); ?></span></td>
                        <td>
                            <span class="badge badge-<?php echo $page['status'] == 'published' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($page['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($page['created_at'])); ?></td>
                        <td>
                            <?php if ($page['status'] == 'published'): ?>
                                <a href="../page.php?slug=<?php echo htmlspecialchars($page['slug']); ?>" target="_blank" class="action-btn text-success" title="View"><i class="fas fa-eye"></i></a>
                            <?php endif; ?>
                            <a href="edit-page.php?id=<?php echo $page['id']; ?>" class="action-btn text-info" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="pages.php?delete=<?php echo $page['id']; ?>" class="action-btn text-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this page?');"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
