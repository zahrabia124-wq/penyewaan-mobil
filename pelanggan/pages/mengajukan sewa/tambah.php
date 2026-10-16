<?php
// Form pengajuan sewa. Saat disimpan, data dikirim ke function/sewa_insert.php
// Pilihan mobil hanya menampilkan mobil yang statusnya "tersedia".
$carDipilih = (int) ($_GET['car_id'] ?? 0);
$mobil = $conn->query("SELECT *, harga_sewa_per_hari AS harga_per_hari FROM cars WHERE status_mobil = 'tersedia' ORDER BY merk");
?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="pelanggan/function/sewa_insert.php" method="POST">

                    <div class="mb-3">
                        <label class="form-label">Mobil</label>
                        <select name="car_id" class="form-select" required>
                            <option value="">-- Pilih mobil --</option>
                            <?php while ($c = $mobil->fetch_assoc()) { ?>
                                <option value="<?= $c['id'] ?>" <?= ($c['id'] == $carDipilih) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['merk']) ?> - <?= htmlspecialchars($c['plat_nomor']) ?>
                                    (Rp <?= number_format($c['harga_per_hari'], 0, ',', '.') ?>/hari)
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Sewa</label>
                            <input type="date" name="tgl_sewa" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Kembali</label>
                            <input type="date" name="tgl_kembali" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <p class="text-muted small">Total harga dihitung otomatis: lama sewa x harga per hari.</p>

                    <a href="index.php?page=sewa" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Ajukan</button>
                </form>
            </div>
        </div>
    </div>
</div>