<?php
// Halaman detail mobil: data mobil + riwayat sewanya.
// Hanya bisa dibuka kalau mobil tersebut milik pemilik yang login.
$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT id, foto, merk, plat_nomor, harga_sewa_per_hari, status_mobil
                        FROM cars
                        WHERE id = ? AND pemilik_id = ?");
$stmt->bind_param('ii', $id, $pemilikId);
$stmt->execute();
$mobil = $stmt->get_result()->fetch_assoc();

if (!$mobil) {
    echo '<div class="alert alert-danger">Data mobil tidak ditemukan.</div>';
    return;
}

// Riwayat sewa mobil ini (+ invoice kalau sudah diterbitkan)
$stmt = $conn->prepare("SELECT rentals.id, rentals.tgl_sewa, rentals.tgl_kembali, rentals.status_rental,
                               users.nama, payments.total_bayar, payments.status_pembayaran
                        FROM rentals
                        LEFT JOIN users ON users.id = rentals.pelanggan_id
                        LEFT JOIN payments ON payments.rental_id = rentals.id
                        WHERE rentals.car_id = ?
                        ORDER BY rentals.id DESC");
$stmt->bind_param('i', $id);
$stmt->execute();
$hasil = $stmt->get_result();
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Mobil</h1>
    <a href="index.php?page=mobil" class="btn btn-sm btn-secondary">Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?php if ($mobil['foto'] != '') { ?>
         <img src="../foto/<?php echo htmlspecialchars($mobil['foto']); ?>" width="300">
        <?php } ?>
        <table class="table table-sm table-borderless">
            <tr><td width="160">Merk</td><td>: <?php echo htmlspecialchars($mobil['merk']); ?></td></tr>
            <tr><td>Plat Nomor</td><td>: <?php echo htmlspecialchars($mobil['plat_nomor']); ?></td></tr>
            <tr><td>Harga / Hari</td><td>: Rp <?php echo number_format($mobil['harga_sewa_per_hari'], 0, ',', '.'); ?></td></tr>
            <tr><td>Status</td><td>: <?php echo htmlspecialchars($mobil['status_mobil']); ?></td></tr>
        </table>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Riwayat Sewa</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Penyewa</th>
                        <th>Tanggal Sewa</th>
                        <th>Lama</th>
                        <th>Status Sewa</th>
                        <th>Total Bayar</th>
                        <th>Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($row = $hasil->fetch_assoc()) { ?>

                        <?php
                        // Warna label status sewa
                        if ($row['status_rental'] == 'menunggu') {
                            $warna = 'info';
                        } elseif ($row['status_rental'] == 'disetujui') {
                            $warna = 'success';
                        } elseif ($row['status_rental'] == 'ditolak') {
                            $warna = 'danger';
                        } else {
                            $warna = 'secondary';
                        }

                        // Lama sewa (hari), minimal 1 hari
                        $hari = (strtotime($row['tgl_kembali']) - strtotime($row['tgl_sewa'])) / 86400;
                        if ($hari < 1) {
                            $hari = 1;
                        }
                        ?>

                        <tr>
                            <td><?php echo $no; ?></td>
                            <td><?php echo htmlspecialchars($row['nama'] ?? '-'); ?></td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($row['tgl_sewa'])); ?>
                                s/d
                                <?php echo date('d/m/Y', strtotime($row['tgl_kembali'])); ?>
                            </td>
                            <td><?php echo $hari; ?> hari</td>
                            <td><span class="badge badge-<?php echo $warna; ?>"><?php echo $row['status_rental']; ?></span></td>
                            <td>
                                <?php if ($row['total_bayar'] !== null) { ?>
                                    Rp <?php echo number_format($row['total_bayar'], 0, ',', '.'); ?>
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($row['status_pembayaran'] == 'lunas') { ?>
                                    <span class="badge badge-success">Lunas</span>
                                <?php } elseif ($row['status_pembayaran'] !== null) { ?>
                                    <span class="badge badge-warning">Belum bayar</span>
                                <?php } else { ?>
                                    <span class="badge badge-secondary">Belum ada invoice</span>
                                <?php } ?>
                            </td>
                        </tr>

                        <?php $no++; ?>
                    <?php } ?>

                    <?php if ($no == 1) { ?>
                        <tr><td colspan="7" class="text-center text-muted">Mobil ini belum pernah disewa.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>