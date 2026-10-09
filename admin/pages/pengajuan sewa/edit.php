<?php
// Halaman "Proses" pengajuan sewa: ubah status (setujui / tolak / dll).
$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT users.*, rentals.id AS rental_id, rentals.tgl_sewa, rentals.tgl_kembali,
                               rentals.status_rental, cars.merk, cars.plat_nomor, cars.harga_sewa_per_hari
                        FROM rentals
                        JOIN users ON users.id = rentals.pelanggan_id
                        JOIN cars ON cars.id = rentals.car_id
                        WHERE rentals.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$sewa = $stmt->get_result()->fetch_assoc();

if (!$sewa) {
    echo '<div class="alert alert-danger">Data pengajuan tidak ditemukan.</div>';
    return;
}

// Total = lama sewa (hari) x harga per hari, minimal 1 hari
$hari = (strtotime($sewa['tgl_kembali']) - strtotime($sewa['tgl_sewa'])) / 86400;
if ($hari < 1) {
    $hari = 1;
}
$total = $hari * $sewa['harga_sewa_per_hari'];

$pilihan = ['menunggu', 'disetujui', 'ditolak', 'selesai'];

// Data lama yang masih berstatus "berjalan" dianggap "disetujui" supaya dropdown tidak salah pilih
$statusSekarang = ($sewa['status_rental'] == 'berjalan') ? 'disetujui' : $sewa['status_rental'];
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Proses Pengajuan #<?php echo $sewa['rental_id']; ?></h1>
    <a href="index.php?page=sewa" class="btn btn-sm btn-secondary">Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <table class="table table-sm table-borderless">
            <tr><td width="160">Pelanggan</td><td>: <?php echo htmlspecialchars($sewa['nama'] ?? $sewa['name'] ?? $sewa['username'] ?? ''); ?></td></tr>
            <tr><td>Mobil</td><td>: <?php echo htmlspecialchars($sewa['merk']); ?> - <?php echo htmlspecialchars($sewa['plat_nomor']); ?></td></tr>
            <tr><td>Tanggal</td><td>: <?php echo date('d/m/Y', strtotime($sewa['tgl_sewa'])); ?> s/d <?php echo date('d/m/Y', strtotime($sewa['tgl_kembali'])); ?> (<?php echo $hari; ?> hari)</td></tr>
            <tr><td>Total</td><td>: Rp <?php echo number_format($total, 0, ',', '.'); ?></td></tr>
        </table>

        <form action="function/sewa_update.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $sewa['rental_id']; ?>">

            <div class="form-group">
                <label>Status</label>
                <select name="status_rental" class="form-control">
                    <?php foreach ($pilihan as $p) { ?>
                        <option value="<?php echo $p; ?>" <?php echo ($statusSekarang == $p) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($p); ?>
                        </option>
                    <?php } ?>
                </select>
                <small class="text-muted">Disetujui: mobil jadi "disewa". Selesai / ditolak / menunggu: mobil kembali "tersedia".</small>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>