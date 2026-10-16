<?php
require_once __DIR__ . '/../../login/cek_login.php'; cek_login('admin');
// Mengubah status_pembayaran di tabel payments (lunas / belum_bayar).
require_once __DIR__ . '/../database/connection.php';
$db = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';

    // Hanya terima dua nilai yang sesuai dengan enum di database
    if ($id > 0 && in_array($status, ['lunas', 'belum_bayar'], true)) {
        $stmt = $conn->prepare("UPDATE payments SET status_pembayaran = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: ../index.php?page=invoice');
exit;