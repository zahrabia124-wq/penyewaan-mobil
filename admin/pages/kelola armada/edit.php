<?php
// Form edit armada. Saat disimpan, data dikirim ke function/armada_update.php
// $conn = koneksi database, dibuat di index.php

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$mobil = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Hanya user dengan role pemilik yang bisa dipilih sebagai pemilik armada.
// Pemilik yang sekarang tetap ikut tampil supaya datanya tidak berubah sendiri.
$pemilikSekarang = (int) ($mobil['pemilik_id'] ?? 0);
$users = $conn->query("SELECT * FROM users WHERE role LIKE '%pemilik%' OR id = $pemilikSekarang ORDER BY id");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Armada</h1>
    <a href="index.php?page=armada" class="btn btn-sm btn-secondary">Kembali</a>
</div>

<?php if (!$mobil) { ?>

    <div class="alert alert-danger">Data armada tidak ditemukan.</div>

<?php } else { ?>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="function/armada_update.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?php echo $mobil['id']; ?>">

            <div class="form-group">
                <label>Merk / Tipe</label>
                <input type="text" name="merk" class="form-control"
                       value="<?php echo htmlspecialchars($mobil['merk']); ?>" required>
            </div>

            <div class="form-group">
                <label>Plat Nomor</label>
                <input type="text" name="plat_nomor" class="form-control"
                       value="<?php echo htmlspecialchars($mobil['plat_nomor']); ?>" required>
            </div>

            <div class="form-group">
                <label>Harga per Hari (Rp)</label>
                <input type="number" name="harga_per_hari" class="form-control" min="1"
                       value="<?php echo (float) $mobil['harga_sewa_per_hari']; ?>" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <?php foreach (['tersedia' => 'Tersedia', 'disewa' => 'Disewa', 'perawatan' => 'Perawatan'] as $nilai => $label) { ?>
                        <option value="<?php echo $nilai; ?>" <?php echo ($mobil['status_mobil'] == $nilai) ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Pemilik</label>
                <select name="pemilik_id" class="form-control" required>
                    <option value="">-- Pilih pemilik --</option>
                    <?php while ($u = $users->fetch_assoc()) { ?>
                        <option value="<?php echo $u['id']; ?>" <?php echo ($mobil['pemilik_id'] == $u['id']) ? 'selected' : ''; ?>>
                            #<?php echo $u['id']; ?> - <?php echo htmlspecialchars($u['nama'] ?? $u['name'] ?? $u['username'] ?? $u['email'] ?? ''); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Foto Mobil</label>
                <?php if ($mobil['foto'] != '') { ?>
                    <div><img src="../foto/<?php echo htmlspecialchars($mobil['foto']); ?>" width="200"></div>
                <?php } ?>
                <input type="file" name="foto" class="form-control-file" accept="image/jpeg,image/png">
                <small class="text-muted">Kosongkan jika tidak ingin mengganti foto. JPG atau PNG, maksimal 2 MB.</small>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>

<?php } ?>