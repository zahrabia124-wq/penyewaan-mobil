<?php
require_once __DIR__ . '/../database/connection.php';
$db = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=armada');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

try {
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: ../index.php?page=armada&status=sukses&pesan=' . urlencode('Armada berhasil dihapus.'));
} catch (Exception $e) {
    // Gagal biasanya karena armada sudah dipakai di data sewa (foreign key)
    header('Location: ../index.php?page=armada&status=gagal&pesan=' . urlencode('Armada tidak bisa dihapus karena sudah dipakai di data sewa. Ubah statusnya jadi perawatan saja.'));
}
exit;