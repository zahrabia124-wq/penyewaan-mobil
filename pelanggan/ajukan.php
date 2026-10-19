<?php
// Pindah ke folder utama supaya semua path include lama tetap berlaku
chdir(__DIR__ . '/..');

if (session_status() === PHP_SESSION_NONE) {
    session_name('erent_pelanggan');
    session_start();
}

// Belum login? Kirim ke halaman login (mobil yang dipilih dibawa lewat car_id).
// Setelah login, pelanggan dikembalikan ke form ini dengan mobil yang sama.
$sudahLogin = (($_SESSION['role'] ?? '') === 'pelanggan');
if (!$sudahLogin) {
    $carId = (int) ($_GET['car_id'] ?? 0);
    $root  = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
    header('Location: ' . $root . '/login/login.php?belum_login=1' . ($carId ? '&car_id=' . $carId : ''));
    exit;
}
require_once 'pelanggan/database/connection.php';
$db = new Database();
$conn = $db->conn;

$tpl = 'pelanggan/template/assets';

$pelangganId   = (int) $_SESSION['id'];
$namaPelanggan = $_SESSION['nama'] ?? '';

// Alamat folder utama, dipakai <base> di head.php agar CSS, JS, dan link form tetap benar
$base = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';
// ===== Data untuk halaman form ajukan sewa =====
$judul      = 'Ajukan Sewa';                       // dipakai components/page_title.php
$carDipilih = (int) ($_GET['car_id'] ?? 0);         // mobil yang dipilih dari halaman sebelumnya

// Hanya mobil yang statusnya "tersedia". Harga & foto dipakai untuk ringkasan di sisi kanan.
$hasilMobil = $conn->query("SELECT id, merk, plat_nomor, foto, harga_sewa_per_hari FROM cars WHERE status_mobil = 'tersedia' ORDER BY merk");
$daftarMobil = [];
while ($c = $hasilMobil->fetch_assoc()) {
    $daftarMobil[] = $c;
}

// Pesan error dari function/sewa_insert.php (kalau validasi di server gagal)
$pesanError = [
    'data'   => 'Data belum lengkap atau formatnya salah. Periksa lagi mobil dan tanggalnya.',
    'lewat'  => 'Tanggal sewa tidak boleh sebelum hari ini.',
    'urutan' => 'Tanggal kembali tidak boleh sebelum tanggal sewa.',
    'mobil'  => 'Mobil ini sudah tidak tersedia. Silakan pilih mobil lain.',
];
$kodeError = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<?php include 'pelanggan/partials/head.php' ?>

