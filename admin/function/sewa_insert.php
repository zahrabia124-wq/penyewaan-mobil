<?php
// Memproses form "Tambah Pengajuan Sewa".
// Alur: terima data -> cek data -> hitung total -> simpan -> kembali ke daftar.

require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

// Kalau file ini dibuka langsung (bukan dari form), kembalikan ke daftar
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=sewa');
    exit;
}

// 1. Ambil data dari form
$nama    = trim($_POST['nama_penyewa']);
$hp      = trim($_POST['no_hp']);
$car_id  = (int) $_POST['car_id'];
$mulai   = $_POST['tanggal_mulai'];
$selesai = $_POST['tanggal_selesai'];

// 2. Cek isian
if ($nama == '' || $hp == '' || $car_id == 0 || $mulai == '' || $selesai == '' || $selesai < $mulai) {
    header('Location: ../index.php?page=sewa-tambah');
    exit;
}

try {
    // 3. Ambil harga mobil, sekaligus cek mobilnya masih tersedia
    $stmt = $conn->prepare("SELECT harga_per_hari, status FROM cars WHERE id = ?");
    $stmt->bind_param('i', $car_id);
    $stmt->execute();
    $car = $stmt->get_result()->fetch_assoc();

    if (!$car || $car['status'] != 'tersedia') {
        header('Location: ../index.php?page=sewa-tambah');
        exit;
    }

    // 4. Hitung lama sewa (hari) dan total harga
    $hari = (strtotime($selesai) - strtotime($mulai)) / 86400;
    if ($hari < 1) {
        $hari = 1;
    }
    $total = $hari * $car['harga_per_hari'];

    // 5. Simpan ke database, status awal selalu "pengajuan"
    $stmt = $conn->prepare("INSERT INTO rentals (car_id, nama_penyewa, no_hp, tanggal_mulai, tanggal_selesai, total_harga, status)
                            VALUES (?, ?, ?, ?, ?, ?, 'pengajuan')");
    $stmt->bind_param('issssd', $car_id, $nama, $hp, $mulai, $selesai, $total);
    $stmt->execute();

    header('Location: ../index.php?page=sewa');
} catch (Exception $e) {
    header('Location: ../index.php?page=sewa-tambah');
}
exit;