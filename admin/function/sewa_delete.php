<?php
require_once __DIR__ . '/../../login/cek_login.php'; cek_login('admin');
// Menghapus 1 pengajuan sewa (tabel rentals) berdasarkan id.
// Kalau sewa itu sedang disetujui, mobilnya dikembalikan jadi "tersedia".
// Tidak bisa dihapus kalau sudah punya denda (returns) atau invoice (payments).
require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=sewa');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

function balik($status, $pesan)
{
    header('Location: ../index.php?page=sewa&status=' . $status . '&pesan=' . urlencode($pesan));
    exit;
}

if ($id <= 0) {
    balik('gagal', 'Pengajuan tidak ditemukan.');
}

try {
    $stmt = $conn->prepare("SELECT car_id, status_rental FROM rentals WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $sewa = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$sewa) {
        balik('gagal', 'Pengajuan tidak ditemukan.');
    }

    $conn->begin_transaction();

    $stmt = $conn->prepare("DELETE FROM rentals WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // Sewa yang sedang disetujui membuat mobil berstatus "disewa" -> kembalikan jadi "tersedia"
    if ($sewa['status_rental'] === 'disetujui') {
        $stmt = $conn->prepare("UPDATE cars SET status_mobil = 'tersedia' WHERE id = ?");
        $stmt->bind_param('i', $sewa['car_id']);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
    balik('sukses', 'Pengajuan berhasil dihapus.');
} catch (Exception $e) {
    $conn->rollback();
    // Kode 1451 = masih dipakai di tabel lain (denda / invoice)
    $pesan = ($e->getCode() == 1451)
        ? 'Pengajuan ini sudah punya denda atau invoice, jadi tidak bisa dihapus.'
        : 'Gagal menghapus: ' . $e->getMessage();
    balik('gagal', $pesan);
}