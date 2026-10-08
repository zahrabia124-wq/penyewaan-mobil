<?php
// Memproses form "Tambah Denda".

require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=denda');
    exit;
}

// 1. Ambil data dari form
$rental_id  = (int) $_POST['rental_id'];
$jenis      = $_POST['jenis'];
$jumlah     = (float) $_POST['jumlah'];
$keterangan = trim($_POST['keterangan']);

// 2. Cek isian
if ($rental_id == 0 || $jumlah <= 0) {
    header('Location: ../index.php?page=denda-tambah');
    exit;
}

// 3. Simpan ke database
try {
    $stmt = $conn->prepare("INSERT INTO fines (rental_id, jenis, jumlah, keterangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('isds', $rental_id, $jenis, $jumlah, $keterangan);
    $stmt->execute();

    header('Location: ../index.php?page=denda');
} catch (Exception $e) {
    header('Location: ../index.php?page=denda-tambah');
}
exit;