<?php
// index.php (file utama di root)

include "config.php";
include "class/Database.php";
include "class/Form.php";

// Mulai session (diperlukan untuk pengecekan login)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ambil routing dari query string ?url=module/page
$url = isset($_GET['url']) ? trim($_GET['url']) : 'artikel/index';
$parts = explode('/', $url);
$module = $parts[0] ?: 'home';
$page = isset($parts[1]) ? $parts[1] : 'index';

// Halaman publik (tidak memerlukan login)
$public_modules = ['home', 'user'];

// Jika modul bukan publik dan user belum login, arahkan ke halaman login (query string)
if (!in_array($module, $public_modules) && empty($_SESSION['is_login'])) {
    header('Location: ?url=user/login');
    exit();
}

// Tentukan path file yang akan dimuat
$path = "module/{$module}/{$page}.php";

// Load template header
include "template/header.php";
include "template/sidebar.php";

echo "<div class='content'>";

if (file_exists($path)) {
    include $path;
} else {
    echo "<h3>404 - Halaman tidak ditemukan</h3>";
    echo "<p>Path: {$path} tidak ditemukan</p>";
}

echo "</div>";

include "template/footer.php";
?>