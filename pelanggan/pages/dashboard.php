<?php
// Dashboard E-Rent (pelanggan).
// Sesuaikan nilai status di bawah dengan isi kolom di databasemu.
$STATUS_MENUNGGU  = 'menunggu';   // rentals.status_rental: pengajuan baru
$STATUS_BERJALAN  = 'disetujui';  // rentals.status_rental: sedang disewa
$STATUS_TERSEDIA  = 'tersedia';   // cars.status_mobil
$STATUS_LUNAS     = 'lunas';      // payments.status_pembayaran

$idPelanggan = (int) $pelangganId;

function angka($conn, $sql) {
    $r = $conn->query($sql);
    return $r ? ($r->fetch_row()[0] ?? 0) : 0;
}

$totalArmada    = angka($conn, "SELECT COUNT(*) FROM cars");
$armadaTersedia = angka($conn, "SELECT COUNT(*) FROM cars WHERE status_mobil = '$STATUS_TERSEDIA'");
$totalMenunggu  = angka($conn, "SELECT COUNT(*) FROM rentals WHERE pelanggan_id = $idPelanggan AND status_rental = '$STATUS_MENUNGGU'");
$totalBerjalan  = angka($conn, "SELECT COUNT(*) FROM rentals WHERE pelanggan_id = $idPelanggan AND status_rental = '$STATUS_BERJALAN'");
$totalBayar     = angka($conn, "SELECT COALESCE(SUM(payments.total_bayar),0) FROM payments
                                JOIN rentals ON rentals.id = payments.rental_id
                                WHERE rentals.pelanggan_id = $idPelanggan AND payments.status_pembayaran = '$STATUS_LUNAS'");
$totalTagihan   = angka($conn, "SELECT COALESCE(SUM(payments.total_bayar),0) FROM payments
                                JOIN rentals ON rentals.id = payments.rental_id
                                WHERE rentals.pelanggan_id = $idPelanggan AND payments.status_pembayaran <> '$STATUS_LUNAS'");

// Total denda milik pelanggan ini (dari tabel returns)
$totalDenda     = angka($conn, "SELECT COALESCE(SUM(returns.denda),0) FROM returns
                                JOIN rentals ON rentals.id = returns.rental_id
                                WHERE rentals.pelanggan_id = $idPelanggan");

$sewaTerbaru = $conn->query("SELECT rentals.id, rentals.tgl_sewa, rentals.tgl_kembali, rentals.status_rental,
                                    cars.merk, cars.plat_nomor
                             FROM rentals
                             JOIN cars ON cars.id = rentals.car_id
                             WHERE rentals.pelanggan_id = $idPelanggan
                             ORDER BY rentals.id DESC
                             LIMIT 6");

$statusSewa = $conn->query("SELECT status_rental, COUNT(*) AS jumlah FROM rentals
                            WHERE pelanggan_id = $idPelanggan GROUP BY status_rental");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <small class="text-muted">Ringkasan penyewaan &amp; rental mobil kamu</small>
    </div>
    <div class="d-print-none">
        <a href="index.php?page=sewa-tambah" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-key fa-sm text-white-50"></i> Ajukan Sewa
        </a>
        <button type="button" onclick="window.print()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-print fa-sm text-white-50"></i> Cetak Laporan
        </button>
    </div>
</div>

<!-- Peringatan denda: hanya muncul kalau pelanggan punya denda -->
<?php if ($totalDenda > 0) { ?>
    <div class="alert alert-danger d-flex justify-content-between align-items-center" role="alert">
        <div>
            <i class="fas fa-exclamation-triangle"></i>
            Kamu memiliki denda sebesar
            <strong>Rp <?= number_format($totalDenda, 0, ',', '.') ?></strong>
            (misalnya karena terlambat mengembalikan mobil).
        </div>
        <a href="index.php?page=berkas" class="btn btn-sm btn-danger ml-3">Lihat rincian</a>
    </div>
<?php } ?>

<!-- Kartu ringkasan -->
<div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Mobil Tersedia</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $armadaTersedia ?> mobil</div>
                        <div class="small text-muted">dari <?= $totalArmada ?> armada</div>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Bayar (Lunas)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($totalBayar, 0, ',', '.') ?></div>
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
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tagihan Belum Lunas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($totalTagihan, 0, ',', '.') ?></div>
                        <a class="small" href="index.php?page=berkas">Lihat berkas</a>
                    </div>
                    <div class="col-auto"><i class="fas fa-wallet fa-2x text-gray-300"></i></div>
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
                    <table class="table table-sm table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Mobil</th>
                                <th>Tanggal Sewa</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($sewaTerbaru && $sewaTerbaru->num_rows > 0) { ?>
                                <?php while ($s = $sewaTerbaru->fetch_assoc()) { ?>
                                    <?php
                                    if ($s['status_rental'] == $STATUS_MENUNGGU) {
                                        $warna = 'warning';
                                    } elseif ($s['status_rental'] == $STATUS_BERJALAN) {
                                        $warna = 'success';
                                    } elseif ($s['status_rental'] == 'ditolak') {
                                        $warna = 'danger';
                                    } else {
                                        $warna = 'secondary';
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <?= htmlspecialchars($s['merk']) ?><br>
                                            <small class="text-muted"><?= htmlspecialchars($s['plat_nomor']) ?></small>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($s['tgl_sewa'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($s['tgl_kembali'])) ?></td>
                                        <td><span class="badge badge-<?= $warna ?>"><?= htmlspecialchars($s['status_rental']) ?></span></td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr><td colspan="4" class="text-center text-muted">Belum ada pengajuan sewa.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Status sewa -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status Sewa Saya</h6>
            </div>
            <div class="card-body">
                <?php if ($statusSewa && $statusSewa->num_rows > 0) { ?>
                    <?php while ($st = $statusSewa->fetch_assoc()) { ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-gray-800"><?= htmlspecialchars(ucfirst($st['status_rental'])) ?></span>
                            <span class="badge badge-primary"><?= $st['jumlah'] ?></span>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="text-center text-muted">Belum ada data.</div>
                <?php } ?>
                <hr>
                <a href="index.php?page=mobil" class="btn btn-sm btn-primary btn-block">Pilih Mobil</a>
            </div>
        </div>
    </div>

</div>