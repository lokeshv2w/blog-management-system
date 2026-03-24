<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (!isset($_GET['id'])) {
    redirect('index.php');
}

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $comment_content = clean_input($_POST['content']);
    
    if (empty($name) || empty($email) || empty($comment_content)) {
        set_flash_message('danger', 'All fields are required.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash_message('danger', 'Invalid email format.');
    } else {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, name, email, content) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$id, $name, $email, $comment_content])) {
            set_flash_message('success', 'Your comment has been submitted and is awaiting moderation.');
            redirect("post.php?id=$id#comments");
        } else {
            set_flash_message('danger', 'Error submitting comment.');
        }
    }
}

// Fetch approved comments
$stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? AND status = 'approved' ORDER BY created_at ASC");
$stmt->execute([$id]);
$comments = $stmt->fetchAll();

// Fetch post
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category, c.slug as cat_slug, u.username as author 
    FROM posts p 
    LEFT JOIN categories c ON p.category_id = c.id 
    LEFT JOIN users u ON p.user_id = u.id 
    WHERE p.id = ? AND p.status = 'published'
");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    redirect('index.php');
}

$page_title = $post['title'];
require_once __DIR__ . '/includes/header.php';
?>

<article class="single-post">
    <div class="container">
        <header class="single-post-header">
            <?php if ($post['category']): ?>
                <a href="index.php?category=<?php echo $post['cat_slug']; ?>" class="post-category" style="font-size: 1rem; padding: 0.5rem 1rem;"><?php echo htmlspecialchars($post['category']); ?></a>
            <?php endif; ?>
            
            <h1 class="single-post-title text-gradient"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="single-post-meta">
                <div class="author-info">
                    <div class="author-avatar"><i class="fas fa-user"></i></div>
                    <span>By <?php echo htmlspecialchars($post['author']); ?></span>
                </div>
                <span>&bull;</span>
                <span><i class="far fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
            </div>
        </header>
        
        <?php if ($post['image']): ?>
            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="single-post-image">
        <?php endif; ?>
        
        <div class="single-post-content">
            <!-- Content styled using typography utilities -->
            <?php echo $post['content']; ?>
        </div>
        
        <!-- Comments Section -->
        <div id="comments" style="max-width: 800px; margin: 4rem auto 0; padding-top: 2rem; border-top: 1px solid var(--border);">
            <h3>Comments (<?php echo count($comments); ?>)</h3>
            
            <?php echo get_flash_message(); ?>
            
            <?php if (empty($comments)): ?>
                <p style="color: var(--text-muted); margin-bottom: 2rem;">No comments yet. Be the first to share your thoughts!</p>
            <?php else: ?>
                <div class="comments-list" style="margin-bottom: 3rem;">
                    <?php foreach($comments as $comment): ?>
                        <div class="comment" style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; border: 1px solid var(--border);">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                <div style="width: 40px; height: 40px; background: var(--bg-main); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--text-muted);"><i class="fas fa-user" style="font-size: 1rem;"></i></div>
                                <div>
                                    <h5 style="margin: 0; font-size: 1.1rem;"><?php echo htmlspecialchars($comment['name']); ?></h5>
                                    <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo date('M j, Y \a\t g:i a', strtotime($comment['created_at'])); ?></span>
                                </div>
                            </div>
                            <p style="margin: 0; color: var(--text-main); white-space: pre-line;"><?php echo htmlspecialchars($comment['content']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Comment Form -->
            <div class="comment-form" style="background: var(--bg-card); padding: 2rem; border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
                <h4 style="margin-bottom: 1.5rem;">Leave a Comment</h4>
                <form method="POST" action="post.php?id=<?php echo $id; ?>#comments">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <input type="text" name="name" class="form-control" placeholder="Your Name *" required>
                        <input type="email" name="email" class="form-control" placeholder="Your Email *" required>
                    </div>
                    <div style="margin-bottom: 1.5rem;">
                        <textarea name="content" class="form-control" rows="5" placeholder="Share your thoughts..." required></textarea>
                    </div>
                    <button type="submit" name="submit_comment" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Post Comment</button>
                </form>
            </div>
        </div>

        <div style="max-width: 800px; margin: 4rem auto 0; padding-top: 2rem; border-top: 1px solid var(--border); text-align: center;">
            <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to the Blog</a>
        </div>
    </div>
</article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
