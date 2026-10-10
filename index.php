<?php
session_start();
require_once __DIR__ . '/pelanggan/database/connection.php';
$db = new Database();
$conn = $db->conn;

// Template SB Admin 2 ada di folder pelanggan/template
$tpl = 'pelanggan/template';

// Sementara sebelum ada login: pakai pelanggan id 1. Ganti dengan session login-mu.
$pelangganId   = $_SESSION['id']   ?? 1;
$namaPelanggan = $_SESSION['nama'] ?? 'Pelanggan';

$page = $_GET['page'] ?? 'dashboard';

// ===== ROUTER SEDERHANA: index.php?page=dashboard =====
$routes = [
    'dashboard'   => 'pelanggan/pages/dashboard.php',
    'mobil'       => 'pelanggan/pages/memilih mobil/mobil.php',
    'sewa'        => 'pelanggan/pages/mengajukan sewa/sewa.php',
    'sewa-tambah' => 'pelanggan/pages/mengajukan sewa/tambah.php',
    'berkas'      => 'pelanggan/pages/berkas persetujuan/berkas.php',
];
$halaman = $routes[$page] ?? $routes['dashboard'];
?>
<!DOCTYPE html>
<html lang="id">

<?php include 'pelanggan/partials/head.php' ?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'pelanggan/components/sidebar.php' ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include 'pelanggan/components/topbar.php' ?>

                <!-- Isi halaman -->
                <div class="container-fluid">
                    <?php include $halaman; ?>
                </div>

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include 'pelanggan/components/footer.php' ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Script -->
    <?php include 'pelanggan/partials/script.php' ?>

</body>

</html>