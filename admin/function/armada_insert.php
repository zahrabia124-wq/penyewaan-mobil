<?php
require_once __DIR__ . '/../../login/cek_login.php'; cek_login('admin');
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

if ($merk == '' || $plat == '' || $owner <= 0 || $harga <= 0 || !in_array($status, ['tersedia', 'disewa', 'perawatan'])) {
    header('Location: ../index.php?page=armada-tambah&status=gagal&pesan=' . urlencode('Data tidak valid, periksa isian form.'));
    exit;
}

// ---- Proses upload foto (opsional) ----
$namaFoto = '';
if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
    $f = $_FILES['foto'];

    if ($f['error'] !== UPLOAD_ERR_OK) {
        header('Location: ../index.php?page=armada-tambah&status=gagal&pesan=' . urlencode('Upload foto gagal (kode ' . $f['error'] . '). Cek ukuran file.'));
        exit;
    }
    if ($f['size'] > 2 * 1024 * 1024) {
        header('Location: ../index.php?page=armada-tambah&status=gagal&pesan=' . urlencode('Ukuran foto maksimal 2 MB.'));
        exit;
    }

    // Cek tipe file asli (bukan cuma dari ekstensi)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($f['tmp_name']);
    $boleh = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif', 'image/bmp' => 'bmp', 'image/avif' => 'avif'];
    if (!isset($boleh[$mime])) {
        header('Location: ../index.php?page=armada-tambah&status=gagal&pesan=' . urlencode('Foto harus berformat JPG, PNG, WEBP, GIF, BMP, atau AVIF.'));
        exit;
    }

    // Folder foto: penyewaan-mobil/foto/
    $folder = realpath(__DIR__ . '/../../') . '/foto/';
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $namaFoto = 'mobil_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $boleh[$mime];
    if (!move_uploaded_file($f['tmp_name'], $folder . $namaFoto)) {
        header('Location: ../index.php?page=armada-tambah&status=gagal&pesan=' . urlencode('Gagal menyimpan file foto ke folder foto.'));
        exit;
    }
}

try {
    $stmt = $conn->prepare("INSERT INTO cars (pemilik_id, merk, plat_nomor, harga_sewa_per_hari, status_mobil, foto) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issdss', $owner, $merk, $plat, $harga, $status, $namaFoto);
    $stmt->execute();
    header('Location: ../index.php?page=armada&status=sukses&pesan=' . urlencode('Armada berhasil ditambahkan.'));
} catch (Exception $e) {
    // Kalau simpan ke database gagal, hapus foto yang sudah terlanjur diupload
    if ($namaFoto != '' && file_exists($folder . $namaFoto)) {
        unlink($folder . $namaFoto);
    }
    $pesan = ($e->getCode() == 1062) ? 'Plat nomor sudah terdaftar.' : 'Gagal menyimpan: ' . $e->getMessage();
    header('Location: ../index.php?page=armada-tambah&status=gagal&pesan=' . urlencode($pesan));
}
exit;