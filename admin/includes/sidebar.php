<div class="sidebar">
    <div class="logo">
        <a href="index.php">
            <i class="fas fa-pen-nib"></i> 
            <span class="logo-text">DevBlog CMS</span>
        </a>
    </div>
    
    <ul class="nav-links">
        <li>
            <a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span class="link-name">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="posts.php" class="<?php echo in_array($current_page, ['posts.php', 'add-post.php', 'edit-post.php']) ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i>
                <span class="link-name">Posts</span>
            </a>
        </li>
        <li>
            <a href="pages.php" class="<?php echo in_array($current_page, ['pages.php', 'add-page.php', 'edit-page.php']) ? 'active' : ''; ?>">
                <i class="fas fa-file-powerpoint"></i>
                <span class="link-name">Pages</span>
            </a>
        </li>
        <li>
            <a href="categories.php" class="<?php echo $current_page == 'categories.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i>
                <span class="link-name">Categories</span>
            </a>
        </li>
        <li>
            <a href="media.php" class="<?php echo $current_page == 'media.php' ? 'active' : ''; ?>">
                <i class="fas fa-images"></i>
                <span class="link-name">Media Library</span>
            </a>
        </li>
        <li>
            <a href="menus.php" class="<?php echo $current_page == 'menus.php' ? 'active' : ''; ?>">
                <i class="fas fa-bars"></i>
                <span class="link-name">Navigation Menus</span>
            </a>
        </li>
        <li>
            <a href="comments.php" class="<?php echo $current_page == 'comments.php' ? 'active' : ''; ?>">
                <i class="fas fa-comments"></i>
                <span class="link-name">Comments</span>
                <?php
                // Fetch pending comment count for badge
                $pending_count = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
                if ($pending_count > 0):
                ?>
                <span class="badge badge-warning" style="margin-left: auto;"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li>
            <a href="settings.php" class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cogs"></i>
                <span class="link-name">Settings</span>
            </a>
        </li>
        
        <li class="nav-divider"></li>
        
        <li>
            <a href="../index.php" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span class="link-name">View Site</span>
            </a>
        </li>
    </ul>
</div>
