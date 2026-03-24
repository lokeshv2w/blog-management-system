    </main>
    
    <footer class="footer">
        <div class="container footer-container">
            <div class="footer-brand">
                <h3><i class="fas fa-layer-group text-gradient"></i> <?php echo htmlspecialchars(get_setting('site_title', 'DevBlog')); ?></h3>
                <p><?php echo htmlspecialchars(get_setting('site_description', 'A modern, premium blog management system.')); ?></p>
            </div>
            <div class="footer-links">
                <h4>Categories</h4>
                <ul>
                    <?php 
                    $foot_cats = array_slice($categories ?? [], 0, 5);
                    foreach($foot_cats as $cat): ?>
                        <li><a href="index.php?category=<?php echo htmlspecialchars($cat['slug']); ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="footer-social">
                <h4>Follow Us</h4>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-github"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(get_setting('footer_text', 'DevBlog CMS. All rights reserved.')); ?></p>
            </div>
        </div>
    </footer>
    
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const navLinks = document.querySelector('.nav-links');
            
            if (menuToggle && navLinks) {
                menuToggle.addEventListener('click', function() {
                    navLinks.classList.toggle('active');
                    this.classList.toggle('active');
                });
            }

            // Simple parallax effect for hero
            window.addEventListener('scroll', function() {
                const scrolled = window.scrollY;
                const heroPattern = document.querySelector('.hero-pattern');
                if (heroPattern) {
                    heroPattern.style.transform = 'translateY(' + (scrolled * 0.3) + 'px)';
                }
            });
        });
    </script>
</body>
</html>
