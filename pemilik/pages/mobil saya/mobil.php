<?php
// Halaman daftar mobil milik pemilik yang login (hanya melihat, tidak ada tambah/edit/hapus).
// $conn = koneksi database, $pemilikId = id pemilik, dibuat di index.php

// 1. Ambil mobil milik pemilik + jumlah sewa, total hari, dan pendapatan tiap mobil
$hasil = $conn->query("SELECT cars.id, cars.foto, cars.merk, cars.plat_nomor,
                              cars.harga_sewa_per_hari AS harga_per_hari, cars.status_mobil AS status,
                              (SELECT COUNT(*) FROM rentals
                               WHERE rentals.car_id = cars.id
                               AND rentals.status_rental IN ('disetujui','berjalan','selesai')) AS jumlah_sewa,
                              (SELECT COALESCE(SUM(GREATEST(DATEDIFF(rentals.tgl_kembali, rentals.tgl_sewa), 1)), 0) FROM rentals
                               WHERE rentals.car_id = cars.id
                               AND rentals.status_rental IN ('disetujui','berjalan','selesai')) AS total_hari,
                              (SELECT COALESCE(SUM(payments.total_bayar), 0) FROM payments
                               JOIN rentals ON rentals.id = payments.rental_id
                               WHERE rentals.car_id = cars.id
                               AND payments.status_pembayaran = 'lunas') AS pendapatan
                       FROM cars
                       WHERE cars.pemilik_id = $pemilikId
                       ORDER BY cars.id DESC");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Mobil Saya</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Merk</th>
                        <th>Plat Nomor</th>
                        <th>Harga / Hari</th>
                        <th>Status</th>
                        <th>Jumlah Sewa</th>
                        <th>Total Hari</th>
                        <th>Pendapatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>

                    <!-- 2. Ulangi baris tabel untuk setiap mobil -->
                    <?php while ($row = $hasil->fetch_assoc()) { ?>

                        <?php
                        // Tentukan warna label status
                        if ($row['status'] == 'tersedia') {
                            $warna = 'success';
                        } elseif ($row['status'] == 'disewa') {
                            $warna = 'warning';
                        } else {
                            $warna = 'secondary';
                        }
                        ?>

                        <tr>
                            <td><?php echo $no; ?></td>
                            <td>
                                <?php if ($row['foto'] != '') { ?>
                                  <img src="../foto/<?php echo htmlspecialchars($row['foto']); ?>" width="90">
                                    -
                                <?php } ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['merk']); ?></td>
                            <td><?php echo htmlspecialchars($row['plat_nomor']); ?></td>
                            <td>Rp <?php echo number_format($row['harga_per_hari'], 0, ',', '.'); ?></td>
                            <td><span class="badge badge-<?php echo $warna; ?>"><?php echo $row['status']; ?></span></td>
                            <td><?php echo $row['jumlah_sewa']; ?> kali</td>
                            <td><?php echo $row['total_hari']; ?> hari</td>
                            <td>Rp <?php echo number_format($row['pendapatan'], 0, ',', '.'); ?></td>
                            <td>
                                <a href="index.php?page=mobil-detail&id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Detail</a>
                            </td>
                        </tr>

                        <?php $no++; ?>
                    <?php } ?>

                    <?php if ($no == 1) { ?>
                        <tr><td colspan="10" class="text-center text-muted">Belum ada mobil atas nama Anda.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>