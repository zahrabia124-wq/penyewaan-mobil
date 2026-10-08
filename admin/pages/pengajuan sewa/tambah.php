<?php
// Form tambah pengajuan sewa.
// Pilihan mobil hanya menampilkan mobil yang statusnya "tersedia".
$mobil = $conn->query("SELECT *, harga_sewa_per_hari AS harga_per_hari FROM cars WHERE status_mobil = 'tersedia' ORDER BY merk");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Pengajuan Sewa</h1>
    <a href="index.php?page=sewa" class="btn btn-sm btn-secondary">Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="function/sewa_insert.php" method="POST">

            <div class="form-group">
                <label>Nama Penyewa</label>
                <input type="text" name="nama_penyewa" class="form-control" required>
            </div>

            <div class="form-group">
                <label>No. HP</label>
                <input type="text" name="no_hp" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Mobil</label>
                <select name="car_id" class="form-control" required>
                    <option value="">-- Pilih mobil --</option>
                    <?php while ($c = $mobil->fetch_assoc()) { ?>
                        <option value="<?php echo $c['id']; ?>">
                            <?php echo htmlspecialchars($c['merk']); ?> - <?php echo htmlspecialchars($c['plat_nomor']); ?>
                            (Rp <?php echo number_format($c['harga_per_hari'], 0, ',', '.'); ?>/hari)
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control" required>
            </div>

            <p class="text-muted">Total harga dihitung otomatis: lama sewa x harga per hari.</p>

            <button type="submit" class="btn btn-primary">Ajukan</button>
        </form>
    </div>
</div>