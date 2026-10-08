<?php
// Menghapus 1 invoice berdasarkan id.

require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=invoice');
    exit;
}

$id = (int) $_POST['id'];

try {
    $stmt = $conn->prepare("DELETE FROM payments WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
} catch (Exception $e) {
    // abaikan, kembali ke daftar
}

header('Location: ../index.php?page=invoice');
exit;