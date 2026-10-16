<?php
// Halaman daftar pengajuan sewa milik pelanggan yang sedang login.
// $conn = koneksi database, $pelangganId = id pelanggan, dibuat di index.php
$stmt = $conn->prepare("SELECT rentals.*, cars.merk, cars.plat_nomor, cars.harga_sewa_per_hari
                        FROM rentals
                        JOIN cars ON cars.id = rentals.car_id
                        WHERE rentals.pelanggan_id = ?
                        ORDER BY rentals.id DESC");
$stmt->bind_param('i', $pelangganId);
$stmt->execute();
$hasil = $stmt->get_result();
$ada = false;
?>

<!-- Pengajuan Sewa Saya Section -->
<section id="sewa" class="section">

  <div class="container section-title" data-aos="fade-up">
    <span>Riwayat</span>
    <h2>Pengajuan Sewa Saya</h2>
    <p>Pantau status pengajuan sewa kamu</p>
  </div>

  <div class="container">
    <div class="row gy-3">

      <?php while ($row = $hasil->fetch_assoc()) { ?>
        <?php
        $ada = true;
        if ($row['status_rental'] == 'menunggu') {
            $warna = 'b-warning';
        } elseif ($row['status_rental'] == 'disetujui') {
            $warna = 'b-success';
        } elseif ($row['status_rental'] == 'ditolak') {
            $warna = 'b-danger';
        } else {
            $warna = '';
        }

        // Total = lama sewa (hari) x harga per hari, minimal 1 hari
        $hari = (strtotime($row['tgl_kembali']) - strtotime($row['tgl_sewa'])) / 86400;
        if ($hari < 1) {
            $hari = 1;
        }
        $total = $hari * $row['harga_sewa_per_hari'];
        ?>
        <div class="col-12" data-aos="fade-up">
          <div class="erent-item">
            <div class="row align-items-center gy-2">
              <div class="col-md-3">
                <h5 class="mb-0"><i class="bi bi-car-front"></i> <?= htmlspecialchars($row['merk']) ?></h5>
                <span class="erent-meta"><?= htmlspecialchars($row['plat_nomor']) ?></span>
              </div>
              <div class="col-md-3">
                <div class="erent-meta">Tanggal sewa</div>
                <?= date('d/m/Y', strtotime($row['tgl_sewa'])) ?> s/d <?= date('d/m/Y', strtotime($row['tgl_kembali'])) ?>
              </div>
              <div class="col-md-2">
                <div class="erent-meta">Total</div>
                <strong>Rp <?= number_format($total, 0, ',', '.') ?></strong>
              </div>
              <div class="col-md-2">
                <span class="erent-badge <?= $warna ?>"><?= htmlspecialchars($row['status_rental']) ?></span>
              </div>
              <div class="col-md-2 text-md-end">
                <?php if ($row['status_rental'] == 'menunggu') { ?>
                  <!-- Pengajuan hanya bisa dibatalkan selama masih menunggu -->
                  <form action="pelanggan/function/sewa_delete.php" method="POST" class="d-inline"
                        onsubmit="return confirm('Yakin batalkan pengajuan ini?')">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit" class="erent-btn danger sm">Batalkan</button>
                  </form>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>

      <?php if (!$ada) { ?>
        <div class="col-12">
          <div class="erent-item text-center erent-meta">
            Belum ada pengajuan sewa. <a href="pelanggan/ajukan.php">Ajukan sewa sekarang</a>
          </div>
        </div>
      <?php } ?>

    </div>
  </div>

</section><!-- /Pengajuan Sewa Saya Section -->