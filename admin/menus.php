<?php
require_once __DIR__ . '/includes/header.php';

$action = $_GET['action'] ?? 'list';
$edit_menu = null;

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $label = clean_input($_POST['label']);
    $url = clean_input($_POST['url']);
    $is_mega = isset($_POST['is_mega_menu']) ? 1 : 0;
    
    if (empty($label) || empty($url)) {
        set_flash_message('danger', 'Label and URL are required.');
    } else {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE menus SET label = ?, url = ?, is_mega_menu = ? WHERE id = ?");
            if ($stmt->execute([$label, $url, $is_mega, $id])) {
                set_flash_message('success', 'Menu updated successfully.');
            }
        } else {
            // Get max sort_order
            $maxSort = $pdo->query("SELECT MAX(sort_order) FROM menus WHERE parent_id IS NULL")->fetchColumn();
            $sort = $maxSort ? $maxSort + 1 : 1;
            
            $stmt = $pdo->prepare("INSERT INTO menus (label, url, sort_order, is_mega_menu) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$label, $url, $sort, $is_mega])) {
                set_flash_message('success', 'Menu added successfully.');
            }
        }
        redirect('menus.php');
    }
}

// Handle Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM menus WHERE id = ?");
    if ($stmt->execute([$id])) {
        set_flash_message('success', 'Menu deleted.');
    }
    redirect('menus.php');
}

// Fetch for edit
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM menus WHERE id = ?");
    $stmt->execute([$id]);
    $edit_menu = $stmt->fetch();
}

// Fetch all menus to build hierarchy
$stmt = $pdo->query("SELECT * FROM menus ORDER BY sort_order ASC");
$all_menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Categories, Posts, and Pages for "Quick Add"
$categories = $pdo->query("SELECT id, name, slug FROM categories ORDER BY name ASC")->fetchAll();
$posts = $pdo->query("SELECT id, title FROM posts WHERE status='published' ORDER BY created_at DESC")->fetchAll();
$pages = $pdo->query("SELECT id, title, slug FROM pages WHERE status='published' ORDER BY title ASC")->fetchAll();

function build_menu_tree($elements, $parentId = null) {
    $branch = array();
    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = build_menu_tree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[] = $element;
        }
    }
    return $branch;
}

$menu_tree = build_menu_tree($all_menus);

function render_nestable($tree) {
    echo '<ol class="dd-list">';
    foreach ($tree as $item) {
        echo '<li class="dd-item" data-id="' . $item['id'] . '">';
        echo '<div class="dd-handle" style="padding-right: 120px;">';
        
        echo '<div><i class="fas fa-arrows-alt text-muted" style="margin-right:0.5rem; cursor: move;"></i> <strong>' . htmlspecialchars($item['label']) . '</strong> <span style="color:#9CA3AF; font-size:0.85rem; margin-left:0.5rem;">(' . htmlspecialchars($item['url']) . ')</span></div>';
        
        echo '</div>'; // End handle
        
        // Actions container directly inside list item overlaid over the handle
        $actions = '<div class="item-actions" style="position: absolute; right: 15px; top: 12px; z-index: 50;">';
        if ($item['is_mega_menu']) $actions .= '<span class="badge badge-info" style="margin-right: 0.5rem;">Mega</span>';
        $actions .= '<a href="menus.php?action=edit&id=' . $item['id'] . '" class="text-info" style="margin-right:0.5rem;"><i class="fas fa-edit" style="font-size: 1.1rem;"></i></a>';
        $actions .= '<a href="menus.php?action=delete&id=' . $item['id'] . '" class="text-danger" onclick="return confirm(\'Delete this menu?\');"><i class="fas fa-trash" style="font-size: 1.1rem;"></i></a>';
        $actions .= '</div>';
        
        echo $actions;
        
        if (!empty($item['children'])) {
            render_nestable($item['children']);
        }
        echo '</li>';
    }
    echo '</ol>';
}
?>

