<?php
// Pindah ke folder utama supaya semua path include lama tetap berlaku
chdir(__DIR__ . '/..');

if (session_status() === PHP_SESSION_NONE) {
    session_name('erent_pelanggan');
    session_start();
}

// Belum login? Kirim ke halaman login (mobil yang dipilih dibawa lewat car_id).
// Setelah login, pelanggan dikembalikan ke form ini dengan mobil yang sama.
$sudahLogin = (($_SESSION['role'] ?? '') === 'pelanggan');
if (!$sudahLogin) {
    $carId = (int) ($_GET['car_id'] ?? 0);
    $root  = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
    header('Location: ' . $root . '/login/login.php?belum_login=1' . ($carId ? '&car_id=' . $carId : ''));
    exit;
}
require_once 'pelanggan/database/connection.php';
$db = new Database();
$conn = $db->conn;

$tpl = 'pelanggan/template/assets';

$pelangganId   = (int) $_SESSION['id'];
$namaPelanggan = $_SESSION['nama'] ?? '';

// Alamat folder utama, dipakai <base> di head.php agar CSS, JS, dan link form tetap benar
$base = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';
?>
<!DOCTYPE html>
<html lang="id">

<?php include 'pelanggan/partials/head.php' ?>

<body class="index-page">

    <?php include 'pelanggan/components/header.php' ?>

    <main class="main" style="padding-top: 110px; min-height: 80vh;">
        <div class="container">
            <?php include 'pelanggan/pages/mengajukan sewa/tambah.php'; ?>
        </div>
    </main>

    <?php include 'pelanggan/components/footer.php' ?>
    <?php include 'pelanggan/components/logout_modal.php' ?>

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include 'pelanggan/partials/script.php' ?>

    <script>
        // Menu "#bagian" diarahkan balik ke index.php
        document.querySelectorAll('#navmenu a[href^="#"]').forEach(function (a) {
            a.setAttribute('href', 'index.php' + a.getAttribute('href'));
        });
    </script>
</body>

</html>