
<?php
require_once __DIR__ . '/../../login/cek_login.php'; cek_login('admin');
// Memproses form "Edit Invoice": ...

require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

// Kalau file ini dibuka langsung (bukan dari form), kembalikan ke daftar
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=invoice');
    exit;
}

// 1. Ambil data dari form
$id     = (int) $_POST['id'];
$status = $_POST['status_pembayaran'] ?? '';

// 2. Cek isian
if ($id == 0 || !in_array($status, ['belum_bayar', 'lunas'])) {
    header('Location: ../index.php?page=invoice-edit&id=' . $id);
    exit;
}

try {
    // 3. Simpan status pembayaran
    $stmt = $conn->prepare("UPDATE payments SET status_pembayaran = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();

    header('Location: ../index.php?page=invoice');
} catch (Exception $e) {
    header('Location: ../index.php?page=invoice-edit&id=' . $id);
}
exit;