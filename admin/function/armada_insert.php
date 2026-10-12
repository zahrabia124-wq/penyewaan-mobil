<?php
require_once __DIR__ . '/../database/connection.php';
$db = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=armada');
    exit;
}

$merk   = trim($_POST['merk'] ?? '');
$plat   = strtoupper(trim($_POST['plat_nomor'] ?? ''));
$harga  = (float)($_POST['harga_per_hari'] ?? 0);
$status = $_POST['status'] ?? 'tersedia';
$owner  = (int)($_POST['pemilik_id'] ?? 0);

if ($merk == '' || $plat == '' || $owner <= 0 || $harga <= 0 || ($status != 'tersedia' && $status != 'disewa' && $status != 'perawatan')) {
    header('Location: ../index.php?page=armada-tambah&status=gagal&pesan=' . urlencode('Data tidak valid, periksa isian form.'));
    exit;
}

// ---- Upload foto (boleh dikosongkan) ----
$foto = '';
if ($_FILES['foto']['name'] != '') {

    // 3 huruf terakhir nama file, contoh: jpg
    $akhir = strtolower(substr($_FILES['foto']['name'], -3));

    if ($akhir != 'jpg' && $akhir != 'png') {
        header('Location: ../index.php?page=armada-tambah&status=gagal&pesan=' . urlencode('Foto harus berformat JPG atau PNG.'));
        exit;
    }

    // Nama foto ditambah waktu supaya tidak kembar
    $foto = time() . '_' . $_FILES['foto']['name'];

    // Lokasi folder foto (di sebelah folder admin). Kalau belum ada, dibuat otomatis
    $folder = __DIR__ . '/../../uploads/mobil/';
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    // Simpan foto ke folder tersebut
    move_uploaded_file($_FILES['foto']['tmp_name'], $folder . $foto);
}

try {
    $stmt = $conn->prepare("INSERT INTO cars (pemilik_id, merk, plat_nomor, harga_sewa_per_hari, status_mobil, foto) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issdss', $owner, $merk, $plat, $harga, $status, $foto);
    $stmt->execute();
    header('Location: ../index.php?page=armada&status=sukses&pesan=' . urlencode('Armada berhasil ditambahkan.'));
} catch (Exception $e) {
    $pesan = ($e->getCode() == 1062) ? 'Plat nomor sudah terdaftar.' : 'Gagal menyimpan: ' . $e->getMessage();
    header('Location: ../index.php?page=armada-tambah&status=gagal&pesan=' . urlencode($pesan));
}
exit;