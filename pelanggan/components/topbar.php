<?php
$inisial = strtoupper(mb_substr($namaPelanggan, 0, 1));
?>
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Judul & tanggal -->
    <div class="d-none d-sm-block ml-md-2">
        <h6 class="mb-0 font-weight-bold text-gray-800">Halaman Pelanggan</h6>
        <small class="text-muted"><?= date('d M Y') ?></small>
    </div>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto align-items-center">

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-700 small font-weight-bold">
                    <?= htmlspecialchars($namaPelanggan) ?>
                    <small class="d-block text-muted font-weight-normal">Pelanggan</small>
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