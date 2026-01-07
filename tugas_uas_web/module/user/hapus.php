<?php
require_once __DIR__ . '/../../class/Database.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['is_login']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?url=artikel/index');
    exit;
}
$db = new Database();
$id = intval($_GET['id'] ?? 0);
$user = $db->getById('users', $id);
$message = '';
if (!$user) {
    echo '<div class="alert alert-danger">User tidak ditemukan.</div>';
    exit;
}
// Hapus hanya ketika POST konfirmasi diterima
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        // Prevent admin deleting their own account accidentally
        if (isset($_SESSION['username']) && $_SESSION['username'] === $user['username']) {
            $message = 'Anda tidak dapat menghapus akun Anda sendiri.';
        } else {
            $db->delete('users', $id);
            header('Location: ?url=user/index&success=hapus');
            exit;
        }
    } else {
        header('Location: ?url=user/index');
        exit;
    }
}
?>
<div class="header">
    <h1><i class="fas fa-user-times"></i> Hapus Pengguna</h1>
    <a href="?url=user/index" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<?php if ($message): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h3>
    </div>
    <div class="card-body">
        <p>Anda akan menghapus pengguna berikut:</p>
        <div class="info-item">
            <div class="info-label">Username</div>
            <div><?= htmlspecialchars($user['username']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Nama</div>
            <div><?= htmlspecialchars($user['nama'] ?? '-') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Role</div>
            <div><?= htmlspecialchars($user['role']) ?></div>
        </div>

        <p class="mt-20 text-muted">Tindakan ini tidak dapat dibatalkan. Pastikan Anda benar-benar ingin menghapus pengguna ini.</p>

        <form method="POST" style="margin-top:20px">
            <button type="submit" name="confirm" value="yes" class="btn btn-danger"><i class="fas fa-trash"></i> Hapus Sekarang</button>
            <a href="?url=user/index" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a>
        </form>
    </div>
</div>
