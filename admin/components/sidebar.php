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
        Menu Admin
    </div>

    <!-- Nav Item - Kelola Armada -->
    <li class="nav-item <?= strpos($page, 'armada') === 0 ? 'active' : '' ?>">
        <a class="nav-link" href="index.php?page=armada">
            <i class="fas fa-fw fa-car"></i>
            <span>Kelola Armada</span></a>
    </li>

    <!-- Nav Item - Pengajuan Sewa -->
    <li class="nav-item <?= strpos($page, 'sewa') === 0 ? 'active' : '' ?>">
        <a class="nav-link" href="index.php?page=sewa">
            <i class="fas fa-fw fa-key"></i>
            <span>Pengajuan Sewa</span></a>
    </li>

    <!-- Nav Item - Denda -->
    <li class="nav-item <?= strpos($page, 'denda') === 0 ? 'active' : '' ?>">
        <a class="nav-link" href="index.php?page=denda">
            <i class="fas fa-fw fa-exclamation-triangle"></i>
            <span>Denda</span></a>
    </li>

    <!-- Nav Item - Terbitkan Invoice -->
    <li class="nav-item <?= strpos($page, 'invoice') === 0 ? 'active' : '' ?>">
        <a class="nav-link" href="index.php?page=invoice">
            <i class="fas fa-fw fa-file-invoice-dollar"></i>
            <span>Terbitkan Invoice</span></a>
    </li>

    <!-- Nav Item - Kelola Pengguna -->
    <li class="nav-item <?= strpos($page, 'pengguna') === 0 ? 'active' : '' ?>">
        <a class="nav-link" href="index.php?page=pengguna">
            <i class="fas fa-fw fa-users"></i>
            <span>Kelola Pengguna</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>