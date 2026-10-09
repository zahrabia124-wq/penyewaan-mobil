<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon">
            <i class="fas fa-car-side"></i>
        </div>
        <div class="sidebar-brand-text mx-2 text-left">
            E-Rent
            <small class="d-block brand-sub">Penyewaan &amp; Rental Mobil</small>
        </div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item <?= $page === 'dashboard' ? 'active' : '' ?>">
        <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Menu Pelanggan
    </div>

    <!-- Nav Item - Memilih Mobil -->
    <li class="nav-item <?= $page === 'mobil' ? 'active' : '' ?>">
        <a class="nav-link" href="index.php?page=mobil">
            <i class="fas fa-fw fa-car"></i>
            <span>Memilih Mobil</span></a>
    </li>

    <!-- Nav Item - Mengajukan Sewa -->
    <li class="nav-item <?= strpos($page, 'sewa') === 0 ? 'active' : '' ?>">
        <a class="nav-link" href="index.php?page=sewa">
            <i class="fas fa-fw fa-key"></i>
            <span>Mengajukan Sewa</span></a>
    </li>

    <!-- Nav Item - Berkas Persetujuan -->
    <li class="nav-item <?= $page === 'berkas' ? 'active' : '' ?>">
        <a class="nav-link" href="index.php?page=berkas">
            <i class="fas fa-fw fa-file-signature"></i>
            <span>Berkas Persetujuan</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>