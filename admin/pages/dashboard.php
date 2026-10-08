<?php
// Dashboard E-Rent.
// Sesuaikan nilai status di bawah dengan isi kolom di databasemu.
$STATUS_MENUNGGU  = 'menunggu';   // rentals.status_rental: pengajuan baru
$STATUS_BERJALAN  = 'disetujui';  // rentals.status_rental: sedang disewa
$STATUS_TERSEDIA  = 'tersedia';   // cars.status_mobil
$STATUS_LUNAS     = 'lunas';      // payments.status_pembayaran

function angka($conn, $sql) {
    $r = $conn->query($sql);
    return $r ? ($r->fetch_row()[0] ?? 0) : 0;
}

$totalArmada    = angka($conn, "SELECT COUNT(*) FROM cars");
$armadaTersedia = angka($conn, "SELECT COUNT(*) FROM cars WHERE status_mobil = '$STATUS_TERSEDIA'");
$totalMenunggu  = angka($conn, "SELECT COUNT(*) FROM rentals WHERE status_rental = '$STATUS_MENUNGGU'");
$totalBerjalan  = angka($conn, "SELECT COUNT(*) FROM rentals WHERE status_rental = '$STATUS_BERJALAN'");
$pendapatan     = angka($conn, "SELECT COALESCE(SUM(total_bayar),0) FROM payments WHERE status_pembayaran = '$STATUS_LUNAS'");
$totalDenda     = angka($conn, "SELECT COALESCE(SUM(denda),0) FROM returns");

$sewaTerbaru = $conn->query("SELECT rentals.id, rentals.tgl_sewa, rentals.tgl_kembali, rentals.status_rental,
                                    users.nama, cars.merk, cars.plat_nomor
                             FROM rentals
                             JOIN users ON users.id = rentals.pelanggan_id
                             JOIN cars ON cars.id = rentals.car_id
                             ORDER BY rentals.id DESC
                             LIMIT 6");

$statusArmada = $conn->query("SELECT status_mobil, COUNT(*) AS jumlah FROM cars GROUP BY status_mobil");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <small class="text-muted">Ringkasan penyewaan &amp; rental mobil</small>
    </div>
    <div class="d-print-none">
        <a href="index.php?page=sewa" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-key fa-sm text-white-50"></i> Cek Pengajuan
        </a>
        <button type="button" onclick="window.print()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-print fa-sm text-white-50"></i> Cetak Laporan
        </button>
    </div>
</div>

<!-- Kartu ringkasan -->
<div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Armada</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalArmada ?> mobil</div>
                        <div class="small text-muted"><?= $armadaTersedia ?> tersedia</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-car fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pengajuan Menunggu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalMenunggu ?></div>
                        <a class="small" href="index.php?page=sewa">Lihat pengajuan</a>
                    </div>
                    <div class="col-auto"><i class="fas fa-key fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pendapatan (Lunas)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($pendapatan, 0, ',', '.') ?></div>
                        <div class="small text-muted"><?= $totalBerjalan ?> sewa sedang berjalan</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Denda</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($totalDenda, 0, ',', '.') ?></div>
                        <a class="small" href="index.php?page=denda">Lihat denda</a>
                    </div>
                    <div class="col-auto"><i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <!-- Sewa terbaru -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Sewa Terbaru</h6>
                <a href="index.php?page=sewa" class="btn btn-sm btn-primary">Semua</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm