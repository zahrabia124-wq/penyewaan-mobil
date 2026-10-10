<?php
session_start();
require_once __DIR__ . '/database/connection.php';
$db = new Database();
$conn = $db->conn;
$page = $_GET['page'] ?? 'dashboard';

// ===== DATA PEMILIK YANG LOGIN =====
// Sesuaikan key session dengan kode login-mu (di sini dipakai $_SESSION['id'] atau $_SESSION['user_id'])
$pemilikId = (int) ($_SESSION['id'] ?? $_SESSION['user_id'] ?? 0);

// SEMENTARA (sebelum login jadi): kalau belum ada session, pakai pemilik pertama di tabel users.
// Hapus blok ini kalau login sudah terhubung.
if ($pemilikId == 0) {
    $qPemilik = $conn->query("SELECT id FROM users WHERE role = 'pemilik' ORDER BY id LIMIT 1");
    $pemilikId = $qPemilik ? (int) ($qPemilik->fetch_row()[0] ?? 0) : 0;
}

$qNama = $conn->query("SELECT nama FROM users WHERE id = $pemilikId");
$namaPemilik = $qNama ? ($qNama->fetch_row()[0] ?? 'Pemilik') : 'Pemilik';

// Persentase bagi hasil untuk pemilik (sisanya untuk pihak rental)
$PERSEN_PEMILIK = 70;

// ===== ROUTER SEDERHANA: index.php?page=mobil =====
$routes = [
    'mobil'           => 'pages/mobil saya/mobil.php',
    'mobil-detail'    => 'pages/mobil saya/detail.php',
    'statistik'       => 'pages/Statistik Penggunaan/statistik.php',
    'laporan'         => 'pages/laporan bagi hasil/laporan.php',
];
$halaman = ($page !== 'dashboard' && isset($routes[$page]))
    ? $routes[$page]
    : 'pages/dashboard.php';
?>
<!DOCTYPE html>
<html lang="id">

<?php include 'partials/head.php' ?>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'components/sidebar.php' ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include 'components/topbar.php' ?>

                <!-- Isi halaman -->
                <div class="container-fluid">
                    <?php include $halaman; ?>
                </div>

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include 'components/footer.php' ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
     <?php include 'partials/script.php' ?>

</body>

</html>