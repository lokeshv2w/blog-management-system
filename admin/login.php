<?php
// We only need functions to check session and redirect before header
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Login success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            
            set_flash_message('success', 'Welcome back, ' . htmlspecialchars($user['username']) . '!');
            redirect('index.php');
        } else {
            $error = "Invalid username or password.";
        }
    }
}

// Include header for layout
require_once __DIR__ . '/includes/header.php';
?>

<div class="login-card">
    <div class="login-header">
        <h2><i class="fas fa-pen-nib"></i> DevBlog CMS</h2>
        <p>Sign in to manage your blog.</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="username">Username</label>
            <div class="input-icon">
                <i class="fas fa-user"></i>
                <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($username ?? ''); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-icon">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" required>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
