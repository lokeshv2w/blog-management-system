<?php
$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';

// Check for category filter
$category_slug = $_GET['category'] ?? '';
$where_clause = "WHERE p.status = 'published'";
$params = [];

$category_name = '';
if ($category_slug) {
    // Find category ID
    $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE slug = ?");
    $stmt->execute([$category_slug]);
    $cat = $stmt->fetch();
    
    if ($cat) {
        $where_clause .= " AND p.category_id = ?";
        $params[] = $cat['id'];
        $category_name = $cat['name'];
        $page_title = $cat['name'] . ' - DevBlog';
    }
}

// Fetch total count for pagination
$count_sql = "SELECT COUNT(*) FROM posts p $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_posts = $stmt->fetchColumn();

$pagination = get_pagination_data($total_posts, 9); // Show 9 posts per page (3x3 grid)

// Fetch published posts with limit and offset
$sql = "SELECT p.id, p.title, p.slug, p.content, p.image, p.created_at, c.name as category, c.slug as cat_slug, u.username as author 
        FROM posts p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN users u ON p.user_id = u.id 
        $where_clause 
        ORDER BY p.created_at DESC 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
// Re-bind params if any (from categories)
foreach ($params as $key => $val) {
    $stmt->bindValue($key + 1, $val);
}
// Bind pagination params
$stmt->bindValue(':limit', (int)$pagination['limit'], PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$pagination['offset'], PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-pattern"></div>
    <div class="container hero-content">
        <?php if ($category_name): ?>
            <h1>Posts about <span class="text-gradient"><?php echo htmlspecialchars($category_name); ?></span></h1>
            <p>Explore all the latest articles and tutorials in this category.</p>
        <?php else: ?>
            <h1>Welcome to <span class="text-gradient">DevBlog</span></h1>
            <p>Discover the latest articles, tutorials, and insights on modern web development.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Blog Posts -->
<section class="container" style="min-height: 40vh;">
    <?php if(empty($posts)): ?>
        <div class="text-center" style="padding: 4rem 0;">
            <i class="fas fa-folder-open text-muted" style="font-size: 4rem; margin-bottom: 1rem;"></i>
            <h3>No posts found</h3>
            <p class="text-muted">Check back later for new content.</p>
        </div>
    <?php else: ?>
        <div class="blog-grid">
            <?php foreach($posts as $post): ?>
                <div class="post-card">
                    <a href="post.php?id=<?php echo $post['id']; ?>">
                        <?php if ($post['image']): ?>
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-image">
                        <?php else: ?>
                            <div class="post-image" style="background: linear-gradient(135deg, var(--bg-main), #E2E8F0); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                                <i class="fas fa-image" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                    </a>
                    <div class="post-content">
                        <div class="post-meta">
                            <?php if ($post['category']): ?>
                                <a href="index.php?category=<?php echo $post['cat_slug']; ?>" class="post-category"><?php echo htmlspecialchars($post['category']); ?></a>
                            <?php endif; ?>
                            <span><i class="far fa-calendar-alt"></i> <?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                        </div>
                        
                        <h2 class="post-title"><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                        
                        <p class="post-excerpt">
                            <?php echo get_preview($post['content'], 120); ?>
                        </p>
                        
                        <div class="post-footer">
                            <div class="author-info">
                                <div class="author-avatar"><i class="fas fa-user"></i></div>
                                <span><?php echo htmlspecialchars($post['author']); ?></span>
                            </div>
                            <a href="post.php?id=<?php echo $post['id']; ?>" style="font-weight: 600; font-size: 0.9rem;">Read More <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'index.php'); ?>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
