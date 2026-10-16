<?php
date_default_timezone_set('Asia/Jakarta'); // jam server mengikuti WIB
require_once __DIR__ . '/../login/cek_login.php';
cek_login('pemilik');
require_once __DIR__ . '/database/connection.php';
$db = new Database();
$conn = $db->conn;
$page = $_GET['page'] ?? 'dashboard';

// ===== DATA PEMILIK YANG LOGIN =====
// Sesi sudah dijamin ada dan role-nya 'pemilik' oleh cek_login()
$pemilikId = (int) $_SESSION['id'];

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
                    <h5 class="modal-title" id="exampleModalLabel">Yakin ingin keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Klik "Logout" jika Anda ingin mengakhiri sesi ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="../login/logout.php?role=pemilik">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
     <?php include 'partials/script.php' ?>

</body>

</html>