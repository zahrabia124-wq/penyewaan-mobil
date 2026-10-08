<?php
// Halaman daftar pengajuan sewa.

// JOIN = gabungkan tabel rentals (sewa) dengan cars (mobil) supaya nama mobil ikut tampil
$hasil = $conn->query("SELECT rentals.*, cars.merk, cars.plat_nomor
                       FROM rentals
                       JOIN cars ON cars.id = rentals.car_id
                       ORDER BY rentals.id DESC");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengajuan Sewa</h1>
    <a href="index.php?page=sewa-tambah" class="btn btn-sm btn-primary">+ Tambah Pengajuan</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Penyewa</th>
                        <th>Mobil</th>
                        <th>Tanggal Sewa</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = $hasil->fetch_assoc()) { ?>

                        <?php
                        // Warna label status
                        if ($row['status'] == 'pengajuan') {
                            $warna = 'info';
                        } elseif ($row['status'] == 'disetujui') {
                            $warna = 'success';
                        } elseif ($row['status'] == 'ditolak') {
                            $warna = 'danger';
                        } else {
                            $warna = 'secondary';
                        }
                        ?>

                        <tr>
                            <td><?php echo $no; ?></td>
                            <td>
                                <?php echo htmlspecialchars($row['nama_penyewa']); ?><br>
                                <small><?php echo htmlspecialchars($row['no_hp']); ?></small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['merk']); ?><br>
                                <small><?php echo htmlspecialchars($row['plat_nomor']); ?></small>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($row['tanggal_mulai'])); ?>
                                s/d
                                <?php echo date('d/m/Y', strtotime($row['tanggal_selesai'])); ?>
                            </td>
                            <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            <td><span class="badge badge-<?php echo $warna; ?>"><?php echo $row['status']; ?></span></td>
                            <td>
                                <a href="index.php?page=sewa-edit&id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Proses</a>

                                <form action="function/sewa_delete.php" method="POST" style="display:inline"
                                      onsubmit="return confirm('Yakin hapus pengajuan ini?')">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        <?php $no++; ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>