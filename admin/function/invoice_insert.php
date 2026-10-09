<?php
// Menyimpan invoice baru ke tabel payments.
// Total tagihan = (jumlah hari x harga sewa per hari) + total denda, sama seperti di tambah.php.
require_once __DIR__ . '/../database/connection.php';
$db = new Database();
$conn = $db->conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?page=invoice');
    exit;
}

$rental_id = (int) ($_POST['rental_id'] ?? 0);
if ($rental_id <= 0) {
    die('Sewa belum dipilih. <a href="../index.php?page=invoice-tambah">Kembali</a>');
}

// Ambil data sewa + harga mobil + total denda
$stmt = $conn->prepare("SELECT rentals.tgl_sewa, rentals.tgl_kembali, cars.harga_sewa_per_hari,
                               (SELECT COALESCE(SUM(returns.denda), 0) FROM returns WHERE returns.rental_id = rentals.id) AS total_denda
                        FROM rentals
                        JOIN cars ON cars.id = rentals.car_id
                        WHERE rentals.id = ?");
$stmt->bind_param('i', $rental_id);
$stmt->execute();
$sewa = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sewa) {
    die('Data sewa tidak ditemukan. <a href="../index.php?page=invoice-tambah">Kembali</a>');
}

// Cegah invoice ganda untuk sewa yang sama
$cek = $conn->prepare("SELECT id FROM payments WHERE rental_id = ?");
$cek->bind_param('i', $rental_id);
$cek->execute();
$cek->store_result();
if ($cek->num_rows > 0) {
    $cek->close();
    header('Location: ../index.php?page=invoice');
    exit;
}
$cek->close();

// Hitung total tagihan
$hari = (strtotime($sewa['tgl_kembali']) - strtotime($sewa['tgl_sewa'])) / 86400;
if ($hari < 1) {
    $hari = 1;
}
$total = ($hari * $sewa['harga_sewa_per_hari']) + $sewa['total_denda'];

// Simpan invoice, status awal belum_bayar
$status = 'belum_bayar';
$ins = $conn->prepare("INSERT INTO payments (rental_id, total_bayar, status_pembayaran) VALUES (?, ?, ?)");
$ins->bind_param('ids', $rental_id, $total, $status);

if (!$ins->execute()) {
    die('Gagal menyimpan invoice: ' . htmlspecialchars($ins->error) . ' <a href="../index.php?page=invoice-tambah">Kembali</a>');
}
$ins->close();

header('Location: ../index.php?page=invoice');
exit;