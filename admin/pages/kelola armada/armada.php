<?php
// Halaman daftar armada.
// $conn = koneksi database, dibuat di index.php

// 1. Ambil semua data mobil beserta pemiliknya, yang terbaru di atas
$hasil = $conn->query("SELECT cars.*, cars.harga_sewa_per_hari AS harga_per_hari, cars.status_mobil AS status,
                              pemilik.nama AS nama_pemilik, pemilik.email AS email_pemilik
                       FROM cars
                       LEFT JOIN users AS pemilik ON pemilik.id = cars.pemilik_id
                       ORDER BY cars.id DESC");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kelola Armada</h1>
    <a href="index.php?page=armada-tambah" class="btn btn-sm btn-primary">+ Tambah Armada</a>
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
                        <th>Pemilik</th>
                        <th>Harga / Hari</th>
                        <th>Status</th>
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
                                <?php } else { ?>
                                    -
                                <?php } ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['merk']); ?></td>
                            <td><?php echo htmlspecialchars($row['plat_nomor']); ?></td>
                            <td>
                                <?php if ($row['nama_pemilik'] !== null) { ?>
                                    <?php echo htmlspecialchars($row['nama_pemilik']); ?><br>
                                    <small><?php echo htmlspecialchars($row['email_pemilik'] ?? ''); ?></small>
                                <?php } else { ?>
                                    <span class="text-danger">Pemilik #<?php echo (int) $row['pemilik_id']; ?></span><br>
                                    <small class="text-danger">tidak ditemukan di tabel users</small>
                                <?php } ?>
                            </td>
                            <td>Rp <?php echo number_format($row['harga_per_hari'], 0, ',', '.'); ?></td>
                            <td><span class="badge badge-<?php echo $warna; ?>"><?php echo $row['status']; ?></span></td>
                            <td>
                                <a href="index.php?page=armada-edit&id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>

                                <!-- Tombol hapus pakai form POST + konfirmasi -->
                                <form action="function/armada_delete.php" method="POST" style="display:inline"
                                      onsubmit="return confirm('Yakin hapus armada ini?')">
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