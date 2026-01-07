    </div> <!-- tutup container -->
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Script untuk SweetAlert
        <?php if(isset($_GET['success']) && $_GET['success'] == 'hapus'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Artikel berhasil dihapus.',
                icon: 'success',
                confirmButtonColor: '#4361ee',
                timer: 2000,
                timerProgressBar: true
            });
        });
        <?php endif; ?>
        
        <?php if(isset($_GET['success']) && $_GET['success'] == 'tambah'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Artikel berhasil ditambahkan.',
                icon: 'success',
                confirmButtonColor: '#4361ee',
                timer: 2000,
                timerProgressBar: true
            });
        });
        <?php endif; ?>
        
        <?php if(isset($_GET['success']) && $_GET['success'] == 'edit'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Artikel berhasil diperbarui.',
                icon: 'success',
                confirmButtonColor: '#4361ee',
                timer: 2000,
                timerProgressBar: true
            });
        });
        <?php endif; ?>
    </script>
</body>
</html>
<?php
ob_end_flush(); // Akhiri output buffering
?>