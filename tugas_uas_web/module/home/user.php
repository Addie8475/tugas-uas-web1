<?php
// Halaman menu cepat untuk user: Home, Data Artikel, Logout

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hanya boleh diakses ketika sudah login

if (empty($_SESSION['is_login'])) {
    header('Location: ?url=user/login');
    exit;
}
// Hanya user biasa (bukan admin) yang boleh akses halaman ini
if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: ?url=user/index');
    exit;
}

$username = htmlspecialchars($_SESSION['username'] ?? 'Guest');

?>

<div class="header">
    <h1><i class="fas fa-user-circle"></i> Menu Pengguna</h1>
    <p class="welcome-text">Selamat datang kembali, <strong><?= $username ?></strong>!</p>
</div>

<div class="user-menu-grid">
   

    <a href="?url=artikel/index" class="menu-card">
        <div class="menu-card-icon bg-warning-gradient">
            <i class="fas fa-newspaper"></i>
        </div>
        <div class="menu-card-content">
            <h4>Artikel</h4>
            <p>Lihat artikel</p>
        </div>
    
    </a>

    <a href="?url=user/logout" class="menu-card">
        <div class="menu-card-icon bg-danger-gradient">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <div class="menu-card-content">
            <h4>Logout</h4>
            <p>Keluar dari sesi akun Anda</p>
        </div>
    </a>
</div>

<div class="card mt-40">
    <div class="card-header">
        <h3><i class="fas fa-info-circle"></i> Info Akun</h3>
    </div>
    <div class="card-body">
        <p>Ini adalah halaman menu cepat anda sebagai user.</p>
    </div>
</div>
