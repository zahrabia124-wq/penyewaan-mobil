<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
// Hanya boleh dibuka oleh role pelanggan
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: login/login.php?belum_login=1");
    exit;
}
require_once __DIR__ . '/pelanggan/database/connection.php';
$db = new Database();
$conn = $db->conn;

// Template SB Admin 2 ada di folder pelanggan/template
$tpl = 'pelanggan/template';

// Data pelanggan yang sedang login (dari session)
$pelangganId   = (int) $_SESSION['id'];
$namaPelanggan = $_SESSION['nama'];

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
    <div id="wrapper">

        <?php include 'pelanggan/components/sidebar.php' ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <?php include 'pelanggan/components/topbar.php' ?>

                <div class="container-fluid">
                    <?php include $halaman; ?>
                </div>

            </div>

            <?php include 'pelanggan/components/footer.php' ?>
        </div>

    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutLabel">Yakin ingin keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Klik "Logout" jika Anda ingin mengakhiri sesi ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="login/logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'pelanggan/partials/script.php' ?>
</body>

</html>