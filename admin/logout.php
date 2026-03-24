<?php
require_once __DIR__ . '/../includes/functions.php';

// Unset all session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session.
session_destroy();
session_start(); // Start new session to set flash message
set_flash_message('info', 'You have been successfully logged out.');

// Redirect to login page
redirect('login.php');
?>
