<?php
// Form tambah denda.
// Denda disimpan di tabel returns (kolom denda & kondisi_mobil).
// Pilihan sewa: hanya sewa yang sudah disetujui / selesai.
$sewa = $conn->query("SELECT rentals.id AS rental_id, users.nama, cars.merk, cars.plat_nomor
                      FROM rentals
                      JOIN users ON users.id = rentals.pelanggan_id
                      JOIN cars ON cars.id = rentals.car_id
                      WHERE rentals.status_rental IN ('disetujui', 'selesai')
                      ORDER BY rentals.id DESC");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Denda</h1>
    <a href="index.php?page=denda" class="btn btn-sm btn-secondary">Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="function/denda_insert.php" method="POST">

            <div class="form-group">
                <label>Sewa</label>
                <select name="rental_id" class="form-control" required>
                    <option value="">-- Pilih sewa --</option>
                    <?php while ($s = $sewa->fetch_assoc()) { ?>
                        <option value="<?php echo $s['rental_id']; ?>">
                            #<?php echo $s['rental_id']; ?> - <?php echo htmlspecialchars($s['nama']); ?>
                            - <?php echo htmlspecialchars($s['merk']); ?> (<?php echo htmlspecialchars($s['plat_nomor']); ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tanggal Dikembalikan</label>
                <input type="date" name="tgl_dikembalikan" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label>Denda (Rp)</label>
                <input type="number" name="denda" class="form-control" min="1" step="0.01" required>
            </div>

            <div class="form-group">
                <label>Kondisi Mobil / Keterangan</label>
                <textarea name="kondisi_mobil" class="form-control" rows="3"
                          placeholder="Contoh: Terlambat 2 hari, bumper lecet"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>