<?php
// Memproses form "Terbitkan Invoice": hitung total tagihan lalu simpan ke tabel payments.

require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=invoice');
    exit;
}

$rental_id = (int) $_POST['rental_id'];

if ($rental_id == 0) {
    header('Location: ../index.php?page=invoice-tambah');
    exit;
}

try {
    // 1. Ambil data sewa + harga mobil
    $stmt = $conn->prepare("SELECT rentals.tgl_sewa, rentals.tgl_kembali, cars.harga_sewa_per_hari
                            FROM rentals
                            JOIN cars ON cars.id = rentals.car_id
                            WHERE rentals.id = ?");
    $stmt->bind_param('i', $rental_id);
    $stmt->execute();
    $sewa = $stmt->get_result()->fetch_assoc();

    if (!$sewa) {
        header('Location: ../index.php?page=invoice-tambah');
        exit;
    }

    // 2. Total sewa = lama sewa (hari) x harga per hari, minimal 1 hari
    $hari = (strtotime($sewa['tgl_kembali']) - strtotime($sewa['tgl_sewa'])) / 86400;
    if ($hari < 1) {
        $hari = 1;
    }
    $totalSewa = $hari * $sewa['harga_sewa_per_hari'];

    // 3. Jumlahkan semua denda pada sewa ini
    $stmt = $conn->prepare("SELECT COALESCE(SUM(jumlah), 0) AS total_denda FROM fines WHERE rental_id = ?");
    $stmt->bind_param('i', $rental_id);
    $stmt->execute();
    $denda = $stmt->get_result()->fetch_assoc();

    $total = $totalSewa + $denda['total_denda'];

    // 4. Simpan, status awal otomatis "belum_bayar"
    $stmt = $conn->prepare("INSERT INTO payments (rental_id, total_bayar) VALUES (?, ?)");
    $stmt->bind_param('id', $rental_id, $total);
    $stmt->execute();

    header('Location: ../index.php?page=invoice');
} catch (Exception $e) {
    header('Location: ../index.php?page=invoice-tambah');
}
exit;