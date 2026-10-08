<?php
// Menghapus 1 pengajuan sewa berdasarkan id.

require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=sewa');
    exit;
}

$id = (int) $_POST['id'];

try {
    $stmt = $conn->prepare("DELETE FROM rentals WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    header('Location: ../index.php?page=sewa');
} catch (Exception $e) {
    // Biasanya gagal karena sewa ini sudah punya denda / invoice
    header('Location: ../index.php?page=sewa');
}
exit;