<?php
require_once __DIR__ . '/../login/cek_login.php';
cek_login('admin');
require_once __DIR__ . '/database/connection.php';
$db = new Database();
$conn = $db->conn;
$page = $_GET['page'] ?? 'dashboard';

// ===== ROUTER SEDERHANA: index.php?page=denda =====
$routes = [
    'denda'           => 'pages/denda/denda.php',
    'denda-tambah'    => 'pages/denda/tambah.php',
    'denda-edit'      => 'pages/denda/edit.php',
    'armada'          => 'pages/kelola armada/armada.php',
    'armada-tambah'   => 'pages/kelola armada/tambah.php',
    'armada-edit'     => 'pages/kelola armada/edit.php',
    'sewa'            => 'pages/pengajuan sewa/sewa.php',
    'sewa-tambah'     => 'pages/pengajuan sewa/tambah.php',
    'sewa-edit'       => 'pages/pengajuan sewa/edit.php',
    'invoice'         => 'pages/terbitkan invoice/invoice.php',
    'invoice-tambah'  => 'pages/terbitkan invoice/tambah.php',
    'invoice-edit'    => 'pages/terbitkan invoice/edit.php',
    'pengguna'        => 'pages/pengguna/pengguna.php',
    'pengguna-tambah' => 'pages/pengguna/tambah.php',
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
                    <a class="btn btn-primary" href="../login/logout.php?role=admin">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
     <?php include 'partials/script.php' ?>

    <!-- Page level plugins -->
    <?php if ($page === 'dashboard' || !isset($routes[$page])) { ?>
    <script src="template/vendor/chart.js/Chart.min.js"></script>
    <script src="template/js/demo/chart-area-demo.js"></script>
    <script src="template/js/demo/chart-pie-demo.js"></script>
    <?php } ?>

</body>

</html>