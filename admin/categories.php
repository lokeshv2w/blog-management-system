<?php
require_once __DIR__ . '/includes/header.php';

$action = $_GET['action'] ?? 'list';
$edit_category = null;

// Handle Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        if ($stmt->execute([$id])) {
            set_flash_message('success', 'Category deleted successfully.');
        } else {
            set_flash_message('danger', 'Error deleting category.');
        }
    } catch (PDOException $e) {
        set_flash_message('danger', 'Cannot delete this category because it is in use.');
    }
    redirect('categories.php');
}

// Handle Add / Edit POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $name = clean_input($_POST['name']);
    $slug = slugify($name);

    if (empty($name)) {
        set_flash_message('danger', 'Category name is required.');
    } else {
        if ($id) {
            // Edit
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
            try {
                if ($stmt->execute([$name, $slug, $id])) {
                    set_flash_message('success', 'Category updated successfully.');
                }
            } catch (PDOException $e) {
                set_flash_message('danger', 'Category with this name might already exist.');
            }
        } else {
            // Add
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
            try {
                if ($stmt->execute([$name, $slug])) {
                    set_flash_message('success', 'Category added successfully.');
                }
            } catch (PDOException $e) {
                set_flash_message('danger', 'Category slug already exists.');
            }
        }
        redirect('categories.php');
    }
}

// Prepare Edit Form Data
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $edit_category = $stmt->fetch();
}

// Fetch total count for pagination
$total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$pagination = get_pagination_data($total_categories, 10);

// List categories with limit and offset
$stmt = $pdo->prepare("SELECT c.*, COUNT(p.id) as post_count 
                      FROM categories c 
                      LEFT JOIN posts p ON c.id = p.category_id 
                      GROUP BY c.id 
                      ORDER BY c.name ASC 
                      LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', (int)$pagination['limit'], PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$pagination['offset'], PDO::PARAM_INT);
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<div class="dashboard-header card-header" style="margin-bottom: 2rem; background: var(--card-bg); border-radius: var(--radius); box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between;">
    <div>
        <h1 style="font-size: 1.5rem; margin-bottom: 0.25rem;">Categories</h1>
        <p style="color: var(--text-muted);">Manage post categories.</p>
    </div>
</div>

<div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
    <!-- Add/Edit Form -->
    <div class="card" style="flex: 1; min-width: 300px;">
        <div class="card-header">
            <h3><?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?></h3>
            <?php if ($edit_category): ?>
                <a href="categories.php" class="btn btn-sm btn-outline">Cancel</a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form method="POST" action="categories.php">
                <?php if ($edit_category): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : ''; ?>">
                    <small style="color: var(--text-muted); display: block; margin-top: 0.5rem;">The slug will be automatically generated from the name.</small>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $edit_category ? 'Update Category' : 'Add Category'; ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Categories List -->
    <div class="card" style="flex: 2; min-width: 500px;">
        <div class="card-header">
            <h3>All Categories</h3>
        </div>
        <div class="card-body" style="overflow-x: auto;">
            <?php if(empty($categories)): ?>
                <p>No categories found. Add one on the left.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Posts Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $category): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($category['name']); ?></strong></td>
                            <td><span class="badge badge-info" style="background:#DBEAFE; color:#1E40AF;"><?php echo htmlspecialchars($category['slug']); ?></span></td>
                            <td>
                                <span class="badge" style="background:var(--body-bg); color:var(--text-main); border:1px solid var(--border);">
                                    <?php echo $category['post_count']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="categories.php?action=edit&id=<?php echo $category['id']; ?>" class="action-btn text-info" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="categories.php?action=delete&id=<?php echo $category['id']; ?>" class="action-btn text-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this category?');"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'categories.php'); ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
