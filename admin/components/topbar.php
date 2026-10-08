<?php
// Nama admin dari session (sesuaikan key dengan kode login-mu)
$namaAdmin = $_SESSION['nama'] ?? 'Admin';
$inisial   = strtoupper(mb_substr($namaAdmin, 0, 1));

// Pengajuan sewa yang masih menunggu persetujuan
// (sesuaikan 'menunggu' dengan nilai status_rental di databasemu)
$jumlahBaru = 0;
$daftarBaru = [];
$qBaru = $conn->query("SELECT rentals.id, rentals.tgl_sewa, users.nama, cars.merk, cars.plat_nomor
                       FROM rentals
                       JOIN users ON users.id = rentals.pelanggan_id
                       JOIN cars ON cars.id = rentals.car_id
                       WHERE rentals.status_rental = 'menunggu'
                       ORDER BY rentals.id DESC");
if ($qBaru) {
    $jumlahBaru = $qBaru->num_rows;
    $i = 0;
    while ($r = $qBaru->fetch_assoc()) {
        if ($i++ < 5) $daftarBaru[] = $r; // tampilkan maksimal 5
    }
}
?>
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Judul & tanggal -->
    <div class="d-none d-sm-block ml-md-2">
        <h6 class="mb-0 font-weight-bold text-gray-800">Panel Admin</h6>
        <small class="text-muted"><?= date('d M Y') ?></small>
    </div>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto align-items-center">

        <!-- Nav Item - Notifikasi Pengajuan Sewa -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <?php if ($jumlahBaru > 0) { ?>
                    <span class="badge badge-danger badge-counter"><?= $jumlahBaru ?></span>
                <?php } ?>
            </a>
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">Pengajuan Sewa Baru</h6>

                <?php if (empty($daftarBaru)) { ?>
                    <span class="dropdown-item text-center small text-gray-500">Tidak ada pengajuan baru</span>
                <?php } else { ?>
                    <?php foreach ($daftarBaru as $b) { ?>
                        <a class="dropdown-item d-flex align-items-center" href="index.php?page=sewa">
                            <div class="mr-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-key text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500"><?= htmlspecialchars($b['tgl_sewa']) ?></div>
                                <span class="font-weight-bold"><?= htmlspecialchars($b['nama']) ?></span>
                                mengajukan sewa <?= htmlspecialchars($b['merk']) ?> (<?= htmlspecialchars($b['plat_nomor']) ?>)
                            </div>
                        </a>
                    <?php } ?>
                <?php } ?>

                <a class="dropdown-item text-center small text-gray-500" href="index.php?page=sewa">Lihat Semua Pengajuan</a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-700 small font-weight-bold">
                    <?= htmlspecialchars($namaAdmin) ?>
                    <small class="d-block text-muted font-weight-normal">Administrator</small>
                </span>
                <span class="avatar-initial"><?= $inisial ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>
</nav>