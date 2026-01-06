<?php
require_once "class/Database.php";
$db = new Database();
// Pastikan session tersedia untuk cek role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header("Location: ?url=artikel");
    exit;
}

$artikel = $db->getById('artikel', $id);
if (!$artikel) {
    header("Location: ?url=artikel&error=not_found");
    exit;
}

function formatTanggal($date) {
    if(empty($date)) return '-';
    return date('d M Y', strtotime($date));
}
?>

<div class="header">
    <h1><i class="fas fa-newspaper"></i> <?= htmlspecialchars($artikel['judul']) ?></h1>
    <div class="header-actions">
        <a href="?url=artikel" class="btn btn-light">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="?url=artikel/ubah&id=<?= $artikel['id'] ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="#" onclick="hapusArtikel(<?= $artikel['id'] ?>, '<?= htmlspecialchars(addslashes($artikel['judul'])) ?>')" class="btn btn-danger">
                <i class="fas fa-trash"></i> Hapus
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card mt-16">
    <div class="card-header">
        <h3><i class="fas fa-file-alt"></i> Detail Artikel</h3>
    </div>
    <div class="card-body">
        <div class="article-meta">
            <strong>Tanggal:</strong> <?= formatTanggal($artikel['tanggal']) ?>
            &nbsp;—&nbsp;
            <strong>ID:</strong> <span class="badge badge-success">#<?= htmlspecialchars($artikel['id']) ?></span>
        </div>

        <div class="article-content">
            <?php
            // Isi artikel mungkin mengandung HTML; tampilkan apa adanya (asumsi konten terpercaya dari admin)
            echo $artikel['isi'];
            ?>
        </div>
    </div>
</div>

<!-- SweetAlert dan fungsi hapus (sama seperti di index.php) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function hapusArtikel(id, judul) {
    Swal.fire({
        title: 'Hapus Artikel?',
        html: `Anda yakin ingin menghapus artikel:<br><b>"${judul}"</b>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f72585',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `?url=artikel/hapus&id=${id}&success=hapus`;
        }
    });
}
</script>
