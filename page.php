<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (!isset($_GET['slug'])) {
    redirect('index.php');
}

$slug = clean_input($_GET['slug']);

// Fetch the published page
try {
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ? AND status = 'published'");
    $stmt->execute([$slug]);
    $page = $stmt->fetch();
} catch (PDOException $e) {
    $page = false;
}

if (!$page) {
    // 404 Not Found
    http_response_code(404);
    $page_title = "404 Not Found";
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="container" style="text-align:center; padding: 10rem 1rem;">';
    echo '<h1 style="font-size: 5rem; color: var(--danger); margin-bottom: 1rem;"><i class="fas fa-exclamation-triangle"></i></h1>';
    echo '<h2>Oops! Page Not Found.</h2>';
    echo '<p style="color: var(--text-muted); margin-bottom: 2rem;">The page you are looking for does not exist or has been removed.</p>';
    echo '<a href="index.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Return Home</a>';
    echo '</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Set meta data for the header
$page_title = $page['title'];

// Safely assign meta definition dynamically specifically mapped out to override global layouts
$meta_description = !empty($page['meta_description']) ? $page['meta_description'] : '';

require_once __DIR__ . '/includes/header.php';

// Prepare hero banner style
$hero_style = '';
$text_color = "var(--text-main)";

if (!empty($page['featured_image']) && file_exists(__DIR__ . '/assets/images/uploads/' . $page['featured_image'])) {
    $img_url = 'assets/images/uploads/' . htmlspecialchars($page['featured_image']);
    // Render dynamic overlay header
    $hero_style = "background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('$img_url') no-repeat center center/cover; padding: 6rem 0;";
    $text_color = "white"; // Force white text on dark hero overlays
} else {
    $hero_style = "background: var(--bg-card); padding: 4rem 0;";
}
?>

<div class="page-header" style="<?php echo $hero_style; ?> border-bottom: 1px solid var(--border); text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 0.5rem; color: <?php echo $text_color; ?>;"><?php echo htmlspecialchars($page['title']); ?></h1>
    </div>
</div>

<div class="container" style="padding: 4rem 1rem;">
    <div class="post-content" style="max-width: 900px; margin: 0 auto; background: var(--bg-card); padding: 4rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid var(--border); line-height: 1.8;">
        <?php echo $page['content']; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
