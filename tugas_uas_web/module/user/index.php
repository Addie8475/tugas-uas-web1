<?php
require_once __DIR__ . '/../../class/Database.php';

// Pastikan user sudah login
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}


if (empty($_SESSION['is_login'])) {
	header('Location: ?url=user/login');
	exit;
}
// Hanya admin yang boleh akses
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
	header('Location: ?url=artikel/index');
	exit;
}

$db = new Database();
$users = $db->getAll('users');
$total_users = count($users);
?>

<div class="header">
    <h1><i class="fas fa-user-circle"></i> Menu Pengguna</h1>
    <?php $welcome_name = htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?>
    <p class="welcome-text">Selamat datang kembali, <strong><?= $welcome_name ?></strong>!</p>
</div>

<div class="user-menu-grid">
   

    <a href="?url=artikel/index" class="menu-card">
        <div class="menu-card-icon bg-warning-gradient">
            <i class="fas fa-newspaper"></i>
        </div>
        <div class="menu-card-content">
            <h4>Manajemen Artikel</h4>
            <p>Lihat, edit, atau hapus artikel</p>
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
        <p>Ini adalah halaman menu cepat anda sebagai administrator. Dari sini, Anda dapat dengan mudah mengakses fungsionalitas utama sistem.</p>
    </div>
</div>

<!-- Header -->
<div class="header">
	<h1><i class="fas fa-users"></i> Daftar Pengguna</h1>
	<a href="?url=user/tambah" class="btn btn-primary">
		<i class="fas fa-plus"></i> Tambah Pengguna
	</a>
</div>

<!-- Statistik -->
<div class="stat-card">
	<div class="stat-icon users">
		<i class="fas fa-user"></i>
	</div>
	<div class="stat-content">
		<h3><?= $total_users ?></h3>
		<p>Total Pengguna</p>
	</div>
</div>

<!-- Card Utama -->
<div class="card">
	<div class="card-header">
		<h3><i class="fas fa-list"></i> Semua Pengguna</h3>
	</div>
	<div class="card-body">
		<?php if(isset($_GET['success']) && $_GET['success'] == 'tambah'): ?>
			<div class="alert alert-success">
				<i class="fas fa-check-circle"></i>
				Pengguna berhasil ditambahkan!
			</div>
		<?php endif; ?>

		<?php if(isset($_GET['success']) && $_GET['success'] == 'edit'): ?>
			<div class="alert alert-success">
				<i class="fas fa-check-circle"></i>
				Pengguna berhasil diperbarui!
			</div>
		<?php endif; ?>

		<?php if(isset($_GET['success']) && $_GET['success'] == 'hapus'): ?>
			<div class="alert alert-success">
				<i class="fas fa-check-circle"></i>
				Pengguna berhasil dihapus!
			</div>
		<?php endif; ?>

		<?php if($total_users > 0): ?>
			<div class="table-container">
				<table class="data-table">
					<thead>
						<tr>
							<th width="50">ID</th>
							<th>Username</th>
							<th>Peran</th>
							<th width="150" class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($users as $u): ?>
							<tr>
								<td><span class="badge badge-success">#<?= htmlspecialchars($u['id']) ?></span></td>
								<td><?= htmlspecialchars($u['username']) ?></td>
								<td><?= htmlspecialchars($u['role'] ?? '-') ?></td>
								<td class="text-center">
									<div class="action-buttons">
										<a href="?url=user/ubah&id=<?= $u['id'] ?>" class="btn btn-warning btn-sm">
											<i class="fas fa-edit"></i> Edit
										</a>
										<a href="#" onclick="hapusUser(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['username'])) ?>')" class="btn btn-danger btn-sm">
											<i class="fas fa-trash"></i> Hapus
										</a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php else: ?>
			<div class="empty-state">
				<i class="fas fa-user-plus"></i>
				<h3>Belum ada pengguna</h3>
				<p>Tambahkan pengguna baru untuk mulai menggunakan sistem.</p>
				<a href="?url=user/tambah" class="btn btn-primary mt-20">
					<i class="fas fa-plus"></i> Tambah Pengguna
				</a>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Script untuk konfirmasi hapus (SweetAlert) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function hapusUser(id, username) {
	Swal.fire({
		title: 'Hapus Pengguna?',
		html: `Anda yakin ingin menghapus pengguna:<br><b>"${username}"</b>?`,
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#f72585',
		cancelButtonColor: '#6c757d',
		confirmButtonText: 'Ya, Hapus!',
		cancelButtonText: 'Batal',
		reverseButtons: true
	}).then((result) => {
		if (result.isConfirmed) {
			window.location.href = `?url=user/hapus&id=${id}`;
		}
	});
}
</script>

