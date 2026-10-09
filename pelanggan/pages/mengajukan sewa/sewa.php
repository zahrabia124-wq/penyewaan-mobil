<?php
// Halaman daftar pengajuan sewa milik pelanggan yang sedang login.
// $conn = koneksi database, $pelangganId = id pelanggan, dibuat di index.php

// 1. JOIN = gabungkan tabel rentals (sewa) dengan cars (mobil) supaya nama mobil ikut tampil
$stmt = $conn->prepare("SELECT rentals.*, cars.merk, cars.plat_nomor, cars.harga_sewa_per_hari
                        FROM rentals
                        JOIN cars ON cars.id = rentals.car_id
                        WHERE rentals.pelanggan_id = ?
                        ORDER BY rentals.id DESC");
$stmt->bind_param('i', $pelangganId);
$stmt->execute();
$hasil = $stmt->get_result();
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Mengajukan Sewa</h1>
    <a href="index.php?page=sewa-tambah" class="btn btn-sm btn-primary">+ Ajukan Sewa</a>
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
                                <?php echo htmlspecialchars($row['merk']); ?><br>
                                <small><?php echo htmlspecialchars($row['plat_nomor']); ?></small>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($row['tgl_sewa'])); ?>
                                s/d
                                <?php echo date('d/m/Y', strtotime($row['tgl_kembali'])); ?>
                            </td>
                            <td>Rp <?php echo number_format($total, 0, ',', '.'); ?></td>
                            <td><span class="badge badge-<?php echo $warna; ?>"><?php echo $row['status_rental']; ?></span></td>
                            <td>
                                <?php if ($row['status_rental'] == 'menunggu') { ?>
                                    <!-- Pengajuan hanya bisa dibatalkan selama masih menunggu -->
                                    <form action="function/sewa_delete.php" method="POST" style="display:inline"
                                          onsubmit="return confirm('Yakin batalkan pengajuan ini?')">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Batalkan</button>
                                    </form>
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>
                        </tr>

                        <?php $no++; ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>