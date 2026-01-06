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
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $nama = trim($_POST['nama'] ?? '');
    $role = $_POST['role'] ?? 'user';
    if ($username === '' || $password === '' || $nama === '') {
        $message = 'Semua field wajib diisi.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $db->insert('users', [
            'username' => $username,
            'password' => $hash,
            'nama' => $nama,
            'role' => $role
        ]);
        header('Location: ?url=user/index&success=tambah');
        exit;
    }
}
?>
<div class="header">
    <h1><i class="fas fa-user-plus"></i> Tambah Pengguna</h1>
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
        <h3><i class="fas fa-user-plus"></i> Form Tambah Pengguna</h3>
    </div>
    <div class="card-body">
        <form method="POST" id="formTambahUser">
            <div class="form-group">
                <label for="username" class="form-label"><i class="fas fa-user"></i> Username</label>
                <input id="username" type="text" name="username" class="form-input" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Password</label>
                <input id="password" type="password" name="password" class="form-input" required>
                <div class="form-hint">Gunakan password yang kuat (min. 8 karakter)</div>
            </div>

            <div class="form-group">
                <label for="role" class="form-label"><i class="fas fa-user-cog"></i> Role</label>
                <select id="role" name="role" class="form-input">
                    <option value="user" <?= (($_POST['role'] ?? '') === 'user') ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <button type="reset" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</button>
                <a href="?url=user/index" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>
