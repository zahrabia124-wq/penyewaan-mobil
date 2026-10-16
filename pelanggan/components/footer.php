<footer id="footer" class="footer dark-background">

    <div class="container footer-top">
        <div class="row gy-4">

            <div class="col-lg-5 col-md-12 footer-about">
                <a href="#hero" class="logo d-flex align-items-center">
                    <span class="sitename"><i class="bi bi-car-front-fill"></i> E-Rent</span>
                </a>
                <p>Sistem penyewaan dan rental mobil. Pilih mobil yang tersedia, ajukan sewa, lalu pantau persetujuan, denda, dan invoice kamu dalam satu tempat.</p>
            </div>

            <div class="col-lg-3 col-6 footer-links">
                <h4>Menu</h4>
                <ul>
                    <li><a href="#hero">Beranda</a></li>
                    <li><a href="#dashboard">Dashboard</a></li>
                    <li><a href="#mobil">Memilih Mobil</a></li>
                    <li><a href="pelanggan/ajukan.php">Mengajukan Sewa</a></li>
                    <li><a href="#berkas">Berkas Persetujuan</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-6 footer-links">
                <h4>Akun</h4>
                <ul>
                    <li><i class="bi bi-person-circle"></i> <?= htmlspecialchars($namaPelanggan) ?></li>
                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a></li>
                </ul>
            </div>

        </div>
    </div>

    <div class="container copyright text-center mt-4">
        <p>&copy; <span>Copyright</span> <strong class="px-1 sitename">E-Rent</strong> <span>Penyewaan &amp; Rental Mobil</span></p>
    </div>

</footer>