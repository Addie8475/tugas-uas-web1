<?php
// Mulai sesi (penting untuk penggunaan $_SESSION)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, langsung ke daftar artikel
if (!empty($_SESSION['is_login'])) {
    header('Location: ?url=artikel/index');
    exit;
}

$message = '';
$username = '';

// Proses login ketika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();

    // Ambil dan sanitasi input dasar (gunakan isset untuk menghindari notice)
    $username = isset($_POST['username']) ? trim((string) $_POST['username']) : '';
    $password = isset($_POST['password']) ? (string) $_POST['password'] : '';

    if ($username === '' || $password === '') {
        $message = 'Username dan password wajib diisi.';
    } else {
        // Gunakan prepared statement untuk mencegah SQL Injection
        // Pilih kolom eksplisit supaya kita tahu urutan kolom saat menggunakan bind_result
        $stmt = $db->conn->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            // get_result() tidak selalu tersedia (butuh mysqlnd). Berikan fallback ke bind_result
            $data = null;
            if (method_exists($stmt, 'get_result')) {
                $result = $stmt->get_result();
                $data = $result ? $result->fetch_assoc() : null;
            } else {
                // Fallback: bind result ke variabel dan fetch
                $stmt->bind_result($col_id, $col_username, $col_password, $col_nama, $col_role);
                if ($stmt->fetch()) {
                    $data = [
                        'id' => $col_id,
                        'username' => $col_username,
                        'password' => $col_password,
                        'nama' => $col_nama,
                        'role' => $col_role
                    ];
                }
            }

            // Pastikan kolom password ada untuk menghindari notice
            if ($data && isset($data['password'])) {
                $stored = $data['password'];
                $login_ok = false;

                // Pertama coba verifikasi password hash (jika sudah di-hash)
                if (password_verify($password, $stored)) {
                    $login_ok = true;
                }

                // Jika tidak cocok, cek kecocokan plaintext (kompatibilitas dengan data lama)
                // Jika cocok, re-hash dan simpan agar lebih aman
                if (!$login_ok && hash_equals((string) $stored, (string) $password)) {
                    $login_ok = true;
                    // Re-hash password dan update database untuk keamanan
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    if (isset($data['id'])) {
                        $db->update('users', ['password' => $newHash], $data['id']);
                    }
                }

                if ($login_ok) {
                    // Login sukses: set session
                    $_SESSION['is_login'] = true;
                    $_SESSION['username'] = $data['username'];
                    $_SESSION['nama'] = $data['nama'];
                    $_SESSION['role'] = $data['role'];

                    // Redirect sesuai role
                    if ($data['role'] === 'admin') {
                        header('Location: ?url=user/index'); // admin ke manajemen user
                    } else {
                        header('Location: ?url=artikel/index'); // user ke artikel
                    }
                    exit;
                } else {
                    // Jika user ada tapi password tidak cocok, catat di error log untuk debugging lokal
                    if ($data && isset($data['username'])) {
                        error_log("[DEBUG] Login gagal untuk user '{$data['username']}' - stored_password='" . (isset($data['password']) ? $data['password'] : '<none>') . "'\n");
                    }
                    $message = 'Username atau password salah.';
                }
            } else {
                // Data user tidak ditemukan atau kolom password tidak ada
                $message = 'Username atau password salah.';
            }

            $stmt->close();
        } else {
            // Jika prepare gagal, jangan tampilkan detail error ke user
            $message = 'Terjadi kesalahan pada server. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="login-container">
        <h3 class="text-center mb-4">Login User</h3>

        <?php if ($message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="on">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required maxlength="100" value="<?= htmlspecialchars($username) ?>">
            </div>
                <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</body>
</html>