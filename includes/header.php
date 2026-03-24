<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Fetch dynamic menus
try {
    $stmt = $pdo->query("SELECT * FROM menus ORDER BY sort_order ASC");
    $all_menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Failsafe when table not generated
    $all_menus = [];
}

function build_public_menu($elements, $parentId = null) {
    $branch = array();
    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = build_public_menu($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
        }
    }
    return $branch;
}

$menu_tree = build_public_menu($all_menus);

function render_public_menu($tree) {
    foreach ($tree as $item) {
        $has_children = !empty($item['children']);
        
        if ($has_children) {
            if ($item['is_mega_menu']) {
                // Mega menu layout
                echo '<div class="dropdown mega-dropdown">';
                echo '<a href="' . htmlspecialchars($item['url']) . '" class="nav-link dropdown-toggle">' . htmlspecialchars($item['label']) . ' <i class="fas fa-chevron-down" style="font-size: 0.8rem; margin-left: 0.25rem;"></i></a>';
                echo '<div class="mega-menu-content">';
                
                echo '<div class="mega-menu-grid container">';
                foreach ($item['children'] as $child) {
                    echo '<div class="mega-menu-column">';
                    echo '<h4><a href="' . htmlspecialchars($child['url']) . '">' . htmlspecialchars($child['label']) . '</a></h4>';
                    if (!empty($child['children'])) {
                        echo '<ul>';
                        foreach ($child['children'] as $subchild) {
                            echo '<li><a href="' . htmlspecialchars($subchild['url']) . '">' . htmlspecialchars($subchild['label']) . '</a></li>';
                        }
                        echo '</ul>';
                    }
                    echo '</div>'; // .mega-menu-column
                }
                echo '</div>'; // .mega-menu-grid
                
                echo '</div>'; // .mega-menu-content
                echo '</div>'; // .mega-dropdown
            } else {
                // Standard dropdown
                echo '<div class="dropdown">';
                echo '<a href="' . htmlspecialchars($item['url']) . '" class="nav-link dropdown-toggle">' . htmlspecialchars($item['label']) . ' <i class="fas fa-chevron-down" style="font-size: 0.8rem; margin-left: 0.25rem;"></i></a>';
                echo '<div class="dropdown-menu">';
                foreach ($item['children'] as $child) {
                    echo '<a href="' . htmlspecialchars($child['url']) . '">' . htmlspecialchars($child['label']) . '</a>';
                }
                echo '</div>';
                echo '</div>';
            }
        } else {
            // Simple link
            echo '<a href="' . htmlspecialchars($item['url']) . '" class="nav-link">' . htmlspecialchars($item['label']) . '</a>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' . htmlspecialchars(get_setting('site_title', 'DevBlog')) : htmlspecialchars(get_setting('site_title', 'DevBlog')) . ' - Modern CMS'; ?></title>
    
    <?php if (isset($meta_description) && !empty($meta_description)): ?>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <?php else: ?>
    <meta name="description" content="<?php echo htmlspecialchars(get_setting('site_description', 'A modern, premium blog management system.')); ?>">
    <?php endif; ?>
    
    <!-- Modern Display Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar" style="position: sticky; top: 0; z-index: 1000; position: -webkit-sticky;">
        <div class="container nav-container" style="position: relative;">
            <a href="index.php" class="brand">
                <i class="fas fa-layer-group text-gradient"></i> <?php echo htmlspecialchars(get_setting('site_title', 'DevBlog')); ?>
            </a>
            
            <button class="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="nav-links">
                <?php 
                if (empty($menu_tree)) {
                    // Fallback purely for first load before user seeds menus structure
                    echo '<a href="index.php" class="nav-link">Home</a>';
                } else {
                    render_public_menu($menu_tree);
                }
                ?>
                
                <?php if (is_logged_in()): ?>
                    <a href="admin/index.php" class="btn btn-primary btn-rounded">Dashboard</a>
                <?php else: ?>
                    <a href="admin/login.php" class="btn btn-outline btn-rounded">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <!-- We adjust navbar static trick via style injection if mega menu is rendered -->
    <style>
        .navbar:has(.mega-dropdown:hover) { /* CSS fallback trick */ position: relative; }
        .mega-dropdown { position: static; } 
        .mega-menu-content {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: var(--bg-card);
            box-shadow: var(--shadow-lg);
            border-top: 1px solid var(--border);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 10;
            padding: 3rem 0;
        }
        .mega-dropdown:hover .mega-menu-content {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .mega-menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .mega-menu-column h4 {
            margin-bottom: 1.2rem;
            font-size: 1.15rem;
            color: var(--primary);
            border-bottom: 2px solid var(--border);
            padding-bottom: 0.5rem;
        }
        .mega-menu-column h4 a { color: var(--primary); text-decoration: none; }
        .mega-menu-column ul {
            list-style: none;
            padding: 0; margin: 0;
        }
        .mega-menu-column li {
            margin-bottom: 0.75rem;
        }
        .mega-menu-column li a {
            color: var(--text-muted);
            transition: color 0.2s;
            font-weight: 500;
            text-decoration: none;
        }
        .mega-menu-column li a:hover {
            color: var(--primary);
        }
        
        /* Mobile adjustment */
        @media (max-width: 768px) {
            .mega-menu-content {
                position: static;
                opacity: 1; visibility: visible; transform: none; display: none; padding: 1rem; border-top: none;
            }
            .mega-dropdown:hover .mega-menu-content { display: block; }
        }
    </style>
    <main class="main-content">
