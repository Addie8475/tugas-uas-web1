<?php
require_once __DIR__ . '/../../class/Database.php';
require_once __DIR__ . '/../../config.php';

// Mulai session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, redirect ke artikel
if (!empty($_SESSION['is_login'])) {
    header('Location: ?url=artikel/index');
    exit;
}

$db = new Database();
$message = '';
$allowAdminSelection = !empty(ADMIN_REG_CODE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $nama = trim($_POST['nama'] ?? '');
    // Pastikan role ditetapkan dengan aman (default: user)
    $role = isset($_POST['role']) && in_array($_POST['role'], ['user', 'admin']) ? $_POST['role'] : 'user';

    // Validasi dasar
    if ($username === '' || $password === '') {
        $message = 'Username dan password wajib diisi.';
    } elseif (strlen($password) < 8) {
        $message = 'Password harus minimal 8 karakter.';
    } else {
        // Cek apakah username sudah ada
        $u = $db->conn->real_escape_string($username);
        $res = $db->query("SELECT id FROM users WHERE username = '$u' LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $message = 'Username sudah digunakan. Pilih username lain.';
        } else {
            // Jika user memilih role admin, cek kode rahasia
            if ($role === 'admin') {
                if (empty(ADMIN_REG_CODE)) {
                    $message = 'Registrasi admin tidak diizinkan.';
                } else {
                    $code = $_POST['admin_code'] ?? '';
                    if ($code !== ADMIN_REG_CODE) {
                        $message = 'Kode registrasi admin tidak valid.';
                    }
                }
            }

            // Jika tidak ada error, simpan user
            if ($message === '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);

                // Periksa apakah kolom 'nama' ada di tabel users agar tidak menyebabkan error pada skema lama
                $colCheck = $db->query("SHOW COLUMNS FROM users LIKE 'nama'");
                $hasNama = $colCheck && $colCheck->num_rows > 0;

                $insertData = [
                    'username' => $username,
                    'password' => $hash,
                    'role' => $role
                ];

                if ($hasNama) {
                    $insertData['nama'] = $nama;
                }

                $db->insert('users', $insertData);

                header('Location: ?url=user/login&registered=1');
                exit;
            }
        }
    }
}
?>

<div class="header">
    <h1><i class="fas fa-user-plus"></i> Registrasi Pengguna</h1>
    <a href="?url=user/login" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Kembali ke Login
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
        <h3><i class="fas fa-user-plus"></i> Form Registrasi</h3>
    </div>
    <div class="card-body">
        <form method="POST" id="formRegister">
            <div class="form-group">
                <label for="username" class="form-label"><i class="fas fa-user"></i> Username</label>
                <input id="username" type="text" name="username" class="form-input" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Password</label>
                <input id="password" type="password" name="password" class="form-input" required>
                <div class="form-hint">Minimal 8 karakter</div>
            </div>

            

            <div class="form-group">
                <label for="role" class="form-label"><i class="fas fa-user-cog"></i> Role</label>
                <?php if ($allowAdminSelection): ?>
                    <select id="role" name="role" class="form-input">
                        <option value="user" <?= (($_POST['role'] ?? '') === 'user') ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
                    </select>
                    <div class="form-hint">Untuk mendaftar sebagai admin, masukkan kode registrasi admin di bawah.</div>
                    <div class="form-group mt-16">
                        <label class="form-label">Kode Registrasi Admin</label>
                        <input type="text" name="admin_code" class="form-input">
                    </div>
                <?php else: ?>
                    <input type="hidden" name="role" value="user">
                    <input type="text" class="form-input" value="user" readonly>
                    <div class="form-hint">Registrasi admin dinonaktifkan oleh administrator.</div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Daftar</button>
                <a href="?url=user/login" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
// Validasi sederhana di sisi klien
document.getElementById('formRegister').addEventListener('submit', function(e) {
    var pw = document.getElementById('password').value || '';
    if (pw.length < 8) {
        e.preventDefault();
        Swal.fire({ title: 'Password terlalu pendek', text: 'Password harus minimal 8 karakter', icon: 'warning' });
    }
});
</script>
