<?php
// Halaman login E-Rent (folder login/, sejajar dengan admin, pelanggan, pemilik)

require_once __DIR__ . '/../admin/database/connection.php';

// Tujuan setelah login, sesuai role
$tujuan = [
    'admin'     => '../admin/index.php',
    'pelanggan' => '../index.php',
    'pemilik'   => '../pemilik/index.php',
];

// Form login selalu ditampilkan (tidak dilempar otomatis), supaya bisa ganti akun.
// Login baru akan menimpa sesi lama.

// Kalau datang dari tombol "Ajukan Sewa" (belum login), ingat mobil yang dipilih
$carId  = (int) ($_GET['car_id'] ?? 0);

$error  = '';
$info   = '';
$email  = '';

if (isset($_GET['logout'])) {
    $info = 'Anda sudah berhasil keluar.';
}
if (isset($_GET['gagal'])) {
    $error = 'Email atau password salah.';
}
if (isset($_GET['belum_login'])) {
    $error = 'Silakan login terlebih dahulu.';
}
if (isset($_GET['daftar'])) {
    $info = 'Pendaftaran berhasil. Silakan login.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email dan password wajib diisi.';
    } else {
        $db   = new Database();
        $conn = $db->conn;

        $stmt = $conn->prepare("SELECT id, nama, email, password, role FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        $cocok = false;
        if ($user) {
            if (password_verify($password, $user['password'])) {
                // Password sudah di-hash
                $cocok = true;
                if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $up = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $up->bind_param('si', $hash, $user['id']);
                    $up->execute();
                }
            } elseif (hash_equals($user['password'], $password)) {
                // Password di database masih teks biasa -> cocok, lalu otomatis diubah jadi hash
                $cocok = true;
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $up = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $up->bind_param('si', $hash, $user['id']);
                $up->execute();
            }
        }

        if ($cocok && isset($tujuan[$user['role']])) {
            // Tiap role punya sesi sendiri, jadi login role lain tidak menimpa yang ini
            session_name('erent_' . $user['role']);
            session_start();
            $_SESSION = [];
            session_regenerate_id(true);
            $_SESSION['id']    = (int) $user['id'];
            $_SESSION['nama']  = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role']  = $user['role'];

            // Pelanggan yang tadi mau menyewa mobil -> langsung ke form pengajuan
            if ($user['role'] === 'pelanggan' && $carId > 0) {
                header('Location: ../pelanggan/ajukan.php?car_id=' . $carId);
                exit;
            }

            header('Location: ' . $tujuan[$user['role']]);
            exit;
        }

        $error = 'Email atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - E-Rent</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='22' fill='%234e73df'/><text x='50' y='68' font-size='58' text-anchor='middle'>🚗</text></svg>">

    <!-- Memakai template yang sudah ada di folder admin -->
    <link href="../admin/template/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="../admin/template/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); min-height: 100vh; }
        .login-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .login-card { width: 100%; max-width: 420px; border: 0; border-radius: 1rem; }
        .brand-icon {
            width: 64px; height: 64px; border-radius: 18px; margin: 0 auto 1rem;
            background: linear-gradient(135deg, #4e73df, #224abe); color: #fff;
            display: flex; align-items: center; justify-content: center; font-size: 1.8rem;
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="card login-card shadow-lg">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="brand-icon"><i class="fas fa-car-side"></i></div>
                <h1 class="h4 text-gray-900 font-weight-bold mb-1">E-Rent</h1>
                <p class="text-muted mb-0">Silakan login untuk melanjutkan</p>
            </div>

            <?php if ($error !== '') { ?>
                <div class="alert alert-danger py-2" role="alert">
                    <i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php } ?>
            <?php if ($info !== '') { ?>
                <div class="alert alert-success py-2" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> <?= htmlspecialchars($info) ?>
                </div>
            <?php } ?>

            <form method="post" action="login.php<?= $carId > 0 ? '?car_id=' . $carId : '' ?>" autocomplete="off">
                <div class="form-group">
                    <label for="email" class="small font-weight-bold text-gray-700">Email</label>
                    <input type="email" class="form-control form-control-user" id="email" name="email"
                           placeholder="nama@email.com" value="<?= htmlspecialchars($email) ?>" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password" class="small font-weight-bold text-gray-700">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-user" id="password" name="password"
                               placeholder="Password" required>
                    </div>
                    <div class="custom-control custom-checkbox small mt-2">
                        <input type="checkbox" class="custom-control-input" id="lihat">
                        <label class="custom-control-label" for="lihat">Tampilkan password</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-user btn-block">
                    <i class="fas fa-sign-in-alt mr-1"></i> Login
                </button>
            </form>

            <hr>
            <div class="text-center small">
                Belum punya akun? <a href="register.php">Daftar sebagai pelanggan</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('lihat').addEventListener('change', function () {
        document.getElementById('password').type = this.checked ? 'text' : 'password';
    });
</script>
</body>
</html>