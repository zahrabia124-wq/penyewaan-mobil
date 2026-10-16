<?php
// Dashboard Pemilik E-Rent (hanya membaca data, semua dibatasi mobil milik pemilik yang login).
// Sesuaikan nilai status di bawah dengan isi kolom di databasemu.
$STATUS_TERSEDIA  = 'tersedia';   // cars.status_mobil
$STATUS_DISEWA    = 'disewa';     // cars.status_mobil
$STATUS_LUNAS     = 'lunas';      // payments.status_pembayaran
$SEWA_SAH         = "'disetujui','berjalan','selesai'"; // rentals.status_rental yang dihitung sebagai sewa

function angka($conn, $sql) {
    $r = $conn->query($sql);
    return $r ? ($r->fetch_row()[0] ?? 0) : 0;
}

$totalMobil     = angka($conn, "SELECT COUNT(*) FROM cars WHERE pemilik_id = $pemilikId");
$mobilTersedia  = angka($conn, "SELECT COUNT(*) FROM cars WHERE pemilik_id = $pemilikId AND status_mobil = '$STATUS_TERSEDIA'");
$mobilDisewa    = angka($conn, "SELECT COUNT(*) FROM cars WHERE pemilik_id = $pemilikId AND status_mobil = '$STATUS_DISEWA'");
$sewaBulanIni   = angka($conn, "SELECT COUNT(*) FROM rentals JOIN cars ON cars.id = rentals.car_id
                                WHERE cars.pemilik_id = $pemilikId
                                AND rentals.status_rental IN ($SEWA_SAH)
                                AND DATE_FORMAT(rentals.tgl_sewa, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')");
$pendapatan     = angka($conn, "SELECT COALESCE(SUM(payments.total_bayar),0) FROM payments
                                JOIN rentals ON rentals.id = payments.rental_id
                                JOIN cars ON cars.id = rentals.car_id
                                WHERE cars.pemilik_id = $pemilikId AND payments.status_pembayaran = '$STATUS_LUNAS'");
$bagiHasil      = $pendapatan * $PERSEN_PEMILIK / 100;

// Bagi hasil 6 bulan terakhir (untuk grafik)
$namaBulan = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun',
              '07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
$mulai = date('Y-m-01', strtotime('first day of -5 months'));

$perBulan = [];
$qBulan = $conn->query("SELECT DATE_FORMAT(rentals.tgl_sewa, '%Y-%m') AS bulan, SUM(payments.total_bayar) AS total
                        FROM payments
                        JOIN rentals ON rentals.id = payments.rental_id
                        JOIN cars ON cars.id = rentals.car_id
                        WHERE cars.pemilik_id = $pemilikId
                        AND payments.status_pembayaran = '$STATUS_LUNAS'
                        AND rentals.tgl_sewa >= '$mulai'
                        GROUP BY bulan");
if ($qBulan) {
    while ($b = $qBulan->fetch_assoc()) {
        $perBulan[$b['bulan']] = $b['total'];
    }
}

$labelBulan = [];
$dataBulan  = [];
for ($i = 5; $i >= 0; $i--) {
    $t = strtotime("first day of -$i months");
    $labelBulan[] = $namaBulan[date('m', $t)] . ' ' . date('Y', $t);
    $dataBulan[]  = round(($perBulan[date('Y-m', $t)] ?? 0) * $PERSEN_PEMILIK / 100);
}

$sewaTerbaru = $conn->query("SELECT rentals.id, rentals.tgl_sewa, rentals.tgl_kembali, rentals.status_rental,
                                    users.nama, cars.merk, cars.plat_nomor
                             FROM rentals
                             LEFT JOIN users ON users.id = rentals.pelanggan_id
                             JOIN cars ON cars.id = rentals.car_id
                             WHERE cars.pemilik_id = $pemilikId
                             ORDER BY rentals.id DESC
                             LIMIT 6");

$statusMobil = $conn->query("SELECT id, merk, plat_nomor, status_mobil FROM cars
                             WHERE pemilik_id = $pemilikId ORDER BY id DESC");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <small class="text-muted">Ringkasan penggunaan mobil &amp; bagi hasil Anda</small>
    </div>
    <div class="d-print-none">
        <a href="index.php?page=laporan" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-file-invoice-dollar fa-sm text-white-50"></i> Laporan Bagi Hasil
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
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Mobil Saya</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalMobil ?> mobil</div>
                        <div class="small text-muted"><?= $mobilTersedia ?> tersedia, <?= $mobilDisewa ?> disewa</div>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sewa Bulan Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $sewaBulanIni ?></div>
                        <a class="small" href="index.php?page=statistik">Lihat statistik</a>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pendapatan Kotor (Lunas)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($pendapatan, 0, ',', '.') ?></div>
                        <div class="small text-muted">Seluruh mobil milik Anda</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Bagi Hasil Anda (<?= $PERSEN_PEMILIK ?>%)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($bagiHasil, 0, ',', '.') ?></div>
                        <a class="small" href="index.php?page=laporan">Lihat laporan</a>
                    </div>
                    <div class="col-auto"><i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <!-- Grafik bagi hasil per bulan -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Bagi Hasil 6 Bulan Terakhir</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="chartBagiHasil"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Status mobil -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Status Mobil</h6>
                <a href="index.php?page=mobil" class="btn btn-sm btn-primary">Semua</a>
            </div>
            <div class="card-body">
                <?php if ($statusMobil && $statusMobil->num_rows > 0) { ?>
                    <?php while ($m = $statusMobil->fetch_assoc()) { ?>
                        <?php
                        if ($m['status_mobil'] == 'tersedia') {
                            $warna = 'success';
                        } elseif ($m['status_mobil'] == 'disewa') {
                            $warna = 'warning';
                        } else {
                            $warna = 'secondary';
                        }
                        ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <?= htmlspecialchars($m['merk']) ?><br>
                                <small class="text-muted"><?= htmlspecialchars($m['plat_nomor']) ?></small>
                            </div>
                            <span class="badge badge-<?= $warna ?>"><?= $m['status_mobil'] ?></span>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="text-center text-muted">Belum ada mobil.</div>
                <?php } ?>
            </div>
        </div>
    </div>

</div>

<!-- Sewa terbaru -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sewa Terbaru Mobil Saya</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Penyewa</th>
                                <th>Mobil</th>
                                <th>Tanggal Sewa</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($sewaTerbaru && $sewaTerbaru->num_rows > 0) { ?>
                                <?php while ($row = $sewaTerbaru->fetch_assoc()) { ?>
                                    <?php
                                    if ($row['status_rental'] == 'menunggu') {
                                        $warna = 'info';
                                    } elseif ($row['status_rental'] == 'disetujui') {
                                        $warna = 'success';
                                    } elseif ($row['status_rental'] == 'ditolak') {
                                        $warna = 'danger';
                                    } else {
                                        $warna = 'secondary';
                                    }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['nama'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($row['merk']) ?> - <?= htmlspecialchars($row['plat_nomor']) ?></td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($row['tgl_sewa'])) ?>
                                            s/d
                                            <?= date('d/m/Y', strtotime($row['tgl_kembali'])) ?>
                                        </td>
                                        <td><span class="badge badge-<?= $warna ?>"><?= $row['status_rental'] ?></span></td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr><td colspan="4" class="text-center text-muted">Belum ada sewa.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik (Chart.js bawaan template) -->
<script src="template/vendor/chart.js/Chart.min.js"></script>
<script>
new Chart(document.getElementById("chartBagiHasil"), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labelBulan) ?>,
        datasets: [{
            label: 'Bagi hasil',
            backgroundColor: '#4e73df',
            data: <?= json_encode($dataBulan) ?>
        }]
    },
    options: {
        maintainAspectRatio: false,
        legend: { display: false },
        scales: {
            yAxes: [{ ticks: { beginAtZero: true, callback: function (v) { return 'Rp ' + v.toLocaleString('id-ID'); } } }]
        },
        tooltips: {
            callbacks: { label: function (t) { return 'Rp ' + t.yLabel.toLocaleString('id-ID'); } }
        }
    }
});
</script>