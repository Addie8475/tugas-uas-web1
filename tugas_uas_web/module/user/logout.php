<?php
// Pastikan session dimulai terlebih dahulu
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Hapus semua data session dan kembalikan ke halaman login
$_SESSION = [];
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params['path'], $params['domain'], $params['secure'], $params['httponly']
	);
}
session_destroy();

header('Location: ?url=user/login');
exit;
?>