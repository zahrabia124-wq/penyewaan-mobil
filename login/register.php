<?php
// Halaman daftar akun E-Rent untuk pelanggan (folder login/, sejajar dengan admin, pelanggan, pemilik)
// Role selalu "pelanggan". Akun admin dan pemilik hanya bisa dibuat oleh admin.

require_once __DIR__ . '/../admin/database/connection.php';

$error     = '';
$nama      = '';
$email     = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ambil data dari form
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $ulang    = $_POST['ulang_password'] ?? '';

    // 2. Cek isian
    if ($nama === '' || $email === '' || $password === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $ulang) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $db   = new Database();
        $conn = $db->conn;

        try {
            // 3. Email tidak boleh sama dengan akun lain (email dipakai untuk login)
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();

            if ($stmt->get_result()->fetch_assoc()) {
                $error = 'Email sudah terdaftar. Silakan login.';
            } else {
                // 4. Password dienkripsi dulu (hash), role dikunci "pelanggan"
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $role = 'pelanggan';

                $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('ssss', $nama, $email, $hash, $role);
                $stmt->execute();

                header('Location: login.php?daftar=1');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Pendaftaran gagal, coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Daftar - E-Rent</title>

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
                <h1 class="h4 text-gray-900 font-weight-bold mb-1">Daftar Akun</h1>
                <p class="text-muted mb-0">Buat akun pelanggan E-Rent</p>
            </div>

            <?php if ($error !== '') { ?>
                <div class="alert alert-danger py-2" role="alert">
                    <i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php } ?>

            <form method="post" action="register.php" autocomplete="off">
                <div class="form-group">
                    <label for="nama" class="small font-weight-bold text-gray-700">Nama Lengkap</label>
                    <input type="text" class="form-control form-control-user" id="nama" name="nama"
                           placeholder="Nama kamu" value="<?= htmlspecialchars($nama) ?>" required autofocus>
                </div>
                <div class="form-group">
                    <label for="email" class="small font-weight-bold text-gray-700">Email</label>
                    <input type="email" class="form-control form-control-user" id="email" name="email"
                           placeholder="nama@email.com" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <div class="form-group">
                    <label for="password" class="small font-weight-bold text-gray-700">Password</label>
                    <input type="password" class="form-control form-control-user" id="password" name="password"
                           placeholder="Minimal 6 karakter" minlength="6" required>
                </div>
                <div class="form-group">
                    <label for="ulang_password" class="small font-weight-bold text-gray-700">Ulangi Password</label>
                    <input type="password" class="form-control form-control-user" id="ulang_password" name="ulang_password"
                           placeholder="Ketik ulang password" minlength="6" required>
                    <div class="custom-control custom-checkbox small mt-2">
                        <input type="checkbox" class="custom-control-input" id="lihat">
                        <label class="custom-control-label" for="lihat">Tampilkan password</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-user btn-block">
                    <i class="fas fa-user-plus mr-1"></i> Daftar
                </button>
            </form>

            <hr>
            <div class="text-center small">
                Sudah punya akun? <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('lihat').addEventListener('change', function () {
        var tipe = this.checked ? 'text' : 'password';
        document.getElementById('password').type = tipe;
        document.getElementById('ulang_password').type = tipe;
    });
</script>
</body>
</html>