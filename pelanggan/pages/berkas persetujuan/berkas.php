<?php
// Halaman berkas persetujuan: sewa milik pelanggan yang sudah disetujui admin,
// lengkap dengan denda (tabel returns) dan invoice-nya (tabel payments) kalau sudah diterbitkan.
// $conn = koneksi database, $pelangganId = id pelanggan, dibuat di index.php
$stmt = $conn->prepare("SELECT rentals.id, rentals.tgl_sewa, rentals.tgl_kembali, rentals.status_rental,
                               cars.merk, cars.plat_nomor,
                               payments.id AS payment_id, payments.total_bayar, payments.status_pembayaran,
                               (SELECT COALESCE(SUM(returns.denda), 0) FROM returns
                                WHERE returns.rental_id = rentals.id) AS total_denda,
                               (SELECT GROUP_CONCAT(returns.kondisi_mobil SEPARATOR '; ') FROM returns
                                WHERE returns.rental_id = rentals.id AND returns.denda > 0) AS ket_denda
                        FROM rentals
                        JOIN cars ON cars.id = rentals.car_id
                        LEFT JOIN payments ON payments.rental_id = rentals.id
                        WHERE rentals.pelanggan_id = ?
                          AND rentals.status_rental IN ('disetujui', 'berjalan', 'selesai')
                        ORDER BY rentals.id DESC");
$stmt->bind_param('i', $pelangganId);
$stmt->execute();
$hasil = $stmt->get_result();
$ada = false;
?>

<!-- Berkas Persetujuan Section -->
<section id="berkas" class="section light-background">

  <div class="container section-title" data-aos="fade-up">
    <span>Berkas</span>
    <h2>Berkas Persetujuan</h2>
    <p>Sewa yang disetujui, denda, dan invoice pembayaran</p>
  </div>

  <div class="container">

    <div class="d-flex justify-content-end mb-3 d-print-none">
      <button type="button" onclick="window.print()" class="erent-btn sm"><i class="bi bi-printer"></i> Cetak</button>
    </div>

    <div class="row gy-4">

      <?php while ($row = $hasil->fetch_assoc()) { ?>
        <?php
        $ada = true;
        if ($row['status_pembayaran'] == 'lunas') {
            $warna = 'b-success';
            $teks  = 'Lunas';
        } else {
            $warna = 'b-warning';
            $teks  = 'Belum bayar';
        }
        ?>
        <div class="col-lg-6" data-aos="fade-up">
          <div class="erent-box">

            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h4 class="mb-0"><i class="bi bi-car-front"></i> <?= htmlspecialchars($row['merk']) ?></h4>
                <span class="erent-meta"><?= htmlspecialchars($row['plat_nomor']) ?></span>
              </div>
              <span class="erent-badge b-success"><?= htmlspecialchars($row['status_rental']) ?></span>
            </div>

            <div class="erent-row">
              <span class="erent-meta">Tanggal sewa</span>
              <span><?= date('d/m/Y', strtotime($row['tgl_sewa'])) ?> s/d <?= date('d/m/Y', strtotime($row['tgl_kembali'])) ?></span>
            </div>

            <!-- Denda: tampil walaupun invoice belum diterbitkan -->
            <div class="erent-row">
              <span class="erent-meta">Denda</span>
              <span class="text-end">
                <?php if ($row['total_denda'] > 0) { ?>
                  <span class="text-danger fw-bold">Rp <?= number_format($row['total_denda'], 0, ',', '.') ?></span>
                  <?php if (!empty($row['ket_denda'])) { ?>
                    <br><small class="erent-meta"><?= htmlspecialchars($row['ket_denda']) ?></small>
                  <?php } ?>
                <?php } else { ?>
                  -
                <?php } ?>
              </span>
            </div>

            <?php if ($row['payment_id']) { ?>
              <div class="erent-row">
                <span class="erent-meta">No. Invoice</span>
                <span>INV-<?= str_pad($row['payment_id'], 4, '0', STR_PAD_LEFT) ?></span>
              </div>
              <div class="erent-row">
                <span class="erent-meta">Total tagihan</span>
                <strong>Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></strong>
              </div>
              <div class="erent-row">
                <span class="erent-meta">Pembayaran</span>
                <span class="erent-badge <?= $warna ?>"><?= $teks ?></span>
              </div>
            <?php } else { ?>
              <div class="erent-row">
                <span class="erent-meta">Invoice</span>
                <span class="erent-meta">belum diterbitkan</span>
              </div>
            <?php } ?>

          </div>
        </div>
      <?php } ?>

      <?php if (!$ada) { ?>
        <div class="col-12">
          <div class="erent-item text-center erent-meta">Belum ada sewa yang disetujui.</div>
        </div>
      <?php } ?>

    </div>
  </div>

</section><!-- /Berkas Persetujuan Section -->