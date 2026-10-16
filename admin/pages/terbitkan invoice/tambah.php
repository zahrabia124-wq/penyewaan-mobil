<?php
// Form terbitkan invoice.
// Hanya sewa yang sudah disetujui dan belum punya invoice yang bisa dipilih.
// Total tagihan = total sewa + denda pada sewa itu (diambil dari tabel returns).
$sewa = $conn->query("SELECT users.nama, rentals.id AS rental_id, rentals.tgl_sewa, rentals.tgl_kembali,
                             cars.merk, cars.plat_nomor, cars.harga_sewa_per_hari,
                             (SELECT COALESCE(SUM(returns.denda), 0) FROM returns WHERE returns.rental_id = rentals.id) AS total_denda
                      FROM rentals
                      JOIN users ON users.id = rentals.pelanggan_id
                      JOIN cars ON cars.id = rentals.car_id
                      WHERE rentals.status_rental NOT IN ('menunggu', 'ditolak')
                      AND rentals.id NOT IN (SELECT rental_id FROM payments)
                      ORDER BY rentals.id DESC");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Terbitkan Invoice</h1>
    <a href="index.php?page=invoice" class="btn btn-sm btn-secondary">Kembali</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="function/invoice_insert.php" method="POST">

            <div class="form-group">
                <label>Sewa</label>
                <select name="rental_id" class="form-control" required>
                    <option value="">-- Pilih sewa --</option>
                    <?php while ($s = $sewa->fetch_assoc()) { ?>
                        <?php
                        $hari = (strtotime($s['tgl_kembali']) - strtotime($s['tgl_sewa'])) / 86400;
                        if ($hari < 1) {
                            $hari = 1;
                        }
                        $total = ($hari * $s['harga_sewa_per_hari']) + $s['total_denda'];
                        ?>
                        <option value="<?php echo $s['rental_id']; ?>">
                            #<?php echo $s['rental_id']; ?> - <?php echo htmlspecialchars($s['nama']); ?>
                            - <?php echo htmlspecialchars($s['merk']); ?> (<?php echo htmlspecialchars($s['plat_nomor']); ?>)
                            - Tagihan Rp <?php echo number_format($total, 0, ',', '.'); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Terbitkan</button>
        </form>
    </div>
</div>