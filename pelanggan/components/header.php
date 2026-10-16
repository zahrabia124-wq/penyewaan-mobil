<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

        <a href="#hero" class="logo d-flex align-items-center me-auto">
            <h1 class="sitename"><i class="bi bi-car-front-fill"></i> E-Rent</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="#hero" class="active">Beranda</a></li>
                <?php if ($sudahLogin) { ?>
                    <li><a href="#dashboard">Dashboard</a></li>
                <?php } ?>
                <li><a href="#mobil">Memilih Mobil</a></li>
                <?php if ($sudahLogin) { ?>
                    <li><a href="#sewa">Mengajukan Sewa</a></li>
                    <li><a href="#berkas">Berkas Persetujuan</a></li>
                <?php } ?>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <?php if ($sudahLogin) { ?>
            <a class="btn-getstarted" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($namaPelanggan) ?>
            </a>
        <?php } else { ?>
            <a class="btn-getstarted" href="<?= htmlspecialchars(($base ?? '') . $urlLogin) ?>">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
        <?php } ?>

    </div>
</header>