<?php
// Halaman laporan bagi hasil. Hanya pembayaran yang sudah lunas dari mobil milik pemilik.
// $PERSEN_PEMILIK = persen bagian pemilik, dibuat di index.php

// 1. Filter bulan (format YYYY-MM). Kalau kosong = tampilkan semua bulan.
$bulan = $_GET['bulan'] ?? date('Y-m');

// 2. Susun query. Kondisi bulan ditambahkan hanya kalau filter diisi.
$sql = "SELECT rentals.tgl_sewa, rentals.tgl_kembali, payments.total_bayar,
               users.nama, cars.merk, cars.plat_nomor
        FROM payments
        JOIN rentals ON rentals.id = payments.rental_id
        JOIN cars ON cars.id = rentals.car_id
        LEFT JOIN users ON users.id = rentals.pelanggan_id
        WHERE cars.pemilik_id = $pemilikId
        AND payments.status_pembayaran = 'lunas'";

if ($bulan != '') {
    $bulan = $conn->real_escape_string($bulan);
    $sql .= " AND rentals.tgl_sewa LIKE '$bulan%'"; // contoh: tgl_sewa LIKE '2026-10%'
}

$sql .= " ORDER BY rentals.tgl_sewa DESC";
$hasil = $conn->query($sql);

// 3. Variabel untuk menjumlahkan total di bawah tabel
$totalBayar   = 0;
$totalPemilik = 0;
$totalRental  = 0;
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Laporan Bagi Hasil</h1>
        <small class="text-muted">Pemilik <?php echo $PERSEN_PEMILIK; ?>% | Rental <?php echo 100 - $PERSEN_PEMILIK; ?>%</small>
    </div>
    <button type="button" onclick="window.print()" class="btn btn-sm btn-primary d-print-none">Cetak</button>
</div>

<!-- Filter bulan -->
<div class="card shadow mb-4 d-print-none">
    <div class="card-body">
        <form action="index.php" method="GET" class="form-inline">
            <input type="hidden" name="page" value="laporan">

            <label class="mr-2">Bulan</label>
            <input type="month" name="bulan" class="form-control mr-3" value="<?php echo htmlspecialchars($bulan); ?>">

            <button type="submit" class="btn btn-primary mr-2">Tampilkan</button>
            <a href="index.php?page=laporan&bulan=" class="btn btn-secondary">Semua Bulan</a>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Sewa</th>
                        <th>Mobil</th>
                        <th>Pelanggan</th>
                        <th>Total Bayar</th>
                        <th>Bagian Pemilik</th>
                        <th>Bagian Rental</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = $hasil->fetch_assoc()) { ?>

                        <?php
                        // Hitung bagi hasil transaksi ini
                        $bagianPemilik = $row['total_bayar'] * $PERSEN_PEMILIK / 100;
                        $bagianRental  = $row['total_bayar'] - $bagianPemilik;

                        // Tambahkan ke total
                        $totalBayar   += $row['total_bayar'];
                        $totalPemilik += $bagianPemilik;
                        $totalRental  += $bagianRental;
                        ?>

                        <tr>
                            <td><?php echo $no; ?></td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($row['tgl_sewa'])); ?>
                                s/d
                                <?php echo date('d/m/Y', strtotime($row['tgl_kembali'])); ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['merk']); ?> - <?php echo htmlspecialchars($row['plat_nomor']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama'] ?? '-'); ?></td>
                            <td>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($bagianPemilik, 0, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($bagianRental, 0, ',', '.'); ?></td>
                        </tr>

                        <?php $no++; ?>
                    <?php } ?>

                    <?php if ($no == 1) { ?>
                        <tr><td colspan="7" class="text-center text-muted">Belum ada pembayaran lunas.</td></tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="4" class="text-right">Total</td>
                        <td>Rp <?php echo number_format($totalBayar, 0, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($totalPemilik, 0, ',', '.'); ?></td>
                        <td>Rp <?php echo number_format($totalRental, 0, ',', '.'); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>