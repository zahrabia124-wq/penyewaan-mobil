<?php
// Halaman daftar denda.
// Data denda ada di tabel returns, digabung dengan rentals, users (penyewa) dan cars (mobil).
$hasil = $conn->query("SELECT returns.id AS returns_id, returns.tgl_dikembalikan, returns.denda,
                              returns.kondisi_mobil,
                              users.nama, cars.merk, cars.plat_nomor,
                              payments.status_pembayaran
                       FROM returns
                       JOIN rentals ON rentals.id = returns.rental_id
                       LEFT JOIN payments ON payments.rental_id = rentals.id
                       JOIN users ON users.id = rentals.pelanggan_id
                       JOIN cars ON cars.id = rentals.car_id
                       WHERE returns.denda > 0
                       ORDER BY returns.id DESC");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Data Denda</h1>
    <a href="index.php?page=denda-tambah" class="btn btn-sm btn-primary">+ Tambah Denda</a>
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
                        <th>Tgl Dikembalikan</th>
                        <th>Denda</th>
                        <th>Kondisi Mobil</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = $hasil->fetch_assoc()) { ?>
                        <?php
                        // Status denda mengikuti status invoice sewa yang sama (tabel payments)
                        if ($row['status_pembayaran'] == 'lunas') {
                            $warna = 'success';
                            $teks  = 'Lunas';
                        } elseif ($row['status_pembayaran'] == 'belum_bayar') {
                            $warna = 'warning';
                            $teks  = 'Belum bayar';
                        } else {
                            $warna = 'secondary';
                            $teks  = 'Belum ada invoice';
                        }
                        ?>
                        <tr>
                            <td><?php echo $no; ?></td>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td><?php echo htmlspecialchars($row['merk']); ?> - <?php echo htmlspecialchars($row['plat_nomor']); ?></td>
                            <td><?php echo htmlspecialchars($row['tgl_dikembalikan']); ?></td>
                            <td>Rp <?php echo number_format($row['denda'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['kondisi_mobil'] ?? '-'); ?></td>
                            <td><span class="badge badge-<?php echo $warna; ?>"><?php echo $teks; ?></span></td>
                            <td>
                                <a href="index.php?page=denda-edit&id=<?php echo $row['returns_id']; ?>" class="btn btn-warning btn-sm">Edit</a>

                                <form action="function/denda_delete.php" method="POST" style="display:inline"
                                      onsubmit="return confirm('Yakin hapus denda ini?')">
                                    <input type="hidden" name="id" value="<?php echo $row['returns_id']; ?>">
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