<?php
// Beranda + Dashboard E-Rent (pelanggan). $conn, $pelangganId, $namaPelanggan, $tpl datang dari index.php
// Sesuaikan nilai status di bawah dengan isi kolom di databasemu.
$STATUS_MENUNGGU = 'menunggu';   // rentals.status_rental: pengajuan baru
$STATUS_BERJALAN = 'disetujui';  // rentals.status_rental: sedang disewa
$STATUS_TERSEDIA = 'tersedia';   // cars.status_mobil
$STATUS_LUNAS    = 'lunas';      // payments.status_pembayaran

$idPelanggan = (int) $pelangganId;

function angka($conn, $sql) {
    $r = $conn->query($sql);
    return $r ? ($r->fetch_row()[0] ?? 0) : 0;
}

$totalArmada    = angka($conn, "SELECT COUNT(*) FROM cars");
$armadaTersedia = angka($conn, "SELECT COUNT(*) FROM cars WHERE status_mobil = '$STATUS_TERSEDIA'");
$totalMenunggu  = angka($conn, "SELECT COUNT(*) FROM rentals WHERE pelanggan_id = $idPelanggan AND status_rental = '$STATUS_MENUNGGU'");
$totalBerjalan  = angka($conn, "SELECT COUNT(*) FROM rentals WHERE pelanggan_id = $idPelanggan AND status_rental = '$STATUS_BERJALAN'");
$totalBayar     = angka($conn, "SELECT COALESCE(SUM(payments.total_bayar),0) FROM payments
                                JOIN rentals ON rentals.id = payments.rental_id
                                WHERE rentals.pelanggan_id = $idPelanggan AND payments.status_pembayaran = '$STATUS_LUNAS'");
$totalTagihan   = angka($conn, "SELECT COALESCE(SUM(payments.total_bayar),0) FROM payments
                                JOIN rentals ON rentals.id = payments.rental_id
                                WHERE rentals.pelanggan_id = $idPelanggan AND payments.status_pembayaran <> '$STATUS_LUNAS'");
$totalDenda     = angka($conn, "SELECT COALESCE(SUM(returns.denda),0) FROM returns
                                JOIN rentals ON rentals.id = returns.rental_id
                                WHERE rentals.pelanggan_id = $idPelanggan");

$sewaTerbaru = $conn->query("SELECT rentals.id, rentals.tgl_sewa, rentals.tgl_kembali, rentals.status_rental,
                                    cars.merk, cars.plat_nomor, cars.foto
                             FROM rentals
                             JOIN cars ON cars.id = rentals.car_id
                             WHERE rentals.pelanggan_id = $idPelanggan
                             ORDER BY rentals.id DESC
                             LIMIT 5");

// Sewa yang sedang aktif (menunggu persetujuan / disetujui) paling dekat tanggalnya
$qAktif = $conn->query("SELECT rentals.*, cars.merk, cars.plat_nomor, cars.foto, cars.harga_sewa_per_hari
                        FROM rentals
                        JOIN cars ON cars.id = rentals.car_id
                        WHERE rentals.pelanggan_id = $idPelanggan
                          AND rentals.status_rental IN ('$STATUS_MENUNGGU', '$STATUS_BERJALAN')
                        ORDER BY rentals.tgl_sewa ASC
                        LIMIT 1");
$aktif = $qAktif ? $qAktif->fetch_assoc() : null;
?>

<!-- Hero Section -->
<section id="hero" class="hero section dark-background">

  <img src="<?= $tpl ?>/img/world-dotted-map.png" alt="" class="hero-bg" data-aos="fade-in">

  <div class="container">
    <div class="row gy-4 d-flex justify-content-between">
      <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
        <h2 data-aos="fade-up"><?= $sudahLogin ? 'Halo, ' . htmlspecialchars($namaPelanggan) . '! ' : '' ?>Sewa Mobil Jadi Lebih Mudah</h2>
        <p data-aos="fade-up" data-aos-delay="100"><?= $sudahLogin ? 'Pilih mobil yang tersedia, ajukan sewa, lalu pantau persetujuan, denda, dan invoice kamu dalam satu tempat.' : 'Lihat dulu mobil yang tersedia. Login hanya diperlukan saat kamu mau mengajukan sewa.' ?></p>

        <div class="d-flex gap-2 mb-4" data-aos="fade-up" data-aos-delay="200">
          <a href="#mobil" class="btn btn-primary">Pilih Mobil</a>
          <a href="pelanggan/ajukan.php" class="btn btn-warning">Ajukan Sewa</a>
        </div>

        <div class="row gy-4" data-aos="fade-up" data-aos-delay="300">
          <div class="col-lg-3 col-6">
            <div class="stats-item text-center w-100 h-100">
              <span><?= $armadaTersedia ?></span>
              <p>Mobil Tersedia</p>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stats-item text-center w-100 h-100">
              <span><?= $totalArmada ?></span>
              <p>Total Armada</p>
            </div>
          </div>
          <?php if ($sudahLogin) { ?>
          <div class="col-lg-3 col-6">
            <div class="stats-item text-center w-100 h-100">
              <span><?= $totalMenunggu ?></span>
              <p>Menunggu</p>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="stats-item text-center w-100 h-100">
              <span><?= $totalBerjalan ?></span>
              <p>Berjalan</p>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>

      <div class="col-lg-5 order-1 order-lg-2 hero-img" data-aos="zoom-out">
        <img src="<?= $tpl ?>/img/hero-img.svg" class="img-fluid mb-3 mb-lg-0" alt="">
      </div>
    </div>
  </div>

</section><!-- /Hero Section -->

<!-- Featured Services Section -->
<section id="featured-services" class="featured-services section">
  <div class="container">
    <div class="row gy-4">

      <div class="col-lg-4 col-md-6 service-item d-flex" data-aos="fade-up" data-aos-delay="100">
        <div class="icon flex-shrink-0"><i class="bi bi-car-front"></i></div>
        <div>
          <h4 class="title">Memilih Mobil</h4>
          <p class="description">Lihat semua mobil yang tersedia beserta harga sewa per hari.</p>
          <a href="#mobil" class="readmore stretched-link"><span>Lihat Mobil</span><i class="bi bi-arrow-right"></i></a>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 service-item d-flex" data-aos="fade-up" data-aos-delay="200">
        <div class="icon flex-shrink-0"><i class="bi bi-key"></i></div>
        <div>
          <h4 class="title">Mengajukan Sewa</h4>
          <p class="description">Ajukan sewa mobil pilihanmu dan pantau status pengajuannya.</p>
          <a href="pelanggan/ajukan.php" class="readmore stretched-link"><span>Ajukan Sewa</span><i class="bi bi-arrow-right"></i></a>
        </div>
      </div>

      <div class="col-lg-4 col-md-6 service-item d-flex" data-aos="fade-up" data-aos-delay="300">
        <div class="icon flex-shrink-0"><i class="bi bi-file-earmark-text"></i></div>
        <div>
          <h4 class="title">Berkas Persetujuan</h4>
          <p class="description">Lihat sewa yang disetujui, denda, dan invoice pembayaran.</p>
          <a href="#berkas" class="readmore stretched-link"><span>Lihat Berkas</span><i class="bi bi-arrow-right"></i></a>
        </div>
      </div>

    </div>
  </div>
</section><!-- /Featured Services Section -->

<!-- Call To Action Section -->
<section id="call-to-action" class="call-to-action section dark-background">

  <img src="<?= $tpl ?>/img/cta-bg.jpg" alt="">

  <div class="container">
    <div class="row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
      <div class="col-xl-10">
        <div class="text-center">
          <h3>Butuh Mobil Sekarang?</h3>
          <p>Pilih mobil, isi tanggal sewa, dan ajukan. Kamu bisa memantau persetujuannya langsung di halaman ini.</p>
          <a class="cta-btn" href="pelanggan/ajukan.php">Ajukan Sewa</a>
        </div>
      </div>
    </div>
  </div>

</section><!-- /Call To Action Section -->

<!-- Ringkasan Section (hanya untuk pelanggan yang sudah login) -->
<?php if ($sudahLogin) { ?>
<section id="dashboard" class="section light-background">

  <div class="container section-title" data-aos="fade-up">
    <span>Dashboard</span>
    <h2>Ringkasan Sewa Saya</h2>
    <p>Sewa yang sedang berjalan, tagihan, dan riwayat terbaru</p>
  </div>

  <div class="container">
    <div class="row gy-4">

      <!-- Sewa aktif -->
      <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
        <div class="erent-box p-0 overflow-hidden">
          <?php if ($aktif) { ?>
            <?php
            $hari = (strtotime($aktif['tgl_kembali']) - strtotime($aktif['tgl_sewa'])) / 86400;
            if ($hari < 1) { $hari = 1; }
            $total = $hari * $aktif['harga_sewa_per_hari'];

            $hariIni = strtotime(date('Y-m-d'));
            $mulai   = strtotime($aktif['tgl_sewa']);
            $selesai = strtotime($aktif['tgl_kembali']);
            $disetujui = ($aktif['status_rental'] == $STATUS_BERJALAN);
            $infoWarna = 'text-body';
            if (!$disetujui) {
                $info = 'Menunggu persetujuan admin';
            } elseif ($hariIni < $mulai) {
                $info = 'Mulai ' . (int) (($mulai - $hariIni) / 86400) . ' hari lagi';
            } elseif ($hariIni <= $selesai) {
                $info = 'Sisa ' . (int) (($selesai - $hariIni) / 86400) . ' hari';
            } else {
                $info = 'Terlambat ' . (int) (($hariIni - $selesai) / 86400) . ' hari';
                $infoWarna = 'text-danger';
            }
            ?>
            <div class="row g-0">
              <div class="col-md-5">
                <?php if ($aktif['foto'] != '') { ?>
                  <img src="foto/<?= htmlspecialchars($aktif['foto']) ?>" class="erent-cover" alt="<?= htmlspecialchars($aktif['merk']) ?>" style="border-radius:0">
                <?php } else { ?>
                  <div class="erent-cover d-flex align-items-center justify-content-center fs-1 text-secondary" style="border-radius:0"><i class="bi bi-car-front"></i></div>
                <?php } ?>
              </div>
              <div class="col-md-7 p-4">
                <span class="erent-meta text-uppercase">Sewa aktif</span>
                <h3 class="mb-0 mt-1"><?= htmlspecialchars($aktif['merk']) ?></h3>
                <div class="erent-meta mb-3"><i class="bi bi-credit-card-2-front"></i> <?= htmlspecialchars($aktif['plat_nomor']) ?></div>

                <div class="erent-row"><span class="erent-meta">Tanggal</span><span><?= date('d/m/Y', $mulai) ?> s/d <?= date('d/m/Y', $selesai) ?> (<?= (int) $hari ?> hari)</span></div>
                <div class="erent-row"><span class="erent-meta">Estimasi total</span><strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></div>
                <div class="erent-row"><span class="erent-meta">Keterangan</span><strong class="<?= $infoWarna ?>"><?= $info ?></strong></div>

                <div class="erent-steps">
                  <div class="erent-step done"><i class="bi bi-send"></i><span>Diajukan</span></div>
                  <div class="erent-step <?= $disetujui ? 'done' : '' ?>"><i class="bi bi-check-lg"></i><span>Disetujui</span></div>
                  <div class="erent-step"><i class="bi bi-flag"></i><span>Selesai</span></div>
                </div>
              </div>
            </div>
          <?php } else { ?>
            <div class="text-center p-5">
              <div class="erent-icon mx-auto mb-3"><i class="bi bi-car-front"></i></div>
              <h4>Belum ada sewa aktif</h4>
              <p class="erent-meta mb-3">Pilih mobil yang tersedia lalu ajukan sewa pertamamu.</p>
              <a href="#mobil" class="erent-btn solid">Pilih Mobil</a>
            </div>
          <?php } ?>
        </div>
      </div>

      <!-- Tagihan -->
      <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
        <div class="d-flex flex-column gap-3 h-100">

          <div class="erent-item d-flex align-items-center gap-3">
            <div class="erent-icon green"><i class="bi bi-wallet2"></i></div>
            <div>
              <div class="erent-meta">Total dibayar (lunas)</div>
              <strong class="fs-5">Rp <?= number_format($totalBayar, 0, ',', '.') ?></strong>
            </div>
          </div>

          <div class="erent-item d-flex align-items-center gap-3">
            <div class="erent-icon amber"><i class="bi bi-receipt"></i></div>
            <div>
              <div class="erent-meta">Tagihan belum lunas</div>
              <strong class="fs-5">Rp <?= number_format($totalTagihan, 0, ',', '.') ?></strong>
            </div>
          </div>

          <div class="erent-item d-flex align-items-center gap-3">
            <div class="erent-icon <?= $totalDenda > 0 ? 'red' : '' ?>"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
              <div class="erent-meta">Denda</div>
              <strong class="fs-5 <?= $totalDenda > 0 ? 'text-danger' : '' ?>">Rp <?= number_format($totalDenda, 0, ',', '.') ?></strong>
            </div>
          </div>

          <a href="#berkas" class="erent-btn mt-auto">Lihat Berkas &amp; Invoice</a>
        </div>
      </div>

      <!-- Riwayat -->
      <div class="col-12" data-aos="fade-up">
        <div class="erent-box">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">Riwayat Sewa Terbaru</h4>
            <div class="d-print-none">
              <button type="button" onclick="window.print()" class="erent-btn sm"><i class="bi bi-printer"></i> Cetak</button>
              <a href="#sewa" class="erent-btn solid sm">Semua</a>
            </div>
          </div>

          <?php $adaSewa = false; ?>
          <?php if ($sewaTerbaru) { while ($r = $sewaTerbaru->fetch_assoc()) { ?>
            <?php
            $adaSewa = true;
            if ($r['status_rental'] == $STATUS_MENUNGGU) {
                $warna = 'b-warning';
            } elseif ($r['status_rental'] == $STATUS_BERJALAN) {
                $warna = 'b-success';
            } elseif ($r['status_rental'] == 'ditolak') {
                $warna = 'b-danger';
            } else {
                $warna = '';
            }
            ?>
            <div class="erent-row align-items-center">
              <div class="d-flex align-items-center gap-3">
                <?php if ($r['foto'] != '') { ?>
                  <img src="foto/<?= htmlspecialchars($r['foto']) ?>" class="erent-thumb" alt="">
                <?php } else { ?>
                  <div class="erent-thumb d-flex align-items-center justify-content-center text-secondary"><i class="bi bi-car-front"></i></div>
                <?php } ?>
                <div>
                  <strong><?= htmlspecialchars($r['merk']) ?></strong>
                  <span class="erent-meta">&middot; <?= htmlspecialchars($r['plat_nomor']) ?></span><br>
                  <span class="erent-meta"><?= date('d/m/Y', strtotime($r['tgl_sewa'])) ?> s/d <?= date('d/m/Y', strtotime($r['tgl_kembali'])) ?></span>
                </div>
              </div>
              <span class="erent-badge <?= $warna ?>"><?= htmlspecialchars($r['status_rental']) ?></span>
            </div>
          <?php } } ?>

          <?php if (!$adaSewa) { ?>
            <div class="text-center erent-meta py-3">Belum ada pengajuan sewa.</div>
          <?php } ?>
        </div>
      </div>

    </div>
  </div>

</section><!-- /Ringkasan Section -->
<?php } ?>