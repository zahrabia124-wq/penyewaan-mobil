<?php
// Halaman daftar pengajuan sewa.

// JOIN = gabungkan tabel rentals (sewa) dengan users (pelanggan) dan cars (mobil)
// supaya nama pelanggan, nama mobil, dan nama pemilik ikut tampil
$hasil = $conn->query("SELECT rentals.*, users.nama, users.email, cars.merk, cars.plat_nomor, cars.harga_sewa_per_hari,
                              pemilik.nama AS nama_pemilik
                       FROM rentals
                       LEFT JOIN users ON users.id = rentals.pelanggan_id
                       JOIN cars ON cars.id = rentals.car_id
                       LEFT JOIN users AS pemilik ON pemilik.id = cars.pemilik_id
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
                        if ($row['status_rental'] == 'menunggu') {
                            $warna = 'info';
                        } elseif ($row['status_rental'] == 'disetujui') {
                            $warna = 'success';
                        } elseif ($row['status_rental'] == 'ditolak') {
                            $warna = 'danger';
                        } else {
                            $warna = 'secondary';
                        }

                        // Total = lama sewa (hari) x harga per hari, minimal 1 hari
                        $hari = (strtotime($row['tgl_kembali']) - strtotime($row['tgl_sewa'])) / 86400;
                        if ($hari < 1) {
                            $hari = 1;
                        }
                        $total = $hari * $row['harga_sewa_per_hari'];
                        ?>

                        <tr>
                            <td><?php echo $no; ?></td>
                            <td>
                                <?php if ($row['nama'] !== null) { ?>
                                    <?php echo htmlspecialchars($row['nama']); ?><br>
                                    <small><?php echo htmlspecialchars($row['email'] ?? ''); ?></small>
                                <?php } else { ?>
                                    <!-- Sewa tetap tampil walaupun pelanggan_id tidak ada di tabel users -->
                                    <span class="text-danger">Pelanggan #<?php echo (int) $row['pelanggan_id']; ?></span><br>
                                    <small class="text-danger">tidak ditemukan di tabel users</small>
                                <?php } ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['merk']); ?><br>
                                <small><?php echo htmlspecialchars($row['plat_nomor']); ?></small><br>
                                <small class="text-muted">Pemilik: <?php echo htmlspecialchars($row['nama_pemilik'] ?? 'tidak diketahui'); ?></small>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($row['tgl_sewa'])); ?>
                                s/d
                                <?php echo date('d/m/Y', strtotime($row['tgl_kembali'])); ?>
                            </td>
                            <td>Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                            <td><span class="badge badge-<?php echo $warna; ?>"><?php echo $row['status_rental']; ?></span></td>
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