<?php
// Menghapus 1 pengguna berdasarkan id.
// Tidak bisa menghapus akun sendiri, dan tidak bisa kalau pengguna masih punya data sewa/armada.
require_once __DIR__ . '/../../login/cek_login.php';
cek_login('admin');
require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=pengguna');
    exit;
}

$id     = (int) ($_POST['id'] ?? 0);
$idSaya = (int) ($_SESSION['id'] ?? 0);

if ($id == 0 || $id == $idSaya) {
    header('Location: ../index.php?page=pengguna&status=gagal&pesan=' . urlencode('Pengguna tidak bisa dihapus.'));
    exit;
}

try {
    // Hanya pelanggan dan pemilik yang boleh dihapus lewat halaman ini (admin tidak)
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role IN ('pelanggan', 'pemilik')");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    if ($stmt->affected_rows == 0) {
        header('Location: ../index.php?page=pengguna&status=gagal&pesan=' . urlencode('Pengguna tidak ditemukan atau tidak boleh dihapus.'));
        exit;
    }

    header('Location: ../index.php?page=pengguna&status=sukses&pesan=' . urlencode('Pengguna berhasil dihapus.'));
} catch (Exception $e) {
    // Kode 1451 = masih dipakai di tabel lain (sewa / armada)
    $pesan = ($e->getCode() == 1451) ? 'Pengguna masih punya data sewa atau armada, tidak bisa dihapus.' : 'Gagal menghapus: ' . $e->getMessage();
    header('Location: ../index.php?page=pengguna&status=gagal&pesan=' . urlencode($pesan));
}
exit;