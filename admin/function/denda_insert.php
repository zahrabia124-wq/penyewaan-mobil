<?php
// Memproses form "Tambah Denda".
// Denda disimpan di tabel returns (kolom: rental_id, tgl_dikembalikan, denda, kondisi_mobil).

require_once __DIR__ . '/../database/connection.php';
$db   = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php?page=denda');
    exit;
}

// 1. Ambil data dari form (nama field harus sama dengan di pages/denda/tambah.php)
$rental_id        = (int) ($_POST['rental_id'] ?? 0);
$tgl_dikembalikan = $_POST['tgl_dikembalikan'] ?? '';
$denda            = (float) ($_POST['denda'] ?? 0);
$kondisi_mobil    = trim($_POST['kondisi_mobil'] ?? '');

// 2. Cek isian
if ($rental_id == 0 || $denda <= 0 || $tgl_dikembalikan == '') {
    die('Data belum lengkap. <a href="../index.php?page=denda-tambah">Kembali</a>');
}

// 3. Simpan ke database
try {
    $stmt = $conn->prepare("INSERT INTO returns (rental_id, tgl_dikembalikan, denda, kondisi_mobil) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('isds', $rental_id, $tgl_dikembalikan, $denda, $kondisi_mobil);
    $stmt->execute();
    $stmt->close();

    header('Location: ../index.php?page=denda');
} catch (Exception $e) {
    // Tampilkan error supaya mudah dicari penyebabnya
    die('Gagal menyimpan denda: ' . htmlspecialchars($e->getMessage()) . ' <a href="../index.php?page=denda-tambah">Kembali</a>');
}
exit;