<body class="index-page">

    <?php include 'pelanggan/components/header.php' ?>

    <main class="main">

        <!-- Judul halaman + breadcrumb -->
        <?php include 'pelanggan/components/page_title.php' ?>

        <section class="section light-background">
            <div class="container">
                <div class="row gy-4 justify-content-center">

                    <!-- ===== Kolom kiri: form ===== -->
                    <div class="col-lg-7">
                        <div class="erent-box" style="height: auto;">

                            <h4 class="mb-1" style="color: var(--heading-color);">Detail pemesanan</h4>
                            <p class="erent-meta mb-0">Pilih mobil, tentukan tanggal, lalu kirim pengajuan. Admin akan memprosesnya.</p>

                            <!-- Langkah -->
                            <div class="erent-steps mb-4">
                                <div class="erent-step" id="langkah-1"><i class="bi bi-car-front"></i><span>Pilih mobil</span></div>
                                <div class="erent-step" id="langkah-2"><i class="bi bi-calendar-event"></i><span>Tentukan tanggal</span></div>
                                <div class="erent-step" id="langkah-3"><i class="bi bi-send"></i><span>Ajukan</span></div>
                            </div>

                            <?php if (isset($pesanError[$kodeError])) { ?>
                                <div class="alert alert-danger d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div><?= htmlspecialchars($pesanError[$kodeError]) ?></div>
                                </div>
                            <?php } ?>

                            <form action="pelanggan/function/sewa_insert.php" method="POST" id="form-ajukan" class="erent-form">

                                <div class="mb-4">
                                    <label for="car">Mobil</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-car-front"></i></span>
                                        <select name="car_id" id="car" class="form-select" required>
                                            <option value="">-- Pilih mobil --</option>
                                            <?php foreach ($daftarMobil as $c) { ?>
                                                <option value="<?= (int) $c['id'] ?>"
                                                        data-merk="<?= htmlspecialchars($c['merk']) ?>"
                                                        data-plat="<?= htmlspecialchars($c['plat_nomor']) ?>"
                                                        data-foto="<?= htmlspecialchars($c['foto'] ?? '') ?>"
                                                        data-harga="<?= (int) $c['harga_sewa_per_hari'] ?>"
                                                        <?= ($c['id'] == $carDipilih) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($c['merk']) ?> - <?= htmlspecialchars($c['plat_nomor']) ?>
                                                    (Rp <?= number_format($c['harga_sewa_per_hari'], 0, ',', '.') ?>/hari)
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="tgl_sewa">Tanggal sewa</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                            <input type="date" name="tgl_sewa" id="tgl_sewa" class="form-control" min="<?= date('Y-m-d') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tgl_kembali">Tanggal kembali</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-calendar-x"></i></span>
                                            <input type="date" name="tgl_kembali" id="tgl_kembali" class="form-control" min="<?= date('Y-m-d') ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pilihan cepat lama sewa -->
                                <div class="mb-4">
                                    <div class="erent-meta mb-2">Pilih cepat lama sewa:</div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button" class="erent-chip" data-hari="1">1 hari</button>
                                        <button type="button" class="erent-chip" data-hari="3">3 hari</button>
                                        <button type="button" class="erent-chip" data-hari="7">1 minggu</button>
                                        <button type="button" class="erent-chip" data-hari="14">2 minggu</button>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    <a href="index.php#mobil" class="btn btn-outline-secondary rounded-pill px-4"><i class="bi bi-arrow-left"></i> Kembali</a>
                                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-send"></i> Ajukan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- ===== Kolom kanan: ringkasan pemesanan ===== -->
                    <div class="col-lg-5">
                        <div class="erent-box erent-sum">
                            <img id="sum-foto" src="" alt="" style="display: none;">
                            <div id="sum-kosong" class="kosong d-flex align-items-center justify-content-center"><i class="bi bi-car-front"></i></div>

                            <div class="body">
                                <div class="erent-meta mb-1">Ringkasan pemesanan</div>
                                <h4 id="sum-merk" class="mb-1" style="color: var(--heading-color);">Pilih mobil dulu</h4>
                                <div class="erent-meta mb-3"><i class="bi bi-credit-card-2-front"></i> <span id="sum-plat">-</span></div>

                                <div class="erent-row"><span class="erent-meta">Harga per hari</span><strong id="sum-harga">-</strong></div>
                                <div class="erent-row"><span class="erent-meta">Tanggal sewa</span><strong id="sum-sewa">-</strong></div>
                                <div class="erent-row"><span class="erent-meta">Tanggal kembali</span><strong id="sum-kembali">-</strong></div>
                                <div class="erent-row"><span class="erent-meta">Lama sewa</span><strong id="sum-hari">-</strong></div>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="erent-meta">Total estimasi</span>
                                    <span class="erent-price" id="sum-total">Rp 0</span>
                                </div>

                                <div class="mt-3 p-3 rounded" style="background: color-mix(in srgb, var(--accent-color), transparent 92%); font-size: 13px;">
                                    <i class="bi bi-info-circle"></i>
                                    Pengajuan berstatus <strong>menunggu</strong> sampai disetujui admin. Keterlambatan atau kerusakan mobil dapat dikenai denda.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Ringkasan & total dihitung langsung di browser saat pilihan diubah -->
        <script>
            (function () {
                var car = document.getElementById('car');
                var sewa = document.getElementById('tgl_sewa');
                var kembali = document.getElementById('tgl_kembali');
                var $ = function (id) { return document.getElementById(id); };
                var rp = function (n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); };
                var pad = function (n) { return (n < 10 ? '0' : '') + n; };
                var iso = function (d) { return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate()); };
                var tampil = function (v) { if (!v) { return '-'; } var p = v.split('-'); return p[2] + '/' + p[1] + '/' + p[0]; };

                function hitung() {
                    var opt = car.options[car.selectedIndex];
                    var adaMobil = car.value !== '';
                    var harga = adaMobil ? parseInt(opt.getAttribute('data-harga'), 10) : 0;

                    // Ringkasan mobil
                    $('sum-merk').textContent  = adaMobil ? opt.getAttribute('data-merk') : 'Pilih mobil dulu';
                    $('sum-plat').textContent  = adaMobil ? opt.getAttribute('data-plat') : '-';
                    $('sum-harga').textContent = adaMobil ? rp(harga) : '-';

                    var foto = adaMobil ? opt.getAttribute('data-foto') : '';
                    if (foto) {
                        $('sum-foto').src = 'foto/' + encodeURIComponent(foto);
                        $('sum-foto').style.display = 'block';
                        $('sum-kosong').style.display = 'none';
                    } else {
                        $('sum-foto').style.display = 'none';
                        $('sum-kosong').style.display = 'flex';
                    }

                    // Tanggal kembali tidak boleh sebelum tanggal sewa
                    if (sewa.value) { kembali.min = sewa.value; }
                    if (sewa.value && kembali.value && kembali.value < sewa.value) { kembali.value = ''; }

                    $('sum-sewa').textContent    = tampil(sewa.value);
                    $('sum-kembali').textContent = tampil(kembali.value);

                    // Lama sewa (minimal 1 hari, sama seperti perhitungan di server)
                    var hari = 0;
                    if (sewa.value && kembali.value) {
                        hari = Math.round((new Date(kembali.value) - new Date(sewa.value)) / 86400000);
                        if (hari < 1) { hari = 1; }
                    }
                    $('sum-hari').textContent  = hari ? hari + ' hari' : '-';
                    $('sum-total').textContent = rp(hari * harga);

                    // Penanda langkah
                    $('langkah-1').classList.toggle('done', adaMobil);
                    $('langkah-2').classList.toggle('done', adaMobil && hari > 0);
                    $('langkah-3').classList.toggle('done', adaMobil && hari > 0);
                }

                // Tombol pilihan cepat: isi tanggal kembali dari tanggal sewa + N hari
                document.querySelectorAll('.erent-chip').forEach(function (b) {
                    b.addEventListener('click', function () {
                        if (!sewa.value) { sewa.value = iso(new Date()); }
                        var p = sewa.value.split('-');
                        var d = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
                        d.setDate(d.getDate() + parseInt(b.getAttribute('data-hari'), 10));
                        kembali.value = iso(d);
                        hitung();
                    });
                });

                car.addEventListener('change', hitung);
                sewa.addEventListener('change', hitung);
                kembali.addEventListener('change', hitung);
                hitung();
            })();
        </script>

    </main>

    <?php include 'pelanggan/components/footer.php' ?>
    <?php include 'pelanggan/components/logout_modal.php' ?>

    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include 'pelanggan/partials/script.php' ?>

    <!-- Notifikasi pengajuan: form dikirim lewat JavaScript (fetch), hasilnya ditampilkan sebagai popup -->
    <script>
        (function () {
            var form = document.querySelector('form[action*="sewa_insert.php"]');
            if (!form) { return; }

            var pesanError = {
                data:   'Data belum lengkap atau formatnya salah. Periksa lagi mobil dan tanggalnya.',
                lewat:  'Tanggal sewa tidak boleh sebelum hari ini.',
                urutan: 'Tanggal kembali tidak boleh sebelum tanggal sewa.',
                mobil:  'Mobil ini sudah tidak tersedia. Silakan pilih mobil lain.'
            };

            function popup(ikon, warna, judul, isi, tombolTeks, tombolAksi, catatan) {
                var lama = document.getElementById('popup-notif');
                if (lama) { lama.remove(); }
                var box = document.createElement('div');
                box.id = 'popup-notif';
                box.style.cssText = 'position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;padding:16px;';
                box.innerHTML =
                    '<div style="background:#fff;border-radius:12px;max-width:420px;width:100%;padding:32px 28px;text-align:center;box-shadow:0 10px 40px rgba(0,0,0,.3);">' +
                    '<i class="bi ' + ikon + '" style="font-size:3.5rem;color:' + warna + ';"></i>' +
                    '<h5 style="margin:12px 0 8px;">' + judul + '</h5>' +
                    '<p style="color:#6c757d;margin-bottom:8px;">' + isi + '</p>' +
                    '<p id="popup-catatan" style="color:#6c757d;font-size:13px;margin-bottom:18px;">' + (catatan || '') + '</p>' +
                    '<button type="button" id="popup-tombol" class="btn btn-primary px-4">' + tombolTeks + '</button>' +
                    '</div>';
                document.body.appendChild(box);
                document.getElementById('popup-tombol').onclick = tombolAksi;
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var tombol = form.querySelector('button[type="submit"]');
                var htmlAsli = tombol ? tombol.innerHTML : '';
                if (tombol) { tombol.disabled = true; tombol.innerHTML = 'Mengirim...'; }

                fetch(form.action, { method: 'POST', body: new FormData(form), credentials: 'same-origin' })
                    .then(function (res) {
                        var u = new URL(res.url);
                        var kode = u.searchParams.get('error');
                        if (tombol) { tombol.disabled = false; tombol.innerHTML = htmlAsli; }

                        // Sesi habis -> dikirim ke halaman login
                        if (u.pathname.indexOf('login') !== -1) { window.location.href = res.url; return; }

                        // Gagal validasi di server
                        if (kode || u.pathname.indexOf('ajukan.php') !== -1) {
                            popup('bi-exclamation-triangle-fill', '#dc3545', 'Pengajuan Gagal',
                                  pesanError[kode] || 'Pengajuan belum bisa disimpan. Coba lagi.',
                                  'Tutup', function () { document.getElementById('popup-notif').remove(); });
                            return;
                        }

                        // Berhasil -> popup, lalu pindah ke daftar pengajuan
                        var idBaru = u.searchParams.get('baru');
                        var tujuan = 'index.php' + (idBaru ? '?baru=' + encodeURIComponent(idBaru) : '') + '#sewa';
                        var sisa = 4;
                        popup('bi-check-circle-fill', '#198754', 'Pengajuan Berhasil Dikirim',
                              'Pengajuan sewa kamu sudah tersimpan dengan status <strong>menunggu</strong> persetujuan admin.',
                              'Lihat Pengajuan Saya', function () { window.location.href = tujuan; },
                              'Mengalihkan ke daftar pengajuan dalam <span id="hitung">' + sisa + '</span> detik...');
                        var t = setInterval(function () {
                            sisa--;
                            var h = document.getElementById('hitung');
                            if (h) { h.textContent = sisa; }
                            if (sisa <= 0) { clearInterval(t); window.location.href = tujuan; }
                        }, 1000);
                    })
                    .catch(function () {
                        // Kalau fetch gagal (mis. koneksi), kirim form seperti biasa
                        form.submit();
                    });
            });
        })();
    </script>

    <script>
        // Menu "#bagian" diarahkan balik ke index.php
        document.querySelectorAll('#navmenu a[href^="#"]').forEach(function (a) {
            a.setAttribute('href', 'index.php' + a.getAttribute('href'));
        });
    </script>
</body>

</html>