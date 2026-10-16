<?php
// Membatalkan pengajuan sewa. Hanya boleh untuk milik sendiri dan selama masih "menunggu".
require_once __DIR__ . '/../../login/cek_login.php';
cek_login('pelanggan');
require_once __DIR__ . '/../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db   = new Database();
    $conn = $db->conn;

    $id          = (int) ($_POST['id'] ?? 0);
    $pelangganId = (int) $_SESSION['id'];

    $stmt = $conn->prepare("DELETE FROM rentals WHERE id = ? AND pelanggan_id = ? AND status_rental = 'menunggu'");
    $stmt->bind_param('ii', $id, $pelangganId);
    $stmt->execute();
}

header('Location: ../../index.php#sewa');
exit;