<?php
require_once "class/Database.php";

$db = new Database();

$judul = $_POST['judul'] ?? '';
$isi = $_POST['isi'] ?? '';

if(!empty($judul) && !empty($isi)) {
    $db->insert("artikel", [
        'judul' => $judul,
        'isi'   => $isi,
        'tanggal' => date("Y-m-d H:i:s")
    ]);
    
    
    exit;
} else {
    echo "<script>alert('Judul dan Isi harus diisi!'); window.history.back();</script>";
}
?>