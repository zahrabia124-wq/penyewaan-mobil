<?php
// Membatalkan (menghapus) 1 pengajuan sewa milik pelanggan berdasarkan id.
// Hanya bisa kalau pengajuan itu milik pelanggan ini dan statusnya masih "menunggu".
session_start();

require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=sewa');
    exit;
}

// Sementara sebelum ada login: pelanggan id 1 (samakan dengan index.php)
$pelangganId = $_SESSION['id'] ?? 1;

$id = (int) $_POST['id'];

try {
    $stmt = $conn->prepare("DELETE FROM rentals WHERE id = ? AND pelanggan_id = ? AND status_rental = 'menunggu'");
    $stmt->bind_param('ii', $id, $pelangganId);
    $stmt->execute();

    header('Location: ../index.php?page=sewa');
} catch (Exception $e) {
    header('Location: ../index.php?page=sewa');
}
exit;