<?php
// Form edit denda. Saat disimpan, data dikirim ke function/denda_update.php
// Denda disimpan di tabel returns (kolom denda, tgl_dikembalikan, kondisi_mobil).
// $conn = koneksi database, dibuat di index.php

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT returns.*, users.nama, cars.merk, cars.plat_nomor
                        FROM returns
                        JOIN rentals ON rentals.id = returns.rental_id
                        JOIN users ON users.id = rentals.pelanggan_id
                        JOIN cars ON cars.id = rentals.car_id
                        WHERE returns.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Denda</h1>
    <a href="index.php?page=denda" class="btn btn-sm btn-secondary">Kembali</a>
</div>

<?php if (!$data) { ?>

    <div class="alert alert-danger">Data denda tidak ditemukan.</div>

<?php } else { ?>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="function/denda_update.php" method="POST">

            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

            <div class="form-group">
                <label>Sewa</label>
                <input type="text" class="form-control" disabled
                       value="#<?php echo $data['rental_id']; ?> - <?php echo htmlspecialchars($data['nama']); ?> - <?php echo htmlspecialchars($data['merk']); ?> (<?php echo htmlspecialchars($data['plat_nomor']); ?>)">
            </div>

            <div class="form-group">
                <label>Tanggal Dikembalikan</label>
                <input type="date" name="tgl_dikembalikan" class="form-control"
                       value="<?php echo htmlspecialchars($data['tgl_dikembalikan']); ?>" required>
            </div>

            <div class="form-group">
                <label>Denda (Rp)</label>
                <input type="number" name="denda" class="form-control" min="1" step="0.01"
                       value="<?php echo (float) $data['denda']; ?>" required>
            </div>

            <div class="form-group">
                <label>Kondisi Mobil / Keterangan</label>
                <textarea name="kondisi_mobil" class="form-control" rows="3"
                          placeholder="Contoh: Terlambat 2 hari, bumper lecet"><?php echo htmlspecialchars($data['kondisi_mobil'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<?php } ?>