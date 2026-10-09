<?php
// Memproses form "Tambah Pengajuan Sewa".
// Alur: terima data -> cek data -> simpan -> kembali ke daftar.

require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

// Kalau file ini dibuka langsung (bukan dari form), kembalikan ke daftar
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=sewa');
    exit;
}

// 1. Ambil data dari form
$pelanggan_id = (int) $_POST['pelanggan_id'];
$car_id       = (int) $_POST['car_id'];
$tgl_sewa     = $_POST['tgl_sewa'];
$tgl_kembali  = $_POST['tgl_kembali'];

// 2. Cek isian
if ($pelanggan_id == 0 || $car_id == 0 || $tgl_sewa == '' || $tgl_kembali == '' || $tgl_kembali < $tgl_sewa) {
    header('Location: ../index.php?page=sewa-tambah');
    exit;
}

try {
    // 3. Cek mobilnya masih tersedia
    $stmt = $conn->prepare("SELECT status_mobil FROM cars WHERE id = ?");
    $stmt->bind_param('i', $car_id);
    $stmt->execute();
    $car = $stmt->get_result()->fetch_assoc();

    if (!$car || $car['status_mobil'] != 'tersedia') {
        header('Location: ../index.php?page=sewa-tambah');
        exit;
    }

    // 4. Simpan ke database, status awal selalu "menunggu"
    $stmt = $conn->prepare("INSERT INTO rentals (pelanggan_id, car_id, tgl_sewa, tgl_kembali, status_rental)
                            VALUES (?, ?, ?, ?, 'menunggu')");
    $stmt->bind_param('iiss', $pelanggan_id, $car_id, $tgl_sewa, $tgl_kembali);
    $stmt->execute();

    header('Location: ../index.php?page=sewa');
} catch (Exception $e) {
    header('Location: ../index.php?page=sewa-tambah');
}
exit;