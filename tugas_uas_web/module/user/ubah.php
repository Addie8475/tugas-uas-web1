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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $data = [
        'username' => $username,
        'nama' => $nama,
        'role' => $role
    ];
    if (!empty($_POST['password'])) {
        $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }
    $db->update('users', $data, $id);
    header('Location: ?url=user/index&success=edit');
    exit;
}
?>
<div class="header">
    <h1><i class="fas fa-user-edit"></i> Ubah Pengguna</h1>
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
        <h3><i class="fas fa-user-edit"></i> Form Ubah Pengguna</h3>
    </div>
    <div class="card-body">
        <form method="POST" id="formUbahUser">
            <div class="form-group">
                <label for="username" class="form-label"><i class="fas fa-user"></i> Username</label>
                <input id="username" type="text" name="username" class="form-input" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Password <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                <input id="password" type="password" name="password" class="form-input">
            </div>

            <div class="form-group">
                <label for="role" class="form-label"><i class="fas fa-user-cog"></i> Role</label>
                <select id="role" name="role" class="form-input">
                    <option value="user" <?= ($user['role'] === 'user') ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="?url=user/index" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
