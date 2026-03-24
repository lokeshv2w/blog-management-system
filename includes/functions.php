<?php
session_start();

function redirect($url) {
    header("Location: $url");
    exit();
}

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return "<div class='alert alert-{$message['type']}'>{$message['message']}</div>";
    }
    return '';
}

function slugify($text, string $divider = '-') {
    // replace non letter or digits by divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // trim
    $text = trim($text, $divider);
    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);
    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

function get_preview($content, $limit = 150) {
    $content = strip_tags($content);
    if (strlen($content) > $limit) {
        return substr($content, 0, $limit) . '...';
    }
    return $content;
}

// Site global settings getter
function get_setting($key, $default = '') {
    global $pdo;
    if (!$pdo) return $default;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? $val : $default;
    } catch (PDOException $e) {
        return $default; // Silently fail if DB setup isn't done yet
    }
}

/**
 * Get pagination data
 * @param int $total_items
 * @param int $limit
 * @return array
 */
function get_pagination_data($total_items, $limit = 10) {
    if ($total_items === 0) {
        return [
            'total_items' => 0,
            'total_pages' => 0,
            'current_page' => 1,
            'offset' => 0,
            'limit' => $limit
        ];
    }
    $total_pages = ceil($total_items / $limit);
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($current_page < 1) $current_page = 1;
    if ($current_page > $total_pages) $current_page = $total_pages;
    
    $offset = ($current_page - 1) * $limit;
    
    return [
        'total_items' => $total_items,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'offset' => $offset,
        'limit' => $limit
    ];
}

/**
 * Render pagination HTML
 * @param int $current_page
 * @param int $total_pages
 * @param string $base_url
 * @return string
 */
function render_pagination($current_page, $total_pages, $base_url) {
    if ($total_pages <= 1) return '';
    
    $query_params = $_GET;
    unset($query_params['page']);
    $url_prefix = $base_url;
    
    // Standardize URL logic
    $params_str = http_build_query($query_params);
    $connector = (strpos($base_url, '?') === false) ? '?' : '&';
    if (!empty($params_str)) {
        $url_prefix .= $connector . $params_str . '&page=';
    } else {
        $url_prefix .= $connector . 'page=';
    }

    $html = '<nav aria-label="Page navigation" class="pagination-container">';
    $html .= '<ul class="pagination">';
    
    // First Link
    if ($current_page > 2) {
        $html .= "<li><a href='{$url_prefix}1' class='pagination-link' title='First Page'><i class='fas fa-angle-double-left'></i></a></li>";
    }

    // Previous Link
    $disabled = ($current_page <= 1) ? 'disabled' : '';
    $html .= "<li><a href='{$url_prefix}" . ($current_page - 1) . "' class='pagination-link {$disabled}'><i class='fas fa-chevron-left'></i></a></li>";
    
    // Page Numbers (Show max 5 pages around current page)
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);
    
    for ($i = $start_page; $i <= $end_page; $i++) {
        $active = ($i === $current_page) ? 'active' : '';
        $html .= "<li><a href='{$url_prefix}{$i}' class='pagination-link {$active}'>{$i}</a></li>";
    }
    
    // Next Link
    $disabled = ($current_page >= $total_pages) ? 'disabled' : '';
    $html .= "<li><a href='{$url_prefix}" . ($current_page + 1) . "' class='pagination-link {$disabled}'><i class='fas fa-chevron-right'></i></a></li>";
    
    // Last Link
    if ($current_page < $total_pages - 1) {
        $html .= "<li><a href='{$url_prefix}{$total_pages}' class='pagination-link' title='Last Page'><i class='fas fa-angle-double-right'></i></a></li>";
    }

    $html .= '</ul></nav>';
    
    return $html;
}
?>