<!-- Require Nestable CSS & JS -->
<style>
.dd { position: relative; display: block; margin: 0; padding: 0; max-width: 600px; list-style: none; font-size: 0.95rem; line-height: 1.5; }
.dd-list { display: block; position: relative; margin: 0; padding: 0; list-style: none; }
.dd-list .dd-list { padding-left: 30px; }
.dd-item, .dd-empty, .dd-placeholder { display: block; position: relative; margin: 0; padding: 0; min-height: 20px; font-size: 0.95rem; line-height: 20px; }
.dd-handle { display: block; margin: 5px 0; padding: 0.6rem 1rem; color: #333; text-decoration: none; border: 1px solid #E5E7EB; background: #fafafa; border-radius: 6px; box-sizing: border-box; cursor: grab; }
.dd-handle:hover { color: var(--primary); background: #fff; }
.dd-item > button { display: none; } /* hide collapse buttons for simplicity */
.dd-placeholder { margin: 5px 0; padding: 0; min-height: 42px; background: #f3f4f6; border: 1px dashed #d1d5db; box-sizing: border-box; }
.dd-dragel { position: absolute; pointer-events: none; z-index: 9999; }
.dd-dragel > .dd-item .dd-handle { margin-top: 0; }
</style>

<div class="dashboard-header card-header" style="margin-bottom: 2rem; background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between;">
    <div>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Navigation Menus</h1>
        <p style="color: var(--text-muted);">Manage your site header menus via Drag and Drop.</p>
    </div>
</div>

<?php echo get_flash_message(); ?>

<div style="display: flex; gap: 2rem; flex-wrap: wrap; align-items: flex-start;">
    
    <!-- Add/Edit form -->
    <div class="card" style="flex: 1; min-width: 300px;">
        <div class="card-header">
            <h3><?php echo $edit_menu ? 'Edit Menu Item' : 'Add Menu Item'; ?></h3>
            <?php if ($edit_menu): ?>
                <a href="menus.php" class="btn btn-sm btn-outline">Cancel</a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form method="POST" action="menus.php">
                <?php if ($edit_menu): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_menu['id']; ?>">
                <?php endif; ?>
                
                <div style="background: #F9FAFB; border: 1px dashed #d1d5db; border-radius: var(--radius); padding: 1rem; margin-bottom: 2rem;">
                    <label style="display: block; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 0.75rem;"><i class="fas fa-bolt text-warning"></i> Quick Add Link</label>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <select id="quick-category" class="form-control" style="flex: 1; font-size: 0.9rem; padding: 0.4rem 0.75rem; background: white;">
                            <option value="">-- Choose Category --</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="index.php?category=<?php echo htmlspecialchars($cat['slug']); ?>" data-label="<?php echo htmlspecialchars($cat['name']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-outline btn-sm" onclick="useQuickLink('quick-category')"><i class="fas fa-plus"></i></button>
                    </div>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <select id="quick-post" class="form-control" style="flex: 1; font-size: 0.9rem; padding: 0.4rem 0.75rem; background: white;">
                            <option value="">-- Choose Post --</option>
                            <?php foreach($posts as $post): ?>
                                <option value="post.php?id=<?php echo $post['id']; ?>" data-label="<?php echo htmlspecialchars($post['title']); ?>"><?php echo htmlspecialchars(mb_substr($post['title'], 0, 30)) . (mb_strlen($post['title']) > 30 ? '...' : ''); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-outline btn-sm" onclick="useQuickLink('quick-post')"><i class="fas fa-plus"></i></button>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <select id="quick-page" class="form-control" style="flex: 1; font-size: 0.9rem; padding: 0.4rem 0.75rem; background: white;">
                            <option value="">-- Choose Custom Page --</option>
                            <?php foreach($pages as $page): ?>
                                <option value="page.php?slug=<?php echo htmlspecialchars($page['slug']); ?>" data-label="<?php echo htmlspecialchars($page['title']); ?>"><?php echo htmlspecialchars($page['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-outline btn-sm" onclick="useQuickLink('quick-page')"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Menu Label</label>
                    <input type="text" name="label" class="form-control" required value="<?php echo $edit_menu ? htmlspecialchars($edit_menu['label']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>URL / Link</label>
                    <input type="text" name="url" class="form-control" required value="<?php echo $edit_menu ? htmlspecialchars($edit_menu['url']) : ''; ?>" placeholder="e.g. index.php or #">
                </div>
                
                <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem; margin-bottom: 1.5rem;">
                    <input type="checkbox" name="is_mega_menu" id="is_mega_menu" value="1" <?php echo ($edit_menu && $edit_menu['is_mega_menu']) ? 'checked' : ''; ?>>
                    <label for="is_mega_menu" style="margin: 0;">Enable Mega Menu Dropdown</label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Save Menu</button>
            </form>
        </div>
    </div>

    <!-- Drag & Drop Menu Builder -->
    <div class="card" style="flex: 2; min-width: 400px;">
        <div class="card-header" style="display: flex; justify-content: space-between;">
            <h3>Menu Structure Structure</h3>
            <button id="save-menu-hierarchy" class="btn btn-sm btn-success"><i class="fas fa-save"></i> Save Order</button>
        </div>
        <div class="card-body">
            <p style="color: var(--text-muted); margin-bottom: 1rem; font-size: 0.9rem;">Drag and drop items to arrange your navigation structure. Click <strong>Save Order</strong> when finished.</p>
            
            <div class="dd" id="nestable">
                <?php if (empty($menu_tree)): ?>
                    <div class="dd-empty">No menu items found. Add one.</div>
                <?php else: ?>
                    <?php render_nestable($menu_tree); ?>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</div>

<!-- jQuery (Required for Nestable) -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- jQuery Nestable Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Nestable/2012-10-15/jquery.nestable.min.js"></script>

<script>
$(document).ready(function() {
    $('#nestable').nestable({
        maxDepth: 3
    });
    
    $('#save-menu-hierarchy').on('click', function() {
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        var hierarchy = window.JSON.stringify($('#nestable').nestable('serialize'));
        
        $.ajax({
            url: 'ajax_save_menu.php',
            type: 'POST',
            data: { hierarchy: hierarchy },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.status === 'success') {
                    $btn.html('<i class="fas fa-check"></i> Saved!');
                    setTimeout(() => $btn.html(originalText), 2000);
                } else {
                    alert('Error saving menu.');
                    $btn.html(originalText);
                }
            },
            error: function() {
                alert('Connection error.');
                $btn.html(originalText);
            }
        });
    });
});

function useQuickLink(selectId) {
    const select = document.getElementById(selectId);
    if (select.selectedIndex <= 0) return;
    const option = select.options[select.selectedIndex];
    
    // Auto populate the label and URL fields
    document.querySelector('input[name="label"]').value = option.getAttribute('data-label');
    document.querySelector('input[name="url"]').value = option.value;
    
    // Reset selection back to 'choose' placeholder
    select.selectedIndex = 0;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
