<?php
// Halaman berkas persetujuan: sewa milik pelanggan yang sudah disetujui admin,
// lengkap dengan denda (tabel returns) dan invoice-nya (tabel payments) kalau sudah diterbitkan.
// $conn = koneksi database, $pelangganId = id pelanggan, dibuat di index.php

$stmt = $conn->prepare("SELECT rentals.id, rentals.tgl_sewa, rentals.tgl_kembali, rentals.status_rental,
                               cars.merk, cars.plat_nomor,
                               payments.id AS payment_id, payments.total_bayar, payments.status_pembayaran,
                               (SELECT COALESCE(SUM(returns.denda), 0) FROM returns
                                WHERE returns.rental_id = rentals.id) AS total_denda,
                               (SELECT GROUP_CONCAT(returns.kondisi_mobil SEPARATOR '; ') FROM returns
                                WHERE returns.rental_id = rentals.id AND returns.denda > 0) AS ket_denda
                        FROM rentals
                        JOIN cars ON cars.id = rentals.car_id
                        LEFT JOIN payments ON payments.rental_id = rentals.id
                        WHERE rentals.pelanggan_id = ?
                          AND rentals.status_rental IN ('disetujui', 'berjalan', 'selesai')
                        ORDER BY rentals.id DESC");
$stmt->bind_param('i', $pelangganId);
$stmt->execute();
$hasil = $stmt->get_result();
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Berkas Persetujuan</h1>
    <button type="button" onclick="window.print()" class="d-print-none btn btn-sm btn-primary">Cetak</button>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mobil</th>
                        <th>Tanggal Sewa</th>
                        <th>Status Sewa</th>
                        <th>Denda</th>
                        <th>No. Invoice</th>
                        <th>Total Tagihan</th>
                        <th>Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = $hasil->fetch_assoc()) { ?>

                        <?php
                        if ($row['status_pembayaran'] == 'lunas') {
                            $warna = 'success';
                            $teks  = 'Lunas';
                        } else {
                            $warna = 'warning';
                            $teks  = 'Belum bayar';
                        }
                        ?>

                        <tr>
                            <td><?php echo $no; ?></td>
                            <td>
                                <?php echo htmlspecialchars($row['merk']); ?><br>
                                <small><?php echo htmlspecialchars($row['plat_nomor']); ?></small>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($row['tgl_sewa'])); ?>
                                s/d
                                <?php echo date('d/m/Y', strtotime($row['tgl_kembali'])); ?>
                            </td>
                            <td><span class="badge badge-success"><?php echo htmlspecialchars($row['status_rental']); ?></span></td>

                            <!-- Denda: tampil walaupun invoice belum diterbitkan -->
                            <td>
                                <?php if ($row['total_denda'] > 0) { ?>
                                    <span class="text-danger font-weight-bold">
                                        Rp <?php echo number_format($row['total_denda'], 0, ',', '.'); ?>
                                    </span>
                                    <?php if (!empty($row['ket_denda'])) { ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($row['ket_denda']); ?></small>
                                    <?php } ?>
                                <?php } else { ?>
                                    <span class="text-muted">-</span>
                                <?php } ?>
                            </td>

                            <?php if ($row['payment_id']) { ?>
                                <td>INV-<?php echo str_pad($row['payment_id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                                <td><span class="badge badge-<?php echo $warna; ?>"><?php echo $teks; ?></span></td>
                            <?php } else { ?>
                                <td colspan="3" class="text-muted">Invoice belum diterbitkan</td>
                            <?php } ?>
                        </tr>

                        <?php $no++; ?>
                    <?php } ?>

                    <?php if ($no == 1) { ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada sewa yang disetujui.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>