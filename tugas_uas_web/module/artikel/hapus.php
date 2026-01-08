<?php
require_once "class/Database.php";

// Pastikan session dan role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['is_login']) || empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?url=artikel/index');
    exit;
}

$id = $_GET['id'] ?? 0;
$db = new Database();

if($id > 0) {
    // Cek apakah artikel ada
    $artikel = $db->getById("artikel", $id);
    if($artikel) {
        $judul_artikel = $artikel['judul'];
        
        // Hapus artikel
        if($db->delete("artikel", $id)) {
            // Redirect dengan parameter sukses dan judul artikel
            $judul_encoded = urlencode($judul_artikel);
            header("Location: ?url=artikel/index&success=hapus&judul={$judul_encoded}");
            exit;
        } else {
            header("Location: ?url=artikel/index&error=gagal_hapus");
            exit;
        }
    } else {
        header("Location: ?url=artikel/index&error=not_found");
        exit;
    }
}

// Jika gagal
header("Location: ?url=artikel/index&error=invalid_id");
exit;
?>