<?php
// Halaman statistik penggunaan mobil milik pemilik, bisa difilter per rentang tanggal.

// 1. Ambil tanggal dari filter. Kalau belum diisi: awal tahun ini sampai hari ini
$dari   = $_GET['dari']   ?? date('Y-01-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

// Rapikan format tanggal supaya aman dipakai di query
$dari   = date('Y-m-d', strtotime($dari));
$sampai = date('Y-m-d', strtotime($sampai));

// 2. Hitung jumlah hari dalam periode (dipakai untuk menghitung okupansi)
$hariPeriode = (strtotime($sampai) - strtotime($dari)) / 86400 + 1;
if ($hariPeriode < 1) {
    $hariPeriode = 1;
}

// 3. Ambil semua mobil milik pemilik
$mobil = $conn->query("SELECT id, merk, plat_nomor FROM cars WHERE pemilik_id = $pemilikId ORDER BY id DESC");

// 4. Untuk setiap mobil, hitung jumlah sewa dan hari tersewa dalam periode
$data      = [];   // menyimpan hasil hitungan tiap mobil
$totalSewa = 0;
$totalHari = 0;
$terlaris  = null; // mobil yang paling sering disewa

while ($m = $mobil->fetch_assoc()) {

    // Ambil sewa mobil ini yang tanggalnya bersinggungan dengan periode
    $carId = $m['id'];
    $sewa = $conn->query("SELECT tgl_sewa, tgl_kembali FROM rentals
                          WHERE car_id = $carId
                          AND status_rental IN ('disetujui', 'berjalan', 'selesai')
                          AND tgl_sewa <= '$sampai'
                          AND tgl_kembali >= '$dari'");

    $jumlahSewa  = 0;
    $hariTersewa = 0;

    while ($s = $sewa->fetch_assoc()) {
        $jumlahSewa++;

        // Hitung hari sewa yang masuk ke dalam periode saja
        $mulai   = max(strtotime($s['tgl_sewa']), strtotime($dari));
        $selesai = min(strtotime($s['tgl_kembali']), strtotime($sampai));
        $hari    = ($selesai - $mulai) / 86400;
        if ($hari < 1) {
            $hari = 1;
        }
        $hariTersewa += $hari;
    }

    // Okupansi = hari tersewa dibagi hari dalam periode (maksimal 100%)
    $okupansi = round($hariTersewa / $hariPeriode * 100);
    if ($okupansi > 100) {
        $okupansi = 100;
    }

    $m['jumlah_sewa']  = $jumlahSewa;
    $m['hari_tersewa'] = $hariTersewa;
    $m['okupansi']     = $okupansi;
    $data[] = $m;

    $totalSewa += $jumlahSewa;
    $totalHari += $hariTersewa;

    // Catat mobil dengan sewa terbanyak
    if ($jumlahSewa > 0 && ($terlaris === null || $jumlahSewa > $terlaris['jumlah_sewa'])) {
        $terlaris = $m;
    }
}

// 5. Okupansi rata-rata semua mobil
$jumlahMobil  = count($data);
$okupansiRata = 0;
if ($jumlahMobil > 0) {
    $okupansiRata = round($totalHari / ($jumlahMobil * $hariPeriode) * 100);
    if ($okupansiRata > 100) {
        $okupansiRata = 100;
    }
}
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Statistik Penggunaan</h1>
        <small class="text-muted">
            Periode <?php echo date('d/m/Y', strtotime($dari)); ?> s/d <?php echo date('d/m/Y', strtotime($sampai)); ?>
            (<?php echo $hariPeriode; ?> hari)
        </small>
    </div>
</div>

<!-- Filter tanggal -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form action="index.php" method="GET" class="form-inline">
            <input type="hidden" name="page" value="statistik">

            <label class="mr-2">Dari</label>
            <input type="date" name="dari" class="form-control mr-3" value="<?php echo $dari; ?>">

            <label class="mr-2">Sampai</label>
            <input type="date" name="sampai" class="form-control mr-3" value="<?php echo $sampai; ?>">

            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </form>
    </div>
</div>

<!-- Kartu ringkasan -->
<div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sewa</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalSewa; ?> kali</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Hari Tersewa</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalHari; ?> hari</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Okupansi Rata-rata</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $okupansiRata; ?>%</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Paling Sering Disewa</div>
                <?php if ($terlaris) { ?>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo htmlspecialchars($terlaris['merk']); ?></div>
                    <div class="small text-muted"><?php echo htmlspecialchars($terlaris['plat_nomor']); ?> (<?php echo $terlaris['jumlah_sewa']; ?> kali)</div>
                <?php } else { ?>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">-</div>
                <?php } ?>
            </div>
        </div>
    </div>

</div>

<!-- Tabel per mobil -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mobil</th>
                        <th>Jumlah Sewa</th>
                        <th>Hari Tersewa</th>
                        <th>Okupansi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($data as $row) { ?>
                        <tr>
                            <td><?php echo $no; ?></td>
                            <td>
                                <?php echo htmlspecialchars($row['merk']); ?><br>
                                <small><?php echo htmlspecialchars($row['plat_nomor']); ?></small>
                            </td>
                            <td><?php echo $row['jumlah_sewa']; ?> kali</td>
                            <td><?php echo $row['hari_tersewa']; ?> hari</td>
                            <td>
                                <!-- Bar persen okupansi -->
                                <div class="progress" style="height: 18px;">
                                    <div class="progress-bar bg-success" style="width: <?php echo $row['okupansi']; ?>%">
                                        <?php echo $row['okupansi']; ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php $no++; ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>