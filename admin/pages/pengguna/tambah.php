<?php
// Form tambah pengguna. Saat disimpan, data dikirim ke function/pengguna_insert.php
$status = $_GET['status'] ?? '';
$pesan  = $_GET['pesan'] ?? '';
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah User</h1>
    <a href="index.php?page=pengguna" class="btn btn-sm btn-secondary">Kembali</a>
</div>

<?php if ($pesan != '') { ?>
    <div class="alert alert-<?php echo ($status == 'sukses') ? 'success' : 'danger'; ?>">
        <?php echo htmlspecialchars($pesan); ?>
    </div>
<?php } ?>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="function/pengguna_insert.php" method="POST">

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" minlength="6" required>
                <small class="text-muted">Minimal 6 karakter. Disimpan dalam bentuk terenkripsi.</small>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="">-- Pilih role --</option>
                    <option value="pemilik">Pemilik Kendaraan</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>