<?php
// Logout per role: logout.php?role=admin | pemilik | pelanggan
// Hapus sesi milik role itu saja. Pelanggan kembali ke halaman pelanggan (../index.php),
// admin dan pemilik kembali ke halaman login.
$role = $_GET['role'] ?? '';

if (in_array($role, ['admin', 'pemilik', 'pelanggan'], true)) {
    session_name('erent_' . $role);
    session_start();

    $_SESSION = [];

    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);

    session_destroy();
}

// Pelanggan boleh melihat-lihat tanpa login, jadi setelah logout kembali ke halaman semula
if ($role === 'pelanggan') {
    header('Location: ../index.php');
    exit;
}

header('Location: login.php?logout=1');
exit;