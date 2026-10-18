<?php
require_once __DIR__ . '/../../login/cek_login.php'; cek_login('admin');
// Memproses form "Edit Denda".
// Denda disimpan di tabel returns (kolom: tgl_dikembalikan, denda, kondisi_mobil).

require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=denda');
    exit;
}

// 1. Ambil data dari form (nama field harus sama dengan di pages/denda/edit.php)
$id               = (int) ($_POST['id'] ?? 0);
$tgl_dikembalikan = $_POST['tgl_dikembalikan'] ?? '';
$denda            = (float) ($_POST['denda'] ?? 0);
$kondisi_mobil    = trim($_POST['kondisi_mobil'] ?? '');

// 2. Cek isian
if ($id <= 0 || $denda <= 0 || $tgl_dikembalikan == '') {
    die('Data belum lengkap. <a href="../index.php?page=denda-edit&id=' . $id . '">Kembali</a>');
}

// 3. Simpan perubahan ke database
try {
    $stmt = $conn->prepare("UPDATE returns SET tgl_dikembalikan = ?, denda = ?, kondisi_mobil = ? WHERE id = ?");
    $stmt->bind_param('sdsi', $tgl_dikembalikan, $denda, $kondisi_mobil, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: ../index.php?page=denda');
} catch (Exception $e) {
    die('Gagal menyimpan perubahan denda: ' . htmlspecialchars($e->getMessage()) . ' <a href="../index.php?page=denda-edit&id=' . $id . '">Kembali</a>');
}
exit;