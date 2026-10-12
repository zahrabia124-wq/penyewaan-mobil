<?php
// Halaman memilih mobil. Hanya mobil yang statusnya "tersedia" yang tampil.
// $conn = koneksi database, dibuat di index.php

// 1. Ambil mobil yang tersedia, urut berdasarkan merk
$hasil = $conn->query("SELECT *, harga_sewa_per_hari AS harga_per_hari, status_mobil AS status FROM cars WHERE status_mobil = 'tersedia' ORDER BY merk");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Memilih Mobil</h1>
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>

                    <!-- 2. Ulangi baris tabel untuk setiap mobil -->
                    <?php while ($row = $hasil->fetch_assoc()) { ?>

                        <tr>
                            <td><?php echo $no; ?></td>
                            <td>
                                <?php if ($row['foto'] != '') { ?>
                                    <img src="uploads/mobil/<?php echo $row['foto']; ?>" width="90">
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['merk']); ?></td>
                            <td><?php echo htmlspecialchars($row['plat_nomor']); ?></td>
                            <td>Rp <?php echo number_format($row['harga_per_hari'], 0, ',', '.'); ?></td>
                            <td><span class="badge badge-success"><?php echo $row['status']; ?></span></td>
                            <td>
                                <!-- Pilih mobil -> lanjut ke form pengajuan sewa -->
                                <a href="index.php?page=sewa-tambah&car_id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Pilih & Ajukan</a>
                            </td>
                        </tr>

                        <?php $no++; ?>
                    <?php } ?>

                    <?php if ($no == 1) { ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada mobil yang tersedia.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>