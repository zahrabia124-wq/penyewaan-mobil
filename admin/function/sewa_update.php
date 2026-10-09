<?php
// Memproses form "Proses Pengajuan": ubah status sewa, lalu sesuaikan status mobilnya.
// Disetujui -> mobil jadi "disewa"
// Menunggu / ditolak / selesai -> mobil kembali "tersedia"

require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

// Kalau file ini dibuka langsung (bukan dari form), kembalikan ke daftar
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=sewa');
    exit;
}

// 1. Ambil data dari form
$id     = (int) $_POST['id'];
$status = $_POST['status_rental'] ?? '';

// 2. Cek isian
$pilihan = ['menunggu', 'disetujui', 'ditolak', 'selesai'];
if ($id == 0 || !in_array($status, $pilihan)) {
    header('Location: ../index.php?page=sewa-edit&id=' . $id);
    exit;
}

try {
    // 3. Ambil mobil yang dipakai di sewa ini
    $stmt = $conn->prepare("SELECT car_id FROM rentals WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $sewa = $stmt->get_result()->fetch_assoc();

    if (!$sewa) {
        header('Location: ../index.php?page=sewa');
        exit;
    }

    // 4. Tentukan status mobil mengikuti status sewa
    if ($status == 'disetujui') {
        $statusMobil = 'disewa';
    } else {
        $statusMobil = 'tersedia';
    }

    // 5. Simpan status sewa dan status mobil sekaligus
    $conn->begin_transaction();

    $stmt = $conn->prepare("UPDATE rentals SET status_rental = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE cars SET status_mobil = ? WHERE id = ?");
    $stmt->bind_param('si', $statusMobil, $sewa['car_id']);
    $stmt->execute();

    $conn->commit();

    header('Location: ../index.php?page=sewa');
} catch (Exception $e) {
    $conn->rollback();
    header('Location: ../index.php?page=sewa-edit&id=' . $id);
}
exit;