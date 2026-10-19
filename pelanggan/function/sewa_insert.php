<?php
// Menyimpan pengajuan sewa dari form di ajukan.php.
// Pengajuan baru selalu berstatus "menunggu" sampai disetujui admin.
require_once __DIR__ . '/../../login/cek_login.php';
cek_login('pelanggan');
require_once __DIR__ . '/../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../ajukan.php');
    exit;
}

$db   = new Database();
$conn = $db->conn;

$pelangganId = (int) $_SESSION['id'];          // diambil dari sesi, bukan dari form
$carId       = (int) ($_POST['car_id'] ?? 0);
$tglSewa     = $_POST['tgl_sewa'] ?? '';
$tglKembali  = $_POST['tgl_kembali'] ?? '';

// Kembali ke form dengan kode pesan (teksnya ditampilkan di tambah.php)
function balik($kode, $carId)
{
    header('Location: ../ajukan.php?car_id=' . (int) $carId . '&error=' . $kode);
    exit;
}

// Validasi tanggal: format yyyy-mm-dd, tidak boleh lewat, kembali tidak boleh sebelum sewa
$formatOk = preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglSewa) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tglKembali);
if ($carId < 1 || !$formatOk) {
    balik('data', $carId);
}
if ($tglSewa < date('Y-m-d')) {
    balik('lewat', $carId);
}
if ($tglKembali < $tglSewa) {
    balik('urutan', $carId);
}

// Mobil harus ada dan masih tersedia
$cek = $conn->prepare("SELECT id FROM cars WHERE id = ? AND status_mobil = 'tersedia'");
$cek->bind_param('i', $carId);
$cek->execute();
if ($cek->get_result()->num_rows === 0) {
    balik('mobil', $carId);
}

$stmt = $conn->prepare("INSERT INTO rentals (pelanggan_id, car_id, tgl_sewa, tgl_kembali, status_rental)
                        VALUES (?, ?, ?, ?, 'menunggu')");
$stmt->bind_param('iiss', $pelangganId, $carId, $tglSewa, $tglKembali);
$stmt->execute();
$idBaru = (int) $conn->insert_id;

// Kembali ke daftar "Pengajuan Sewa Saya", membawa id pengajuan baru supaya disorot
header('Location: ../../index.php?baru=' . $idBaru . '#sewa');
exit;