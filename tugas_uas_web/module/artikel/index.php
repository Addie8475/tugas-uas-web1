<?php
require_once "class/Database.php";
$db = new Database();
// Pastikan session tersedia untuk cek role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Pagination setup
$limit = 5; // items per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Pencarian (optional)
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$qParam = $search !== '' ? '&q=' . rawurlencode($search) : '';

if($search !== '') {
    // aman: escape input menggunakan real_escape_string
    $q = $db->conn->real_escape_string($search);
    $where = "WHERE judul LIKE '%$q%' OR isi LIKE '%$q%'";

    // Hitung total hasil pencarian
    $resCount = $db->query("SELECT COUNT(*) as total FROM artikel $where");
    $rowCount = $resCount ? $resCount->fetch_assoc() : ['total' => 0];
    $total_artikel = intval($rowCount['total']);

    // Ambil data halaman saat ini (dengan filter)
    $sql = "SELECT * FROM artikel $where ORDER BY tanggal DESC LIMIT $limit OFFSET $offset";
    $res = $db->query($sql);
    $data = [];
    if($res && $res->num_rows > 0) {
        while($r = $res->fetch_assoc()) {
            $data[] = $r;
        }
    }
} else {
    // Tanpa pencarian: perilaku sebelumnya
    $total_artikel = $db->count('artikel');
    $data = $db->getPaged('artikel', $limit, $offset, 'tanggal DESC');
}

$total_pages = $total_artikel > 0 ? ceil($total_artikel / $limit) : 1;

// Format tanggal
function formatTanggal($date) {
    if(empty($date)) return '-';
    return date('d M Y', strtotime($date));
}
?>

<!-- Header -->
<div class="header">
    <h1><i class="fas fa-newspaper"></i> Daftar Artikel</h1>
    <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="?url=artikel/tambah" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Artikel Baru
        </a>
    <?php endif; ?>
</div>

<!-- Statistik -->
<div class="stat-card">
    <div class="stat-icon articles">
        <i class="fas fa-file-alt"></i>
    </div>
    <div class="stat-content">
        <h3><?= $total_artikel ?></h3>
        <p>Total Artikel</p>
    </div>
</div>

<!-- Card Utama -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Semua Artikel</h3>
        <form method="get" action="" class="search-form">
            <input type="hidden" name="url" value="artikel">
            <input type="text" name="q" placeholder="Cari judul atau isi..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" class="search-input">
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Cari</button>
            <?php if(isset($_GET['q']) && $_GET['q'] !== ''): ?>
                <a href="?url=artikel" class="btn btn-light">Reset</a>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="card-body">
        <?php if(isset($_GET['success']) && $_GET['success'] == 'tambah'): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Artikel berhasil ditambahkan!
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['success']) && $_GET['success'] == 'edit'): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Artikel berhasil diperbarui!
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['success']) && $_GET['success'] == 'hapus'): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Artikel berhasil dihapus!
            </div>
        <?php endif; ?>

        <?php if($search !== ''): ?>
            <div class="search-info">
                <strong>Hasil pencarian untuk:</strong> "<?= htmlspecialchars($search) ?>" &nbsp;—&nbsp; <strong><?= $total_artikel ?></strong> hasil
            </div>
        <?php endif; ?>

        <?php if($total_artikel > 0): ?>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Judul Artikel</th>
                            <th width="200">Tanggal</th>
                            <th width="350">Konten Preview</th>
                    <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <th width="150" class="text-center">Aksi</th>
                    <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data as $d): 
                            $preview = strip_tags($d['isi']);
                            $preview = strlen($preview) > 150 ? substr($preview, 0, 150) . '...' : $preview;
                        ?>
                            <tr>
                                <td>
                                    <span class="badge badge-success">#<?= htmlspecialchars($d['id']) ?></span>
                                </td>
                                <td>
                                    <div class="article-title"><a href="?url=artikel/view&id=<?= $d['id'] ?>"><?= htmlspecialchars($d['judul']) ?></a></div>
                                    <div class="article-date">
                                        <i class="far fa-calendar"></i> 
                                        <?= formatTanggal($d['tanggal']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="article-date">
                                        <?= formatTanggal($d['tanggal']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="truncate">
                                        <?= htmlspecialchars($preview) ?>
                                    </div>
                                </td>
                                <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <td class="text-center">
                                    <div class="action-buttons">
                                            <a href="?url=artikel/ubah&id=<?= $d['id'] ?>" 
                                               class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="#" 
                                               onclick="hapusArtikel(<?= $d['id'] ?>, '<?= htmlspecialchars(addslashes($d['judul'])) ?>')" 
                                               class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if($total_pages > 1): ?>
                <div class="pagination-wrapper">
                    <?php if($page > 1): ?>
                        <a class="btn btn-light" href="?url=artikel&page=<?= $page-1 ?><?= $qParam ?>">&laquo; Prev</a>
                    <?php endif; ?>

                    <?php for($p = 1; $p <= $total_pages; $p++): ?>
                        <a class="btn btn-page <?= $p == $page ? 'active' : '' ?>" href="?url=artikel&page=<?= $p ?><?= $qParam ?>"><?= $p ?></a> 
                    <?php endfor; ?>

                    <?php if($page < $total_pages): ?>
                        <a class="btn btn-light" href="?url=artikel&page=<?= $page+1 ?><?= $qParam ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Belum ada artikel</h3>
                <p>Mulai dengan membuat artikel pertama Anda</p>
                <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="?url=artikel/tambah" class="btn btn-primary mt-20">
                        <i class="fas fa-plus"></i> Buat Artikel Pertama
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Script untuk SweetAlert -->
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

// Animasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.data-table tbody tr');
    rows.forEach((row, index) => {
        row.style.animationDelay = `${index * 0.1}s`;
        row.style.animation = 'fadeInUp 0.5s ease forwards';
        row.style.opacity = '0';
    });
});


</script>