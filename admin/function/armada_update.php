<?php
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

try {
    $stmt = $conn->prepare("UPDATE cars SET pemilik_id = ?, merk = ?, plat_nomor = ?, harga_sewa_per_hari = ?, status_mobil = ? WHERE id = ?");
    $stmt->bind_param('issdsi', $owner, $merk, $plat, $harga, $status, $id);
    $stmt->execute();
    header('Location: ../index.php?page=armada&status=sukses&pesan=' . urlencode('Armada berhasil diperbarui.'));
} catch (Exception $e) {
    $pesan = ($e->getCode() == 1062) ? 'Plat nomor sudah dipakai armada lain.' : 'Gagal menyimpan: ' . $e->getMessage();
    header('Location: ../index.php?page=armada-edit&id=' . $id . '&status=gagal&pesan=' . urlencode($pesan));
}
exit;