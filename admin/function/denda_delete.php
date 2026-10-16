<?php
require_once __DIR__ . '/../../login/cek_login.php'; cek_login('admin');
// Menghapus 1 data denda berdasarkan id.

require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=denda');
    exit;
}

$id = (int) $_POST['id'];

try {
    $stmt = $conn->prepare("DELETE FROM fines WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    header('Location: ../index.php?page=denda');
} catch (Exception $e) {
    header('Location: ../index.php?page=denda');
}
exit;