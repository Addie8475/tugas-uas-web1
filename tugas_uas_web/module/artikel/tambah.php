<?php 
require_once "class/Form.php";

// Pastikan session dan role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['is_login']) || empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?url=artikel/index');
    exit;
}

// Inisialisasi variabel untuk pesan
$success_message = '';
$error_message = '';

// Tangani form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once "class/Database.php";
    $db = new Database();
    
    $judul = $_POST['judul'] ?? '';
    $isi = $_POST['isi'] ?? '';
    
    if (!empty($judul) && !empty($isi)) {
        $result = $db->insert("artikel", [
            'judul' => $judul,
            'isi'   => $isi,
            'tanggal' => date("Y-m-d H:i:s")
        ]);
        
        if ($result) {
            // Redirect dengan pesan sukses
            $judul_encoded = urlencode($judul);
            header("Location: ?url=artikel/index&success=tambah&judul={$judul_encoded}");
            exit;
        } else {
            $error_message = "Gagal menyimpan artikel. Silakan coba lagi.";
        }
    } else {
        $error_message = "Judul dan Isi harus diisi!";
    }
}
?>

<div class="header">
    <h1><i class="fas fa-plus-circle"></i> Tambah Artikel Baru</h1>
    <a href="?url=artikel/index" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<!-- Pesan Error -->
<?php if (!empty($error_message)): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <?= htmlspecialchars($error_message) ?>
</div>
<?php endif; ?>

<!-- Card Form -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-edit"></i> Form Tambah Artikel</h3>
    </div>
    
    <div class="card-body">
        <form method="POST" id="formTambah">
            <div class="form-group">
                <label for="judul" class="form-label">
                    <i class="fas fa-heading"></i> Judul Artikel
                </label>
                <input type="text" 
                       id="judul" 
                       name="judul" 
                       class="form-input"
                       placeholder="Masukkan judul artikel..."
                       value="<?= htmlspecialchars($_POST['judul'] ?? '') ?>"
                       required>
                <div class="form-hint">Maksimal 255 karakter</div>
            </div>
            
            <div class="form-group">
                <label for="isi" class="form-label">
                    <i class="fas fa-file-alt"></i> Isi Artikel
                </label>
                <textarea id="isi" 
                          name="isi" 
                          class="form-textarea"
                          rows="10"
                          placeholder="Tulis isi artikel di sini..."
                          required><?= htmlspecialchars($_POST['isi'] ?? '') ?></textarea>
                <div class="form-hint">Gunakan format HTML jika diperlukan</div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Artikel
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </button>
                <a href="?url=artikel/index" class="btn btn-outline">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>



<script>
// Character counter untuk judul
const judulInput = document.getElementById('judul');
const judulCounter = document.createElement('div');
judulCounter.className = 'character-count';
judulCounter.textContent = '0/255';
judulInput.parentNode.appendChild(judulCounter);

judulInput.addEventListener('input', function() {
    const count = this.value.length;
    judulCounter.textContent = `${count}/255`;
    judulCounter.style.color = count > 255 ? 'var(--danger)' : 'var(--gray)';
});

// Character counter untuk isi
const isiTextarea = document.getElementById('isi');
const isiCounter = document.createElement('div');
isiCounter.className = 'character-count';
isiCounter.textContent = '0 karakter';
isiTextarea.parentNode.appendChild(isiCounter);

isiTextarea.addEventListener('input', function() {
    const count = this.value.length;
    isiCounter.textContent = `${count} karakter`;
});

// Form validation
document.getElementById('formTambah').addEventListener('submit', function(e) {
    const judul = judulInput.value.trim();
    const isi = isiTextarea.value.trim();
    
    if (!judul || !isi) {
        e.preventDefault();
        Swal.fire({
            title: 'Form Tidak Lengkap',
            text: 'Judul dan Isi harus diisi!',
            icon: 'warning',
            confirmButtonColor: '#f8961e'
        });
        return;
    }
    
    if (judul.length > 255) {
        e.preventDefault();
        Swal.fire({
            title: 'Judul Terlalu Panjang',
            text: 'Judul maksimal 255 karakter!',
            icon: 'warning',
            confirmButtonColor: '#f8961e'
        });
        return;
    }
    
    // Tampilkan loading
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Sedang menyimpan artikel',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });
});
</script>