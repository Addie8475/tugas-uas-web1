<?php
require_once "class/Database.php";

// Pastikan session dan role
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['is_login']) || empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ?url=artikel/index');
    exit;
}

$id = $_GET['id'] ?? 0;
$db = new Database();

if (!$id) {
    header("Location: ?url=artikel/index&error=invalid_id");
    exit;
}

$artikel = $db->getById("artikel", $id);

if (!$artikel) {
    header("Location: ?url=artikel/index&error=not_found");
    exit;
}

// Variabel untuk pesan
$success_message = '';
$error_message = '';

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'] ?? '';
    $isi   = $_POST['isi'] ?? '';

    if (!empty($judul) && !empty($isi)) {
        $result = $db->update("artikel", [
            'judul' => $judul,
            'isi'   => $isi,
            'tanggal_ubah'   => $tanggal_ubah = date('Y-m-d H:i:s')
        ], $id);

        if ($result) {
            // Redirect dengan pesan sukses
            $judul_encoded = urlencode($judul);
            header("Location: ?url=artikel/index&success=edit&judul={$judul_encoded}");
            exit;
        } else {
            $error_message = "Gagal mengupdate data! Silakan coba lagi.";
        }
    } else {
        $error_message = "Judul dan Isi harus diisi!";
    }
}
?>

<div class="header">
    <h1><i class="fas fa-edit"></i> Edit Artikel</h1>
    <div class="header-actions">
        <a href="?url=artikel/index" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<!-- Info Artikel -->
<div class="card mb-20">
    <div class="card-header">
        <h3><i class="fas fa-info-circle"></i> Informasi Artikel</h3>
    </div>
    <div class="card-body">
        <div class="article-info">
            <div class="info-item">
                <span class="info-label">ID Artikel:</span>
                <span class="badge badge-success">#<?= htmlspecialchars($artikel['id']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Dibuat:</span>
                <span><?= date('d F Y', strtotime($artikel['tanggal'])) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Terakhir Diubah:</span>
                <span><?= date('d F Y', strtotime($artikel['tanggal_ubah'])) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Pesan Error -->
<?php if (!empty($error_message)): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i>
    <?= htmlspecialchars($error_message) ?>
</div>
<?php endif; ?>

<!-- Card Form Edit -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-edit"></i> Form Edit Artikel</h3>
    </div>
    
    <div class="card-body">
        <form method="POST" id="formEdit">
            <div class="form-group">
                <label for="judul" class="form-label">
                    <i class="fas fa-heading"></i> Judul Artikel
                </label>
                <input type="text" 
                       id="judul" 
                       name="judul" 
                       class="form-input"
                       value="<?= htmlspecialchars($artikel['judul']) ?>"
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
                          rows="15"
                          required><?= htmlspecialchars($artikel['isi']) ?></textarea>
                <div class="form-hint">Gunakan format HTML jika diperlukan</div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <button type="button" onclick="resetForm()" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="button" onclick="konfirmasiBatal()" class="btn btn-outline">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </form>
    </div>
</div>




</style>

<script>
// Simpan data asli untuk reset
const originalData = {
    judul: document.getElementById('judul').value,
    isi: document.getElementById('isi').value
};

// Character counter untuk judul
const judulInput = document.getElementById('judul');
const judulCounter = document.createElement('div');
judulCounter.className = 'character-count';
judulCounter.textContent = originalData.judul.length + '/255';
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
isiCounter.textContent = originalData.isi.length + ' karakter';
isiTextarea.parentNode.appendChild(isiCounter);

isiTextarea.addEventListener('input', function() {
    const count = this.value.length;
    isiCounter.textContent = `${count} karakter`;
});

// Fungsi reset form
function resetForm() {
    Swal.fire({
        title: 'Reset Form?',
        text: 'Semua perubahan akan dikembalikan ke nilai semula',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f8961e',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Reset',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            judulInput.value = originalData.judul;
            isiTextarea.value = originalData.isi;
            judulCounter.textContent = originalData.judul.length + '/255';
            isiCounter.textContent = originalData.isi.length + ' karakter';
            
            Swal.fire({
                title: 'Berhasil!',
                text: 'Form telah direset',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

// Fungsi konfirmasi batal
function konfirmasiBatal() {
    const judulCurrent = judulInput.value;
    const isiCurrent = isiTextarea.value;
    
    const hasChanges = judulCurrent !== originalData.judul || isiCurrent !== originalData.isi;
    
    if (hasChanges) {
        Swal.fire({
            title: 'Ada Perubahan Belum Disimpan',
            text: 'Apakah Anda yakin ingin membatalkan? Perubahan yang belum disimpan akan hilang.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f72585',
            cancelButtonColor: '#4361ee',
            confirmButtonText: 'Ya, Batal',
            cancelButtonText: 'Lanjut Edit'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '?url=artikel/index';
            }
        });
    } else {
        window.location.href = '?url=artikel/index';
    }
}

// Form validation
document.getElementById('formEdit').addEventListener('submit', function(e) {
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
        text: 'Sedang menyimpan perubahan',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });
});

// Auto-save draft (opsional)
let saveTimeout;
function autoSaveDraft() {
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(() => {
        localStorage.setItem(`artikel_draft_${<?= $id ?>}`, JSON.stringify({
            judul: judulInput.value,
            isi: isiTextarea.value,
            timestamp: new Date().toISOString()
        }));
    }, 2000);
}

judulInput.addEventListener('input', autoSaveDraft);
isiTextarea.addEventListener('input', autoSaveDraft);

// Load draft jika ada
document.addEventListener('DOMContentLoaded', function() {
    const draft = localStorage.getItem(`artikel_draft_${<?= $id ?>}`);
    if (draft) {
        Swal.fire({
            title: 'Draft Ditemukan',
            text: 'Ada draft yang belum disimpan. Ingin memulihkannya?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#4361ee',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Pulihkan',
            cancelButtonText: 'Hapus Draft'
        }).then((result) => {
            if (result.isConfirmed) {
                const data = JSON.parse(draft);
                judulInput.value = data.judul;
                isiTextarea.value = data.isi;
                judulCounter.textContent = data.judul.length + '/255';
                isiCounter.textContent = data.isi.length + ' karakter';
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                localStorage.removeItem(`artikel_draft_${<?= $id ?>}`);
            }
        });
    }
});

// Clear draft saat form berhasil disubmit
document.getElementById('formEdit').addEventListener('submit', function() {
    localStorage.removeItem(`artikel_draft_${<?= $id ?>}`);
});
</script>
