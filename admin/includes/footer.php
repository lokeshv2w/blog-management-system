<?php if (is_logged_in()): ?>
        </div> <!-- /content-container -->
    </div> <!-- /main-content -->
</div> <!-- /admin-wrapper -->

<script>
    // Sidebar toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });
        }
    });

    // TinyMCE Initialization
    tinymce.init({
      selector: 'textarea.rich-editor',
      plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
      height: 400
    });
</script>
<?php else: ?>
        </div> <!-- /login-container -->
    </div> <!-- /login-wrapper -->
<?php endif; ?>
</body>
</html>
