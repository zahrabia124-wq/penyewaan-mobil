<?php
require_once __DIR__ . '/../../login/cek_login.php'; cek_login('admin');
// Memproses form "Edit Armada".
require_once __DIR__ . '/../database/connection.php';
$db = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=armada');
    exit;
}

$id     = (int) ($_POST['id'] ?? 0);
$merk   = trim($_POST['merk'] ?? '');
$plat   = strtoupper(trim($_POST['plat_nomor'] ?? ''));
$harga  = (float) ($_POST['harga_per_hari'] ?? 0);
$status = $_POST['status'] ?? 'tersedia';
$owner  = (int) ($_POST['pemilik_id'] ?? 0);

if ($id <= 0 || $merk == '' || $plat == '' || $owner <= 0 || $harga <= 0 || !in_array($status, ['tersedia', 'disewa', 'perawatan'])) {
    header('Location: ../index.php?page=armada-edit&id=' . $id . '&status=gagal&pesan=' . urlencode('Data tidak valid, periksa isian form.'));
    exit;
}

$folder = realpath(__DIR__ . '/../../') . '/foto/';

// Ambil nama foto lama
$stmt = $conn->prepare("SELECT foto FROM cars WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$lama = $stmt->get_result()->fetch_assoc();
$stmt->close();
$fotoLama = $lama['foto'] ?? '';

// ---- Proses upload foto baru (opsional) ----
$fotoBaru = '';
if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
    $f = $_FILES['foto'];

    if ($f['error'] !== UPLOAD_ERR_OK) {
        header('Location: ../index.php?page=armada-edit&id=' . $id . '&status=gagal&pesan=' . urlencode('Upload foto gagal (kode ' . $f['error'] . '). Cek ukuran file.'));
        exit;
    }
    if ($f['size'] > 2 * 1024 * 1024) {
        header('Location: ../index.php?page=armada-edit&id=' . $id . '&status=gagal&pesan=' . urlencode('Ukuran foto maksimal 2 MB.'));
        exit;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($f['tmp_name']);
    $boleh = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif', 'image/bmp' => 'bmp', 'image/avif' => 'avif'];
    if (!isset($boleh[$mime])) {
        header('Location: ../index.php?page=armada-edit&id=' . $id . '&status=gagal&pesan=' . urlencode('Foto harus berformat JPG, PNG, WEBP, GIF, BMP, atau AVIF.'));
        exit;
    }

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $fotoBaru = 'mobil_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $boleh[$mime];
    if (!move_uploaded_file($f['tmp_name'], $folder . $fotoBaru)) {
        header('Location: ../index.php?page=armada-edit&id=' . $id . '&status=gagal&pesan=' . urlencode('Gagal menyimpan file foto ke folder foto.'));
        exit;
    }
}

try {
    if ($fotoBaru != '') {
        $stmt = $conn->prepare("UPDATE cars SET pemilik_id = ?, merk = ?, plat_nomor = ?, harga_sewa_per_hari = ?, status_mobil = ?, foto = ? WHERE id = ?");
        $stmt->bind_param('issdssi', $owner, $merk, $plat, $harga, $status, $fotoBaru, $id);
    } else {
        // Tidak ada foto baru: foto lama dibiarkan
        $stmt = $conn->prepare("UPDATE cars SET pemilik_id = ?, merk = ?, plat_nomor = ?, harga_sewa_per_hari = ?, status_mobil = ? WHERE id = ?");
        $stmt->bind_param('issdsi', $owner, $merk, $plat, $harga, $status, $id);
    }
    $stmt->execute();

    // Hapus file foto lama kalau sudah diganti
    if ($fotoBaru != '' && $fotoLama != '' && is_file($folder . basename($fotoLama))) {
        unlink($folder . basename($fotoLama));
    }

    header('Location: ../index.php?page=armada&status=sukses&pesan=' . urlencode('Armada berhasil diperbarui.'));
} catch (Exception $e) {
    if ($fotoBaru != '' && file_exists($folder . $fotoBaru)) {
        unlink($folder . $fotoBaru);
    }
    $pesan = ($e->getCode() == 1062) ? 'Plat nomor sudah dipakai armada lain.' : 'Gagal menyimpan: ' . $e->getMessage();
    header('Location: ../index.php?page=armada-edit&id=' . $id . '&status=gagal&pesan=' . urlencode($pesan));
}
exit;