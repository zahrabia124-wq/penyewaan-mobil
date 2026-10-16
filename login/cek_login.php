<?php
// Penjaga halaman. Tiap role punya sesi sendiri (cookie: erent_admin, erent_pemilik, erent_pelanggan),
// jadi admin, pemilik, dan pelanggan bisa login bersamaan di satu browser tanpa saling menimpa.
function cek_login($role)
{
    $nama = 'erent_' . $role;

    // Kalau ada sesi lain yang sudah aktif (nama berbeda), tutup dulu
    if (session_status() === PHP_SESSION_ACTIVE && session_name() !== $nama) {
        session_write_close();
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_name($nama);
        session_start();
    }

    if (($_SESSION['role'] ?? '') === $role) {
        return; // role cocok, boleh lanjut
    }

    // Kalau browser ini login sebagai role lain -> "Email atau password salah."
    // Kalau belum login sama sekali            -> "Silakan login terlebih dahulu."
    $pesan = 'belum_login=1';
    foreach (['admin', 'pemilik', 'pelanggan'] as $r) {
        if ($r !== $role && isset($_COOKIE['erent_' . $r])) {
            $pesan = 'gagal=1';
            break;
        }
    }

    // Alamat folder login relatif terhadap document root (misal /penyewaan-mobil/login)
    $root = rtrim(str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'])), '/');
    $dir  = str_replace('\\', '/', __DIR__);
    $url  = (stripos($dir, $root) === 0) ? substr($dir, strlen($root)) : '/login';

    header('Location: ' . $url . '/login.php?' . $pesan);
    exit;
}