<?php
// Form tambah armada. Saat disimpan, data dikirim ke function/armada_insert.php
$users = $conn->query("SELECT * FROM users ORDER BY id");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Armada</h1>
    <a href="index.php?page=armada" class="btn btn-sm btn-secondary">Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="function/armada_insert.php" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label>Merk / Tipe</label>
                <input type="text" name="merk" class="form-control" placeholder="Toyota Avanza" required>
            </div>

            <div class="form-group">
                <label>Plat Nomor</label>
                <input type="text" name="plat_nomor" class="form-control" placeholder="AB 1234 CD" required>
            </div>

            <div class="form-group">
                <label>Harga per Hari (Rp)</label>
                <input type="number" name="harga_per_hari" class="form-control" min="1" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="tersedia">Tersedia</option>
                    <option value="disewa">Disewa</option>
                    <option value="perawatan">Perawatan</option>
                </select>
            </div>

            <div class="form-group">
                <label>Pemilik</label>
                <select name="pemilik_id" class="form-control" required>
                    <option value="">-- Pilih pemilik --</option>
                    <?php while ($u = $users->fetch_assoc()) { ?>
                        <option value="<?php echo $u['id']; ?>">#<?php echo $u['id']; ?> - <?php echo htmlspecialchars($u['nama'] ?? $u['name'] ?? $u['username'] ?? $u['email'] ?? ''); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Foto Mobil</label>
                <input type="file" name="foto" class="form-control-file" accept="image/jpeg,image/png">
                <small class="text-muted">JPG atau PNG, maksimal 2 MB.</small>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>