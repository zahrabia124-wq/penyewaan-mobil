<?php
// Halaman edit invoice: ubah status pembayaran (belum bayar / lunas).
$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT users.*, payments.id AS payment_id, payments.total_bayar, payments.status_pembayaran,
                               cars.merk, cars.plat_nomor
                        FROM payments
                        JOIN rentals ON rentals.id = payments.rental_id
                        JOIN users ON users.id = rentals.pelanggan_id
                        JOIN cars ON cars.id = rentals.car_id
                        WHERE payments.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$inv = $stmt->get_result()->fetch_assoc();

if (!$inv) {
    echo '<div class="alert alert-danger">Invoice tidak ditemukan.</div>';
    return;
}
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Invoice INV-<?php echo str_pad($inv['payment_id'], 4, '0', STR_PAD_LEFT); ?></h1>
    <a href="index.php?page=invoice" class="btn btn-sm btn-secondary">Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <table class="table table-sm table-borderless">
            <tr><td width="160">Penyewa</td><td>: <?php echo htmlspecialchars($inv['nama'] ?? $inv['name'] ?? $inv['username'] ?? ''); ?></td></tr>
            <tr><td>Mobil</td><td>: <?php echo htmlspecialchars($inv['merk']); ?> - <?php echo htmlspecialchars($inv['plat_nomor']); ?></td></tr>
            <tr><td>Total Tagihan</td><td>: Rp <?php echo number_format($inv['total_bayar'], 0, ',', '.'); ?></td></tr>
        </table>

        <form action="function/invoice_update.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $inv['payment_id']; ?>">

            <div class="form-group">
                <label>Status Pembayaran</label>
                <select name="status_pembayaran" class="form-control">
                    <option value="belum_bayar" <?php echo ($inv['status_pembayaran'] == 'belum_bayar') ? 'selected' : ''; ?>>Belum bayar</option>
                    <option value="lunas" <?php echo ($inv['status_pembayaran'] == 'lunas') ? 'selected' : ''; ?>>Lunas</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>