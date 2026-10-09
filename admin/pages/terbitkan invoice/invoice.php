<?php
// Halaman daftar invoice. Invoice disimpan di tabel payments.
$hasil = $conn->query("SELECT users.*, payments.id AS payment_id, payments.total_bayar, payments.status_pembayaran,
                              cars.merk, cars.plat_nomor
                       FROM payments
                       JOIN rentals ON rentals.id = payments.rental_id
                       JOIN users ON users.id = rentals.pelanggan_id
                       JOIN cars ON cars.id = rentals.car_id
                       ORDER BY payments.id DESC");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Terbitkan Invoice</h1>
    <a href="index.php?page=invoice-tambah" class="btn btn-sm btn-primary">+ Terbitkan Invoice</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Penyewa</th>
                        <th>Mobil</th>
                        <th>Total Tagihan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
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
                            <td>INV-<?php echo str_pad($row['payment_id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($row['nama'] ?? $row['name'] ?? $row['username'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['merk']); ?> - <?php echo htmlspecialchars($row['plat_nomor']); ?></td>
                            <td>Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                            <td><span class="badge badge-<?php echo $warna; ?>"><?php echo $teks; ?></span></td>
                            <td>
                                <?php if ($row['status_pembayaran'] == 'lunas') { ?>
                                    <!-- Batalkan status lunas (kalau salah klik) -->
                                    <form action="function/invoice_lunas.php" method="POST" style="display:inline"
                                          onsubmit="return confirm('Kembalikan status ke belum bayar?')">
                                        <input type="hidden" name="id" value="<?php echo $row['payment_id']; ?>">
                                        <input type="hidden" name="status" value="belum_bayar">
                                        <button type="submit" class="btn btn-secondary btn-sm">Batalkan Lunas</button>
                                    </form>
                                <?php } else { ?>
                                    <!-- Tandai sudah dibayar -->
                                    <form action="function/invoice_lunas.php" method="POST" style="display:inline"
                                          onsubmit="return confirm('Tandai invoice ini sudah lunas?')">
                                        <input type="hidden" name="id" value="<?php echo $row['payment_id']; ?>">
                                        <input type="hidden" name="status" value="lunas">
                                        <button type="submit" class="btn btn-success btn-sm">Tandai Lunas</button>
                                    </form>
                                <?php } ?>

                                <a href="index.php?page=invoice-edit&id=<?php echo $row['payment_id']; ?>" class="btn btn-warning btn-sm">Edit</a>

                                <form action="function/invoice_delete.php" method="POST" style="display:inline"
                                      onsubmit="return confirm('Yakin hapus invoice ini?')">
                                    <input type="hidden" name="id" value="<?php echo $row['payment_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>