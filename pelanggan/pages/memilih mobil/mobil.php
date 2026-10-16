<?php
// Halaman memilih mobil. Hanya mobil yang statusnya "tersedia" yang tampil.
// $conn = koneksi database, dibuat di index.php
$hasil = $conn->query("SELECT *, harga_sewa_per_hari AS harga_per_hari, status_mobil AS status FROM cars WHERE status_mobil = 'tersedia' ORDER BY merk");
$ada = false;
?>

<!-- Memilih Mobil Section -->
<section id="mobil" class="services section">

  <div class="container section-title" data-aos="fade-up">
    <span>Armada</span>
    <h2>Memilih Mobil</h2>
    <p>Pilih mobil yang tersedia, lalu lanjut mengajukan sewa<?= $sudahLogin ? '' : ' (login diminta saat mengajukan)' ?></p>
  </div>

  <div class="container">
    <div class="row gy-4">

      <?php while ($row = $hasil->fetch_assoc()) { ?>
        <?php $ada = true; ?>
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
          <div class="card">
            <div class="card-img">
              <?php if ($row['foto'] != '') { ?>
                <img src="foto/<?= htmlspecialchars($row['foto']) ?>" class="img-fluid" alt="<?= htmlspecialchars($row['merk']) ?>">
              <?php } else { ?>
                <div class="car-noimg d-flex align-items-center justify-content-center"><i class="bi bi-car-front"></i></div>
              <?php } ?>
              <span class="erent-badge b-success"><?= htmlspecialchars($row['status']) ?></span>
            </div>

            <h3><?= htmlspecialchars($row['merk']) ?></h3>
            <p class="mb-2"><i class="bi bi-credit-card-2-front"></i> <?= htmlspecialchars($row['plat_nomor']) ?></p>
            <p class="mb-3">
              <span class="erent-price">Rp <?= number_format($row['harga_per_hari'], 0, ',', '.') ?></span>
              <span class="erent-meta">/ hari</span>
            </p>
            <p>
              <!-- Pilih mobil -> pindah ke halaman form pengajuan, mobil terpilih otomatis -->
             <a href="pelanggan/ajukan.php?car_id=<?= (int) $row['id'] ?>" class="btn btn-primary rounded-pill px-4 py-2"><?= $sudahLogin ? 'Pilih &amp; Ajukan' : 'Pilih &amp; Ajukan' ?></a>
            </p>
          </div>
        </div>
      <?php } ?>

      <?php if (!$ada) { ?>
        <div class="col-12 text-center erent-meta">Belum ada mobil yang tersedia.</div>
      <?php } ?>

    </div>
  </div>

</section><!-- /Memilih Mobil Section -->