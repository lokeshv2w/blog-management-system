<?php
require_once __DIR__ . '/includes/header.php';

$action = $_GET['action'] ?? 'list';

// Handle Action
if (isset($_GET['id']) && in_array($action, ['approve', 'delete'])) {
    $id = (int)$_GET['id'];
    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?");
        if ($stmt->execute([$id])) set_flash_message('success', 'Comment approved successfully.');
    } else {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        if ($stmt->execute([$id])) set_flash_message('success', 'Comment deleted successfully.');
    }
    redirect('comments.php');
}

// Fetch comments
$stmt = $pdo->query("
    SELECT c.*, p.title as post_title 
    FROM comments c 
    JOIN posts p ON c.post_id = p.id 
    ORDER BY c.status ASC, c.created_at DESC
");
$comments = $stmt->fetchAll();
?>

<div class="dashboard-header card-header" style="margin-bottom: 2rem; background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between;">
    <div>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Comments Moderation</h1>
        <p style="color: var(--text-muted);">Manage and moderate user comments across all posts.</p>
    </div>
</div>

<div class="card">
    <div class="card-body" style="overflow-x: auto;">
        <?php echo get_flash_message(); ?>
        
        <?php if(empty($comments)): ?>
            <p style="color: var(--text-muted); text-align: center; padding: 2rem;">No comments found.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Author Info</th>
                        <th>Comment Snippet</th>
                        <th>Post Reference</th>
                        <th>Status</th>
                        <th>Submitted On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($comments as $comment): ?>
                    <tr style="<?php echo $comment['status'] === 'pending' ? 'background-color: #FEF3C7;' : ''; ?>">
                        <td style="min-width: 150px;">
                            <strong><?php echo htmlspecialchars($comment['name']); ?></strong><br>
                            <small class="text-muted"><a href="mailto:<?php echo htmlspecialchars($comment['email']); ?>" style="color: inherit;"><?php echo htmlspecialchars($comment['email']); ?></a></small>
                        </td>
                        <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-main);" title="<?php echo htmlspecialchars($comment['content']); ?>">
                            <?php echo htmlspecialchars($comment['content']); ?>
                        </td>
                        <td style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <a href="../post.php?id=<?php echo $comment['post_id']; ?>#comments" target="_blank" class="text-primary font-weight-500">
                                <?php echo htmlspecialchars($comment['post_title']); ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $comment['status'] === 'approved' ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($comment['status']); ?>
                            </span>
                        </td>
                        <td>
                            <span style="color: var(--text-muted); font-size: 0.85rem;"><i class="far fa-calendar-alt"></i> <?php echo date('M j, Y g:i a', strtotime($comment['created_at'])); ?></span>
                        </td>
                        <td>
                            <?php if ($comment['status'] === 'pending'): ?>
                                <a href="comments.php?action=approve&id=<?php echo $comment['id']; ?>" class="action-btn text-success" title="Approve" style="margin-right: 0.5rem;"><i class="fas fa-check-circle" style="font-size: 1.25rem;"></i></a>
                            <?php endif; ?>
                            <a href="comments.php?action=delete&id=<?php echo $comment['id']; ?>" class="action-btn text-danger" title="Delete" onclick="return confirm('Are you sure you want to permanently delete this comment?');"><i class="fas fa-trash-alt" style="font-size: 1.25rem;"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
