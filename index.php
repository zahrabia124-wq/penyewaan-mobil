<?php
// Pengunjung boleh melihat-lihat tanpa login.
// Sesi pelanggan memakai nama 'erent_pelanggan' (sama seperti di login/cek_login.php).
if (session_status() === PHP_SESSION_NONE) {
    session_name('erent_pelanggan');
    session_start();
}
$urlLogin   = 'login/login.php';
$sudahLogin = (($_SESSION['role'] ?? '') === 'pelanggan');
require_once __DIR__ . '/pelanggan/database/connection.php';
$db = new Database();
$conn = $db->conn;

// Folder aset template Logis (isinya: css, js, img, vendor)
$tpl = 'pelanggan/template/assets';

// Pengunjung boleh melihat-lihat mobil tanpa login.
// Login baru diminta saat menekan "Pilih & Ajukan" (lihat pelanggan/ajukan.php).
$pelangganId   = $sudahLogin ? (int) $_SESSION['id'] : 0;
$namaPelanggan = $sudahLogin ? ($_SESSION['nama'] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="id">

<?php include 'pelanggan/partials/head.php' ?>

<body class="index-page">

    <!-- Supaya judul bagian tidak tertutup menu yang menempel di atas -->
    <style>html { scroll-padding-top: 90px; }</style>

    <?php include 'pelanggan/components/header.php' ?>

    <main class="main">
        <!-- Satu halaman panjang, bagian-bagiannya berurutan dari atas ke bawah -->
        <?php include 'pelanggan/dashboard.php'; ?>
        <?php include 'pelanggan/pages/memilih mobil/mobil.php'; ?>
        <?php if ($sudahLogin) { // bagian data pribadi hanya untuk yang sudah login ?>
            <?php include 'pelanggan/pages/mengajukan sewa/sewa.php'; ?>
            <?php include 'pelanggan/pages/berkas persetujuan/berkas.php'; ?>
        <?php } ?>
    </main>

    <?php include 'pelanggan/components/footer.php' ?>
    <?php if ($sudahLogin) { include 'pelanggan/components/logout_modal.php'; } ?>

    <!-- Scroll Top (wajib ada, dipakai main.js) -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include 'pelanggan/partials/script.php' ?>

    <!-- Menandai menu yang aktif sesuai bagian yang sedang dilihat -->
    <script>
        (function () {
            var links = document.querySelectorAll('#navmenu a[href^="#"]');
            function tandai() {
                var aktif = null;
                links.forEach(function (a) {
                    var s = document.querySelector(a.getAttribute('href'));
                    if (s && s.getBoundingClientRect().top <= 140) { aktif = a; }
                });
                links.forEach(function (a) { a.classList.remove('active'); });
                if (aktif) { aktif.classList.add('active'); }
            }
            window.addEventListener('scroll', tandai);
            window.addEventListener('load', tandai);
        })();
    </script>
</body>

</html>